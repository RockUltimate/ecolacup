<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pleme extends Model
{
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plemena';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kod',
        'nazev',
        'poradi',
    ];
}
