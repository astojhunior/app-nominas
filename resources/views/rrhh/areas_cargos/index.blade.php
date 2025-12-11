<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Áreas y Cargos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

{{-- FONDO DEL MISMO COLOR QUE LA LETRA text-blue-700 (#1D4ED8) --}}
<body class="min-h-screen p-6" style="background-color:#1D4ED8;">

    <div class="max-w-7xl mx-auto bg-white shadow-xl rounded-2xl p-6 border border-blue-100"
         x-data="{
            tab: 'registrar_area',

            showPasswordModalArea: false,
            showEditModalArea: false,
            showDeleteModalArea: false,

            showPasswordModalCargo: false,
            showEditModalCargo: false,
            showDeleteModalCargo: false,

            selectedArea: null,
            selectedCargo: null,
            passwordInput: '',

            resetModals() {
                this.showPasswordModalArea = false;
                this.showEditModalArea = false;
                this.showDeleteModalArea = false;

                this.showPasswordModalCargo = false;
                this.showEditModalCargo = false;
                this.showDeleteModalCargo = false;

                this.passwordInput = '';
                this.selectedArea = null;
                this.selectedCargo = null;
            }
         }"
         x-init="resetModals()"
    >

        {{-- TITULO + BOTÓN DE REGRESAR --}}
        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-extrabold text-blue-700">
                Gestión de Áreas y Cargos
            </h1>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg
                      hover:bg-blue-800 transition shadow-md">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 15l-6-6m0 0l6-6m-6 6h18" />
                </svg>

               Volver
            </a>

        </div>

        {{-- MENSAJES GLOBALES --}}
        @include('rrhh.areas_cargos.messages')

        {{-- HEADER DE PESTAÑAS --}}
        @include('rrhh.areas_cargos.tabs_header')

        {{-- TAB: Registrar Área --}}
        <div x-show="tab === 'registrar_area'" x-cloak>
            @include('rrhh.areas_cargos.tabs.registrar_area')
        </div>

        {{-- TAB: Registrar Cargo --}}
        <div x-show="tab === 'registrar_cargo'" x-cloak>
            @include('rrhh.areas_cargos.tabs.registrar_cargo')
        </div>

        {{-- TAB: Listar Áreas --}}
        <div x-show="tab === 'listar_areas'" x-cloak>
            @include('rrhh.areas_cargos.tabs.listar_areas')
        </div>

        {{-- TAB: Listar Cargos --}}
        <div x-show="tab === 'listar_cargos'" x-cloak>
            @include('rrhh.areas_cargos.tabs.listar_cargos')
        </div>

        {{-- MODALES ÁREA --}}
        @include('rrhh.areas_cargos.modals.area_edit_password')
        @include('rrhh.areas_cargos.modals.area_edit')
        @include('rrhh.areas_cargos.modals.area_delete')

        {{-- MODALES CARGO --}}
        @include('rrhh.areas_cargos.modals.cargo_edit_password')
        @include('rrhh.areas_cargos.modals.cargo_edit')
        @include('rrhh.areas_cargos.modals.cargo_delete')

    </div>

</body>
</html>
