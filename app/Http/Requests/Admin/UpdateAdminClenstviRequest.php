<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminClenstviRequest extends FormRequest
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
            'evidencni_cislo' => ['nullable', 'string', 'max:20'],
            'rok' => ['required', 'integer', 'min:2000', 'max:2100'],
            'cena' => ['required', 'numeric', 'min:0'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'sken_prihlaska' => ['nullable', 'string', 'max:255'],
            'aktivni' => ['nullable', 'boolean'],
            'souhlas_gdpr' => ['nullable', 'boolean'],
            'souhlas_email' => ['nullable', 'boolean'],
            'souhlas_zverejneni' => ['nullable', 'boolean'],
        ];
    }
}
