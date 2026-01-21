@extends('template.index')

@section('header')
    <h1>Sedes</h1>
    <p>Lista de Sedes</p>
@endsection
@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner mt-n5 py-0">
        <!-- Card que contiene el formulario y la tabla -->
        <div class="card shadow">
            <!-- Cuerpo del Card -->
            <div class="card-body">
                <!-- Formulario de Registro -->
                <form id="createSedeForm" class="mb-5" action="{{ route('sedes.store') }}" method="POST">
                    @csrf
                    <!-- Fila 1: Nombre -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="name" class="form-label mb-0">Nombre</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre de la sede"
                                        id="name" name="name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Guardar (alineado a la derecha) -->
                    <div class="row mb-3">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Agregar sede</button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de Registros -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sedes as $sede)
                                <tr>
                                    <td>{{ ($sedes->currentPage() - 1) * $sedes->perPage() + $loop->iteration }}</td>
                                    <td>{{ $sede->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $sede->id }}"
                                            data-name="{{ $sede->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $sede->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay sedes registradas.</td>
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
                <form id="editSedeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Sede</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="name" required>
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
                <form id="deleteSedeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar Sede</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar esta sede?</p>
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
            // Modal de Editar
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');

                $('#editSedeForm').attr('action', `{{ url('sedes') }}/${id}`);
                $('#edit_nombre').val(name);
            });

            // Modal de Eliminar
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');

                $('#deleteSedeForm').attr('action', `{{ url('sedes') }}/${id}`);
            });

            $('form').on('submit', function() {
                $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
            });
        });
    </script>
@endsection
