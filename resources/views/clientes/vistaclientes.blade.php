<x-app-layout>
    {{-- Colocamos el enlace CSS directamente aquí para que la plantilla lo renderice --}}
    <link rel="stylesheet" href="{{ asset('css/cliente.css') }}">

    <div class="cliente-container">

        {{-- Encabezado --}}
        <div class="cliente-header">
            <div>
                <span class="badge-panel">Panel Administrativo</span>
                <h1>{{ $cliente->razon_social }}</h1>
                <p>
                    Código: <strong>{{ $cliente->codigo_cliente }}</strong> ·
                    RUC: <strong>{{ $cliente->ruc }}</strong> ·
                    {{ $cliente->provincia }}
                </p>
            </div>

            <div class="header-acciones" style="display: flex; gap: 10px; align-items: center;">
                {{-- Botón Exportar Excel (Solo Icono) --}}
                <a href="{{ route('clientes.exportar.excel', $cliente->codigo_cliente) }}" class="btn-icono" title="Exportar a Excel">
                    <img src="{{ asset('img/icono-excel.png') }}" alt="Excel" style="width: 28px; height: 28px;">
                </a>

                {{-- Botón Exportar PDF (Solo Icono) --}}
                <a href="{{ route('clientes.exportar.pdf', $cliente->codigo_cliente) }}" class="btn-icono" title="Exportar a PDF">
                    <img src="{{ asset('img/icono-pdf.png') }}" alt="PDF" style="width: 28px; height: 28px;">
                </a>

                {{-- Botón Agregar extintor --}}
                <a href="{{ route('equipos.create', $cliente->codigo_cliente) }}" class="btn-agregar">
                    + Agregar extintor
                </a>
            </div>
        </div>

        @if (session('mensaje'))
            <div class="alert-mensaje">
                {{ session('mensaje') }}
            </div>
        @endif

        {{-- Tarjetas resumen --}}
        <div class="resumen-grid">
            <div class="card-resumen border-black">
                <p>Total de equipos</p>
                <h3>{{ $resumen['total'] }}</h3>
            </div>
            <div class="card-resumen border-green">
                <p>Vigentes</p>
                <h3 class="text-green">{{ $resumen['vigentes'] }}</h3>
            </div>
            <div class="card-resumen border-orange">
                <p>Por vencer (60 días)</p>
                <h3 class="text-orange">{{ $resumen['por_vencer'] }}</h3>
            </div>
            <div class="card-resumen border-red">
                <p>Vencidos</p>
                <h3 class="text-red">{{ $resumen['vencidos'] }}</h3>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="filtros-form">
            <select name="sede" onchange="this.form.submit()">
                <option value="">Todas las sedes</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->codigo_sede }}" @selected(request('sede') == $sede->codigo_sede)>
                        {{ $sede->nombre_sede }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por N° serie o N° interno">

            <button type="submit" class="btn-buscar">Filtrar</button>
        </form>

        {{-- Tabla de extintores --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>N° Serie</th>
                        <th>N° Interno</th>
                        <th>Sede</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Marca</th>
                        <th>Año Fabricación</th>
                        <th>Fecha Últ. Servicio</th>
                        <th>Próx. Mantenimiento</th>
                        <th>Fecha Prueba PH</th>
                        <th>Venc. Prueba PH</th>
                        <th>Estado</th>
                        <th>Certificados</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Convierte una fecha (Carbon/date) al formato "MES-AÑO" en mayúsculas, ej. "JUL-2027"
                        $mesAnio = fn($fecha) => $fecha ? strtoupper($fecha->locale('es')->isoFormat('MMM-YYYY')) : 'S/N';
                    @endphp
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td><strong>{{ $equipo->numero_serie }}</strong></td>
                            <td>{{ $equipo->numero_interno ?? 'S/N' }}</td>
                            <td>{{ $equipo->sede->nombre_sede ?? 'S/N' }}</td>
                            <td>{{ $equipo->tipo_extintor }}</td>
                            <td>{{ $equipo->capacidad_carga }}</td>
                            <td>{{ $equipo->marca }}</td>
                            <td>{{ $equipo->anio_fabricacion ?? 'S/N' }}</td>
                            <td>{{ $mesAnio($equipo->fecha_ultimo_servicio) }}</td>
                            <td>{{ $equipo->proximo_mantenimiento ?? 'S/N' }}</td>
                            <td>{{ $equipo->fecha_prueba_hidrostatica ?? 'S/N' }}</td>
                            <td>{{ $mesAnio($equipo->vencimiento_ph) }}</td>
                            <td>
                                @php
                                    $colores = [
                                        'vigente' => 'badge-vigente',
                                        'por_vencer' => 'badge-por-vencer',
                                        'vencido' => 'badge-vencido',
                                        'sin_dato' => 'badge-sin-dato',
                                    ];
                                    $etiquetas = [
                                        'vigente' => 'Vigente',
                                        'por_vencer' => 'Por vencer',
                                        'vencido' => 'Vencido',
                                        'sin_dato' => 'Sin dato',
                                    ];
                                    $estado = $equipo->estado_visual;
                                @endphp
                                <span class="badge {{ $colores[$estado] ?? 'badge-sin-dato' }}">
                                    {{ $etiquetas[$estado] ?? 'Sin dato' }}
                                </span>
                            </td>
                            <td>
                                <div class="certificados-links">
                                    @php
                                        $enlace = fn($ruta) => str_starts_with($ruta, 'http') ? $ruta : Storage::url($ruta);
                                    @endphp
                                    @if ($equipo->ruta_cert_operatividad)
                                        <a href="{{ $enlace($equipo->ruta_cert_operatividad) }}" target="_blank">Operatividad</a>
                                    @endif
                                    @if ($equipo->ruta_informe_tecnico)
                                        <a href="{{ $enlace($equipo->ruta_informe_tecnico) }}" target="_blank">Informe</a>
                                    @endif
                                    @if ($equipo->ruta_cert_ph)
                                        <a href="{{ $enlace($equipo->ruta_cert_ph) }}" target="_blank">PH</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center">No se encontraron extintores con estos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Barra de navegación (paginación), 15 registros por página --}}
        <div class="paginacion-wrap">
            {{ $equipos->links() }}
        </div>

    </div>
</x-app-layout>
