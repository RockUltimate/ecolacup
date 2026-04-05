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
     * @param Collection<int, UdalostMoznost> $moznosti
     * @param Collection<int, UdalostUstajeni> $ustajeni
     */
    private function computeTotal(Collection $moznosti, Collection $ustajeni): float
    {
        return (float) $moznosti->sum('cena') + (float) $ustajeni->sum('cena');
    }
}
