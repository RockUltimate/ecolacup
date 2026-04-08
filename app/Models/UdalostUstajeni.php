<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UdalostUstajeni extends Model
{
    /**
     * @var list<string>
     */
    protected $table = 'udalost_ustajeni';

    protected $fillable = [
        'udalost_id',
        'nazev',
        'typ',
        'cena',
        'kapacita',
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
            'cena' => 'decimal:2',
            'kapacita' => 'integer',
        ];
    }

    public function udalost(): BelongsTo
    {
        return $this->belongsTo(Udalost::class);
    }
}
