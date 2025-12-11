<div class="flex space-x-8 border-b pb-2 mb-6 select-none">

    <!-- TAB Registrar Área -->
    <button 
        @click="tab = 'registrar_area'"
        :class="tab === 'registrar_area'
            ? 'text-blue-700 font-semibold border-b-4 border-blue-700'
            : 'text-gray-500 hover:text-blue-700'"
        class="px-1 pb-2 transition-all duration-200"
    >
        Registrar Área
    </button>

    <!-- TAB Registrar Cargo -->
    <button 
        @click="tab = 'registrar_cargo'"
        :class="tab === 'registrar_cargo'
            ? 'text-blue-700 font-semibold border-b-4 border-blue-700'
            : 'text-gray-500 hover:text-blue-700'"
        class="px-1 pb-2 transition-all duration-200"
    >
        Registrar Cargo
    </button>

    <!-- TAB Listar Áreas -->
    <button 
        @click="tab = 'listar_areas'"
        :class="tab === 'listar_areas'
            ? 'text-blue-700 font-semibold border-b-4 border-blue-700'
            : 'text-gray-500 hover:text-blue-700'"
        class="px-1 pb-2 transition-all duration-200"
    >
        Listar Áreas
    </button>

    <!-- TAB Listar Cargos -->
    <button 
        @click="tab = 'listar_cargos'"
        :class="tab === 'listar_cargos'
            ? 'text-blue-700 font-semibold border-b-4 border-blue-700'
            : 'text-gray-500 hover:text-blue-700'"
        class="px-1 pb-2 transition-all duration-200"
    >
        Listar Cargos
    </button>

</div>
