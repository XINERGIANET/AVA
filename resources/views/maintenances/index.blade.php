@extends('template.index')

@section('header')
  <h1>Mantenimiento</h1>
  <p>Lista de Mantenimientos</p>
@endsection
@section('content')
  <div class="container-fluid content-inner mt-n5 py-0">
    <!-- Card que contiene el formulario y la tabla -->
    <div class="card shadow">
      <!-- Cuerpo del Card -->
      <div class="card-body">
        <!-- Formulario de Registro -->
        <form id="createCollaboratorForm" class="mb-5" action="{{ route('maintenances.store') }}" method="POST">
          @csrf
          <div class="row mb-3 align-items-center">
            <div class="col-md-4">
              <div class="row align-items-center">
                <div class="col-md-4">
                  <label for="name" class="form-label mb-0">Fecha</label>
                </div>
                <div class="col-md-8">
                  <input type="date" class="form-control" id="date" name="date"
                    required>
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
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($maintenances as $mant)
                <tr>
                  <td>{{ ($maintenances->currentPage() - 1) * $maintenances->perPage() + $loop->iteration }}</td>
                  <td>{{ $mant->date->format('d/m/Y') }}</td>
                  <td>{{ $mant->description }}</td>
                  <td>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $mant->id }}">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">No hay manteminientos registrados.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Eliminar -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="deletMaintenanceForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-header">
            <h5 class="modal-title">Eliminar Mantenimiento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este registro?</p>
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
      // $('#editModal').on('show.bs.modal', function(event) {
      //   const button = $(event.relatedTarget); // Botón que activó el modal
      //   const id = button.data('id'); // Obtener el ID del colaborador

      //   // Actualizar la acción del formulario con el ID del colaborador
      //   $('#editCollaboratorForm').attr('action', `{{ url('maintenances') }}/${id}`);

      //   // Prellenar los campos del formulario con los datos del colaborador
      //   $('#edit_nombre').val(button.data('name'));
      //   $('#edit_direccion').val(button.data('description'));
      // });

      // Modal de Eliminar
      $('#deleteModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del colaborador

        // Actualizar la acción del formulario con el ID del colaborador
        $('#deletMaintenanceForm').attr('action', `{{ url('maintenances') }}/${id}`);
      });
    });
  </script>
@endsection
