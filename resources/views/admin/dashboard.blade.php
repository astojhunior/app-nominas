<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FUENTE PROFESIONAL INTER -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* SCROLLBAR DEL SIDEBAR */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 20px; }

        /* SCROLLBAR DEL PANEL */
        .panel-scroll::-webkit-scrollbar { width: 6px; }
        .panel-scroll::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 20px; }

        /* ESTILO PREMIUM PARA ITEMS DEL MENÚ */
        .menu-item {
            position: relative;
            transition: all 0.25s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
        }

        /* Icono animado */
        .menu-item i {
            transition: all 0.25s ease;
            opacity: 0.85;
        }

        .menu-item:hover i {
            opacity: 1;
            transform: scale(1.12);
        }

        /* Barra luminosa para el item activo */
        .menu-item.active::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            border-radius: 6px;
            background: white;
            box-shadow: 0 0 12px rgba(255,255,255,0.8);
        }

        /* TARJETAS ANIMADAS */
        .card-anim {
            transition: all 0.28s ease;
        }

        .card-anim:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
            border-color: #1e40af !important; /* azul más fuerte */
        }

        /* ANIMACIÓN DE ENTRADA DEL CONTENIDO */
        .page-animate {
            opacity: 0;
            transform: translateY(15px);
            transition: all 0.45s ease;
        }

        .page-animate-loaded {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-gradient-to-b from-blue-700 to-blue-600 text-white 
                   h-screen flex flex-col justify-between shadow-xl fixed left-0 top-0">

        <div>
            <div class="text-center py-6 border-b border-blue-500 select-none">
                <h1 class="text-xl font-extrabold">GestRestaurant</h1>
                <p class="text-xs opacity-80">Admin</p>
            </div>

            <div class="mt-4 overflow-y-auto h-[75vh] px-4 space-y-2 sidebar-scroll">

                <a href="{{ route('areas_cargos.index') }}"
                   class="menu-item {{ request()->is('areas_cargos*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="layers"></i> Áreas y Cargos
                </a>

                <a href="{{ route('turnos.index') }}"
                   class="menu-item {{ request()->is('turnos*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="clock"></i> Turnos
                </a>

                <a href="{{ route('empleados.create') }}"
                   class="menu-item {{ request()->is('empleados/create') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="user-plus"></i> Registrar Empleado
                </a>

                <a href="{{ route('empleados.index') }}"
                   class="menu-item {{ request()->is('empleados') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="users"></i> Lista de Empleados
                </a>

                <a href="{{ route('asistencias.registrar') }}"
                   class="menu-item {{ request()->is('asistencias/registrar') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="check-circle"></i> Registrar Asistencia
                </a>

                <a href="{{ route('asistencias.index') }}"
                   class="menu-item {{ request()->is('asistencias') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="calendar-check"></i> Lista de Asistencias
                </a>

                <a href="{{ route('tiposancion.index') }}"
                   class="menu-item {{ request()->is('tiposancion*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="alert-triangle"></i> Tipos de Sanción
                </a>

                <a href="{{ route('sanciones.index') }}"
                   class="menu-item {{ request()->is('sanciones*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="shield-alert"></i> Sanciones
                </a>

                <a href="{{ route('boletas.index') }}"
                   class="menu-item {{ request()->is('boletas*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="file-text"></i> Boletas
                </a>

                <a href="{{ route('seguridad.index') }}"
                   class="menu-item {{ request()->is('seguridad*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="lock"></i> Seguridad
                </a>

                <a href="{{ route('renuncias.index', ['ver' => 'lista']) }}"
                   class="menu-item {{ request()->is('renuncias*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="log-out"></i> Renuncias
                </a>
                
                <a href="{{ route('reportes.personal.index') }}"
                    class="menu-item {{ request()->is('reportes/personal*') ? 'active' : '' }} flex items-center gap-3 py-3 px-3 rounded-lg font-medium">
                    <i data-lucide="bar-chart-3"></i> Reportes
                </a>

            </div>
        </div>

        <form action="{{ route('admin.logout') }}" method="POST" class="p-4">
            @csrf
            <button class="w-full flex items-center justify-center gap-3 bg-gray-900 text-white py-3 rounded-lg shadow-lg hover:bg-black active:scale-95 font-semibold">
                <i data-lucide="power"></i> Cerrar sesión
            </button>
        </form>
    </aside>

    <!-- CONTENIDO -->
    <main class="flex-1 p-10 overflow-y-auto ml-72 panel-scroll page-animate">


        <h2 class="text-3xl font-bold text-blue-800 mb-2">Panel Administrativo</h2>

        <p class="text-gray-700 text-lg mb-10">
            Bienvenido, <strong>{{ Auth::guard('admin')->user()->nombre }}</strong>.
        </p>

        <!-- TARJETAS -->
        <div class="grid grid-cols-3 gap-8">

    <a href="http://127.0.0.1:8000/renuncias?ver=mes" 
       class="card-anim bg-white border-2 border-blue-600 p-6 rounded-xl shadow hover:shadow-xl hover:bg-blue-50">
        <h3 class="text-xl font-bold text-blue-700 mb-2">Renuncias este mes</h3>
        <p class="text-gray-600 text-sm">Ver listado de renuncias registradas en el mes actual.</p>
    </a>

    <a href="http://127.0.0.1:8000/rrhh/empleados" 
       class="card-anim bg-white border-2 border-blue-600 p-6 rounded-xl shadow hover:shadow-xl hover:bg-blue-50">
        <h3 class="text-xl font-bold text-blue-700 mb-2">Lista de empleados</h3>
        <p class="text-gray-600 text-sm">Ver todo el personal activo y registrado en el sistema.</p>
    </a>

    <a href="http://127.0.0.1:8000/asistencias" 
       class="card-anim bg-white border-2 border-blue-600 p-6 rounded-xl shadow hover:shadow-xl hover:bg-blue-50">
        <h3 class="text-xl font-bold text-blue-700 mb-2">Asistencias este mes</h3>
        <p class="text-gray-600 text-sm">Consultar asistencias registradas durante el mes actual.</p>
    </a>

</div>


        <!-- SELECTORES ARRIBA DEL GRÁFICO -->
        <form method="GET" class="flex items-center gap-6 mt-10 mb-6">

            <div>
                <label class="font-bold text-gray-700">Mes:</label>
                <select name="mes" class="border px-3 py-2 rounded-lg">
                    @php
                        $meses = [
                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                        ];
                    @endphp

                    @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-bold text-gray-700">Año:</label>
                <select name="anio" class="border px-3 py-2 rounded-lg">
                    @for($a = 2024; $a <= 2027; $a++)
                        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endfor
                </select>
            </div>

            <button class="px-6 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800">
                Aplicar
            </button>

        </form>

        <!-- GRÁFICO -->
        <div class="bg-white p-6 rounded-xl shadow border border-blue-600">
            <h2 class="text-2xl font-bold text-blue-700 mb-4">Estadísticas del Sistema</h2>
            <canvas id="dashboardChart" height="110"></canvas>
        </div>

    </main>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ICONOS PROFESIONALES -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script> lucide.createIcons(); </script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelector(".page-animate")
            ?.classList.add("page-animate-loaded");
    });
</script>

    <script>
        const ctx = document.getElementById('dashboardChart').getContext('2d');

        const dataLaravel = {
            asistencias: @json($asistenciasDias),
            renuncias: @json($renunciasDias),
            sanciones: @json($sancionesDias),
            labels: @json($labelsMes)
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataLaravel.labels,
                datasets: [
                    {
                        label: "Asistencias",
                        data: dataLaravel.asistencias,
                        borderColor: "#1D4ED8",
                        backgroundColor: "rgba(29,78,216,0.15)",
                        borderWidth: 3,
                        tension: 0.35
                    },
                    {
                        label: "Renuncias",
                        data: dataLaravel.renuncias,
                        borderColor: "#DC2626",
                        backgroundColor: "rgba(220,38,38,0.15)",
                        borderWidth: 3,
                        tension: 0.35
                    },
                    {
                        label: "Sanciones",
                        data: dataLaravel.sanciones,
                        borderColor: "#F59E0B",
                        backgroundColor: "rgba(245,158,11,0.15)",
                        borderWidth: 3,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: "Resumen del mes de {{ ucfirst($mesNombre) }}",
                        font: { size: 18 }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.raw} registro(s)`
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Días del Mes — {{ ucfirst($mesNombre) }}",
                            font: { size: 16 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        title: {
                            display: true,
                            text: "Cantidad de registros",
                            font: { size: 16 }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
