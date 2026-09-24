<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · Anka Fire</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-body">
    <main class="login-wrap">
        <div class="login-card">

            {{-- Columna del formulario --}}
            <section class="login-form-col">

                {{-- Logo --}}
               

                <div class="login-content">
                    <h1>Iniciar sesión</h1>
                    <p class="login-sub">Ingresa para ver el estado de tus extintores.</p>

                    @if (session('status'))
                        <div class="alert-ok">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        {{-- Correo --}}
                        <div class="field">
                            <label for="email" class="sr-only">Correo electrónico</label>
                            <div class="input-wrap">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
                                </svg>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                       placeholder="Correo electrónico" required autofocus autocomplete="username">
                            </div>
                            @error('email')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contraseña --}}
                        <div class="field">
                            <label for="password" class="sr-only">Contraseña</label>
                            <div class="input-wrap">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>
                                </svg>
                                <input id="password" type="password" name="password"
                                       placeholder="Contraseña" required autocomplete="current-password">
                                <button type="button" id="togglePassword" class="toggle-pass" aria-label="Mostrar contraseña">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                                        <path class="slash" d="M4 4l16 16"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Recordarme / olvidé contraseña --}}
                        <div class="row-options">
                            <label for="remember_me" class="check">
                                <input id="remember_me" type="checkbox" name="remember">
                                Recordarme
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="link-red">¿Olvidaste tu contraseña?</a>
                            @endif
                        </div>

                        <button type="submit" class="btn-primary">Ingresar</button>
                    </form>
                </div>

                <p class="login-footer">© {{ date('Y') }} Anka Fire · Equipos contra incendios</p>
            </section>

            {{-- Panel derecho: logo grande y centrado con llamas animadas --}}
            <aside class="login-panel">
                <div class="logo-anim" id="logoAnim" role="img" aria-label="Anka Fire, equipos contra incendios">
                    <img id="logoImg" src="{{ asset('img/logo-anka-panel.png') }}" alt="" width="1100" height="924">
                    <canvas id="logoCanvas" aria-hidden="true"></canvas>
                </div>

                <p class="panel-text">Tus extintores, vencimientos y certificados en un solo lugar.</p>
            </aside>
        </div>
    </main>

    <script src="{{ asset('js/login.js') }}"></script>
    <script src="{{ asset('js/logo-fuego.js') }}"></script>
</body>
</html>
