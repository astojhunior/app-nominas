{{-- MODAL: EDITAR CARGO --}}
<div 
    x-show="showEditModalCargo"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
>
    <div class="bg-white w-96 p-6 rounded-xl shadow-lg">

        <h2 class="text-xl font-semibold mb-4 text-gray-800">
            Editar Cargo
        </h2>

        <form action="{{ route('cargos.update') }}" method="POST">
            @csrf

            <input type="hidden" name="cargo_id" :value="selectedCargo.id">

            {{-- SELECCIONAR ÁREA --}}
            <label class="block mb-2 font-medium text-gray-700">
                Área
            </label>

            <select 
                name="area_id"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none mb-4"
            >
                @foreach($areas as $area)
                    <option 
                        value="{{ $area->id }}"
                        :selected="selectedCargo.area_id == {{ $area->id }}"
                    >
                        {{ $area->nombre }}
                    </option>
                @endforeach
            </select>


            {{-- NOMBRE DEL CARGO --}}
            <label class="block mb-2 font-medium text-gray-700">
                Nombre del Cargo
            </label>

            <input 
                type="text"
                name="cargo"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none mb-4"
                :value="selectedCargo.cargo"
                required
            >


            {{-- SUELDO --}}
            <label class="block mb-2 font-medium text-gray-700">
                Sueldo (S/)
            </label>

            <input 
                type="number"
                step="0.01"
                name="sueldo"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none mb-4"
                :value="selectedCargo.sueldo"
                required
            >


            {{-- DESCRIPCIÓN --}}
            <label class="block mb-2 font-medium text-gray-700">
                Descripción
            </label>

            <textarea 
                name="descripcion"
                rows="3"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none mb-4"
                x-text="selectedCargo.descripcion"
            ></textarea>


            <div class="flex justify-end space-x-2">

                <button 
                    type="button"
                    @click="showEditModalCargo = false"
                    class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500"
                >
                    Cancelar
                </button>

                <button 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                >
                    Guardar Cambios
                </button>

            </div>

        </form>

    </div>
</div>
