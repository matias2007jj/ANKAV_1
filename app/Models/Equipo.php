<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = [
        'codigo_cliente',
        'codigo_sede',
        'numero_serie',
        'numero_interno',
        'tipo_extintor',
        'capacidad_carga',
        'marca',
        'anio_fabricacion',
        'proximo_mantenimiento',
        'proximo_mantenimiento_real',
        'vencimiento_ph',
        'estado',
        'fecha_ultimo_servicio',
        'numero_certificado',
        'ruta_cert_operatividad',
        'ruta_informe_tecnico',
        'ruta_cert_ph',
    ];

    protected $casts = [
        'proximo_mantenimiento_real' => 'date',
        'vencimiento_ph' => 'date',
        'fecha_ultimo_servicio' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_cliente', 'codigo_cliente');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'codigo_sede', 'codigo_sede');
    }
}