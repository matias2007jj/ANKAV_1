<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClienteDashboardController extends Controller
{
    public function show(string $codigo, Request $request)
    {
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

        return view('clientes.dashboard', compact('cliente', 'equipos', 'resumen', 'sedes'));
    }
}
