<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kone', function (Blueprint $table) {
            $table->dropColumn(['ehv_datum', 'aie_datum', 'chripka_datum']);
        });
    }

    public function down(): void
    {
        Schema::table('kone', function (Blueprint $table) {
            $table->date('ehv_datum')->nullable()->after('pohlavi');
            $table->date('aie_datum')->nullable()->after('ehv_datum');
            $table->date('chripka_datum')->nullable()->after('aie_datum');
        });
    }
};
