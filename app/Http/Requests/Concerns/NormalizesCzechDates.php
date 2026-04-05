<?php

namespace App\Http\Requests\Concerns;

use App\Support\CzechDate;

trait NormalizesCzechDates
{
    /**
     * @param  array<int, string>  $fields
     */
    protected function normalizeCzechDateFields(array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            $value = $this->input($field);

            if ($value === null) {
                continue;
            }

            $normalized[$field] = CzechDate::normalize((string) $value) ?? $value;
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
