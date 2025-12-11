<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('boletas', function (Blueprint $table) {

        $cols = Schema::getColumnListing('boletas');

        if (!in_array('fecha_ingreso', $cols))
            $table->string('fecha_ingreso')->nullable();

        if (!in_array('fecha_cese', $cols))
            $table->string('fecha_cese')->nullable();

        if (!in_array('antiguedad_texto', $cols))
            $table->string('antiguedad_texto')->nullable();

        if (!in_array('dias_trabajados_reales', $cols))
            $table->integer('dias_trabajados_reales')->nullable();

        if (!in_array('sueldo', $cols))
            $table->decimal('sueldo', 10, 2)->nullable();

        if (!in_array('asignacion', $cols))
            $table->decimal('asignacion', 10, 2)->nullable();

        if (!in_array('base', $cols))
            $table->decimal('base', 10, 2)->nullable();

        if (!in_array('vac_meses', $cols))
            $table->integer('vac_meses')->nullable();

        if (!in_array('vac_dias', $cols))
            $table->integer('vac_dias')->nullable();

        if (!in_array('monto_vacaciones', $cols))
            $table->decimal('monto_vacaciones', 10, 2)->nullable();

        if (!in_array('cts_meses', $cols))
            $table->integer('cts_meses')->nullable();

        if (!in_array('cts_dias', $cols))
            $table->integer('cts_dias')->nullable();

        if (!in_array('monto_cts', $cols))
            $table->decimal('monto_cts', 10, 2)->nullable();

        if (!in_array('grati_meses', $cols))
            $table->integer('grati_meses')->nullable();

        if (!in_array('monto_grati', $cols))
            $table->decimal('monto_grati', 10, 2)->nullable();

        if (!in_array('dias_ultimo_mes', $cols))
            $table->integer('dias_ultimo_mes')->nullable();

        if (!in_array('monto_dias_mes', $cols))
            $table->decimal('monto_dias_mes', 10, 2)->nullable();

        if (!in_array('total_liquidacion', $cols))
            $table->decimal('total_liquidacion', 10, 2)->nullable();
    });
}

public function down()
{
    Schema::table('boletas', function (Blueprint $table) {

        $cols = Schema::getColumnListing('boletas');

        foreach([
            'fecha_ingreso','fecha_cese','antiguedad_texto','dias_trabajados_reales',
            'sueldo','asignacion','base',
            'vac_meses','vac_dias','monto_vacaciones',
            'cts_meses','cts_dias','monto_cts',
            'grati_meses','monto_grati',
            'dias_ultimo_mes','monto_dias_mes',
            'total_liquidacion'
        ] as $col){
            if(in_array($col, $cols)) $table->dropColumn($col);
        }

    });
}


};
