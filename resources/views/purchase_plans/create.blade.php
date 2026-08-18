@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Solicitud de Compra de Combustible</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchase_plans.index') }}" class="text-decoration-none text-muted">Planificación</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Nueva Solicitud</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <form action="{{ route('purchase_plans.store') }}" method="POST" id="planForm">
        @csrf

        <div class="row g-3">
            <!-- COLUMNA IZQUIERDA: DATOS GENERALES Y ESTADO FINANCIERO -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-geo-alt me-1"></i> 1. Información de la Sede</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $currentLocationName = $locations->firstWhere('id', $selectedLocationId)->name ?? ($locations->first()->name ?? 'Sin Sede');
                        @endphp
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Sede Solicitante <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ $currentLocationName }}" readonly>
                            <input type="hidden" name="location_id" id="location_id" value="{{ $selectedLocationId }}">
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_date" class="form-label text-dark fw-bold">Fecha Programada de Compra <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                   name="scheduled_date" id="scheduled_date" 
                                   value="{{ old('scheduled_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="available_money" class="form-label text-dark fw-bold">Dinero Total Disponible <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">S/</span>
                                <input type="number" step="0.01" class="form-control fw-bold text-primary bg-light @error('available_money') is-invalid @enderror" 
                                       name="available_money" id="available_money" 
                                       value="{{ old('available_money', number_format($availableMoney, 2, '.', '')) }}" readonly required>
                            </div>
                            <small class="text-muted">Monto total disponible para la compra (Bóveda + Efectivo + Tarjeta + Yape + Transferencias, etc.).</small>
                        </div>

                        <!-- DESGLOSE FINANCIERO EN TIEMPO REAL -->
                        <div class="card bg-light border-0 mb-3" style="border-radius: 8px;">
                            <div class="card-body p-2">
                                <span class="fw-bold text-dark text-uppercase d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-wallet2 text-primary me-1"></i> Desglose de Fondos de la Sede
                                </span>
                                
                                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                    <span class="text-dark fw-bold small"><i class="bi bi-safe text-info me-1"></i> En Bóveda:</span>
                                    <span class="fw-bold text-primary small" id="spanVaultMoney">S/ {{ number_format($vaultMoney, 2) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                    <span class="text-dark fw-bold small"><i class="bi bi-cash-stack text-success me-1"></i> En Cajas (Efectivo Turnos/General):</span>
                                    <span class="fw-bold text-success small" id="spanCashMoney">S/ {{ number_format($cashMoney, 2) }}</span>
                                </div>

                                <span class="fw-bold text-dark text-uppercase d-block mb-1" style="font-size: 0.68rem;">
                                    <i class="bi bi-credit-card me-1"></i> Por Métodos de Pago:
                                </span>
                                <div id="containerPaymentMethods">
                                    @forelse($paymentMethodsBreakdown as $pm)
                                        <div class="d-flex justify-content-between align-items-center mb-1 px-1 py-1 rounded {{ $pm['id'] == 1 ? 'bg-white border' : '' }}">
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-dot"></i> {{ $pm['name'] }}:
                                            </span>
                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">
                                                S/ {{ number_format($pm['amount'], 2) }}
                                            </span>
                                        </div>
                                    @empty
                                        <small class="text-muted fst-italic d-block" style="font-size: 0.7rem;">Sin métodos de pago configurados</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="notes" class="form-label text-dark fw-bold">Observaciones de la Solicitud</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Información relevante para gerencia sobre la rotación esperada o necesidad urgente...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- STOCK ACTUAL EN TANQUES -->
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-fuel-pump me-1"></i> Stock en Tanques</h6>
                        <span class="badge bg-secondary" id="tankCountBadge">{{ count($tanks) }} Tanques</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0 align-middle" id="tanksSummaryTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="font-size: 0.75rem;">Tanque</th>
                                        <th style="font-size: 0.75rem;">Combustible</th>
                                        <th style="font-size: 0.75rem;">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center" id="tanksSummaryTbody">
                                    @forelse($tanks as $t)
                                        <tr>
                                            <td class="fw-bold">{{ $t->name }}</td>
                                            <td>{{ $t->product->name ?? 'N/A' }}</td>
                                            <td class="fw-bold text-success">{{ number_format($t->stored_quantity, 2) }} Gls</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted py-3">No hay tanques registrados para esta sede.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: REQUERIMIENTO DE COMBUSTIBLES -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                    <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-cart-plus me-1"></i> 2. Detalle de Galones Requeridos por Combustible</h6>
                        <button type="button" class="btn btn-light btn-sm fw-bold text-primary" id="btnAgregarFila">
                            <i class="bi bi-plus-lg me-1"></i> Agregar Combustible
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-3">
                            <table class="table table-hover align-middle" id="tablaItems" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Combustible <span class="text-danger">*</span></th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Tanque Destino</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important; width: 140px;">Stock Actual (Gls)</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important; width: 180px;">Galones a Comprar <span class="text-danger">*</span></th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important; width: 140px;">P. Unit. Estimado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important; width: 60px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyItems">
                                    <!-- Si vienen tanques por defecto, precargamos los combustibles vinculados -->
                                    @php $rowIndex = 0; @endphp
                                    @if(count($tanks) > 0)
                                        @foreach($tanks as $t)
                                            <tr data-index="{{ $rowIndex }}">
                                                <td>
                                                    <select name="items[{{ $rowIndex }}][product_id]" class="form-select form-select-sm select-product" required>
                                                        <option value="{{ $t->product_id }}" selected>{{ $t->product->name ?? 'Combustible' }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $rowIndex }}][tank_id]" value="{{ $t->id }}">
                                                    <span class="badge bg-light text-dark border">{{ $t->name }}</span>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control form-control-sm text-end input-stock bg-light" 
                                                           name="items[{{ $rowIndex }}][current_stock]" value="{{ $t->stored_quantity }}" readonly>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" min="1" class="form-control text-end fw-bold text-primary input-gallons" 
                                                               name="items[{{ $rowIndex }}][requested_quantity]" placeholder="0.00" required>
                                                        <span class="input-group-text">Gls</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">S/</span>
                                                        <input type="number" step="0.01" class="form-control text-end input-price" 
                                                               name="items[{{ $rowIndex }}][unit_price_estimate]" placeholder="0.00">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @php $rowIndex++; @endphp
                                        @endforeach
                                    @else
                                        <tr data-index="0">
                                            <td>
                                                <select name="items[0][product_id]" class="form-select form-select-sm select-product" required>
                                                    <option value="">Seleccione combustible</option>
                                                    @foreach($fuelProducts as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="items[0][tank_id]" class="form-select form-select-sm select-tank">
                                                    <option value="">General / Sin tanque</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm text-end input-stock" 
                                                       name="items[0][current_stock]" value="0" placeholder="0.00">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="1" class="form-control text-end fw-bold text-primary input-gallons" 
                                                           name="items[0][requested_quantity]" placeholder="0.00" required>
                                                    <span class="input-group-text">Gls</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" step="0.01" class="form-control text-end input-price" 
                                                           name="items[0][unit_price_estimate]" placeholder="0.00">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end fw-bold">TOTAL GALONES SOLICITADOS:</th>
                                        <th class="text-end text-primary fw-bold fs-6" id="totalGallonsFooter">0.00 Gls</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-warning py-2 mb-4 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <div class="small">
                                Al guardar, la solicitud quedará en estado <strong>Pendiente de Aprobación</strong>. El usuario Gerente/Maestro recibirá la notificación en su bandeja para validar los galones y autorizar el pedido.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('purchase_plans.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-send-fill me-1"></i>Enviar Solicitud a Gerencia</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIndex = {{ $rowIndex > 0 ? $rowIndex : 1 }};
        const sedeSelect = document.getElementById('location_id');
        const availableMoneyInput = document.getElementById('available_money');
        const tbody = document.getElementById('tbodyItems');
        const totalGallonsFooter = document.getElementById('totalGallonsFooter');

        // Recalcular total galones
        function recalculateTotal() {
            let total = 0;
            document.querySelectorAll('.input-gallons').forEach(function(input) {
                total += parseFloat(input.value) || 0;
            });
            totalGallonsFooter.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Gls';
        }

        tbody.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-gallons')) {
                recalculateTotal();
            }
        });

        // Eliminar fila
        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-row');
            if (btn) {
                if (tbody.querySelectorAll('tr').length > 1) {
                    btn.closest('tr').remove();
                    recalculateTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debe existir al menos un combustible en la solicitud.'
                    });
                }
            }
        });

        // Agregar nueva fila manual
        document.getElementById('btnAgregarFila').addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.setAttribute('data-index', rowIndex);
            tr.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm select-product" required>
                        <option value="">Seleccione combustible</option>
                        @foreach($fuelProducts as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="items[${rowIndex}][tank_id]" class="form-select form-select-sm select-tank">
                        <option value="">General / Sin tanque</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control form-control-sm text-end input-stock" 
                           name="items[${rowIndex}][current_stock]" value="0" placeholder="0.00">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="1" class="form-control text-end fw-bold text-primary input-gallons" 
                               name="items[${rowIndex}][requested_quantity]" placeholder="0.00" required>
                        <span class="input-group-text">Gls</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">S/</span>
                        <input type="number" step="0.01" class="form-control text-end input-price" 
                               name="items[${rowIndex}][unit_price_estimate]" placeholder="0.00">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            rowIndex++;
        });

        // Cambio de sede por AJAX
        sedeSelect.addEventListener('change', function() {
            const locId = this.value;
            if (!locId) return;

            fetch(`{{ route('purchase_plans.sede_info') }}?location_id=${locId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        availableMoneyInput.value = (data.availableMoney || 0).toFixed(2);
                        
                        // Actualizar Desglose Financiero
                        document.getElementById('spanVaultMoney').textContent = 'S/ ' + (data.vaultMoney || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        document.getElementById('spanCashMoney').textContent = 'S/ ' + (data.cashMoney || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        
                        const containerPM = document.getElementById('containerPaymentMethods');
                        containerPM.innerHTML = '';
                        if (data.paymentMethods && data.paymentMethods.length > 0) {
                            data.paymentMethods.forEach(pm => {
                                const isCash = pm.id == 1 || pm.name.toLowerCase().includes('efectivo');
                                containerPM.innerHTML += `
                                    <div class="d-flex justify-content-between align-items-center mb-1 px-1 py-1 rounded ${isCash ? 'bg-white border' : ''}">
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-dot"></i> ${pm.name}:
                                        </span>
                                        <span class="fw-bold text-dark" style="font-size: 0.75rem;">
                                            S/ ${parseFloat(pm.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                        </span>
                                    </div>
                                `;
                            });
                        } else {
                            containerPM.innerHTML = '<small class="text-muted fst-italic d-block" style="font-size: 0.7rem;">Sin métodos de pago configurados</small>';
                        }

                        // Actualizar tabla de tanques resumen
                        const summaryTbody = document.getElementById('tanksSummaryTbody');
                        const tankBadge = document.getElementById('tankCountBadge');
                        summaryTbody.innerHTML = '';
                        tankBadge.textContent = `${data.tanks.length} Tanques`;

                        if (data.tanks.length > 0) {
                            tbody.innerHTML = '';
                            rowIndex = 0;
                            data.tanks.forEach(t => {
                                const prodName = t.product ? t.product.name : 'Combustible';
                                summaryTbody.innerHTML += `
                                    <tr>
                                        <td class="fw-bold">${t.name}</td>
                                        <td>${prodName}</td>
                                        <td class="fw-bold text-success">${parseFloat(t.stored_quantity).toFixed(2)} Gls</td>
                                    </tr>
                                `;

                                // Añadir a la tabla de solicitud
                                tbody.innerHTML += `
                                    <tr data-index="${rowIndex}">
                                        <td>
                                            <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm select-product" required>
                                                <option value="${t.product_id}" selected>${prodName}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" name="items[${rowIndex}][tank_id]" value="${t.id}">
                                            <span class="badge bg-light text-dark border">${t.name}</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm text-end input-stock bg-light" 
                                                   name="items[${rowIndex}][current_stock]" value="${t.stored_quantity}" readonly>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.01" min="1" class="form-control text-end fw-bold text-primary input-gallons" 
                                                       name="items[${rowIndex}][requested_quantity]" placeholder="0.00" required>
                                                <span class="input-group-text">Gls</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">S/</span>
                                                <input type="number" step="0.01" class="form-control text-end input-price" 
                                                       name="items[${rowIndex}][unit_price_estimate]" placeholder="0.00">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
                                rowIndex++;
                            });
                        } else {
                            summaryTbody.innerHTML = '<tr><td colspan="3" class="text-muted py-3">No hay tanques registrados para esta sede.</td></tr>';
                        }
                        recalculateTotal();
                    }
                })
                .catch(err => console.error('Error cargando información de sede:', err));
        });

        recalculateTotal();
    });
</script>
@endsection
