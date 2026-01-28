@extends('template.index')
@section('header')
    <h1>Histórico de Egresos</h1>
    <p>Lista de egresos</p>
@endsection
@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="{{ route('expenses.historico') }}" method="GET" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label small">Fecha Inicial</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ request()->start_date ? request()->start_date : '' }}">
                                </div>
                                <!-- Fecha final -->
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label small">Fecha Final</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ request()->end_date ? request()->end_date : '' }}">
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sede</label>
                                        <select class="form-select" id="location_id" name="location_id">
                                            <option value="">Todas las sedes</option>
                                            @foreach ($locations ?? [] as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary w-100"
                                        id="btnFiltrar">Filtrar</button>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <a href="{{ route('expenses.historico') }}" class="btn btn-warning w-100"
                                        id="btnLimpiar">Limpiar</a>
                                </div>
                            </div>
                                                        <div class="row">
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <h5>
                                                <strong>Total egresos: S/ {{ number_format($totalExpenses, 2, '.', ',') }}</strong>
                                            </h5>
                                        </div>
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
                                        <th>Descripción</th>
                                        <th>Monto</th>
                                        
                                        <th>Sede / Isla</th>
                                        
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ $expense->amount }}</td>
                                            <td>{{ $expense->location->name }} / {{ $expense->isle->name }}</td>
                                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editExpenseModal" data-id="{{ $expense->id }}"
                                                    title="Editar egreso">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteExpenseModal" data-id="{{ $expense->id }}"
                                                    title="Eliminar egreso">
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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel">Editar Egreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="editExpenseForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_expense_id" name="expense_id">
                        
                        <div class="mb-3">
                            <label for="edit_isle_id" class="form-label">Isla <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_isle_id" name="isle_id" required>
                                <option value="">Seleccione una isla</option>
                                @foreach ($isles ?? [] as $isle)
                                    <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Monto <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required min="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="edit_description" name="description" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Egreso -->
    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExpenseModalLabel">Eliminar Egreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este egreso?</p>
                    <p class="text-muted small">El monto será devuelto a la caja chica de la isla correspondiente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteExpense">Eliminar</button>
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
</script>
@endsection
