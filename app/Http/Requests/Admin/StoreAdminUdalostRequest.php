<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\NormalizesCzechDates;
use App\Support\CzechDate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAdminUdalostRequest extends FormRequest
{
    use NormalizesCzechDates;

    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nazev' => ['required', 'string', 'max:255'],
            'misto' => ['required', 'string', 'max:255'],
            'datum_zacatek' => ['required', 'date_format:d.m.Y'],
            'datum_konec' => ['required', 'date_format:d.m.Y'],
            'uzavierka_prihlasek' => ['required', 'date_format:d.m.Y'],
            'kapacita' => ['nullable', 'integer', 'min:1'],
            'aktivni' => ['nullable', 'boolean'],
            'popis' => ['nullable', 'string'],
            'propozice_pdf_upload' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'vysledky_pdf_upload' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'fotoalbum_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCzechDateFields(['datum_zacatek', 'datum_konec', 'uzavierka_prihlasek']);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $start = CzechDate::toCarbon($this->input('datum_zacatek'));
            $end = CzechDate::toCarbon($this->input('datum_konec'));

            if ($start && $end && $end->lt($start)) {
                $validator->errors()->add('datum_konec', 'Datum konce musí být stejný nebo pozdější než datum začátku.');
            }
        });
    }
}
