<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Empleado;
use App\Models\Asistencia;
use App\Models\Contrato;
use App\Models\Admin; // tu modelo de admin, si existe
use Carbon\Carbon;

class BoletaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear un admin y autenticarse
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
    }

    /** @test */
    public function un_admin_puede_generar_boleta_fin_mes_correctamente()
    {
        $empleado = Empleado::factory()->create();

        Contrato::factory()->create([
            'empleado_id' => $empleado->id,
            'sueldo' => 2500,
            'sistema_pension' => 'ONP',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => null,
        ]);

        $mes = 12;
        $anio = 2025;
        $diasMes = Carbon::create($anio, $mes, 1)->daysInMonth;

        // Crear asistencias completas
        for ($i = 1; $i <= $diasMes; $i++) {
            Asistencia::factory()->create([
                'empleado_id' => $empleado->id,
                'fecha' => Carbon::create($anio, $mes, $i),
            ]);
        }

        // Llamada al endpoint correcto
        $response = $this->post(route('boletas.fin_mes.confirmar'), [
            'empleado_id'  => $empleado->id,
            'periodo_mes'  => $mes,
            'periodo_anio' => $anio,
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('empleado', $empleado);
        $response->assertViewHas('sueldo_base', 2500);
        $response->assertViewHas('periodo_mes', $mes);
        $response->assertViewHas('periodo_anio', $anio);
    }

    /** @test */
    public function no_se_puede_generar_boleta_si_faltan_asistencias()
    {
        $empleado = Empleado::factory()->create();

        Contrato::factory()->create([
            'empleado_id' => $empleado->id,
            'sueldo' => 2500,
            'sistema_pension' => 'ONP',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => null,
        ]);

        $mes = 12;
        $anio = 2025;

        // Crear solo 5 asistencias
        for ($i = 1; $i <= 5; $i++) {
            Asistencia::factory()->create([
                'empleado_id' => $empleado->id,
                'fecha' => Carbon::create($anio, $mes, $i),
            ]);
        }

        $response = $this->post(route('boletas.fin_mes.confirmar'), [
            'empleado_id'  => $empleado->id,
            'periodo_mes'  => $mes,
            'periodo_anio' => $anio,
        ]);

        $response->assertRedirect(); // redirige al index
        $response->assertSessionHas('error', "El empleado solo tiene 5 de 31 asistencias este mes.");
    }
}
