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
        Schema::create('repair_service_appointment', function (Blueprint $table) {
            $table->char('service_id', 36);
            $table->char('appointment_id', 36);

            // Defining Composite Primary Key
            $table->primary(['service_id', 'appointment_id']);

            // Foreign Keys
            $table->foreign('service_id')->references('service_id')->on('repair_service')->onDelete('CASCADE');
            $table->foreign('appointment_id')->references('appointment_id')->on('repair_appointment')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_service_appointment');
    }
};
