<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Anka Fire</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-container">

        <div class="login-logo">
            <h1><span class="anka">ANKA</span> <span class="fire">FIRE</span></h1>
            <p>Equipos contra incendios</p>
        </div>

        <div class="login-card">
            <h2>Iniciar sesión</h2>

            @if (session('status'))
                <p class="form-error">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password" required>
                    <span id="toggle-password" class="toggle-password">Mostrar contraseña</span>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> Recordarme
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>
        </div>

        <p class="login-footer">&copy; {{ date('Y') }} Anka Fire — Equipos contra incendios</p>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>

</body>

</html>

