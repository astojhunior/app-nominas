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
    Schema::table('contratos', function (Blueprint $table) {

        // 1. Primero elimina la foreign key
        $table->dropForeign(['turno_id']);

        // 2. Luego elimina la columna
        $table->dropColumn('turno_id');
    });
}

public function down()
{
    Schema::table('contratos', function (Blueprint $table) {
        $table->unsignedBigInteger('turno_id')->nullable();

        $table->foreign('turno_id')->references('id')->on('turnos')
              ->nullOnDelete();
    });
}


};
