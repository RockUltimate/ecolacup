<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUdalostRequest extends FormRequest
{
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
            'datum_zacatek' => ['required', 'date'],
            'datum_konec' => ['required', 'date', 'after_or_equal:datum_zacatek'],
            'uzavierka_prihlasek' => ['required', 'date'],
            'kapacita' => ['nullable', 'integer', 'min:1'],
            'aktivni' => ['nullable', 'boolean'],
            'popis' => ['nullable', 'string'],
            'propozice_pdf_upload' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
