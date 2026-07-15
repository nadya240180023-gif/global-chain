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
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->integer('weather_score')->default(0);
            $table->integer('inflation_score')->default(0);
            $table->integer('currency_score')->default(0);
            $table->integer('news_score')->default(0);
            $table->integer('total_score')->default(0);
            $table->enum('risk_level', ['Low', 'Medium', 'High'])->default('Low');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
