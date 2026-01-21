@extends('template.index')

@section('header')
    <h1>Tanques</h1>
    <p>Lista de Tanques</p>
@endsection
@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner mt-n5 py-0">
        <!-- Card que contiene el formulario y la tabla -->
        <div class="card shadow">
            <!-- Cuerpo del Card -->
            <div class="card-body">
                <!-- Formulario de Registro -->
                <form id="createTanqueForm" class="mb-5" action="{{ route('tanques.store') }}" method="POST">
                    @csrf
                    <!-- Fila 1: Sede y Nombre -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="location_id" class="form-label mb-0">Sede</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="location_id" name="location_id" required
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Nombre</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre del tanque"
                                        id="name" name="name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fila 2: Capacidad y Producto -->
                    <div class="row mb-3 align-items-center">
                        <!-- Capacidad -->
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="capacity" class="form-label mb-0">Capacidad</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" placeholder="Ingrese la capacidad del tanque"
                                        id="capacity" name="capacity" required step="1" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Producto -->
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="product_id" class="form-label mb-0">Producto</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="product_id" name="product_id" required>
                                        <option value="" selected>Seleccione un producto</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Es reserva -->
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <!-- enviar 0 cuando no está marcado -->
                                <input type="hidden" name="is_reserve" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_reserve"
                                        name="is_reserve">
                                    <label class="form-check-label" for="is_reserve">Reserva</label>
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

                <!-- Tabla de Registros -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th>N°</th>
                                <th>Sede</th>
                                <th>Nombre</th>
                                <th>Capacidad (galones)</th>
                                <th>Producto</th>
                                <th>Reserva</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse ($tanques as $tanque)
                                <tr>
                                    <td>{{ ($tanques->currentPage() - 1) * $tanques->perPage() + $loop->iteration }}</td>
                                    <td>{{ $tanque->sede_nombre }}</td>
                                    <td>{{ $tanque->name }}</td>
                                    <td>{{ number_format($tanque->capacity) }}</td>
                                    <td>{{ $tanque->producto_nombre ?? 'Sin producto' }}</td>
                                    <td>{{ $tanque->is_reserve === 1 ? 'Si' : 'No' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $tanque->id }}"
                                            data-location_id="{{ $tanque->location_id }}" data-name="{{ $tanque->name }}"
                                            data-capacity="{{ $tanque->capacity }}"
                                            data-product_id="{{ $tanque->product_id }}"
                                            data-is_reserve="{{ $tanque->is_reserve }}"
                                            data-estado="{{ $tanque->estado }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $tanque->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay tanques registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editTanqueForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Tanque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_sede_id" class="form-label">Sede</label>
                            <select class="form-control" id="edit_sede_id" name="location_id" required>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_capacidad" class="form-label">Capacidad</label>
                            <input type="number" class="form-control" id="edit_capacidad" name="capacity" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_producto_id" class="form-label">Producto</label>
                            <select class="form-control" id="edit_producto_id" name="product_id" required>
                                @foreach ($products as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <!-- enviar 0 cuando no está marcado -->
                                    <input type="hidden" name="is_reserve" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            id="edit_is_reserve" name="is_reserve">
                                        <label class="form-check-label" for="edit_is_reserve">Reserva</label>
                                    </div>
                                </div>
                            </div>
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

    <!-- Modal Eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteTanqueForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar Tanque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar este tanque?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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
