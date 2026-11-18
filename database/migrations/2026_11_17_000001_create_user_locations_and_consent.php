<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // null = belum memilih, granted = mengizinkan, denied = menolak
            $table->string('location_consent', 10)->nullable();
            $table->timestamp('location_consent_at')->nullable();
        });

        Schema::create('user_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 10, 6);
            $table->unsignedInteger('accuracy_m')->nullable();
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->index(['user_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_locations');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['location_consent', 'location_consent_at']);
        });
    }
};