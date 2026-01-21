@extends('template.index')

@section('header')
  <h1>Camiones</h1>
  <p>Lista de Camiones</p>
@endsection
@section('content')
  <div class="container-fluid content-inner mt-n5 py-0">
    <!-- Card que contiene el formulario y la tabla -->
    <div class="card shadow">
      <!-- Cuerpo del Card -->
      <div class="card-body">
        <!-- Formulario de Registro -->
        <form id="createCollaboratorForm" class="mb-5" action="{{ route('plaques.store') }}" method="POST">
          @csrf
          <div class="row mb-3 align-items-center">
            <div class="col-md-4">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <label for="name" class="form-label mb-0">Nombre</label>
                </div>
                <div class="col-md-8">
                  <input type="text" class="form-control" placeholder="Ingrese el nombre" id="name" name="name"
                    required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <label for="plate" class="form-label mb-0">Placa</label>
                </div>
                <div class="col-md-8">
                  <input type="text" class="form-control" placeholder="Ingrese la placa" id="plate" name="plate" required>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <label for="description" class="form-label mb-0">Descripción</label>
                </div>
                <div class="col-md-8">
                  <input type="text" class="form-control" placeholder="Ingrese la descripción" id="description" name="description">
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
                <th>Placa</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($plaques as $plaque)
                <tr>
                  <td>{{ ($plaques->currentPage() - 1) * $plaques->perPage() + $loop->iteration }}</td>
                  <td>{{ $plaque->name }}</td>
                  <td>{{ $plaque->plate }}</td>
                  <td>{{ $plaque->description }}</td>
                  <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                      data-id="{{ $plaque->id }}" data-name="{{ $plaque->name }}"
                      data-plate="{{ $plaque->plate }}" data-description="{{ $plaque->description }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $plaque->id }}">
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
              <label for="edit_nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="edit_nombre" name="name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_telefono" class="form-label">Placa</label>
              <input type="text" class="form-control" id="edit_telefono" name="plate" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="edit_direccion" class="form-label">Descripción</label>
              <input type="text" class="form-control" id="edit_direccion" name="description">
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
        $('#editCollaboratorForm').attr('action', `{{ url('plaques') }}/${id}`);

        // Prellenar los campos del formulario con los datos del colaborador
        $('#edit_nombre').val(button.data('name'));
        $('#edit_telefono').val(button.data('plate'));
        $('#edit_direccion').val(button.data('description'));
      });

      // Modal de Eliminar
      $('#deleteModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del colaborador

        // Actualizar la acción del formulario con el ID del colaborador
        $('#deleteCollaboratorForm').attr('action', `{{ url('plaques') }}/${id}`);
      });
    });
  </script>
@endsection
