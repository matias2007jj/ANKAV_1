<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function create(string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();
        $sedes = $cliente->sedes;

        return view('equipos.create', compact('cliente', 'sedes'));
    }

    public function store(Request $request, string $codigo)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo)->firstOrFail();

        $datos = $request->validate([
            'codigo_sede' => 'nullable|string|exists:sedes,codigo_sede',
            'numero_serie' => 'nullable|string|max:255',
            'numero_interno' => 'nullable|string|max:255',
            'tipo_extintor' => 'required|string|max:255',
            'capacidad_carga' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'anio_fabricacion' => 'nullable|integer|min:1980|max:' . date('Y'),
            'proximo_mantenimiento' => 'nullable|string|max:255',
            'proximo_mantenimiento_real' => 'nullable|date',
            'vencimiento_ph' => 'nullable|date',
            'estado' => 'nullable|string|max:255',
            'fecha_ultimo_servicio' => 'nullable|date',
            'numero_certificado' => 'nullable|string|max:255',
            'cert_operatividad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'informe_tecnico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'cert_ph' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $datos['codigo_cliente'] = $cliente->codigo_cliente;

        // Guardamos cada archivo en el disco local del hosting y solo la ruta en la BD
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

        // Quitamos las claves de los archivos crudos antes de guardar el modelo
        unset($datos['cert_operatividad'], $datos['informe_tecnico'], $datos['cert_ph']);

        Equipo::create($datos);

        return redirect()
            ->route('clientes.dashboard', $cliente->codigo_cliente)
            ->with('mensaje', 'Extintor agregado correctamente.');
    }
}
