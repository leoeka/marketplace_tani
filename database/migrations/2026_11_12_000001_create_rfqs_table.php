<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfqs', function (Blueprint $table): void {
            $table->id();
            $table->string('buyer_name');
            $table->string('company');
            $table->string('country', 100);
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('quantity')->nullable();
            $table->enum('quantity_unit', ['kg', 'ton'])->nullable();
            $table->boolean('wants_sample')->default(false);
            $table->boolean('asks_moq')->default(false);
            $table->enum('incoterms', ['FOB', 'CIF', 'EXW', 'TBD'])->default('TBD');
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'handled'])->default('new');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};