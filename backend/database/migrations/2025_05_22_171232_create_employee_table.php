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
        Schema::create('employee', function (Blueprint $table) {
            $table->char('user_id', 36)->primary();
            $table->string('job_title', 255);
            $table->decimal('salary', 10, 2);
            $table->date('hire_date');
            $table->string('shift', 50);
            $table->string('role', 100);
            $table->foreign('user_id')->references('user_id')->on('app_user')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
