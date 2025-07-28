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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('guardian_name');
            $table->string('mobile_phone');
            $table->string('gender');
            $table->tinyInteger('age');
            $table->date('birth_date');
            $table->string('height');
            $table->string('weight');
            $table->string('blood_groupe');
            $table->string('address_line');
            $table->string('city');
            $table->string('area');
            $table->string('postal_code');
            $table->foreignId('user_id')->constrained();
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
