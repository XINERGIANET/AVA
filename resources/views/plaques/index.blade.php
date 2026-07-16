@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Placas (Camiones)</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Placas</li>
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
                    <form action="{{ route('plaques.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Placa o Nombre</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. ABC-123..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Placa
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
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Placa</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($plaques as $plaque)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark">{{ ($plaques->currentPage() - 1) * $plaques->perPage() + $loop->iteration }}</td>
                            <td class="text-dark">{{ $plaque->name }}</td>
                            <td class="text-dark">{{ $plaque->plate }}</td>
                            <td class="text-dark">{{ $plaque->description ?: '-' }}</td>
                            <td class="pe-4 text-center">
                                <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $plaque->id }}" data-name="{{ $plaque->name }}"
                                data-plate="{{ $plaque->plate }}" data-description="{{ $plaque->description }}" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $plaque->id }}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No hay placas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $plaques->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createPlaqueForm" action="{{ route('plaques.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nueva Placa (Camión)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label text-dark fw-bold">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese el nombre" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="plate" class="form-label text-dark fw-bold">Placa</label>
                        <input type="text" class="form-control" placeholder="Ingrese la placa" id="plate" name="plate" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label text-dark fw-bold">Descripción</label>
                        <input type="text" class="form-control" placeholder="Ingrese la descripción" id="description" name="description">
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
            <h5 class="modal-title text-dark fw-bold">Editar Placa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row">
            <div class="col-md-6 mb-3">
              <label for="edit_nombre" class="form-label text-dark fw-bold">Nombre</label>
              <input type="text" class="form-control" id="edit_nombre" name="name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_telefono" class="form-label text-dark fw-bold">Placa</label>
              <input type="text" class="form-control" id="edit_telefono" name="plate" required>
            </div>
            <div class="col-md-12 mb-3">
              <label for="edit_direccion" class="form-label text-dark fw-bold">Descripción</label>
              <input type="text" class="form-control" id="edit_direccion" name="description">
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
            <h5 class="modal-title text-dark fw-bold">Eliminar Placa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-dark">¿Estás seguro de que deseas eliminar esta placa?</p>
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
