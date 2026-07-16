@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-building me-2 text-primary"></i>Sedes</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Sedes</li>
        </ol>
    </nav>
@endsection
@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <!-- Toolbar superior de la tarjeta -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex">
                        <!-- Espacio para futuros filtros o búsqueda -->
                    </div>
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Sede
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                        <thead>
                            <tr>
                                <th class="ps-4 fw-bold text-uppercase" style="width: 10%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">ID</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombres</th>
                                <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sedes as $sede)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td class="ps-4 text-dark">{{ $sede->id }}</td>
                                    <td class="text-dark">{{ $sede->name }}</td>
                                    <td class="pe-4 text-center">
                                        <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $sede->id }}"
                                            data-name="{{ $sede->name }}" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $sede->id }}" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="bi bi-building fs-1"></i></div>
                                        <p class="mb-0 fw-medium">No hay sedes registradas.</p>
                                    </td>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="createSedeForm" action="{{ route('sedes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="createModalLabel"><i class="bi bi-building text-primary me-2"></i>Agregar Nueva Sede</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold text-dark">Nombre de la Sede</label>
                            <input type="text" class="form-control form-control-lg" placeholder="Ej. Sede Central" id="name" name="name" required>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="editSedeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editModalLabel"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Sede</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label fw-bold text-dark">Nombre de la Sede</label>
                            <input type="text" class="form-control form-control-lg" id="edit_nombre" name="name" required>
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
                <form id="deleteSedeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Eliminar Sede</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-trash text-danger mb-3" style="font-size: 3.5rem;"></i>
                        <h4 class="mb-3 text-dark fw-bold">¿Estás seguro?</h4>
                        <p class="text-dark mb-0">¿Deseas eliminar permanentemente esta sede? Esta acción no se puede deshacer.</p>
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
