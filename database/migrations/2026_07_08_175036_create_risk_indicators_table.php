public function up(): void
{
    Schema::create('risk_indicators', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->decimal('temperature',8,2)->nullable();

        $table->decimal('rainfall',8,2)->nullable();

        $table->decimal('wind_speed',8,2)->nullable();

        $table->decimal('gdp',18,2)->nullable();

        $table->decimal('inflation',8,2)->nullable();

        $table->decimal('exchange_rate',12,4)->nullable();

        $table->timestamps();
    });
}