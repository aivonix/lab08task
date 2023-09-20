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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->default(1);
            $table->unsignedBigInteger('discount_card_id')->default(1);
            $table->string('plate_number', 10)->unique();
            $table->string('description')->nullable();
            $table->foreign('category_id')->references('id')->on('vehicle_categories');
            $table->foreign('discount_card_id')->references('id')->on('discounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
