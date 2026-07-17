@extends('template.index')
@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-wallet2 me-2 text-primary"></i>Histórico de Egresos
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Reportes</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Egresos</li>
        </ol>
    </nav>
@endsection
@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body border-bottom">
                        <form action="{{ route('expenses.historico') }}" method="GET" id="fromFilter">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label text-dark fw-bold small mb-1">Fecha Inicial</label>
                                    <input type="date" class="form-control form-control-sm" name="start_date" id="start_date"
                                        value="{{ request()->start_date ? request()->start_date : '' }}">
                                </div>
                                <!-- Fecha final -->
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label text-dark fw-bold small mb-1">Fecha Final</label>
                                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date"
                                        value="{{ request()->end_date ? request()->end_date : '' }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label text-dark fw-bold small mb-1">Sede</label>
                                    <select class="form-select form-select-sm" id="location_id" name="location_id">
                                        <option value="">Todas las sedes</option>
                                        @foreach ($locations ?? [] as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-auto ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-medium px-3" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm fw-medium px-3" id="btnExcel" onclick="exportExcel()" style="border-radius: 6px;">
                                        <i class="bi bi-file-earmark-excel me-1"></i>Excel
                                    </button>
                                    <a href="{{ route('expenses.historico') }}" class="btn btn-light btn-sm fw-medium px-3" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                                    </a>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="d-flex justify-content-end">
                                        <div class="bg-light px-3 py-2 rounded border">
                                            <h6 class="mb-0 text-dark">
                                                <strong>Total egresos: S/ {{ number_format($totalExpenses, 2, '.', ',') }}</strong>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Monto</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede / Isla</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ $expense->amount }}</td>
                                            <td>{{ $expense->location->name }} / {{ $expense->isle->name }}</td>
                                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editExpenseModal" data-id="{{ $expense->id }}"
                                                    title="Editar egreso" style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteExpenseModal" data-id="{{ $expense->id }}"
                                                    title="Eliminar egreso" style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Egreso -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="editExpenseModalLabel"><i class="bi bi-pencil-square me-2 text-warning"></i>Editar Egreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="editExpenseForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_expense_id" name="expense_id">
                        
                        <div class="mb-3">
                            <label for="edit_isle_id" class="form-label text-dark fw-bold mb-1">Isla <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="edit_isle_id" name="isle_id" required>
                                <option value="">Seleccione una isla</option>
                                @foreach ($isles ?? [] as $isle)
                                    <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_amount" class="form-label text-dark fw-bold mb-1">Monto <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control form-control-sm" id="edit_amount" name="amount" required min="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label text-dark fw-bold mb-1">Descripción</label>
                            <input type="text" class="form-control form-control-sm" id="edit_description" name="description" maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_category" class="form-label text-dark fw-bold mb-1">Categoría</label>
                            <input type="text" class="form-control form-control-sm" id="edit_category" name="category" maxlength="255">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_payment_method" class="form-label text-dark fw-bold mb-1">Método de Pago</label>
                            <select class="form-select form-select-sm" id="edit_payment_method" name="payment_method">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_observation" class="form-label text-dark fw-bold mb-1">Observaciones</label>
                            <textarea class="form-control form-control-sm" id="edit_observation" name="observation" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_date" class="form-label text-dark fw-bold mb-1">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="edit_date" name="date" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light">
                        <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-medium btn-sm"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Egreso -->
    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="deleteExpenseModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Egreso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-3">¿Estás seguro de que deseas eliminar este egreso?</p>
                    <p class="text-muted small mb-0">El monto será devuelto a la caja chica de la isla correspondiente.</p>
                </div>
                <div class="modal-footer border-top-0 bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 fw-medium" id="btnDeleteExpense">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let expenseToDelete = null;

    // Modal de Editar
    $('#editExpenseModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const expenseId = button.data('id');

        // Limpiar formulario
        $('#editExpenseForm')[0].reset();
        $('#edit_expense_id').val(expenseId);

        // Cargar datos del egreso
        $.ajax({
            url: '{{ url("expenses") }}/' + expenseId + '/edit',
            method: 'GET',
            success: function(response) {
                if (response.success && response.expense) {
                    const expense = response.expense;
                    $('#edit_isle_id').val(expense.isle_id);
                    $('#edit_amount').val(expense.amount);
                    $('#edit_description').val(expense.description || '');
                    $('#edit_category').val(expense.category || '');
                    $('#edit_payment_method').val(expense.payment_method || 'Efectivo');
                    $('#edit_observation').val(expense.observation || '');
                    $('#edit_date').val(expense.date ? expense.date.split(' ')[0] : '');
                } else {
                    ToastError.fire({
                        text: 'Error al cargar los datos del egreso'
                    });
                    $('#editExpenseModal').modal('hide');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                ToastError.fire({
                    text: 'Error al cargar los datos del egreso'
                });
                $('#editExpenseModal').modal('hide');
            }
        });
    });

    // Enviar formulario de editar
    $('#editExpenseForm').on('submit', function(e) {
        e.preventDefault();
        
        const expenseId = $('#edit_expense_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ url("expenses") }}/' + expenseId,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    ToastMessage.fire({
                        text: response.message || 'Egreso actualizado exitosamente'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    ToastError.fire({
                        text: response.message || 'Error al actualizar el egreso'
                    });
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error al actualizar el egreso';
                ToastError.fire({
                    text: message
                });
            }
        });
    });

    // Modal de Eliminar
    $('#deleteExpenseModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        expenseToDelete = button.data('id');
    });

    // Confirmar eliminación
    $('#btnDeleteExpense').on('click', function() {
        if (!expenseToDelete) return;

        $.ajax({
            url: '{{ url("expenses") }}/' + expenseToDelete,
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    ToastMessage.fire({
                        text: response.message || 'Egreso eliminado exitosamente'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    ToastError.fire({
                        text: response.message || 'Error al eliminar el egreso'
                    });
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error al eliminar el egreso';
                ToastError.fire({
                    text: message
                });
            },
            complete: function() {
                $('#deleteExpenseModal').modal('hide');
                expenseToDelete = null;
            }
        });
    });

    function exportExcel() {
        const formData = $('#fromFilter').serialize();
        window.location.href = "{{ route('expenses.excel') }}?" + formData;
    }
</script>
@endsection
