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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->json('invoice_data');
            $table->foreignId('refer_id')->nullable()->constrained('references');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('employee_id')->constrained('employees');
            $table->bigInteger('total_amount');
            $table->bigInteger('paid_amount');
            $table->bigInteger('payable_amount');
            $table->bigInteger('online_fee')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->boolean('due_status');
            $table->bigInteger('due_amount')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
