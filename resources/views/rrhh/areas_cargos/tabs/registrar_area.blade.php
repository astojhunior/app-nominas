<div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

    <h2 class="text-xl font-semibold text-blue-700 mb-4">
        Registrar Nueva Área
    </h2>

    <form action="{{ route('areas.store') }}" method="POST">
        @csrf

        {{-- INPUT --}}
        <label class="block mb-2 font-medium text-gray-700">
            Nombre del Área
        </label>

        <input 
            type="text" 
            name="nombre" 
            class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
            placeholder="Ejemplo: Recursos Humanos"
            required
        >

        {{-- BOTÓN --}}
        <button 
            class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-all">
            Registrar Área
        </button>
    </form>

</div>
