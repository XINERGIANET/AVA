<?php

namespace App\Exports;


use App\Models\Transfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransfersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $from_location_id;
    protected $from_tank;
    protected $to_location_id;
    protected $to_tank;
    protected $product;
    protected $unit;
    protected $quantity;
    protected $received;
    protected $from_date;
    protected $to_date;

    public function __construct(
        $from_location_id = null,
        $from_tank = null,
        $to_location_id = null,
        $to_tank = null,
        $product = null,
        $unit = null,
        $quantity = null,
        $from_date = null,
        $to_date = null,
        $received = null
    ) {
        $this->from_location_id = $from_location_id;
        $this->from_tank = $from_tank;
        $this->to_location_id = $to_location_id;
        $this->to_tank = $to_tank;
        $this->product = $product;
        $this->unit = $unit;
        $this->quantity = $quantity;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->received = $received;
    }

    public function collection()
    {
        $query = Transfer::with(['from_tank.location', 'to_tank.location', 'product'])
            ->where('deleted', 0);

        // Fechas
        if ($this->from_date && $this->to_date) {
            $query->whereBetween('created_at', [
                $this->from_date . ' 00:00:00',
                $this->to_date   . ' 23:59:59'
            ]);
        } elseif ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        } elseif ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        // ...resto de filtros igual...
        if ($this->from_location_id) {
            $query->whereHas('from_tank', function ($q) {
                $q->where('location_id', $this->from_location_id);
            });
        }
        if ($this->from_tank) {
            $query->where('from_tank_id', $this->from_tank);
        }
        if ($this->to_location_id) {
            $query->whereHas('to_tank', function ($q) {
                $q->where('location_id', $this->to_location_id);
            });
        }
        if ($this->to_tank) {
            $query->where('to_tank_id', $this->to_tank);
        }
        if ($this->product) {
            $query->where('product_id', $this->product);
        }
        if ($this->unit) {
            $query->where('unit', 'like', '%' . $this->unit . '%');
        }
        if ($this->quantity) {
            $query->where('quantity', $this->quantity);
        }
        if ($this->received !== null && $this->received !== '') {
            $query->where('received', $this->received);
        }

        return $query->get();
    }

    public function map($transfer): array
    {
        return [
            optional($transfer->from_tank->location)->name ?? 'N/A', // Sede origen
            optional($transfer->from_tank)->name ?? 'N/A',         // Tanque origen
            optional($transfer->to_tank->location)->name ?? 'N/A',   // Sede destino
            optional($transfer->to_tank)->name ?? 'N/A',           // Tanque destino
            optional($transfer->product)->name ?? 'N/A',           // Producto
            $transfer->unit ?? 'N/A',                              // Unidad
            $transfer->quantity ?? 'N/A',                          // Cantidad
            $transfer->created_at ? $transfer->created_at->format('d/m/Y') : 'N/A', // Fecha de creación
            $transfer->received ? 'Sí' : 'No',                     // Recibido
        ];
    }

    public function headings(): array
    {
        return [
            'SEDE ORIGEN',
            'TANQUE ORIGEN',
            'SEDE DESTINO',
            'TANQUE DESTINO',
            'PRODUCTO',
            'UNIDAD',
            'CANTIDAD',
            'FECHA DE CREACIÓN',
            'RECIBIDO'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
