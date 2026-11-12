<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loading_ports', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('city');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('freight_estimates', function (Blueprint $table): void {
            $table->id();
            $table->enum('mode', ['sea', 'air']);
            $table->string('destination', 100);
            $table->foreignId('loading_port_id')->nullable()->constrained('loading_ports')->restrictOnDelete();
            $table->unsignedSmallInteger('transit_days_min');
            $table->unsignedSmallInteger('transit_days_max');
            $table->decimal('rate_usd_per_kg', 10, 2)->nullable();
            $table->string('note', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['mode', 'is_active', 'destination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freight_estimates');
        Schema::dropIfExists('loading_ports');
    }
};