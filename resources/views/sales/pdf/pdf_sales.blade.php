<!-- filepath: resources/views/sales/pdf/pdf_sales.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>REPORTE DE VENTAS</title>
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

        .sale-block {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .sale-header {
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

        .sale-total {
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
        <h1>REPORTE DE VENTAS</h1>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @if (
            $filters['start_date'] ||
                $filters['end_date'] ||
                $filters['client'] ||
                $filters['location_id'] ||
                $filters['number'] ||
                $filters['type_sale'] ||
                $filters['type_voucher'] ||
                $filters['payment_method_id'] ||
                $filters['user_id']
        )
            @if ($filters['start_date'])
                Desde: {{ date('d/m/Y', strtotime($filters['start_date'])) }}
            @endif
            @if ($filters['end_date'])
                Hasta: {{ date('d/m/Y', strtotime($filters['end_date'])) }}
            @endif
            @if ($filters['client'])
                | Cliente: {{ $filters['client_name'] }}
            @endif
            @if ($filters['location_id'])
                | Sede: {{ $filters['location_name'] }}
            @endif
            @if ($filters['number'])
                | N° Comprobante: {{ $filters['number'] }}
            @endif
            @if (isset($filters['type_sale']))
                | Tipo de venta: {{ $filters['type_sale_name'] }}
            @endif
            @if ($filters['type_voucher'])
                | Tipo de comprobante: {{ $filters['type_voucher'] }}
            @endif
            @if ($filters['payment_method_id'])
                | Método de pago ID: {{ $filters['payment_method_name'] }}
            @endif
            @if ($filters['user_id'])
                | Usuario ID: {{ $filters['user_id'] }}
            @endif
        @else
            Todas las ventas
        @endif
    </div>
    @if ($sales->count() > 0)
        @foreach ($sales as $index => $sale)
            <div class="sale-block">
                <!-- Cabecera de la venta -->
                <div class="sale-header">
                    VENTA {{ $index + 1 }} - {{ date('d/m/Y', strtotime($sale->date)) }}
                    @if ($sale->payments && $sale->payments->count())
                        | {{ strtoupper($sale->payments->first()->voucher_type ?? '') }}:
                        {{ $sale->payments->first()->number ?? '---' }}
                    @endif
                </div>

                <!-- Información del cliente -->
                <div class="client-info">
                    <strong>CLIENTE:</strong>
                    {{ $sale->client->business_name ?? ($sale->client->contact_name ?? ($sale->client ?? 'varios')) }}
                    @if ($sale->client && $sale->client->document)
                        | Documento: {{ $sale->client->document }}
                    @endif
                </div>

                <!-- Detalles de la venta -->
                @if ($sale->sale_details && $sale->sale_details->count() > 0)
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
                            @foreach ($sale->sale_details as $detail)
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

                <!-- Total de la venta -->
                <div class="sale-total">
                    TOTAL VENTA: S/ {{ number_format($sale->total, 2) }}
                </div>
            </div>
        @endforeach

        <!-- Total general -->
        <div class="grand-total">
            TOTAL GENERAL DE VENTAS: S/ {{ number_format($total, 2) }}
        </div>
    @else
        <div class="no-data">
            <h3>No se encontraron ventas</h3>
            <p>No hay ventas que coincidan con los filtros aplicados.</p>
        </div>
    @endif
</body>

</html>
