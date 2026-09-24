<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Encabezado --}}
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $cliente->razon_social }}</h1>
                <p class="text-sm text-gray-500">
                    Código: {{ $cliente->codigo_cliente }} · RUC: {{ $cliente->ruc }} · {{ $cliente->provincia }}
                </p>
            </div>
            <a href="{{ route('equipos.create', $cliente->codigo_cliente) }}"
               class="bg-gray-800 text-white text-sm px-4 py-2 rounded whitespace-nowrap">
                + Agregar extintor
            </a>
        </div>

        @if (session('mensaje'))
            <div class="bg-green-50 text-green-700 text-sm p-3 rounded mb-4">
                {{ session('mensaje') }}
            </div>
        @endif

        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Total de extintores</p>
                <p class="text-3xl font-bold text-gray-800">{{ $resumen['total'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4">
                <p class="text-sm text-green-700">Vigentes</p>
                <p class="text-3xl font-bold text-green-700">{{ $resumen['vigentes'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4">
                <p class="text-sm text-yellow-700">Por vencer (60 días)</p>
                <p class="text-3xl font-bold text-yellow-700">{{ $resumen['por_vencer'] }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4">
                <p class="text-sm text-red-700">Vencidos</p>
                <p class="text-3xl font-bold text-red-700">{{ $resumen['vencidos'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex flex-wrap gap-3 mb-4">
            <select name="sede" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">Todas las sedes</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->codigo_sede }}" @selected(request('sede') == $sede->codigo_sede)>
                        {{ $sede->nombre_sede }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Buscar por N° serie o N° interno"
                   class="border rounded px-3 py-2 text-sm flex-1 min-w-[200px]">

            <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Buscar</button>
        </form>

        {{-- Tabla de extintores --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 text-left">
                    <tr>
                        <th class="px-4 py-3">N° Serie</th>
                        <th class="px-4 py-3">Sede</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Capacidad</th>
                        <th class="px-4 py-3">Marca</th>
                        <th class="px-4 py-3">Vencimiento PH</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Certificados</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td class="px-4 py-3">{{ $equipo->numero_serie }}</td>
                            <td class="px-4 py-3">{{ $equipo->sede->nombre_sede ?? 'S/N' }}</td>
                            <td class="px-4 py-3">{{ $equipo->tipo_extintor }}</td>
                            <td class="px-4 py-3">{{ $equipo->capacidad_carga }}</td>
                            <td class="px-4 py-3">{{ $equipo->marca }}</td>
                            <td class="px-4 py-3">
                                {{ $equipo->vencimiento_ph?->format('d/m/Y') ?? 'S/N' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $colores = [
                                        'vigente' => 'bg-green-100 text-green-700',
                                        'por_vencer' => 'bg-yellow-100 text-yellow-700',
                                        'vencido' => 'bg-red-100 text-red-700',
                                        'sin_dato' => 'bg-gray-100 text-gray-500',
                                    ];
                                    $etiquetas = [
                                        'vigente' => 'Vigente',
                                        'por_vencer' => 'Por vencer',
                                        'vencido' => 'Vencido',
                                        'sin_dato' => 'Sin dato',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colores[$equipo->estado_visual] }}">
                                    {{ $etiquetas[$equipo->estado_visual] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 space-x-2">
                                @php
                                    $enlace = fn($ruta) => str_starts_with($ruta, 'http') ? $ruta : Storage::url($ruta);
                                @endphp
                                @if ($equipo->ruta_cert_operatividad)
                                    <a href="{{ $enlace($equipo->ruta_cert_operatividad) }}" target="_blank" class="text-blue-600 underline text-xs">Operatividad</a>
                                @endif
                                @if ($equipo->ruta_informe_tecnico)
                                    <a href="{{ $enlace($equipo->ruta_informe_tecnico) }}" target="_blank" class="text-blue-600 underline text-xs">Informe</a>
                                @endif
                                @if ($equipo->ruta_cert_ph)
                                    <a href="{{ $enlace($equipo->ruta_cert_ph) }}" target="_blank" class="text-blue-600 underline text-xs">PH</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                                No se encontraron extintores con estos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>