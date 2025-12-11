<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\TipoSancion;
use App\Models\Sancion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TipoSancionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear admin para autenticación
        $this->admin = Admin::create([
            'nombre'   => 'Administrador',
            'email'    => 'admin@nominaempleados.com',
            'password' => Hash::make('123456'),
        ]);

        $this->actingAs($this->admin, 'admin');
    }

    /** @test */
    public function puede_registrar_tipo_de_sancion()
    {
        $response = $this->post(route('tiposancion.store'), [
            'nombre'        => 'Suspensión',
            'descripcion'   => 'Por falta grave',
            'requiere_dias' => true,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tipos_sancion', [
            'nombre' => 'Suspensión'
        ]);
    }

    /** @test */
    public function no_permite_nombre_duplicado()
    {
        TipoSancion::create([
            'nombre'        => 'Suspensión',
            'requiere_dias' => true,
        ]);

        $response = $this->post(route('tiposancion.store'), [
            'nombre'        => 'Suspensión',
            'requiere_dias' => false,
        ]);

        $response->assertSessionHasErrors(['nombre']);
    }

    /** @test */
    public function puede_actualizar_tipo_de_sancion()
    {
        $tipo = TipoSancion::create([
            'nombre'        => 'Amonestación',
            'requiere_dias' => false,
        ]);

        $response = $this->put(route('tiposancion.update', $tipo->id), [
            'nombre'        => 'Amonestación leve',
            'descripcion'   => 'Actualizado',
            'requiere_dias' => false,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tipos_sancion', [
            'id'     => $tipo->id,
            'nombre' => 'Amonestación leve',
        ]);
    }

    /** @test */
/** @test */
public function no_permite_eliminar_si_tiene_sanciones_asociadas()
{
    // Crear admin real
    $admin = \App\Models\Admin::create([
        'nombre' => 'Administrador Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
    ]);
    $this->actingAs($admin, 'admin');

    // Crear empleado real
    $empleado = \App\Models\Empleado::create([
        'nombres' => 'Empleado',
        'apellidos' => 'Prueba',
        'dni' => '12345678',
        'correo' => 'empleado@test.com',
        'fecha_nacimiento' => '1990-01-01',
        'sexo' => 'masculino',
        'direccion' => 'Test',
        'estado_civil' => 'soltero',
        'nacionalidad' => 'peruana',
        'telefono' => '987654321',
        'contacto_nombre' => 'Contacto',
        'contacto_telefono' => '999999999',
        'contacto_parentesco' => 'Hermano',
        'estado' => 'activo',
    ]);

    // Crear tipo de sanción
    $tipo = TipoSancion::create([
        'nombre' => 'Suspensión grave',
        'descripcion' => 'Prueba',
        'requiere_dias' => true,
        'estado' => true,
    ]);

    // Crear sanción asociada
    \App\Models\Sancion::create([
        'empleado_id' => $empleado->id,
        'tipo_sancion_id' => $tipo->id,
        'fecha_aplicacion' => '2025-01-01'
    ]);

    // Intentar eliminar el tipo
    $response = $this->delete(route('tiposancion.delete', $tipo->id), [
        'password' => '123456'
    ]);

    // Debe fallar
    $response->assertSessionHas('error');

    // Confirmar que NO se eliminó
    $this->assertDatabaseHas('tipos_sancion', [
        'id' => $tipo->id
    ]);
}


    /** @test */
    public function puede_eliminar_con_contrasena_correcta()
    {
        $tipo = TipoSancion::create([
            'nombre'        => 'Retiro',
            'requiere_dias' => false,
        ]);

        $response = $this->delete(route('tiposancion.delete', $tipo->id), [
            'password' => '123456'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('tipos_sancion', [
            'id' => $tipo->id
        ]);
    }

    /** @test */
    public function no_permite_eliminar_contrasena_incorrecta()
    {
        $tipo = TipoSancion::create([
            'nombre'        => 'Advertencia',
            'requiere_dias' => false,
        ]);

        $response = $this->delete(route('tiposancion.delete', $tipo->id), [
            'password' => 'incorrecta'
        ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('tipos_sancion', [
            'id' => $tipo->id
        ]);
    }
}
