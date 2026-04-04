<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrihlaskaPolozka extends Model
{
    /**
     * @var string
     */
    protected $table = 'prihlasky_polozky';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prihlaska_id',
        'moznost_id',
        'nazev',
        'cena',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cena' => 'decimal:2',
        ];
    }

    public function prihlaska(): BelongsTo
    {
        return $this->belongsTo(Prihlaska::class);
    }

    public function moznost(): BelongsTo
    {
        return $this->belongsTo(UdalostMoznost::class, 'moznost_id');
    }
}
