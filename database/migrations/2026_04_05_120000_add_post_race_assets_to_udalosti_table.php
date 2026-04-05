<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('udalosti', function (Blueprint $table) {
            $table->string('vysledky_pdf')->nullable()->after('propozice_pdf');
            $table->string('fotoalbum_url')->nullable()->after('vysledky_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('udalosti', function (Blueprint $table) {
            $table->dropColumn(['vysledky_pdf', 'fotoalbum_url']);
        });
    }
};
