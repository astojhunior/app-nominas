<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Sanción</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-5xl rounded-2xl shadow-xl p-8">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Gestión de Tipos de Sanción
        </h1>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <a href="{{ route('tiposancion.index') }}"
               class="pb-3 transition
                {{ !request('ver') 
                    ? 'text-blue-700 border-b-4 border-blue-700' 
                    : 'text-gray-500 hover:text-blue-600' }}">
                Registrar Tipo
            </a>

            <a href="{{ route('tiposancion.index', ['ver' => 'lista']) }}"
               class="pb-3 transition
                {{ request('ver')=='lista'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600' }}">
                Tipos Registrados
            </a>
        </div>
    </div>

    <!--  CONTENIDO -->
    @if(!request('ver'))

        <!-- FORMULARIO REGISTRAR -->
        <form action="{{ route('tiposancion.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="font-semibold text-gray-700">Nombre del Tipo</label>
                <input type="text" name="nombre"
                       class="w-full border rounded-lg px-3 py-2 mt-1"
                       placeholder="Ejemplo: Suspensión" required>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Descripción (opcional)</label>
                <textarea name="descripcion"
                          class="w-full border rounded-lg px-3 py-2 mt-1"
                          rows="2"
                          placeholder="Detalle del tipo de sanción"></textarea>
            </div>

            <div>
                <label class="font-semibold text-gray-700">¿Requiere días?</label>
                <select name="requiere_dias"
                        class="w-full border rounded-lg px-3 py-2 mt-1" required>
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </select>
            </div>

            <button class="bg-blue-700 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                Registrar Tipo de Sanción
            </button>
        </form>

    @else

        <!--  BUSCADOR -->
        <div class="mb-4 flex justify-end">
            <input id="buscar"
                   type="text"
                   placeholder="Buscar por nombre..."
                   class="border px-3 py-2 rounded-lg w-64">
        </div>

        <!--  LISTA -->
        <table class="w-full text-center border rounded-lg overflow-hidden" id="tabla-tipos">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="py-3">Nombre</th>
                    <th class="py-3 w-48">Descripción</th>
                    <th class="py-3">Requiere Días</th>
                    <th class="py-3">Estado</th>
                    <th class="py-3">Acciones</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                @forelse($tipos as $t)
                    <tr class="border-b fila">
                        <td class="py-3 nombre">{{ $t->nombre }}</td>

                        <!-- DESCRIPCIÓN REDUCIDA -->
                        <td class="px-2 text-sm text-gray-600 truncate max-w-[150px]">
                            {{ $t->descripcion ?? '-' }}
                        </td>

                        <td>{{ $t->requiere_dias ? 'Sí' : 'No' }}</td>
                        <td>{{ $t->estado ? 'Activo' : 'Inactivo' }}</td>

                        <td class="flex justify-center gap-4 py-3">

                            <button class="text-blue-600 font-semibold hover:underline"
                                    onclick="abrirModalEditar(
                                        {{ $t->id }},
                                        '{{ $t->nombre }}',
                                        `{{ $t->descripcion }}`,
                                        {{ $t->requiere_dias }}
                                    )">
                                Editar
                            </button>

                            <button class="text-red-600 font-semibold hover:underline"
                                    onclick="abrirModalEliminar({{ $t->id }})">
                                Eliminar
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-gray-500">
                            No hay tipos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @endif

</div>


<!-- MODALES (SE MANTIENEN IGUAL) -->
{{-- MODAL EDITAR --}}
<div id="modal-editar" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-96 shadow-xl">
        <h2 class="text-xl font-bold text-blue-700 mb-4">Editar Tipo</h2>

        <form id="form-editar" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit-id" name="id">

            <label class="font-semibold">Nombre</label>
            <input id="edit-nombre" name="nombre" class="w-full border rounded-lg px-3 py-2 mb-3" required>

            <label class="font-semibold">Descripción</label>
            <textarea id="edit-descripcion" name="descripcion"
                      class="w-full border rounded-lg px-3 py-2 mb-3" rows="2"></textarea>

            <label class="font-semibold">¿Requiere días?</label>
            <select id="edit-requiere" name="requiere_dias"
                    class="w-full border rounded-lg px-3 py-2 mb-4">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModalEditar()"
                        class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>

                <button class="px-4 py-2 bg-blue-700 text-white rounded-lg">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


{{-- MODAL ELIMINAR --}}
<div id="modal-eliminar" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-80 shadow-xl">
        <h2 class="text-xl font-bold text-red-600 mb-3">Eliminar Tipo</h2>

        <p class="text-gray-600 mb-4">Ingrese contraseña para confirmar.</p>

        <form id="form-eliminar" method="POST">
            @csrf
            @method('DELETE')

            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2 mb-4"
                   placeholder="Contraseña" required>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModalEliminar()"
                        class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>

                <button class="px-4 py-2 bg-red-600 text-white rounded-lg">
                    Eliminar
                </button>
            </div>
        </form>
    </div>
</div>


<script>
/* FILTRO EN TIEMPO REAL */
document.getElementById('buscar')?.addEventListener('input', function () {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('.fila').forEach(fila => {
        const nombre = fila.querySelector('.nombre').textContent.toLowerCase();
        fila.style.display = nombre.includes(filtro) ? '' : 'none';
    });
});

/* MODALES */
function abrirModalEditar(id, nombre, descripcion, requiere) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-descripcion').value = descripcion ?? '';
    document.getElementById('edit-requiere').value = requiere;

    document.getElementById('form-editar').action = `/tiposancion/${id}`;
    document.getElementById('modal-editar').classList.remove('hidden');
}

function cerrarModalEditar() {
    document.getElementById('modal-editar').classList.add('hidden');
}

function abrirModalEliminar(id) {
    document.getElementById('form-eliminar').action = `/tiposancion/${id}`;
    document.getElementById('modal-eliminar').classList.remove('hidden');
}

function cerrarModalEliminar() {
    document.getElementById('modal-eliminar').classList.add('hidden');
}
</script>

</body>
</html>
