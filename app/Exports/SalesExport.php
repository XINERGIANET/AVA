<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $start_date;
    protected $end_date;
    protected $location_id;
    protected $number;
    protected $client;
    protected $type_sale;
    protected $type_voucher;
    protected $payment_method_id;
    protected $user_id;

    public function __construct(
        $start_date = null,
        $end_date = null,
        $location_id = null,
        $number = null,
        $client = null,
        $type_sale = null,
        $type_voucher = null,
        $payment_method_id = null,
        $user_id = null
    ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location_id = $location_id;
        $this->number = $number;
        $this->client = $client;
        $this->type_sale = $type_sale;
        $this->type_voucher = $type_voucher;
        $this->payment_method_id = $payment_method_id;
        $this->user_id = $user_id;
    }

    public function collection()
    {
        $query = Sale::with(['location', 'sale_details.product', 'payments.payment_method', 'client']);

        if ($this->start_date) {
            $query->whereDate('date', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        if ($this->location_id) {
            $query->where('location_id', $this->location_id);
        }

        if ($this->number) {
            $query->where('id', 'like', '%' . $this->number . '%');
        }

        if ($this->client) {
            $query->where(function ($q) {
                $q->where('client', 'like', '%' . $this->client . '%')
                    ->orWhere('client_name', 'like', '%' . $this->client . '%')
                    ->orWhereHas('client', function ($subQuery) {
                        $subQuery->where('business_name', 'like', '%' . $this->client . '%')
                            ->orWhere('commercial_name', 'like', '%' . $this->client . '%');
                    });
            });
        }

        if (!is_null($this->type_sale) && $this->type_sale !== '') {
            $query->where('type_sale', $this->type_sale);
        }

        if ($this->type_voucher) {
            $query->where('type_voucher', $this->type_voucher);
        }

        if ($this->payment_method_id) {
            $query->whereHas('payments', function ($q) {
                $q->where('payment_method_id', $this->payment_method_id);
            });
        }

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        return $query->where('deleted', 0)->get();
    }

    public function map($sale): array
    {
        $data = [];

        foreach ($sale->sale_details as $detail) {
            $payment_method = $sale->payments->first() ? $sale->payments->first()->payment_method->name : 'N/A';

            // Determinar el tipo de venta
            $type_sale_text = '';
            switch ($sale->type_sale) {
                case 0:
                    $type_sale_text = 'Directa';
                    break;
                case 1:
                    $type_sale_text = 'Contrato';
                    break;
                case 2:
                    $type_sale_text = 'Crédito';
                    break;
                default:
                    $type_sale_text = 'N/A';
                    break;
            }

            // Determinar el nombre del cliente correctamente
            $client_name = 'N/A';
            if ($sale->client && $sale->client->business_name) {
                // Si tiene cliente relacionado, usar business_name
                $client_name = $sale->client->business_name;
            } elseif ($sale->client_name) {
                // Si no tiene cliente relacionado pero tiene client_name
                $client_name = $sale->client_name;
            } elseif ($sale->client) {
                // Si tiene el campo client (para ventas directas)
                $client_name = $sale->client;
            }

            $data[] = [
                $sale->date->format('d/m/Y'),                             // Fecha
                $sale->date->format('d'),                                 // Día
                $sale->date->translatedFormat('F'),                       // Mes completo
                $sale->date->format('Y'),                                 // Año
                $sale->date->translatedFormat('l'),                       // Día de semana
                $client_name,                                             // Cliente
                $sale->phone ?? 'N/A',                                    // Teléfono/Documento
                $type_sale_text,                                          // Tipo de venta
                $sale->id,                                                // Número (usando ID)
                $payment_method,                                          // Forma de pago
                $sale->total,                                             // Total
                optional($detail->product)->name ?? 'N/A',               // Artículo
                'UND',                                                    // Unidad
                $detail->quantity,                                        // Cantidad
                $detail->unit_price,                                      // Precio unitario
                $detail->subtotal                                         // Subtotal venta
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'FECHA',
            'DIA',
            'MES',
            'AÑO',
            'DIA DE SEMANA',
            'CLIENTE',
            'TELÉFONO',
            'TIPO VENTA',
            'NÚMERO',
            'FORMA DE PAGO',
            'TOTAL',
            'ARTÍCULO',
            'UNIDAD',
            'CANTIDAD',
            'PRECIO UNITARIO',
            'SUBTOTAL VENTA'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
