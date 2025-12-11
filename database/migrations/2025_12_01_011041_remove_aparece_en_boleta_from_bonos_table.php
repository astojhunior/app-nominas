<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonos', function (Blueprint $table) {
            if (Schema::hasColumn('bonos', 'aparece_en_boleta')) {
                $table->dropColumn('aparece_en_boleta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bonos', function (Blueprint $table) {
            $table->boolean('aparece_en_boleta')->default(false);
        });
    }
};
