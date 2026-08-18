@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Solicitud de Compra #{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase_plans.index') }}" class="text-decoration-none text-muted">Planificación</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Detalle #{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('purchase_plans.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver al Listado</a>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase_plans.pdf', $plan->id) }}" target="_blank" class="btn btn-danger btn-sm fw-medium shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Exportar PDF
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 text-white fw-bold"><i class="bi bi-info-circle me-1"></i> Resumen de la Solicitud</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Estado:</span>
                            <span>
                                @if($plan->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($plan->status === 'approved')
                                    <span class="badge bg-info text-white">Aprobado</span>
                                @elseif($plan->status === 'completed')
                                    <span class="badge bg-success">Completado 100%</span>
                                @elseif($plan->status === 'partially_completed')
                                    <span class="badge bg-secondary">Parcial ({{ $plan->effective_compliance }}%)</span>
                                @elseif($plan->status === 'rejected')
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Sede:</span>
                            <span class="fw-bold text-dark">{{ $plan->location->name ?? '---' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Fecha Programada:</span>
                            <span class="fw-bold text-dark">{{ $plan->scheduled_date->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Dinero Reportado Disponible:</span>
                            <span class="fw-bold text-primary">S/ {{ number_format($plan->available_money, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Registrado por:</span>
                            <span>{{ $plan->user->name ?? '---' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Fecha de Registro:</span>
                            <span>{{ $plan->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @if($plan->reviewer)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Revisado por (Gerencia):</span>
                                <span class="fw-bold text-success">{{ $plan->reviewer->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Fecha de Revisión:</span>
                                <span>{{ $plan->reviewed_at ? $plan->reviewed_at->format('d/m/Y H:i') : '---' }}</span>
                            </li>
                        @endif
                    </ul>

                    @if($plan->notes)
                        <div class="mt-3">
                            <label class="fw-bold text-dark small">Observaciones del Administrador:</label>
                            <div class="p-2 bg-light rounded text-muted small border">{{ $plan->notes }}</div>
                        </div>
                    @endif

                    @if($plan->manager_notes)
                        <div class="mt-3">
                            <label class="fw-bold text-dark small">Comentarios de Gerencia:</label>
                            <div class="p-2 bg-light rounded text-muted small border">{{ $plan->manager_notes }}</div>
                        </div>
                    @endif

                    @if($plan->justification_notes)
                        <div class="mt-3">
                            <label class="fw-bold text-danger small">Justificación de Desviación en Compra Real:</label>
                            <div class="p-2 bg-danger bg-opacity-10 text-danger rounded small border border-danger">{{ $plan->justification_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0 text-white fw-bold"><i class="bi bi-list-check me-1"></i> Detalle de Combustibles</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                            <thead class="text-center">
                                <tr>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Combustible</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Tanque</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Stock Inicial</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Solicitado</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Autorizado</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Comprado Real</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Eficacia</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($plan->details as $det)
                                    <tr>
                                        <td class="text-start fw-bold text-dark">{{ $det->product->name ?? '---' }}</td>
                                        <td>{{ $det->tank->name ?? 'General' }}</td>
                                        <td class="text-success fw-medium">{{ number_format($det->current_stock, 2) }} Gls</td>
                                        <td class="text-primary fw-bold">{{ number_format($det->requested_quantity, 2) }} Gls</td>
                                        <td class="fw-bold">
                                            {{ $det->approved_quantity !== null ? number_format($det->approved_quantity, 2) . ' Gls' : '---' }}
                                        </td>
                                        <td class="text-info fw-bold">
                                            {{ number_format($det->purchased_quantity, 2) }} Gls
                                        </td>
                                        <td>
                                            @php
                                                $rate = $det->compliance_rate;
                                                $bClass = $rate >= 100 ? 'bg-success' : ($rate >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                                            @endphp
                                            <span class="badge {{ $bClass }}">{{ $rate }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light text-center fw-bold">
                                <tr>
                                    <th colspan="3" class="text-end">TOTALES:</th>
                                    <th class="text-primary">{{ number_format($plan->total_requested_gallons, 2) }} Gls</th>
                                    <th>{{ number_format($plan->total_approved_gallons, 2) }} Gls</th>
                                    <th class="text-info">{{ number_format($plan->total_purchased_gallons, 2) }} Gls</th>
                                    <th>
                                        <span class="badge {{ $plan->effective_compliance >= 100 ? 'bg-success' : ($plan->effective_compliance >= 70 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $plan->effective_compliance }}% Global
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
