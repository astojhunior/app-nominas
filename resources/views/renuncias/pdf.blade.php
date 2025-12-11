<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.6; font-size: 14px; }
        .titulo { font-weight: bold; }
        .firma { margin-top: 80px; text-align: center; }
    </style>
</head>
<body>

    <p class="titulo">
        LIMA, {{ \Carbon\Carbon::parse($renuncia->fecha_renuncia)->format('d \d\e F \d\e\l Y') }}
    </p>

    <p class="titulo">
        SEÑOR:<br>
        {{ strtoupper($renuncia->empleado->apellidos . ' ' . $renuncia->empleado->nombres) }}<br>
        PRESENTE.-
    </p>

    <p>De mi mayor consideración:</p>

    <p>
        Me es grato dirigirme a usted para hacer de su conocimiento que, al amparo
        de lo establecido en la legislación vigente, presento mi renuncia al cargo
        de <strong>{{ $renuncia->empleado->contratoActual?->cargo?->cargo ?? '_________' }}</strong>,
        que he venido desempeñando en esta empresa.
    </p>

    <p>
        Por motivos personales que motivan esta decisión, solicito tenga a bien
        aceptar mi renuncia, siendo mi último día de labores el
        <strong>{{ \Carbon\Carbon::parse($renuncia->fecha_cese)->format('d \d\e F \d\e\l Y') }}</strong>.
    </p>

    @if($renuncia->motivo)
        <p><strong>Motivo:</strong> {{ $renuncia->motivo }}</p>
    @endif

    <p>
        Solicito se me facilite la entrega de mi certificado de trabajo y demás
        beneficios sociales que correspondan.
    </p>

    <p>Agradezco la confianza y oportunidad brindada durante mi permanencia.</p>

    <p>Atentamente,</p>

    <div class="firma">
        <br><br>-------------------------------<br>
        {{ $renuncia->empleado->nombres }} {{ $renuncia->empleado->apellidos }}<br>
        DNI: {{ $renuncia->empleado->dni }}
    </div>

</body>
</html>
