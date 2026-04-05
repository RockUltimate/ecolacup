<?php

namespace App\GDPR;

use App\Models\User;

class UserDataExport
{
    public function toCsv(User $user): string
    {
        $user->load([
            'osoby',
            'kone',
            'prihlasky.udalost',
            'prihlasky.osoba',
            'prihlasky.kun',
            'prihlasky.polozky',
            'prihlasky.ustajeniChoices.ustajeni',
        ]);

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['sekce', 'zaznam_id', 'pole', 'hodnota'], ';');

        $this->writeRow($handle, 'user', $user->id, 'jmeno', $user->jmeno);
        $this->writeRow($handle, 'user', $user->id, 'prijmeni', $user->prijmeni);
        $this->writeRow($handle, 'user', $user->id, 'email', $user->email);
        $this->writeRow($handle, 'user', $user->id, 'telefon', $user->telefon);
        $this->writeRow($handle, 'user', $user->id, 'is_admin', $user->is_admin ? '1' : '0');
        $this->writeRow($handle, 'user', $user->id, 'gdpr_souhlas', $user->gdpr_souhlas ? '1' : '0');
        $this->writeRow($handle, 'user', $user->id, 'gdpr_souhlas_at', optional($user->gdpr_souhlas_at)->format('d.m.Y H:i:s'));

        foreach ($user->osoby as $osoba) {
            $this->writeRow($handle, 'osoba', $osoba->id, 'jmeno', $osoba->jmeno);
            $this->writeRow($handle, 'osoba', $osoba->id, 'prijmeni', $osoba->prijmeni);
            $this->writeRow($handle, 'osoba', $osoba->id, 'datum_narozeni', optional($osoba->datum_narozeni)->format('d.m.Y'));
            $this->writeRow($handle, 'osoba', $osoba->id, 'staj', $osoba->staj);
        }

        foreach ($user->kone as $kun) {
            $this->writeRow($handle, 'kun', $kun->id, 'jmeno', $kun->jmeno);
            $this->writeRow($handle, 'kun', $kun->id, 'plemeno_kod', $kun->plemeno_kod);
            $this->writeRow($handle, 'kun', $kun->id, 'rok_narozeni', (string) $kun->rok_narozeni);
            $this->writeRow($handle, 'kun', $kun->id, 'staj', $kun->staj);
        }

        foreach ($user->prihlasky as $prihlaska) {
            $this->writeRow($handle, 'prihlaska', $prihlaska->id, 'udalost', $prihlaska->udalost?->nazev);
            $this->writeRow($handle, 'prihlaska', $prihlaska->id, 'osoba', trim(($prihlaska->osoba?->prijmeni ?? '').' '.($prihlaska->osoba?->jmeno ?? '')));
            $this->writeRow($handle, 'prihlaska', $prihlaska->id, 'kun', $prihlaska->kun?->jmeno);
            $this->writeRow($handle, 'prihlaska', $prihlaska->id, 'start_cislo', (string) ($prihlaska->start_cislo ?? ''));
            $this->writeRow($handle, 'prihlaska', $prihlaska->id, 'cena_celkem', (string) $prihlaska->cena_celkem);

            foreach ($prihlaska->polozky as $polozka) {
                $this->writeRow($handle, 'prihlaska_polozka', $polozka->id, 'nazev', $polozka->nazev);
                $this->writeRow($handle, 'prihlaska_polozka', $polozka->id, 'cena', (string) $polozka->cena);
            }

            foreach ($prihlaska->ustajeniChoices as $choice) {
                $this->writeRow($handle, 'prihlaska_ustajeni', $choice->id, 'nazev', $choice->ustajeni?->nazev);
                $this->writeRow($handle, 'prihlaska_ustajeni', $choice->id, 'cena', (string) $choice->cena);
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $csv;
    }

    /**
     * @param  resource  $handle
     */
    private function writeRow($handle, string $section, int $recordId, string $field, ?string $value): void
    {
        fputcsv($handle, [$section, (string) $recordId, $field, $value ?? ''], ';');
    }
}
