<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use Carbon\Carbon;
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
            'fecha_fabricacion' => 'nullable|date_format:Y-m', // viene del <input type="month">
            'estado' => 'nullable|string|max:255',
            'fecha_ultimo_servicio' => 'nullable|date',
            'mes_prueba_hidrostatica' => 'nullable|date_format:Y-m',
            'numero_certificado' => 'nullable|string|max:255',
            'cert_operatividad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'informe_tecnico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'cert_ph' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Usamos el mes/año de fabricación (si lo indicaron) como base para calcular
        // el próximo mantenimiento y el vencimiento de PH. Si no lo indicaron, se usa hoy.
        $fechaBase = $datos['fecha_fabricacion']
            ? Carbon::createFromFormat('Y-m', $datos['fecha_fabricacion'])->startOfMonth()
            : now();

        $datos['anio_fabricacion'] = $fechaBase->year;
        $datos['vencimiento_ph'] = $fechaBase->copy()->addYears(5)->toDateString();
        $datos['proximo_mantenimiento'] = strtoupper(
            $fechaBase->copy()->addYear()->locale('es')->isoFormat('MMM-YYYY')
        );

        // Fecha de prueba hidrostática, en formato MES-AÑO
        $datos['fecha_prueba_hidrostatica'] = !empty($datos['mes_prueba_hidrostatica'])
            ? strtoupper(Carbon::createFromFormat('Y-m', $datos['mes_prueba_hidrostatica'])->locale('es')->isoFormat('MMM-YYYY'))
            : 'S/N';

        unset($datos['fecha_fabricacion'], $datos['mes_prueba_hidrostatica']);

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
