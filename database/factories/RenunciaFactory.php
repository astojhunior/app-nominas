<?php

namespace Database\Factories;

use App\Models\Renuncia;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

class RenunciaFactory extends Factory
{
    /**
     * El modelo correspondiente a esta factory.
     *
     * @var string
     */
    protected $model = Renuncia::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Usamos la misma fecha para renuncia y cese por defecto
        $fecha = $this->faker->date('Y-m-d');

        return [
            'empleado_id'    => Empleado::factory(),   // Se puede sobreescribir en el test
            'fecha_renuncia' => $fecha,
            'fecha_cese'     => $fecha,
            'motivo'         => $this->faker->sentence(6),
            'documento_adj'  => null,
            'estado'         => 'registrada',
        ];
    }

    /**
     * Estado "anulada" por si lo quieres usar en el futuro.
     */
    public function anulada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'anulada',
        ]);
    }
}
