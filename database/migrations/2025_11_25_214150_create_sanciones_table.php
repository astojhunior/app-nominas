<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanciones', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('empleado_id')
                  ->constrained('empleados')
                  ->onDelete('cascade');

            $table->foreignId('tipo_sancion_id')
                  ->constrained('tipos_sancion');

            // Datos principales
            $table->date('fecha_aplicacion');

            // Solo si requiere días
            $table->integer('dias_suspension')->nullable();
            $table->date('fecha_inicio_suspension')->nullable();
            $table->date('fecha_fin_suspension')->nullable();

            // Información adicional
            $table->text('motivo')->nullable();
            $table->string('documento_adj')->nullable(); // PDF futuro

            // Estado
            $table->enum('estado', ['activo', 'cumplido', 'anulado'])
                  ->default('activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};
