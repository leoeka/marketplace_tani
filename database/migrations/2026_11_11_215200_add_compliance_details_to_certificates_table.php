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
        Schema::table('certificates', function (Blueprint $table): void {
            $table->string('type')->default('other')->after('name');
            $table->string('issuer')->nullable()->after('type');
            $table->string('certificate_number')->nullable()->after('issuer');
            $table->date('expires_at')->nullable()->after('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table): void {
            $table->dropColumn(['type', 'issuer', 'certificate_number', 'expires_at']);
        });
    }
};
