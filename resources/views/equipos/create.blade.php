<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Agregar extintor</h1>
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

        <form method="POST" action="{{ route('equipos.store', $cliente->codigo_cliente) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                    <select name="codigo_sede" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Sin sede específica --</option>
                        @foreach ($sedes as $sede)
                            <option value="{{ $sede->codigo_sede }}" @selected(old('codigo_sede') == $sede->codigo_sede)>
                                {{ $sede->nombre_sede }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de extintor *</label>
                    <select name="tipo_extintor" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_extintor') == $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de serie</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° interno</label>
                    <input type="text" name="numero_interno" value="{{ old('numero_interno') }}"
                           placeholder="S/N si no aplica"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidad de carga</label>
                    <select name="capacidad_carga" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($capacidades as $capacidad)
                            <option value="{{ $capacidad }}" @selected(old('capacidad_carga') == $capacidad)>{{ $capacidad }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <select name="marca" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca }}" @selected(old('marca') == $marca)>{{ $marca }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes y año de fabricación</label>
                    <input type="month" name="fecha_fabricacion" value="{{ old('fecha_fabricacion') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">El próximo mantenimiento (mismo mes, +1 año) y el vencimiento de PH (+5 años) se calculan a partir de esta fecha.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="Vigente">Vigente</option>
                        <option value="Vencido">Vencido</option>
                        <option value="De baja">De baja</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha último servicio</label>
                    <input type="date" name="fecha_ultimo_servicio" value="{{ old('fecha_ultimo_servicio') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes y año de prueba hidrostática</label>
                    <input type="month" name="mes_prueba_hidrostatica" value="{{ old('mes_prueba_hidrostatica') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de certificado</label>
                    <input type="text" name="numero_certificado" value="{{ old('numero_certificado') }}"
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
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Informe técnico</label>
                    <input type="file" name="informe_tecnico" class="w-full text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Certificado de PH</label>
                    <input type="file" name="cert_ph" class="w-full text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('clientes.dashboard', $cliente->codigo_cliente) }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                <button type="submit" class="bg-gray-800 text-white text-sm px-5 py-2 rounded">Guardar extintor</button>
            </div>
        </form>
    </div>
</x-app-layout>
