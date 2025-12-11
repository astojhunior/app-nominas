{{-- MODAL: EDITAR ÁREA --}}
<div 
    x-show="showEditModalArea"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
>
    <div class="bg-white w-96 p-6 rounded-xl shadow-xl border border-blue-100">

        <h2 class="text-xl font-semibold mb-4 text-blue-700">
            Editar Área
        </h2>

        <form action="{{ route('areas.update') }}" method="POST">
            @csrf

            <input type="hidden" name="area_id" :value="selectedArea.id">

            <label class="block mb-2 font-medium text-gray-700">
                Nuevo nombre del Área
            </label>

            <input 
                type="text" 
                name="nombre"
                class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
                :value="selectedArea.nombre"
                required
            >

            <div class="flex justify-end space-x-3">
                <button 
                    type="button"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100"
                    @click="resetModals()"
                >
                    Cancelar
                </button>

                <button 
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-700 text-white hover:bg-blue-800"
                >
                    Guardar
                </button>
            </div>

        </form>

    </div>
</div>
