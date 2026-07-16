@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-wallet2 me-2 text-primary"></i>Cuentas por Pagar</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.create') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Cuentas por Pagar</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar de Filtros -->
            <form action="{{ route('supplier_payments.index') }}" method="GET" id="fromFilter" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->start_date ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                        <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->end_date ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Proveedor</label>
                        <select class="form-select form-select-sm" id="supplier_id" name="supplier_id">
                            <option value="">Todos los proveedores</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request()->supplier_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check pt-2">
                            <input class="form-check-input" type="checkbox" name="only_pending" id="only_pending" value="1" {{ $onlyPending === '1' ? 'checked' : '' }}>
                            <label class="form-check-label text-dark fw-medium small" for="only_pending">
                                Solo con saldo pendiente
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                        <a href="{{ route('supplier_payments.index') }}" class="btn btn-warning btn-sm w-100 text-white"><i class="bi bi-eraser-fill me-1"></i>Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-end mb-3">
                <h5 class="mb-0 text-dark fw-bold">Deuda total mostrada: <span class="text-danger">S/ {{ number_format($totalDebt, 2, '.', ',') }}</span></h5>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N° Comprobante</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Proveedor</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total (S/)</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Pagado (S/)</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Saldo (S/)</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($purchases as $purchase)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td class="text-dark fw-medium">{{ $purchase->invoice_number ?? '---' }}</td>
                                <td class="text-dark">{{ $purchase->supplier->company_name ?? 'Sin proveedor' }}</td>
                                <td class="text-dark">{{ $purchase->date->format('d/m/Y') }}</td>
                                <td class="text-dark fw-bold">{{ number_format($purchase->total, 2) }}</td>
                                <td class="text-success fw-bold">{{ number_format($purchase->paid_amount, 2) }}</td>
                                <td class="fw-bold {{ $purchase->balance > 0.009 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($purchase->balance, 2) }}
                                </td>
                                <td>
                                    @if ($purchase->balance <= 0.009)
                                        <span class="badge bg-success">Pagado</span>
                                    @elseif ($purchase->paid_amount > 0)
                                        <span class="badge bg-warning text-dark">Parcial</span>
                                    @else
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    @if ($purchase->balance > 0.009)
                                        <button class="btn btn-sm btn-primary text-white btn-registrar-pago fw-medium" style="border-radius: 4px; padding: 0.25rem 0.5rem;"
                                            data-bs-toggle="modal" data-bs-target="#registrarPagoModal"
                                            data-purchase-id="{{ $purchase->id }}"
                                            data-invoice="{{ $purchase->invoice_number ?? '---' }}"
                                            data-supplier="{{ $purchase->supplier->company_name ?? 'Sin proveedor' }}"
                                            data-balance="{{ number_format($purchase->balance, 2, '.', '') }}">
                                            <i class="bi bi-cash-coin me-1"></i>Pagar
                                        </button>
                                    @else
                                        <span class="text-muted small"><i class="bi bi-check2-circle text-success me-1"></i>Sin saldo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No hay compras que coincidan con el filtro.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $purchases->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="registrarPagoModal" tabindex="-1" aria-labelledby="registrarPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="registrarPagoModalLabel">Registrar pago a proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pago_purchase_id">
                <div class="alert alert-info border-0 py-3 mb-4 rounded-3 shadow-sm d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted mb-1">Proveedor: <strong id="pago_supplier" class="text-dark"></strong></div>
                        <div class="small text-muted">Comprobante: <strong id="pago_invoice" class="text-dark"></strong></div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted mb-1">Saldo pendiente</div>
                        <h4 id="pago_saldo_label" class="mb-0 text-danger fw-bold">S/ 0.00</h4>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Monto a pagar</label>
                    <div class="input-group">
                        <span class="input-group-text">S/</span>
                        <input type="number" min="0.01" step="0.01" class="form-control" id="pago_amount">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Método de pago</label>
                    <select class="form-select" id="pago_payment_method">
                        <option value="">Seleccione un método</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Fecha</label>
                    <input type="date" class="form-control" id="pago_date" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Observación</label>
                    <input type="text" maxlength="255" class="form-control" id="pago_observation" placeholder="Ej. Depósito, Cheque N°, etc.">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btnGuardarPago">Guardar Pago</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.getElementById('only_pending').addEventListener('change', function() {
            document.getElementById('fromFilter').submit();
        });

        document.querySelectorAll('.btn-registrar-pago').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('pago_purchase_id').value = this.dataset.purchaseId;
                document.getElementById('pago_supplier').textContent = this.dataset.supplier;
                document.getElementById('pago_invoice').textContent = this.dataset.invoice;
                document.getElementById('pago_saldo_label').textContent = 'S/ ' + this.dataset.balance;
                document.getElementById('pago_amount').value = this.dataset.balance;
                document.getElementById('pago_amount').max = this.dataset.balance;
                document.getElementById('pago_payment_method').value = '';
                document.getElementById('pago_observation').value = '';
            });
        });

        document.getElementById('btnGuardarPago').addEventListener('click', function() {
            const purchaseId = document.getElementById('pago_purchase_id').value;
            const amount = Number(document.getElementById('pago_amount').value);
            const paymentMethod = document.getElementById('pago_payment_method').value;
            const date = document.getElementById('pago_date').value;
            const observation = document.getElementById('pago_observation').value;

            if (!amount || amount <= 0) {
                Swal.fire('Dato requerido', 'Ingrese un monto mayor a cero.', 'warning');
                return;
            }
            if (!paymentMethod) {
                Swal.fire('Dato requerido', 'Seleccione el método de pago.', 'warning');
                return;
            }

            this.disabled = true;
            fetch("{{ route('supplier_payments.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    purchase_id: purchaseId,
                    amount: amount,
                    payment_method_id: paymentMethod,
                    date: date,
                    observation: observation
                })
            })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok || data.status === false) {
                    throw new Error(data.error || 'No se pudo registrar el pago.');
                }
                bootstrap.Modal.getInstance(document.getElementById('registrarPagoModal')).hide();
                Swal.fire({icon: 'success', title: 'Pago registrado', timer: 1400, showConfirmButton: false})
                    .then(() => location.reload());
            })
            .catch((error) => {
                Swal.fire('No se pudo registrar', error.message, 'error');
            })
            .finally(() => { this.disabled = false; });
        });
    </script>
@endsection
