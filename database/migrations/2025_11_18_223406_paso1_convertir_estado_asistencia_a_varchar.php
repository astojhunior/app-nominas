<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Convertir el ENUM actual a VARCHAR temporalmente
            $table->string('estado_asistencia', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Volver al ENUM original (por si haces rollback)
            $table->enum('estado_asistencia', [
                'presente',
                'tarde',
                'falta',
                'descanso',
                'licencia'
            ])->default('presente')->change();
        });
    }
};
