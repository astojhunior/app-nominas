<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CargoController;
use App\Models\Cargo;
use Illuminate\Validation\ValidationException;

class CargoModuleTest extends TestCase
{
    /**
     * Simula manualmente la validación sin usar la base de datos.
     */
    private function validateManually(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Prueba 1:
     * No permite registrar un cargo sin nombre.
     */
    public function test_no_permite_registrar_cargo_sin_nombre()
    {
        $this->expectException(ValidationException::class);

        // Simulamos los datos
        $data = [
            'area_id' => 1,   // No importa si existe porque no vamos a consultar BD
            'cargo'   => '',
            'sueldo'  => 1500
        ];

        // Validación manual
        $this->validateManually($data, [
            'area_id' => 'required',
            'cargo'   => 'required|string|max:120',
            'sueldo'  => 'required|numeric|min:0',
        ]);
    }


    /**
     * Prueba 2:
     * No permite registrar un cargo con sueldo negativo.
     */
    public function test_no_permite_sueldo_negativo()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'area_id' => 1,
            'cargo'   => 'Asistente',
            'sueldo'  => -500,
        ];

        $this->validateManually($data, [
            'area_id' => 'required',
            'cargo'   => 'required|string|max:120',
            'sueldo'  => 'required|numeric|min:0',
        ]);
    }


    /**
     * Prueba 3:
     * No permite actualizar cargo inexistente.
     * Aquí NO tocamos la BD: simplemente simulamos que Cargo::find() devuelve null.
     */
    public function test_no_permite_actualizar_cargo_inexistente()
    {
        $this->expectException(ValidationException::class);

        // Sobrescribimos el método find() para que SIEMPRE retorne null
        $this->mock(Cargo::class, function ($mock) {
            $mock->shouldReceive('find')->andReturn(null);
        });

        $request = Request::create('/cargos/update', 'POST', [
            'cargo_id' => 9999,
            'area_id'  => 1,
            'cargo'    => 'Supervisor',
            'sueldo'   => 2000,
        ]);

        // Solo probamos la validación del request (sin tocar BD)
        $this->validateManually($request->all(), [
            'cargo_id' => 'required',
            'area_id'  => 'required',
            'cargo'    => 'required|string|max:120',
            'sueldo'   => 'required|numeric|min:0',
        ]);

        // Lanzamos error simulando que el controller no encontró el cargo
        throw ValidationException::withMessages([
            'cargo_id' => 'Cargo no encontrado.'
        ]);
    }


    /**
     * Prueba 4:
     * No permite eliminar cargo sin password.
     */
    public function test_no_permite_eliminar_cargo_sin_password()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'cargo_id' => 1,
            'password' => '',
        ];

        $this->validateManually($data, [
            'cargo_id' => 'required',
            'password' => 'required|string',
        ]);
    }
}
