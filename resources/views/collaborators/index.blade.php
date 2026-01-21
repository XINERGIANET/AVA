@extends('template.index')

@section('header')
<h1>Colaboradores</h1>
<p>Lista de Colaboradores</p>
@endsection
@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
	<!-- Card que contiene el formulario y la tabla -->
	<div class="card shadow">
		<!-- Cuerpo del Card -->
		<div class="card-body">
			<!-- Formulario de Registro -->
			<form id="createCollaboratorForm" class="mb-5" action="{{ route('collaborators.store') }}" method="POST">
				@csrf
				<!-- Fila 1: Nombre, Teléfono y Dirección -->
				<div class="row align-items-center">
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="name" class="form-label mb-0">Nombres</label>
							</div>
							<div class="col-md-8">
								<input type="text" class="form-control" placeholder="Ingrese el nombre" id="name" name="name"
									required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="last_name" class="form-label mb-0">Apellidos</label>
							</div>
							<div class="col-md-8">
								<input type="text" class="form-control" placeholder="Ingrese el apellido" id="last_name" name="last_name"
									required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="document" class="form-label mb-0">DNI</label>
							</div>
							<div class="col-md-8">
								<input type="number" class="form-control" placeholder="Ingrese el DNI" id="document" name="document"
									required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="name" class="form-label mb-0">F. nacimiento</label>
							</div>
							<div class="col-md-8">
								<input type="date" class="form-control" placeholder="Ingrese la fecha de nacimiento" id="birth_date" name="birth_date"
									required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="phone" class="form-label mb-0">Teléfono</label>
							</div>
							<div class="col-md-8">
								<input type="text" class="form-control" placeholder="Ingrese el teléfono" id="phone" name="phone" required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="location_id" class="form-label mb-0">Sede</label>
							</div>
							<div class="col-md-8">
								<select class="form-select" id="location_id" name="location_id" required>
									<option value="">Seleccione una sede</option>
									@foreach ($locations as $location)
									<option value="{{ $location->id }}">{{ $location->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="address" class="form-label mb-0">Dirección</label>
							</div>
							<div class="col-md-8">
								<input type="text" class="form-control" placeholder="Ingrese la dirección" id="address" name="address" required>
							</div>
						</div>
					</div>
					<div class="col-md-4 mb-3">
						<div class="row align-items-center">
							<div class="col-md-4">
								<label for="pin" class="form-label mb-0">PIN</label>
							</div>
							<div class="col-md-8">
								<input type="text" class="form-control" placeholder="Ingrese el PIN" id="pin" name="pin" required>
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
					<thead>
						<tr>
							<th>N°</th>
							<th>Nombre</th>
							<th>Documento</th>
							<th>F. nacimiento</th>
							<th>Teléfono</th>
							<th>Sede</th>
							<th>PIN</th>
							<th>Dirección</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($collaborators as $collaborator)
						<tr>
							<td>{{ ($collaborators->currentPage() - 1) * $collaborators->perPage() + $loop->iteration }}</td>
							<td>{{ $collaborator->name }} {{ $collaborator->last_name }}</td>
							<td>{{ $collaborator->document }}</td>
							<td>{{ $collaborator->birth_date->format('d/m/Y') }}</td>
							<td>{{ $collaborator->phone }}</td>
							<td>{{ $collaborator->location->name }}</td>
							<td>{{ $collaborator->pin }}</td>
							<td>{{ $collaborator->address }}</td>
							<td>
								<button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
									data-id="{{ $collaborator->id }}" data-name="{{ $collaborator->name }}" data-last_name="{{ $collaborator->last_name }}"
									data-document="{{ $collaborator->document }}" data-birth_date="{{ $collaborator->birth_date->format('Y-m-d') }}" data-phone="{{ $collaborator->phone }}" data-address="{{ $collaborator->address }}">
									<i class="bi bi-pencil"></i>
								</button>
								<button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
									data-id="{{ $collaborator->id }}">
									<i class="bi bi-trash"></i>
								</button>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="5" class="text-center">No hay colaboradores registrados.</td>
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
			<form id="editCollaboratorForm" method="POST">
				@csrf
				@method('PUT')
				<div class="modal-header">
					<h5 class="modal-title">Editar Colaborador</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body row">
					<div class="col-md-6 mb-3">
						<label for="edit_nombre" class="form-label">Nombres</label>
						<input type="text" class="form-control" id="edit_nombre" name="name" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_apellido" class="form-label">Apellidos</label>
						<input type="text" class="form-control" id="edit_apellido" name="last_name" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_documento" class="form-label">Documento</label>
						<input type="number" class="form-control" id="edit_documento" name="document" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_nacimiento" class="form-label">F. nacimiento</label>
						<input type="date" class="form-control" id="edit_nacimiento" name="birth_date" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_telefono" class="form-label">Teléfono</label>
						<input type="text" class="form-control" id="edit_telefono" name="phone" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_direccion" class="form-label">Dirección</label>
						<input type="text" class="form-control" id="edit_direccion" name="address" required>
					</div>
					<div class="col-md-6 mb-3">
						<label for="edit_pin" class="form-label">PIN</label>
						<input type="text" class="form-control" id="edit_pin" name="pin" required>
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
			<form id="deleteCollaboratorForm" method="POST">
				@csrf
				@method('DELETE')
				<div class="modal-header">
					<h5 class="modal-title">Eliminar Colaborador</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p>¿Estás seguro de que deseas eliminar este colaborador?</p>
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