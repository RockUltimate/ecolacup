<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('homepage_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('homepage_messages')->insert([
            'id' => 1,
            'title' => 'Moderní přihlášky na koňské závody',
            'body' => 'Veřejný kalendář, přehled uzávěrek, disciplín a kapacit. Přihlášení jezdci navazují rovnou na správu osob, koní a přihlášek bez ruční administrativy navíc.',
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_messages');
    }
};
