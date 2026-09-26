<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Equipos - {{ $cliente->codigo_cliente }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #18181b; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.subtitulo { margin-top: 0; color: #52525b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #d4d4d8; padding: 6px 8px; text-align: left; }
        th { background-color: #dc2626; color: #ffffff; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #f4f4f5; }
    </style>
</head>
<body>
    <h1>Reporte de Equipos</h1>
   <p class="subtitulo">Cliente: {{ $cliente->razon_social ?? $cliente->nombre_comercial }} ({{ $cliente->codigo_cliente }})</p>
    <table>
        <thead>
            <tr>
                <th>N° Interno</th>
                <th>N° Serie</th>
                <th>Tipo</th>
                <th>Capacidad</th>
                <th>Marca</th>
                <th>Próx. Mantenimiento</th>
                <th>Vencimiento PH</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipos as $equipo)
                <tr>
                    <td>{{ $equipo->numero_interno }}</td>
                    <td>{{ $equipo->numero_serie }}</td>
                    <td>{{ $equipo->tipo_extintor }}</td>
                    <td>{{ $equipo->capacidad_carga }}</td>
                    <td>{{ $equipo->marca }}</td>
                    <td>{{ $equipo->proximo_mantenimiento }}</td>
                    <td>{{ $equipo->vencimiento_ph ? $equipo->vencimiento_ph->format('d/m/Y') : '' }}</td>
                    <td>{{ $equipo->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Sin equipos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>