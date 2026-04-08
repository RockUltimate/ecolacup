<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Udalost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'udalosti';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nazev',
        'misto',
        'datum_zacatek',
        'datum_konec',
        'uzavierka_prihlasek',
        'kapacita',
        'propozice_pdf',
        'vysledky_pdf',
        'fotoalbum_url',
        'aktivni',
        'popis',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datum_zacatek' => 'date',
            'datum_konec' => 'date',
            'uzavierka_prihlasek' => 'date',
            'aktivni' => 'boolean',
            'kapacita' => 'integer',
        ];
    }

    public function moznosti(): HasMany
    {
        return $this->hasMany(UdalostMoznost::class)->orderBy('poradi');
    }

    public function ustajeniMoznosti(): HasMany
    {
        return $this->hasMany(UdalostUstajeni::class);
    }

    public function prihlasky(): HasMany
    {
        return $this->hasMany(Prihlaska::class);
    }

    public function getPocetPrihlasekAttribute(): int
    {
        return $this->prihlasky()
            ->where('smazana', false)
            ->count();
    }

    public function getPocetStartuAttribute(): int
    {
        return (int) DB::table('prihlasky_polozky')
            ->join('prihlasky', 'prihlasky.id', '=', 'prihlasky_polozky.prihlaska_id')
            ->where('prihlasky.udalost_id', $this->id)
            ->count();
    }

    public static function deactivatePastEvents(?Carbon $today = null): int
    {
        $today ??= now()->startOfDay();

        return static::query()
            ->where('aktivni', true)
            ->where(function ($query) use ($today): void {
                $query
                    ->whereDate('datum_konec', '<', $today)
                    ->orWhere(function ($subQuery) use ($today): void {
                        $subQuery
                            ->whereNull('datum_konec')
                            ->whereDate('datum_zacatek', '<', $today);
                    });
            })
            ->update([
                'aktivni' => false,
                'updated_at' => now(),
            ]);
    }
}
