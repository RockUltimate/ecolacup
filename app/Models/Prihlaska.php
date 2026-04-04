<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prihlaska extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'prihlasky';

    /**
     * The attributes that are mass assignable.
     *
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
}
