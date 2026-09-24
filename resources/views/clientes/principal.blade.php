<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anka Fire - Panel Principal</title>
    <!-- Hoja de Estilos Externa -->
    <link rel="stylesheet" href="{{ asset('css/principal.css') }}">
</head>
<body>

    <!-- Encabezado Estilo Corporativo Anka Fire -->
    <header class="anka-header">
        <div class="anka-container">
            <!-- Logo -->
            <div style="display: flex; align-items: center;">
                <a href="{{ route('principal') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="Anka Fire" style="height: 75px; width: auto; object-fit: contain; display: block;">
                </a>
            </div>

            <!-- Menú de Usuario Desplegable -->
            <div class="anka-user-dropdown">
                <div class="anka-user-btn">
                    <span>{{ Auth::user()->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                
                <div class="anka-dropdown-content">
                    <a href="{{ route('profile.edit') }}">Perfil</a>
                    <!-- Authentication (Logout) -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" onclick="event.preventDefault(); this.closest('form').submit();">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Principal con la Tabla de Clientes -->
    <main class="anka-main" style="flex-direction: column; align-items: center; justify-content: flex-start;">
        <div class="anka-table-container">
            
            <!-- Cabecera de la sección con Título, Contador y Botón AGREGAR CLIENTES -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Listado de Clientes Registrados</h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Total: {{ $resumen['total'] }} registros</span>
                </div>
                
                <!-- Botón solicitado: AGREGAR CLIENTES -->
                <a href="#" class="anka-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar Clientes
                </a>
            </div>
            
            <!-- Contenedor con scroll interno exclusivo para la tabla -->
            <div class="anka-table-scroll-wrapper">
                <table class="anka-table">
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Razón Social</th>
                            <th>Nombre Comercial</th>
                            <th>RUC</th>
                            <th>Dirección</th>
                            <th>Provincia</th>
                            <th>Contacto</th>
                            <th>Cargo</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <!-- Fila clicable que redirige automáticamente al dashboard del cliente -->
                            
                            <!-- Columna de Acciones (Evita que el clic propague al formulario) -->
                            <td onclick="event.stopPropagation();">
                                <div class="anka-action-buttons">
                                    <!-- Botón Editar -->
                                    <a href="#" class="btn-editar">Editar Cliente</a>

                                    <!-- Botón Eliminar -->
                                    <form action="#" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-eliminar">Eliminar Cliente</button>
                                    </form>
                                </div>
                            </td>

                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->codigo_cliente }}</td>
                            <td>{{ $cliente->razon_social }}</td>
                            <td>{{ $cliente->nombre_comercial }}</td>
                            <td>{{ $cliente->ruc }}</td>
                            <td>{{ $cliente->direccion }}</td>
                            <td>{{ $cliente->provincia }}</td>
                            <td>{{ $cliente->contacto }}</td>
                            <td>{{ $cliente->cargo }}</td>
                            <td>{{ $cliente->correo }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td><span class="badge-estado">{{ $cliente->estado }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" style="text-align: center; color: var(--text-muted); padding: 2rem;">No se encontraron clientes registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <!-- Pie de página -->
    <footer class="anka-footer">
        &copy; 2026 Anka Fire - Todos los derechos reservados.
    </footer>

</body>
</html>