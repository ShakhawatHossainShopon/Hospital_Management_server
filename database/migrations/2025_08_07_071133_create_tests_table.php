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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('code')->nullable();
            $table->foreignId('groupe_id')->constrained('groupes')->onDelete('cascade');
            $table->string('unit_price');
            $table->smallInteger('max_discound')->nullable();
            $table->text('des')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
