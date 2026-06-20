@extends('template.index')

@section('header')
    <h1>Bóveda</h1>
    <p>Lista de transacciones de bóveda</p>
@endsection
@section('content')
    <div class="container-fluid content-inner mt-0">
        <!-- Card que contiene el formulario y la tabla -->
        <div class="card shadow">
            <!-- Cuerpo del Card -->
            <div class="card-body">
                <!-- Formulario de Registro -->
                <form id="createCollaboratorForm" class="mb-5" action="{{ route('vault.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Sede</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="location_id" name="location_id">
                                        <option value="{{ auth()->user()->location_id }}">
                                            {{ auth()->user()->location->name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Isla</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="isle_id" name="isle_id" required>
                                        <option value="">Seleccione una isla...</option>
                                        @foreach ($isles as $isle)
                                            <option value="{{ $isle->id }}" data-balance="{{ $isle->vault }}">
                                                {{ $isle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Tipo</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="type" name="type">
                                        <option value="eb">Entrada</option>
                                        <option value="sb">Salida</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Monto</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Fecha</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="date" class="form-control" id="date" name="date" required
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="description" class="form-label mb-0">Descripción</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" placeholder="" id="description"
                                        name="description">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Guardar (alineado a la derecha) -->
                    <div class="row mb-3">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>

                <form id="vaultFilterForm" class="row g-3 mb-3" method="GET" action="{{ route('vault.create') }}">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="from_date" name="from_date"
                            value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="to_date" name="to_date"
                            value="{{ $toDate }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="{{ route('vault.create') }}" class="btn btn-warning">Limpiar</a>
                    </div>
                </form>

                <div class="mb-3 d-flex flex-column">
                    <strong>
                        Total filtrado ({{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}):
                        S/ {{ number_format($filteredTotal, 2) }}
                    </strong>
                    <small class="text-muted">
                        Por defecto se muestra la última fecha registrada: {{ \Carbon\Carbon::parse($defaultDate)->format('d/m/Y') }}
                    </small>
                </div>
                <!-- Tabla de Registros -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Usuario</th>    
                                <th>Isla</th>    
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                @if (auth()->user()->role->nombre == 'admin' || auth()->user()->role->nombre == 'master')
                                    <th>Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $trans)
                                <tr>
                                    <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $trans->user->name }}</td>
                                    <td>{{ $trans->isle->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($trans->amount, 2) }}</td>
                                    <td>{{ $trans->type == 'eb' ? 'Entrada' : 'Salida' }}</td>
                                    <td>{{ $trans->date->format('d/m/Y') }}</td>
                                    <td>{{ $trans->description ?? '-' }}</td>
                                    <td><span
                                            class="text-{{ $trans->status == 0 ? 'danger' : 'primary' }}">{{ $trans->status == 0 ? 'Pendiente' : 'Aprobado' }}</span>
                                    </td>
                                    @if (auth()->user()->role->nombre == 'admin' || auth()->user()->role->nombre == 'master')
                                        <td>
                                            <button class="btn btn-sm btn-primary {{ $trans->status == 0 ? '' : 'disabled' }}" data-bs-toggle="modal"
                                                data-bs-target="#approveModal" data-id="{{ $trans->id }}">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="{{ $trans->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay transacciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Seguro que deseas eliminar esta transacción?</p>
                    <small class="text-danger">Esta acción revertirá saldos relacionados cuando corresponda.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-delete">Eliminar</button>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas aprobar esta transacción?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-approve">Aprobar</button>
                </div>
                <form id="approveForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                </form>
            </div>
        </div>
    </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#approveModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const actionUrl = "{{ route('vault.approve', ['id' => ':id']) }}".replace(':id', id);
                $('#approveForm').attr('action', actionUrl);
            });

            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const actionUrl = "{{ route('vault.destroy', ['vault' => ':id']) }}".replace(':id', id);
                $('#deleteForm').attr('action', actionUrl);
            });

            $('#btn-approve').on('click', function() {
                $('#approveForm').submit();
            });

            $('#btn-delete').on('click', function() {
                $('#deleteForm').submit();
            });
        });
    </script>
@endsection


