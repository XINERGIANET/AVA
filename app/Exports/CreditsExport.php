<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CreditsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $start_date;
    protected $end_date;
    protected $location_id;
    protected $client_id;
    protected $product_id;
    protected $payment_day;


    public function __construct(
        $start_date = null,
        $end_date = null,
        $location_id = null,
        $client_id = null,
        $product_id = null,
        $payment_day = null
    ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location_id = $location_id;
        $this->client_id = $client_id;
        $this->product_id = $product_id;
        $this->payment_day = $payment_day;
    }

    public function collection()
    {
        $credits = Payment::with([
            'sale.sale_details.product',
            'sale.location',
            'sale.responsible',
            'agreement.agreement_details.product',
            'agreement.location',
            'client',
            'payment_method'
        ])
            ->whereIn('status', ['paid', 'pending'])
            ->where('deleted', 0);

        if ($this->start_date) {
            $credits->whereDate('date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $credits->whereDate('date', '<=', $this->end_date);
        }
        if ($this->client_id) {
            $credits->where('client_id', $this->client_id);
        }
        if ($this->location_id) {
            $credits->where(function($query) {
                $query->whereHas('sale.location', fn($q) => $q->where('id', $this->location_id))
                      ->orWhereHas('agreement.location', fn($q) => $q->where('id', $this->location_id));
            });
        }
        
        return $credits->orderBy('date', 'desc')->get();
    }

    public function map($payment): array
    {
        $productos = '';
        $voucher_code = 'N/A';
        $responsible = 'N/A';
        $detail = 'N/A';
        $vehicle_plate = 'N/A';

        if ($payment->sale && $payment->sale->sale_details) {
            $productos = $payment->sale->sale_details->map(function ($detail) {
                return ($detail->product->name ?? 'Producto') . ' (' . $detail->quantity . ')';
            })->join(', ');
            $voucher_code = $payment->sale->voucher_code ?? 'N/A';
            $responsible = $payment->sale->responsible ? $payment->sale->responsible->name . ' ' . $payment->sale->responsible->last_name : 'N/A';
            $detail = $payment->sale->detail ?? 'N/A';
            $vehicle_plate = $payment->sale->vehicle_plate ?? 'N/A';
        } elseif ($payment->agreement && $payment->agreement->agreement_details) {
            $productos = $payment->agreement->agreement_details->map(function ($detail) {
                return ($detail->product->name ?? 'Producto') . ' (' . $detail->quantity . ')';
            })->join(', ');
        }

        $cliente_name = $payment->client_name ?? 'Sin cliente';
        $document = $payment->client ? $payment->client->document : 'N/A';
        $phone = $payment->client ? $payment->client->phone : 'N/A';
        $metodo_pago = $payment->payment_method ? $payment->payment_method->name : 'N/A';
        
        return [
            $payment->date ? $payment->date->format('d/m/Y') : 'N/A', // Fecha
            $voucher_code, // Código de Vale
            $cliente_name, // Cliente
            $vehicle_plate, // Placa
            $document, // DNI/RUC
            $phone, // Celular
            $responsible, // Responsable
            $productos, // Descripción (Productos)
            number_format($payment->amount, 2), // Monto
            ucfirst($payment->status == 'paid' ? "Pagado" : "No pagado"), // Estado
            $payment->status == 'paid' ? ($payment->updated_at ? $payment->updated_at->format('d/m/Y') : 'N/A') : 'N/A', // Fecha Pago
            $metodo_pago, // Método de Pago
            $detail // Detalle
        ];
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Código de Vale',
            'Cliente',
            'Placa',
            'DNI/RUC',
            'Celular',
            'Responsable',
            'Descripción',
            'Monto',
            'Estado',
            'Fecha Pago',
            'Método de Pago',
            'Detalle',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
