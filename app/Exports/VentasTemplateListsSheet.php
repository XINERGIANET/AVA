<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Location;
use App\Models\Product;
use App\Models\PaymentMethod;

class VentasTemplateListsSheet implements FromArray, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Listas';
    }

    public function array(): array
    {
        $locations = Location::where('deleted', false)->pluck('name')->toArray();
        $products = Product::pluck('name')->toArray();
        $methods = PaymentMethod::where('deleted', false)->pluck('name')->toArray();
        
        $maxRows = max(count($locations), count($products), count($methods));
        
        $data = [
            ['Sedes', 'Productos', 'Métodos de Pago']
        ];
        
        for ($i = 0; $i < $maxRows; $i++) {
            $data[] = [
                $locations[$i] ?? '',
                $products[$i] ?? '',
                $methods[$i] ?? ''
            ];
        }
        
        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Ocultar hoja de listas opcionalmente para que no estorbe (usuario lo notará más limpio)
                // Usaremos SHEETSTATE_HIDDEN en lugar de _VERYHIDDEN para que usuarios avanzados puedan verla si quieren
                $event->sheet->getDelegate()->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
            }
        ];
    }
}
