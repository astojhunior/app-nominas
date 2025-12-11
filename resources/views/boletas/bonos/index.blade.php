<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Bonos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>[x-cloak]{ display:none !important; }</style>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-7xl rounded-2xl shadow-xl p-8"
     x-data="{
        tab: '{{ request('tab','registrar') }}',
        showDelete:false,
        showEdit:false,
        showRenew:false,
        deleteId:null,
        editData:{},
        renewId:null,
        password:''
     }">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">Gestión de Bonos</h1>

        <a href="{{ route('boletas.index') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- MENSAJES ANIMADOS -->
    <div class="relative mb-6">

        @if(session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-3"
                class="p-4 rounded-lg shadow-lg bg-green-600 text-white font-semibold text-center"
            >
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 -translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-3"
                class="p-4 rounded-lg shadow-lg bg-red-600 text-white font-semibold text-center"
            >
                {{ session('error') }}
            </div>
        @endif

    </div>

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <button @click="tab='registrar'"
                    class="pb-3"
                    :class="tab=='registrar'
                        ? 'text-blue-700 border-b-4 border-blue-700'
                        : 'text-gray-500 hover:text-blue-600'">
                Registrar Bono
            </button>

            <button @click="tab='lista'"
                    class="pb-3"
                    :class="tab=='lista'
                        ? 'text-blue-700 border-b-4 border-blue-700'
                        : 'text-gray-500 hover:text-blue-600'">
                Lista de Bonos
            </button>

        </div>
    </div>

    <!-- ===================================================== -->
    <!-- TAB REGISTRAR BONO -->
    <!-- ===================================================== -->
    <div x-show="tab=='registrar'" x-cloak>

    <form action="{{ route('bonos.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-6">

            <div>
                <label class="font-semibold">Nombre del Bono:</label>
                <input type="text" name="nombre" required
                       class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="font-semibold">Monto (S/):</label>
                <input type="number" step="0.01" name="monto" required
                       class="w-full border p-2 rounded">
            </div>

           
            <div>
                <label class="font-semibold">Fecha de aplicación:</label>
                <input type="date" name="fecha_aplicacion"
                       value="{{ date('Y-m-d') }}"
                       required
                       class="w-full border p-2 rounded">
            </div>
           

                <!-- DIRIGIDO A -->
                <div>
                    <label class="font-semibold block">Dirigido a:</label>

                    <select name="dirigido_a" id="dirigido_a"
                            class="w-full border p-2 rounded"
                            onchange="actualizarDestino(this.value)">
                        <option value="">-- Seleccionar --</option>
                        <option value="empleado">Empleado específico</option>
                        <option value="cargo">Cargo</option>
                        <option value="todos">Todos los empleados</option>
                    </select>
                </div>

                <!-- SELECT EMPLEADO -->
                <div id="selectEmpleado" class="hidden">
                    <label class="font-semibold">Empleado:</label>
                    <select name="empleado_id" class="w-full border p-2 rounded">
                        <option value="">-- Seleccionar --</option>
                        @foreach($empleados as $e)
                            <option value="{{ $e->id }}">
                                {{ $e->apellidos }} {{ $e->nombres }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- SELECT CARGO -->
                <div id="selectCargo" class="hidden">
                    <label class="font-semibold">Cargo:</label>
                    <select name="cargo_id" class="w-full border p-2 rounded">
                        <option value="">-- Seleccionar --</option>
                        @foreach($cargos as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->cargo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- MOTIVO -->
                <div class="col-span-2">
                    <label class="font-semibold">Motivo:</label>
                    <<textarea name="motivo" class="w-full border p-2 rounded h-24"
          x-model="editData.motivo"></textarea>
                </div>

                <!-- MESES APLICACIÓN -->
                <div class="col-span-2">
                    <label class="font-semibold">Meses en que aplicará:</label>

                    <div class="grid grid-cols-6 gap-2 mt-2">
                        @php
                            $meses = [
                                1 => 'ENE', 2=>'FEB', 3=>'MAR', 4=>'ABR',
                                5=>'MAY', 6=>'JUN', 7=>'JUL', 8=>'AGO',
                                9=>'SEP', 10=>'OCT', 11=>'NOV', 12=>'DIC'
                            ];
                            $año = date('Y');
                        @endphp

                        @foreach($meses as $num=>$short)
                            <label class="flex items-center gap-2">
                                <input type="checkbox"
                                       name="meses_aplicacion[]"
                                       value="{{ $año.'-'.sprintf('%02d',$num) }}">
                                {{ $short }} {{ $año }}
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <button class="bg-blue-700 text-white px-5 py-2 rounded shadow hover:bg-blue-800">
                Registrar Bono
            </button>

        </form>
    </div>

    <!-- ===================================================== -->
    <!-- TAB LISTA -->
    <!-- ===================================================== -->
    <div x-show="tab=='lista'" x-cloak>

        <table class="w-full text-center border rounded-lg overflow-hidden mt-4">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">Nombre</th>
                <th class="py-3">Dirigido a</th>
                <th class="py-3">Monto</th>
                <th class="py-3">Duración</th>
                <th class="py-3">Estado</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">

            @forelse($bonosActivos->merge($bonosVencidos) as $b)
                @php
                    $duracion = collect($b->meses_aplicacion)->map(function($m){
                        return strtoupper(date('M y', strtotime($m)));
                    })->join(', ');

                    $mesArray = $b->meses_aplicacion;
                    $mini = strtoupper(date('M', strtotime($mesArray[0] ?? 'now')));
                    $maxi = strtoupper(date('M', strtotime(end($mesArray) ?? 'now')));

                    $añoDur = date('Y', strtotime($mesArray[0] ?? 'now'));

                    if(count($mesArray) > 1){
                        $duracion = "$mini–$maxi $añoDur";
                    }
                @endphp

                <tr class="border-b">

                    <td class="py-3">{{ $b->nombre }}</td>

                    <td>
                        @if($b->dirigido_a=='empleado')
                            {{ $b->empleado->apellidos }} {{ $b->empleado->nombres }}
                        @elseif($b->dirigido_a=='cargo')
                            Cargo: {{ $b->cargo->cargo }}
                        @else
                            Todos
                        @endif
                    </td>

                    <td>S/ {{ number_format($b->monto,2) }}</td>
                    <td>{{ $duracion }}</td>

                    <td>
                        <span class="px-3 py-1 rounded text-white 
                            {{ $b->estado=='activo' ? 'bg-green-600':'bg-red-600' }}">
                            {{ strtoupper($b->estado) }}
                        </span>
                    </td>

                    <td class="py-3 flex justify-center gap-5">

                        <!-- EDITAR -->
                        <button
                            @click='
    showEdit=true;
    editData = JSON.parse(`@json($b)`);
'
                            class="text-blue-700 hover:underline font-semibold">
                            Editar
                        </button>

                        <!-- RENOVAR -->
                        <button
                            @click="showRenew=true; renewId={{ $b->id }}"
                            class="text-green-700 hover:underline font-semibold 
                                   {{ $b->estado=='vencido' ? '' : 'opacity-40 cursor-not-allowed' }}"
                            {{ $b->estado=='vencido' ? '' : 'disabled' }}>
                            Renovar
                        </button>

                        <!-- ELIMINAR -->
                        <button
                            @click="showDelete=true; deleteId={{ $b->id }}"
                            class="text-red-600 hover:underline font-semibold">
                            Eliminar
                        </button>

                    </td>

                </tr>
            @empty
                <tr><td colspan="6" class="py-4 text-gray-500">Sin registros</td></tr>
            @endforelse

            </tbody>
        </table>

    </div>


    <!-- ===================================================== -->
    <!-- MODAL ELIMINAR -->
    <!-- ===================================================== -->
    <div x-show="showDelete"
         x-transition.opacity.duration.300ms
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white p-6 rounded-xl w-96 shadow-xl">

            <h2 class="text-xl font-bold text-red-600 mb-3">
                Confirmar eliminación
            </h2>

            <p class="mb-3">Ingrese su contraseña para eliminar el bono.</p>

            <input type="password" x-model="password"
                   class="w-full border p-2 rounded mb-4" placeholder="Contraseña">

            <div class="flex justify-end gap-3">
                <button @click="showDelete=false; password=''"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancelar
                </button>

                <form :action="`/bonos/delete/${deleteId}`" method="POST">
                    @csrf @method('DELETE')
                    <input type="hidden" name="password" :value="password">

                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </form>
            </div>

        </div>
    </div>
<!-- ===================================================== -->
<!-- MODAL EDITAR BONO -->
<!-- ===================================================== -->
<div 
    x-show="showEdit"
    x-transition.opacity.duration.300ms
    x-cloak
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
>
    <div class="bg-white p-6 rounded-xl w-[520px] shadow-xl relative"
         @click.away="showEdit=false"
         x-data="{ errorPass: '' }"
    >
        <h2 class="text-xl font-bold text-blue-700 mb-4">Editar Bono</h2>

        <!-- MENSAJE DE ERROR DEL BACKEND (por contraseña incorrecta) -->
        @if($errors->has('edit'))
            <div 
                x-data="{show:true}" 
                x-init="setTimeout(()=>show=false,3500)" 
                x-show="show"
                x-transition:enter="transition ease-out duration-500" 
                x-transition:enter-start="opacity-0 -translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-3"
                class="bg-red-600 text-white p-3 mb-4 rounded font-semibold text-center shadow-lg"
            >
                {{ $errors->first('edit') }}
            </div>
        @endif

        <!-- FORMULARIO EDITAR -->
        <form 
            x-ref="formEdit"
            @submit.prevent="
                if (!$refs.passEdit.value) {
                    errorPass = 'Debe ingresar su contraseña para continuar.';
                } else {
                    errorPass = '';
                    $refs.formEdit.submit();
                }
            "
            :action="`/bonos/update/${editData.id}`"
            method="POST"
        >
            @csrf 
            @method('PUT')

            <!-- ID DEL BONO -->
            <input type="hidden" name="edit_id" :value="editData.id">

            <!-- CAMPOS -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Nombre:</label>
                    <input 
                        type="text"
                        class="w-full border p-2 rounded"
                        name="nombre"
                        :value="editData.nombre"
                    >
                </div>

                <div>
                    <label>Monto:</label>
                    <input 
                        type="number" 
                        step="0.01"
                        class="w-full border p-2 rounded"
                        name="monto"
                        :value="editData.monto"
                    >
                </div>
            </div>

            <div class="mt-3">
                <label>Motivo:</label>
                <textarea 
                    name="motivo" 
                    class="w-full border p-2 rounded h-24"
                    x-text="editData.motivo"
                ></textarea>
            </div>

            <div class="mt-3">
                <label>Contraseña para confirmar:</label>
                <input 
                    type="password" 
                    name="password"
                    x-ref="passEdit"
                    class="w-full border p-2 rounded"
                >
                <!-- MENSAJE DE VALIDACIÓN EN EL CLIENTE -->
                <p 
                    x-show="errorPass"
                    x-transition.opacity.duration.200ms
                    class="text-sm text-red-600 mt-1"
                    x-text="errorPass"
                ></p>
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3 mt-4">
                <button 
                    type="button"
                    @click="showEdit=false"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                >
                    Cancelar
                </button>

                <button 
                    class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800"
                >
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>


    <!-- ===================================================== -->
    <!-- MODAL RENOVAR -->
    <!-- ===================================================== -->
    <div x-show="showRenew"
         x-transition.opacity.duration.300ms
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white p-6 rounded-xl w-96 shadow-xl">

            <h2 class="text-xl font-bold text-green-700 mb-3">
                Renovar Bono
            </h2>

            <form :action="`/bonos/renovar/${renewId}`" method="POST">
                @csrf

                <label>Seleccione meses adicionales:</label>

                <div class="grid grid-cols-3 gap-2 mt-2">
                    @foreach($meses as $num=>$txt)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="meses_aplicacion[]"
                                value="{{ date('Y').'-'.sprintf('%02d',$num) }}">
                            {{ $txt }} {{ date('Y') }}
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button"
                            @click="showRenew=false"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancelar
                    </button>

                    <button class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                        Renovar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function actualizarDestino(v){
    document.querySelector('#selectEmpleado').classList.add('hidden');
    document.querySelector('#selectCargo').classList.add('hidden');

    if(v === 'empleado') document.querySelector('#selectEmpleado').classList.remove('hidden');
    if(v === 'cargo') document.querySelector('#selectCargo').classList.remove('hidden');
}
</script>

</body>
</html>
