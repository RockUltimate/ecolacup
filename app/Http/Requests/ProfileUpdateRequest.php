<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'jmeno' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'prijmeni' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'datum_narozeni' => ['nullable', 'date'],
            'pohlavi' => ['nullable', 'in:M,F'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'telefon' => ['nullable', 'string', 'max:30'],
            'gdpr_souhlas' => ['sometimes', 'accepted'],
        ];
    }
}
