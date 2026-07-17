@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-tools me-2 text-primary"></i>Lista de Mantenimientos
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Control y Soporte</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Mantenimientos</li>
        </ol>
    </nav>
@endsection
@section('content')
  <div class="container-fluid content-inner" style="padding-top: 1rem;">
    <!-- Encabezado y botón de registro -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="mb-0 fw-bold text-dark">Registros de Mantenimiento</h5>
      <button type="button" class="btn btn-primary px-4 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
        <i class="bi bi-plus-circle me-1"></i> Registrar Mantenimiento
      </button>
    </div>

    <!-- Card que contiene la tabla -->
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
      <!-- Cuerpo del Card -->
      <div class="card-body">

        <!-- Tabla de Registros -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
            <thead class="text-center">
              <tr>
                <th class="fw-bold text-uppercase" style="width: 10%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                <th class="fw-bold text-uppercase" style="width: 20%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                <th class="fw-bold text-uppercase" style="width: 55%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                <th class="fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
              </tr>
            </thead>
            <tbody class="text-center">
              @forelse ($maintenances as $mant)
                <tr>
                  <td>{{ ($maintenances->currentPage() - 1) * $maintenances->perPage() + $loop->iteration }}</td>
                  <td>{{ $mant->date->format('d/m/Y') }}</td>
                  <td>{{ $mant->description }}</td>
                  <td>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $mant->id }}" style="border-radius: 6px;" title="Eliminar mantenimiento">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">No hay mantenimientos registrados.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Registrar -->
  <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0 shadow">
        <form action="{{ route('maintenances.store') }}" method="POST">
          @csrf
          <div class="modal-header bg-light border-bottom-0">
            <h5 class="modal-title fw-bold text-dark" id="createModalLabel"><i class="bi bi-tools me-2 text-primary"></i>Registrar Mantenimiento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="date" class="form-label text-dark fw-bold mb-1">Fecha</label>
              <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="mb-3">
              <label for="description" class="form-label text-dark fw-bold mb-1">Descripción</label>
              <textarea class="form-control" placeholder="Ingrese la descripción" id="description" name="description" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer border-top-0 bg-light">
            <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
            <button type="submit" class="btn btn-primary px-4 fw-medium btn-sm"><i class="bi bi-save me-1"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Eliminar -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content border-0 shadow">
        <form id="deletMaintenanceForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="modal-header bg-danger text-white border-bottom-0">
            <h5 class="modal-title fw-bold text-white"><i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Mantenimiento</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center py-4">
            <p class="mb-0">¿Estás seguro de que deseas eliminar este registro?</p>
          </div>
          <div class="modal-footer border-top-0 bg-light justify-content-center">
            <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger px-4 fw-medium btn-sm">Eliminar</button>
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
