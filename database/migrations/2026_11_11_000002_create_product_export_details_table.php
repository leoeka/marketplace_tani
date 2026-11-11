<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_export_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->text('description_en');
            $table->string('hs_code')->nullable();
            $table->decimal('moisture_percent', 5, 2)->nullable();
            $table->string('size_spec')->nullable();
            $table->unsignedBigInteger('monthly_capacity')->nullable();
            $table->enum('monthly_capacity_unit', ['kg', 'ton'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_export_details');
    }
};