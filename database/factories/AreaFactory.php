<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition()
    {
        return [
            'nombre'      => $this->faker->word(),       
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
