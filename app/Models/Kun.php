<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kun extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'kone';

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
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prihlasky(): HasMany
    {
        return $this->hasMany(Prihlaska::class, 'kun_id');
    }
}
