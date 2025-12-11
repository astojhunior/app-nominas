<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonos', function (Blueprint $table) {

            // Meses exactos de aplicación del bono (JSON)
            $table->json('meses_aplicacion')
                  ->nullable()
                  ->after('motivo');

            // Si el bono aparece o no en la boleta de fin de mes
            $table->boolean('aparece_en_boleta')
                  ->default(true)
                  ->after('meses_aplicacion');
        });
    }

    public function down(): void
    {
        Schema::table('bonos', function (Blueprint $table) {
            $table->dropColumn('meses_aplicacion');
            $table->dropColumn('aparece_en_boleta');
        });
    }
};
