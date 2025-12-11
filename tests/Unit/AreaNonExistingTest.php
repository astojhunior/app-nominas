<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AreaNonExistingTest extends TestCase
{
    /** @test */
    public function no_permite_actualizar_area_sin_id()
    {
        $data = [
            'area_id' => null,
            'nombre'  => 'Área Nueva'
        ];

        $rules = [
            'area_id' => 'required',
            'nombre'  => 'required|string|max:120'
        ];

        $this->expectException(ValidationException::class);

        Validator::make($data, $rules)->validate();
    }
}
