<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>

   <style>
        .triangulo-azul {
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: #1e3aef;
            clip-path: polygon(35% 0, 100% 0, 100% 100%, 10% 100%);
            z-index: 1;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 relative overflow-hidden">

    <!-- TRIÁNGULO AZUL -->
    <div class="triangulo-azul"></div>

    <!-- PUNTITOS DORADOS ARRIBA -->
    <div class="absolute top-10 right-[18%] grid grid-cols-6 gap-2 z-20 opacity-80">
        @for($i=0;$i<30;$i++)
            <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
        @endfor
    </div>

    <!-- PUNTITOS DORADOS ABAJO -->
    <div class="absolute bottom-10 right-[10%] grid grid-cols-6 gap-2 z-20 opacity-80">
        @for($i=0;$i<30;$i++)
            <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
        @endfor
    </div>

    <!-- ⬇⬇⬇ CAMBIO IMPORTANTE: h-screen PARA CENTRAR EL LOGIN -->
    <div class="flex w-full h-screen">

        <!-- LADO IZQUIERDO -->
        <div class="flex-1 flex items-center justify-center relative z-20">

            <div class="w-full max-w-sm ml-10">

                <!-- ICONO -->
                <div class="flex justify-center mb-6">
                    <img src="https://cdn-icons-png.flaticon.com/512/1144/1144760.png"
                         class="h-20 opacity-90" alt="usuario">
                </div>

                <!-- TÍTULO -->
                <h2 class="text-3xl font-bold text-center text-blue-800 mb-6">
                    ¡Bienvenido Nuevamente!
                </h2>

                <!-- ERRORES -->
                @if($errors->any())
                    <div class="bg-red-500 text-white p-2 rounded mb-4 text-center text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- FORMULARIO -->
                <form action="{{ route('admin.login.post') }}" method="POST">
                    @csrf

                    <!-- CORREO -->
                    <label class="block text-gray-700 font-semibold mb-1 ml-1 text-sm">
                        Correo electrónico
                    </label>
                    <input type="email" name="email"
                        class="w-full p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 mb-4 text-sm"
                        placeholder="admin@example.com" required>

                    <!-- CONTRASEÑA -->
                    <label class="block text-gray-700 font-semibold mb-1 ml-1 text-sm">
                        Contraseña
                    </label>

                    <div class="relative mb-4">
                        <input type="password" id="passwordInput" name="password"
                            class="w-full p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="••••••••" required>

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
                                       9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <!-- RECORDAR -->
                    <div class="flex items-center mb-4 ml-1">
                        <input id="recordar" type="checkbox"
                            class="mr-2 rounded text-blue-600">
                        <label for="recordar" class="text-gray-700 text-sm select-none">
                            Recordar contraseña
                        </label>
                    </div>

                    <!-- BOTÓN -->
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-md transition text-sm">
                        Entrar
                    </button>

                </form>

            </div>
        </div>

        <!-- LADO DERECHO (TEXTO) -->
        <div class="flex-1 flex items-center justify-center relative z-20 text-white text-center p-10">

            <div class="max-w-md">
                <h2 class="text-3xl font-bold mb-4 leading-tight">
                    ¡Disfrutemos de<br>nuestro trabajo juntos!
                </h2>

                <p class="opacity-90 text-sm">
                    Acceso seguro al panel administrativo.
                </p>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            let input = document.getElementById("passwordInput");
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>
