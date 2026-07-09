<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesByIsleSheet implements FromCollection, WithHeadings, WithEvents, WithStyles, ShouldAutoSize, WithTitle
{
    protected $title;
    protected $sales;
    protected $date;
    protected $isleId;

    public function __construct(string $title, $sales, $date = null, $isleId = null)
    {
        $this->title = $title;
        $this->sales = $sales;
        $this->date = $date;
        $this->isleId = $isleId;
    }

    public function collection()
    {
        $groups = [];

        $this->eachSale(function ($sale, $details) use (&$groups) {
            if (! $sale) {
                return;
            }

            $key = $this->groupKey($sale);
            $groups[$key] = $groups[$key] ?? $this->emptyGroup($sale);
            $group = &$groups[$key];

            $group['total_venta'] += (float) ($sale->total ?? $details->sum('subtotal'));
            $group['descuentos'] += $this->discountTotal($details);

            $paidAmount = 0;
            foreach ($sale->payments->where('deleted', 0) as $payment) {
                $amount = (float) $payment->amount;

                if ($payment->status === 'pending') {
                    continue;
                }

                $paidAmount += $amount;
                $bucket = $this->paymentBucket(optional($payment->payment_method)->name);
                if ($bucket) {
                    $group[$bucket] += $amount;
                } elseif ((int) $sale->type_sale !== 0) {
                    $group['cuenta_cobrada'] += $amount;
                }
            }

            if ((int) $sale->type_sale !== 0) {
                $group['por_cobrar'] += max(0, (float) $sale->total - $paidAmount);
            }
        });

        foreach ($this->expensesByGroup() as $key => $expense) {
            $groups[$key] = $groups[$key] ?? $this->emptyExpenseGroup($expense);
            $groups[$key]['gastos_costos'] += (float) $expense['amount'];
        }

        ksort($groups);

        return collect(array_map(function ($group) {
            $totalCuadre = $group['efectivo']
                + $group['yape']
                + $group['bcp']
                + $group['bbva']
                + $group['qulqui']
                + $group['cuenta_cobrada']
                + $group['por_cobrar'];

            $diferencia = $group['total_venta'] - $totalCuadre;
            $ingresoCaja = $group['efectivo']
                + $group['yape']
                + $group['bcp']
                + $group['bbva']
                + $group['qulqui']
                + $group['cuenta_cobrada']
                - $group['gastos_costos'];

            return [
                $group['date'],
                $group['responsable'],
                round($group['total_venta'], 2),
                round($group['efectivo'], 2),
                round($group['yape'], 2),
                round($group['bcp'], 2),
                round($group['bbva'], 2),
                round($group['qulqui'], 2),
                round($group['descuentos'], 2),
                round($group['cuenta_cobrada'], 2),
                round($group['por_cobrar'], 2),
                round($group['gastos_costos'], 2),
                round($totalCuadre, 2),
                round($diferencia, 2),
                round($ingresoCaja, 2),
            ];
        }, $groups));
    }

    public function headings(): array
    {
        return [
            'FECHA',
            'RESPONSABLE',
            'TOTAL DE VENTA',
            'EFECTIVO',
            'YAPE',
            'BCP',
            'BBVA',
            'QULQUI',
            'DESCUENTOS',
            'CUENTA COBRADA',
            'POR COBRAR',
            'GASTOS/COSTOS',
            'TOTAL',
            'DIFERENCIA',
            'INGRESO A CAJA DEL DIA',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 1);
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', 'DETALLE DE VENTAS');

                $sheet->getStyle('A1:O1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '20124D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $headerColors = [
                    'A2' => '274E13',
                    'B2' => '274E13',
                    'C2' => '274E13',
                    'D2' => 'D9EAD3',
                    'E2' => '7F00FF',
                    'F2' => '0000FF',
                    'G2' => 'CFE2F3',
                    'H2' => 'F6B26B',
                    'I2' => 'EA9999',
                    'J2' => '6AA84F',
                    'K2' => 'FFFF00',
                    'L2' => 'F4CCCC',
                    'M2' => 'FFFFFF',
                    'N2' => '990000',
                    'O2' => 'D9EAD3',
                ];

                foreach ($headerColors as $cell => $color) {
                    $fontColor = in_array($cell, ['D2', 'G2', 'K2', 'L2', 'M2', 'O2'], true) ? '000000' : 'FFFFFF';
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $fontColor]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:O{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'B7B7B7'],
                        ],
                    ],
                ]);
                if ($highestRow >= 3) {
                    $sheet->getStyle("C3:O{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                }
                $sheet->freezePane('A3');
            },
        ];
    }

    protected function eachSale(callable $callback): void
    {
        $first = $this->sales->first();

        if ($first && (isset($first->sale) || isset($first->sale_id))) {
            $this->sales
                ->filter(function ($detail) {
                    return ! isset($detail->deleted) || (int) $detail->deleted === 0;
                })
                ->groupBy('sale_id')
                ->each(function ($details) use ($callback) {
                    $callback(optional($details->first())->sale, $details);
                });

            return;
        }

        foreach ($this->sales as $sale) {
            $callback($sale, $sale->sale_details);
        }
    }

    protected function groupKey($sale): string
    {
        $date = optional($sale->date)->format('Y-m-d') ?: '';
        return $date . '|' . $this->responsibleId($sale) . '|' . $this->responsibleName($sale);
    }

    protected function emptyGroup($sale): array
    {
        return [
            'date' => optional($sale->date)->format('d/m/Y') ?: '',
            'responsable' => $this->responsibleName($sale),
            'total_venta' => 0,
            'efectivo' => 0,
            'yape' => 0,
            'bcp' => 0,
            'bbva' => 0,
            'qulqui' => 0,
            'descuentos' => 0,
            'cuenta_cobrada' => 0,
            'por_cobrar' => 0,
            'gastos_costos' => 0,
        ];
    }

    protected function emptyExpenseGroup(array $expense): array
    {
        return [
            'date' => $expense['date'],
            'responsable' => $expense['responsable'],
            'total_venta' => 0,
            'efectivo' => 0,
            'yape' => 0,
            'bcp' => 0,
            'bbva' => 0,
            'qulqui' => 0,
            'descuentos' => 0,
            'cuenta_cobrada' => 0,
            'por_cobrar' => 0,
            'gastos_costos' => 0,
        ];
    }

    protected function responsibleId($sale): string
    {
        if ($sale->responsible_id) {
            return 'employee-' . $sale->responsible_id;
        }

        return 'user-' . ($sale->user_id ?: 'sin-responsable');
    }

    protected function responsibleName($sale): string
    {
        if ($sale->responsible) {
            return trim($sale->responsible->name . ' ' . $sale->responsible->last_name);
        }

        if ($sale->user) {
            return $sale->user->name;
        }

        return 'SIN RESPONSABLE';
    }

    protected function discountTotal($details): float
    {
        return (float) $details->sum(function ($detail) {
            if ($detail->discounted_price === null || $detail->discounted_price === '') {
                return 0;
            }

            return max(0, ((float) $detail->unit_price - (float) $detail->discounted_price) * (float) $detail->quantity);
        });
    }

    protected function paymentBucket(?string $name): ?string
    {
        $normalized = $this->normalize($name ?? '');

        if ($this->contains($normalized, 'efectivo')) {
            return 'efectivo';
        }

        if ($this->contains($normalized, 'yape') || $this->contains($normalized, 'plin')) {
            return 'yape';
        }

        if ($this->contains($normalized, 'bcp')) {
            return 'bcp';
        }

        if ($this->contains($normalized, 'bbva')) {
            return 'bbva';
        }

        if ($this->contains($normalized, 'qulqui') || $this->contains($normalized, 'culqi')) {
            return 'qulqui';
        }

        if ($this->contains($normalized, 'cuenta') || $this->contains($normalized, 'cobr')) {
            return 'cuenta_cobrada';
        }

        return null;
    }

    protected function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value));
        return strtr($value, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
        ]);
    }

    protected function contains(string $haystack, string $needle): bool
    {
        return mb_strpos($haystack, $needle) !== false;
    }

    protected function expensesByGroup(): array
    {
        $query = Transaction::with('user')->where('type', 'scc');

        if ($this->isleId) {
            $query->where('isle_id', $this->isleId);
        }

        if ($this->date) {
            $query->whereDate('date', $this->date);
        }

        $expenses = [];
        foreach ($query->get() as $expense) {
            $dateKey = optional($expense->date)->format('Y-m-d') ?: '';
            $responsable = $expense->user ? $expense->user->name : 'SIN RESPONSABLE';
            $key = $dateKey . '|user-' . ($expense->user_id ?: 'sin-responsable') . '|' . $responsable;

            $expenses[$key] = $expenses[$key] ?? [
                'date' => optional($expense->date)->format('d/m/Y') ?: '',
                'responsable' => $responsable,
                'amount' => 0,
            ];
            $expenses[$key]['amount'] += (float) $expense->amount;
        }

        return $expenses;
    }
}
