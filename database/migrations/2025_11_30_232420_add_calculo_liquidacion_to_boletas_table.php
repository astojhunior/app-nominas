<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boletas', function (Blueprint $table) {

            $table->string('fecha_ingreso')->nullable();
            $table->string('fecha_cese')->nullable();
            $table->string('antiguedad_texto')->nullable();
            $table->integer('dias_trabajados_reales')->nullable();

            $table->decimal('sueldo', 10,2)->default(0);
            $table->decimal('asignacion', 10,2)->default(0);
            $table->decimal('base', 10,2)->default(0);

            $table->integer('vac_meses')->default(0);
            $table->integer('vac_dias')->default(0);
            $table->decimal('monto_vacaciones', 10,2)->default(0);

            $table->integer('cts_meses')->default(0);
            $table->integer('cts_dias')->default(0);
            $table->decimal('monto_cts', 10,2)->default(0);

            $table->integer('grati_meses')->default(0);
            $table->decimal('monto_grati', 10,2)->default(0);

            $table->integer('dias_ultimo_mes')->default(0);
            $table->decimal('monto_dias_mes', 10,2)->default(0);

            $table->decimal('total_liquidacion', 10,2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('boletas', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_ingreso',
                'fecha_cese',
                'antiguedad_texto',
                'dias_trabajados_reales',
                'sueldo',
                'asignacion',
                'base',
                'vac_meses',
                'vac_dias',
                'monto_vacaciones',
                'cts_meses',
                'cts_dias',
                'monto_cts',
                'grati_meses',
                'monto_grati',
                'dias_ultimo_mes',
                'monto_dias_mes',
                'total_liquidacion'
            ]);
        });
    }
};
