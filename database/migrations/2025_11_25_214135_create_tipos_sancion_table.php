<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_sancion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->boolean('requiere_dias')->default(false);
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true); // activo / inactivo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_sancion');
    }
};
