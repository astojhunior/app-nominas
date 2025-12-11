<div 
    class="bg-white p-6 rounded-xl shadow-md border border-blue-100"
    x-data="{
        search: '',
        filterArea: '',
        orderBy: '',
        cargosOriginal: {{ $cargos->toJson() }},
        
        get cargosFiltrados() {
            let data = this.cargosOriginal;

            // BUSQUEDA GENERAL
            if (this.search.trim() !== '') {
                const txt = this.search.toLowerCase();
                data = data.filter(c =>
                    c.cargo.toLowerCase().includes(txt) ||
                    c.area.nombre.toLowerCase().includes(txt)
                );
            }

            // FILTRO POR ÁREA
            if (this.filterArea !== '') {
                data = data.filter(c => c.area_id == this.filterArea);
            }

            // ORDENAMIENTO
            if (this.orderBy === 'az') {
                data = data.sort((a, b) => a.cargo.localeCompare(b.cargo));
            }

            if (this.orderBy === 'za') {
                data = data.sort((a, b) => b.cargo.localeCompare(a.cargo));
            }

            if (this.orderBy === 'sueldo_asc') {
                data = data.sort((a, b) => a.sueldo - b.sueldo);
            }

            if (this.orderBy === 'sueldo_desc') {
                data = data.sort((a, b) => b.sueldo - a.sueldo);
            }

            return data;
        }
    }"
>

    <h2 class="text-xl font-semibold text-blue-700 mb-4">
        Lista de Cargos Registrados
    </h2>

    {{-- CONTROLES SUPERIORES --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">

        {{-- BUSCADOR --}}
        <input
            type="text"
            x-model="search"
            class="p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
            placeholder="Buscar por cargo o área…"
        >

        {{-- FILTRAR POR ÁREA --}}
        <select 
            x-model="filterArea"
            class="p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
        >
            <option value="">Filtrar por Área</option>

            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
            @endforeach
        </select>

        {{-- ORDENAR --}}
        <select 
            x-model="orderBy"
            class="p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
        >
            <option value="">Ordenar por…</option>
            <option value="az">Nombre A-Z</option>
            <option value="za">Nombre Z-A</option>
            <option value="sueldo_asc">Sueldo: Menor a Mayor</option>
            <option value="sueldo_desc">Sueldo: Mayor a Menor</option>
        </select>

    </div>

    {{-- TABLA --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-blue-100 rounded-lg overflow-hidden bg-white">

            <thead class="bg-blue-50">
                <tr>
                    <th class="p-3 border w-12 text-left text-blue-700 font-semibold">ID</th>
                    <th class="p-3 border w-40 text-left text-blue-700 font-semibold">Área</th>
                    <th class="p-3 border w-32 text-left text-blue-700 font-semibold">Cargo</th>
                    <th class="p-3 border w-28 text-center text-blue-700 font-semibold">Sueldo</th>
                    <th class="p-3 border w-2/5 text-left text-blue-700 font-semibold">Descripción</th>
                    <th class="p-3 border w-32 text-center text-blue-700 font-semibold">Acciones</th>
                </tr>
            </thead>

            <tbody>

                <template x-for="cargo in cargosFiltrados" :key="cargo.id">
                    <tr class="hover:bg-blue-50 transition">

                        <td class="p-3 border" x-text="cargo.id"></td>

                        <td class="p-3 border" x-text="cargo.area.nombre"></td>

                        <td class="p-3 border" x-text="cargo.cargo"></td>

                        {{-- SUELDO FORMATEADO CORRECTAMENTE --}}
                        <td 
                            class="p-3 border text-center font-semibold"
                            x-text="'S/ ' + Number(cargo.sueldo).toFixed(2)">
                        </td>

                        <td class="p-3 border" x-text="cargo.descripcion ?? '—'"></td>

                        {{-- BOTONES --}}
                        <td class="p-3 border">
                            <div class="flex justify-center gap-4">

                                {{-- EDITAR --}}
                                <button 
                                    class="bg-yellow-500 text-white px-4 py-1 rounded-lg hover:bg-yellow-600 transition"
                                    @click="
                                        selectedCargo = cargo;
                                        showPasswordModalCargo = true;
                                    "
                                >
                                    Editar
                                </button>

                                {{-- ELIMINAR --}}
                                <button 
                                    class="bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700 transition"
                                    @click="
                                        selectedCargo = cargo;
                                        showDeleteModalCargo = true;
                                    "
                                >
                                    Eliminar
                                </button>

                            </div>
                        </td>

                    </tr>
                </template>

                {{-- VACÍO --}}
                <tr x-show="cargosFiltrados.length === 0">
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        No se encontraron resultados.
                    </td>
                </tr>

            </tbody>

        </table>
    </div>

</div>
