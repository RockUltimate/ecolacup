<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class HomepageMessage extends Model
{
    public const SINGLETON_ID = 1;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'title',
        'body',
        'updated_by',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return array{title: string, body: string}
     */
    public static function defaults(): array
    {
        return [
            'title' => 'Moderní přihlášky na koňské závody',
            'body' => 'Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek bez ruční administrativy navíc.',
        ];
    }

    public static function singleton(): self
    {
        $model = new self;

        if (! Schema::hasTable($model->getTable())) {
            return new self([
                'id' => self::SINGLETON_ID,
                ...self::defaults(),
            ]);
        }

        return self::query()->find(self::SINGLETON_ID)
            ?? new self([
                'id' => self::SINGLETON_ID,
                ...self::defaults(),
            ]);
    }
}
