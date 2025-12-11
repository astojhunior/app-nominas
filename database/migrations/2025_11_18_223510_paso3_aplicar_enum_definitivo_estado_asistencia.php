<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {

            // Aplicar ENUM definitivo con todos los estados válidos
            $table->enum('estado_asistencia', [
                'turno',
                'descanso',
                'descanso_medico',
                'feriado_no_trabajado',
                'feriado_trabajado',
                'falta',
                'licencia_sin_goce',
                'licencia_con_goce',
                'suspension',
                'licencia_maternidad',
                'licencia_paternidad',
                'turno_incompleto'
            ])->default('turno')->change();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {

            // Revertir al VARCHAR usado temporalmente
            $table->string('estado_asistencia', 50)->change();
        });
    }
};
