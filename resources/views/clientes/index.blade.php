<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Clientes</h1>

        <form method="GET" class="mb-4">
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Buscar por nombre, código o RUC"
                   class="border rounded px-3 py-2 text-sm w-full md:w-96">
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 text-left">
                    <tr>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Razón social</th>
                        <th class="px-4 py-3">RUC</th>
                        <th class="px-4 py-3">Provincia</th>
                        <th class="px-4 py-3">Extintores</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td class="px-4 py-3">{{ $cliente->codigo_cliente }}</td>
                            <td class="px-4 py-3">{{ $cliente->razon_social }}</td>
                            <td class="px-4 py-3">{{ $cliente->ruc }}</td>
                            <td class="px-4 py-3">{{ $cliente->provincia }}</td>
                            <td class="px-4 py-3">{{ $cliente->equipos_count }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('clientes.dashboard', $cliente->codigo_cliente) }}"
                                   class="text-blue-600 underline text-xs">Ver dashboard</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                No se encontraron clientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $clientes->links() }}
        </div>
    </div>
</x-app-layout>
