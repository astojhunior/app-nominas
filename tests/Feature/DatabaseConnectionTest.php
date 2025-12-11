<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    /** @test */
    public function puede_conectarse_a_mysql()
    {
        try {
            DB::connection()->getPdo();
            $connected = true;
        } catch (\Exception $e) {
            $connected = false;
        }

        $this->assertTrue($connected, "No se pudo establecer conexión con MySQL.");
    }
}
