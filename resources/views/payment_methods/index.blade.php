@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i>Métodos de Pago</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Métodos de Pago</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar superior de la tarjeta -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('payment-methods.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="{{ $isMaster ? 'col-md-5' : 'col-md-8' }}">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Método de Pago</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Efectivo, Yape..." value="{{ request('search') }}">
                            </div>
                            @if ($isMaster)
                                <div class="col-md-4">
                                    <label for="filter_location" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Filtrar por Sede</label>
                                    <select name="location_id" id="filter_location" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit();">
                                        <option value="">Todas las asignaciones</option>
                                        <option value="global" {{ request('location_id') === 'global' ? 'selected' : '' }}>Globales (Todas las sedes)</option>
                                        @foreach ($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Método
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 8%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase text-start ps-4" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre del Método</th>
                            <th class="fw-bold text-uppercase text-center" style="width: 25%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede Asignada</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 18%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($paymentMethods as $method)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark fw-medium">{{ ($paymentMethods->currentPage() - 1) * $paymentMethods->perPage() + $loop->iteration }}</td>
                            <td class="text-dark fw-bold text-start ps-4">{{ $method->name }}</td>
                            <td class="text-center">
                                @if ($isMaster)
                                    @if (!empty($method->is_global))
                                        <span class="badge bg-info text-dark px-3 py-2" style="font-size: 0.8rem;"><i class="bi bi-globe me-1"></i>Todas las Sedes (Global)</span>
                                    @elseif (!empty($method->locations) && count($method->locations) > 0)
                                        @foreach ($method->locations as $loc)
                                            <span class="badge bg-primary px-2 py-1 me-1 mb-1" style="font-size: 0.75rem;"><i class="bi bi-geo-alt-fill me-1"></i>{{ $loc->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-info text-dark px-3 py-2" style="font-size: 0.8rem;"><i class="bi bi-globe me-1"></i>Todas las Sedes (Global)</span>
                                    @endif
                                @else
                                    @if (is_null($method->location_id))
                                        <span class="badge bg-info text-dark px-3 py-2" style="font-size: 0.8rem;"><i class="bi bi-globe me-1"></i>Todas las Sedes (Global)</span>
                                    @else
                                        <span class="badge bg-primary px-3 py-2" style="font-size: 0.8rem;"><i class="bi bi-geo-alt-fill me-1"></i>{{ $method->location->name ?? 'Sede Actual' }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                @if ($isMaster || ($user->location_id && $method->location_id == $user->location_id))
                                    <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $method->id }}" data-name="{{ $method->name }}"
                                    data-is-global="{{ !empty($method->is_global) ? 1 : 0 }}"
                                    data-location-ids="{{ json_encode(!empty($method->location_ids) ? $method->location_ids : ($method->location_id ? [$method->location_id] : [])) }}"
                                    title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $method->id }}" data-name="{{ $method->name }}" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                @else
                                    <span class="badge bg-secondary opacity-75 px-2 py-1" title="Método global (solo administrable por el usuario Master)" style="font-size: 0.75rem;">
                                        <i class="bi bi-lock-fill me-1"></i>Protegido
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No se encontraron métodos de pago registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $paymentMethods->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createPaymentMethodForm" action="{{ route('payment-methods.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nuevo Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label text-dark fw-bold">Nombre del Método <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Ej. Yape Sede Central, Efectivo..." id="create_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold"><i class="bi bi-building text-primary me-1"></i>Sedes Asignadas</label>
                        @if ($isMaster)
                            <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div class="form-check mb-2 pb-2 border-bottom">
                                    <input class="form-check-input" type="checkbox" id="create_loc_global" name="is_global" value="1" onchange="toggleCreateGlobal(this)">
                                    <label class="form-check-label fw-bold text-primary cursor-pointer" for="create_loc_global">
                                        <i class="bi bi-globe me-1"></i>Todas las Sedes (Global)
                                    </label>
                                </div>
                                <div class="row g-2">
                                    @foreach ($locations as $loc)
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input create-loc-item" type="checkbox" name="location_ids[]" value="{{ $loc->id }}" id="create_loc_{{ $loc->id }}">
                                                <label class="form-check-label text-dark cursor-pointer small" for="create_loc_{{ $loc->id }}">
                                                    {{ $loc->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Marque una o varias sedes específicas donde desea habilitar este método de pago, o marque "Todas las Sedes (Global)".
                            </small>
                        @else
                            @php
                                $userLocationName = $locations->firstWhere('id', $user->location_id)->name ?? 'Sede Actual';
                            @endphp
                            <input type="text" class="form-control bg-light" value="{{ $userLocationName }}" readonly disabled>
                            <input type="hidden" name="location_id" value="{{ $user->location_id }}">
                            <small class="text-muted">Como administrador de sede, el método de pago se asignará automáticamente a <strong>{{ $userLocationName }}</strong>.</small>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPaymentMethodForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Editar Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label text-dark fw-bold">Nombre del Método <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold"><i class="bi bi-building text-primary me-1"></i>Sedes Asignadas</label>
                        @if ($isMaster)
                            <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div class="form-check mb-2 pb-2 border-bottom">
                                    <input class="form-check-input" type="checkbox" id="edit_loc_global" name="is_global" value="1" onchange="toggleEditGlobal(this)">
                                    <label class="form-check-label fw-bold text-primary cursor-pointer" for="edit_loc_global">
                                        <i class="bi bi-globe me-1"></i>Todas las Sedes (Global)
                                    </label>
                                </div>
                                <div class="row g-2">
                                    @foreach ($locations as $loc)
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input edit-loc-item" type="checkbox" name="location_ids[]" value="{{ $loc->id }}" id="edit_loc_{{ $loc->id }}">
                                                <label class="form-check-label text-dark cursor-pointer small" for="edit_loc_{{ $loc->id }}">
                                                    {{ $loc->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @php
                                $userLocationName = $locations->firstWhere('id', $user->location_id)->name ?? 'Sede Actual';
                            @endphp
                            <input type="text" class="form-control bg-light" value="{{ $userLocationName }}" readonly disabled>
                            <input type="hidden" name="location_id" value="{{ $user->location_id }}">
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deletePaymentMethodForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Eliminar Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark mb-0">¿Estás seguro de que deseas eliminar el método de pago <strong id="delete_name"></strong>?</p>
                    <small class="text-muted">El registro ya no aparecerá en las opciones activas del sistema.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    function toggleCreateGlobal(checkbox) {
        if ($(checkbox).is(':checked')) {
            $('.create-loc-item').prop('checked', true);
        } else {
            $('.create-loc-item').prop('checked', false);
        }
    }

    function toggleEditGlobal(checkbox) {
        if ($(checkbox).is(':checked')) {
            $('.edit-loc-item').prop('checked', true);
        } else {
            $('.edit-loc-item').prop('checked', false);
        }
    }

    $(document).ready(function() {
        $(document).on('change', '.create-loc-item', function() {
            var total = $('.create-loc-item').length;
            var checkedCount = $('.create-loc-item:checked').length;
            if (total > 0 && total === checkedCount) {
                $('#create_loc_global').prop('checked', true);
            } else {
                $('#create_loc_global').prop('checked', false);
            }
        });

        $(document).on('change', '.edit-loc-item', function() {
            var total = $('.edit-loc-item').length;
            var checkedCount = $('.edit-loc-item:checked').length;
            if (total > 0 && total === checkedCount) {
                $('#edit_loc_global').prop('checked', true);
            } else {
                $('#edit_loc_global').prop('checked', false);
            }
        });

        // Modal Editar
        $('#editModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const isGlobal = button.data('is-global');
            let locationIds = button.data('location-ids');

            if (typeof locationIds === 'string') {
                try { locationIds = JSON.parse(locationIds); } catch(e) { locationIds = []; }
            }

            $('#editPaymentMethodForm').attr('action', `{{ url('payment-methods') }}/${id}`);
            $('#edit_name').val(name);

            $('.edit-loc-item').prop('checked', false);
            $('#edit_loc_global').prop('checked', false);

            if (isGlobal == 1) {
                $('#edit_loc_global').prop('checked', true);
                $('.edit-loc-item').prop('checked', true);
            } else if (Array.isArray(locationIds)) {
                locationIds.forEach(function(locId) {
                    $('#edit_loc_' + locId).prop('checked', true);
                });
                var total = $('.edit-loc-item').length;
                if (total > 0 && total === locationIds.length) {
                    $('#edit_loc_global').prop('checked', true);
                }
            }
        });

        // Modal Eliminar
        $('#deleteModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            $('#deletePaymentMethodForm').attr('action', `{{ url('payment-methods') }}/${id}`);
            $('#delete_name').text(name);
        });
    });
</script>
@endsection
