<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOsobaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'jmeno' => ['required', 'string', 'max:255'],
            'prijmeni' => ['required', 'string', 'max:255'],
            'datum_narozeni' => ['required', 'date'],
            'staj' => ['required', 'string', 'max:255'],
            'gdpr_souhlas' => ['nullable', 'boolean'],
            'gdpr_odvolano' => ['nullable', 'boolean'],
        ];
    }
}
