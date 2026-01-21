<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $start_date;
    protected $end_date;
    protected $number;
    protected $client_name;
    protected $voucher_type;
    protected $payment_method_id;

    public function __construct($start_date = null, $end_date = null, $number = null, $client_name = null, $voucher_type = null, $payment_method_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->number = $number;
        $this->client_name = $client_name;
        $this->voucher_type = $voucher_type;
        $this->payment_method_id = $payment_method_id;
    }

    public function collection()
    {
       $consulta = Payment::query()
            ->with('payment_method','client')
            ->when($this->start_date, fn($query) => $query->whereDate('date', '>=', $this->start_date))
            ->when($this->end_date, fn($query) => $query->whereDate('date', '<=', $this->end_date))
            ->when($this->number, fn($query) => $query->where('number', 'like', "%$this->number%"))
            ->when($this->client_name, fn($query) => $query->where('client_name', 'like', "%$this->client_name%"))
            ->when($this->voucher_type, fn($query) => $query->where('voucher_type', $this->voucher_type))
            ->when($this->payment_method_id, fn($query) => $query->where('payment_method_id', $this->payment_method_id))
            ->orderBy("date", "desc")
            ->orderBy("id", "desc");

        return $consulta->get();
    }

    public function map($payment): array
    {

        $data[] = [
            $payment->date->format('d/m/Y'),                             // Fecha
            $payment->date->format('d'),                                 // Día
            $payment->date->translatedFormat('F'),                       // Mes completo
            $payment->date->format('Y'),                                 // Año
            $payment->date->translatedFormat('l'),                       // Día de semana
            $payment->number,                                            // Número
            $payment->voucher_type,                        // Comprobante
            $payment->payment_method->name,
            $payment->client_id ? $payment->client->nombre : $payment->cliente ?? "varios",       // Cliente
            $payment->amount,                                             // Total
            $payment->deleted == 0 ? 'Activo' : 'Anulado'
        ];
        

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
            'NÚMERO',
            'COMPROBANTE',
            'MÉTODO DE PAGO',
            'CLIENTE',
            'MONTO',
            'ESTADO'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
