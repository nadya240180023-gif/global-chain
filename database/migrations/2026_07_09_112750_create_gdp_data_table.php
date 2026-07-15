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
    Schema::create('gdp_data', function (Blueprint $table) {
        $table->id();

        $table->foreignId('country_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->year('year');

        $table->decimal('gdp_value', 18, 2)->nullable();

        $table->decimal('gdp_growth', 8, 2)->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdp_data');
    }
};
