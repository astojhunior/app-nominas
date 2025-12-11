<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AreaUpdateValidationTest extends TestCase
{
    /** @test */
    public function no_permite_actualizar_area_sin_nombre()
    {
        $data = [
            'area_id' => 1,
            'nombre'  => ''
        ];

        $rules = [
            'area_id' => 'required',
            'nombre'  => 'required|string|max:120'
        ];

        $this->expectException(ValidationException::class);

        Validator::make($data, $rules)->validate();
    }
}
