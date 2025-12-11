<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            text-align: justify;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .firma {
            text-align: center;
            margin-top: 60px;
        }
        .signature-img {
            width: 180px;
            opacity: 0.8;
        }
        .datos {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

    <h2>PRÓRROGA DE CONTRATO DE TRABAJO SUJETO A MODALIDAD</h2>

    @php
        $empleadorNombre = "CORPORACION EMPRESARIAL HOLDING S.A.C.";
        $empleadorRuc = "20611137355";
        $empleadorDireccion = "Av. Manuel Olguín 211, int.1002 Santiago de Surco – Lima";

        $empleadorRep = "DIEGO ROBERTO LANDEO CARDENAS";
        $empleadorDni = "44857614";

        $trab = $contrato->empleado;
    @endphp


    <p class="datos">
        Conste por el presente documento, la prórroga de contrato de trabajo sujeto a la modalidad incremento
        de actividades que celebran, de una parte,
        <b>{{ $empleadorNombre }}</b>, con Registro Único de Contribuyentes Nº <b>{{ $empleadorRuc }}</b>,
        con domicilio en <b>{{ $empleadorDireccion }}</b>, debidamente representada por su Gerente General
        Don <b>{{ $empleadorRep }}</b>, identificado con DNI Nº <b>{{ $empleadorDni }}</b>, a quien en adelante se le denominará
        <b>EL EMPLEADOR</b>; y, de la otra parte,
        Don(ña) <b>{{ strtoupper($trab->nombres . ' ' . $trab->apellidos) }}</b>,
        identificado con DNI Nº <b>{{ $trab->dni }}</b> y domicilio en <b>{{ $trab->direccion }}</b>, en adelante
        <b>EL TRABAJADOR(A)</b>, en los términos y condiciones siguientes:
    </p>


    <p><b>PRIMERO:</b> Con fecha 
    <b>{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</b>,
    EL EMPLEADOR y EL TRABAJADOR(A), celebraron un contrato de trabajo sujeto a modalidad,
    para que este último preste servicios como <b>{{ $contrato->cargo->nombre }}</b>.
</p>

<p>
    <b>SEGUNDO:</b> Por el presente documento, y considerando que la causa que justificó la contratación
    temporal se mantiene vigente, las partes acuerdan prorrogar el contrato hasta el día
    <b>{{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}</b>, 
    manteniéndose las condiciones inicialmente pactadas.
</p>


    <p>
        <b>TERCERO:</b> EL EMPLEADOR y EL TRABAJADOR(A) reconocen que, por la naturaleza del negocio,
        la labor está sujeta a jornadas atípicas, incluyendo turnos nocturnos y días festivos,
        respetándose la normativa laboral vigente.
    </p>

    <p>
        <b>CUARTO:</b> Las partes dejan constancia que todas las cláusulas del contrato primigenio
        que no hayan sido modificadas se mantienen plenamente vigentes.
    </p>

    <p>
        <b>QUINTO:</b> La presente prórroga se expide en dos ejemplares, uno de los cuales se entrega a
        EL TRABAJADOR(A).
    </p>

    <br><br>

    <p>
        Firmando las partes, en señal de conformidad, en la ciudad de <b>TRUJILLO</b>
        el <b>{{ now()->format('d') }}</b> de
        <b>{{ __(now()->format('F')) }}</b> del <b>{{ now()->format('Y') }}</b>.
    </p>

    <div class="firma">
        <img class="signature-img" src="{{ public_path('firma.png') }}" alt="Firma">
        <br>
        ______________________________________ <br>
        <b>{{ $empleadorRep }}</b> <br>
        <b>{{ $empleadorNombre }}</b>
    </div>

</body>
</html>
