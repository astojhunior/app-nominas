<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ modificar enum estado (solo si existe la columna)
        if (Schema::hasColumn('empleados', 'estado')) {
            DB::statement("
                ALTER TABLE empleados
                MODIFY estado ENUM('activo','baja') NOT NULL DEFAULT 'activo'
            ");
        }

        // ✅ agregar fecha_cese solo si no existe
        if (!Schema::hasColumn('empleados', 'fecha_cese')) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->date('fecha_cese')->nullable()->after('estado');
            });
        }
    }

    public function down(): void
    {
        // ❗ revertir enum a solo 'activo'
        if (Schema::hasColumn('empleados', 'estado')) {
            DB::statement("
                ALTER TABLE empleados
                MODIFY estado ENUM('activo') NOT NULL DEFAULT 'activo'
            ");
        }

        // eliminar fecha_cese si se creó
        if (Schema::hasColumn('empleados', 'fecha_cese')) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->dropColumn('fecha_cese');
            });
        }
    }
};
