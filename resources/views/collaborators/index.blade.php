@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Colaboradores</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Colaboradores</li>
        </ol>
    </nav>
@endsection
@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar superior de la tarjeta -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('collaborators.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar por Nombre o DNI</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Juan Perez..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Colaborador
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Documento</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">F. nacimiento</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Teléfono</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">PIN</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Dirección</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 12%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($collaborators as $collaborator)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark">{{ ($collaborators->currentPage() - 1) * $collaborators->perPage() + $loop->iteration }}</td>
                            <td class="text-dark">{{ $collaborator->name }} {{ $collaborator->last_name }}</td>
                            <td class="text-dark">{{ $collaborator->document }}</td>
                            <td class="text-dark">{{ $collaborator->birth_date->format('d/m/Y') }}</td>
                            <td class="text-dark">{{ $collaborator->phone }}</td>
                            <td class="text-dark">{{ $collaborator->location->name ?? '-' }}</td>
                            <td class="text-dark">{{ $collaborator->pin }}</td>
                            <td class="text-dark">{{ $collaborator->address }}</td>
                            <td class="pe-4 text-center">
                                <button class="btn btn-sm btn-warning text-white me-1 mb-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $collaborator->id }}" data-name="{{ $collaborator->name }}" data-last_name="{{ $collaborator->last_name }}"
                                    data-document="{{ $collaborator->document }}" data-birth_date="{{ $collaborator->birth_date->format('Y-m-d') }}" data-phone="{{ $collaborator->phone }}" data-address="{{ $collaborator->address }}" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-danger text-white mb-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $collaborator->id }}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No hay colaboradores registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $collaborators->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createCollaboratorForm" action="{{ route('collaborators.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nuevo Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label text-dark fw-bold">Nombres</label>
                        <input type="text" class="form-control" placeholder="Ingrese el nombre" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label text-dark fw-bold">Apellidos</label>
                        <input type="text" class="form-control" placeholder="Ingrese el apellido" id="last_name" name="last_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document" class="form-label text-dark fw-bold">DNI</label>
                        <input type="number" class="form-control" placeholder="Ingrese el DNI" id="document" name="document" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label text-dark fw-bold">F. nacimiento</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label text-dark fw-bold">Teléfono</label>
                        <input type="text" class="form-control" placeholder="Ingrese el teléfono" id="phone" name="phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label text-dark fw-bold">Sede</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="">Seleccione una sede</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label text-dark fw-bold">Dirección</label>
                        <input type="text" class="form-control" placeholder="Ingrese la dirección" id="address" name="address" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pin" class="form-label text-dark fw-bold">PIN</label>
                        <input type="text" class="form-control" placeholder="Ingrese el PIN" id="pin" name="pin" required>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editCollaboratorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Editar Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_nombre" class="form-label text-dark fw-bold">Nombres</label>
                        <input type="text" class="form-control" id="edit_nombre" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_apellido" class="form-label text-dark fw-bold">Apellidos</label>
                        <input type="text" class="form-control" id="edit_apellido" name="last_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_documento" class="form-label text-dark fw-bold">Documento</label>
                        <input type="number" class="form-control" id="edit_documento" name="document" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_nacimiento" class="form-label text-dark fw-bold">F. nacimiento</label>
                        <input type="date" class="form-control" id="edit_nacimiento" name="birth_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_telefono" class="form-label text-dark fw-bold">Teléfono</label>
                        <input type="text" class="form-control" id="edit_telefono" name="phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_direccion" class="form-label text-dark fw-bold">Dirección</label>
                        <input type="text" class="form-control" id="edit_direccion" name="address" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_pin" class="form-label text-dark fw-bold">PIN</label>
                        <input type="text" class="form-control" id="edit_pin" name="pin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteCollaboratorForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Eliminar Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">¿Estás seguro de que deseas eliminar este colaborador?</p>
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
	$(document).ready(function() {
		// Modal de Editar
		$('#editModal').on('show.bs.modal', function(event) {
			const button = $(event.relatedTarget); // Botón que activó el modal
			const id = button.data('id'); // Obtener el ID del colaborador

			// Actualizar la acción del formulario con el ID del colaborador
			$('#editCollaboratorForm').attr('action', `{{ url('collaborators') }}/${id}`);

			// Prellenar los campos del formulario con los datos del colaborador
			$('#edit_nombre').val(button.data('name'));
			$('#edit_apellido').val(button.data('last_name'));
			$('#edit_documento').val(button.data('document'));
			$('#edit_nacimiento').val(button.data('birth_date'));
			$('#edit_telefono').val(button.data('phone'));
			$('#edit_direccion').val(button.data('address'));
			$('#edit_pin').val(button.data('pin'));
		});

		// Modal de Eliminar
		$('#deleteModal').on('show.bs.modal', function(event) {
			const button = $(event.relatedTarget); // Botón que activó el modal
			const id = button.data('id'); // Obtener el ID del colaborador

			// Actualizar la acción del formulario con el ID del colaborador
			$('#deleteCollaboratorForm').attr('action', `{{ url('collaborators') }}/${id}`);
		});
	});
</script>
@endsection