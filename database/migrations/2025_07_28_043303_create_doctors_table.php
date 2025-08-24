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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('working_place')->nullable();
            $table->string('available_days')->nullable();
            $table->string('bmdc_code');
            $table->string('job_designation')->nullable();
            $table->string('booking_phone');
            $table->string('gender');
            $table->string('degree_name')->nullable();
            $table->string('consultancy_fee');
            $table->string('mobile');
            $table->string('provide_service')->nullable();
            $table->string('about')->nullable();
            $table->string('email');
            $table->string('speciality')->nullable();
            $table->string('password');
            $table->date('starting_pratice')->nullable();
            $table->string('achievement')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
