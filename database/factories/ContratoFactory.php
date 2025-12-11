<?php

namespace Database\Factories;

use App\Models\Contrato;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContratoFactory extends Factory
{
    protected $model = Contrato::class;

    public function definition(): array
    {
        return [
            'empleado_id'      => \App\Models\Empleado::factory(),
            'area_id'          => \App\Models\Area::factory(),
            'cargo_id'         => \App\Models\Cargo::factory(),
            'tipo_contrato'    => $this->faker->randomElement(['temporal', 'indefinido']),
            'fecha_inicio'     => now()->subMonths(6)->format('Y-m-d'),
            'fecha_fin'        => null, // Es permitido porque tu BD acepta NULL
            'sueldo'           => 1200,
            'sistema_pension'  => $this->faker->randomElement(['AFP', 'ONP']),
            'afp_nombre'       => null,
            'afp_tipo'         => null,
            'metodo_pago'      => $this->faker->randomElement(['transferencia', 'efectivo']),
            'banco'            => null,
            'cuenta_bancaria'  => null,
            'tipo_cuenta'      => $this->faker->randomElement(['ahorros', 'corriente']),
            'estado_contrato'  => 'activo',
        ];
    }
}
