<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Sancion;
use App\Models\Empleado;
use App\Models\TipoSancion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SancionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear administrador autenticado
        $this->admin = Admin::create([
            'nombre' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
        ]);

        $this->actingAs($this->admin, 'admin');
    }

    /** @test */
    public function puede_registrar_sancion()
    {
        $empleado = Empleado::factory()->create();
        $tipo = TipoSancion::create([
            'nombre' => 'Suspensión',
            'requiere_dias' => true,
            'descripcion' => null,
            'estado' => 1
        ]);

        $response = $this->post(route('sanciones.store'), [
            'empleado_id'      => $empleado->id,
            'tipo_sancion_id'  => $tipo->id,
            'fecha_aplicacion' => '2025-01-01',
            'dias_suspension'  => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sanciones', [
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function no_permite_registrar_doble_sancion_el_mismo_dia()
    {
        $empleado = Empleado::factory()->create();
        $tipo = TipoSancion::create([
            'nombre' => 'Tardanza',
            'requiere_dias' => false,
            'descripcion' => null,
            'estado' => 1
        ]);

        // crear sanción previa
        Sancion::create([
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo->id,
            'fecha_aplicacion' => '2025-01-01',
            'estado' => 'activo'
        ]);

        $response = $this->post(route('sanciones.store'), [
            'empleado_id'      => $empleado->id,
            'tipo_sancion_id'  => $tipo->id,
            'fecha_aplicacion' => '2025-01-01',
        ]);

        $response->assertSessionHas('error');
    }

   /** @test */
public function puede_actualizar_sancion()
{
    $empleado = Empleado::factory()->create();
    $tipo = TipoSancion::create([
        'nombre' => 'Suspensión',
        'requiere_dias' => true,
        'estado' => 1
    ]);

    $s = Sancion::create([
        'empleado_id' => $empleado->id,
        'tipo_sancion_id' => $tipo->id,
        'fecha_aplicacion' => '2025-01-01',
        'dias_suspension' => 2,
        'estado' => 'activo'
    ]);

    $response = $this->put(route('sanciones.update', $s->id), [
    'fecha_aplicacion' => '2025-01-02',
    'dias_suspension' => 5,
    'observaciones' => 'Actualizado prueba'
]);

$response->assertSessionHas('success');

    $this->assertDatabaseHas('sanciones', [
        'id' => $s->id,
        'dias_suspension' => 5,
    ]);
}


    /** @test */
    public function no_permite_anular_contrasena_incorrecta()
    {
        $empleado = Empleado::factory()->create();
        $tipo = TipoSancion::create([
            'nombre' => 'Suspensión',
            'requiere_dias' => true,
            'estado' => 1
        ]);

        $s = Sancion::create([
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo->id,
            'fecha_aplicacion' => '2025-01-01',
            'estado' => 'activo'
        ]);

        $response = $this->post(route('sanciones.anular', $s->id), [
            'password' => 'incorrecta'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('sanciones', [
            'id' => $s->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function puede_anular_contrasena_correcta()
    {
        $empleado = Empleado::factory()->create();
        $tipo = TipoSancion::create([
            'nombre' => 'Suspensión',
            'requiere_dias' => true,
            'estado' => 1
        ]);

        $s = Sancion::create([
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo->id,
            'fecha_aplicacion' => '2025-01-01',
            'estado' => 'activo'
        ]);

        $response = $this->post(route('sanciones.anular', $s->id), [
            'password' => '123456'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('sanciones', [
            'id' => $s->id,
            'estado' => 'anulado'
        ]);
    }

    /** @test */
    public function puede_filtrar_lista_de_sanciones_por_tipo()
    {
        $empleado = Empleado::factory()->create();

        $tipo1 = TipoSancion::create([
            'nombre' => 'Tardanza',
            'requiere_dias' => false,
            'estado' => 1
        ]);

        $tipo2 = TipoSancion::create([
            'nombre' => 'Suspensión',
            'requiere_dias' => true,
            'estado' => 1
        ]);

        // sanciones
        Sancion::create([
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo1->id,
            'fecha_aplicacion' => '2025-01-01',
            'estado' => 'activo'
        ]);

        Sancion::create([
            'empleado_id' => $empleado->id,
            'tipo_sancion_id' => $tipo2->id,
            'fecha_aplicacion' => '2025-01-02',
            'estado' => 'activo'
        ]);

        $response = $this->get(uri: '/sanciones?tipo='.$tipo1->id);

        $response->assertStatus(200);
    }
}
