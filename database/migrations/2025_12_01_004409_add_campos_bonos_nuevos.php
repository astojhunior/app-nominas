<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonos', function (Blueprint $table) {

            // A QUIÉN VA DIRIGIDO EL BONO
            if (!Schema::hasColumn('bonos', 'dirigido_a')) {
                $table->enum('dirigido_a', ['empleado', 'cargo', 'todos'])
                      ->default('empleado')
                      ->after('id');
            }

            // PARA BONOS DIRIGIDOS A UN CARGO
            if (!Schema::hasColumn('bonos', 'cargo_id')) {
                $table->unsignedBigInteger('cargo_id')->nullable()->after('empleado_id');

                // Opcional si quieres integridad referencial
                // $table->foreign('cargo_id')->references('id')->on('cargos')->nullOnDelete();
            }

            // YA TIENES meses_aplicacion Y aparece_en_boleta (no lo toco)

            //asegurar formato JSON
            $table->json('meses_aplicacion')->nullable()->change();

            //asegurar booleano
            $table->boolean('aparece_en_boleta')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bonos', function (Blueprint $table) {
            $table->dropColumn(['dirigido_a', 'cargo_id']);
        });
    }
};
