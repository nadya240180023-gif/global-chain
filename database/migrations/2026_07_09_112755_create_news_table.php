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
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('url')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('category'); // logistics, trade, shipping, economy
            $table->string('sentiment')->default('Neutral'); // Positive, Neutral, Negative
            $table->decimal('sentiment_score', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_cache');
    }
};
