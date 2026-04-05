<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\NormalizesCzechDates;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreRegisteredUserRequest extends FormRequest
{
    use NormalizesCzechDates;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'jmeno' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'prijmeni' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'datum_narozeni' => ['nullable', 'date_format:d.m.Y'],
            'pohlavi' => ['nullable', 'in:M,F'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telefon' => ['nullable', 'string', 'max:30'],
            'gdpr_souhlas' => ['sometimes', 'accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCzechDateFields(['datum_narozeni']);
    }
}
