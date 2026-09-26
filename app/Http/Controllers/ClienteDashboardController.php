<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\EquiposExport;
use Maatwebsite\Excel\Facades\Excel;

class ClienteDashboardController extends Controller
{
    public function show(Request $request, ?string $codigo = null)
    {
        return $this->procesarDashboard($request, $codigo);
    }

    public function vistaPrueba(Request $request, ?string $codigo = null)
    {
        return $this->procesarDashboard($request, $codigo);
    }

    private function procesarDashboard(Request $request, ?string $codigo)
    {
        $user = $request->user();

        // Si no pasan el código por URL pero el usuario logueado tiene un código asignado, úsalo
        if (!$codigo && $user && isset($user->codigo_cliente) && $user->codigo_cliente) {
            $codigo = $user->codigo_cliente;
        }

        if (!$codigo) {
            abort(404, 'No se especificó un código de cliente.');
        }

        // Un cliente solo puede ver su propia empresa, sin importar qué código ponga en la URL
        if ($user && $user->role === 'cliente' && $user->codigo_cliente !== $codigo) {
            abort(403, 'No tienes permiso para ver este cliente.');
        }

        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        $query = $cliente->equipos()->with('sede');

        // Filtro opcional por sede
        if ($request->filled('sede')) {
            $query->where('codigo_sede', $request->input('sede'));
        }

        // Búsqueda opcional por N° de serie o N° interno
        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('numero_serie', 'like', "%{$texto}%")
                  ->orWhere('numero_interno', 'like', "%{$texto}%");
            });
        }

        $equipos = $query->orderBy('vencimiento_ph')->get();

        $hoy = Carbon::today();
        $limiteProximo = $hoy->copy()->addDays(60);

        // Clasificamos cada equipo para pintarlo en la vista
        $equipos->each(function ($equipo) use ($hoy, $limiteProximo) {
            $vencimiento = $equipo->vencimiento_ph ? Carbon::parse($equipo->vencimiento_ph) : null;

            if (!$vencimiento) {
                $equipo->estado_visual = 'sin_dato';
            } elseif ($vencimiento->lt($hoy)) {
                $equipo->estado_visual = 'vencido';
            } elseif ($vencimiento->lte($limiteProximo)) {
                $equipo->estado_visual = 'por_vencer';
            } else {
                $equipo->estado_visual = 'vigente';
            }
        });

        $resumen = [
            'total' => $equipos->count(),
            'vigentes' => $equipos->where('estado_visual', 'vigente')->count(),
            'por_vencer' => $equipos->where('estado_visual', 'por_vencer')->count(),
            'vencidos' => $equipos->where('estado_visual', 'vencido')->count(),
        ];

        $sedes = $cliente->sedes;

        return view('clientes.vistaclientes', compact('cliente', 'equipos', 'resumen', 'sedes'));
    }

    public function exportarExcel(string $codigo)
{
    return Excel::download(new EquiposExport($codigo), "equipos_{$codigo}.xlsx");
}
}