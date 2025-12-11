<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_archivos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            $table->string('ruta_archivo');
            $table->enum('tipo_archivo', ['contrato_firmado', 'adenda', 'otro']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_archivos');
    }
};
