<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrihlaskaUstajeni extends Model
{
    /**
     * @var string
     */
    protected $table = 'prihlasky_ustajeni';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'prihlaska_id',
        'ustajeni_id',
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

    public function ustajeni(): BelongsTo
    {
        return $this->belongsTo(UdalostUstajeni::class, 'ustajeni_id');
    }
}
