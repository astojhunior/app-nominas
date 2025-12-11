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
    Schema::table('asistencias', function (Blueprint $table) {
        
        // Solo agrega los nuevos campos
        if (!Schema::hasColumn('asistencias', column: 'break_inicio')) {
            $table->time('break_inicio')->nullable()->after('hora_salida');
        }

        if (!Schema::hasColumn('asistencias', 'break_fin')) {
            $table->time('break_fin')->nullable()->after('break_inicio');
        }

        if (!Schema::hasColumn('asistencias', 'tardanza_break')) {
            $table->unsignedInteger('tardanza_break')->default(0)->after('break_fin');
        }

        if (!Schema::hasColumn('asistencias', 'tardanza_inicio_turno')) {
            $table->unsignedInteger('tardanza_inicio_turno')->default(0)->after('tardanza_break');
        }

        if (!Schema::hasColumn('asistencias', 'tardanza_total')) {
            $table->unsignedInteger('tardanza_total')->default(0)->after('tardanza_inicio_turno');
        }
    });
}

public function down(): void
{
    Schema::table('asistencias', function (Blueprint $table) {
        $table->dropColumn([
            'break_inicio',
            'break_fin',
            'tardanza_break',
            'tardanza_inicio_turno',
            'tardanza_total',
        ]);
    });
}

};
