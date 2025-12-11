<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bono extends Model
{
    use HasFactory;

    protected $table = 'bonos';

    protected $fillable = [
        'dirigido_a',        // empleado - cargo - todos
        'empleado_id',
        'cargo_id',
        'nombre',
        'monto',
        'tipo',
        'fecha_aplicacion',
        'estado',
        'motivo',
        'meses_aplicacion',  // meses seleccionados
    ];

    protected $casts = [
        'meses_aplicacion' => 'array',
        'fecha_aplicacion' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }
}
