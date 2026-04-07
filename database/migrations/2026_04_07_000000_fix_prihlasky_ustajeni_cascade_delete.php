<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prihlasky_ustajeni', function (Blueprint $table) {
            $table->dropForeign(['ustajeni_id']);
            $table->foreign('ustajeni_id')
                ->references('id')
                ->on('udalost_ustajeni')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prihlasky_ustajeni', function (Blueprint $table) {
            $table->dropForeign(['ustajeni_id']);
            $table->foreign('ustajeni_id')
                ->references('id')
                ->on('udalost_ustajeni');
        });
    }
};
