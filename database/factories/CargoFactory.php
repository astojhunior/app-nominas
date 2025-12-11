<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class CargoFactory extends Factory
{
    protected $model = Cargo::class;

    public function definition()
    {
        return [
            'area_id'     => Area::factory(),                // requerido
            'cargo'       => $this->faker->jobTitle(),       // requerido
            'sueldo'      => 1200,                           // requerido ✔
            'descripcion' => 'Cargo generado para pruebas',  // opcional
        ];
    }
}
