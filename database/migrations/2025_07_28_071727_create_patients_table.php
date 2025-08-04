<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('guardian_name')->nullable();
            $table->string('mobile_phone');
            $table->string('gender');
            $table->string('type')->default('Old');
            $table->tinyInteger('age');
            $table->date('birth_date')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('blood_groupe')->nullable();
            $table->string('address_line')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('postal_code')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
