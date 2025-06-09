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
        Schema::create('repair_service', function (Blueprint $table) {
            $table->char('service_id', 36)->primary();
            $table->string('service_name', 255)->notNull();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->notNull();
            $table->integer('time_taken')->notNull();
            $table->char('model_id', 36)->notNull();

            // Foreign Key Constraint
            $table->foreign('model_id')->references('model_id')->on('device_model')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_service');
    }
};
