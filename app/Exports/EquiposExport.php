<?php

namespace App\Exports;

use App\Models\Cliente;
use App\Models\Equipo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected string $codigoCliente;
    protected string $nombreCliente;

    public function __construct(string $codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        $cliente = Cliente::where('codigo_cliente', $codigoCliente)->first();
        $this->nombreCliente = $cliente->razon_social
            ?? $cliente->nombre_comercial
            ?? $codigoCliente;
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
            ["Cliente: {$this->nombreCliente} ({$this->codigoCliente})"],
            [],
            [
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
            ],
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

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 16,
            'C' => 14,
            'D' => 12,
            'E' => 14,
            'F' => 16,
            'G' => 20,
            'H' => 16,
            'I' => 12,
            'J' => 16,
            'K' => 16,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Fila 1: nombre del cliente
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('DC2626');

        // Fila 3: encabezados de la tabla
        $sheet->getStyle('A3:K3')->getFont()->setBold(true);
        $sheet->getStyle('A3:K3')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A3:K3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DC2626');
        $sheet->getStyle('A3:K3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Filas de datos: bordes suaves y alineación centrada en algunas columnas
        $ultimaFila = 3 + $this->collection()->count();
        if ($ultimaFila > 3) {
            $sheet->getStyle("A4:K{$ultimaFila}")->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setRGB('E4E4E7');

            $sheet->getStyle("A4:A{$ultimaFila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F4:I{$ultimaFila}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(24);
            },
        ];
    }
}