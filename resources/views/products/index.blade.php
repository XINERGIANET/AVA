@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Productos</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Productos</li>
        </ol>
    </nav>
@endsection

@include('components.spinner')

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar superior de la tarjeta -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Nombre</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. Diésel..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Marca</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Categoría</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Und. Medida</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($products as $product)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark">{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                            <td class="text-dark">{{ $product->name ?: '-' }}</td>
                            <td class="text-dark">{{ $product->brand ?: '-' }}</td>
                            <td class="text-dark">{{ $product->type ?: '-' }}</td>
                            <td class="text-dark">{{ $product->category ?: '-' }}</td>
                            <td class="text-dark">{{ $product->measurement_unit ?: '-' }}</td>
                            <td class="pe-4 text-center">
                                <!-- Botón para editar -->
                                <button class="btn btn-sm btn-warning text-white me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-brand="{{ $product->brand }}"
                                data-type="{{ $product->type }}"
                                data-category="{{ $product->category }}"
                                data-measurement_unit="{{ $product->measurement_unit }}"
                                data-prices='@json($product->location_prices ? $product->location_prices->pluck("unit_price", "location_id") : [])' title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                <!-- Botón para eliminar -->
                                <button class="btn btn-sm btn-danger text-white" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $product->id }}" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No hay productos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createProductForm" action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label text-dark fw-bold">Producto</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label text-dark fw-bold">Categoría</label>
                        <div class="input-group">
                            <select name="category" id="category" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" onclick="openQuickCategoryModal(event)" title="Agregar nueva categoría">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label text-dark fw-bold">Precio por Sede</label>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0" id="productionTable">
                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locations as $location)
                                    <tr>
                                        <td class="align-middle">{{ $location->name }}</td>
                                        <td>
                                            <input type="number"
                                                id="unit_price_{{ $location->id }}"
                                                name="unit_price[{{ $location->id }}]"
                                                class="form-control form-control-sm cantidad-input"
                                                min="0.01"
                                                step="0.01"
                                                placeholder="0.00">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
            <form id="editProductForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_nombre" class="form-label">Producto</label>
                        <input type="text" class="form-control" id="edit_nombre" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="edit_marca" name="brand">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_tipo" class="form-label">Tipo</label>
                        <input type="text" class="form-control" id="edit_tipo" name="type">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_categoria" class="form-label text-dark fw-bold">Categoría</label>
                        <div class="input-group">
                            <select name="category" id="edit_categoria" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" onclick="openQuickCategoryModal(event)" title="Agregar nueva categoría">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_unidad_medida" class="form-label">Unidad de Medida</label>
                        <input type="text" class="form-control" id="edit_unidad_medida" name="measurement_unit">
                    </div>
                    <div class="col-md-12 mb-3 row align-items-center"> <!-- alineación vertical centrada -->
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <!-- Label centrado vertical y horizontalmente -->
                            <label for="edit_unit_price" class="col-form-label text-center w-100">
                                Precio por Sede
                            </label>
                        </div>
                        <div class="col-md-10">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="productionTable">
                                    <thead>
                                        <tr>
                                            <th>Sede</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($locations as $location)
                                        <tr>
                                            <td>{{ $location->name }}</td>
                                            <td>
                                                <input type="number"
                                                    id="edit_unit_price_{{ $location->id }}"
                                                    name="unit_price[{{ $location->id }}]"
                                                    class="form-control cantidad-input"
                                                    min="0.01"
                                                    step="0.01"
                                                    placeholder="0.00">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
            <form id="deleteProductForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este producto?</p>
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
<script>
    var productSpinner = document.getElementById('spinner');

    // Modal de Editar
    $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del producto

        // Actualizar la acción del formulario con el ID del producto
        $('#editProductForm').attr('action', `{{ url('products') }}/${id}`);

        // Prellenar los campos del formulario con los datos del producto
        $('#edit_nombre').val(button.data('name'));
        $('#edit_marca').val(button.data('brand'));
        $('#edit_tipo').val(button.data('type'));
        $('#edit_categoria').val(button.data('category'));
        $('#edit_unidad_medida').val(button.data('measurement_unit'));

        //llenado de precios localmente, no hacer en pantallas pesadas sino usar /show
        const prices = button.data('prices') || {};
        @foreach ($locations as $location)
            $('#edit_unit_price_{{ $location->id }}').val(prices['{{ $location->id }}'] ?? '');
        @endforeach


        
    });

    // Modal de Eliminar
    $('#deleteModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Botón que activó el modal
        const id = button.data('id'); // Obtener el ID del producto

        // Actualizar la acción del formulario con el ID del producto
        $('#deleteProductForm').attr('action', `{{ url('products') }}/${id}`);
    });

    // Manejar el envío del formulario de crear producto
    $('#createProductForm').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario

        const formData = new FormData(this);

        productSpinner.classList.add('spinner-visible');
        productSpinner.classList.remove('spinner-hidden');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                productSpinner.classList.add('spinner-hidden');
                productSpinner.classList.remove('spinner-visible');
                if (response.status) {
                   $('#createModal').modal('hide');
                   ToastMessage.fire({
                        icon: 'success',
                        text: response.message || 'Operación exitosa'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    ToastError.fire({
                        text: response.error || 'Ocurrió un error'
                    });
                }
            },
            error: function(xhr) {
                productSpinner.classList.add('spinner-hidden');
                productSpinner.classList.remove('spinner-visible');
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert(xhr.responseJSON.error);
                } else {
                    ToastError.fire({
                        text: 'Ocurrió un error'
                    });
                }
            }
        });
    });
</script>

<!-- Modal Rápido Crear Categoría -->
<div class="modal fade" id="quickCategoryModal" tabindex="-1" aria-labelledby="quickCategoryModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg">
            <form id="quickCategoryForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold"><i class="bi bi-tags-fill me-2 text-primary"></i>Nueva Categoría Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="quickCategoryAlert" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label for="quick_category_name" class="form-label text-dark fw-bold">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Ej. Lubricantes, Filtros..." id="quick_category_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_category_description" class="form-label text-dark fw-bold">Descripción</label>
                        <textarea class="form-control" placeholder="Descripción opcional" id="quick_category_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSaveQuickCategory">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerQuickCategory" role="status" aria-hidden="true"></span>
                        Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    let activeProductModalId = null;

    function openQuickCategoryModal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        if ($('#createModal').hasClass('show')) {
            activeProductModalId = '#createModal';
            $('#createModal').modal('hide');
        } else if ($('#editModal').hasClass('show')) {
            activeProductModalId = '#editModal';
            $('#editModal').modal('hide');
        } else {
            activeProductModalId = null;
        }

        $('#quick_category_name').val('');
        $('#quick_category_description').val('');
        $('#quickCategoryAlert').addClass('d-none');

        setTimeout(function() {
            $('#quickCategoryModal').modal('show');
        }, 200);
    }

    $(document).ready(function() {
        $('#quickCategoryModal').on('hidden.bs.modal', function () {
            if (activeProductModalId) {
                const targetModal = activeProductModalId;
                activeProductModalId = null;
                setTimeout(function() {
                    $(targetModal).modal('show');
                }, 200);
            }
        });

        $('#quickCategoryForm').on('submit', function(e) {
            e.preventDefault();
            const nameInput = $('#quick_category_name').val().trim();
            const descInput = $('#quick_category_description').val().trim();
            const alertBox = $('#quickCategoryAlert');
            const btnSave = $('#btnSaveQuickCategory');
            const spinner = $('#spinnerQuickCategory');

            if (!nameInput) {
                alertBox.removeClass('d-none').text('Por favor ingrese el nombre de la categoría.');
                return;
            }

            alertBox.addClass('d-none');
            btnSave.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: "{{ route('categories.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: nameInput,
                    description: descInput
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    btnSave.prop('disabled', false);
                    spinner.addClass('d-none');

                    if (response.success && response.category) {
                        const catName = response.category.name;
                        
                        // Añadir opción a los select de categoría
                        let existsInCreate = $('#category option[value="' + catName + '"]').length > 0;
                        if (!existsInCreate) {
                            $('#category').append(new Option(catName, catName));
                        }
                        $('#category').val(catName);

                        let existsInEdit = $('#edit_categoria option[value="' + catName + '"]').length > 0;
                        if (!existsInEdit) {
                            $('#edit_categoria').append(new Option(catName, catName));
                        }
                        if ($('#editModal').hasClass('show')) {
                            $('#edit_categoria').val(catName);
                        }

                        // Limpiar formulario y cerrar modal rápido
                        $('#quick_category_name').val('');
                        $('#quick_category_description').val('');
                        $('#quickCategoryModal').modal('hide');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Categoría agregada',
                                text: 'La categoría "' + catName + '" ha sido creada y seleccionada.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        alertBox.removeClass('d-none').text(response.message || 'Error al guardar la categoría.');
                    }
                },
                error: function(xhr) {
                    btnSave.prop('disabled', false);
                    spinner.addClass('d-none');
                    let errMsg = 'Ocurrió un error al registrar la categoría.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                        errMsg = xhr.responseJSON.errors.name[0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    alertBox.removeClass('d-none').text(errMsg);
                }
            });
        });
    });
</script>
@endsection