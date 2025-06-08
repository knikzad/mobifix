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
        Schema::create('app_user', function (Blueprint $table) {
            $table->char('user_id', 36)->primary();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->unique();
            $table->string('password', 255);
            $table->string('salt', 255);
            $table->string('user_type', 50);
            $table->string('status', 20);
            $table->string('street_name', 100)->nullable();
            $table->string('house_number', 10)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->char('referred_by', 36)->nullable();
            $table->foreign('referred_by')->references('user_id')->on('app_user')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_user');
    }
};
