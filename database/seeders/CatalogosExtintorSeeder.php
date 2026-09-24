<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosExtintorSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'PQS ABC',
            'PQS BC',
            'CO2',
            'AGUA DESMINERALIZADA',
            'AGUA DESIONIZADA',
            'ESPUMA (AFFF)',
            'ACETATO DE POTASIO (K)',
            'AGENTE LIMPIO (HALOTRON I)',
            'CLASE D',
            'OTRO',
        ];

        $marcas = [
            'NACIONAL',
            'ASIÁTICO',
            'AMEREX',
            'ANSUL',
            'BADGER',
            'BUCKEYE',
            'GLORIA',
            'FOX',
            'KIDDE',
            'PYRO-CHEM',
            'FIRE STAR',
            'MOYNE',
            'OTROS',
            'SIN MARCA / NO IDENTIFICADA',
        ];

        $capacidades = [
            '01 KG', '02 KG', '04 KG', '05 KG', '06 KG', '09 KG', '10 KG',
            '12 KG', '25 KG', '50 KG', '70 LG', '75 KG', '100 KG',
            '2.5 LB', '05 LB', '10 LB', '11 LB', '15 LB', '20 LB', '30 LB', '125 LB',
            '1.6 GL', '1.75 GL', '2.5 GL',
            '06 LT', '09 LT',
        ];

        foreach ($tipos as $nombre) {
            DB::table('tipos_extintor')->updateOrInsert(
                ['nombre' => $nombre],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach ($marcas as $nombre) {
            DB::table('marcas')->updateOrInsert(
                ['nombre' => $nombre],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach ($capacidades as $valor) {
            DB::table('capacidades')->updateOrInsert(
                ['valor' => $valor],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
