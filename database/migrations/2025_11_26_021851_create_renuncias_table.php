<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renuncias', function (Blueprint $table) {
            $table->id();

            // relación empleado
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->onDelete('cascade');

            $table->date('fecha_renuncia');   // fecha en que presentó carta
            $table->date('fecha_cese');       // último día de trabajo

            $table->text('motivo')->nullable(); // opcional
            $table->string('documento_adj')->nullable(); // PDF o imagen

            $table->enum('estado', ['registrada', 'anulada'])
                ->default('registrada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renuncias');
    }
};
