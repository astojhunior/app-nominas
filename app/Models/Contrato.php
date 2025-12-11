<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'empleado_id',
        'area_id',
        'cargo_id',
        'tipo_contrato',
        'fecha_inicio',
        'fecha_fin',
        'sueldo',
        'sistema_pension',
        'afp_nombre',
        'afp_tipo',
        'metodo_pago',
        'banco',
        'cuenta_bancaria',
        'tipo_cuenta',
        'estado_contrato',
        'turno_id' 
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function archivos()
    {
        return $this->hasMany(ContratoArchivo::class);
    }
}
