<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletas', function (Blueprint $table) {
            $table->id();

            // Relación con empleado
            $table->foreignId('empleado_id')->constrained()->cascadeOnDelete();

            // Tipo de boleta
            // fin_mes, gratificacion, cts, liquidacion, bono_adicional
            $table->string('tipo', 30);

            // Periodo aplicable (solo para fin de mes, gratificación, cts)
            $table->unsignedTinyInteger('periodo_mes')->nullable(); // 1-12
            $table->unsignedSmallInteger('periodo_anio')->nullable(); // 2025, etc.

            // Totales calculados
            $table->decimal('total_ingresos', 10, 2)->default(0);
            $table->decimal('total_descuentos', 10, 2)->default(0);
            $table->decimal('total_aportes', 10, 2)->default(0);
            $table->decimal('neto_pagar', 10, 2)->default(0);

            // Estado: generado, anulado
            $table->string('estado', 20)->default('generado');

            $table->timestamp('fecha_generacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
