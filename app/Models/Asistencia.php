<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'empleado_id',
        'contrato_id',
        'turno_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'break_inicio',
        'break_fin',
        'tardanza_inicio_turno',
        'tardanza_break',
        'tardanza_total',
        'estado_asistencia',
        'tipo_jornada',
        'observaciones',
        'horas_extra',
        'justificacion',
        'marcado_manual',
        'origen_marcado'
    ];

    // ---------------------------------------------
    // RELACIONES
    // ---------------------------------------------

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // ---------------------------------------------
    // CÁLCULO DE TARDANZAS
    // ---------------------------------------------

    public function calcularTardanzas()
    {
        if (!$this->turno || !$this->hora_entrada) {
            $this->update([
                'tardanza_inicio_turno' => 0,
                'tardanza_break'        => 0,
                'tardanza_total'        => 0
            ]);
            return;
        }

        $real     = Carbon::parse($this->hora_entrada);
        $esperado = Carbon::parse($this->turno->hora_ingreso);

        $tEntrada = max(0, $real->diffInMinutes($esperado));

        $tBreak = 0;
        if ($this->break_inicio && $this->break_fin) {
            $bi = Carbon::parse($this->break_inicio);
            $bf = Carbon::parse($this->break_fin);

            $duracion = $bi->diffInMinutes($bf);
            $tBreak   = max(0, $duracion - 45);
        }

        $this->update([
            'tardanza_inicio_turno' => $tEntrada,
            'tardanza_break'        => $tBreak,
            'tardanza_total'        => $tEntrada + $tBreak
        ]);
    }
}
