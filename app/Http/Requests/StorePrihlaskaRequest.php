<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrihlaskaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'osoba_id' => ['required', 'integer', 'exists:osoby,id'],
            'kun_id' => ['required', 'integer', 'exists:kone,id'],
            'kun_tandem_id' => ['nullable', 'integer', 'different:kun_id', 'exists:kone,id'],
            'moznosti' => ['required', 'array', 'min:1'],
            'moznosti.*' => ['integer', 'exists:udalost_moznosti,id'],
            'ustajeni' => ['nullable', 'array'],
            'ustajeni.*' => ['integer', 'exists:udalost_ustajeni,id'],
            'poznamka' => ['nullable', 'string', 'max:2000'],
            'gdpr_souhlas' => ['required', 'accepted'],
        ];
    }
}
