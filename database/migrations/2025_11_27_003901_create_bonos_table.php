<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonos', function (Blueprint $table) {
            $table->id();

            // Si es masivo no tendrá empleado_id
            $table->foreignId('empleado_id')->nullable()
                ->constrained()->cascadeOnDelete();

            $table->string('nombre', 100);
            $table->decimal('monto', 10, 2);

            // individual | masivo
            $table->string('tipo', 20)->default('individual');

            // Para control de vigencia
            $table->date('fecha_aplicacion');

            // activo | aplicado | anulado
            $table->string('estado', 20)->default('activo');

            $table->string('motivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonos');
    }
};
