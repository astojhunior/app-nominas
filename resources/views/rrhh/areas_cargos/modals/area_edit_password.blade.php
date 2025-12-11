{{-- MODAL: VERIFICAR CONTRASEÑA PARA EDITAR ÁREA --}}
<div 
    x-show="showPasswordModalArea"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
>
    <div class="bg-white w-96 p-6 rounded-xl shadow-lg">

        <h2 class="text-xl font-semibold mb-4 text-gray-800">
            Verificación de Seguridad
        </h2>

        <p class="text-gray-600 mb-4">
            Ingrese su contraseña para editar esta área.
        </p>

        {{-- INPUT DE CONTRASEÑA --}}
        <input 
            type="password" 
            x-model="passwordInput"
            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none mb-4"
            placeholder="Contraseña"
        >

        <div class="flex justify-end space-x-2">

            <button 
                @click="showPasswordModalArea = false; passwordInput='';"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500"
            >
                Cancelar
            </button>

            <button 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                @click="
                    fetch('{{ route('areas.checkPassword') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            password: passwordInput,
                            area_id: selectedArea.id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showPasswordModalArea = false;
                            showEditModalArea = true;
                            passwordInput='';
                        } else {
                            alert(data.message);
                        }
                    });
                "
            >
                Confirmar
            </button>

        </div>

    </div>
</div>
