<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertimos todos los valores antiguos a uno válido
        DB::table('asistencias')
            ->whereNotIn('estado_asistencia', [
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
            ])
            ->update([
                'estado_asistencia' => 'turno'
            ]);
    }

    public function down(): void
    {
        // No es necesario revertir valores
    }
};
