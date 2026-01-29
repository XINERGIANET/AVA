@extends('template.index')

@section('header')
    <h1>Compras</h1>
    <p>Registro de compras</p>
@endsection


@section('content')
    @include('components.spinner')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="header-title w-100">
                            <form id="purchaseForm">
                                @csrf
                                <p><strong>Movimientos</strong></p>
                                <div class="mb-2 row">
                                    <label class="col-sm-3 col-form-label text-start">Proveedor:</label>
                                    <div class="col-sm-5">
                                        <div class="d-flex gap-3">
                                            <input type="text" id="search-supplier" class="form-control"
                                                placeholder="Buscar proveedor...">
                                            <input type="hidden" id="supplier_id" name="supplier_id">
                                            <button type="button" class="btn btn-lg btn-success" id="openProviderModal"
                                                data-bs-toggle="modal" data-bs-target="#providerModal">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="row">
                                    <label class="col-sm-3 col-form-label text-start">Buscar Producto:</label>
                                    <div class="col-sm-4 position-relative">
                                        <input type="text" style="display: block;" class="form-control border-dark" id="search-product" name="search-product" placeholder="Buscar producto...">
                                    </div>
                                    <div class="col-sm-1 d-flex align-items-center ps-0">
                                        <i class="bi bi-info-circle"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-bs-title="Esto agregará un producto a la tabla de detalles."></i>
                                    </div>
                           
                                </div> -->


                                <hr style="border: none; border-top: 2px solid #888; margin: 20px 0;">

                                <p><strong>Detalle Compra</strong></p>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label text-start">Tipo de Comprobante</label>
                                    <div class="col-sm-3">
                                        <select class="form-select" id="voucherType" name="voucher_type" required>
                                            <option value="">Seleccione</option>
                                            <option value="1">Factura</option>
                                            <option value="2">Boleta</option>
                                            <option value="3">Nota de Venta</option>
                                            <option value="4">Otro</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-3 col-form-label text-start">N° Comprobante (*)</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control border-dark" id="invoiceNumber"
                                            name="invoice_number">
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label text-start">Método de Pago</label>
                                    <div class="col-sm-3">
                                        <select class="form-control border-dark" id="paymentMethod" name="payment_method_id"
                                            required>
                                            <option value="">Seleccione un método</option>
                                            @foreach ($paymentMethods as $method)
                                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="col-sm-3 col-form-label text-start">Fecha de Compra</label>
                                    <div class="col-sm-3">
                                        <input type="date" class="form-control border-dark" id="purchaseDate"
                                            name="date" required>
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label text-start">T° de compra</label>
                                    <div class="col-sm-3">
                                        <input type="number" step="0.01" class="form-control border-dark"
                                            id="purchase_temp" name="purchase_temp">
                                    </div>
                                    <label class="col-sm-3 col-form-label text-start">T° de llegada</label>
                                    <div class="col-sm-3">
                                        <input type="number" step="0.01" class="form-control border-dark" id="real_temp"
                                            name="real_temp">
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label text-start">Sede</label>
                                    <div class="col-sm-3">
                                        <select class="form-control border-dark" id="location_id" name="location_id" {{ auth()->user()->role->nombre != 'master' ? 'disabled' : '' }}>
                                            @if (auth()->user()->role->nombre == 'master')
                                                <option value="" disabled selected>Seleccione una sede</option>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            @else
                                                <option value="{{ auth()->user()->location_id }}" selected>
                                                    {{ auth()->user()->location->name }}</option>
                                            @endif
                                        </select>
                                    </div>

                                    <label class="col-sm-3 col-form-label text-start">Tanque(s)</label>
                                    <div class="col-sm-3">
                                        <!-- ahora permite seleccionar varios tanques (ctrl/shift) -->
                                        <select class="form-control border-dark" id="tank_id" name="tank_id[]" multiple
                                            size="5" required>
                                            @foreach ($tanks as $tank)
                                                <option data-location_id="{{ $tank->location_id }}"
                                                    data-product_id="{{ $tank->product_id }}" value="{{ $tank->id }}">
                                                    {{ $tank->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text small">Puede seleccionar varios tanques del mismo producto.
                                            Tecla Ctrl + click para seleccionar varios o deseleccionar</div>
                                    </div>
                                </div>


                                <hr style="border: none; border-top: 2px solid #888; margin: 20px 0;">

                                <div class="col-12 mb-3">
                                    <p><strong>Filtro Búsqueda</strong></p>

                                    <div class="row align-items-end">
                                        <!-- Búsqueda al inicio -->
                                        <div class="col-md-4">
                                            <label class="form-label">Producto</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="busquedaProducto"
                                                    placeholder="Buscar producto...">
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-center ps-0" style="height: 38px;">
                                            <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="right"
                                                data-bs-title="Esto filtrará los productos de la tabla para su búsqueda."></i>
                                        </div>

                                        <!-- Espacio en el medio -->
                                        <div class="col-md-3">
                                        </div>

                                        <!-- Total y botón al final -->
                                        <div class="col-md-4 text-end">
                                            <div class="mb-2">
                                                <strong>Total: S/ <span id="totalAmount">0.00</span></strong>
                                            </div>
                                            <button type="submit" class="btn btn-primary" id="savePurchase">
                                                Guardar Compra
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tabla de productos agregados -->



                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="purchaseTable">
                                        <thead class="table">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Unidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Merma</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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
                                    <input type="number" class="form-control" id="document" name="document" required>
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
                spinner.classList.add('spinner-hidden');
                spinner.classList.remove('spinner-visible');

                ToastError.fire({
                    icon: 'warning',
                    text: 'Debe agregar al menos un producto'
                });

                return;
            }

            // Mostrar spinner
            spinner.classList.remove('spinner-hidden');
            spinner.classList.add('spinner-visible');

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
                    spinner.classList.add('spinner-hidden');
                    spinner.classList.remove('spinner-visible');

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
                    spinner.classList.add('spinner-hidden');
                    spinner.classList.remove('spinner-visible');

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
            const tankSelect = $('#tank_id');
            const allTankOptions = tankSelect.find('option').clone(); // cache opciones originales

            // Filtrar tanques cuando cambie la sede (mantener)
            $('#location_id').on('change', function() {
                const locId = $(this).val();
                tankSelect.empty().append('<option value="">Seleccione uno o más tanques</option>');
                if (!locId) {
                    allTankOptions.each(function() {
                        const $opt = $(this);
                        if ($opt.val() !== '') tankSelect.append($opt.clone());
                    });
                    return;
                }
                allTankOptions.each(function() {
                    const $opt = $(this);
                    const optLoc = String($opt.data('location_id') || '');
                    if (optLoc === String(locId)) {
                        tankSelect.append($opt.clone());
                    }
                });
                tankSelect.val([]);
                $('#purchaseTable tbody').empty();
                updateTotal();
            });

            // Nuevo: al seleccionar tanque(s) crear una fila por tanque con "Tanque: [input]" en cantidad
            tankSelect.on('change', function() {
                const $select = $(this);
                // obtener opciones actualmente seleccionadas (no vacías)
                let selectedOptions = $select.find('option:selected').filter(function() {
                    return $(this).val() !== '';
                });

                // limpiar tabla primero
                $('#purchaseTable tbody').empty();

                if (selectedOptions.length === 0) {
                    updateTotal();
                    return;
                }

                // validar mismo product_id entre seleccionados
                let firstProductId = selectedOptions.first().data('product_id');
                const mismatch = selectedOptions.filter(function() {
                    return String($(this).data('product_id')) !== String(firstProductId);
                });

                if (mismatch.length > 0) {
                    ToastError.fire({
                        icon: 'warning',
                        text: 'Todos los tanques seleccionados deben contener el mismo producto. Se deseleccionaron los que no coinciden.'
                    });

                    // deseleccionar sólo los que no coinciden
                    mismatch.prop('selected', false);

                    // volver a calcular las opciones seleccionadas después de la deselección
                    selectedOptions = $select.find('option:selected').filter(function() {
                        return $(this).val() !== '';
                    });

                    // si ya no quedan seleccionados, limpiar y salir
                    if (selectedOptions.length === 0) {
                        updateTotal();
                        return;
                    }

                    // actualizar el product_id de referencia (podría cambiar si el primer fue deseleccionado)
                    firstProductId = selectedOptions.first().data('product_id');
                }

                // obtener datos del producto (asegurar comparación numérica)
                const productId = firstProductId;
                const selectedProduct = newproducts.find(p => Number(p.id) === Number(productId));
                if (!selectedProduct) {
                    updateTotal();
                    return;
                }

                // crear una fila por cada tanque actualmente seleccionado
                selectedOptions.each(function() {
                    const $opt = $(this);
                    const tankId = $opt.val();
                    const tankName = $opt.text();
                    const newRow = `
                    <tr data-product-id="${productId}" data-tank-id="${tankId}" data-unit="${selectedProduct.measurement_unit}">
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
            });

            // Si ya hay sede seleccionada al cargar, aplicar filtro
            if ($('#location_id').val()) {
                $('#location_id').trigger('change');
            }
        });
    </script>
@endsection
