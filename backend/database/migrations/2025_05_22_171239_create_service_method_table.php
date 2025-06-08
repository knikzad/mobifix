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
        Schema::create('service_method', function (Blueprint $table) {
            $table->char('method_id', 36)->primary();
            $table->string('method_name', 50);
            $table->integer('estimated_time')->notNull();
            $table->decimal('cost', 10, 2);
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_method');
    }
};
