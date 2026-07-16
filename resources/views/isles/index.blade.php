@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-geo-alt me-2 text-primary"></i>Islas</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Islas</li>
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
                <form action="{{ route('isles.index') }}" method="GET" id="filterForm">
                    <div class="row g-2 align-items-end">
                        @if (auth()->user()->role->nombre == 'master')
                        <div class="col-md-4">
                            <label for="filter_location" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                            <select name="location_id" id="filter_location" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-5">
                            <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Nombre</label>
                            <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Isla 1..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Isla
                </button>
            </div>
        </div>

        {{-- TABLA DE REGISTROS --}}
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
            <thead class="text-center">
              <tr>
                <th class="fw-bold text-uppercase" style="width: 10%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">ID</th>
                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre</th>
                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
              </tr>
            </thead>
            <tbody class="text-center">

              @forelse ($isles as $isle)
                <tr style="border-bottom: 1px solid #e9ecef;">
                  <td class="text-dark">{{ $isle->id }}</td>
                  <td class="text-dark">{{ $isle->name }}</td>
                  <td class="text-dark">{{ $isle->location->name ?? 'Sin sede' }}</td>

                  <td class="pe-4 text-center">
                    {{-- Botón editar --}}
                    <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="{{ $isle->id }}"
                            data-name="{{ $isle->name }}"
                            data-location="{{ $isle->location_id }}" title="Editar">
                      <i class="bi bi-pencil-fill"></i>
                    </button>

                    {{-- Botón eliminar --}}
                    <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-id="{{ $isle->id }}" title="Eliminar">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4">No hay islas registradas.</td>
                </tr>
              @endforelse

            </tbody>
          </table>

          {{-- Paginación --}}
          <div class="d-flex justify-content-center mt-3">
              {{ $isles->links() }}
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- MODAL CREAR --}}
  <div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="createIsleForm" action="{{ route('isles.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title text-dark fw-bold">Nueva Isla</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-dark fw-bold">Nombre de la isla</label>
              <input type="text" class="form-control" placeholder="Ingrese nombre" id="name" name="name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-dark fw-bold">Sede</label>
              <select name="location_id" id="location_id" class="form-control" required {{ auth()->user()->role->nombre != 'master' ? 'disabled' : '' }}>
                <option value="" disabled selected>-- Seleccione una sede --</option>
                @if(auth()->user()->role->nombre == 'master')
                  @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                  @endforeach
                @else
                  <option value="{{ auth()->user()->location_id }}" selected>{{ auth()->user()->location->name }}</option>
                @endif
              </select>
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

  {{-- MODAL EDITAR --}}
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="editIsleForm" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title text-dark fw-bold">Editar isla</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body row">

            <div class="col-md-6 mb-3">
              <label class="form-label text-dark fw-bold">Nombre</label>
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label text-dark fw-bold">Sede</label>
              <select name="location_id" id="edit_location_id" class="form-control" required>
                @foreach($locations as $loc)
                  <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
              </select>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary px-4">Guardar cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- MODAL ELIMINAR --}}
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form id="deleteIsleForm" method="POST">
          @csrf
          @method('DELETE')

          <div class="modal-header">
            <h5 class="modal-title text-dark fw-bold">Eliminar isla</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <p class="text-dark">¿Estás seguro de que deseas eliminar esta isla?</p>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger px-4">Eliminar</button>
          </div>

        </form>

      </div>
    </div>
  </div>


  {{-- SCRIPTS --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {

      // EDITAR
      $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const name = button.data('name');
        const location_id = button.data('location');

        $('#editIsleForm').attr('action', `{{ url('isles') }}/${id}`);
        $('#edit_name').val(name);
        $('#edit_location_id').val(location_id);
      });

      // ELIMINAR
      $('#deleteModal').on('show.bs.modal', function(event) {
        const id = $(event.relatedTarget).data('id');
        $('#deleteIsleForm').attr('action', `{{ url('isles') }}/${id}`);
      });

      // Spinner
      $('form').on('submit', function() {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
      });

    });
  </script>

@endsection
