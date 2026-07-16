@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-shop me-2 text-primary"></i>Proveedores</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Proveedores</li>
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
                    <form action="{{ route('suppliers.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Razón Social o RUC</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. 20123456789..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Razón Social</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">DNI/RUC</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre comercial</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Teléfono</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($suppliers as $supplier)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                            <td class="text-dark">{{ $supplier->company_name }}</td>
                            <td class="text-dark">{{ $supplier->document }}</td>
                            <td class="text-dark">{{ $supplier->commercial_name ?: '-' }}</td>
                            <td class="text-dark">{{ $supplier->phone }}</td>
                            <td class="pe-4 text-center">
                                <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $supplier->id }}" data-company_name="{{ $supplier->company_name }}"
                                data-document="{{ $supplier->document }}" data-commercial_name="{{ $supplier->commercial_name }}"
                                data-phone="{{ $supplier->phone }}" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $supplier->id }}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay proveedores registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $suppliers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createSupplierForm" action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="company_name" class="form-label text-dark fw-bold">Razón Social</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="document" class="form-label text-dark fw-bold">DNI / RUC</label>
                        <input type="number" class="form-control" id="document" name="document" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="commercial_name" class="form-label text-dark fw-bold">Nombre comercial</label>
                        <input type="text" class="form-control" id="commercial_name" name="commercial_name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label text-dark fw-bold">Teléfono</label>
                        <input type="number" class="form-control" id="phone" name="phone" required>
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
            <form id="editSupplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Editar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_razon_social" class="form-label text-dark fw-bold">Razón Social</label>
                        <input type="text" class="form-control" id="edit_razon_social" name="company_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_dni_ruc" class="form-label text-dark fw-bold">DNI/RUC</label>
                        <input type="text" class="form-control" id="edit_dni_ruc" name="document" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_nombre_comercial" class="form-label text-dark fw-bold">Nombre Comercial</label>
                        <input type="text" class="form-control" id="edit_nombre_comercial" name="commercial_name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_telefono" class="form-label text-dark fw-bold">Teléfono</label>
                        <input type="text" class="form-control" id="edit_telefono" name="phone" required>
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
            <form id="deleteSupplierForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Eliminar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">¿Estás seguro de que deseas eliminar este proveedor?</p>
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
