<div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

    <h2 class="text-xl font-semibold text-blue-700 mb-4">
        Lista de Áreas Registradas
    </h2>

    {{-- TABLA --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-blue-100 rounded-lg overflow-hidden bg-white">

            <thead class="bg-blue-50">
                <tr>
                    <th class="p-3 border text-left text-blue-700 font-semibold">ID</th>
                    <th class="p-3 border text-left text-blue-700 font-semibold">Área</th>
                    <th class="p-3 border text-center text-blue-700 font-semibold">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($areas as $area)
                    <tr class="hover:bg-blue-50 transition">

                        <td class="p-3 border">
                            {{ $area->id }}
                        </td>

                        <td class="p-3 border">
                            {{ $area->nombre }}
                        </td>

                        <td class="p-3 border text-center space-x-2">

                            {{-- BOTÓN EDITAR --}}
                            <button 
                                class="bg-yellow-500 text-white px-4 py-1 rounded-lg hover:bg-yellow-600 transition"
                                @click="
                                    selectedArea = {{ $area }};
                                    showPasswordModalArea = true;
                                "
                            >
                                Editar
                            </button>

                            {{-- BOTÓN ELIMINAR --}}
                            <button 
                                class="bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700 transition"
                                @click="
                                    selectedArea = {{ $area }};
                                    showDeleteModalArea = true;
                                "
                            >
                                Eliminar
                            </button>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">
                            No hay áreas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
