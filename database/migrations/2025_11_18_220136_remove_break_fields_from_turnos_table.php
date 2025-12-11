<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            if (Schema::hasColumn('turnos', 'break_inicio')) {
                $table->dropColumn('break_inicio');
            }
            if (Schema::hasColumn('turnos', 'break_fin')) {
                $table->dropColumn('break_fin');
            }
            if (Schema::hasColumn('turnos', 'break_duracion')) {
                $table->dropColumn('break_duracion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->time('break_inicio')->nullable();
            $table->time('break_fin')->nullable();
            $table->integer('break_duracion')->default(0);
        });
    }
};
