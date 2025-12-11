<div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

    <h2 class="text-xl font-semibold text-blue-700 mb-4">
        Registrar Nuevo Cargo
    </h2>

    <form action="{{ route('cargos.store') }}" method="POST">
        @csrf

        {{-- SELECCIONAR ÁREA --}}
        <label class="block mb-2 font-medium text-gray-700">
            Área
        </label>

        <select 
            name="area_id"
            class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
            required
        >
            <option value="">Seleccione un área</option>

            @foreach($areas as $area)
                <option value="{{ $area->id }}">
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
            class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
            placeholder="Ejemplo: Analista de Nómina"
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
            class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
            placeholder="Ejemplo: 1500.00"
            required
        >

        {{-- DESCRIPCIÓN --}}
        <label class="block mb-2 font-medium text-gray-700">
            Descripción (Opcional)
        </label>

        <textarea
            name="descripcion"
            class="w-full p-3 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:outline-none mb-4"
            placeholder="Descripción breve del cargo…"
            rows="3"
        ></textarea>

        {{-- BOTÓN --}}
        <button 
            class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition-all">
            Registrar Cargo
        </button>

    </form>

</div>
