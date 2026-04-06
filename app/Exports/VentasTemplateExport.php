<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VentasTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new VentasTemplateDataSheet(),
            new VentasTemplateListsSheet()
        ];
    }
}
