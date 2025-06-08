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
        Schema::create('device_model', function (Blueprint $table) {
            $table->char('model_id', 36)->primary();
            $table->string('model_name', 100)->notNull();
            $table->integer('release_year')->notNull();
            $table->char('brand_id', 36)->notNull();

            // Foreign Key Constraint
            $table->foreign('brand_id')->references('brand_id')->on('brand')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_model');
    }
};
