<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #465fff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a202c;
            text-transform: uppercase;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #465fff;
            margin-top: 4px;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .meta-table {
            width: 100%;
        }

        .meta-table td {
            padding: 3px 6px;
            font-size: 10.5px;
        }

        .label {
            font-weight: bold;
            color: #4a5568;
            width: 25%;
        }

        .value {
            color: #1a202c;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #2d3748;
            color: white;
            padding: 5px 8px;
            margin-top: 15px;
            margin-bottom: 0;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .data-table th {
            background-color: #4a5568;
            color: white;
            padding: 6px;
            font-size: 10px;
            text-align: center;
            border: 1px solid #cbd5e1;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-approved { background-color: #dbeafe; color: #1e40af; }
        .badge-completed { background-color: #d1fae5; color: #065f46; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }

        .notes-box {
            background-color: #f1f5f9;
            border-left: 3px solid #64748b;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 10px;
        }

        .signatures-table {
            width: 100%;
            margin-top: 45px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="company-title">SISTEMA AVA - GESTIÓN DE COMBUSTIBLES</div>
                <div class="doc-title">SOLICITUD Y PLANIFICACIÓN DE COMPRA #{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div style="font-size: 9px; color: #666;">Fecha de Emisión: {{ date('d/m/Y H:i') }}</div>
                <div style="font-size: 9px; color: #666;">Estado: 
                    <span class="badge badge-{{ $plan->status }}">{{ strtoupper($plan->status) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Sede / Estación:</td>
                <td class="value font-bold">{{ $plan->location->name ?? '---' }}</td>
                <td class="label">Fecha Programada:</td>
                <td class="value font-bold">{{ $plan->scheduled_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Proveedor:</td>
                <td class="value font-bold">{{ $plan->supplier ? $plan->supplier->company_name : 'No asignado' }}</td>
                <td class="label">Dinero Disponible:</td>
                <td class="value font-bold" style="color: #059669;">S/ {{ number_format($plan->available_money, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Solicitante (Admin):</td>
                <td class="value">{{ $plan->user->name ?? '---' }}</td>
                <td class="label">Aprobado por (Master):</td>
                <td class="value font-bold">{{ $plan->reviewer ? $plan->reviewer->name : 'Pendiente' }}</td>
            </tr>
            @if($plan->reviewed_at)
            <tr>
                <td class="label">Fecha Aprobación:</td>
                <td class="value" colspan="3">{{ $plan->reviewed_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($plan->notes)
    <div class="notes-box">
        <strong>Observaciones de la Solicitud:</strong> {{ $plan->notes }}
    </div>
    @endif

    @if($plan->manager_notes)
    <div class="notes-box" style="border-left-color: #3b82f6;">
        <strong>Comentarios de Gerencia / Maestro:</strong> {{ $plan->manager_notes }}
    </div>
    @endif

    <div class="section-title">DETALLE DE COMBUSTIBLES SOLICITADOS Y DESPACHO</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ÍTEM / COMBUSTIBLE</th>
                <th>TANQUE</th>
                <th>STOCK ACTUAL</th>
                <th>GALONES SOLICITADOS</th>
                <th>GALONES AUTORIZADOS</th>
                <th>COMPRA REAL</th>
                <th>CUMPLIMIENTO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plan->details as $det)
                <tr>
                    <td class="font-bold">{{ $det->product->name ?? '---' }}</td>
                    <td class="text-center">{{ $det->tank->name ?? 'GENERAL' }}</td>
                    <td class="text-right">{{ number_format($det->current_stock, 2) }} Gls</td>
                    <td class="text-right font-bold">{{ number_format($det->requested_quantity, 2) }} Gls</td>
                    <td class="text-right font-bold">
                        {{ $det->approved_quantity !== null ? number_format($det->approved_quantity, 2) . ' Gls' : '---' }}
                    </td>
                    <td class="text-right">
                        {{ number_format($det->purchased_quantity, 2) }} Gls
                    </td>
                    <td class="text-center font-bold">
                        {{ $det->compliance_rate }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" class="text-right">TOTALES:</td>
                <td class="text-right">{{ number_format($plan->total_requested_gallons, 2) }} Gls</td>
                <td class="text-right">{{ number_format($plan->total_approved_gallons, 2) }} Gls</td>
                <td class="text-right">{{ number_format($plan->total_purchased_gallons, 2) }} Gls</td>
                <td class="text-center">{{ $plan->effective_compliance }}%</td>
            </tr>
        </tfoot>
    </table>

    @if($plan->justification_notes)
    <div class="notes-box" style="border-left-color: #dc2626; background-color: #fff1f2;">
        <strong style="color: #991b1b;">Justificación de Cumplimiento Parcial / Brecha de Compra:</strong><br>
        {{ $plan->justification_notes }}
    </div>
    @endif

    <table class="signatures-table">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">
                    {{ $plan->user->name ?? 'ADMINISTRADOR DE SEDE' }}<br>
                    <span style="font-weight: normal; font-size: 9px; color: #666;">Firma Solicitante (Admin Sede)</span>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">
                    {{ $plan->reviewer->name ?? 'GERENCIA / DIRECCIÓN' }}<br>
                    <span style="font-weight: normal; font-size: 9px; color: #666;">Firma de Aprobación (Gerencia)</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
