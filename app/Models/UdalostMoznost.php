<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UdalostMoznost extends Model
{
    /**
     * @var string
     */
    protected $table = 'udalost_moznosti';
    /**
     * @var list<string>
     */
    protected $fillable = [
        'udalost_id',
        'nazev',
        'min_vek',
        'cena',
        'poradi',
        'je_administrativni_poplatek',
        'popis_text',
        'popis_html',
        'foto_path',
        'pdf_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_vek' => 'integer',
            'cena' => 'decimal:2',
            'poradi' => 'integer',
            'je_administrativni_poplatek' => 'boolean',
        ];
    }

    public function udalost(): BelongsTo
    {
        return $this->belongsTo(Udalost::class);
    }

    public function prihlaskyPolozky(): HasMany
    {
        return $this->hasMany(PrihlaskaPolozka::class, 'moznost_id');
    }
}
