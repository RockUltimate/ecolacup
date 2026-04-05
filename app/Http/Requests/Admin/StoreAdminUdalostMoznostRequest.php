<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUdalostMoznostRequest extends FormRequest
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
            'min_vek' => ['nullable', 'integer', 'min:0'],
            'cena' => ['required', 'numeric', 'min:0'],
            'poradi' => ['nullable', 'integer', 'min:0'],
            'je_administrativni_poplatek' => ['nullable', 'boolean'],
        ];
    }
}
