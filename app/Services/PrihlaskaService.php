<?php

namespace App\Services;

use App\Models\Prihlaska;
use App\Models\PrihlaskaPolozka;
use App\Models\Udalost;
use App\Models\UdalostMoznost;
use App\Models\UdalostUstajeni;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrihlaskaService
{
    /**
     * @param array<int, int> $moznostIds
     * @param array<int, int> $ustajeniIds
     */
    public function syncPrihlaska(Prihlaska $prihlaska, array $moznostIds, array $ustajeniIds): Prihlaska
    {
        return DB::transaction(function () use ($prihlaska, $moznostIds, $ustajeniIds): Prihlaska {
            $udalost = $prihlaska->udalost()->firstOrFail();

            $selectedMoznosti = UdalostMoznost::query()
                ->where('udalost_id', $udalost->id)
                ->where('je_administrativni_poplatek', false)
                ->whereIn('id', $moznostIds)
                ->get();

            $selectedUstajeni = UdalostUstajeni::query()
                ->where('udalost_id', $udalost->id)
                ->whereIn('id', $ustajeniIds)
                ->get();

            $prihlaska->polozky()->delete();
            foreach ($selectedMoznosti as $moznost) {
                $prihlaska->polozky()->create([
                    'moznost_id' => $moznost->id,
                    'nazev' => $moznost->nazev,
                    'cena' => $moznost->cena,
                ]);
            }

            $prihlaska->ustajeniChoices()->delete();
            foreach ($selectedUstajeni as $item) {
                $prihlaska->ustajeniChoices()->create([
                    'ustajeni_id' => $item->id,
                    'cena' => $item->cena,
                ]);
            }

            $prihlaska->cena_celkem = $this->computeTotal($selectedMoznosti, $selectedUstajeni);
            if ($prihlaska->start_cislo === null) {
                $prihlaska->start_cislo = $this->nextStartNumber($udalost);
            }
            $prihlaska->save();
            $this->rebalanceAdminFeeForPersonEvent($udalost, (int) $prihlaska->osoba_id, (int) $prihlaska->id);

            return $prihlaska->fresh(['polozky', 'ustajeniChoices']) ?? $prihlaska;
        });
    }

    public function rebalanceAdminFeeForPersonEvent(Udalost $udalost, int $osobaId, ?int $preferredPrihlaskaId = null): void
    {
        DB::transaction(function () use ($udalost, $osobaId, $preferredPrihlaskaId): void {
            $adminFee = UdalostMoznost::query()
                ->where('udalost_id', $udalost->id)
                ->where('je_administrativni_poplatek', true)
                ->orderBy('id')
                ->first();

            if (! $adminFee) {
                return;
            }

            $registrations = Prihlaska::withTrashed()
                ->where('udalost_id', $udalost->id)
                ->where('osoba_id', $osobaId)
                ->with(['polozky', 'ustajeniChoices'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($registrations->isEmpty()) {
                return;
            }

            $activeRegistrations = $registrations
                ->filter(fn (Prihlaska $registration) => ! $registration->smazana)
                ->values();

            $targetRegistration = $activeRegistrations->first(
                fn (Prihlaska $registration) => $registration->polozky->contains(
                    fn (PrihlaskaPolozka $item) => (int) $item->moznost_id === (int) $adminFee->id
                )
            );

            if (! $targetRegistration && $preferredPrihlaskaId !== null) {
                $targetRegistration = $activeRegistrations->firstWhere('id', $preferredPrihlaskaId);
            }

            $targetRegistration ??= $activeRegistrations->first();

            PrihlaskaPolozka::query()
                ->whereIn('prihlaska_id', $registrations->pluck('id'))
                ->where('moznost_id', $adminFee->id)
                ->delete();

            if ($targetRegistration) {
                $targetRegistration->polozky()->create([
                    'moznost_id' => $adminFee->id,
                    'nazev' => $adminFee->nazev,
                    'cena' => $adminFee->cena,
                ]);
            }

            $registrations->load(['polozky', 'ustajeniChoices']);

            foreach ($registrations as $registration) {
                $registration->cena_celkem = $this->computeStoredTotal($registration);
                $registration->save();
            }
        });
    }

    private function nextStartNumber(Udalost $udalost): int
    {
        $max = Prihlaska::query()
            ->where('udalost_id', $udalost->id)
            ->where('smazana', false)
            ->max('start_cislo');

        return ($max ?? 0) + 1;
    }

    /**
     * @param Collection<int, UdalostMoznost> $moznosti
     * @param Collection<int, UdalostUstajeni> $ustajeni
     */
    private function computeTotal(Collection $moznosti, Collection $ustajeni): float
    {
        return (float) $moznosti->sum('cena') + (float) $ustajeni->sum('cena');
    }

    private function computeStoredTotal(Prihlaska $prihlaska): float
    {
        return (float) $prihlaska->polozky->sum('cena') + (float) $prihlaska->ustajeniChoices->sum('cena');
    }
}
