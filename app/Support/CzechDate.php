<?php

namespace App\Support;

use Carbon\Carbon;

class CzechDate
{
    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (! preg_match('/^(?<day>\d{1,2})\.(?<month>\d{1,2})\.(?<year>\d{4})$/', $value, $matches)) {
            return null;
        }

        $day = (int) $matches['day'];
        $month = (int) $matches['month'];
        $year = (int) $matches['year'];

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%02d.%02d.%04d', $day, $month, $year);
    }

    public static function toCarbon(?string $value): ?Carbon
    {
        $normalized = self::normalize($value);

        return $normalized ? Carbon::createFromFormat('d.m.Y', $normalized)->startOfDay() : null;
    }

    public static function toDateString(?string $value): ?string
    {
        return self::toCarbon($value)?->toDateString();
    }
}
