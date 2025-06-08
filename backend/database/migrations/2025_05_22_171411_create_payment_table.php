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
        Schema::create('payment', function (Blueprint $table) {
            $table->char('appointment_id', 36);
            $table->char('payment_number', 36);
            $table->decimal('amount', 10, 2)->notNull();
            $table->string('payment_status', 10)->notNull();
            $table->string('payment_method', 50)->notNull();
            $table->timestamp('payment_date_time')->notNull();

            // Defining Composite Primary Key
            $table->primary(['appointment_id', 'payment_number']);

            // Foreign Key Constraint
            $table->foreign('appointment_id')->references('appointment_id')->on('repair_appointment')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
