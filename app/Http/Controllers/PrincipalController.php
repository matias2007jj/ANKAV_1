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

        $calcularEstado = function ($cliente) {
            if (strtoupper($cliente->estado) === 'VIGENTE') {
                $cliente->estado_visual = 'vigente';
            } else {
                $cliente->estado_visual = 'suspendido';
            }
        };

        // El resumen (contador de total/vigentes/otros) se calcula sobre TODOS
        // los resultados filtrados, no solo los de la página actual.
        $todos = (clone $query)->get();
        $todos->each($calcularEstado);

        $resumen = [
            'total' => $todos->count(),
            'vigentes' => $todos->where('estado_visual', 'vigente')->count(),
            'otros' => $todos->where('estado_visual', '!=', 'vigente')->count(),
        ];

        // La tabla sí se pagina: 15 registros por página
        $clientes = $query->orderBy('id')->paginate(15)->withQueryString();
        $clientes->getCollection()->each($calcularEstado);

        $provincias = Cliente::select('provincia')->distinct()->pluck('provincia');

        return view('clientes.principal', compact('clientes', 'resumen', 'provincias'));
    }
}
