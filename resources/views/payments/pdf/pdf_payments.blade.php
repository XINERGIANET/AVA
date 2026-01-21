<!-- filepath: resources/views/payments/pdf/pdf_payments.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>REPORTE DE PAGOS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .filters {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }

        .payment-block {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .payment-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 12px;
        }

        .client-info {
            background-color: #f1f3f4;
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th {
            background-color: #6c757d;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }

        .details-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #eee;
        }

        .details-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .payment-total {
            background-color: #e9ecef;
            padding: 8px 10px;
            text-align: right;
            font-weight: bold;
            color: #495057;
        }

        .grand-total {
            margin-top: 20px;
            text-align: center;
            background-color: #28a745;
            color: white;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title ?? 'REPORTE DE PAGOS' }}</h1>
        <p>{{ $subtitle ?? 'LISTADO DE PAGOS REGISTRADOS' }}</p>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @if (
            ($filters['start_date'] ?? null) ||
                ($filters['end_date'] ?? null) ||
                ($filters['client_name'] ?? null) ||
                ($filters['voucher_type'] ?? null) ||
                ($filters['payment_method_name'] ?? null) ||
                ($filters['number'] ?? null))
            @if ($filters['start_date'])
                Desde: {{ date('d/m/Y', strtotime($filters['start_date'])) }}
            @endif
            @if ($filters['end_date'])
                Hasta: {{ date('d/m/Y', strtotime($filters['end_date'])) }}
            @endif
            @if ($filters['client_name'])
                | Cliente: {{ $filters['client_name'] }}
            @endif
            @if ($filters['voucher_type'])
                | Tipo de comprobante: {{ $filters['voucher_type'] }}
            @endif
            @if ($filters['payment_method_name'])
                | Método de pago: {{ $filters['payment_method_name'] }}
            @endif
            @if ($filters['number'])
                | N° Comprobante: {{ $filters['number'] }}
            @endif
        @else
            Todos los pagos
        @endif
    </div>

    @if ($payments->count() > 0)
        <table class="details-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Tipo Comprobante</th>
                    <th>N° Comprobante</th>
                    <th>Método de Pago</th>
                    <th>Monto</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($payments as $payment)
                    @php $total += $payment->amount; @endphp
                    <tr>
                        <td>{{ $payment->date ? $payment->date->format('d/m/Y') : '-' }}</td>
                        <td>
                            {{ $payment->client->business_name ?? ($payment->client->contact_name ?? ($payment->client_name ?? 'varios')) }}
                        </td>
                        <td>{{ $payment->voucher_type ?? '-' }}</td>
                        <td>{{ $payment->number ?? '-' }}</td>
                        <td>{{ $payment->payment_method->name ?? '-' }}</td>
                        <td class="text-right">S/ {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->observation ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="grand-total">
            TOTAL PAGADO: S/ {{ number_format($total, 2) }}
        </div>
    @else
        <div class="no-data">
            <h3>No se encontraron pagos</h3>
            <p>No hay pagos que coincidan con los filtros aplicados.</p>
        </div>
    @endif
</body>

</html>
