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
        Schema::create('device_type_brand', function (Blueprint $table) {
            $table->char('device_type_id', 36);
            $table->char('brand_id', 36);

            // Defining Composite Primary Key
            $table->primary(['device_type_id', 'brand_id']);

            // Foreign Keys
            $table->foreign('device_type_id')->references('device_type_id')->on('device_type')->onDelete('CASCADE');
            $table->foreign('brand_id')->references('brand_id')->on('brand')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_type_brand');
    }
};
