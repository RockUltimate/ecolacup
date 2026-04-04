<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Osoba extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'osoby';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'jmeno',
        'prijmeni',
        'datum_narozeni',
        'staj',
        'gdpr_souhlas',
        'gdpr_odvolano',
        'gdpr_souhlas_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datum_narozeni' => 'date',
            'gdpr_souhlas' => 'boolean',
            'gdpr_odvolano' => 'boolean',
            'gdpr_souhlas_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prihlasky(): HasMany
    {
        return $this->hasMany(Prihlaska::class);
    }

    public function clenstviCmt(): HasMany
    {
        return $this->hasMany(ClenstviCmt::class, 'osoba_id');
    }

    public function aktivniClenstviCmt(): HasOne
    {
        return $this->hasOne(ClenstviCmt::class, 'osoba_id')
            ->where('aktivni', true)
            ->latestOfMany('rok');
    }

    public function getCmtStatusAttribute(): string
    {
        if ($this->relationLoaded('aktivniClenstviCmt') && $this->aktivniClenstviCmt) {
            return 'active';
        }

        $hasAny = $this->relationLoaded('clenstviCmt')
            ? $this->clenstviCmt->isNotEmpty()
            : $this->clenstviCmt()->exists();

        return $hasAny ? 'inactive' : 'none';
    }
}
