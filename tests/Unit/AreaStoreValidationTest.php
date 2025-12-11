<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AreaController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AreaStoreValidationTest extends TestCase
{
    /** @test */
    public function no_permite_registrar_area_sin_nombre()
    {
        $controller = new AreaController();

        // Simulamos request vacío
        $request = Request::create('/rrhh/areas_cargos', 'POST', [
            'nombre' => ''  // vacío → debe fallar
        ]);

        try {
            $controller->store($request);
            $this->fail('La validación no se ejecutó correctamente.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nombre', $e->errors());
        }
    }
}
