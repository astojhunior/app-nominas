<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {

            // Solo agregar si NO existe
            if (!Schema::hasColumn('asistencias', 'turno_id')) {
                $table->unsignedBigInteger('turno_id')->nullable()->after('contrato_id');

                $table->foreign('turno_id')
                    ->references('id')->on('turnos')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {

            if (Schema::hasColumn('asistencias', 'turno_id')) {
                $table->dropForeign(['turno_id']);
                $table->dropColumn('turno_id');
            }
        });
    }
};
