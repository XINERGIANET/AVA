@extends('template.index')

@section('header')
  <h1>Islas</h1>
  <p>Lista de Islas</p>
@endsection

@section('content')
  @include('components.spinner')

  <div class="container-fluid content-inner mt-n5 py-0">
    <div class="card shadow">
      <div class="card-body">

        {{-- FORMULARIO DE REGISTRO --}}
        <form id="createIsleForm" class="mb-5" action="{{ route('isles.store') }}" method="POST">
          @csrf

          <div class="row mb-3">

            {{-- Nombre --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre de la isla</label>
              <input type="text" class="form-control" placeholder="Ingrese nombre" id="name" name="name" required>
            </div>

            {{-- Sede --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Sede</label>
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

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Agregar isla</button>
          </div>

        </form>

        {{-- TABLA DE REGISTROS --}}
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>N°</th>
                <th>Nombre</th>
                <th>Sede</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>

              @forelse ($isles as $isle)
                <tr>
                  <td>{{ ($isles->currentPage() - 1) * $isles->perPage() + $loop->iteration }}</td>
                  <td>{{ $isle->name }}</td>
                  <td>{{ $isle->location->name ?? 'Sin sede' }}</td>

                  <td>
                    {{-- Botón editar --}}
                    <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="{{ $isle->id }}"
                            data-name="{{ $isle->name }}"
                            data-location="{{ $isle->location_id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    {{-- Botón eliminar --}}
                    <button class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-id="{{ $isle->id }}">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center">No hay islas registradas.</td>
                </tr>
              @endforelse

            </tbody>
          </table>

          {{-- Paginación --}}
          {{ $isles->links() }}

        </div>
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
            <h5 class="modal-title">Editar isla</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body row">

            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Sede</label>
              <select name="location_id" id="edit_location_id" class="form-control" required>
                @foreach($locations as $loc)
                  <option value="{{ $loc->id }}">{{ $loc->name }}</option>
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

        <form id="deleteIsleForm" method="POST">
          @csrf
          @method('DELETE')

          <div class="modal-header">
            <h5 class="modal-title">Eliminar isla</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar esta isla?</p>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Eliminar</button>
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
