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
        'vencimiento_ph',
        'fecha_prueba_hidrostatica',
        'estado',
        'fecha_ultimo_servicio',
        'numero_certificado',
        'ruta_cert_operatividad',
        'ruta_informe_tecnico',
        'ruta_cert_ph',
    ];

    protected $casts = [
        'vencimiento_ph' => 'date',
        'fecha_ultimo_servicio' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Al crear un extintor, calculamos automáticamente sus fechas
        // si es que no vinieron ya definidas manualmente.
        static::creating(function ($equipo) {
            $fechaBase = now();

            // Vencimiento del PH: cada 5 años desde la fecha de creación
            if (!$equipo->vencimiento_ph) {
                $equipo->vencimiento_ph = $fechaBase->copy()->addYears(5)->toDateString();
            }

            // Próximo mantenimiento: mismo mes, un año después (ej. "ABR-2027")
            if (!$equipo->proximo_mantenimiento) {
                $equipo->proximo_mantenimiento = strtoupper(
                    $fechaBase->copy()->addYear()->locale('es')->isoFormat('MMM-YYYY')
                );
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_cliente', 'codigo_cliente');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'codigo_sede', 'codigo_sede');
    }
}
