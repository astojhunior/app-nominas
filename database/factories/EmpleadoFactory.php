<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpleadoFactory extends Factory
{
    protected $model = Empleado::class;

    public function definition()
    {
        return [
            'nombres'            => $this->faker->firstName(),
            'apellidos'          => $this->faker->lastName(),
            'dni'                => $this->faker->numerify('########'),
            'correo'             => $this->faker->unique()->safeEmail(),
            'fecha_nacimiento'   => $this->faker->date(),
            'sexo'               => 'masculino', 
            'direccion'          => 'Test dirección',
            'estado_civil'       => 'soltero',
            'nacionalidad'       => 'peruana',
            'telefono'           => '987654321',
            'contacto_nombre'    => 'Contacto',
            'contacto_telefono'  => '999999999',
            'contacto_parentesco'=> 'Hermano',
            'estado'             => 'activo',
            'fecha_cese'         => null,
            'asignacion_familiar'=> false,
            'bonificacion'       => 0,
        ];
    }
}
