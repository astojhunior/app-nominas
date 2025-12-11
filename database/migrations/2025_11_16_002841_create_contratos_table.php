<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');
            $table->foreignId('cargo_id')->constrained('cargos')->onDelete('restrict');

            // Tipo de contrato
            $table->enum('tipo_contrato', ['temporal', 'indefinido']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Sueldo snapshot (histórico)
            $table->decimal('sueldo', 10, 2);

            // Pensiones
            $table->enum('sistema_pension', ['AFP', 'ONP']);
            $table->string('afp_nombre')->nullable();
            $table->string('afp_tipo')->nullable();

            // Información de pago
            $table->enum('metodo_pago', ['transferencia', 'efectivo']);
            $table->string('banco')->nullable();
            $table->string('cuenta_bancaria')->nullable();
            $table->enum('tipo_cuenta', ['ahorros', 'corriente'])->nullable();

            // Estado del contrato
            $table->enum('estado_contrato', ['activo', 'vencido', 'rescindido'])->default('activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
