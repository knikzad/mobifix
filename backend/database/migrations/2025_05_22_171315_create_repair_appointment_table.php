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
        Schema::create('repair_appointment', function (Blueprint $table) {
            $table->char('appointment_id', 36)->primary();
            $table->char('customer_id', 36)->nullable();
            $table->char('employee_id', 36)->nullable();
            $table->char('method_id', 36)->nullable();
            $table->timestamp('date_time')->notNull();
            $table->string('status', 20)->notNull();
            $table->decimal('total_price', 10, 2)->notNull();

            // Foreign Keys
            $table->foreign('customer_id')->references('user_id')->on('customer')->onDelete('SET NULL');
            $table->foreign('employee_id')->references('user_id')->on('employee')->onDelete('SET NULL');
            $table->foreign('method_id')->references('method_id')->on('service_method')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_appointment');
    }
};
