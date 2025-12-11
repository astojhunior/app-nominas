<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Renuncia extends Model
{
       use HasFactory;
    protected $fillable = [
        'empleado_id',
        'fecha_renuncia',
        'fecha_cese',
        'motivo',
        'documento_adj',
        'estado'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
