<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prihlaska extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'prihlasky';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'udalost_id',
        'user_id',
        'osoba_id',
        'kun_id',
        'kun_tandem_id',
        'start_cislo',
        'poznamka',
        'gdpr_souhlas',
        'cena_celkem',
        'smazana',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gdpr_souhlas' => 'boolean',
            'smazana' => 'boolean',
            'cena_celkem' => 'decimal:2',
        ];
    }

    public function udalost(): BelongsTo
    {
        return $this->belongsTo(Udalost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function osoba(): BelongsTo
    {
        return $this->belongsTo(Osoba::class);
    }

    public function kun(): BelongsTo
    {
        return $this->belongsTo(Kun::class, 'kun_id');
    }

    public function kunTandem(): BelongsTo
    {
        return $this->belongsTo(Kun::class, 'kun_tandem_id');
    }

    public function polozky(): HasMany
    {
        return $this->hasMany(PrihlaskaPolozka::class, 'prihlaska_id');
    }

    public function ustajeniChoices(): HasMany
    {
        return $this->hasMany(PrihlaskaUstajeni::class, 'prihlaska_id');
    }

    public function vekKategorie(): string
    {
        $vek = $this->osoba?->datum_narozeni?->age;

        if ($vek === null) {
            return '';
        }

        if ($vek < 8) {
            return ' (m*)';
        }
        if ($vek < 14) {
            return ' (m)';
        }
        if ($vek < 18) {
            return ' (j)';
        }

        return '';
    }
}
