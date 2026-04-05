<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClenstviCmtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $membershipTypes = array_keys((array) config('clenstvi_cmt.membership_types', []));
        return [
            'osoba_id' => ['required', 'integer', 'exists:osoby,id'],
            'typ_clenstvi' => ['required', Rule::in($membershipTypes)],
            'rok' => ['required', 'integer', 'min:2000', 'max:2100'],
            'cena' => ['required', 'numeric', 'min:0'],
            'organizace_id' => ['nullable', 'integer'],
            'evidencni_cislo' => ['nullable', 'string', 'max:20'],
            'titul' => ['nullable', 'string', 'max:100'],
            'bydliste' => ['nullable', 'string', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'nazev_organizace' => ['nullable', 'string', 'max:255'],
            'ico' => ['nullable', 'string', 'max:30'],
            'aktivni' => ['nullable', 'boolean'],
            'zastupce_titul' => ['nullable', 'string', 'max:100'],
            'zastupce_jmeno' => ['nullable', 'string', 'max:255'],
            'zastupce_prijmeni' => ['nullable', 'string', 'max:255'],
            'zastupce_rok_narozeni' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'zastupce_vztah' => ['nullable', 'string', 'max:255'],
            'zastupce_bydliste' => ['nullable', 'string', 'max:255'],
            'zastupce_telefon' => ['nullable', 'string', 'max:50'],
            'zastupce_email' => ['nullable', 'email', 'max:255'],
            'sken_prihlaska_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:8192'],
            'souhlas_gdpr' => ['required', 'accepted'],
            'souhlas_email' => ['nullable', 'boolean'],
            'souhlas_zverejneni' => ['nullable', 'boolean'],
        ];
    }
}
