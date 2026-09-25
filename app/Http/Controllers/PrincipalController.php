<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrincipalController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Filtro opcional por provincia
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->input('provincia'));
        }

        // Búsqueda opcional por Razón Social, RUC o Código de Cliente
        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('razon_social', 'like', "%{$texto}%")
                  ->orWhere('ruc', 'like', "%{$texto}%")
                  ->orWhere('codigo_cliente', 'like', "%{$texto}%")
                  ->orWhere('nombre_comercial', 'like', "%{$texto}%");
            });
        }

        $clientes = $query->orderBy('id')->get();

        $clientes->each(function ($cliente) {
            if (strtoupper($cliente->estado) === 'VIGENTE') {
                $cliente->estado_visual = 'vigente';
            } else {
                $cliente->estado_visual = 'suspendido';
            }
        });

        $resumen = [
            'total' => $clientes->count(),
            'vigentes' => $clientes->where('estado_visual', 'vigente')->count(),
            'otros' => $clientes->where('estado_visual', '!=', 'vigente')->count(),
        ];

        $provincias = Cliente::select('provincia')->distinct()->pluck('provincia');

        return view('clientes.principal', compact('clientes', 'resumen', 'provincias'));
    }
}