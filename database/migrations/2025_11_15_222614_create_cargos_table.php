<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('area_id')
                  ->constrained('areas')
                  ->onDelete('cascade'); // si se elimina un área, elimina sus cargos

            $table->string('cargo', 120);
            $table->decimal('sueldo', 8, 2);
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
