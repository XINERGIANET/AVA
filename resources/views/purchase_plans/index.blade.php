@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-calendar2-check me-2 text-primary"></i>Planificación de Compras de Combustible</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Planificación</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">

    <!-- TARJETAS DE INDICADORES / KPIS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #465fff !important;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Total Solicitudes</span>
                            <h3 class="mb-0 text-dark fw-bold mt-1">{{ $totalPlans }}</h3>
                            <small class="text-muted"><span class="text-warning fw-bold">{{ $pendingPlans }}</span> pendientes</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(70, 95, 255, 0.1);">
                            <i class="bi bi-clipboard-data text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #10b981 !important;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Tasa de Aprobación</span>
                            <h3 class="mb-0 text-dark fw-bold mt-1">{{ $confirmationRate }}%</h3>
                            <small class="text-muted"><span class="text-success fw-bold">{{ $approvedPlans }}</span> confirmadas por Gerencia</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #06b6d4 !important;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Eficacia de Compra</span>
                            <h3 class="mb-0 text-dark fw-bold mt-1">{{ $avgCompliance }}%</h3>
                            <small class="text-muted">Cumplimiento galones solicitados</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(6, 182, 212, 0.1);">
                            <i class="bi bi-speedometer2 text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #ef4444 !important;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Rechazadas</span>
                            <h3 class="mb-0 text-dark fw-bold mt-1">{{ $rejectedPlans }}</h3>
                            <small class="text-muted">Desestimadas o replanificadas</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA Y FILTROS -->
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar de Filtros -->
            <form action="{{ route('purchase_plans.index') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                        <select name="location_id" id="location_id" class="form-select form-select-sm">
                            <option value="">Todas las Sedes</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request()->location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Estado</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">Todos los Estados</option>
                            <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>Pendiente de Aprobación</option>
                            <option value="approved" {{ request()->status == 'approved' ? 'selected' : '' }}>Aprobado por Gerencia</option>
                            <option value="completed" {{ request()->status == 'completed' ? 'selected' : '' }}>Comprado (100%)</option>
                            <option value="partially_completed" {{ request()->status == 'partially_completed' ? 'selected' : '' }}>Comprado Parcial (&lt; 100%)</option>
                            <option value="rejected" {{ request()->status == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Desde</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->start_date }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Hasta</label>
                        <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->end_date }}">
                    </div>
                    <div class="col-md-3 d-flex gap-1">
                        <button class="btn btn-primary btn-sm w-100" type="submit"><i class="bi bi-search me-1"></i>Filtrar</button>
                        <a href="{{ route('purchase_plans.index') }}" class="btn btn-warning btn-sm w-100 text-white"><i class="bi bi-eraser-fill me-1"></i>Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-0 text-dark fw-bold">Listado de Solicitudes y Planificaciones</h5>
                </div>
                <div>
                    <a href="{{ route('purchase_plans.create') }}" class="btn btn-primary btn-sm fw-medium shadow-sm" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Solicitud de Compra
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">ID</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Proveedor</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Fecha Prog.</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Dinero Disp.</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Galones Solicitados</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Galones Comprados</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Eficacia / Cumpl.</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important; width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($plans as $plan)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td class="fw-bold text-primary">#{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-dark fw-bold">{{ $plan->location->name ?? '---' }}</td>
                                <td class="text-dark">
                                    @if($plan->supplier)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-truck me-1 text-primary"></i>{{ $plan->supplier->company_name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">No asignado</span>
                                    @endif
                                </td>
                                <td class="text-dark">{{ $plan->scheduled_date->format('d/m/Y') }}</td>
                                <td class="text-dark fw-bold">S/ {{ number_format($plan->available_money, 2) }}</td>
                                <td class="text-dark">
                                    <span class="badge bg-light text-dark border">
                                        {{ number_format($plan->total_requested_gallons, 2) }} Gls
                                    </span>
                                </td>
                                <td class="text-dark">
                                    @if(in_array($plan->status, ['completed', 'partially_completed']))
                                        <span class="badge bg-light text-primary border fw-bold">
                                            {{ number_format($plan->total_purchased_gallons, 2) }} Gls
                                        </span>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($plan->status, ['completed', 'partially_completed']))
                                        @php
                                            $eff = $plan->effective_compliance;
                                            $badgeClass = $eff >= 100 ? 'bg-success' : ($eff >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                                        @endphp
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge {{ $badgeClass }} fw-bold mb-1">{{ $eff }}%</span>
                                            <div class="progress" style="height: 6px; width: 80px;">
                                                <div class="progress-bar {{ $eff >= 100 ? 'bg-success' : ($eff >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                                                     role="progressbar" style="width: {{ min($eff, 100) }}%;"></div>
                                            </div>
                                            @if($eff < 100 && $plan->justification_notes)
                                                <small class="text-muted mt-1" title="{{ $plan->justification_notes }}" style="cursor: pointer; font-size: 0.7rem;">
                                                    <i class="bi bi-info-circle text-primary"></i> Con Justificación
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">Pendiente compra</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plan->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>
                                    @elseif($plan->status === 'approved')
                                        <span class="badge bg-info text-white"><i class="bi bi-check2-circle me-1"></i>Aprobado</span>
                                    @elseif($plan->status === 'completed')
                                        <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Completado 100%</span>
                                    @elseif($plan->status === 'partially_completed')
                                        <span class="badge bg-secondary"><i class="bi bi-pie-chart me-1"></i>Parcial ({{ $plan->effective_compliance }}%)</span>
                                    @elseif($plan->status === 'rejected')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rechazado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- VER DETALLE -->
                                        <a href="{{ route('purchase_plans.show', $plan->id) }}" class="btn btn-sm btn-outline-info" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- DESCARGAR PDF -->
                                        <a href="{{ route('purchase_plans.pdf', $plan->id) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Reporte PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>

                                        <!-- EVALUAR / APROBAR (ÚNICAMENTE MASTER) -->
                                        @if($isMaster && $plan->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success btn-evaluar" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalReview-{{ $plan->id }}" 
                                                    title="Revisar / Aprobar Master">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                        @endif

                                        <!-- REGISTRAR COMPRA REAL & JUSTIFICACIÓN -->
                                        @if(in_array($plan->status, ['approved', 'partially_completed', 'completed']))
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalPurchased-{{ $plan->id }}" 
                                                    title="Registrar / Actualizar Compra Real">
                                                <i class="bi bi-cart-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    No se encontraron solicitudes de compras registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Mostrando {{ $plans->firstItem() ?? 0 }} a {{ $plans->lastItem() ?? 0 }} de {{ $plans->total() }} registros</small>
                <div>{{ $plans->links() }}</div>
            </div>
        </div>
    </div>

    <!-- MODALES DE REVISIÓN MASTER Y COMPRA REAL (FUERA DE LA TABLA) -->
    @foreach($plans as $plan)
        @if($isMaster)
        <div class="modal fade text-start" id="modalReview-{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('purchase_plans.review', $plan->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title text-white"><i class="bi bi-shield-check me-2"></i>Evaluación de Solicitud de Compra #{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2 mb-3">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <strong>Sede:</strong> {{ $plan->location->name }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Fecha Programada:</strong> {{ $plan->scheduled_date->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Dinero Disponible:</strong> <span class="fw-bold text-success">S/ {{ number_format($plan->available_money, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="supplier_id_{{ $plan->id }}" class="form-label text-dark fw-bold mb-0">
                                        <i class="bi bi-truck text-primary me-1"></i> Proveedor Asignado / Autorizado:
                                    </label>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-bold btn-open-quick-supplier" data-plan-id="{{ $plan->id }}" style="font-size: 0.75rem;">
                                        <i class="bi bi-plus-circle me-1"></i>Nuevo Proveedor
                                    </button>
                                </div>
                                <div class="input-group input-group-sm">
                                    <select name="supplier_id" id="supplier_id_{{ $plan->id }}" class="form-select select-review-supplier">
                                        <option value="">-- Sin Proveedor Asignado --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ $plan->supplier_id == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->company_name }} {{ $sup->document ? '('.$sup->document.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-primary btn-open-quick-supplier" data-plan-id="{{ $plan->id }}" title="Agregar Proveedor">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">Puede confirmar el proveedor sugerido por la sede o seleccionar/crear otro para esta orden.</small>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">Cantidades Solicitadas por Combustible:</h6>
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Combustible</th>
                                        <th>Stock Tanque</th>
                                        <th>Galones Solicitados</th>
                                        <th style="width: 180px;">Galones Autorizados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->details as $det)
                                        <tr>
                                            <td class="fw-bold">{{ $det->product->name }}</td>
                                            <td class="text-center">{{ number_format($det->current_stock, 2) }} Gls</td>
                                            <td class="text-center fw-bold text-primary">{{ number_format($det->requested_quantity, 2) }} Gls</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" class="form-control text-end fw-bold" 
                                                           name="approved_quantities[{{ $det->id }}]" 
                                                           value="{{ $det->approved_quantity !== null ? $det->approved_quantity : $det->requested_quantity }}">
                                                    <span class="input-group-text">Gls</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Comentarios / Observaciones de Gerencia:</label>
                                <textarea name="manager_notes" class="form-control" rows="2" placeholder="Indicaciones para el despacho o motivos del ajuste...">{{ $plan->manager_notes }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-dark">Decisión:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="actionApprove{{ $plan->id }}" value="approve" checked>
                                        <label class="form-check-label text-success fw-bold" for="actionApprove{{ $plan->id }}">
                                            <i class="bi bi-check-circle me-1"></i>Aprobar / Confirmar Solicitud
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="actionReject{{ $plan->id }}" value="reject">
                                        <label class="form-check-label text-danger fw-bold" for="actionReject{{ $plan->id }}">
                                            <i class="bi bi-x-circle me-1"></i>Rechazar Solicitud
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Guardar Decisión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="modal fade text-start" id="modalPurchased-{{ $plan->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('purchase_plans.purchased', $plan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white"><i class="bi bi-cart-check me-2"></i>Actualizar Compra Real y Eficacia #{{ str_pad($plan->id, 5, '0', STR_PAD_LEFT) }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border py-2 mb-2 d-flex flex-wrap gap-3 align-items-center small">
                                <div><strong>Sede:</strong> {{ $plan->location->name }}</div>
                                <div><strong>Proveedor:</strong> 
                                    @if($plan->supplier)
                                        <span class="badge bg-dark text-white"><i class="bi bi-truck me-1"></i>{{ $plan->supplier->company_name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No asignado</span>
                                    @endif
                                </div>
                                <div><strong>Fecha Programada:</strong> {{ $plan->scheduled_date->format('d/m/Y') }}</div>
                            </div>
                            <p class="text-muted small mb-3">
                                Ingrese los galones efectivamente comprados y descargados en estación. Si la cantidad comprada es inferior a lo solicitado/autorizado, registre la justificación correspondiente.
                            </p>

                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Combustible</th>
                                        <th>Autorizado</th>
                                        <th style="width: 200px;">Galones Comprados</th>
                                        <th>% Cumplimiento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->details as $det)
                                        @php
                                            $authQty = $det->approved_quantity !== null ? $det->approved_quantity : $det->requested_quantity;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $det->product->name }}</td>
                                            <td class="text-center fw-bold">{{ number_format($authQty, 2) }} Gls</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" class="form-control text-end fw-bold input-purchased-qty" 
                                                           name="purchased_quantities[{{ $det->id }}]" 
                                                           data-target="{{ $authQty }}"
                                                           value="{{ $det->purchased_quantity }}">
                                                    <span class="input-group-text">Gls</span>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-primary">
                                                <span class="span-compliance-calc">{{ $det->compliance_rate }}%</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- SUBIDA DE VOUCHERS / COMPROBANTES -->
                            <div class="card bg-light border-0 mb-3 p-3" style="border-radius: 8px;">
                                <label class="form-label fw-bold text-dark mb-1">
                                    <i class="bi bi-paperclip text-primary me-1"></i> Comprobantes / Vouchers de Pago y Descarga:
                                </label>
                                <input type="file" name="voucher_files[]" class="form-control form-control-sm" multiple accept="image/*,application/pdf">
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    Puede adjuntar fotos o capturas de vouchers bancarios, boletas, facturas o guías de remisión (JPG, PNG, PDF).
                                </small>

                                @if(!empty($plan->voucher_images) && count($plan->voucher_images) > 0)
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="fw-bold text-dark small d-block mb-1">Comprobantes ya adjuntados:</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($plan->voucher_images as $vImg)
                                                @php
                                                    $isPdf = str_ends_with(strtolower($vImg['path'] ?? ''), '.pdf');
                                                @endphp
                                                <a href="{{ asset('storage/' . $vImg['path']) }}" target="_blank" class="btn btn-sm btn-outline-dark py-1 px-2 d-inline-flex align-items-center" style="font-size: 0.75rem;">
                                                    <i class="bi {{ $isPdf ? 'bi-file-earmark-pdf text-danger' : 'bi-image text-primary' }} me-1"></i>
                                                    {{ Str::limit($vImg['name'] ?? 'Comprobante', 18) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Justificación de Desviación / Compra Parcial:</label>
                                <textarea name="justification_notes" class="form-control" rows="2" placeholder="Ej: Se solicitaron 10,000 gls pero solo se compraron 7,000 gls (70%) debido a falta de cupo de crédito con proveedor / quiebre de stock en planta / restricción de liquidez...">{{ $plan->justification_notes }}</textarea>
                                <small class="text-muted">Requerido si la compra fue inferior al 100% de lo solicitado.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Actualizar Eficacia y Guardar Comprobantes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($isMaster)
<!-- MODAL PARA AGREGAR PROVEEDOR RÁPIDO DESDE BANDEJA / EVALUACIÓN -->
<div class="modal fade" id="quickProviderModalIndex" tabindex="-1" aria-labelledby="quickProviderModalIndexLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white fw-bold" id="quickProviderModalIndexLabel"><i class="bi bi-truck me-2"></i>Registrar Nuevo Proveedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="quickProviderFormIndex">
                    <div class="mb-3">
                        <label for="quick_index_document" class="form-label text-dark fw-bold">RUC / DNI <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="quick_index_document" name="document" placeholder="Ingrese RUC o DNI" maxlength="20" required>
                            <button class="btn btn-primary" type="button" id="btnSearchSunatIndex" onclick="searchQuickSupplierDocIndex()">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                        <small class="text-muted">Presione Buscar para autocompletar la Razón Social vía SUNAT/RENIEC.</small>
                    </div>
                    <div class="mb-3">
                        <label for="quick_index_company_name" class="form-label text-dark fw-bold">Razón Social / Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_index_company_name" name="company_name" placeholder="Razón social o denominación" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quick_index_commercial_name" class="form-label text-dark fw-bold">Nombre Comercial</label>
                            <input type="text" class="form-control" id="quick_index_commercial_name" name="commercial_name" placeholder="Opcional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quick_index_phone" class="form-label text-dark fw-bold">Teléfono</label>
                            <input type="text" class="form-control" id="quick_index_phone" name="phone" placeholder="Opcional">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveQuickSupplierIndex">
                    <i class="bi bi-check-circle me-1"></i> Guardar y Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    let activeReviewPlanId = null;

    function searchQuickSupplierDocIndex() {
        const doc = document.getElementById('quick_index_document').value.trim();
        const companyInput = document.getElementById('quick_index_company_name');
        const btn = document.getElementById('btnSearchSunatIndex');

        if (!/^\d{8}$|^\d{11}$/.test(doc)) {
            if (typeof ToastError !== 'undefined') {
                ToastError.fire({ text: 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.' });
            } else {
                alert('El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.');
            }
            return;
        }

        const originalBtnHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btn.disabled = true;

        fetch(`{{ url('sunat/consultar') }}?doc=${doc}`)
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;

                if (data && (data.razon_social || data.nombre || data.nombres)) {
                    const name = data.razon_social || `${data.nombres || ''} ${data.apellido_paterno || ''} ${data.apellido_materno || ''}`.trim();
                    companyInput.value = name;
                    if (typeof ToastMessage !== 'undefined') {
                        ToastMessage.fire({ icon: 'success', text: 'Datos encontrados con éxito' });
                    }
                } else if (data && data.error) {
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: data.error });
                    } else {
                        alert(data.error);
                    }
                } else {
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: 'No se encontraron datos para el documento ingresado.' });
                    } else {
                        alert('No se encontraron datos para el documento ingresado.');
                    }
                }
            })
            .catch(err => {
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;
                console.error('Error consultando SUNAT:', err);
                if (typeof ToastError !== 'undefined') {
                    ToastError.fire({ text: 'Error de conexión al consultar el documento.' });
                } else {
                    alert('Error de conexión al consultar el documento.');
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Cálculo dinámico de % al escribir cantidades compradas
        document.querySelectorAll('.input-purchased-qty').forEach(function(input) {
            input.addEventListener('input', function() {
                const target = parseFloat(this.dataset.target) || 0;
                const val = parseFloat(this.value) || 0;
                const span = this.closest('tr').querySelector('.span-compliance-calc');
                if (target > 0) {
                    const pct = Math.round((val / target) * 100);
                    span.textContent = pct + '%';
                }
            });
        });

        // Abrir modal de proveedor desde modal de evaluación
        document.querySelectorAll('.btn-open-quick-supplier').forEach(function(btn) {
            btn.addEventListener('click', function() {
                activeReviewPlanId = this.dataset.planId;
                const modalQuickEl = document.getElementById('quickProviderModalIndex');
                if (modalQuickEl) {
                    const bsModal = new bootstrap.Modal(modalQuickEl);
                    bsModal.show();
                }
            });
        });

        // Guardar proveedor desde modal
        const btnSaveSupplier = document.getElementById('btnSaveQuickSupplierIndex');
        if (btnSaveSupplier) {
            btnSaveSupplier.addEventListener('click', function() {
                const doc = document.getElementById('quick_index_document').value.trim();
                const compName = document.getElementById('quick_index_company_name').value.trim();
                const commName = document.getElementById('quick_index_commercial_name').value.trim();
                const phone = document.getElementById('quick_index_phone').value.trim();

                if (!doc || !compName) {
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: 'RUC/DNI y Razón Social son campos obligatorios.' });
                    } else {
                        alert('RUC/DNI y Razón Social son campos obligatorios.');
                    }
                    return;
                }

                const originalBtnHtml = btnSaveSupplier.innerHTML;
                btnSaveSupplier.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
                btnSaveSupplier.disabled = true;

                fetch('{{ route('suppliers.saveSupplier') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        document: doc,
                        company_name: compName,
                        commercial_name: commName,
                        phone: phone
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btnSaveSupplier.innerHTML = originalBtnHtml;
                    btnSaveSupplier.disabled = false;

                    if (data.success && data.supplier) {
                        // Agregar el nuevo proveedor a todos los selectores de revisión
                        document.querySelectorAll('.select-review-supplier').forEach(function(select) {
                            const opt = document.createElement('option');
                            opt.value = data.supplier.id;
                            opt.textContent = `${data.supplier.company_name} (${data.supplier.document})`;
                            select.appendChild(opt);
                        });

                        // Seleccionarlo en el modal que abrió la creación
                        if (activeReviewPlanId) {
                            const targetSelect = document.getElementById(`supplier_id_${activeReviewPlanId}`);
                            if (targetSelect) {
                                targetSelect.value = data.supplier.id;
                            }
                        }

                        if (typeof ToastMessage !== 'undefined') {
                            ToastMessage.fire({ icon: 'success', text: data.message || 'Proveedor registrado correctamente.' });
                        }

                        // Limpiar formulario y cerrar modal
                        document.getElementById('quickProviderFormIndex').reset();
                        const modalEl = document.getElementById('quickProviderModalIndex');
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) bsModal.hide();
                    } else {
                        if (typeof ToastError !== 'undefined') {
                            ToastError.fire({ text: data.message || 'Error al guardar el proveedor.' });
                        } else {
                            alert(data.message || 'Error al guardar el proveedor.');
                        }
                    }
                })
                .catch(err => {
                    btnSaveSupplier.innerHTML = originalBtnHtml;
                    btnSaveSupplier.disabled = false;
                    console.error('Error guardando proveedor:', err);
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: 'Error inesperado al guardar el proveedor.' });
                    } else {
                        alert('Error inesperado al guardar el proveedor.');
                    }
                });
            });
        }
    });
</script>
@endsection
