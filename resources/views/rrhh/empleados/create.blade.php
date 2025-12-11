<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen p-6 bg-[#0A4CB5]">

    <!-- CONTENEDOR CENTRAL -->
    <div class="max-w-6xl mx-auto bg-white shadow-xl rounded-2xl p-10 border border-blue-100"
        x-data="{
            metodoPago: '{{ old('metodo_pago', 'efectivo') }}',
            areaSeleccionada: '{{ old('area_id') }}',
            sistemaPension: '{{ old('sistema_pension') }}',
            cargos: {{ $cargos->toJson() }},
        }">

        <!-- CABECERA -->
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-blue-700">Registrar Empleado</h1>

            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 border border-blue-200 bg-white text-blue-700 rounded-lg 
                       hover:bg-blue-50 transition shadow-sm">
                Volver
            </a>
        </div>

        <!-- ERRORES -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 text-red-800 p-4 rounded-lg border border-red-300">
                <strong>Debes corregir lo siguiente:</strong>
                <ul class="ml-6 mt-2 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <!-- FORMULARIO -->
        <form action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-10">

            @csrf

            <!-- ========================================================= -->
            <!-- DATOS PERSONALES -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Datos personales</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="font-medium">Nombres *</label>
                        <input name="nombres" value="{{ old('nombres') }}" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Apellidos *</label>
                        <input name="apellidos" value="{{ old('apellidos') }}" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">DNI *</label>
                        <input name="dni" maxlength="8" value="{{ old('dni') }}" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Correo *</label>
                        <input type="email" name="correo" value="{{ old('correo') }}" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Fecha de nacimiento *</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Sexo *</label>
                        <select name="sexo" required
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Seleccione</option>
                            <option {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option {{ old('sexo') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Estado civil</label>
                        <select name="estado_civil"
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Seleccione</option>
                            <option {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                            <option {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado</option>
                            <option {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                            <option {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Nacionalidad</label>
                        <select name="nacionalidad"
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Seleccione</option>

                            @php
                                $paises = [
                                    'Perú','Argentina','Bolivia','Brasil','Chile','Colombia',
                                    'Ecuador','Venezuela','México','Estados Unidos','Canadá',
                                    'España','Italia','Francia','Alemania','Japón','China'
                                ];
                            @endphp

                            @foreach ($paises as $p)
                                <option {{ old('nacionalidad') == $p ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Dirección</label>
                        <input name="direccion" value="{{ old('direccion') }}"
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Teléfono</label>
                        <input name="telefono" value="{{ old('telefono') }}"
                            class="block w-full mt-1 border border-blue-200 px-4 py-2 rounded-lg 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>

                </div>
            </div>


            <!-- ========================================================= -->
            <!-- CONTACTO EMERGENCIA -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Contacto de emergencia</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <input name="emergencia_nombre" placeholder="Nombre"
                        class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">

                    <input name="emergencia_telefono" placeholder="Teléfono"
                        class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">

                    <input name="emergencia_parentesco" placeholder="Parentesco"
                        class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                </div>
            </div>


            <!-- FOTO -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Foto del empleado</h2>
            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">
                <input type="file" name="foto"
                    class="border border-blue-200 px-4 py-2 rounded-lg w-full
                           focus:ring-blue-600">
            </div>


            <!-- ========================================================= -->
            <!-- DATOS LABORALES -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Datos laborales</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="font-medium">Área *</label>
                        <select name="area_id" x-model="areaSeleccionada" required
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione área</option>

                            @foreach ($areas as $a)
                                <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Cargo *</label>
                        <select name="cargo_id" required
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione cargo</option>

                            <template x-for="c in cargos.filter(c => c.area_id == areaSeleccionada)">
                                <option :value="c.id" x-text="c.cargo"></option>
                            </template>

                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Tipo contrato *</label>
                        <select name="tipo_contrato" required
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione</option>
                            <option value="temporal">Temporal</option>
                            <option value="indefinido">Indefinido</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-medium">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" required
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="font-medium">Fecha fin</label>
                        <input type="date" name="fecha_fin"
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                    </div>

                </div>
            </div>


            <!-- ========================================================= -->
            <!-- AFP / ONP -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Sistema de pensiones</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <label class="font-medium">Sistema *</label>
                        <select name="sistema_pension" x-model="sistemaPension" required
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione</option>
                            <option value="AFP">AFP</option>
                            <option value="ONP">ONP</option>
                        </select>
                    </div>

                    <div x-show="sistemaPension === 'AFP'" x-transition>
                        <label class="font-medium">Nombre AFP *</label>
                        <select name="afp_nombre"
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione</option>
                            <option>AFP Integra</option>
                            <option>AFP Prima</option>
                            <option>AFP Habitat</option>
                            <option>AFP Profuturo</option>
                        </select>
                    </div>

                    <div x-show="sistemaPension === 'AFP'" x-transition>
                        <label class="font-medium">Tipo AFP *</label>
                        <select name="afp_tipo"
                            class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                            <option value="">Seleccione</option>
                            <option value="flujo">Flujo</option>
                            <option value="mixta">Mixta</option>
                        </select>
                    </div>

                </div>
            </div>


            <!-- ========================================================= -->
            <!-- MÉTODO DE PAGO -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Pago</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">

                <label class="font-medium">Método de pago *</label>
                <select name="metodo_pago" x-model="metodoPago" required
                    class="mt-1 block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                    <option value="">Seleccione</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>

                <div x-show="metodoPago === 'transferencia'"
                    class="mt-6 p-6 border rounded-lg bg-blue-50 shadow-sm"
                    x-transition>

                    <h3 class="font-semibold mb-4 text-blue-700">Datos bancarios</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <input name="banco" placeholder="Banco"
                            class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">

                        <input name="cuenta_bancaria" placeholder="Cuenta bancaria"
                            class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">

                        <input name="tipo_cuenta" placeholder="Tipo de cuenta"
                            class="border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                    </div>

                </div>
            </div>


            <!-- ========================================================= -->
            <!-- ESTADO -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Estado del empleado</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">
                <select name="estado_empleado"
                    class="block w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600">
                    <option value="activo" selected>Activo</option>
                </select>
            </div>


            <!-- ========================================================= -->
            <!-- OBSERVACIONES -->
            <!-- ========================================================= -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Observaciones</h2>

            <div class="bg-white p-6 rounded-xl shadow-md border border-blue-100">
                <textarea name="observaciones" rows="3"
                    class="w-full border border-blue-200 px-4 py-2 rounded-lg focus:ring-blue-600"></textarea>
            </div>


            <!-- ========================================================= -->
            <!-- BOTÓN FINAL -->
            <!-- ========================================================= -->
            <button type="submit"
                class="w-full py-3 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl shadow-lg transition">
                Registrar Empleado y Contrato
            </button>

        </form>

    </div>

</body>
</html>
