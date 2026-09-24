<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Un cliente no ve la lista completa, va directo a su propio dashboard
        if ($request->user()->role === 'cliente') {
            return redirect()->route('clientes.dashboard', $request->user()->codigo_cliente);
        }

        $query = Cliente::withCount('equipos');

        if ($request->filled('buscar')) {
            $texto = $request->input('buscar');
            $query->where(function ($q) use ($texto) {
                $q->where('razon_social', 'like', "%{$texto}%")
                  ->orWhere('nombre_comercial', 'like', "%{$texto}%")
                  ->orWhere('codigo_cliente', 'like', "%{$texto}%")
                  ->orWhere('ruc', 'like', "%{$texto}%");
            });
        }

        $clientes = $query->orderBy('razon_social')->paginate(15)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }
}
