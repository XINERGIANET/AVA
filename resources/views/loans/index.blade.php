@extends('template.index')

@section('content')
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4 class="mb-0">Registro de Prestamos</h4>
                <small class="text-muted">Control de prestamos otorgados y recuperaciones por isla</small>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loanModal" id="btnNewLoan">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Prestamo
            </button>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('loans.index') }}" class="row g-2 mb-3">
                @if ($isMaster)
                    <div class="col-md-3">
                        <label class="form-label">Sede</label>
                        <select name="location_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('loans.index') }}" class="btn btn-warning">Limpiar</a>
                </div>
            </form>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <div class="small text-muted">Prestado</div>
                        <div class="fs-5 fw-bold">S/ {{ number_format($totalLoaned, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <div class="small text-muted">Cobrado / Recuperado</div>
                        <div class="fs-5 fw-bold">S/ {{ number_format($totalRecovered, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light">
                        <div class="small text-muted">Por cobrar</div>
                        <div class="fs-5 fw-bold">S/ {{ number_format($totalPending, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center loan-table">
                    <thead>
                        <tr>
                            <th colspan="11" class="loan-title">REGISTRO DE PRESTAMOS</th>
                            <th class="loan-title"></th>
                        </tr>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">NOMBRE</th>
                            <th rowspan="2">MOTIVO / DESCRIPCION</th>
                            <th colspan="3" class="loan-group">PRESTAMO OTORGADO</th>
                            <th colspan="3" class="loan-group loan-group-green">COBRO / RECUPERACION</th>
                            <th colspan="2" class="loan-group loan-group-yellow">RESULTADO</th>
                            <th rowspan="2">ESTADO</th>
                        </tr>
                        <tr>
                            <th>Fecha Prestamo</th>
                            <th>Monto Prestado (S/.)</th>
                            <th>MEDIO DE ENVIO</th>
                            <th>Fecha Vencimiento</th>
                            <th>Monto Recuperado (S/.)</th>
                            <th>MEDIO DE COBRO</th>
                            <th>TOTAL COBRADO</th>
                            <th>POR COBRAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr>
                                <td>{{ ($loans->currentPage() - 1) * $loans->perPage() + $loop->iteration }}</td>
                                <td class="text-start">{{ $loan->name }}</td>
                                <td class="text-start">{{ $loan->description }}</td>
                                <td>{{ optional($loan->loan_date)->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($loan->loan_amount, 2) }}</td>
                                <td>{{ $loan->send_method ?: 'Efectivo' }}</td>
                                <td>{{ optional($loan->due_date)->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($loan->recovered_amount, 2) }}</td>
                                <td>{{ $loan->collection_method ?: '-' }}</td>
                                <td class="text-end">{{ number_format($loan->recovered_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($loan->balance, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $loan->status === 'paid' ? 'success' : ($loan->status === 'overdue' ? 'danger' : ($loan->status === 'partial' ? 'warning' : 'secondary')) }}">
                                        {{ ['paid' => 'Pagado', 'overdue' => 'Vencido', 'partial' => 'Parcial', 'pending' => 'Pendiente'][$loan->status] ?? $loan->status }}
                                    </span>
                                    <div class="mt-2 d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-outline-primary btn-edit-loan"
                                            data-loan='@json($loan)'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-loan" data-id="{{ $loan->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">No hay prestamos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $loans->links() }}
        </div>
    </div>

    <div class="modal fade" id="loanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" id="loanForm">
                @csrf
                <input type="hidden" id="loan_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="loanModalTitle">Nuevo Prestamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="loan_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Isla</label>
                            <select class="form-select" id="loan_isle_id" required>
                                <option value="">Seleccione</option>
                                @foreach ($isles as $isle)
                                    <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Motivo / Descripcion</label>
                            <textarea class="form-control" id="loan_description" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Prestamo</label>
                            <input type="date" class="form-control" id="loan_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto Prestado</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="loan_amount" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Medio de Envio</label>
                            <select class="form-select" id="loan_send_method">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Yape">Yape</option>
                                <option value="BCP">BCP</option>
                                <option value="BBVA">BBVA</option>
                                <option value="Qulqui">Qulqui</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Vencimiento</label>
                            <input type="date" class="form-control" id="loan_due_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monto Recuperado</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="loan_recovered_amount" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Medio de Cobro</label>
                            <select class="form-select" id="loan_collection_method">
                                <option value="">Sin cobro</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Yape">Yape</option>
                                <option value="BCP">BCP</option>
                                <option value="BBVA">BBVA</option>
                                <option value="Qulqui">Qulqui</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Cobro</label>
                            <input type="date" class="form-control" id="loan_collection_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Cobrado</label>
                            <input type="number" class="form-control" id="loan_total_collected" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Por Cobrar</label>
                            <input type="number" class="form-control" id="loan_pending_amount" disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveLoan">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .loan-table th {
            background: #2f75b5;
            color: #fff;
            font-size: 11px;
            vertical-align: middle;
        }

        .loan-table .loan-title {
            background: #2f75b5;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
        }

        .loan-table .loan-group {
            background: #1f4e79;
        }

        .loan-table .loan-group-green {
            background: #548235;
        }

        .loan-table .loan-group-yellow {
            background: #f4b183;
            color: #111;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const loanRoutes = {
            store: @json(route('loans.store')),
            base: @json(url('loans'))
        };

        function todayDate() {
            return new Date().toISOString().slice(0, 10);
        }

        function resetLoanForm() {
            $('#loan_id').val('');
            $('#loanModalTitle').text('Nuevo Prestamo');
            $('#loan_name').val('');
            $('#loan_isle_id').val('');
            $('#loan_description').val('');
            $('#loan_date').val(todayDate());
            $('#loan_amount').val('');
            $('#loan_send_method').val('Efectivo');
            $('#loan_due_date').val('');
            $('#loan_recovered_amount').val('0');
            $('#loan_collection_method').val('');
            $('#loan_collection_date').val('');
            calculateLoanResult();
        }

        function calculateLoanResult() {
            const amount = parseFloat($('#loan_amount').val()) || 0;
            const recovered = Math.min(parseFloat($('#loan_recovered_amount').val()) || 0, amount);
            $('#loan_total_collected').val(recovered.toFixed(2));
            $('#loan_pending_amount').val(Math.max(0, amount - recovered).toFixed(2));
        }

        $('#btnNewLoan').on('click', resetLoanForm);
        $('#loan_amount, #loan_recovered_amount').on('input', calculateLoanResult);

        $('.btn-edit-loan').on('click', function() {
            const loan = $(this).data('loan');
            $('#loan_id').val(loan.id);
            $('#loanModalTitle').text('Editar Prestamo');
            $('#loan_name').val(loan.name || '');
            $('#loan_isle_id').val(loan.isle_id || '');
            $('#loan_description').val(loan.description || '');
            $('#loan_date').val((loan.loan_date || '').slice(0, 10));
            $('#loan_amount').val(loan.loan_amount || 0);
            $('#loan_send_method').val(loan.send_method || 'Efectivo');
            $('#loan_due_date').val((loan.due_date || '').slice(0, 10));
            $('#loan_recovered_amount').val(loan.recovered_amount || 0);
            $('#loan_collection_method').val(loan.collection_method || '');
            $('#loan_collection_date').val((loan.collection_date || '').slice(0, 10));
            calculateLoanResult();
            $('#loanModal').modal('show');
        });

        $('#loanForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#loan_id').val();
            const payload = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                name: $('#loan_name').val(),
                isle_id: $('#loan_isle_id').val(),
                description: $('#loan_description').val(),
                loan_date: $('#loan_date').val(),
                loan_amount: $('#loan_amount').val(),
                send_method: $('#loan_send_method').val(),
                due_date: $('#loan_due_date').val(),
                recovered_amount: $('#loan_recovered_amount').val() || 0,
                collection_method: $('#loan_collection_method').val(),
                collection_date: $('#loan_collection_date').val(),
            };

            if (id) {
                payload._method = 'PUT';
            }

            const $btn = $('#btnSaveLoan');
            $btn.prop('disabled', true);

            $.ajax({
                url: id ? `${loanRoutes.base}/${id}` : loanRoutes.store,
                method: 'POST',
                data: payload,
                success: function(resp) {
                    if (resp.success) {
                        ToastMessage.fire({ text: resp.message });
                        $('#loanModal').modal('hide');
                        setTimeout(() => location.reload(), 700);
                    } else {
                        ToastError.fire({ text: resp.message || 'No se pudo guardar el prestamo.' });
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    const msg = errors ? Object.values(errors).flat().join(', ') : (xhr.responseJSON?.message || 'Error al guardar.');
                    ToastError.fire({ text: msg });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $('.btn-delete-loan').on('click', function() {
            const id = $(this).data('id');
            if (!confirm('Eliminar este prestamo?')) {
                return;
            }

            $.ajax({
                url: `${loanRoutes.base}/${id}`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function(resp) {
                    if (resp.success) {
                        ToastMessage.fire({ text: resp.message });
                        setTimeout(() => location.reload(), 700);
                    } else {
                        ToastError.fire({ text: resp.message || 'No se pudo eliminar.' });
                    }
                },
                error: function(xhr) {
                    ToastError.fire({ text: xhr.responseJSON?.message || 'Error al eliminar.' });
                }
            });
        });
    </script>
@endsection
