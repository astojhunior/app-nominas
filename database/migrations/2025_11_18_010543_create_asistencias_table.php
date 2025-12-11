<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            // RELACIONES
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->cascadeOnDelete();

            $table->foreignId('contrato_id')
                ->nullable()
                ->constrained('contratos')
                ->nullOnDelete();

            // FECHA Y HORAS PRINCIPALES
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();

            // BREAK
            $table->time('break_inicio')->nullable();
            $table->time('break_fin')->nullable();
            $table->unsignedInteger('tardanza_break')->default(0);

            // TARDANZAS
            $table->unsignedInteger('tardanza_inicio_turno')->default(0);
            $table->unsignedInteger('tardanza_total')->default(0); // suma automática

            // OTROS CAMPOS EXISTENTES
            $table->enum('tipo_jornada', ['completa', 'media', 'extra'])
                  ->default('completa');

            $table->enum('estado_asistencia', ['presente', 'tarde', 'falta', 'descanso', 'licencia'])
                  ->default('presente');

            $table->decimal('horas_extra', 5, 2)->default(0);

            $table->string('justificacion', 255)->nullable();
            $table->boolean('marcado_manual')->default(false);

            $table->enum('origen_marcado', ['sistema', 'manual', 'importado'])
                  ->default('manual');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            // NO DUPLICADOS
            $table->unique(['empleado_id', 'fecha'], 'asistencia_empleado_fecha_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
