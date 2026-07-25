@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-droplet-half me-2 text-primary"></i>Tanques</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Tanques</li>
        </ol>
    </nav>
@endsection
@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <!-- Toolbar superior de la tarjeta -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-9">
                        <form action="{{ route('tanques.index') }}" method="GET" id="filterForm">
                            <div class="row g-2 align-items-end">
                                @if (auth()->user()->role->nombre == 'master')
                                <div class="col-md-3">
                                    <label for="filter_location" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                    <select name="location_id" id="filter_location" class="form-select form-select-sm">
                                        <option value="">Todas</option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}" {{ request('location_id') == $sede->id ? 'selected' : '' }}>{{ $sede->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-3">
                                    <label for="filter_product" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Producto</label>
                                    <select name="product_id" id="filter_product" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Nombre</label>
                                    <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Tanque A..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Tanque
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                        <thead class="text-center">
                            <tr>
                                <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">ID</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Capacidad (GL)</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Reserva</th>
                                <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse ($tanques as $tanque)
                                <tr>
                                    <td>{{ ($tanques->currentPage() - 1) * $tanques->perPage() + $loop->iteration }}</td>
                                    <td>{{ $tanque->sede_nombre }}</td>
                                    <td>{{ $tanque->name }}</td>
                                    <td>{{ number_format($tanque->capacity) }}</td>
                                    <td>
                                        @if ($tanque->producto_nombre)
                                            {{ $tanque->producto_nombre }}
                                        @elseif ($tanque->product_id)
                                            <span class="text-danger" title="El producto asignado a este tanque fue eliminado del catálogo">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Producto eliminado
                                            </span>
                                        @else
                                            <span class="text-muted">Sin producto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $tanque->is_reserve === 1 ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $tanque->is_reserve === 1 ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $tanque->id }}"
                                            data-location_id="{{ $tanque->location_id }}" data-name="{{ $tanque->name }}"
                                            data-capacity="{{ $tanque->capacity }}"
                                            data-product_id="{{ $tanque->product_id }}"
                                            data-is_reserve="{{ $tanque->is_reserve }}"
                                            data-estado="{{ $tanque->estado }}" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $tanque->id }}" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay tanques registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <form id="createTanqueForm" action="{{ route('tanques.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="createModalLabel"><i class="bi bi-droplet-half text-primary me-2"></i>Agregar Nuevo Tanque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 row">
                        <div class="col-md-6 mb-3">
                            <label for="location_id" class="form-label fw-bold text-dark">Sede</label>
                            <select class="form-control form-control-lg" id="location_id" name="location_id" required
                                {{ auth()->user()->role->nombre != 'master' ? 'readonly' : '' }}>
                                @if (auth()->user()->role->nombre == 'master')
                                    <option value="" selected>Seleccione una sede</option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                                    @endforeach
                                @else
                                    <option value="{{ auth()->user()->location_id }}" selected>
                                        {{ auth()->user()->location->name }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-bold text-dark">Nombre</label>
                            <input type="text" class="form-control form-control-lg" placeholder="Ingrese el nombre del tanque" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label fw-bold text-dark">Capacidad</label>
                            <input type="number" class="form-control form-control-lg" placeholder="Ingrese la capacidad del tanque" id="capacity" name="capacity" required step="1" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label fw-bold text-dark">Producto</label>
                            <select class="form-control form-control-lg" id="product_id" name="product_id" required>
                                <option value="" selected>Seleccione un producto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <input type="hidden" name="is_reserve" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_reserve" name="is_reserve">
                                <label class="form-check-label fw-bold text-dark" for="is_reserve">Reserva</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <form id="editTanqueForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editModalLabel"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Tanque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_sede_id" class="form-label fw-bold text-dark">Sede</label>
                            <select class="form-control form-control-lg" id="edit_sede_id" name="location_id" required>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombre" class="form-label fw-bold text-dark">Nombre</label>
                            <input type="text" class="form-control form-control-lg" id="edit_nombre" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_capacidad" class="form-label fw-bold text-dark">Capacidad</label>
                            <input type="number" class="form-control form-control-lg" id="edit_capacidad" name="capacity" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_producto_id" class="form-label fw-bold text-dark">Producto</label>
                            <select class="form-control form-control-lg" id="edit_producto_id" name="product_id" required>
                                @foreach ($products as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <input type="hidden" name="is_reserve" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="edit_is_reserve" name="is_reserve">
                                <label class="form-check-label fw-bold text-dark" for="edit_is_reserve">Reserva</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="deleteTanqueForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Eliminar Tanque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-trash text-danger mb-3" style="font-size: 3.5rem;"></i>
                        <h4 class="mb-3 text-dark fw-bold">¿Estás seguro?</h4>
                        <p class="text-dark mb-0">¿Deseas eliminar permanentemente este tanque? Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer bg-light justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger px-4">Sí, Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Mostrar modal de edición
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Botón que activó el modal
                const id = button.data('id');

                // Actualizar acción del formulario
                $('#editTanqueForm').attr('action', `{{ url('tanques') }}/${id}`);

                // Rellenar campos
                $('#edit_sede_id').val(button.data('location_id'));
                $('#edit_nombre').val(button.data('name'));
                $('#edit_capacidad').val(button.data('capacity'));
                $('#edit_producto_id').val(button.data('product_id'));

                const isReserve = button.data('is_reserve');
                $('#edit_is_reserve').prop('checked', String(isReserve) === '1' || isReserve === 1 ||
                    isReserve === true);
            });

            // Mostrar modal de eliminación
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                $('#deleteTanqueForm').attr('action', `{{ url('tanques') }}/${id}`);
            });

            // Desactivar botones al enviar formularios
            $('#createTanqueForm').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true).text('Guardando...');
            });

            $('#editTanqueForm').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true).text('Guardando...');
            });

            $('#deleteTanqueForm').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true).text('Eliminando...');
            });

            $('form').on('submit', function() {
                $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
            });
        });
    </script>
@endsection
