<?php

namespace App\Exports;

use App\Models\SaleDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClosingExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $closing_number;
    protected $agreement_id;

    public function __construct($closing_number = null, $agreement_id = null)
    {
        $this->closing_number = $closing_number;
        $this->agreement_id = $agreement_id;
    }

   public function collection()
    {
        $closingNumber = $this->closing_number;
        $agreementId  = $this->agreement_id;

        $consulta = SaleDetail::with([
                'sale',
                'order_detail.product',
                'order_detail.order'
            ])
            ->where('closing_number', $closingNumber)
            ->whereHas('order_detail.order', function ($q) use ($agreementId) {
                $q->where('agreement_id', $agreementId);
            })
            ->get();

        return $consulta;
    }

    public function map($detail): array
    {

        $data[] = [
            $detail->sale->date->format('d/m/Y'),
            $detail->quantity,
            $detail->order_detail->area ?? 'sin asignar',
            $detail->vehicle ?? 'sin asignar', //llamar al nombre o lo que sea cuando se implemente
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            'FECHA',
            'CANTIDAD',
            'ÁREA',
            'VEHÍCULO'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
