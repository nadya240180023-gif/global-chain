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
        Schema::create('risk_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('risk_indicator_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('risk_score');
            $table->enum('risk_level', [
                'Low',
                'Medium',
                'High'
            ]);

            $table->text('recommendation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_analyses');
    }
};