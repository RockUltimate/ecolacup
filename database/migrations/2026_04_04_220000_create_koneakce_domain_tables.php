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
        Schema::create('osoby', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jmeno');
            $table->string('prijmeni');
            $table->date('datum_narozeni');
            $table->string('staj');
            $table->boolean('gdpr_souhlas')->default(false);
            $table->boolean('gdpr_odvolano')->default(false);
            $table->timestamp('gdpr_souhlas_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plemena', function (Blueprint $table) {
            $table->id();
            $table->string('kod', 20)->unique();
            $table->string('nazev');
            $table->integer('poradi')->default(0);
            $table->timestamps();
        });

        Schema::create('kone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jmeno');
            $table->string('plemeno_kod', 20)->nullable();
            $table->string('plemeno_nazev')->nullable();
            $table->string('plemeno_vlastni')->nullable();
            $table->unsignedSmallInteger('rok_narozeni');
            $table->string('staj');
            $table->string('pohlavi', 1);
            $table->date('ehv_datum')->nullable();
            $table->date('aie_datum')->nullable();
            $table->date('chripka_datum')->nullable();
            $table->string('cislo_prukazu')->nullable();
            $table->string('cislo_hospodarstvi')->nullable();
            $table->text('majitel_jmeno_adresa')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('udalosti', function (Blueprint $table) {
            $table->id();
            $table->string('nazev');
            $table->string('misto');
            $table->date('datum_zacatek');
            $table->date('datum_konec');
            $table->date('uzavierka_prihlasek');
            $table->integer('kapacita')->nullable();
            $table->string('propozice_pdf')->nullable();
            $table->boolean('aktivni')->default(true);
            $table->text('popis')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('udalost_moznosti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('udalost_id')->constrained('udalosti')->cascadeOnDelete();
            $table->string('nazev');
            $table->integer('min_vek')->nullable();
            $table->decimal('cena', 8, 2);
            $table->integer('poradi')->default(0);
            $table->boolean('je_administrativni_poplatek')->default(false);
            $table->timestamps();
        });

        Schema::create('udalost_ustajeni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('udalost_id')->constrained('udalosti')->cascadeOnDelete();
            $table->string('nazev');
            $table->string('typ');
            $table->decimal('cena', 8, 2);
            $table->integer('kapacita')->nullable();
            $table->timestamps();
        });

        Schema::create('prihlasky', function (Blueprint $table) {
            $table->id();
            $table->foreignId('udalost_id')->constrained('udalosti');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('osoba_id')->constrained('osoby');
            $table->foreignId('kun_id')->constrained('kone');
            $table->foreignId('kun_tandem_id')->nullable()->constrained('kone');
            $table->integer('start_cislo')->nullable();
            $table->text('poznamka')->nullable();
            $table->boolean('gdpr_souhlas')->default(false);
            $table->decimal('cena_celkem', 10, 2)->default(0);
            $table->boolean('smazana')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prihlasky_polozky', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prihlaska_id')->constrained('prihlasky')->cascadeOnDelete();
            $table->foreignId('moznost_id')->constrained('udalost_moznosti');
            $table->string('nazev');
            $table->decimal('cena', 8, 2);
            $table->timestamps();
        });

        Schema::create('prihlasky_ustajeni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prihlaska_id')->constrained('prihlasky')->cascadeOnDelete();
            $table->foreignId('ustajeni_id')->constrained('udalost_ustajeni');
            $table->decimal('cena', 8, 2);
            $table->timestamps();
        });

        Schema::create('clenstvi_cmt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('osoba_id')->constrained('osoby')->cascadeOnDelete();
            $table->unsignedBigInteger('organizace_id')->default(2);
            $table->string('evidencni_cislo', 20)->nullable();
            $table->string('titul')->nullable();
            $table->string('bydliste')->nullable();
            $table->string('telefon')->nullable();
            $table->string('email')->nullable();
            $table->string('nazev_organizace')->nullable();
            $table->string('ico')->nullable();
            $table->string('typ_clenstvi');
            $table->unsignedSmallInteger('rok');
            $table->decimal('cena', 8, 2);
            $table->boolean('aktivni')->default(false);
            $table->string('zastupce_titul')->nullable();
            $table->string('zastupce_jmeno')->nullable();
            $table->string('zastupce_prijmeni')->nullable();
            $table->unsignedSmallInteger('zastupce_rok_narozeni')->nullable();
            $table->string('zastupce_vztah')->nullable();
            $table->string('zastupce_bydliste')->nullable();
            $table->string('zastupce_telefon')->nullable();
            $table->string('zastupce_email')->nullable();
            $table->string('sken_prihlaska')->nullable();
            $table->boolean('souhlas_gdpr')->default(false);
            $table->boolean('souhlas_email')->default(false);
            $table->boolean('souhlas_zverejneni')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clenstvi_cmt');
        Schema::dropIfExists('prihlasky_ustajeni');
        Schema::dropIfExists('prihlasky_polozky');
        Schema::dropIfExists('prihlasky');
        Schema::dropIfExists('udalost_ustajeni');
        Schema::dropIfExists('udalost_moznosti');
        Schema::dropIfExists('udalosti');
        Schema::dropIfExists('kone');
        Schema::dropIfExists('plemena');
        Schema::dropIfExists('osoby');
    }
};
