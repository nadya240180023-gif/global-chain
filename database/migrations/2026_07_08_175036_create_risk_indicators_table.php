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
        Schema::create('risk_indicators', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('temperature', 8, 2)->nullable();
            $table->decimal('rainfall', 8, 2)->nullable();
            $table->decimal('wind_speed', 8, 2)->nullable();

            $table->decimal('gdp', 18, 2)->nullable();
            $table->decimal('inflation', 8, 2)->nullable();
            $table->decimal('exchange_rate', 12, 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_indicators');
    }
};