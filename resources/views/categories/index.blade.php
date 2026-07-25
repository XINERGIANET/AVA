@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Categorías de Productos</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Categorías</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar superior de la tarjeta -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('categories.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Categoría</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Combustible, Lubricantes..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 8%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase text-start ps-4" style="width: 30%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nombre</th>
                            <th class="fw-bold text-uppercase text-start ps-4" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 18%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($categories as $category)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark fw-medium">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                            <td class="text-dark fw-bold text-start ps-4">{{ $category->name }}</td>
                            <td class="text-dark text-start ps-4">{{ $category->description ?: '-' }}</td>
                            <td class="pe-4 text-center">
                                <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-description="{{ $category->description }}" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No se encontraron categorías registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCategoryForm" action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label text-dark fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Ej. Combustibles, Aditivos, Filtros..." id="create_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_description" class="form-label text-dark fw-bold">Descripción</label>
                        <textarea class="form-control" placeholder="Descripción opcional de la categoría" id="create_description" name="description" rows="3"></textarea>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label text-dark fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label text-dark fw-bold">Descripción</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Eliminar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark mb-0">¿Estás seguro de que deseas eliminar la categoría <strong id="delete_name"></strong>?</p>
                    <small class="text-muted">El registro ya no aparecerá como opción activa para la clasificación de productos.</small>
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
        // Modal Editar
        $('#editModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const description = button.data('description');

            $('#editCategoryForm').attr('action', `{{ url('categories') }}/${id}`);
            $('#edit_name').val(name);
            $('#edit_description').val(description || '');
        });

        // Modal Eliminar
        $('#deleteModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            $('#deleteCategoryForm').attr('action', `{{ url('categories') }}/${id}`);
            $('#delete_name').text(name);
        });
    });
</script>
@endsection
