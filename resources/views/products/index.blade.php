@extends('template.index')

@section('header')
<h1>Productos</h1>
<p>Lista de productos</p>
@endsection

@include('components.spinner')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title w-100">
                        <form id="createProductForm" action="{{ route('products.store') }}" method="POST">
                            @csrf
                            <div class="mb-3 row">
                                <label for="name" class="col-sm-3 col-form-label text-start">Producto</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control border-dark" id="name" name="name" required>
                                </div>

                                <label for="category" class="col-sm-3 col-form-label text-start">Categoría</label>
                                <div class="col-sm-3">
                                    <select name="category" id="category" class="form-control border-dark" required>
                                        <option value="">Seleccione una categoría</option>
                                        <option>Combustible</option>
                                        <option>Inv. interno</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center"> <!-- alineación vertical centrada -->
								<div class="col-md-2 d-flex align-items-center justify-content-center">
									<!-- Label centrado vertical y horizontalmente -->
									<label for="unit_price" class="col-form-label text-center w-100">
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
															id="unit_price_{{ $location->id }}"
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
                                    <th>Producto</th>
                                    <th>Marca</th>
                                    <th>Tipo</th>
                                    <th>Categoría</th>
                                    <th>Und. Medida</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                                    <td>{{ $product->name ?: '-' }}</td>
                                    <td>{{ $product->brand ?: '-' }}</td>
                                    <td>{{ $product->type ?: '-' }}</td>
                                    <td>{{ $product->category ?: '-' }}</td>
                                    <td>{{ $product->measurement_unit ?: '-' }}</td>
                                    <td>
                                        <!-- Botón para editar -->
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-brand="{{ $product->brand }}"
                                        data-type="{{ $product->type }}"
                                        data-category="{{ $product->category }}"
                                        data-measurement_unit="{{ $product->measurement_unit }}"
                                        data-prices='@json($product->location_prices ? $product->location_prices->pluck("unit_price", "location_id") : [])'>
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Botón para eliminar -->
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $product->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay productos registrados.</td>
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
                        <label for="edit_categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="edit_categoria" name="category">
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

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                spinner.classList.add('spinner-visible');
                spinner.classList.remove('spinner-hidden');
                if (response.status) {
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
                spinner.classList.add('spinner-visible');
                spinner.classList.remove('spinner-hidden');
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


@endsection