public function up(): void
{
    Schema::create('shipments', function (Blueprint $table) {

        $table->id();

        $table->foreignId('supplier_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->string('product_name');

        $table->integer('quantity');

        $table->date('shipping_date');

        $table->date('estimated_arrival');

        $table->enum('status',[
            'Pending',
            'Shipping',
            'Arrived',
            'Delayed'
        ])->default('Pending');

        $table->timestamps();
    });
}