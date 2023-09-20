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
        Schema::create('parking_lot_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_lot_id')->constrained();
            $table->string('category');
            $table->decimal('day_rate', 5, 2);
            $table->decimal('night_rate', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_lot_rates');
    }
};
