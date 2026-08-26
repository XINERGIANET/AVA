@extends('template.index')
@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-safe me-2 text-primary"></i>Histórico de Bóveda
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none text-muted">Ventas y Caja</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Histórico Bóveda</li>
        </ol>
    </nav>
@endsection
@section('content')
    <style>
        /* Reserva el mismo alto para la etiqueta de cada tarjeta (1 o 2 líneas)
           para que el monto de abajo arranque a la misma altura en todas. */
        .vault-card-label {
            min-height: 2.5rem;
        }
    </style>
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        @php $totalCards = 1 + $vaultDestinations->count(); @endphp
        <div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-md-{{ min($totalCards, 6) }}">
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold vault-card-label">Acumulado en bóveda</div>
                        <div class="fs-3 fw-bold text-primary">S/ {{ number_format($vaultAccumulated, 2) }}</div>
                        <div class="text-muted small">{{ $currentLocation ? 'Sede: ' . $currentLocation->name : 'Selecciona una sede arriba' }}</div>
                    </div>
                </div>
            </div>
            @foreach ($vaultDestinations as $destination)
                @php $enviado = $egresosPorDestino[$destination->id] ?? 0; @endphp
                <div class="col">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase fw-semibold vault-card-label">Enviado a {{ $destination->name }}</div>
                            <div class="fs-3 fw-bold {{ $enviado > 0 ? 'text-primary' : 'text-secondary' }}">S/ {{ number_format($enviado, 2) }}</div>
                            <div class="text-muted small">{{ $enviado > 0 ? 'Total histórico' : 'Sin movimientos aún' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary fw-medium px-4" data-bs-toggle="modal" data-bs-target="#modalGenerarMovimiento" style="border-radius: 8px;">
                                <i class="bi bi-arrow-left-right me-2"></i>Generar Movimiento
                            </button>
                        </div>
                        <form id="fromFilter">
                            <div class="row g-2 align-items-end mb-4">
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                    <input id="from_date" type="date" class="form-control form-control-sm" value="{{ request()->from_date ?? '' }}" name="from_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                    <input type="date" class="form-control form-control-sm" value="{{ request()->to_date ?? '' }}" name="to_date" id="to_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                    @php $isMasterUser = auth()->user()->role->nombre === 'master'; @endphp
                                    <select class="form-select form-select-sm" name="location_id" id="location_id">
                                        <option value="" disabled {{ $isMasterUser && !request()->filled('location_id') ? 'selected' : '' }}>Seleccione</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ (!$isMasterUser || request()->location_id == $location->id) ? 'selected' : '' }}>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="isle_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Isla</label>
                                    <select class="form-select form-select-sm" name="isle_id" id="isle_id">
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach ($isles as $isle)
                                            <option value="{{ $isle->id }}" {{ request()->isle_id == $isle->id ? 'selected' : '' }}>{{ $isle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="user_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Usuario</label>
                                    <select class="form-select form-select-sm" name="user_id" id="user_id">
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="submit" id="btnFiltrar" class="btn btn-primary btn-sm px-3 fw-medium w-100 mb-1" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('vault.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium w-100" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-eraser me-1"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Monto</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Destino</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                                        @if (auth()->user()->role->nombre == 'admin' || auth()->user()->role->nombre == 'master')
                                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($transactions as $trans)
                                    <tr>
                                        <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                                        <td>{{ $trans->user->name }}</td>
                                        <td>{{ $trans->isle->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($trans->amount,2) }}</td>
                                        <td>
                                            @if ($trans->type == 'eb') Ingreso
                                            @elseif ($trans->type == 'eg') Egreso
                                            @else Salida a Caja Chica
                                            @endif
                                        </td>
                                        <td>{{ $trans->vault_destination->name ?? '-' }}</td>
                                        <td>{{ $trans->date->format('d/m/Y') }}</td>
                                        <td>{{ $trans->location->name }}</td>
                                        <td>
                                            @if ($trans->status == 0)
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @else
                                                <span class="badge bg-success">Aprobado</span>
                                            @endif
                                        </td>
                                        <td>{{ $trans->description ?? '-' }}</td>
                                        @if (auth()->user()->role->nombre == 'admin' || auth()->user()->role->nombre == 'master')
                                            <td>
                                                <button class="btn btn-sm btn-primary {{ $trans->status == 0 ? '' : 'disabled' }}" data-bs-toggle="modal"
                                                    data-bs-target="#approveModal" data-id="{{ $trans->id }}" title="{{ $trans->status == 0 ? 'Aprobar movimiento' : 'Ya aprobado' }}">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal" data-id="{{ $trans->id }}" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No hay transacciones registradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Eliminar Movimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">¿Cómo deseas procesar la eliminación de este movimiento?</p>
                    
                    <div class="list-group">
                        <div class="list-group-item list-group-item-action border mb-2 rounded p-3">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-arrow-counterclockwise me-1"></i>Revertir Saldo a Origen</h6>
                            </div>
                            <p class="small text-muted mb-2">Devuelve el dinero a la caja o procedencia original de donde se tomó.</p>
                            <button type="button" class="btn btn-primary btn-sm px-3 fw-medium" id="btn-delete-revert">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Eliminar y Revertir
                            </button>
                        </div>

                        <div class="list-group-item list-group-item-action border rounded p-3">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-trash3 me-1"></i>Eliminar Definitivamente</h6>
                            </div>
                            <p class="small text-muted mb-2">Borra únicamente el registro del historial sin alterar los saldos de caja.</p>
                            <button type="button" class="btn btn-danger btn-sm px-3 fw-medium" id="btn-delete-direct">
                                <i class="bi bi-trash3 me-1"></i>Eliminar Definitivamente
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="revert_balance" id="delete_revert_balance" value="1">
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

    <div class="modal fade" id="modalGenerarMovimiento" tabindex="-1" aria-labelledby="modalGenerarMovimientoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalGenerarMovimientoLabel"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Generar Movimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formGenerarMovimiento" action="{{ route('vault.movement.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tipo de Movimiento</label>
                            <select class="form-select" id="movement_type" name="movement_type" required>
                                <option value="ingreso">Ingreso (dinero que entra)</option>
                                <option value="egreso">Egreso (dinero que sale)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Sede</label>
                            <select class="form-select" id="movement_location_id" name="location_id" required>
                                <option value="" {{ $isMasterUser ? 'selected' : '' }}>Seleccione una sede...</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" data-balance="{{ $locationVaultTotals[$location->id] ?? 0 }}" {{ !$isMasterUser ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small" id="locationBalanceHint"></div>
                        </div>
                        <div class="mb-3" id="destinoGroup" style="display: none;">
                            <label class="form-label small fw-bold">Destino</label>
                            <div class="input-group">
                                <select class="form-select" id="vault_destination_id" name="vault_destination_id">
                                    <option value="">Seleccione un destino...</option>
                                    @foreach ($vaultDestinations as $destination)
                                        <option value="{{ $destination->id }}">{{ $destination->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" type="button" id="addDestinationBtn" data-bs-toggle="modal" data-bs-target="#modalAgregarDestino" title="Agregar destino">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Monto</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="movement_amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fecha</label>
                            <input type="date" class="form-control" id="movement_date" name="date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descripción</label>
                            <input type="text" class="form-control" id="movement_description" name="description" placeholder="Opcional">
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

    <div class="modal fade" id="modalAgregarDestino" tabindex="-1" aria-labelledby="modalAgregarDestinoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalAgregarDestinoLabel"><i class="bi bi-plus-circle me-2 text-primary"></i>Agregar Destino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-bold">Nombre del destino</label>
                    <input type="text" class="form-control" id="new_destination_name" placeholder="Ej. Banco BCP, Socio, etc.">
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" id="saveDestinationBtn">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#fromFilter').on('submit', function(e) {
                e.preventDefault();

                // Mostrar indicador de carga
                $('#btnFiltrar').html('<i class="bi bi-search"></i> Filtrando...').prop('disabled', true);

                // Obtener datos del formulario
                const formData = $(this).serialize();

                // Redirigir con los parámetros
                window.location.href = "{{ route('vault.index') }}?" + formData;
            });

            function toggleDestinoField() {
                const isEgreso = $('#movement_type').val() === 'egreso';
                $('#destinoGroup').toggle(isEgreso);
                $('#vault_destination_id').prop('required', isEgreso);
                updateLocationBalanceHint();
            }

            function updateLocationBalanceHint() {
                const isEgreso = $('#movement_type').val() === 'egreso';
                const selected = $('#movement_location_id option:selected');
                const balance = parseFloat(selected.data('balance')) || 0;

                if (!isEgreso || !selected.val()) {
                    $('#locationBalanceHint').text('');
                    return;
                }

                $('#locationBalanceHint').text('Disponible en la bóveda de esta sede: S/ ' + balance.toFixed(2));
            }

            $('#movement_type').on('change', toggleDestinoField);
            $('#movement_location_id').on('change', updateLocationBalanceHint);
            toggleDestinoField();

            $('#saveDestinationBtn').on('click', function() {
                const name = $('#new_destination_name').val().trim();
                if (!name) {
                    ToastError.fire({ text: 'Ingresa un nombre para el destino.' });
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
                btn.disabled = true;

                $.ajax({
                    url: "{{ route('vault-destinations.store') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function(response) {
                        if (response.success) {
                            const option = new Option(response.destination.name, response.destination.id, true, true);
                            $('#vault_destination_id').append(option);
                            $('#new_destination_name').val('');
                            $('#modalAgregarDestino').modal('hide');
                            ToastMessage.fire({ text: response.message || 'Destino agregado correctamente.' });
                        } else {
                            ToastError.fire({ text: response.message || 'Error al agregar el destino.' });
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || 'Error al agregar el destino.';
                        ToastError.fire({ text: msg });
                    },
                    complete: function() {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
            });

            // Handlers para Aprobar y Eliminar
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
                $(this).prop('disabled', true).text('Aprobando...');
                $('#approveForm').submit();
            });

            $('#btn-delete-revert').on('click', function() {
                $(this).prop('disabled', true).text('Revirtiendo...');
                $('#btn-delete-direct').prop('disabled', true);
                $('#delete_revert_balance').val('1');
                $('#deleteForm').submit();
            });

            $('#btn-delete-direct').on('click', function() {
                $(this).prop('disabled', true).text('Eliminando...');
                $('#btn-delete-revert').prop('disabled', true);
                $('#delete_revert_balance').val('0');
                $('#deleteForm').submit();
            });
        });
    </script>
@endsection
