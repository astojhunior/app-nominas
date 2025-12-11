<?php

namespace Database\Factories;

use App\Models\Boleta;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoletaFactory extends Factory
{
    protected $model = Boleta::class;

    public function definition()
    {
        return [
            'empleado_id' => Empleado::factory(),
            'tipo' => 'fin_mes',
            'periodo_mes' => 1,
            'periodo_anio' => 2025,
            'total_ingresos' => 1200,
            'total_descuentos' => 0,
            'total_aportes' => 0,
            'neto_pagar' => 1200,
            'estado' => 'generado',
            'fecha_generacion' => now(),
        ];
    }
}
