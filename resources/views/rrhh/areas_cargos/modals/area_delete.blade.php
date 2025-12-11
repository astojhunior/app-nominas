{{-- MODAL: ELIMINAR ÁREA --}}
<div 
    x-show="showDeleteModalArea"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
>
    <div class="bg-white w-96 p-6 rounded-xl shadow-lg">

        <h2 class="text-xl font-semibold mb-4 text-gray-800">
            Eliminar Área
        </h2>

        <p class="mb-3 text-gray-700">
            ¿Está seguro que desea eliminar el área 
            <strong x-text="selectedArea.nombre"></strong>?
        </p>

        <p class="text-sm text-red-600 mb-4">
            Esta acción es permanente y no se puede deshacer.
        </p>

        <form action="{{ route('areas.destroy') }}" method="POST">
            @csrf

            <input type="hidden" name="area_id" :value="selectedArea.id">

            <label class="block mb-2 font-medium text-gray-700">
                Ingrese su contraseña:
            </label>

            <input 
                type="password"
                name="password"
                required
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none mb-4"
            >

            <div class="flex justify-end space-x-2">

                <button 
                    type="button"
                    @click="showDeleteModalArea = false"
                    class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500"
                >
                    Cancelar
                </button>

                <button 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                >
                    Eliminar
                </button>

            </div>

        </form>

    </div>
</div>
