<!-- filepath: resources/views/credits/pdf/pdf_credits.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>REPORTE DE CRÉDITOS</title>
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

        .credit-block {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .credit-header {
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

        .credit-total {
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
        <h1>{{ $title ?? 'REPORTE DE CONTRATOS' }}</h1>
        <p>{{ $subtitle ?? 'LISTADO DE CONTRATOS REGISTRADOS' }}</p>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @if (
            ($filters['start_date'] ?? null) ||
                ($filters['end_date'] ?? null) ||
                ($filters['client'] ?? null) ||
                ($filters['location'] ?? null))
            @if ($filters['start_date'])
                Desde: {{ date('d/m/Y', strtotime($filters['start_date'])) }}
            @endif
            @if ($filters['end_date'])
                Hasta: {{ date('d/m/Y', strtotime($filters['end_date'])) }}
            @endif
            @if ($filters['client'])
                | Cliente: {{ $filters['client'] }}
            @endif
            @if ($filters['location'])
                | Sede: {{ $filters['location'] }}
            @endif
        @else
            Todos los créditos
        @endif
    </div>

    @if ($contracts->count() > 0)
        @php $total = 0; @endphp
        @foreach ($contracts as $index => $contract)
            @php $total += $contract->total; @endphp
            <div class="credit-block">
                <!-- Cabecera del crédito -->
                <div class="credit-header">
                    CRÉDITO {{ $index + 1 }} - {{ date('d/m/Y', strtotime($contract->date)) }}
                    @if ($contract->payment_date)
                        | Fecha de pago: {{ date('d/m/Y', strtotime($contract->payment_date)) }}
                    @endif
                </div>

                <!-- Información del cliente -->
                <div class="client-info">
                    <strong>CLIENTE:</strong>
                    {{ $contract->client->commercial_name ?? ($contract->client->contact_name ?? ($contract->client ?? 'varios')) }}
                    @if ($contract->client && $contract->client->document)
                        | Documento: {{ $contract->client->document }}
                    @endif
                </div>

                <!-- Detalles del crédito -->
                @if ($contract->agreement_details && $contract->agreement_details->count() > 0)
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="width: 60px;">Cant.</th>
                                <th style="width: 80px;">P. Unit.</th>
                                <th style="width: 80px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contract->agreement_details as $detail)
                                <tr>
                                    <td>
                                        @if ($detail->product)
                                            {{ $detail->product->name }}
                                        @else
                                            Producto eliminado
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-right">S/ {{ number_format($detail->unit_price, 2) }}</td>
                                    <td class="text-right">S/ {{ number_format($detail->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="padding: 10px; color: #6c757d; font-style: italic;">
                        Sin detalles registrados
                    </div>
                @endif

                <!-- Total del crédito -->
                <div class="credit-total">
                    TOTAL CONTRATO: S/ {{ number_format($contract->total, 2) }}
                </div>
            </div>
        @endforeach

        <!-- Total general -->
        <div class="grand-total">
            TOTAL GENERAL DE CONTRATO: S/ {{ number_format($total, 2) }}
        </div>
    @else
        <div class="no-data">
            <h3>No se encontraron contratos</h3>
            <p>No hay contratos que coincidan con los filtros aplicados.</p>
        </div>
    @endif
</body>

</html>
