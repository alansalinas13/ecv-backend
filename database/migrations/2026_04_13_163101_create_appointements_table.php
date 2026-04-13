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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->dateTime('appointment_date');

            $table->enum('status', ['pending', 'confirmed', 'cancelled'])
                  ->default('pending');///el enum solo te deja insertar los valores que le pones ahi, o sea es para valores fijos, por eso lo agregamos como string(si agregamos Hola, inseratara pendign)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointements');
    }
};
