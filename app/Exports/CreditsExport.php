<?php

namespace App\Exports;

use App\Models\Agreement;
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
        $credits = Agreement::with(['client', 'location'])
            ->where('status', 'active')
            ->where('type', 'credit');

        if ($this->start_date && $this->end_date) {
            $credits->whereBetween('date', [$this->start_date, $this->end_date]);
        }
        if ($this->location_id) {
            $credits->where('location_id', $this->location_id);
        }
        if ($this->client_id) {
            $credits->where('client_id', $this->client_id);
        }
        if ($this->payment_day) {
            $credits->where('days_credit', $this->payment_day);
        }
        if ($this->product_id) {
            $credits->whereHas('agreement_details', function ($query) {
                $query->where('product_id', $this->product_id);
            });
        }
        return $credits->get();
    }

    public function map($credit): array
    {
        $productos = $credit->agreement_details->map(function ($detail) {
            return $detail->product->name . ' (' . $detail->quantity . ')';
        })->join(', ');
        return [
            //fecha
            $credit->date->format('d/m/Y'),                             // Fecha
            $credit->date->format('d'),                                 // Día
            $credit->date->translatedFormat('F'),                       // Mes completo
            $credit->date->format('Y'),                                 // Año
            $credit->date->translatedFormat('l'),
            //mas
            $credit->client->business_name ? $credit->client->business_name : $credit->client->contact_name,
            $credit->location->name,
            number_format($credit->total, 2),
            $productos,
            ucfirst($credit->status== 1 ? "Pagado" : "No pagado"),
        ];
    }

    public function headings(): array
    {
        return [

            //fehca
            'FECHA',
            'DIA',
            'MES',
            'AÑO',
            'DIA DE SEMANA',
            //mas
            'CLIENTE',
            'UBICACIÓN',
            'TOTAL',
            'PRODUCTOS',
            'ESTADO',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
