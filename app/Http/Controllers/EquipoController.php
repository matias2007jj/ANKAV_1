<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function create(string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();
        $sedes = $cliente->sedes;

        $tipos = DB::table('tipos_extintor')->orderBy('nombre')->pluck('nombre');
        $marcas = DB::table('marcas')->orderBy('nombre')->pluck('nombre');
        $capacidades = DB::table('capacidades')->orderBy('valor')->pluck('valor');

        return view('equipos.create', compact('cliente', 'sedes', 'tipos', 'marcas', 'capacidades'));
    }

    public function store(Request $request, string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        $datos = $request->validate([
            'codigo_sede' => 'nullable|string|exists:sedes,codigo_sede',
            'numero_serie' => 'nullable|string|max:255',
            'numero_interno' => 'nullable|string|max:255',
            'tipo_extintor' => 'required|string|exists:tipos_extintor,nombre',
            'capacidad_carga' => 'nullable|string|exists:capacidades,valor',
            'marca' => 'nullable|string|exists:marcas,nombre',
            'anio_fabricacion' => 'nullable|integer|min:1980|max:' . date('Y'),
            'estado' => 'nullable|string|max:255',
            'fecha_ultimo_servicio' => 'nullable|date',
            'numero_certificado' => 'nullable|string|max:255',
            'cert_operatividad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'informe_tecnico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'cert_ph' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        // Nota: vencimiento_ph y proximo_mantenimiento NO se piden en el formulario,
        // el modelo Equipo los calcula automáticamente al crearse.

        $datos['codigo_cliente'] = $cliente->codigo_cliente;

        foreach ([
            'cert_operatividad' => 'ruta_cert_operatividad',
            'informe_tecnico' => 'ruta_informe_tecnico',
            'cert_ph' => 'ruta_cert_ph',
        ] as $campoArchivo => $campoRuta) {
            if ($request->hasFile($campoArchivo)) {
                $datos[$campoRuta] = $request->file($campoArchivo)
                    ->store('certificados', 'public');
            }
        }

        unset($datos['cert_operatividad'], $datos['informe_tecnico'], $datos['cert_ph']);

        Equipo::create($datos);

        return redirect()
            ->route('clientes.dashboard', $cliente->codigo_cliente)
            ->with('mensaje', 'Extintor agregado correctamente.');
    }
}
