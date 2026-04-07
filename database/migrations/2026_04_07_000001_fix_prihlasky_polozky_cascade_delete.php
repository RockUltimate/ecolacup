<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prihlasky_polozky', function (Blueprint $table) {
            $table->dropForeign(['moznost_id']);
            $table->foreign('moznost_id')
                ->references('id')
                ->on('udalost_moznosti')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prihlasky_polozky', function (Blueprint $table) {
            $table->dropForeign(['moznost_id']);
            $table->foreign('moznost_id')
                ->references('id')
                ->on('udalost_moznosti');
        });
    }
};
