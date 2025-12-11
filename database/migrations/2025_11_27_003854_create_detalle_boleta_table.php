<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_boleta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('boleta_id')->constrained('boletas')->cascadeOnDelete();

            // ingreso | descuento | aporte
            $table->string('tipo', 20);

            $table->string('concepto', 100);
            $table->decimal('monto', 10, 2);

            // Para descuentos con motivo (vaso roto, perdida, etc.)
            $table->string('motivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_boleta');
    }
};
