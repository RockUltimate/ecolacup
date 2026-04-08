<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('homepage_messages')) {
            return;
        }

        $existing = DB::table('homepage_messages')
            ->orderByRaw('CASE WHEN id = 1 THEN 0 ELSE 1 END')
            ->orderBy('id')
            ->first();

        $defaults = [
            'title' => 'Moderní přihlášky na koňské závody',
            'body' => 'Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek bez ruční administrativy navíc.',
            'updated_by' => null,
        ];

        $createdAt = $existing?->created_at ?? now();
        $updatedAt = $existing?->updated_at ?? now();

        DB::table('homepage_messages')->updateOrInsert(
            ['id' => 1],
            [
                'title' => $existing?->title ?? $defaults['title'],
                'body' => $existing?->body ?? $defaults['body'],
                'updated_by' => $existing?->updated_by ?? $defaults['updated_by'],
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]
        );

        DB::table('homepage_messages')
            ->where('id', '!=', 1)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
