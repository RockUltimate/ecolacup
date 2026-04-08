<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('udalost_moznosti', function (Blueprint $table) {
            $table->text('popis_text')->nullable()->after('je_administrativni_poplatek');
            $table->text('popis_html')->nullable()->after('popis_text');
            $table->string('foto_path')->nullable()->after('popis_html');
            $table->string('pdf_path')->nullable()->after('foto_path');
        });

        Schema::table('udalost_ustajeni', function (Blueprint $table) {
            $table->text('popis_text')->nullable()->after('kapacita');
            $table->text('popis_html')->nullable()->after('popis_text');
            $table->string('foto_path')->nullable()->after('popis_html');
            $table->string('pdf_path')->nullable()->after('foto_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('udalost_moznosti', function (Blueprint $table) {
            $table->dropColumn(['popis_text', 'popis_html', 'foto_path', 'pdf_path']);
        });

        Schema::table('udalost_ustajeni', function (Blueprint $table) {
            $table->dropColumn(['popis_text', 'popis_html', 'foto_path', 'pdf_path']);
        });
    }
};
