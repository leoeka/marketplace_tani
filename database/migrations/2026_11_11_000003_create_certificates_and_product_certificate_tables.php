<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('product_certificate', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'certificate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_certificate');
        Schema::dropIfExists('certificates');
    }
};