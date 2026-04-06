<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasTemplateDataSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Ventas';
    }

    public function array(): array
    {
        return [
            [
                '1',                   // A: Ref. venta
                date('Y-m-d'),         // B: Fecha
                '',      // C: Sede
                '',          // D: Cliente
                '',            // E: Documento
                '',           // F: Teléfono
                '0',                   // G: Tipo venta
                '',             // H: Placa
                '0',                   // I: Adicional
                '',     // J: Producto
                '1',                   // K: Cantidad
                '',               // L: Precio
                '',                    // M: Descuento
                '',               // N: Subtotal
                '',            // O: Método pago
                '',                    // P: Número
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Ref. venta (opcional, vacío = 1 venta por fila)',
            'Fecha (AAAA-MM-DD)',
            'Sede (nombre — ver hoja Lista sedes)',
            'Cliente (nombre o razón social)',
            'Documento (opcional, DNI/RUC)',
            'Teléfono',
            'Tipo venta (0=Directa, 2=Crédito)',
            'Placa vehículo',
            'Adicional',
            'Producto (nombre — ver Lista productos)',
            'Cantidad',
            'Precio unitario',
            'Precio con descuento',
            'Subtotal línea',
            'Método de pago (nombre — ver Lista métodos)',
            'Número crédito o ticket (opcional, solo crédito)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE0E0E0']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Configurar validación base
                $validation = $sheet->getCell('C2')->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Error de captura');
                $validation->setError('El valor no está en la lista. Puedes usar la lista desplegable.');
                
                // Sede (C)
                $validationC = clone $validation;
                $validationC->setPromptTitle('Sede');
                $validationC->setPrompt('Seleccione la Sede');
                $validationC->setFormula1('\'Listas\'!$A$2:$A$100');
                $sheet->setDataValidation('C2:C500', $validationC);

                // Tipo Venta (G)
                $validationG = clone $validation;
                $validationG->setPromptTitle('Tipo de venta');
                $validationG->setPrompt('0 para Directa, 2 para Crédito');
                $validationG->setFormula1('"0,2"');
                $sheet->setDataValidation('G2:G500', $validationG);

                // Producto (J)
                $validationJ = clone $validation;
                $validationJ->setPromptTitle('Producto');
                $validationJ->setPrompt('Seleccione el Producto');
                $validationJ->setFormula1('\'Listas\'!$B$2:$B$100');
                $sheet->setDataValidation('J2:J500', $validationJ);
                
                // Método Pago (O)
                $validationO = clone $validation;
                $validationO->setPromptTitle('Método de Pago');
                $validationO->setPrompt('Seleccione la Forma de Pago');
                $validationO->setFormula1('\'Listas\'!$C$2:$C$100');
                $sheet->setDataValidation('O2:O500', $validationO);
            }
        ];
    }
}
