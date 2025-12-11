<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Turnos</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #1859D1;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-5xl rounded-2xl shadow-xl p-8">

    <!--  ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Gestión de Turnos
        </h1>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!--  PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">

        <div class="flex gap-6 text-lg font-semibold">

            <a href="{{ route('turnos.index') }}"
               class="pb-3 transition {{ !request('ver') ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                Registrar Turno
            </a>

            <a href="{{ route('turnos.index', ['ver' => 'lista']) }}"
               class="pb-3 transition {{ request('ver')=='lista' ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                Turnos Registrados
            </a>

        </div>
    </div>

    <!--  CONTENIDO SEGÚN PESTAÑA -->
    @if(!request('ver'))

        <!--  FORMULARIO REGISTRAR -->
        <form action="{{ route('turnos.store') }}" method="POST">
            @csrf

            <div>
                <label class="font-semibold text-gray-700">Nombre del Turno</label>
                <input type="text" name="nombre"
                       class="w-full border rounded-lg px-3 py-2 mt-1"
                       placeholder="Ejemplo: Mañana" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="font-semibold text-gray-700">Hora Ingreso</label>
                    <input type="time" name="hora_ingreso"
                           class="w-full border rounded-lg px-3 py-2 mt-1" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Hora Salida</label>
                    <input type="time" name="hora_salida"
                           class="w-full border rounded-lg px-3 py-2 mt-1" required>
                </div>
            </div>

            <button class="mt-5 bg-blue-700 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                Registrar Turno
            </button>
        </form>

    @else

        <!--  LISTA DE TURNOS -->
        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">Nombre</th>
                <th class="py-3">Ingreso</th>
                <th class="py-3">Salida</th>
                <th class="py-3">Acciones</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @foreach($turnos as $t)
                <tr class="border-b">
                    <td class="py-3">{{ $t->nombre }}</td>
                    <td>{{ $t->hora_ingreso }}</td>
                    <td>{{ $t->hora_salida }}</td>

                    <td class="flex justify-center gap-4 py-3">

                        <!-- EDITAR -->
                        <button class="text-blue-600 font-semibold hover:underline"
                                onclick="openEditModal({{ $t->id }}, '{{ $t->nombre }}', '{{ $t->hora_ingreso }}', '{{ $t->hora_salida }}')">
                            Editar
                        </button>

                        <!-- ELIMINAR -->
                        <button class="text-red-600 font-semibold hover:underline"
                                onclick="openDeleteModal({{ $t->id }}, '{{ $t->nombre }}')">
                            Eliminar
                        </button>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif
</div>

<!-- MODAL EDITAR TURNO -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 w-[420px]">

        <h2 class="text-xl font-bold text-blue-700 mb-4">
            Editar Turno
        </h2>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <label class="font-semibold text-gray-700">Nombre del Turno</label>
            <input type="text" id="edit-nombre" name="nombre"
                   class="w-full border rounded-lg px-3 py-2 mb-3" required>

            <label class="font-semibold text-gray-700">Hora Ingreso</label>
            <input type="time" id="edit-ingreso" name="hora_ingreso"
                   class="w-full border rounded-lg px-3 py-2 mb-3" required>

            <label class="font-semibold text-gray-700">Hora Salida</label>
            <input type="time" id="edit-salida" name="hora_salida"
                   class="w-full border rounded-lg px-3 py-2 mb-5" required>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg">
                    Cancelar
                </button>

                <button class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!--  MODAL ELIMINAR CON CONTRASEÑA -->
<div id="modalDelete" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 w-[420px]">

        <h2 class="text-xl font-bold text-red-600 mb-4">
            Confirmar eliminación
        </h2>

        <p class="mb-4 text-gray-700">
            Para eliminar el turno <b id="delete-name"></b>, ingrese su contraseña.
        </p>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')

            <label class="font-semibold text-gray-700">Contraseña</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2 mb-5"
                   placeholder="Ingrese su contraseña" required>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg">
                    Cancelar
                </button>

                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Eliminar
                </button>
            </div>

        </form>
    </div>
</div>


<!-- SCRIPTS -->
<script>
function openEditModal(id, nombre, ingreso, salida) {
    const modal = document.getElementById('modalEdit');
    const form  = document.getElementById('editForm');

    form.action = "/turnos/" + id;

    document.getElementById('edit-nombre').value  = nombre;
    document.getElementById('edit-ingreso').value = ingreso;
    document.getElementById('edit-salida').value  = salida;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}

function openDeleteModal(id, nombre) {
    const modal = document.getElementById('modalDelete');
    const form  = document.getElementById('deleteForm');

    document.getElementById('delete-name').innerText = nombre;
    form.action = "/turnos/" + id;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('modalDelete').classList.add('hidden');
    document.getElementById('modalDelete').classList.remove('flex');
}
</script>

</body>
</html>
