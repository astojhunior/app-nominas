<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\TurnoController;
use App\Models\Turno;
use Illuminate\Validation\ValidationException;

class TurnoModuleTest extends TestCase
{
    /**
     * Prueba 1:
     * No permite registrar un turno sin nombre.
     */
    public function test_no_permite_registrar_turno_sin_nombre()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/turnos', 'POST', [
            'nombre'        => '',
            'hora_ingreso'  => '08:00',
            'hora_salida'   => '17:00',
        ]);

        $controller = new TurnoController();
        $controller->store($request);
    }


    /**
     * Prueba 2:
     * No permite registrar turno con hora inválida.
     */
    public function test_no_permite_horas_invalidas()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/turnos', 'POST', [
            'nombre'        => 'Mañana',
            'hora_ingreso'  => '25:00',  // Inválido
            'hora_salida'   => '17:00',
        ]);

        $controller = new TurnoController();
        $controller->store($request);
    }


    /**
 * Prueba 3:
 * No permite actualizar un turno que no existe.
 */
public function test_no_permite_actualizar_turno_inexistente()
{
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    // Simular que el modelo NO existe sin consultar la base de datos
    $mock = \Mockery::mock('overload:App\Models\Turno');
    $mock->shouldReceive('findOrFail')
         ->with(9999)
         ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

    $request = Request::create('/turnos/update', 'POST', [
        'nombre'        => 'Turno Tarde',
        'hora_ingreso'  => '13:00',
        'hora_salida'   => '22:00',
    ]);

    $controller = new \App\Http\Controllers\TurnoController();
    $controller->update($request, 9999); // lanza ModelNotFoundException
}


    /**
     * Prueba 4:
     * No permite eliminar un turno sin contraseña.
     */
    public function test_no_permite_eliminar_sin_password()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/turnos/delete', 'POST', [
            'password' => ''
        ]);

        $controller = new TurnoController();
        $controller->destroy($request, 1);
    }
}
