@extends('template.index')

@section('header')
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <h4 class="mb-0 text-dark fw-bold">
                <i class="bi bi-safe me-2 text-primary"></i>Registrar en Bóveda
            </h4>
        </div>
        <div>
            <button class="btn btn-primary fw-medium px-3" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                <i class="bi bi-plus-circle me-1"></i>Nuevo Registro
            </button>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none text-muted">Ventas y Caja</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Registrar en Bóveda</li>
        </ol>
    </nav>
@endsection
@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <!-- Card que contiene la tabla y filtros -->
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <form id="vaultFilterForm" method="GET" action="{{ route('vault.create') }}">
                    <div class="row g-2 align-items-end mb-4">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Desde</label>
                            <input type="date" class="form-control form-control-sm" id="from_date" name="from_date" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Hasta</label>
                            <input type="date" class="form-control form-control-sm" id="to_date" name="to_date" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-medium" style="border-radius: 6px;">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('vault.create') }}" class="btn btn-secondary btn-sm px-3 fw-medium ms-1" style="border-radius: 6px;">
                                <i class="bi bi-eraser me-1"></i>Limpiar
                            </a>
                        </div>
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
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                        <thead class="text-center">
                            <tr>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>    
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>    
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Monto</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                                @if (auth()->user()->role->nombre == 'admin' || auth()->user()->role->nombre == 'master')
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-center">
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
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-safe me-2 text-primary"></i>Registrar Transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createCollaboratorForm" action="{{ route('vault.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sede</label>
                            <select class="form-select" id="location_id" name="location_id">
                                <option value="{{ auth()->user()->location_id }}">{{ auth()->user()->location->name }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Isla</label>
                            <select class="form-select" id="isle_id" name="isle_id" required>
                                <option value="">Seleccione una isla...</option>
                                @foreach ($isles as $isle)
                                    <option value="{{ $isle->id }}" data-balance="{{ $isle->vault }}">{{ $isle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tipo</label>
                            <select class="form-select" id="type" name="type">
                                <option value="eb">Entrada</option>
                                <option value="sb">Salida</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descripción</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar</button>
                    </div>
                </form>
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


