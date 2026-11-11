<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['local_courier', 'cold_chain']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('shipping_service_name')->nullable();
            $table->string('shipping_service_type')->nullable();
        });

        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users');
            $table->string('tracking_number');
            $table->timestamp('shipped_at');
            $table->timestamps();
            $table->unique(['order_id', 'farmer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipments');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_service_id');
            $table->dropColumn(['shipping_service_name', 'shipping_service_type']);
        });

        Schema::dropIfExists('shipping_services');
    }
};
