<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sancion extends Model
{
    protected $table = 'sanciones';

    protected $fillable = [
        'empleado_id',
        'tipo_sancion_id',
        'fecha_aplicacion',
        'dias_suspension',
        'fecha_inicio_suspension',
        'fecha_fin_suspension',
        'motivo',
        'documento_adj',
        'estado',
    ];

    /**
     * Una sanción pertenece a un empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Una sanción pertenece a un tipo de sanción
     */
    public function tipo()
    {
        return $this->belongsTo(TipoSancion::class, 'tipo_sancion_id');
    }
}
