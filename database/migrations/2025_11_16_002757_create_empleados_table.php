<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('nombres', 120);
            $table->string('apellidos', 120);
            $table->string('dni', 8)->unique();
            $table->string('correo', 150)->unique();
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['Masculino', 'Femenino', 'Otro']);
            $table->string('direccion')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('telefono', 20)->nullable();

            // Contacto de emergencia
            $table->string('contacto_nombre', 120)->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_parentesco', 60)->nullable();

            // Administrativos
            $table->string('foto')->nullable();
            $table->enum('estado', ['activo', 'baja', 'suspendido'])->default('activo');
            $table->text('observaciones')->nullable();

            // Beneficios
            $table->boolean('asignacion_familiar')->default(false);
            $table->decimal('bonificacion', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
