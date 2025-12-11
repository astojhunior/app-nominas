<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();

            // Nombre del turno (Mañana, Tarde, Noche, Completo, Personalizado...)
            $table->string('nombre', 50);

            // Horarios principales
            $table->time('hora_ingreso');
            $table->time('hora_salida');

            // Break (descanso)
            $table->time('break_inicio')->nullable();

            // break_fin se calcula sumando 45 min al break_inicio
            // pero igual lo guardamos aquí para consultas rápidas
            $table->time('break_fin')->nullable();

            // Duración fija del break en minutos
            $table->integer('break_duracion')->default(45);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};

