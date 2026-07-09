<?php

namespace App\Exports;

use App\Models\PurchaseDetail;
use App\Models\TankReading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MermaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $start_date;
    protected $end_date;
    protected $location_id;
    protected $product_id;

    public function __construct(
        $start_date = null,
        $end_date = null,
        $location_id = null,
        $product_id = null
    ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location_id = $location_id;
        $this->product_id = $product_id;
    }

    public function collection()
    {
        $readings = TankReading::with(['tank.product', 'tank.location', 'user'])
            ->where('deleted', 0);

        if ($this->start_date) {
            $readings->whereDate('date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $readings->whereDate('date', '<=', $this->end_date);
        }
        if ($this->location_id) {
            $readings->whereHas('tank', function ($q) {
                $q->where('location_id', $this->location_id);
            });
        }
        if ($this->product_id) {
            $readings->whereHas('tank', function ($q) {
                $q->where('product_id', $this->product_id);
            });
        }

        return $readings->orderBy('date', 'desc')->get();
    }

    public function map($reading): array
    {
        $tank = $reading->tank;

        $proveedor = 'N/A';
        if ($tank) {
            $purchaseDetail = PurchaseDetail::where('tank_id', $tank->id)
                ->whereHas('purchase', fn($q) => $q->whereDate('date', '<=', $reading->date))
                ->with('purchase.supplier')
                ->latest('id')
                ->first();

            if ($purchaseDetail && $purchaseDetail->purchase && $purchaseDetail->purchase->supplier) {
                $proveedor = $purchaseDetail->purchase->supplier->commercial_name
                    ?? $purchaseDetail->purchase->supplier->company_name
                    ?? 'N/A';
            }
        }

        $nivelStock = 'SIN DATO';
        if ($tank && $tank->capacity > 0) {
            $porcentaje = $tank->stored_quantity / $tank->capacity;
            if ($porcentaje <= 0.05) {
                $nivelStock = 'SIN STOCK';
            } elseif ($porcentaje <= 0.25) {
                $nivelStock = 'STOCK BAJO';
            } elseif ($porcentaje <= 0.60) {
                $nivelStock = 'STOCK MEDIO';
            } else {
                $nivelStock = 'STOCK ALTO';
            }
        }

        $estados = [
            'falta' => 'Falta',
            'cuadra' => 'Cuadra',
            'sobra' => 'Sobra',
        ];

        return [
            $reading->date ? $reading->date->format('d/m/Y') : 'N/A',
            $reading->user ? $reading->user->name : 'N/A',
            $tank && $tank->location ? $tank->location->name : 'N/A',
            $tank && $tank->product ? $tank->product->name : 'N/A',
            $proveedor,
            number_format($reading->previous_stock, 3),
            number_format($reading->theoretical_stock, 3),
            number_format($reading->physical_quantity, 3),
            number_format($reading->difference, 3),
            $estados[$reading->status] ?? ucfirst($reading->status),
            $nivelStock,
        ];
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Responsable',
            'Sede',
            'Producto',
            'Proveedor',
            'Stock Inicial',
            'Teórico',
            'Real',
            'Diferencia',
            'Estado',
            'Nivel de Stock',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
