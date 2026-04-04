<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kun extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'jmeno',
        'plemeno_kod',
        'plemeno_nazev',
        'plemeno_vlastni',
        'rok_narozeni',
        'staj',
        'pohlavi',
        'ehv_datum',
        'aie_datum',
        'chripka_datum',
        'cislo_prukazu',
        'cislo_hospodarstvi',
        'majitel_jmeno_adresa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rok_narozeni' => 'integer',
            'ehv_datum' => 'date',
            'aie_datum' => 'date',
            'chripka_datum' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ockovaniOk(): array
    {
        $today = now()->startOfDay();
        $status = [];

        foreach (['ehv_datum', 'aie_datum', 'chripka_datum'] as $field) {
            $date = $this->{$field};

            if (! $date) {
                $status[$field] = 'missing';
                continue;
            }

            $status[$field] = $date->lt($today) ? 'expired' : 'ok';
        }

        return $status;
    }
}
