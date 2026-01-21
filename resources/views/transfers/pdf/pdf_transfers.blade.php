<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>REPORTE DE TRANSFERENCIAS</title>
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

        .transfer-block {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: visible;
        }

        .transfer-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-weight: bold;
            font-size: 12px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .details-table th {
            text-align: center;
            vertical-align: middle;
            background-color: #6c757d;
            color: white;
            padding: 6px;
            font-size: 10px;
        }

        .details-table td {
            text-align: center;
            vertical-align: middle;
            padding: 5px 6px;
            border-bottom: 1px solid #eee;
        }

        .details-table tr:nth-child(even) {
            background-color: #f9f9f9;
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
        <h1>{{ $title ?? 'REPORTE DE TRANSFERENCIAS' }}</h1>
        <p>{{ $subtitle ?? 'LISTADO DE TRANSFERENCIAS' }}</p>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @if (
            $filters['from_date'] ||
                $filters['to_date'] ||
                $filters['from_location'] ||
                $filters['from_tank'] ||
                $filters['to_location'] ||
                $filters['to_tank']
        )
            @if ($filters['from_date'])
                Desde: {{ date('d/m/Y', strtotime($filters['from_date'])) }}
            @endif
            @if ($filters['to_date'])
                Hasta: {{ date('d/m/Y', strtotime($filters['to_date'])) }}
            @endif
            @if ($filters['from_location'])
                | Origen: {{ $filters['from_location'] }}
            @endif
            @if ($filters['from_tank'])
                | Tanque Origen: {{ $filters['from_tank'] }}
            @endif
            @if ($filters['to_location'])
                | Destino: {{ $filters['to_location'] }}
            @endif
            @if ($filters['to_tank'])
                | Tanque Destino: {{ $filters['to_tank'] }}
            @endif
        @else
            Todas las transferencias
        @endif
    </div>

    @if ($distribucion->count() > 0)
        @php $total = 0; @endphp
        @foreach ($distribucion as $index => $transfer)
            @php $total += $transfer->volume ?? 0; @endphp
            <div class="transfer-block">
                <!-- Cabecera -->
                <div class="transfer-header">
                    TRANSFERENCIA {{ $index + 1 }} - {{ $transfer->date }}
                </div>

                <!-- Detalles -->
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Producto</th>
                            <th>Unidad</th>
                            <th>Cantidad</th>
                            <th>Recibido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $transfer->from_tank->location->name ?? '---' }} -
                                {{ $transfer->from_tank->name ?? '---' }}</td>
                            <td>{{ $transfer->to_tank->location->name ?? '---' }} -
                                {{ $transfer->to_tank->name ?? '---' }}</td>
                            <td>{{ $transfer->product->name }}</td>
                            <td>{{ $transfer->unit ?? '---' }}</td>
                            <td>{{ $transfer->quantity ?? 0 }}</td>
                            <td>
                                @if ($transfer->received)
                                    <span>Sí</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>No se encontraron transferencias</h3>
            <p>No hay transferencias que coincidan con los filtros aplicados.</p>
        </div>
    @endif
</body>

</html>
