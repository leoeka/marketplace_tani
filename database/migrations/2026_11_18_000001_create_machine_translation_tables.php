<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hasil terjemahan Google yang disimpan agar teks yang sama tidak diterjemahkan dua kali.
        Schema::create('machine_translations', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 10);
            $table->char('source_hash', 40);
            $table->text('text');
            $table->timestamps();

            $table->unique(['locale', 'source_hash']);
        });

        // Pemakaian karakter per bulan, untuk menjaga batas biaya.
        Schema::create('machine_translation_usages', function (Blueprint $table): void {
            $table->id();
            $table->char('period', 7)->unique();
            $table->unsignedBigInteger('characters')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_translation_usages');
        Schema::dropIfExists('machine_translations');
    }
};