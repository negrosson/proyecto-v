<?php

declare(strict_types=1);

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
        Schema::create('quote_logs', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->date('event_date')->nullable()->index();
            $table->string('location');
            $table->unsignedInteger('total_amount');
            $table->unsignedInteger('items_count');
            $table->json('items_payload');
            $table->string('status')->default('pendiente')->index();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_logs');
    }
};
