<?php

namespace App\Exports;

use App\Models\Sale;
use App\Exports\SalesByIsleSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesByIsleExport implements WithMultipleSheets
{
    protected $date;
    protected $location_id;

    public function __construct($date = null, $location_id = null)
    {
        $this->date = $date;
        $this->location_id = $location_id;
    }

    public function sheets(): array
    {
        // normalizar fecha
        $normalizedDate = $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null;

        // cargar sale_details.pump.isle porque pump está en sale_detail
        $query = Sale::with([
            'sale_details.pump.isle',
            'sale_details.product',
            'payments.payment_method',
            'client'
        ])->where('deleted', 0);

        if ($normalizedDate) {
            $query->whereDate('date', $normalizedDate);
        }

        // opcional: intentar registrar SQL para debug
        try {
            $rawSql = str_replace('?', "'%s'", $query->toSql());
            $fullSql = vsprintf($rawSql, $query->getBindings());
            Log::debug('[SalesByIsleExport] SQL: ' . $fullSql);
        } catch (\Throwable $e) {
            Log::debug('[SalesByIsleExport] toSql error: ' . $e->getMessage());
        }

        $sales = $query->get();

        // convertir ventas a colección de detalles (cada detalle conserva referencia a su sale)
        $details = $sales->flatMap(function ($sale) {
            return $sale->sale_details->map(function ($detail) use ($sale) {
                $detail->sale = $sale;
                return $detail;
            });
        });

        // si se pasó location_id, filtrar sólo detalles cuya isla pertenece a esa ubicación
        if ($this->location_id !== null) {
            $details = $details->filter(function ($detail) {
                $isle = optional(optional($detail->pump)->isle);
                return $isle && ((string)$isle->location_id === (string)$this->location_id);
            })->values();
        }

        // agrupar por isla (detalles sin pump/isle van a "sin_isla")
        $groups = $details->groupBy(function ($detail) {
            $isle = optional(optional($detail->pump)->isle);
            return $isle ? $isle->id : 'sin_isla';
        });

        $sheets = [];
        foreach ($groups as $groupKey => $groupDetails) {
            $title = 'Sin Isla';
            if ($groupKey !== 'sin_isla') {
                $first = $groupDetails->first();
                $isle = optional(optional($first->pump)->isle);
                $title = $isle ? ($isle->name ?? 'Isla ' . $isle->id) : 'Sin Isla';
            }
            $title = $this->sanitizeSheetName($title);
            // pasar colección de detalles a la hoja; SalesByIsleSheet procesará cada detalle y su ->sale
            $sheets[] = new SalesByIsleSheet($title, $groupDetails);
        }

        return $sheets;
    }

    protected function sanitizeSheetName(string $name): string
    {
        $invalid = ['\\', '/', '*', '[', ']', ':', '?'];
        $clean = str_replace($invalid, '-', $name);
        return mb_substr($clean, 0, 31) ?: 'Hoja';
    }
}