<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClenstviCmt extends Model
{
    /**
     * @var string
     */
    protected $table = 'clenstvi_cmt';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'osoba_id',
        'organizace_id',
        'evidencni_cislo',
        'titul',
        'bydliste',
        'telefon',
        'email',
        'nazev_organizace',
        'ico',
        'typ_clenstvi',
        'rok',
        'cena',
        'aktivni',
        'zastupce_titul',
        'zastupce_jmeno',
        'zastupce_prijmeni',
        'zastupce_rok_narozeni',
        'zastupce_vztah',
        'zastupce_bydliste',
        'zastupce_telefon',
        'zastupce_email',
        'sken_prihlaska',
        'souhlas_gdpr',
        'souhlas_email',
        'souhlas_zverejneni',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rok' => 'integer',
            'cena' => 'decimal:2',
            'aktivni' => 'boolean',
            'souhlas_gdpr' => 'boolean',
            'souhlas_email' => 'boolean',
            'souhlas_zverejneni' => 'boolean',
            'zastupce_rok_narozeni' => 'integer',
        ];
    }

    public function osoba(): BelongsTo
    {
        return $this->belongsTo(Osoba::class);
    }
}
