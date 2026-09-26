<?php

namespace App\Exports;

use App\Models\Equipo;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EquiposExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected string $codigoCliente;

    // Colores de marca / estado — ajusta los HEX a los que uses en el resto del sistema
    private const COLOR_ENCABEZADO = 'DC3545';
    private const COLOR_VIGENTE    = 'C6EFCE';
    private const COLOR_POR_VENCER = 'FD7E14';
    private const COLOR_VENCIDO    = 'DC3545';

    public function __construct(string $codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;
    }

    public function query()
    {
        return Equipo::where('codigo_cliente', $this->codigoCliente)
            ->orderBy('numero_interno');
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
            $equipo->numero_interno ?: 'S/N',
            $equipo->numero_serie,
            $equipo->tipo_extintor,
            $equipo->capacidad,
            $equipo->marca,
            $equipo->anio_fabricacion,
            $equipo->proximo_mantenimiento, // texto tipo "ABR-2027", no es una fecha parseable
            $this->formatearFecha($equipo->vencimiento_ph),
            $this->calcularEstado($equipo->vencimiento_ph),
            $this->formatearFecha($equipo->ultimo_servicio),
            $equipo->numero_certificado,
        ];
    }

    private function formatearFecha($fecha): string
    {
        if (!$fecha) {
            return '';
        }

        try {
            return Carbon::parse($fecha)->format('d/m/Y');
        } catch (\Throwable $e) {
            // El valor no es una fecha parseable (ej. texto libre); lo mostramos tal cual
            return (string) $fecha;
        }
    }

    private function calcularEstado($vencimientoPh): string
    {
        if (!$vencimientoPh) {
            return 'Sin dato';
        }

        $hoy = Carbon::today();
        $vencimiento = Carbon::parse($vencimientoPh);

        if ($vencimiento->lt($hoy)) {
            return 'Vencido';
        }

        if ($vencimiento->lte($hoy->copy()->addDays(60))) {
            return 'Por vencer';
        }

        return 'Vigente';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 16,
            'C' => 16,
            'D' => 12,
            'E' => 14,
            'F' => 16,
            'G' => 20,
            'H' => 16,
            'I' => 14,
            'J' => 16,
            'K' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $ultimaFila = $sheet->getHighestRow();
                $ultimaColumna = 'K';

                // --- Encabezado ---
                $sheet->getStyle("A1:{$ultimaColumna}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::COLOR_ENCABEZADO],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Fila de encabezado siempre visible al hacer scroll
                $sheet->freezePane('A2');

                // --- Bordes en toda la tabla ---
                $sheet->getStyle("A1:{$ultimaColumna}{$ultimaFila}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'B7B7B7'],
                        ],
                    ],
                ]);

                // --- Filas alternadas (cebra) ---
                for ($fila = 2; $fila <= $ultimaFila; $fila++) {
                    if ($fila % 2 === 0) {
                        $sheet->getStyle("A{$fila}:{$ultimaColumna}{$fila}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ],
                        ]);
                    }
                }

                // --- Color según Estado (columna I) ---
                for ($fila = 2; $fila <= $ultimaFila; $fila++) {
                    $estado = $sheet->getCell("I{$fila}")->getValue();

                    $color = match ($estado) {
                        'Vigente' => self::COLOR_VIGENTE,
                        'Por vencer' => self::COLOR_POR_VENCER,
                        'Vencido' => self::COLOR_VENCIDO,
                        default => null,
                    };

                    if ($color) {
                        $colorTexto = $estado === 'Vigente' ? '000000' : 'FFFFFF';

                        $sheet->getStyle("I{$fila}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $color],
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => $colorTexto],
                            ],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                }

                // Centrar columnas numéricas / cortas
                foreach (['A', 'D', 'F', 'I'] as $columna) {
                    $sheet->getStyle("{$columna}2:{$columna}{$ultimaFila}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}