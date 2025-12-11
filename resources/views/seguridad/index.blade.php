<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguridad del Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { background:#0A4BD6; min-height:100vh; }
        .card { background:white; border:2px solid #0A4BD6; }

        .tab-btn-active {
            background:#1D4ED8;
            color:white;
            border-bottom:3px solid #1E40AF;
        }
        .tab-btn-inactive {
            color:#1D4ED8;
        }
        .tab-btn {
            padding: 10px 16px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
        }
    </style>
</head>
<body class="p-6">

<div class="card rounded-xl p-6 mx-auto max-w-5xl">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-[#0A4BD6]">Seguridad del Sistema</h1>
            <p class="text-sm text-gray-500 mt-1">
                Administra usuarios y contraseñas del sistema GestRestaurant.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-4 py-2 rounded-lg transition">
            Volver
        </a>
    </div>

    <!-- ALERTAS -->
    @if(session('success') || session('error'))
        <div id="alert-global"
             class="fixed top-5 right-5 px-4 py-3 rounded-lg shadow-lg text-white font-semibold
                    {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}
                    opacity-0 translate-y-3 transition-all duration-500">
            {{ session('success') ?? session('error') }}
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const a = document.getElementById('alert-global');
                requestAnimationFrame(() => {
                    a.classList.remove('opacity-0', 'translate-y-3');
                    a.classList.add('opacity-100', 'translate-y-0');
                });
                setTimeout(() => {
                    a.classList.remove('opacity-100', 'translate-y-0');
                    a.classList.add('opacity-0', 'translate-y-3');
                    setTimeout(() => a.remove(), 500);
                }, 3000);
            });
        </script>
    @endif

    <!-- TABS -->
    @php $tabActiva = session('tab','seleccionar'); @endphp

    <div class="border-b border-gray-300 mb-4">
        <nav class="-mb-px flex gap-3">

            <div class="tab-btn {{ $tabActiva==='seleccionar' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
                 data-tab="seleccionar">1. Seleccionar Empleado</div>

            <div class="tab-btn {{ $tabActiva==='asignar' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
                 data-tab="asignar">2. Asignar Usuario</div>

            <div class="tab-btn {{ $tabActiva==='password' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
                 data-tab="password">3. Cambiar Contraseña</div>

            <div class="tab-btn {{ $tabActiva==='admins' ? 'tab-btn-active' : 'tab-btn-inactive' }}"
                 data-tab="admins">4. Lista de Administradores</div>
        </nav>
    </div>

    <!-- TAB 1 : Seleccionar empleado -->
    <div id="tab-seleccionar" class="{{ $tabActiva==='seleccionar' ? '' : 'hidden' }}">
        <h2 class="text-xl font-bold text-[#0A4BD6] mb-4">1. Seleccionar empleado</h2>

        <p class="text-sm text-gray-600 mb-4">
            Selecciona el área, luego el cargo y finalmente el empleado.
        </p>

        <div class="grid md:grid-cols-3 gap-4 mb-6">

            <!-- AREA -->
            <div>
                <label class="text-sm font-semibold text-gray-700">Área</label>
                <select id="sel-area"
                        class="w-full border rounded px-3 py-2 text-sm bg-white">
                    <option value="">-- Seleccione área --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- CARGO -->
            <div>
                <label class="text-sm font-semibold text-gray-700">Cargo</label>
                <select id="sel-cargo"
                        class="w-full border rounded px-3 py-2 text-sm" disabled>
                    <option value="">-- Seleccione cargo --</option>
                </select>
            </div>

            <!-- EMPLEADO -->
            <div>
                <label class="text-sm font-semibold text-gray-700">Empleado</label>
                <select id="sel-empleado"
                        class="w-full border rounded px-3 py-2 text-sm" disabled>
                    <option value="">-- Seleccione empleado --</option>
                </select>
            </div>

        </div>

        <div class="flex justify-end">
            <button id="btn-ir-asignar"
                    class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-6 py-2 rounded-lg text-sm transition"
                    disabled>
                Continuar a Asignar Usuario
            </button>
        </div>
    </div>

    <!-- TAB 2 : Asignar usuario -->
    <div id="tab-asignar" class="{{ $tabActiva==='asignar' ? '' : 'hidden' }} mt-4">

        <h2 class="text-xl font-bold text-[#0A4BD6] mb-4">2. Asignar usuario administrador</h2>

        <form method="POST" action="{{ route('seguridad.crear') }}" class="space-y-4">
            @csrf

            <input type="hidden" id="empleado_id" name="empleado_id">

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold">Empleado</label>
                    <input id="nombre_empleado"
                           class="w-full border rounded px-3 py-2 text-sm bg-gray-100"
                           readonly>
                </div>

                <div>
                    <label class="text-sm font-semibold">Nombre del usuario administrador</label>
                    <input name="nombre_admin" id="nombre_admin"
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="Administrador Principal">
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold">Correo</label>
                <input id="correo_empleado"
                       class="w-full border rounded px-3 py-2 text-sm bg-gray-100"
                       readonly>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold">Contraseña</label>
                    <input type="password" name="password"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-sm font-semibold">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex justify-end">
                <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm">
                    Asignar Usuario
                </button>
            </div>
        </form>

    </div>
    <!-- TAB 3 : Cambiar contraseña -->
    <div id="tab-password" class="{{ $tabActiva==='password' ? '' : 'hidden' }} mt-4">

        <h2 class="text-xl font-bold text-[#0A4BD6] mb-4">
            3. Cambiar contraseña del usuario actual
        </h2>

        <p class="text-sm mb-4 text-gray-700">
            Usuario actual:
            <strong class="text-gray-900">
                {{ $adminActual->nombre }} ({{ $adminActual->email }})
            </strong>
        </p>

        <form method="POST" action="{{ route('seguridad.password') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700">Contraseña actual</label>
                <input type="password" name="password_actual"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nueva contraseña</label>
                    <input type="password" name="password_nueva"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Confirmar nueva contraseña</label>
                    <input type="password" name="password_nueva_confirmation"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg text-sm">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

  <!-- TAB 4 : Lista de administradores -->
<div id="tab-admins" class="{{ $tabActiva==='admins' ? '' : 'hidden' }} mt-4">

    <h2 class="text-xl font-bold text-[#0A4BD6] mb-4">
        4. Lista de administradores del sistema
    </h2>

    <div class="overflow-hidden border rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-[#0A4BD6] text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-left">Correo</th>
                    <th class="px-4 py-2 text-left">Fecha de creación</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach($admins as $ad)
                    @php 
                        $esPrincipal = ($adminActual->email === 'admin@nominaempleados.com');
                        $noEsMismo = ($ad->id !== $adminActual->id);
                    @endphp

                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $ad->nombre }}</td>
                        <td class="px-4 py-2">{{ $ad->email }}</td>
                        <td class="px-4 py-2">{{ $ad->created_at->format('d/m/Y') }}</td>

                        <td class="px-4 py-2">

                            @if($esPrincipal && $noEsMismo)
                                <!-- Botón ABRIR MODAL -->
                                <button 
                                    onclick="abrirModal({{ $ad->id }})"
                                    class="text-red-600 font-bold hover:underline">
                                    Eliminar
                                </button>
                            @else
                                <span class="text-gray-400 text-xs italic">
                                    @if(!$esPrincipal)
                                        Solo el administrador principal puede eliminar
                                    @else
                                        No se puede eliminar a sí mismo
                                    @endif
                                </span>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>


<!-- MODAL ELIMINACIÓN -->
<div id="modalEliminar" 
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50
            flex items-center justify-center">

    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6 animate-fadeIn">

        <h2 class="text-xl font-bold text-red-600 mb-2">
            Confirmar eliminación
        </h2>

        <p class="text-gray-600 text-sm mb-4">
            Para eliminar este usuario administrador, ingrese su contraseña actual.
        </p>

        <form id="formEliminar" method="POST">
            @csrf
            @method('DELETE')

            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Contraseña actual
            </label>
            <input type="password" name="password" required
                   class="w-full border rounded-lg px-3 py-2 mb-4 focus:ring focus:ring-red-300">

            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="cerrarModal()"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800">
                    Cancelar
                </button>

                <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold">
                    Eliminar usuario
                </button>
            </div>

        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity:0; transform:scale(0.9); }
    to { opacity:1; transform:scale(1); }
}
.animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>

<script>
    function abrirModal(id) {
        const modal = document.getElementById('modalEliminar');
        const form = document.getElementById('formEliminar');

        form.action = "/seguridad/eliminar/" + id;
        modal.classList.remove("hidden");
    }

    function cerrarModal() {
        document.getElementById('modalEliminar').classList.add("hidden");
    }
</script>


</div> <!-- cierre card -->

<!--   SCRIPT GENERAL DE TABS + FILTRADO -->
<script>
    const AREAS = @json($areas);
    const CARGOS = @json($cargos);
    const EMPLEADOS = @json($empleados);

    const selArea = document.getElementById('sel-area');
    const selCargo = document.getElementById('sel-cargo');
    const selEmpleado = document.getElementById('sel-empleado');
    const btnIr = document.getElementById('btn-ir-asignar');

    const inpId = document.getElementById('empleado_id');
    const inpNombre = document.getElementById('nombre_empleado');
    const inpCorreo = document.getElementById('correo_empleado');
    const inpAdmin = document.getElementById('nombre_admin');

    /* --- FILTRAR CARGOS POR AREA --- */
    selArea.addEventListener('change', () => {
        const areaId = selArea.value;

        selCargo.innerHTML = '<option value="">-- Seleccione cargo --</option>';
        selEmpleado.innerHTML = '<option value="">-- Seleccione empleado --</option>';
        selCargo.disabled = true;
        selEmpleado.disabled = true;
        btnIr.disabled = true;

        if (!areaId) return;

        CARGOS.filter(c => c.area_id == areaId)
            .forEach(c => {
                selCargo.innerHTML += `<option value="${c.cargo}">${c.cargo}</option>`;
            });

        selCargo.disabled = false;
    });

    /* --- FILTRAR EMPLEADOS POR AREA Y CARGO --- */
    selCargo.addEventListener('change', () => {
        const areaId = selArea.value;
        const cargo = selCargo.value;

        selEmpleado.innerHTML = '<option value="">-- Seleccione empleado --</option>';
        selEmpleado.disabled = true;
        btnIr.disabled = true;

        if (!areaId || !cargo) return;

        EMPLEADOS.filter(e => e.area_id == areaId && e.cargo === cargo)
            .forEach(e => {
                selEmpleado.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
            });

        selEmpleado.disabled = false;
    });

    selEmpleado.addEventListener('change', () => {
        btnIr.disabled = selEmpleado.value === "";
    });

    /* --- IR A TAB ASIGNAR --- */
    btnIr.addEventListener('click', () => {
        const id = selEmpleado.value;
        const emp = EMPLEADOS.find(e => e.id == id);

        inpId.value = emp.id;
        inpNombre.value = emp.nombre;
        inpCorreo.value = emp.correo;

        if (!inpAdmin.value)
            inpAdmin.value = "Administrador Principal";

        activarTab("asignar");
    });

    /* --- MANEJO DE TABS --- */
    const tabButtons = document.querySelectorAll('.tab-btn');

    function activarTab(tab) {
        // Ocultar todos
        ['seleccionar', 'asignar', 'password', 'admins'].forEach(t => {
            document.getElementById('tab-' + t)?.classList.add('hidden');
        });

        // Mostrar activo
        document.getElementById('tab-' + tab)?.classList.remove('hidden');

        // Cambiar estilo tabs
        tabButtons.forEach(btn => {
            if (btn.dataset.tab === tab) {
                btn.classList.remove('tab-btn-inactive');
                btn.classList.add('tab-btn-active');
            } else {
                btn.classList.remove('tab-btn-active');
                btn.classList.add('tab-btn-inactive');
            }
        });
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => activarTab(btn.dataset.tab));
    });
</script>

</body>
</html>
