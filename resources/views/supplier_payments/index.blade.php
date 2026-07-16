@extends('template.index')

@section('header')
    <h1>Cuentas por Pagar</h1>
    <p>Compras registradas y su saldo pendiente frente al proveedor</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="{{ route('supplier_payments.index') }}" method="GET" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label small">Fecha Inicial</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ request()->start_date ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label small">Fecha Final</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ request()->end_date ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Proveedor</label>
                                    <select class="form-select" id="supplier_id" name="supplier_id">
                                        <option value="">Todos los proveedores</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ request()->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="only_pending"
                                            id="only_pending" value="1" {{ $onlyPending === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="only_pending">
                                            Solo con saldo pendiente
                                        </label>
                                    </div>
                                </div>
                                <div class="col d-flex align-items-end mb-3">
                                    <div class="w-50s me-2">
                                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <a href="{{ route('supplier_payments.index') }}" class="btn btn-warning w-100" id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-end">
                                        <h5><strong>Deuda total mostrada: S/ {{ number_format($totalDebt, 2, '.', ',') }}</strong></h5>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° Comprobante</th>
                                        <th>Proveedor</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Pagado</th>
                                        <th>Saldo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $purchase)
                                        <tr>
                                            <td>{{ $purchase->invoice_number ?? '---' }}</td>
                                            <td>{{ $purchase->supplier->company_name ?? 'Sin proveedor' }}</td>
                                            <td>{{ $purchase->date->format('d/m/Y') }}</td>
                                            <td>S/ {{ number_format($purchase->total, 2) }}</td>
                                            <td>S/ {{ number_format($purchase->paid_amount, 2) }}</td>
                                            <td class="fw-semibold {{ $purchase->balance > 0.009 ? 'text-danger' : 'text-success' }}">
                                                S/ {{ number_format($purchase->balance, 2) }}
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
                                            <td>
                                                @if ($purchase->balance > 0.009)
                                                    <button class="btn btn-primary btn-sm btn-registrar-pago"
                                                        data-bs-toggle="modal" data-bs-target="#registrarPagoModal"
                                                        data-purchase-id="{{ $purchase->id }}"
                                                        data-invoice="{{ $purchase->invoice_number ?? '---' }}"
                                                        data-supplier="{{ $purchase->supplier->company_name ?? 'Sin proveedor' }}"
                                                        data-balance="{{ number_format($purchase->balance, 2, '.', '') }}">
                                                        <i class="bi bi-cash-coin me-1"></i>Registrar pago
                                                    </button>
                                                @else
                                                    <span class="text-muted small">Sin saldo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No hay compras que coincidan con el filtro.</td>
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
        </div>
    </div>

    <div class="modal fade" id="registrarPagoModal" tabindex="-1" aria-labelledby="registrarPagoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrarPagoModalLabel">Registrar pago a proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pago_purchase_id">
                    <div class="alert alert-light border py-2 small">
                        Proveedor: <strong id="pago_supplier"></strong><br>
                        Comprobante: <strong id="pago_invoice"></strong><br>
                        Saldo pendiente: <strong id="pago_saldo_label">S/ 0.00</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto a pagar</label>
                        <input type="number" min="0.01" step="0.01" class="form-control" id="pago_amount">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Método de pago</label>
                        <select class="form-select" id="pago_payment_method">
                            <option value="">Seleccione un método</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="pago_date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observación</label>
                        <input type="text" maxlength="255" class="form-control" id="pago_observation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarPago">Guardar</button>
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
