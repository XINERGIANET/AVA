@extends('template.index')

@section('header')
<h1>Surtidores</h1>
<p>Lista de Surtidores</p>
@endsection

@section('content')
@include('components.spinner')

<div class="container-fluid content-inner mt-n5 py-0">
  <div class="card shadow">
    <div class="card-body">

      {{-- FORMULARIO DE REGISTRO --}}
      <form id="createFuelPumpForm" class="mb-5" action="{{ route('fuelpumps.store') }}" method="POST">
        @csrf

        <div class="row mb-3">

          {{-- Nombre --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Nombre del Surtidor</label>
            <input type="text" class="form-control" placeholder="Ingrese nombre" id="name"
              name="name" required>
          </div>

          {{-- Isla --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Isla</label>
            <select name="isle_id" id="isle_id" class="form-control" required>
              <option value="">-- Seleccione una isla --</option>
              @foreach ($isles as $isle)
              <option value="{{ $isle->id }}">{{ $isle->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Lado</label>
            <select name="side" id="side" class="form-control" required>
              <option value="">-- Seleccione un lado --</option>
              <option value="1">1</option>
              <option value="2">2</option>
            </select>
          </div>

          {{-- Producto --}}
          <div class="col-md-6 mb-3">
            <label class="form-label">Producto</label>
            <select name="product_id" id="product_id" class="form-control" required>
              <option value="">-- Seleccione un producto --</option>
              @foreach ($products as $product)
              <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>

        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">Agregar surtidor</button>
        </div>

      </form>

      {{-- TABLA DE REGISTROS --}}
      <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>Isla</th>
              <th>Producto</th>
              <th>Lado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>

            {{-- Iterar sobre surtidores --}}
            @foreach ($fuelpumps as $fuelpump)
            <tr>
              <td>{{ ($fuelpumps->currentPage() - 1) * $fuelpumps->perPage() + $loop->iteration }}
              </td>
              <td>{{ $fuelpump->name }}</td>
              <td>{{ $fuelpump->isle->name ?? 'Sin isla' }}</td>
              <td>{{ $fuelpump->product->name }}</td>
              <td>{{ $fuelpump->side }}</td>

              <td>
                {{-- Botón editar --}}
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                  data-bs-target="#editModal" data-id="{{ $fuelpump->id }}"
                  data-name="{{ $fuelpump->name }}" data-isle="{{ $fuelpump->isle_id }}"
                  data-product="{{ $fuelpump->product_id }}" data-side="{{ $fuelpump->side }}">
                  <i class="bi bi-pencil"></i>
                </button>

                {{-- Botón eliminar --}}
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                  data-bs-target="#deleteModal" data-id="{{ $fuelpump->id }}">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            @endforeach

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

      $('#editFuelPumpForm').attr('action', `{{ url('fuelpumps') }}/${id}`);
      $('#edit_name').val(name);
      $('#edit_isle_id').val(isle_id);
      $('#edit_product_id').val(product_id);
      $('#edit_side').val(side);
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