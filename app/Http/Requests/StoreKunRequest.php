<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesCzechDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreKunRequest extends FormRequest
{
    use NormalizesCzechDates;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'jmeno' => ['required', 'string', 'max:255'],
            'plemeno_kod' => ['nullable', 'string', 'max:20'],
            'plemeno_nazev' => ['nullable', 'string', 'max:255'],
            'plemeno_vlastni' => ['nullable', 'string', 'max:255'],
            'rok_narozeni' => ['required', 'integer', 'min:1900', 'max:'.(string) now()->year],
            'staj' => ['required', 'string', 'max:255'],
            'pohlavi' => ['required', 'in:h,k,v'],
            'ehv_datum' => ['nullable', 'date_format:d.m.Y'],
            'aie_datum' => ['nullable', 'date_format:d.m.Y'],
            'chripka_datum' => ['nullable', 'date_format:d.m.Y'],
            'cislo_prukazu' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\\-\\/ ]+$/'],
            'cislo_hospodarstvi' => ['nullable', 'string', 'max:255'],
            'majitel_jmeno_adresa' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCzechDateFields(['ehv_datum', 'aie_datum', 'chripka_datum']);
    }
}
