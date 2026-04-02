@extends('template.index')

@section('header')
    <h1>Almacén</h1>
    <p>Stock actual por sede y producto</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title">
                            <h4 class="card-title">Stock Disponible</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->role->nombre == 'master')
                            <div class="mb-3">
                                <label for="locationFilter" class="form-label">Filtrar por sede:</label>
                                <select id="locationFilter" class="form-select">
                                    <option value="">Todas las sedes</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sede</th>
                                        <th>Tanque</th>
                                        <th>Producto</th>
                                        <th>Capacidad</th>
                                        <th>Stock Disponible</th>
                                        <th>Unidad</th>
                                        <td>Acciones</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tanks as $tank)
                                        <tr class="storage-row" data-location="{{ $tank->location_id }}">
                                            <td>{{ $tank->location->name }}</td>
                                            <td>{{ $tank->name }}</td>
                                            <td>{{ $tank->product->name }}</td>
                                            <td>{{ number_format($tank->capacity, 2) }}</td>
                                            <td>{{ number_format($tank->stored_quantity, 3) }}</td>
                                            <td>{{ $tank->product->measurement_unit }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-warning btn-edit"
                                                    data-id="{{ $tank->id }}"
                                                    data-name="{{ $tank->name }}"
                                                    data-capacity="{{ $tank->capacity ?? '' }}"
                                                    data-stock="{{ $tank->stored_quantity ?? 0 }}"
                                                    data-location="{{ $tank->location_id }}"
                                                    data-product="{{ $tank->product_id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tanque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nombre</label>
                            <input type="text" class="form-control bg-light" id="editName" disabled required>
                        </div>
                        <div class="mb-3 d-none">
                            <label for="editCapacity" class="form-label">Capacidad</label>
                            <input type="number" class="form-control" id="editCapacity" step="0.001" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStock" class="form-label">Stock Disponible</label>
                            <input type="number" class="form-control" id="editStock" step="0.001" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLocation" class="form-label">Sede</label>
                            <select class="form-select bg-light" id="editLocation" disabled required>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editProduct" class="form-label">Producto</label>
                            <select class="form-select bg-light" id="editProduct" disabled required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="editId">
                        <input type="hidden" id="originalStock">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locationFilter = document.getElementById('locationFilter');
            const rows = document.querySelectorAll('.storage-row');

            if (locationFilter) {
                locationFilter.addEventListener('change', function () {
                    const locationId = this.value;

                    rows.forEach(row => {
                        if (locationId === '' || row.dataset.location === locationId) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Lógica para el botón de editar con confirmación mediante delegación de eventos
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-edit');
                if (!btn) return;
                
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                const capacity = btn.dataset.capacity;
                const stock = btn.dataset.stock;
                const location = btn.dataset.location;
                const product = btn.dataset.product;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas editar este tanque?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, editar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            openEditModal(id, name, capacity, stock, location, product);
                        }
                    });
                } else {
                    if (confirm('¿Estás seguro de que deseas editar este tanque?')) {
                        openEditModal(id, name, capacity, stock, location, product);
                    }
                }
            });

            function openEditModal(id, name, capacity, stock, location, product) {
                document.getElementById('editId').value = id;
                document.getElementById('editName').value = name;
                
                const capacityInput = document.getElementById('editCapacity');
                if (capacityInput) capacityInput.value = capacity;
                
                const stockInput = document.getElementById('editStock');
                if (stockInput) stockInput.value = stock;
                
                const originalStockInput = document.getElementById('originalStock');
                if (originalStockInput) originalStockInput.value = stock;
                
                const locationInput = document.getElementById('editLocation');
                if (locationInput) locationInput.value = location;
                
                const productInput = document.getElementById('editProduct');
                if (productInput) productInput.value = product;

                // Intentar abrir con jQuery
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                    jQuery('#editModal').modal('show');
                } 
                // Fallback estandar de Bootstrap nativo
                else if (typeof bootstrap !== 'undefined') {
                    var editModalElement = document.getElementById('editModal');
                    var editModal = bootstrap.Modal.getInstance(editModalElement);
                    if (!editModal) {
                        editModal = new bootstrap.Modal(editModalElement);
                    }
                    editModal.show();
                } else {
                    alert('No se pudo cargar la ventana porque Bootstrap no está disponible.');
                }
            }

            const saveChangesBtn = document.getElementById('saveChanges');
            if (saveChangesBtn) {
                saveChangesBtn.addEventListener('click', function() {
                    const id = document.getElementById('editId').value;
                    const name = document.getElementById('editName').value;
                    const capacity = document.getElementById('editCapacity').value;
                    const stock = document.getElementById('editStock').value;
                    const location = document.getElementById('editLocation').value;
                    const product = document.getElementById('editProduct').value;

                    if (!name || !capacity || stock === '' || !location || !product) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Por favor complete todos los campos requeridos', 'error');
                        } else {
                            alert('Por favor complete todos los campos requeridos');
                        }
                        return;
                    }

                    if (parseFloat(stock) > parseFloat(capacity)) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'El stock no puede ser mayor a la capacidad del tanque.', 'error');
                        } else {
                            alert('El stock no puede ser mayor a la capacidad del tanque.');
                        }
                        return;
                    }

                    const original = parseFloat(document.getElementById('originalStock').value || 0);
                    const newStock = parseFloat(stock);
                    const diff = newStock - original;

                    if (diff !== 0) {
                        const sign = diff > 0 ? '+' : '';
                        const msg = `Estás a punto de modificar el inventario de este tanque ingresando un ajuste artificial de ${sign}${diff} unidades. ¿Confirmas hacer esta modificación en el Stock?`;
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Advertencia de Ajuste Manual',
                                text: msg,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Sí, forzar ajuste',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    continuarGuardado(id, name, capacity, stock, location, product);
                                }
                            });
                        } else {
                            if (confirm(msg)) {
                                continuarGuardado(id, name, capacity, stock, location, product);
                            }
                        }
                    } else {
                        continuarGuardado(id, name, capacity, stock, location, product);
                    }
                });
            }

            function continuarGuardado(id, name, capacity, stock, location, product) {
                const saveChangesBtn = document.getElementById('saveChanges');
                
                // Disable button to prevent double submit
                saveChangesBtn.disabled = true;
                saveChangesBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

                fetch(`{{ url('storages') }}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            capacity: capacity,
                            stored_quantity: stock,
                            location_id: location,
                            product_id: product
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        saveChangesBtn.disabled = false;
                        saveChangesBtn.innerHTML = 'Guardar Cambios';

                        if (data.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                alert(data.message);
                                window.location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', data.message || 'Error al actualizar', 'error');
                            } else {
                                alert(data.message || 'Error al actualizar');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        saveChangesBtn.disabled = false;
                        saveChangesBtn.innerHTML = 'Guardar Cambios';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Hubo un problema al comunicar con el servidor', 'error');
                        } else {
                            alert('Hubo un problema al comunicar con el servidor');
                        }
                    });
            }
        });
    </script>
@endsection