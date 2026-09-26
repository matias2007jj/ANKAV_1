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
    public function create($codigo_cliente)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo_cliente)->firstOrFail();
        $sedes = $cliente->sedes;

        // Consultamos directo desde tus tablas reales
        $tipos = DB::table('tipos_extintor')->pluck('nombre');
        $capacidades = DB::table('capacidades')->pluck('valor');
        $marcas = DB::table('marcas')->pluck('nombre');

        return view('equipos.create', compact('cliente', 'sedes', 'tipos', 'capacidades', 'marcas'));
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

        foreach (
            [
                'cert_operatividad' => 'ruta_cert_operatividad',
                'informe_tecnico' => 'ruta_informe_tecnico',
                'cert_ph' => 'ruta_cert_ph',
            ] as $campoArchivo => $campoRuta
        ) {
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
    // Cargar la vista con los datos del extintor
    public function edit($codigo_cliente, $id)
    {
        $cliente = Cliente::where('codigo_cliente', $codigo_cliente)->firstOrFail();
        $equipo = Equipo::findOrFail($id);
        $sedes = $cliente->sedes;

        // Consultamos directo desde tus tablas reales
        $tipos = DB::table('tipos_extintor')->pluck('nombre');
        $capacidades = DB::table('capacidades')->pluck('valor');
        $marcas = DB::table('marcas')->pluck('nombre');

        return view('equipos.create', compact('cliente', 'equipo', 'sedes', 'tipos', 'capacidades', 'marcas'));
    }

    // Guardar la actualización en la base de datos
    public function update(Request $request, $codigo_cliente, $id)
    {
        $equipo = Equipo::findOrFail($id);

        // 1. Extraer los datos del formulario excepto los inputs de archivo
        $data = $request->except(['cert_operatividad', 'informe_tecnico', 'cert_ph']);

        // 2. Procesar Certificado de Operatividad
        if ($request->hasFile('cert_operatividad')) {
            if ($equipo->ruta_cert_operatividad) {
                Storage::disk('public')->delete($equipo->ruta_cert_operatividad);
            }
            $data['ruta_cert_operatividad'] = $request->file('cert_operatividad')->store('certificados', 'public');
        }

        // 3. Procesar Informe Técnico
        if ($request->hasFile('informe_tecnico')) {
            if ($equipo->ruta_informe_tecnico) {
                Storage::disk('public')->delete($equipo->ruta_informe_tecnico);
            }
            $data['ruta_informe_tecnico'] = $request->file('informe_tecnico')->store('certificados', 'public');
        }

        // 4. Procesar Certificado de PH
        if ($request->hasFile('cert_ph')) {
            if ($equipo->ruta_cert_ph) {
                Storage::disk('public')->delete($equipo->ruta_cert_ph);
            }
            $data['ruta_cert_ph'] = $request->file('cert_ph')->store('certificados', 'public');
        }

        // 5. Mapeo de fecha si envías "fecha_fabricacion" o "mes_prueba_hidrostatica" desde la vista
        if ($request->filled('mes_prueba_hidrostatica')) {
            $data['fecha_prueba_hidrostatica'] = $request->mes_prueba_hidrostatica . '-01';
        }

        // 6. Actualizar modelo
        $equipo->update($data);

        return redirect()->route('clientes.dashboard', $codigo_cliente)
            ->with('mensaje', 'Extintor y documentos actualizados con éxito.');
    }
    
}
