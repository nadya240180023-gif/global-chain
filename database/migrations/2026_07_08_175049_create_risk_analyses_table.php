public function up(): void
{
    Schema::create('risk_analyses', function (Blueprint $table) {

        $table->id();

        $table->foreignId('shipment_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->integer('weather_score');

        $table->integer('economic_score');

        $table->integer('transport_score');

        $table->integer('total_score');

        $table->string('risk_level');

        $table->text('recommendation')->nullable();

        $table->timestamps();
    });
}