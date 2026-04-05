<?php

namespace App\Http\Requests;

class UpdateClenstviCmtRequest extends StoreClenstviCmtRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['souhlas_gdpr'] = ['nullable', 'boolean'];

        return $rules;
    }
}
