<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    // Mantenemos como 'date' solo los campos de fecha estricta
    protected $casts = [
        'vencimiento_ph' => 'date',
        'fecha_ultimo_servicio' => 'date',
    ];

    /**
     * Accesor para fecha_prueba_hidrostatica.
     * Devuelve el mes abreviado y año en mayúsculas (ej: "SEP.-2026")
     * o "S/N" si está vacía o es inválida.
     */
    protected function fechaPruebaHidrostatica(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value) || $value === 'S/N') {
                    return 'S/N';
                }

                try {
                    return strtoupper(
                        Carbon::parse($value)->locale('es')->isoFormat('MMM-YYYY')
                    );
                } catch (\Exception $e) {
                    return 'S/N';
                }
            }
        );
    }

    protected static function boot()
    {
        parent::boot();

        // 'saving' se ejecuta tanto al CREAR como al ACTUALIZAR
        static::saving(function ($equipo) {
            $fechaBase = now();

            // Vencimiento del PH: cada 5 años
            if (!$equipo->vencimiento_ph) {
                $equipo->vencimiento_ph = $fechaBase->copy()->addYears(5)->toDateString();
            }

            // Próximo mantenimiento: mismo mes, un año después
            if (!$equipo->proximo_mantenimiento) {
                $equipo->proximo_mantenimiento = strtoupper(
                    $fechaBase->copy()->addYear()->locale('es')->isoFormat('MMM.-YYYY')
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