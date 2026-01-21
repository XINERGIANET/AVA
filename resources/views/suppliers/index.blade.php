@extends('template.index')

@section('header')
  <h1>Proovedores</h1>
  <p>Lista de proovedores</p>
@endsection
@section('content')
  <div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <div class="header-title w-100">
                <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                <div class="mb-3 row">
                  <label for="company_name" class="col-sm-3 col-form-label text-start">Razón Social</label>
                  <div class="col-sm-3">
                  <input type="text" class="form-control border-dark" id="company_name" name="company_name" required>
                  </div>
                  <label for="document" class="col-sm-3 col-form-label text-start">DNI / RUC</label>
                  <div class="col-sm-3">
                  <input type="number" class="form-control border-dark" id="document" name="document" required>
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="commercial_name" class="col-sm-3 col-form-label text-start">Nombre comercial</label>
                  <div class="col-sm-3">
                  <input type="text" class="form-control border-dark" id="commercial_name" name="commercial_name">
                  </div>
                  <label for="telefono" class="col-sm-3 col-form-label text-start">Teléfono</label>
                  <div class="col-sm-3">
                  <input type="number" class="form-control border-dark" id="phone" name="phone" required>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                </form>
            </div>
          </div>


          <div class="card-body p-3">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>N°</th>
                    <th>Razón Social</th>
                    <th>DNI/RUC</th>
                    <th>Nombre comercial</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($suppliers as $supplier)
                    <tr>
                      <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                      <td>{{ $supplier->company_name }}</td>
                      <td>{{ $supplier->document }}</td>
                      <td>{{ $supplier->commercial_name }}</td>
                      <td>{{ $supplier->phone }}</td>
                      <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                          data-id="{{ $supplier->id }}" data-company_name="{{ $supplier->company_name }}"
                          data-document="{{ $supplier->document }}"
                          data-commercial_name="{{ $supplier->commercial_name }}"
                          data-phone="{{ $supplier->phone }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                          data-id="{{ $supplier->id }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center">No hay proveedores registrados.</td>
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
            <form id="editSupplierForm" method="POST">
              @csrf
              @method('PUT')
              <div class="modal-header">
                <h5 class="modal-title">Editar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body row">
                <div class="col-md-6 mb-3">
                  <label for="edit_razon_social" class="form-label">Razón Social</label>
                  <input type="text" class="form-control" id="edit_razon_social" name="company_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="edit_dni_ruc" class="form-label">DNI/RUC</label>
                  <input type="text" class="form-control" id="edit_dni_ruc" name="document" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="edit_nombre_comercial" class="form-label">Nombre Comercial</label>
                  <input type="text" class="form-control" id="edit_nombre_comercial" name="commercial_name">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="edit_telefono" class="form-label">Teléfono</label>
                  <input type="text" class="form-control" id="edit_telefono" name="phone" required>
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
            <form id="deleteSupplierForm" method="POST">
              @csrf
              @method('DELETE')
              <div class="modal-header">
                <h5 class="modal-title">Eliminar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este proveedor?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Modal de Editar
      $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del proveedor

        // Actualizar la acción del formulario con el ID del proveedor
        $('#editSupplierForm').attr('action', `{{ url('suppliers') }}/${id}`);

        // Prellenar los campos del formulario con los datos del proveedor
        $('#edit_razon_social').val(button.data('company_name'));
        $('#edit_dni_ruc').val(button.data('document'));
        $('#edit_nombre_comercial').val(button.data('commercial_name'));
        $('#edit_telefono').val(button.data('phone'));
      });

      // Modal de Eliminar
      $('#deleteModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del proveedor

        // Actualizar la acción del formulario con el ID del proveedor
        $('#deleteSupplierForm').attr('action', `{{ url('suppliers') }}/${id}`);
      });
    });
  </script>
@endsection
