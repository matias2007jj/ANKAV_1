<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-1">
            {{ isset($equipo) ? 'Editar extintor' : 'Agregar extintor' }}
        </h1>
        <p class="text-sm text-gray-500 mb-6">Cliente: {{ $cliente->razon_social }} ({{ $cliente->codigo_cliente }})</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 text-sm p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" 
              action="{{ isset($equipo) ? route('equipos.update', [$cliente->codigo_cliente, $equipo->id]) : route('equipos.store', $cliente->codigo_cliente) }}" 
              enctype="multipart/form-data" 
              class="bg-white rounded-lg shadow p-6 space-y-4">
            
            @csrf
            @if(isset($equipo))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Sede --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                    <select name="codigo_sede" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Sin sede específica --</option>
                        @foreach ($sedes as $sede)
                            <option value="{{ $sede->codigo_sede }}" 
                                @selected(old('codigo_sede', $equipo->codigo_sede ?? '') == $sede->codigo_sede)>
                                {{ $sede->nombre_sede }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de Extintor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de extintor *</label>
                    <select name="tipo_extintor" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}" 
                                @selected(trim(old('tipo_extintor', $equipo->tipo_extintor ?? '')) == trim($tipo))>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- N° de Serie --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de serie</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie', $equipo->numero_serie ?? '') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- N° Interno --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° interno</label>
                    <input type="text" name="numero_interno" value="{{ old('numero_interno', $equipo->numero_interno ?? '') }}"
                           placeholder="S/N si no aplica"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- Capacidad de carga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad de carga</label>
                    <select name="capacidad_carga" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($capacidades as $capacidad)
                            <option value="{{ $capacidad }}" 
                                @selected(trim(old('capacidad_carga', $equipo->capacidad_carga ?? '')) == trim($capacidad))>
                                {{ $capacidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Marca --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <select name="marca" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca }}" 
                                @selected(trim(old('marca', $equipo->marca ?? '')) == trim($marca))>
                                {{ $marca }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mes y año de fabricación --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes y año de fabricación</label>
                    @php
                        $fechaFab = '';
                        if (isset($equipo)) {
                            if (!empty($equipo->fecha_fabricacion)) {
                                $fechaFab = \Carbon\Carbon::parse($equipo->fecha_fabricacion)->format('Y-m');
                            } elseif (!empty($equipo->anio_fabricacion)) {
                                $fechaFab = $equipo->anio_fabricacion . '-01';
                            }
                        }
                    @endphp
                    <input type="month" name="fecha_fabricacion" 
                           value="{{ old('fecha_fabricacion', $fechaFab) }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">El próximo mantenimiento (mismo mes, +1 año) y el vencimiento de PH (+5 años) se calculan a partir de esta fecha.</p>
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2 text-sm">
                        @foreach (['Vigente', 'Vencido', 'De baja'] as $est)
                            <option value="{{ $est }}" 
                                @selected(trim(old('estado', $equipo->estado ?? '')) == trim($est))>
                                {{ $est }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha último servicio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha último servicio</label>
                    <input type="date" name="fecha_ultimo_servicio" 
                           value="{{ old('fecha_ultimo_servicio', isset($equipo->fecha_ultimo_servicio) ? \Carbon\Carbon::parse($equipo->fecha_ultimo_servicio)->format('Y-m-d') : '') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- Mes y año de prueba hidrostática --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes y año de prueba hidrostática</label>
                    <input type="month" name="mes_prueba_hidrostatica" 
                           value="{{ old('mes_prueba_hidrostatica', isset($equipo->fecha_prueba_hidrostatica) ? \Carbon\Carbon::parse($equipo->fecha_prueba_hidrostatica)->format('Y-m') : '') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                {{-- N° de certificado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de certificado</label>
                    <input type="text" name="numero_certificado" value="{{ old('numero_certificado', $equipo->numero_certificado ?? '') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

            <p class="text-xs text-gray-500 -mt-2">
                Si no indicas mes/año de fabricación, el vencimiento de PH y el próximo mantenimiento se calculan desde hoy.
            </p>

            <hr class="my-4">

            <p class="text-sm font-medium text-gray-700">Documentos (PDF o imagen, máx. 10MB c/u)</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Certificado de operatividad</label>
                    <input type="file" name="cert_operatividad" class="w-full text-sm">
                    @if(!empty($equipo->ruta_cert_operatividad))
                        <p class="text-xs text-green-600 font-medium mt-1">✓ Documento subido actualmente</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Informe técnico</label>
                    <input type="file" name="informe_tecnico" class="w-full text-sm">
                    @if(!empty($equipo->ruta_informe_tecnico))
                        <p class="text-xs text-green-600 font-medium mt-1">✓ Documento subido actualmente</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Certificado de PH</label>
                    <input type="file" name="cert_ph" class="w-full text-sm">
                    @if(!empty($equipo->ruta_cert_ph))
                        <p class="text-xs text-green-600 font-medium mt-1">✓ Documento subido actualmente</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('clientes.dashboard', $cliente->codigo_cliente) }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                <button type="submit" class="bg-gray-800 text-white text-sm px-5 py-2 rounded">
                    {{ isset($equipo) ? 'Actualizar extintor' : 'Guardar extintor' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>