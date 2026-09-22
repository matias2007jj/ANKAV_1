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
                    <input type="text" name="tipo_extintor" value="{{ old('tipo_extintor') }}" required
                           placeholder="Ej. PQS, CO2, Agua"
                           class="w-full border rounded px-3 py-2 text-sm">
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
                    <input type="text" name="capacidad_carga" value="{{ old('capacidad_carga') }}"
                           placeholder="Ej. 06 KG"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <input type="text" name="marca" value="{{ old('marca') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año de fabricación</label>
                    <input type="number" name="anio_fabricacion" value="{{ old('anio_fabricacion') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vencimiento PH</label>
                    <input type="date" name="vencimiento_ph" value="{{ old('vencimiento_ph') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha último servicio</label>
                    <input type="date" name="fecha_ultimo_servicio" value="{{ old('fecha_ultimo_servicio') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Próximo mantenimiento (real)</label>
                    <input type="date" name="proximo_mantenimiento_real" value="{{ old('proximo_mantenimiento_real') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° de certificado</label>
                    <input type="text" name="numero_certificado" value="{{ old('numero_certificado') }}"
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>

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
