<?php


namespace App\Exports;

use App\Models\Agreement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $start_date;
    protected $end_date;
    protected $location_id;
    protected $client_id;
    protected $product_id;
    protected $payment_day;

    public function __construct($start_date = null, $end_date = null, $location_id = null, $client_id = null, $product_id = null, $payment_day = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location_id = $location_id;
        $this->client_id = $client_id;
        $this->product_id = $product_id;
        $this->payment_day = $payment_day;
    }

    public function collection()
    {
        $contract = Agreement::with(['client', 'location'])
            ->where('status', 'active')
            ->where('type', 'contract');

        if ($this->start_date && $this->end_date) {
            $contract->whereBetween('date', [$this->start_date, $this->end_date]);
        }
        if ($this->location_id) {
            $contract->where('location_id', $this->location_id);
        }
        if ($this->client_id) {
            $contract->where('client_id', $this->client_id);
        }
        if ($this->payment_day) {
            $contract->where('days_credit', $this->payment_day);
        }
        if ($this->product_id) {
            $contract->whereHas('agreement_details', function ($query) {
                $query->where('product_id', $this->product_id);
            });
        }
        return $contract->get();
    }
    public function map($contract): array
    {
        $productos = $contract->agreement_details->map(function ($detail) {
            return $detail->product->name . ' (' . $detail->quantity . ')';
        })->join(', ');
        return [
            //fecha
            $contract->date->format('d/m/Y'),                             // Fecha
            $contract->date->format('d'),                                 // Día
            $contract->date->translatedFormat('F'),                       // Mes completo
            $contract->date->format('Y'),                                 // Año
            $contract->date->translatedFormat('l'),
            //mas
            $contract->client->business_name ? $contract->client->business_name : $contract->client->contact_name,
            $contract->location->name,
            number_format($contract->total, 2),
            $productos,
            ucfirst($contract->status == 1 ? "Pagado" : "No pagado"),
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
