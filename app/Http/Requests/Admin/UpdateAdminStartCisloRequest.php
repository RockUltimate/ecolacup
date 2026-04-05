<?php

namespace App\Http\Requests\Admin;

use App\Models\Prihlaska;
use App\Models\Udalost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminStartCisloRequest extends FormRequest
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
        /** @var Udalost|null $udalost */
        $udalost = $this->route('udalost');
        /** @var Prihlaska|null $prihlaska */
        $prihlaska = $this->route('prihlaska');

        return [
            'start_cislo' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('prihlasky', 'start_cislo')
                    ->where(fn ($query) => $query->where('udalost_id', $udalost?->id))
                    ->ignore($prihlaska?->id),
            ],
        ];
    }
}
