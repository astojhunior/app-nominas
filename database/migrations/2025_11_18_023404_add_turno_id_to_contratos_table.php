<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {

            // Agregamos turno_id con relación
            $table->foreignId('turno_id')
                  ->nullable()
                  ->constrained('turnos')
                  ->nullOnDelete()
                  ->after('cargo_id');  // Ubicación opcional
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign(['turno_id']);
            $table->dropColumn('turno_id');
        });
    }
};
