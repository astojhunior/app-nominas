<?php

namespace Database\Factories;

use App\Models\Asistencia;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsistenciaFactory extends Factory
{
    protected $model = Asistencia::class;

    public function definition()
    {
        return [
            'empleado_id' => Empleado::factory(),
            'fecha' => now()->startOfMonth(),
            'estado_asistencia' => 'turno',
        ];
    }
}
