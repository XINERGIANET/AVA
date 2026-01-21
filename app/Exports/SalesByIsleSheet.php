<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesByIsleSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $title;
    protected $sales; // puede ser colección de Sale o de SaleDetail

    public function __construct(string $title, $sales)
    {
        $this->title = $title;
        $this->sales = $sales;
    }

    public function collection()
    {
        $rows = [];

        $first = $this->sales->first();

        // Si recibimos una colección de sale_details (el export adjunta ->sale a cada detail)
        if ($first && (isset($first->sale) || isset($first->sale_id))) {
            foreach ($this->sales as $detail) {
                $sale = $detail->sale ?? null;
                if (! $sale) {
                    // intentar relación si no se adjuntó explícitamente
                    $sale = $detail->sale ?? null;
                }

                $payment_method = $sale && $sale->payments->first()
                    ? optional($sale->payments->first()->payment_method)->name
                    : 'N/A';

                $type_sale_map = [
                    0 => 'Directa',
                    1 => 'Contrato',
                    2 => 'Crédito',
                ];
                $type_sale_text = $sale ? ($type_sale_map[$sale->type_sale] ?? 'N/A') : 'N/A';

                $client_name = 'N/A';
                if ($sale) {
                    if ($sale->client && isset($sale->client->business_name)) {
                        $client_name = $sale->client->business_name;
                    } elseif ($sale->client_name) {
                        $client_name = $sale->client_name;
                    } elseif ($sale->client) {
                        $client_name = $sale->client;
                    }
                }

                $rows[] = [
                    optional($sale->date)->format('d/m/Y') ?: '',
                    optional($sale->date)->format('d') ?: '',
                    optional($sale->date)->translatedFormat('F') ?: '',
                    optional($sale->date)->format('Y') ?: '',
                    optional($sale->date)->translatedFormat('l') ?: '',
                    $client_name,
                    $sale->phone ?? 'N/A',
                    $type_sale_text,
                    $sale->id ?? ($detail->sale_id ?? ''),
                    $payment_method,
                    $sale->total ?? ($detail->subtotal ?? 0),
                    optional($detail->product)->name ?? 'N/A',
                    'UND',
                    $detail->quantity,
                    $detail->unit_price,
                    $detail->subtotal,
                ];
            }

            return collect($rows);
        }

        // Comportamiento anterior: colección de ventas (cada venta puede contener sale_details)
        foreach ($this->sales as $sale) {
            $payment_method = $sale->payments->first() ? optional($sale->payments->first()->payment_method)->name : 'N/A';

            $type_sale_map = [
                0 => 'Directa',
                1 => 'Contrato',
                2 => 'Crédito',
            ];
            $type_sale_text = $type_sale_map[$sale->type_sale] ?? 'N/A';

            $client_name = 'N/A';
            if ($sale->client && isset($sale->client->business_name)) {
                $client_name = $sale->client->business_name;
            } elseif ($sale->client_name) {
                $client_name = $sale->client_name;
            } elseif ($sale->client) {
                $client_name = $sale->client;
            }

            foreach ($sale->sale_details as $detail) {
                $rows[] = [
                    optional($sale->date)->format('d/m/Y') ?: '',
                    optional($sale->date)->format('d') ?: '',
                    optional($sale->date)->translatedFormat('F') ?: '',
                    optional($sale->date)->format('Y') ?: '',
                    optional($sale->date)->translatedFormat('l') ?: '',
                    $client_name,
                    $sale->phone ?? 'N/A',
                    $type_sale_text,
                    $sale->id,
                    $payment_method,
                    $sale->total,
                    optional($detail->product)->name ?? 'N/A',
                    'UND',
                    $detail->quantity,
                    $detail->unit_price,
                    $detail->subtotal,
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'FECHA','DIA','MES','AÑO','DIA DE SEMANA','CLIENTE','TELÉFONO',
            'TIPO VENTA','NÚMERO','FORMA DE PAGO','TOTAL','ARTÍCULO','UNIDAD',
            'CANTIDAD','PRECIO UNITARIO','SUBTOTAL VENTA'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return $this->title;
    }
}