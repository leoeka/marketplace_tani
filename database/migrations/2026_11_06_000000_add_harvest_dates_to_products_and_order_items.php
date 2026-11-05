<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->date('harvest_date')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->boolean('is_preorder')->default(false);
            $table->date('harvest_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['is_preorder', 'harvest_date']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('harvest_date');
        });
    }
};