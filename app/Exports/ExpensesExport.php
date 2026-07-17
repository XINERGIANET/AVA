<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $locationId;

    public function __construct($startDate = null, $endDate = null, $locationId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->locationId = $locationId;
    }

    public function collection()
    {
        $currentUser = Auth::user();
        $isMaster = $currentUser->role->nombre === 'master';

        $query = Transaction::with(['user', 'location', 'isle'])->where('type', 'scc');

        if (!$isMaster) {
            if ($currentUser->isle_id) {
                $query->where('isle_id', $currentUser->isle_id);
            } elseif ($currentUser->location_id) {
                $query->where('location_id', $currentUser->location_id);
            }
        }

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        if ($this->locationId) {
            $query->where('location_id', $this->locationId);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'FECHA',
            'RESPONSABLE',
            'TIPO',
            'CATEGORÍA',
            'DESCRIPCIÓN',
            'MONTO',
            'MÉTODO DE PAGO',
            'OBSERVACIONES'
        ];
    }

    public function map($expense): array
    {
        // El responsable puede ser el usuario que creó el registro (en la tabla transactions)
        $responsable = $expense->user ? $expense->user->name : 'N/A';
        $tipo = 'EGRESO';
        $monto = $expense->amount ? 'S/ ' . number_format($expense->amount, 2) : 'S/ 0.00';

        return [
            $expense->date ? date('d/m/Y', strtotime($expense->date)) : '',
            $responsable,
            $tipo,
            $expense->category ?? '',
            $expense->description ?? '',
            $monto,
            $expense->payment_method ?? 'EFECTIVO',
            $expense->observation ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para los encabezados
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0070C0'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Autoajustar ancho de columnas
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
