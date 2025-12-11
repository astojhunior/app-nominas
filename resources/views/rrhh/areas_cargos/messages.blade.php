{{-- MENSAJES DE ALERTA GLOBAL DEL SISTEMA --}}
<div class="space-y-3 mb-6">

    @if(session('success_area'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_area') }}
        </div>
    @endif

    @if(session('success_cargo'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_cargo') }}
        </div>
    @endif

    @if(session('success_update_area'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_update_area') }}
        </div>
    @endif

    @if(session('success_update_cargo'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_update_cargo') }}
        </div>
    @endif

    @if(session('success_delete_area'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_delete_area') }}
        </div>
    @endif

    @if(session('success_delete_cargo'))
        <div class="bg-green-500 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('success_delete_cargo') }}
        </div>
    @endif

    @if(session('error_area'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_area') }}
        </div>
    @endif

    @if(session('error_cargo'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_cargo') }}
        </div>
    @endif

    @if(session('error_update_area'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_update_area') }}
        </div>
    @endif

    @if(session('error_update_cargo'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_update_cargo') }}
        </div>
    @endif

    @if(session('error_delete_area'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_delete_area') }}
        </div>
    @endif

    @if(session('error_delete_cargo'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_delete_cargo') }}
        </div>
    @endif

    @if(session('error_password'))
        <div class="bg-red-600 text-white p-3 rounded-lg shadow-md text-sm">
            {{ session('error_password') }}
        </div>
    @endif

</div>
