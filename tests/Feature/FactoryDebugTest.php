<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Empleado;

class FactoryDebugTest extends TestCase
{
    public function test_debug_factory()
    {
        $empleado = Empleado::factory()->make();
        dd($empleado->toArray());
    }
}
