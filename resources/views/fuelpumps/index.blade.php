@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-ev-station me-2 text-primary"></i>Surtidores</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Surtidores</li>
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
              <form action="{{ route('fuelpumps.index') }}" method="GET" id="filterForm">
                  <div class="row g-2 align-items-end">
                      <div class="col-md-3">
                          <label for="filter_isle" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Isla</label>
                          <select name="isle_id" id="filter_isle" class="form-select form-select-sm">
                              <option value="">Todas</option>
                              @foreach ($isles as $isle)
                                  <option value="{{ $isle->id }}" {{ request('isle_id') == $isle->id ? 'selected' : '' }}>{{ $isle->name }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-3">
                          <label for="filter_product" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Producto</label>
                          <select name="product_id" id="filter_product" class="form-select form-select-sm">
                              <option value="">Todos</option>
                              @foreach ($products as $product)
                                  <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-4">
                          <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Nombre</label>
                          <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Surtidor 1..." value="{{ request('search') }}">
                      </div>
                      <div class="col-md-2">
                          <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                      </div>
                  </div>
              </form>
          </div>
          <div class="col-md-3 text-end">
              <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                  <i class="bi bi-plus-lg me-1"></i> Nuevo Surtidor
              </button>
          </div>
      </div>

      {{-- TABLA DE REGISTROS --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
          <thead class="text-center">
            <tr>
              <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">ID</th>
              <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre</th>
              <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
              <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
              <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Lado</th>
              <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
              <th class="pe-4 text-center fw-bold text-uppercase" style="width: 22%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-center">

            {{-- Iterar sobre surtidores --}}
            @forelse ($fuelpumps as $fuelpump)
            <tr style="border-bottom: 1px solid #e9ecef;">
              <td class="text-dark">{{ $fuelpump->id }}</td>
              <td class="text-dark">{{ $fuelpump->name }}</td>
              <td class="text-dark">{{ $fuelpump->isle->name ?? 'Sin isla' }}</td>
              <td class="text-dark">{{ $fuelpump->product->name ?? 'Sin producto' }}</td>
              <td class="text-dark">{{ $fuelpump->side }}</td>
              <td class="text-center">
                @if(($fuelpump->status ?? 'activo') === 'inoperativo')
                  <span class="badge bg-danger rounded-2 px-2 py-1"><i class="bi bi-x-circle me-1"></i>Inoperativo</span>
                @else
                  <span class="badge bg-success rounded-2 px-2 py-1"><i class="bi bi-check-circle me-1"></i>Activo</span>
                @endif
              </td>

              <td class="pe-4 text-center">
                {{-- Botón toggle estado --}}
                <form action="{{ route('fuelpumps.status', $fuelpump->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PATCH')
                  @if(($fuelpump->status ?? 'activo') === 'inoperativo')
                    <button type="submit" class="btn btn-sm btn-outline-success me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" title="Activar surtidor">
                      <i class="bi bi-check-circle-fill me-1"></i>Activar
                    </button>
                  @else
                    <button type="submit" class="btn btn-sm btn-outline-warning text-dark me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" title="Marcar Inoperativo">
                      <i class="bi bi-slash-circle-fill me-1"></i>Inoperativo
                    </button>
                  @endif
                </form>

                {{-- Botón editar --}}
                <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                  data-bs-target="#editModal" data-id="{{ $fuelpump->id }}"
                  data-name="{{ $fuelpump->name }}" data-isle="{{ $fuelpump->isle_id }}"
                  data-product="{{ $fuelpump->product_id }}" data-side="{{ $fuelpump->side }}"
                  data-status="{{ $fuelpump->status ?? 'activo' }}" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </button>

                {{-- Botón eliminar --}}
                <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal"
                  data-bs-target="#deleteModal" data-id="{{ $fuelpump->id }}" title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4">No hay surtidores registrados.</td>
            </tr>
            @endforelse

          </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
          {{-- Paginación --}}
          {{ $fuelpumps->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="createFuelPumpForm" action="{{ route('fuelpumps.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title text-dark fw-bold">Nuevo Surtidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row">
          <div class="col-md-6 mb-3">
            <label class="form-label text-dark fw-bold">Nombre del Surtidor</label>
            <input type="text" class="form-control" placeholder="Ingrese nombre" id="name" name="name" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-dark fw-bold">Isla</label>
            <select name="isle_id" id="isle_id" class="form-control" required>
              <option value="">-- Seleccione una isla --</option>
              @foreach ($isles as $isle)
              <option value="{{ $isle->id }}">{{ $isle->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-dark fw-bold">Lado</label>
            <select name="side" id="side" class="form-control" required>
              <option value="">-- Seleccione un lado --</option>
              <option value="1">1</option>
              <option value="2">2</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-dark fw-bold">Producto</label>
            <select name="product_id" id="product_id" class="form-control" required>
              <option value="">-- Seleccione un producto --</option>
              @foreach ($products as $product)
              <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-dark fw-bold">Estado</label>
            <select name="status" id="status" class="form-control" required>
              <option value="activo" selected>Activo</option>
              <option value="inoperativo">Inoperativo</option>
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
      <form id="editFuelPumpForm" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Editar Surtidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body row">

          <div class="col-md-6 mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Isla</label>
            <select name="isle_id" id="edit_isle_id" class="form-control" required>
              @foreach ($isles as $isle)
              <option value="{{ $isle->id }}">{{ $isle->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Lado</label>
            <input type="text" class="form-control" placeholder="Ingrese lado" id="edit_side"
              name="side" required>
          </div>

          {{-- Producto --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Producto</label>
            <select name="product_id" id="edit_product_id" class="form-control" required>
              <option value="">-- Seleccione un producto --</option>
              @foreach ($products as $product)
              <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Estado</label>
            <select name="status" id="edit_status" class="form-control" required>
              <option value="activo">Activo</option>
              <option value="inoperativo">Inoperativo</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="deleteFuelPumpForm" method="POST">
        @csrf
        @method('DELETE')

        <div class="modal-header">
          <h5 class="modal-title">Eliminar surtidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar este surtidor?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>

      </form>

    </div>
  </div>
</div>
@endsection

@section('scripts')
{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {

    // EDITAR
    $('#editModal').on('show.bs.modal', function(event) {
      const button = $(event.relatedTarget);
      const id = button.data('id');
      const name = button.data('name');
      const isle_id = button.data('isle');
      const product_id = button.data('product');
      const side = button.data('side');
      const status = button.data('status') || 'activo';

      $('#editFuelPumpForm').attr('action', `{{ url('fuelpumps') }}/${id}`);
      $('#edit_name').val(name);
      $('#edit_isle_id').val(isle_id);
      $('#edit_product_id').val(product_id);
      $('#edit_side').val(side);
      $('#edit_status').val(status);
    });

    // ELIMINAR
    $('#deleteModal').on('show.bs.modal', function(event) {
      const id = $(event.relatedTarget).data('id');
      $('#deleteFuelPumpForm').attr('action', `{{ url('fuelpumps') }}/${id}`);
    });

    // Spinner
    $('form').on('submit', function() {
      $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
    });

  });
</script>
@endsection