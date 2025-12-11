<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Empleados</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { background:#0A4BD6; min-height:100vh; }
        .card { background:white; border:2px solid #0A4BD6; }
        .btn { @apply px-3 py-1 rounded-lg text-xs font-semibold transition; }
    </style>
</head>

<body class="p-6">

<div class="max-w-7xl mx-auto">

    <!-- VOLVER -->
    <a href="{{ route('admin.dashboard') }}"
       class="px-4 py-2 bg-white text-[#0A4BD6] font-semibold rounded-lg shadow hover:bg-gray-100 transition">
        Volver
    </a>

    <h1 class="text-4xl font-bold text-white mt-8 mb-6">
        Lista de Empleados
    </h1>

    <!-- FILTROS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <!-- Buscar -->
        <input id="buscarInput" onkeyup="aplicarFiltros()"
               placeholder="Buscar por nombre, DNI o correo"
               class="w-full px-4 py-3 rounded-lg text-black shadow">

        <!-- Cargo -->
        <select id="cargoFiltro" onchange="aplicarFiltros()"
        class="w-full px-4 py-3 rounded-lg text-black shadow">
    <option value="">Filtrar por cargo</option>
    @foreach($cargos as $c)
        <option value="{{ $c->id }}">{{ $c->cargo }}</option>
    @endforeach
</select>


        <!-- Estado -->
        <select id="estadoFiltro" onchange="aplicarFiltros()"
                class="w-full px-4 py-3 rounded-lg text-black shadow">
            <option value="activos" selected>Activos</option>
            <option value="inactivos">No activos</option>
            <option value="todos">Todos</option>
        </select>

        <!-- Orden -->
        <select id="ordenFiltro" onchange="aplicarFiltros()"
                class="w-full px-4 py-3 rounded-lg text-black shadow">
            <option value="">Ordenar</option>
            <option value="asc">A - Z</option>
            <option value="desc">Z - A</option>
            <option value="area">Área</option>
            <option value="cargo">Cargo</option>
        </select>

    </div>

    <!-- TABLA -->
    <div id="tablaContainer" class="card rounded-xl p-6 shadow-2xl">

        <table class="w-full text-left text-sm">
           <thead>
    <tr class="bg-[#0A4BD6] text-white text-sm font-semibold rounded-xl overflow-hidden">
        <th class="px-4 py-3 rounded-l-xl">Empleado</th>
        <th class="px-4 py-3">DNI</th>
        <th class="px-4 py-3">Correo</th>
        <th class="px-4 py-3">Área</th>
        <th class="px-4 py-3">Cargo</th>
        <th class="px-4 py-3">Estado</th>
        <th class="px-4 py-3">Contrato</th>
        <th class="px-4 py-3 rounded-r-xl">Detalle</th>
    </tr>
</thead>

            <tbody id="tablaEmpleados">
                @foreach($empleados as $emp)

                    @php
                        $contrato = $emp->contratoActual;
                        $activo = $contrato ? true : false;
                    @endphp

                    <tr class="border-b hover:bg-gray-50 transition">

                        <!-- Empleado -->
                        <td class="px-4 py-3 font-semibold">
                            {{ $emp->apellidos }}, {{ $emp->nombres }}
                        </td>

                        <td class="px-4 py-3">{{ $emp->dni }}</td>

                        <td class="px-4 py-3 text-blue-600 break-all">
                            {{ $emp->correo }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contrato->area->nombre ?? '—' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $contrato->cargo->cargo ?? '—' }}
                        </td>

                        <!-- Estado -->
                        <td class="px-4 py-3">
    @if($activo)
        <span class="inline-block px-3 py-1 rounded-full bg-green-500 text-white text-xs font-bold shadow">
            Activo
        </span>
    @else
        <span class="inline-block px-3 py-1 rounded-full bg-red-500 text-white text-xs font-bold shadow">
            No activo
        </span>
    @endif
</td>


                        <!-- Ver contrato -->
<td class="px-4 py-3">
    @if($contrato)
        <a href="{{ route('contratos.ver', $contrato->id) }}"
           class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white whitespace-nowrap 
                  rounded-full text-xs font-bold shadow-md transition">
            Ver contrato
        </a>
    @else
        <span class="px-4 py-1.5 bg-gray-300 text-gray-700 whitespace-nowrap 
                     rounded-full text-xs font-bold shadow-md">
            Sin contrato
        </span>
    @endif
</td>

<!-- Ver detalle -->
<td class="px-4 py-3">
    <a href="{{ route('empleados.detalle', $emp->id) }}"
       class="px-4 py-1.5 bg-pink-600 hover:bg-pink-700 text-white whitespace-nowrap
              rounded-full text-xs font-bold shadow-md transition">
        Ver detalle
    </a>
</td>


                    </tr>

                @endforeach
            </tbody>
        </table>

    </div>

</div>

<script>
function aplicarFiltros() {
    let buscar = document.getElementById("buscarInput").value;
    let cargo = document.getElementById("cargoFiltro").value;
    let estado = document.getElementById("estadoFiltro").value;
    let orden = document.getElementById("ordenFiltro").value;

    let url = `?buscar=${buscar}&cargo=${cargo}&estado=${estado}&orden=${orden}`;

    fetch(url)
        .then(res => res.text())
        .then(html => {
            let doc = new DOMParser().parseFromString(html, "text/html");
            document.querySelector("#tablaContainer").innerHTML =
                doc.querySelector("#tablaContainer").innerHTML;
        });
}
</script>

</body>
</html>
