@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-cart-plus me-2 text-primary"></i>Registrar Compra</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Registrar Compra</li>
        </ol>
    </nav>
@endsection


@section('content')
    @include('components.spinner')
    <div class="container-fluid content-inner mt-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="header-title w-100">
                            <form id="purchaseForm">
                                @csrf
                                
                                <!-- SECCIÓN: DATOS GENERALES -->
                                <h5 class="mb-3 text-primary" style="font-weight: 600; font-size: 1.1rem; color: #465fff !important;">
                                    <i class="bi bi-truck me-2"></i>Datos Generales
                                </h5>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-building me-1 text-secondary"></i>Proveedor:
                                        </label>
                                        <div class="input-group">
                                            <input type="text" id="search-supplier" class="form-control" placeholder="Buscar proveedor por RUC o Razón Social...">
                                            <input type="hidden" id="supplier_id" name="supplier_id">
                                            <button type="button" class="btn btn-success text-white" id="openProviderModal" data-bs-toggle="modal" data-bs-target="#providerModal" data-bs-toggle="tooltip" title="Agregar nuevo proveedor">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-calendar3 me-1 text-secondary"></i>Fecha de Compra
                                        </label>
                                        <input type="date" class="form-control" id="purchaseDate" name="date" required>
                                    </div>
                                </div>

                                <!-- SECCIÓN: DETALLE DE COMPRA -->
                                <h5 class="mb-3 mt-4 text-primary border-top pt-4" style="font-weight: 600; font-size: 1.1rem; color: #465fff !important;">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Detalle del Comprobante y Almacenamiento
                                </h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">Tipo de Comprobante</label>
                                                <select class="form-select" id="voucherType" name="voucher_type" required>
                                                    <option value="">Seleccione</option>
                                                    <option value="1">Factura</option>
                                                    <option value="2">Boleta</option>
                                                    <option value="3">Nota de Venta</option>
                                                    <option value="4">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">N° Comprobante (*)</label>
                                                <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" placeholder="Ej. F001-000123">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">Condición</label>
                                                <div class="form-control bg-light text-muted d-flex align-items-center text-nowrap overflow-hidden" style="min-height: 42px;" title="Se registra como Cuenta por Pagar">
                                                    <i class="bi bi-journal-check me-2 flex-shrink-0"></i>Cuenta por Pagar
                                                </div>
                                                <input type="hidden" id="paymentMethod" name="payment_method_id" value="">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-0">
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">T° de Compra</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="purchase_temp" name="purchase_temp" placeholder="0.00">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">T° de Llegada</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="real_temp" name="real_temp" placeholder="0.00">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                                    <i class="bi bi-geo-alt me-1 text-secondary"></i>Sede
                                                </label>
                                                <select class="form-select" id="location_id" name="location_id" {{ auth()->user()->role->nombre != 'master' ? 'disabled' : '' }}>
                                                    @if (auth()->user()->role->nombre == 'master')
                                                        <option value="" disabled {{ auth()->user()->location_id ? '' : 'selected' }}>Seleccione una sede</option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->id }}" {{ auth()->user()->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{ auth()->user()->location_id }}" selected>
                                                            {{ auth()->user()->location->name }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="min-width: 0;">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-database me-1 text-secondary"></i>Tanque(s)
                                        </label>
                                        <input type="text" class="form-control mb-2" id="tankSearch" placeholder="Buscar tanque...">
                                        <div id="tankList" class="border rounded-3 p-2 bg-white" style="max-height: 100px; overflow-y: auto;">
                                            @foreach ($tanks as $tank)
                                                <label class="d-flex align-items-start gap-2 mb-2 tank-item" data-location_id="{{ $tank->location_id }}" data-product_id="{{ $tank->product_id }}" data-name="{{ strtolower($tank->name) }}" style="cursor: pointer;">
                                                    <input class="form-check-input mt-1 tank-checkbox" type="checkbox" name="tank_id[]" value="{{ $tank->id }}">
                                                    <span class="small text-dark tank-name">{{ $tank->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="form-text small text-muted">Marca uno o varios tanques. El listado se filtra por sede.</div>
                                    </div>
                                </div>

                                <!-- SECCIÓN: PRODUCTOS -->
                                <div class="p-3 mt-4 mb-4" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-4">
                                            <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">Filtro de Productos en Tabla</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                                <input type="text" class="form-control" id="busquedaProducto" placeholder="Buscar producto en la lista...">
                                            </div>
                                        </div>
                                        <div class="col-md-8 text-end">
                                            <div class="d-inline-block me-4">
                                                <span class="text-muted" style="font-size: 1.1rem;">Total:</span>
                                                <strong class="text-primary ms-1" style="font-size: 1.5rem; color: #465fff !important;">S/ <span id="totalAmount">0.00</span></strong>
                                            </div>
                                            <button type="submit" class="btn btn-primary px-4 py-2" id="savePurchase" style="background-color: #465fff; border-color: #465fff;">
                                                <i class="bi bi-save me-2"></i>Guardar Compra
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de productos -->
                                <div class="table-responsive rounded shadow-sm border mb-3">
                                    <table class="table table-hover mb-0" id="purchaseTable">
                                        <thead class="text-white">
                                            <tr>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Producto</th>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Unidad</th>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Precio Unitario</th>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Cantidad</th>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Subtotal</th>
                                                <th class="border-0" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Merma</th>
                                                <th class="border-0 text-center" style="background-color: #465fff !important; color: white !important; font-weight: 600; padding: 12px 15px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="align-middle"></tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="providerModalLabel">Agregar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="providerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="document" class="form-label">RUC/DNI</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="document" name="document" placeholder="Ingrese RUC o DNI" required>
                                        <button class="btn btn-outline-primary" type="button" onclick="searchDocumentApi()">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Razón Social</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="saveSupplier">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .cantidad-input {
            width: 100px;
        }

        /* Limita la altura del menú y añade scroll vertical */
        .ui-autocomplete {
            max-height: 200px;
            /* ajusta la altura a tu gusto */
            overflow-y: auto;
            /* habilita scroll vertical */
            overflow-x: hidden;
            /* evita scroll horizontal */
            /* opcional: para que no tape otros elementos */
            z-index: 1000;
        }

        /* Opcional: mejorar visibilidad de cada ítem */
        .ui-menu-item-wrapper {
            white-space: nowrap;
            padding: 4px 8px;
        }

        /* Resalta toda la fila del tanque marcado, no solo el checkbox */
        .tank-item.is-checked,
        .tank-item:has(.tank-checkbox:checked) {
            background-color: rgba(58, 87, 232, 0.08);
            border-radius: 0.375rem;
        }
        .tank-item.is-checked .tank-name,
        .tank-item:has(.tank-checkbox:checked) .tank-name {
            font-weight: 600;
            color: #3a57e8;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <script>
        function collectTableData() {
            const products = [];

            $('#purchaseTable tbody tr').each(function() {
                const row = $(this);
                const productId = row.data('product-id');
                const tankId = row.data('tank-id') || null;
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.unit_price').val()) || 0;
                const subtotal = parseFloat(row.find('.subtotal').val()) || 0;

                if (quantity > 0) {
                    const productData = {
                        product_id: productId,
                        tank_id: tankId,
                        quantity: quantity,
                        price: price,
                        subtotal: subtotal
                    };

                    // Si es un producto nuevo (ID negativo), agregar datos adicionales
                    if (productId < 0) {
                        productData.category_id = row.data('category-id');
                        productData.nombre = row.data('nombre');
                        productData.unidad_medida = row.data('unidad-medida');
                    }

                    products.push(productData);
                }
            });

            return products;
        }



        var suppliers = @json($suppliers);
        var newproducts = @json($products);
        var selectedProducts = [];
        var purchaseSpinner = document.getElementById('spinner');


        function handleProductClickSelect(productId) {
            // Buscar el producto en la lista
            const selectedProduct = newproducts.find(p => p.id === productId);

            if (!selectedProduct) return;

            // Verificar si ya existe en la tabla
            const existingRow = $(`#purchaseTable tr[data-product-id="${productId}"]`);

            if (existingRow.length > 0) {
                // Si existe, incrementar cantidad
                const quantityInput = existingRow.find('.quantity');
                const currentQty = parseInt(quantityInput.val()) || 0;
                quantityInput.val(currentQty + 1);
            } else {
                // Si no existe, agregar nueva fila
                const newRow = `
                <tr data-product-id="${productId}" data-unit="${selectedProduct.measurement_unit}">
                    <td>${selectedProduct.name}</td>
                    <td>${selectedProduct.measurement_unit}</td>
                    <td><input type="number" class="form-control text-end unit_price" step="0.01" min="0" disabled></td>
                    <td><input type="number" class="form-control text-end quantity" min="0.001" step="0.001"></td>
                    <td><input type="number" class="form-control text-end subtotal" min="0.001" step="0.001"></td>
                    <td><input type="number" class="form-control text-end waste" step="0.001" value="0"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#purchaseTable tbody').append(newRow);
                attachEventsToRows();
            }

            // Limpiar campo de búsqueda
            $('#search-product').val('');
        }

        $('#search-supplier').autocomplete({
                source: function(request, response) {
                    var matches = $.grep(suppliers, function(item) {
                        return item.company_name.toLowerCase()
                            .includes(request.term.toLowerCase());
                    });
                    matches = matches.slice(0, 10);
                    var results = $.map(matches, function(item) {
                        return {
                            label: item.company_name,
                            value: item.company_name,
                            id: item.id
                        };
                    });
                    response(results);
                },
                select: function(event, ui) {
                    $('#supplier_id').val(ui.item.id); // Guardar el ID en campo oculto
                    //cargarProductosProveedor(ui.item.id); no hay productos por proveedor
                },
                appendTo: '.container-fluid'
            })
            .autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
                    .appendTo(ul);
            };

        function attachEventsToRows() {
            // Cuando cambien cantidad o precio unitario, recalcular subtotal = precio * cantidad
            $('#purchaseTable').on('input', '.quantity, .unit_price', function() {
                const row = $(this).closest('tr');
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const unitPrice = parseFloat(row.find('.unit_price').val()) || 0;
                const subtotalField = row.find('.subtotal');

                if (quantity > 0 && unitPrice > 0) {
                    const subtotal = (unitPrice * quantity).toFixed(2);
                    subtotalField.val(subtotal);
                } else {
                    // Si no hay datos suficientes, limpiar subtotal
                    subtotalField.val('');
                }

                // Actualizar total general
                updateTotal();
            });
        }


        $('#purchaseForm').on('submit', function(e) {
            e.preventDefault();

            let productsCart = [];
            let suppliesCart = [];

            $('#purchaseTable tbody tr').each(function() {
                let row = $(this);
                let productId = row.data('product-id');
                let tankId = row.data('tank-id') || null;
                let quantity = parseFloat(row.find('.quantity').val());
                let subtotal = parseFloat(row.find('.subtotal').val());
                let unit_price = parseFloat(row.find('.unit_price').val());
                let waste = parseFloat(row.find('.waste').val());
                let measurement_unit = row.data('unit');

                if (productId && quantity >= 0.01 && subtotal >= 0 && unit_price >= 0) {
                    const item = {
                        product_id: productId,
                        tank_id: tankId,
                        quantity: quantity,
                        unit_price: unit_price,
                        subtotal: subtotal,
                        waste: waste,
                        measurement_unit: measurement_unit
                    };

                    productsCart.push(item);

                }
            });

            if (productsCart.length === 0) {
                purchaseSpinner.classList.add('spinner-hidden');
                purchaseSpinner.classList.remove('spinner-visible');

                ToastError.fire({
                    icon: 'warning',
                    text: 'Debe agregar al menos un producto'
                });

                return;
            }

            // Mostrar spinner
            purchaseSpinner.classList.remove('spinner-hidden');
            purchaseSpinner.classList.add('spinner-visible');

            // Preparar los datos para enviar
            let data = {
                _token: $('input[name="_token"]').val(),
                supplier_id: $('#supplier_id').val(),
                voucher_type: $('#voucherType').val(),
                invoice_number: $('#invoiceNumber').val(),
                payment_method_id: $('#paymentMethod').val(),
                date: $('#purchaseDate').val(),
                purchase_temp: $('#purchase_temp').val(),
                real_temp: $('#real_temp').val(),
                tank_id: $('#tank_id').val(),
                products: JSON.stringify(productsCart)
            };

            // Debug: mostrar los datos que se van a enviar
            console.log("Datos a enviar:", data);
            console.log("Carrito:", productsCart);

            // Enviar los datos mediante AJAX
            $.ajax({
                url: '{{ route('purchases.store') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    purchaseSpinner.classList.add('spinner-hidden');
                    purchaseSpinner.classList.remove('spinner-visible');

                    if (response.status) {
                        ToastMessage.fire({
                            icon: 'success',
                            text: response.message || 'Operación exitosa'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Error del backend
                        ToastError.fire({
                            text: response.error || 'Ocurrió un error'
                        });
                    }
                },

                error: function(xhr, status, error) {
                    purchaseSpinner.classList.add('spinner-hidden');
                    purchaseSpinner.classList.remove('spinner-visible');

                    console.log("Error en la petición:");
                    console.log("Products enviados:", productsCart);
                    console.log("Supplies enviados:", suppliesCart);
                    console.log("XHR Response:", xhr);
                    console.log("XHR Status:", status);
                    console.log("XHR Error:", error);

                    let mensaje = 'Ocurrió un error al procesar la compra';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                    } else if (xhr.responseText) {
                        mensaje = xhr.responseText;
                    }

                    ToastError.fire({
                        text: mensaje
                    });
                }

            });
        });

        function updateTotal() {
            let total = 0;

            $('#purchaseTable tbody tr').each(function() {
                let subtotal = parseFloat($(this).find('.subtotal').val()) || 0;
                total += subtotal;
            });

            $('#totalAmount').text(total.toFixed(2));
        }

        document.getElementById('saveSupplier').addEventListener('click', function() {
            var docum = document.getElementById('document').value.trim();
            var companyName = document.getElementById('company_name').value.trim();

            if (docum === "" || companyName === "") {
                alert("Los campos son obligatorios");
                return;
            }

            var data = {
                document: docum,
                company_name: companyName
            };

            var saveBtn = this;
            var originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            saveBtn.disabled = true;

            fetch('{{ route('suppliers.saveSupplier') }}', {
                    method: 'POST', // o el método que necesites
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta:', data);

                    if (data.success) {
                        // Mostrar mensaje de éxito
                        ToastMessage.fire({
                            icon: 'success',
                            text: data.message ||
                                'Operación exitosa' // Corregido: usar data.message en lugar de response.message
                        }).then(() => {
                            console.log(data.supplier);
                            suppliers.push(data.supplier);
                        });

                        // Cerrar modal
                        const modal = document.getElementById('providerModal');
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            //limpiar y esconder
                            document.getElementById('document').value = "";
                            document.getElementById('company_name').value = "";
                            bsModal.hide();
                        }

                    } else {
                        ToastError.fire({
                            text: data.message || 'Error al agregar el proveedor'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    alert('Error: ' + error.message);
                })
                .finally(() => {
                    // Restaurar estado del botón
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                });
        });

        $(document).ready(function() {
            $('#busquedaProducto').on('keyup', function() {
                var valor = $(this).val().toLowerCase();
                $('#purchaseTable tbody tr').each(function() {
                    var nombre = $(this).find('td:eq(0)').text().toLowerCase();
                    if (nombre.includes(valor) || valor === '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        // Evento para eliminar fila
        $('#purchaseTable').on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            updateTotal(); // actualizar total
        });

        $(function() {
            const tankList = $('#tankList');
            const searchInput = $('#tankSearch');

            function getVisibleSelectedTanks() {
                return tankList.find('.tank-checkbox:checked').closest('.tank-item').filter(function() {
                    return $(this).is(':visible');
                });
            }

            function rebuildTankRows() {
                const selectedItems = getVisibleSelectedTanks();
                $('#purchaseTable tbody').empty();

                if (selectedItems.length === 0) {
                    updateTotal();
                    return;
                }

                let firstProductId = selectedItems.first().data('product_id');
                const mismatch = selectedItems.filter(function() {
                    return String($(this).data('product_id')) !== String(firstProductId);
                });

                if (mismatch.length > 0) {
                    mismatch.find('.tank-checkbox').prop('checked', false);
                    ToastError.fire({
                        icon: 'warning',
                        text: 'Todos los tanques seleccionados deben contener el mismo producto. Se desmarcaron los que no coinciden.'
                    });
                }

                const validItems = getVisibleSelectedTanks();
                if (validItems.length === 0) {
                    updateTotal();
                    return;
                }

                firstProductId = validItems.first().data('product_id');
                const selectedProduct = newproducts.find(p => Number(p.id) === Number(firstProductId));
                if (!selectedProduct) {
                    validItems.find('.tank-checkbox').prop('checked', false);
                    ToastError.fire({
                        icon: 'error',
                        text: 'El producto asociado a este tanque no está disponible o ha sido eliminado. Se desmarcó automáticamente.'
                    });
                    updateTotal();
                    return;
                }

                validItems.each(function() {
                    const $item = $(this);
                    const tankId = $item.find('.tank-checkbox').val();
                    const tankName = $item.find('.tank-name').text();
                    const newRow = `
                        <tr data-product-id="${selectedProduct.id}" data-tank-id="${tankId}" data-unit="${selectedProduct.measurement_unit}">
                            <td>${selectedProduct.name}</td>
                            <td>${selectedProduct.measurement_unit}</td>
                            <td><input type="number" class="form-control text-end unit_price" step="0.01" min="0"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">${tankName}:</span>
                                    <input type="number" class="form-control text-end quantity cantidad-input" min="0.001" step="0.001">
                                </div>
                            </td>
                            <td><input type="number" class="form-control text-end subtotal" min="0.001" step="0.001" disabled></td>
                            <td><input type="number" class="form-control text-end waste" step="0.001" value="0"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#purchaseTable tbody').append(newRow);
                });

                attachEventsToRows();
                updateTotal();
            }

            function applyTankFilters() {
                const locationId = String($('#location_id').val() || '');
                const term = searchInput.val().toLowerCase().trim();

                tankList.find('.tank-item').each(function() {
                    const $item = $(this);
                    const itemLoc = String($item.data('location_id') || '');
                    const itemName = String($item.data('name') || '');
                    const matchesLocation = !locationId || itemLoc === locationId;
                    const matchesSearch = !term || itemName.includes(term);
                    const visible = matchesLocation && matchesSearch;

                    $item.toggle(visible);
                    if (!visible) {
                        $item.find('.tank-checkbox').prop('checked', false).trigger('markchange');
                    }
                });

                rebuildTankRows();
            }

            function syncTankMark() {
                const $checkbox = $(this);
                $checkbox.closest('.tank-item').toggleClass('is-checked', $checkbox.is(':checked'));
            }

            $('#location_id').on('change', applyTankFilters);
            searchInput.on('input', applyTankFilters);
            tankList.on('change markchange', '.tank-checkbox', syncTankMark);
            tankList.on('change', '.tank-checkbox', rebuildTankRows);
            tankList.find('.tank-checkbox').each(syncTankMark);

            if ($('#location_id').val()) {
                applyTankFilters();
            }
        });
        // Búsqueda de documento (DNI/RUC) mediante API
        function searchDocumentApi() {
            const doc = $('#document').val().trim();

            $('#company_name').val('');

            if (!/^\d{8}$|^\d{11}$/.test(doc)) {
                if (typeof ToastError !== 'undefined') {
                    ToastError.fire({ text: 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.' });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.', 'error');
                } else {
                    alert('El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.');
                }
                return;
            }

            if (typeof Swal !== 'undefined' && false) { // Removed Swal in favor of global spinner
                Swal.fire({
                    title: 'Buscando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
            }

            const globalSpinner = document.getElementById('spinner');
            if (globalSpinner) {
                globalSpinner.classList.remove('spinner-hidden');
                globalSpinner.classList.add('spinner-visible');
            }

            $.ajax({
                url: "{{ url('sunat/consultar') }}",
                method: 'GET',
                data: { doc: doc },
                success: function(response) {
                    if (globalSpinner) {
                        globalSpinner.classList.add('spinner-hidden');
                        globalSpinner.classList.remove('spinner-visible');
                    }

                    if (response.success) {
                        const data = response.data;
                        $('#document').val(data.document || doc);
                        $('#company_name').val(data.name || '');
                    } else {
                        if (typeof ToastError !== 'undefined') {
                            ToastError.fire({ text: response.message || 'No se encontró información para ese documento.' });
                        } else {
                            alert(response.message || 'No se encontró información para ese documento.');
                        }
                    }
                },
                error: function(xhr) {
                    if (globalSpinner) {
                        globalSpinner.classList.add('spinner-hidden');
                        globalSpinner.classList.remove('spinner-visible');
                    }
                    const msg = xhr.responseJSON?.message || 'Error al consultar el documento.';
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    </script>
@endsection
