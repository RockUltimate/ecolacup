<?php

namespace App\Services;

use App\Models\Prihlaska;
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
                ->whereIn('id', $moznostIds)
                ->get();

            $selectedUstajeni = UdalostUstajeni::query()
                ->where('udalost_id', $udalost->id)
                ->whereIn('id', $ustajeniIds)
                ->get();
            $selectedMoznosti = $this->applyNewMemberAdminFee($prihlaska, $selectedMoznosti, $udalost);

            $selectedMoznosti = $this->applyAdminFeeDedup($prihlaska, $selectedMoznosti);

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

            return $prihlaska->fresh(['polozky', 'ustajeniChoices']) ?? $prihlaska;
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
     * @param Collection<int, UdalostMoznost> $selectedMoznosti
     */
    private function applyAdminFeeDedup(Prihlaska $prihlaska, Collection $selectedMoznosti): Collection
    {
        $adminFeeIds = $selectedMoznosti
            ->filter(fn (UdalostMoznost $item): bool => (bool) $item->je_administrativni_poplatek)
            ->pluck('id');

        if ($adminFeeIds->isEmpty()) {
            return $selectedMoznosti;
        }

        $alreadyCharged = Prihlaska::query()
            ->where('udalost_id', $prihlaska->udalost_id)
            ->where('osoba_id', $prihlaska->osoba_id)
            ->where('id', '!=', $prihlaska->id)
            ->where('smazana', false)
            ->whereHas('polozky', function ($query) use ($adminFeeIds): void {
                $query->whereIn('moznost_id', $adminFeeIds->all());
            })
            ->exists();

        if (! $alreadyCharged) {
            return $selectedMoznosti;
        }

        return $selectedMoznosti->reject(
            fn (UdalostMoznost $item): bool => (bool) $item->je_administrativni_poplatek
        )->values();
    }

    /**
     * @param Collection<int, UdalostMoznost> $selectedMoznosti
     */
    private function applyNewMemberAdminFee(Prihlaska $prihlaska, Collection $selectedMoznosti, Udalost $udalost): Collection
    {
        $osoba = $prihlaska->osoba()->first();
        if (! $osoba || $osoba->clenstviCmt()->exists()) {
            return $selectedMoznosti;
        }

        $adminFeeAlreadySelected = $selectedMoznosti
            ->contains(fn (UdalostMoznost $item): bool => (bool) $item->je_administrativni_poplatek);
        if ($adminFeeAlreadySelected) {
            return $selectedMoznosti;
        }

        $adminFeeOption = UdalostMoznost::query()
            ->where('udalost_id', $udalost->id)
            ->where('je_administrativni_poplatek', true)
            ->orderBy('id')
            ->first();
        if (! $adminFeeOption) {
            return $selectedMoznosti;
        }

        return $selectedMoznosti->push($adminFeeOption);
    }

    /**
     * @param Collection<int, UdalostMoznost> $moznosti
     * @param Collection<int, UdalostUstajeni> $ustajeni
     */
    private function computeTotal(Collection $moznosti, Collection $ustajeni): float
    {
        return (float) $moznosti->sum('cena') + (float) $ustajeni->sum('cena');
    }
}
