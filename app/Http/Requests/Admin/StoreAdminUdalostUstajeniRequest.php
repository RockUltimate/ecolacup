<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUdalostUstajeniRequest extends FormRequest
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
            'typ' => ['required', 'in:ustajeni,ubytovani,strava,ostatni'],
            'cena' => ['required', 'numeric', 'min:0'],
            'kapacita' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
