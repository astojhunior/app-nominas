<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AreaController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AreaNameFormatTest extends TestCase
{
    /** @test */
    public function no_permite_un_nombre_de_area_mayor_a_120_caracteres()
    {
        $controller = new AreaController();

        $nombreLargo = str_repeat('A', 150); // 150 caracteres

        $request = Request::create('/rrhh/areas_cargos', 'POST', [
            'nombre' => $nombreLargo
        ]);

        try {
            $controller->store($request);
            $this->fail('La validación no se ejecutó.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nombre', $e->errors());
        }
    }
}
