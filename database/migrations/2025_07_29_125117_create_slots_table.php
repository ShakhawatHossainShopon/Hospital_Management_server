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
        Schema::create('slots', function (Blueprint $table) {
        $table->id();
        $table->time('time');
        $table->foreignId('scedule_id')->constrained('scedules')->onDelete('cascade');
        $table->boolean('is_booked')->default(false);
        $table->string('time_indicator');
        $table->string('name')->nullable();
        $table->string('phone')->nullable();
        $table->string('type')->nullable();
        $table->string('patient_age')->nullable();
        $table->string('status')->default('pending');
        $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
        $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
