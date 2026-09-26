<?php

namespace App\Exports;

use App\Models\Equipo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected string $codigoCliente;

    public function __construct(string $codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;
    }

    public function collection()
    {
        return Equipo::where('codigo_cliente', $this->codigoCliente)
            ->orderBy('numero_interno')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Interno',
            'N° Serie',
            'Tipo Extintor',
            'Capacidad',
            'Marca',
            'Año Fabricación',
            'Próximo Mantenimiento',
            'Vencimiento PH',
            'Estado',
            'Último Servicio',
            'N° Certificado',
        ];
    }

    public function map($equipo): array
    {
        return [
            $equipo->numero_interno,
            $equipo->numero_serie,
            $equipo->tipo_extintor,
            $equipo->capacidad_carga,
            $equipo->marca,
            $equipo->anio_fabricacion,
            $equipo->proximo_mantenimiento,
            $equipo->vencimiento_ph ? $equipo->vencimiento_ph->format('d/m/Y') : '',
            $equipo->estado,
            $equipo->fecha_ultimo_servicio ? $equipo->fecha_ultimo_servicio->format('d/m/Y') : '',
            $equipo->numero_certificado,
        ];
    }
}