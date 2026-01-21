@extends('template.index')

@section('header')
<h1>Gestión de Contratos</h1>
<p>Administración de contratos, órdenes y áreas asociadas.</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <!-- Card que contiene el formulario y la tabla -->
    <div class="card shadow">
        <!-- Cuerpo del Card -->
        <div class="card-body">
            <!-- Formulario de Registro de Contrato -->
            <form id="formContrato" class="mb-5" method="POST" action="{{ route('contracts.store') }}">
                @csrf
                <!-- Fila 1: Cliente, búsqueda y botón -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Cliente</label>
                    </div>
                    <div class="col-md-7">
                        <!-- Input para mostrar el nombre del cliente -->
                        <input type="text" id="search-client" class="form-control" placeholder="Buscar cliente...">
                        <input type="hidden" id="client_id" name="client_id">
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-primary" id="addClient" data-bs-toggle="modal" data-bs-target="#clientModal">
                            <i class="bi bi-person-add"></i>
                        </a>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">N° de Contrato</label>
                    </div>
                    <div class="col-md-7">
                        <input type="text" id="number" name="number" class="form-control" placeholder="Ingrese el N° de Contrato">
                    </div>
                </div>

                <!-- Fila 2: Sede del contrato -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Sede</label>
                    </div>
                    <div class="col-md-7">
                        <select id="sedeSelect" class="form-select" name="location_id">
                            <option value="">Seleccione una Sede</option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Contenedor de productos que se llenará dinámicamente -->
                <div id="productos-container">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-12 text-center text-muted">
                            <i class="bi bi-info-circle"></i> Seleccione una sede para ver los productos disponibles
                        </div>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkOrdenes" name="generate_orders" value="1">
                            <label class="form-check-label" for="checkOrdenes">Generar Órdenes</label>
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Esto generará un número de órdenes automáticas según el número ingresado."></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="number" id="ordenesInput" name="number_of_orders" class="form-control border-dark"
                            placeholder="N° Órdenes" disabled min="1">
                    </div>
                </div>

                <!-- Botón de Guardar Contrato -->
                <div class="row mb-3 justify-content-end">
                    <div class="col-auto d-flex align-items-center">
                        <label for="totalContrato" class="form-label mb-0 me-2">Total:</label>
                        <input type="number" id="totalContrato" class="form-control form-control-sm" name="total"
                            placeholder="Total" readonly step="0.01">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Guardar Contrato</button>
                    </div>
                </div>
            </form>
            <!-- Tabla de Contratos -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped" id="tablaContratos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>N° de Contrato</th>
                            <th>N° Documento</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Total S/.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agreements as $oac)
                        <tr>
                            <td>{{ $oac->id }}</td>
                            <td>{{ $oac->number }}</td>
                            <td>{{ $oac->client->document }}</td>
                            <td>{{ $oac->client->business_name }}</td>
                            <td>
                                <ul>
                                    @php
                                    $productos = $oac->totalProductos();
                                    @endphp
                                    @if(count($productos) > 0)
                                    @foreach($productos as $producto)
                                    <li>{{ $producto['product_name'] }}: {{ $producto['total_quantity'] }}</li>
                                    @endforeach
                                    @else
                                    <li>No hay productos</li>
                                    @endif
                                </ul>
                            </td>
                            <td>{{ $oac->total }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" onclick="verOrdenes({{ $oac->id }})" title="Ver Órdenes">
                                    <i class="bi bi-clipboard-data"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="verDetalles({{ $oac->id }})" title="Ver Detalles">
                                    <i class="bi bi-bar-chart-steps"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="editarOrden({{ $oac->id }})" title="Editar contrato">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="openPaymentsModal({{ $oac->id }})" title="Gestionar Pagos">
                                    <i class="bi bi-currency-dollar"></i>
                                </button>
                                <form action="" method="POST" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Agregar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="providerForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document" class="form-label">Documento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="document" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Nombre / Razón Social <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="saveClient">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Órdenes -->
<div class="modal fade" id="modalOrdenes" tabindex="-1" aria-labelledby="modalOrdenesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOrdenesLabel">Órdenes del Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenedor de productos para órdenes -->
                <div id="contenedorProductosOrden" class="mb-4">
                    <!-- Se llena dinámicamente con JS -->
                </div>
                <input type="hidden" id="contratoId">

                <!-- Botón de Agregar Orden -->
                <div class="row mb-3">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" id="btnAgregarOrden">Agregar Orden</button>
                    </div>
                </div>

                <!-- Tabla de Órdenes -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N°</th>
                                @foreach ($products as $product)
                                <th>{{ $product->name }}</th>
                                @endforeach
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaOrdenes">
                            <!-- Datos dinámicos de órdenes -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Áreas -->
<div class="modal fade" id="modalAreas" tabindex="-1" aria-labelledby="modalAreasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAreasLabel">Áreas de la Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenedor de áreas -->
                <div id="contenedorAreasOrden" class="mb-4">
                    <!-- Campo Área -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label mb-0">Área</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="area">
                        </div>
                    </div>

                    <!-- Productos dinámicos (se llenan con JS) -->
                    <div id="productosAreaContainer">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>
                <input type="hidden" id="ordenId">

                <!-- Botón de Agregar Detalle -->
                <div class="row mb-3">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" id="btnAgregarDetalle">Agregar Detalle con Área</button>
                    </div>
                </div>

                <!-- Tabla de Detalles con Áreas -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Área</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAreas">
                            <!-- Datos dinámicos de detalles con áreas -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Productos a Orden -->
<div class="modal fade" id="modalAgregarProductos" tabindex="-1" aria-labelledby="modalAgregarProductosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarProductosLabel">Agregar Productos a Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenedorProductosAgregar">
                <!-- Aquí se insertan los inputs de productos por JS -->
            </div>
            <input type="hidden" id="agregar_order_id">
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnGuardarProductos">Guardar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-lg" id="modalEditarContrato" tabindex="-1" aria-labelledby="modalEditarContratoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editContractForm" class="modal-content">
            @csrf
            <input type="hidden" id="editContractId" name="contract_id">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarContratoLabel">Editar Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <input type="text" id="search_edit_client" class="form-control">
                    <input type="hidden" id="edit_client_id" name="client_id">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sede</label>
                    <select id="edit_location_id" name="location_id" class="form-select">
                        <option value="">Seleccione una sede</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- <div class="mb-3">
                    <label class="form-label">Total</label>
                    <input type="number" id="edit_total" name="total" class="form-control" step="0.01">
                </div> -->

                <div id="edit_productos_container" class="mb-3">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>


<div id="contractModalContainer"></div>
@endsection

@section('scripts')
<x-payments-modal />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var productosContratoActual = [];

    let clientSearchTimeout = null;
    $('#search-client').autocomplete({
        source: function(request, response) {
            clearTimeout(clientSearchTimeout);
            clientSearchTimeout = setTimeout(function() {
                let currentTerm = $('#search-client').val();
                // Solo buscar si hay al menos una letra
                if (currentTerm && currentTerm.length > 0) {
                    $.ajax({
                        url: '{{ route('clients.search') }}',
                        method: 'get',
                        data: {
                            query: currentTerm
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.business_name ? item.business_name : item.contact_name,
                                    value: item.business_name ? item.business_name : item.contact_name,
                                    id: item.id,
                                };
                            }));
                        }
                    });
                } else {
                    // Si no hay letras, limpia el autocomplete
                    response([]);
                }
            }, 500);
        },
        appendTo: '.container-fluid',
        select: function(event, ui) {
            $('#client_id').val(ui.item.id);
        },
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
            .appendTo(ul);
    };

    $('#search_edit_client').autocomplete({
        source: function(request, response) {
            clearTimeout(clientSearchTimeout);
            clientSearchTimeout = setTimeout(function() {
                let currentTerm = $('#search_edit_client').val();
                // Solo buscar si hay al menos una letra
                if (currentTerm && currentTerm.length > 0) {
                    $.ajax({
                        url: '{{ route('clients.search') }}',
                        method: 'get',
                        data: {
                            query: currentTerm
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.business_name ? item.business_name : item.contact_name,
                                    value: item.business_name ? item.business_name : item.contact_name,
                                    id: item.id,
                                };
                            }));
                        }
                    });
                } else {
                    // Si no hay letras, limpia el autocomplete
                    response([]);
                }
            }, 500);
        },
        appendTo: '#modalEditarContrato',
        select: function(event, ui) {
            $('#edit_client_id').val(ui.item.id);
        },
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
            .appendTo(ul);
    };

    document.getElementById('saveClient').addEventListener('click', function() {
        var docum = document.getElementById('document').value.trim();
        var companyName = document.getElementById('business_name').value.trim();

        if (docum === "" || companyName === "") {
            alert("Los campos son obligatorios");
            return;
        }

        var data = {
            document: docum,
            business_name: companyName
        };

        var saveBtn = this;
        var originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        saveBtn.disabled = true;

        fetch("{{ route('clients.save') }}", {
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
                        text: data.message || 'Operación exitosa' // Corregido: usar data.message en lugar de response.message
                    }).then(() => {
                        console.log(data.client);
                        clients.push(data.client);
                    });

                    // Cerrar modal
                    document.getElementById('document').value = "";
                    document.getElementById('business_name').value = "";
                    $('#clientModal').modal('hide');

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
        // Mejor manejo de backdrops para evitar quitar el fondo cuando queda
        // otro modal abierto y evitar backdrops huérfanos.
        // - Al ocultar un modal, solo eliminar el backdrop si no quedan modales abiertos.
        // - Al mostrar un modal, asegurar que exista la clase `modal-open` en el body
        //   y limpiar backdrops extra si Bootstrap dejó más de uno.
        $(document).on('hidden.bs.modal', '.modal', function () {
            // Esperar un poco para que Bootstrap complete la transición
            setTimeout(function() {
                // Si no hay otros modales visibles, retirar el backdrop y restablecer body
                if ($('.modal.show').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
                } else {
                    // Si quedan modales abiertos, asegurar que body tenga modal-open
                    // y que exista al menos un backdrop.
                    if ($('.modal-backdrop').length === 0) {
                        $('body').addClass('modal-open');
                        $('body').css('overflow', 'hidden');
                    }
                    // Si por alguna razón hay más de un backdrop, dejar solo uno
                    if ($('.modal-backdrop').length > 1) {
                        $('.modal-backdrop').slice(1).remove();
                    }
                }
            }, 150);
        });

        // Al mostrar un modal, asegurar estado correcto del body y backdrops
        $(document).on('shown.bs.modal', '.modal', function () {
            // Forzar la clase modal-open en el body (Bootstrap normalmente lo hace)
            if ($('.modal.show').length > 0) {
                $('body').addClass('modal-open');
            }

            // Quitar backdrops extra si existen más de uno
            if ($('.modal-backdrop').length > 1) {
                $('.modal-backdrop').slice(1).remove();
            }
        });

        // Cuando cambie la selección de sede
        $('#sedeSelect').change(function() {
            const locationId = $(this).val();
            const productosContainer = $('#productos-container');

            if (locationId) {
                // Mostrar loading
                productosContainer.html(`
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-12 text-center">
                            <i class="bi bi-arrow-clockwise spin"></i> Cargando productos...
                        </div>
                    </div>
                `);

                // Hacer petición AJAX
                $.ajax({
                    url: "{{ route('contracts.products', ':id') }}".replace(':id', locationId),
                    method: 'GET',
                    success: function(products) {
                        if (products.length > 0) {
                            // Generar HTML para cada producto
                            let productosHTML = '';
                            products.forEach(function(product, index) {
                                productosHTML += `
                                    <div class="row mb-3 align-items-center producto-row" data-product-id="${product.id}">
                                        <div class="col-md-3">
                                            <label class="form-label">${product.name}</label>
                                            <input type="hidden" name="product_ids[]" value="${product.id}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control precio-input" name="prices[]" 
                                                placeholder="Precio unitario" step="0.01">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control cantidad-input" name="quantities[]" 
                                                placeholder="Cantidad (${product.measurement_unit || 'unidad'})" step="0.01">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control subtotal-input" name="subtotals[]" 
                                                placeholder="Subtotal" step="0.01" readonly>
                                        </div>
                                    </div>
                                `;
                            });

                            productosContainer.html(productosHTML);
                        } else {
                            productosContainer.html(`
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-12 text-center text-warning">
                                        <i class="bi bi-exclamation-triangle"></i> No hay productos disponibles en esta sede
                                    </div>
                                </div>
                            `);
                        }

                        // Resetear el total
                        $('#totalContrato').val('0.00');
                    },
                    error: function(xhr, status, error) {
                        ToastError.fire({text: 'Error al cargar los productos de la sede'});
                        productosContainer.html(`
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-12 text-center text-danger">
                                    <i class="bi bi-exclamation-circle"></i> Error al cargar los productos de la sede
                                </div>
                            </div>
                        `);
                    }
                });
            } else {
                // Si no hay sede seleccionada, mostrar mensaje inicial
                productosContainer.html(`
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-12 text-center text-muted">
                            <i class="bi bi-info-circle"></i> Seleccione una sede para ver los productos disponibles
                        </div>
                    </div>
                `);
                $('#totalContrato').val('');
            }
        });
    });

    $('#checkOrdenes').on('change', function() {
        if ($(this).is(':checked')) {
            $('#ordenesInput').prop('disabled', false);
            $('#ordenesInput').attr('required', true);
        } else {
            $('#ordenesInput').prop('disabled', true);
            $('#ordenesInput').attr('required', false);
            $('#ordenesInput').val('');
        }
    });

    // Calcular subtotal y total al cambiar cantidad o precio unitario
    $(document).on('input', '.precio-input, .cantidad-input', function() {
        let row = $(this).closest('.producto-row');
        let precio = parseFloat(row.find('.precio-input').val()) || 0;
        let cantidad = parseFloat(row.find('.cantidad-input').val()) || 0;
        let subtotal = precio * cantidad;

        row.find('.subtotal-input').val(subtotal.toFixed(2));

        // Calcular el total sumando todos los subtotales
        let total = 0;
        $('.subtotal-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalContrato').val(total.toFixed(2));
    });

    $('#formContrato').on('submit', function(e) {
        e.preventDefault();

        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        // Validar que hay cliente seleccionado
        if (!$('#client_id').val()) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({ text: 'Por favor selecciona un cliente' });
            return false;
        }

        // Validar que hay al menos un producto con precio y cantidad
        let hasProducts = false;
        let errores = [];

        $('.producto-row').each(function() {
            const productName = $(this).find('label').text();
            const cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
            const precio = parseFloat($(this).find('.precio-input').val()) || 0;

            if (cantidad > 0 && precio <= 0) {
                errores.push(`${productName}: Debe ingresar un precio válido`);
            }
            if (precio > 0 && cantidad <= 0) {
                errores.push(`${productName}: Debe ingresar una cantidad válida`);
            }
            if (cantidad > 0 && precio > 0) {
                hasProducts = true;
            }
        });

        if (errores.length > 0) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({ text: errores.join('\n') });
            return false;
        }

        if (!hasProducts) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({ text: 'Por favor ingresa al menos un producto con precio y cantidad válidos' });
            return false;
        }

        // Preparar FormData (incluye inputs arrays y _token)
        const form = this;
        const formData = new FormData(form);

        // Enviar por AJAX
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method') || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');

                // Mostrar feedback y opcional redirección
                if (response.success) {
                    ToastMessage.fire({ text: response.message || 'Contrato guardado correctamente' });
                    location.reload();
                    
                } else {
                    // backend devolvió success:false
                    ToastError.fire({ text: response.message || 'Error al guardar el contrato' });
                    console.error('Response error:', response);
                }
            },
            error: function(xhr) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');

                // Imprimir error en consola y mostrar mensaje legible
                console.error('AJAX error:', xhr);

                let mensaje = 'Error al guardar el contrato.';
                if (xhr.responseJSON) {
                    // Validación 422
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        // aplanar mensajes
                        const msgs = Object.values(errors).flat().join('\n');
                        mensaje = msgs;
                        console.error('Validation errors:', errors);
                    } else if (xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                        console.error('Message:', xhr.responseJSON);
                    } else if (xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                        console.error('Error payload:', xhr.responseJSON);
                    } else {
                        mensaje = JSON.stringify(xhr.responseJSON);
                    }
                } else {
                    mensaje += ` (${xhr.status} ${xhr.statusText})`;
                }

                ToastError.fire({ text: mensaje });
            }
        });

        return false;
    });

    function verOrdenes(contratoId) {
        $('#contratoId').val(contratoId);
        $.ajax({
            url: "{{ route('contracts.orders', ':id') }}".replace(':id', contratoId),
            method: 'GET',
            success: function(data) {
                productosContratoActual = data.products;
                let productosFormHtml = '';
                
                // Verificar si hay productos con stock disponible
                const productosConStock = data.products.filter(p => p.total_restante > 0);
                
                if (productosConStock.length === 0) {
                    productosFormHtml = `
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> 
                            No hay productos disponibles para crear nuevas órdenes. Todos los productos han alcanzado su límite del contrato.
                        </div>
                    `;
                    // Deshabilitar el botón de agregar orden
                    $('#btnAgregarOrden').prop('disabled', true).text('Sin productos disponibles');
                } else {
                    data.products.forEach(function(product) {
                        const isDisabled = product.total_restante <= 0 ? 'disabled' : '';
                        const inputClass = product.total_restante <= 0 ? 'form-control bg-light' : 'form-control';
                        const labelClass = product.total_restante <= 0 ? 'text-muted' : '';
                        
                        productosFormHtml += `
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label ${labelClass}">
                                        ${product.name}
                                        (Restante: ${product.total_restante})
                                        ${product.total_restante <= 0 ? '<span class="badge bg-danger ms-1">Sin stock</span>' : ''}
                                    </label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" 
                                           class="${inputClass} cantidad-orden" 
                                           data-product-id="${product.id}" 
                                           placeholder="${product.total_restante <= 0 ? 'Sin stock' : 'Cantidad'}" 
                                           max="${product.total_restante}" 
                                           ${isDisabled}>
                                </div>
                            </div>
                        `;
                    });
                    // Habilitar el botón de agregar orden
                    $('#btnAgregarOrden').prop('disabled', false).text('Agregar Orden');
                }
                
                $('#contenedorProductosOrden').html(productosFormHtml);

                let tableHeader = `<th>N°</th>`;
                data.products.forEach(function(product) {
                    tableHeader += `<th>${product.name}</th>`;
                });
                tableHeader += `<th>Acciones</th>`;
                $('#modalOrdenes thead tr').html(tableHeader);

                const tablaOrdenes = document.getElementById('tablaOrdenes');
                if (data.orders && data.orders.length > 0) {
                    tablaOrdenes.innerHTML = data.orders.map(orden => {
                        let productColumns = '';
                        data.products.forEach(product => {
                            let totalQuantity = 0;
                            if (orden.order_details && orden.order_details.length > 0) {
                                orden.order_details.forEach(detail => {
                                    if (detail.product_id === product.id) {
                                        totalQuantity += parseFloat(detail.quantity) || 0;
                                    }
                                });
                            }
                            productColumns += `<td>${totalQuantity > 0 ? totalQuantity : 'N/A'}</td>`;
                        });
                        return `
                            <tr>
                                <td>${orden.number}</td>
                                ${productColumns}
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="abrirAgregarProductos(${orden.id}, productosContratoActual)" title="Agregar Productos">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                    <!--<button type="button" class="btn btn-sm btn-warning" onclick="abrirAgregarProductos(${orden.id}, productosContratoActual)" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>-->
                                    <button type="button" class="btn btn-sm btn-info" onclick="verAreas(${orden.id})" title="Ver Area">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarOrden(${orden.id})" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tablaOrdenes.innerHTML = `
                        <tr>
                            <td colspan="${data.products.length + 2}" class="text-center text-muted">
                                <i class="bi bi-inbox"></i> No hay órdenes para este contrato
                            </td>
                        </tr>
                    `;
                }

                // Mostrar el modal
                $('#modalOrdenes').modal('show');
            },
            error: function(xhr, status, error) {
                ToastError.fire({text: 'Error al obtener las órdenes del contrato.'});
            }
        });
    }

    function abrirAgregarProductos(orderId, productos) {
        $('#agregar_order_id').val(orderId);

        let html = '';
        productos.forEach(function(product) {
            html += `
                <div class="mb-3">
                    <label class="form-label">
                        ${product.name}
                        (Restante: ${product.total_restante})
                    </label>
                    <input type="hidden" name="product_ids[]" value="${product.id}">
                    <input type="number" name="quantities[]" class="form-control" placeholder="Cantidad" min="0.01" max="${product.total_restante}" step="0.01">
                </div>
            `;
        });
        $('#contenedorProductosAgregar').html(html);
        $('#modalAgregarProductos').modal('show');
    }

    // Botón para agregar orden (sin formulario)
    $(document).on('click', '#btnAgregarOrden', function() {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        
        // Recoger datos de las cantidades
        let product_ids = [];
        let cantidad = {};
        let hasValidData = false;
        let errores = [];
        
        $('.cantidad-orden').each(function() {
            const productId = $(this).data('product-id');
            const qty = parseFloat($(this).val()) || 0;
            const restante = parseFloat($(this).closest('.row').find('label').text().match(/Restante: (\d+\.?\d*)/)?.[1] || 0);
            
            if (qty > 0) {
                // Validar que no exceda el restante
                if (qty > restante) {
                    const productName = $(this).closest('.row').find('label').text().split('(')[0].trim();
                    errores.push(`${productName}: No puede agregar ${qty}, solo quedan ${restante} disponibles`);
                    return;
                }
                
                // Validar que el restante no sea 0
                if (restante <= 0) {
                    const productName = $(this).closest('.row').find('label').text().split('(')[0].trim();
                    errores.push(`${productName}: No hay cantidades disponibles (restante: ${restante})`);
                    return;
                }
                
                product_ids.push(productId);
                cantidad[productId] = qty;
                hasValidData = true;
            }
        });
        
        if (errores.length > 0) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: errores.join('\n')});
            return;
        }
        
        if (!hasValidData) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: 'Por favor ingrese al menos una cantidad válida'});
            return;
        }
        
        $.ajax({
            url: "{{ route('orders.store') }}",
            method: 'POST',
            data: {
                contrato_id: $('#contratoId').val(),
                product_ids: product_ids,
                cantidad: cantidad,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                ToastMessage.fire({text: response.message || 'Orden agregada correctamente'});
                // Refrescar el modal para mostrar los nuevos saldos y órdenes
                const contratoId = $('#contratoId').val();
                verOrdenes(contratoId);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    ToastError.fire({text: xhr.responseJSON.errors.join('\n')});
                } else {
                    ToastError.fire({text: 'Error al agregar la orden'});
                }
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    });

    // Botón para guardar productos (sin formulario)
    $(document).on('click', '#btnGuardarProductos', function() {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        
        // Recoge los datos manualmente
        const order_id = $('#agregar_order_id').val();
        let product_ids = [];
        let quantities = [];
        let hasValidData = false;
        let errores = [];
        
        $('#contenedorProductosAgregar input[name="product_ids[]"]').each(function(i) {
            const productId = $(this).val();
            const quantityInput = $('#contenedorProductosAgregar input[name="quantities[]"]').eq(i);
            const quantity = parseFloat(quantityInput.val()) || 0;
            const maxQuantity = parseFloat(quantityInput.attr('max')) || 0;
            const productName = quantityInput.closest('.mb-3').find('label').text().split('(')[0].trim();
            
            if (quantity > 0) {
                // Validar que no exceda el máximo permitido
                if (quantity > maxQuantity) {
                    errores.push(`${productName}: No puede agregar ${quantity}, máximo permitido: ${maxQuantity}`);
                    return;
                }
                
                // Validar que el máximo no sea 0
                if (maxQuantity <= 0) {
                    errores.push(`${productName}: No hay cantidades disponibles para este producto`);
                    return;
                }
                
                product_ids.push(productId);
                quantities.push(quantity);
                hasValidData = true;
            }
        });
        
        if (errores.length > 0) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: errores.join('\n')});
            return;
        }
        
        if (!hasValidData) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: 'Por favor ingrese al menos una cantidad válida'});
            return;
        }
        
        $.ajax({
            url: "{{ route('orderdetails.store') }}",
            method: 'POST',
            data: {
                order_id: order_id,
                product_ids: product_ids,
                quantities: quantities,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                ToastMessage.fire({text: response.message});
                $('#modalAgregarProductos').modal('hide');
                
                // Recargar órdenes después de cerrar el modal
                setTimeout(function() {
                    const contratoId = $('#contratoId').val();
                    verOrdenes(contratoId);
                }, 300);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    ToastError.fire({text: xhr.responseJSON.errors.join('\n')});
                } else {
                    ToastError.fire({text: 'Error al agregar productos'});
                }
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    });

    // Función para eliminar una orden
    function eliminarOrden(ordenId) {
        Swal.fire({
            title: '¿Eliminar orden?',
            text: 'Esta acción marcará la orden como eliminada.',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

            $.ajax({
                url: "{{ route('orderdetails.removeOrder', ':id') }}".replace(':id', ordenId),
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    ToastMessage.fire({ text: response.message });

                    // 🔁 Recargar lista de órdenes actualizada
                    const contratoId = $('#contratoId').val();
                    verOrdenes(contratoId);
                },
                error: function(xhr) {
                    ToastError.fire({ text: 'Error al eliminar la órden de contrato.' });
                },
                complete: function() {
                    $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                }
            });
        });
    }

    // Lógica para ver áreas de una orden
    function verAreas(ordenId) {
        $('#ordenId').val(ordenId);

        $.ajax({
            url: "{{ route('orders.show', ':id') }}".replace(':id', ordenId),
            method: 'GET',
            success: function(data) {
                // Llenar el contenedor de productos para áreas - solo productos disponibles (sin área)
                if (data && data.order_details && data.order_details.length > 0) {
                    let productosHTML = '';
                    
                    // Filtrar solo productos sin área asignada
                    const productosDisponibles = data.order_details.filter(detail => !detail.area);
                    
                    productosDisponibles.forEach(function(detail) {
                        productosHTML += `
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label mb-0">${detail.product ? detail.product.name : 'Producto'}</label>
                                    (Restante: ${detail.quantity})
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control cantidad-area" 
                                           data-product-id="${detail.product_id}" 
                                           placeholder="Cantidad" 
                                           max="${detail.quantity}" 
                                           step="0.01">
                                </div>
                            </div>
                        `;
                    });
                    
                    if (productosHTML) {
                        $('#productosAreaContainer').html(productosHTML);
                    } else {
                        $('#productosAreaContainer').html(`
                            <div class="row mb-3 align-items-center">
                                <div class="col-12 text-center text-muted">
                                    <i class="bi bi-info-circle"></i> No hay productos disponibles para asignar áreas
                                </div>
                            </div>
                        `);
                    }
                } else {
                    $('#productosAreaContainer').html(`
                        <div class="row mb-3 align-items-center">
                            <div class="col-12 text-center text-muted">
                                <i class="bi bi-info-circle"></i> No hay productos en esta orden
                            </div>
                        </div>
                    `);
                }
                
                const tablaAreas = document.getElementById('tablaAreas');

                // Los order_details contienen la información de las áreas
                if (data && data.order_details && data.order_details.length > 0) {
                    tablaAreas.innerHTML = data.order_details.map((detail, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.area || 'Sin área'}</td>
                            <td>${detail.product ? detail.product.name : '-'}</td>
                            <td>${detail.quantity || 'N/A'}</td>
                            <td>
                                ${detail.area ? `
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle(${detail.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tablaAreas.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center">No hay detalles para esta orden.</td>
                        </tr>
                    `;
                }

                $('#modalAreas').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener los detalles de la orden:', error);
                alert('Error al obtener los detalles de la orden.');
            }
        });
    }

    // Botón para agregar detalle con área (sin formulario)
    $(document).on('click', '#btnAgregarDetalle', function() {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        
        const area = $('#area').val();
        const ordenId = $('#ordenId').val();
        
        if (!area) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: 'Por favor ingrese un área'});
            return;
        }
        
        // Recoger datos de productos y cantidades con validaciones
        let productos = [];
        let hasValidData = false;
        let errores = [];
        
        $('.cantidad-area').each(function() {
            const productId = $(this).data('product-id');
            const qty = parseFloat($(this).val()) || 0;
            const maxQty = parseFloat($(this).attr('max')) || 0;
            
            if (qty > 0) {
                // Validar que no exceda el máximo
                if (qty > maxQty) {
                    const productName = $(this).closest('.row').find('label').text().split('(')[0].trim();
                    errores.push(`${productName}: No puede asignar ${qty}, máximo disponible: ${maxQty}`);
                    return;
                }
                
                // Validar que hay stock disponible
                if (maxQty <= 0) {
                    const productName = $(this).closest('.row').find('label').text().split('(')[0].trim();
                    errores.push(`${productName}: No hay stock disponible para asignar área`);
                    return;
                }
                
                productos.push({
                    product_id: productId,
                    quantity: qty
                });
                hasValidData = true;
            }
        });
        
        if (errores.length > 0) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: errores.join('\n')});
            return;
        }
        
        if (!hasValidData) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: 'Por favor ingrese al menos una cantidad válida'});
            return;
        }
        
        // Procesar cada producto por separado
        let completedRequests = 0;
        let totalRequests = productos.length;
        let errors = [];
        
        productos.forEach(function(producto) {
            $.ajax({
                url: "{{ route('orders.areas.store') }}",
                method: 'POST',
                data: {
                    order_id: ordenId,
                    area: area,
                    product_id: producto.product_id,
                    quantity: producto.quantity,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    completedRequests++;
                    if (completedRequests === totalRequests) {
                        if (errors.length === 0) {
                            ToastMessage.fire({text: `Área "${area}" asignada correctamente a ${totalRequests} producto(s)`});
                        } else {
                            ToastMessage.fire({text: `Área asignada parcialmente. ${errors.length} error(es) encontrado(s)`});
                        }
                        // Refrescar el modal de áreas
                        verAreas(ordenId);
                        // Limpiar formulario
                        $('#area').val('');
                        $('.cantidad-area').val('');
                        $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                    }
                },
                error: function(xhr) {
                    completedRequests++;
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                        errors.push(xhr.responseJSON.message);
                    } else {
                        errors.push('Error al asignar área a un producto');
                    }
                    
                    if (completedRequests === totalRequests) {
                        if (errors.length > 0) {
                            ToastError.fire({text: `Errores: ${errors.join(', ')}`});
                        }
                        // Refrescar el modal de áreas aunque haya errores
                        verAreas(ordenId);
                        // Limpiar formulario
                        $('#area').val('');
                        $('.cantidad-area').val('');
                        $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                    }
                }
            });
        });
    });

    // Función para eliminar un detalle de orden
    function eliminarDetalle(detailId) {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('orderdetails.removeArea', ':id') }}".replace(':id', detailId),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                ToastMessage.fire({ text: response.message });
                const ordenId = $('#ordenId').val();
                verAreas(ordenId);
            },
            error: function(xhr) {
                ToastError.fire({ text: 'Error al eliminar el área del detalle.' });
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    }

    // Validación en tiempo real para cantidad-orden
    $(document).on('input', '.cantidad-orden', function() {
        const qty = parseFloat($(this).val()) || 0;
        const restante = parseFloat($(this).closest('.row').find('label').text().match(/Restante: (\d+\.?\d*)/)?.[1] || 0);
        
        if (qty > restante) {
            $(this).addClass('is-invalid');
            // Crear o actualizar mensaje de error
            let feedback = $(this).siblings('.invalid-feedback');
            if (feedback.length === 0) {
                $(this).after(`<div class="invalid-feedback">No puede exceder ${restante}</div>`);
            } else {
                feedback.text(`No puede exceder ${restante}`);
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Validación en tiempo real para quantities en modal agregar productos
    $(document).on('input', '#contenedorProductosAgregar input[name="quantities[]"]', function() {
        const qty = parseFloat($(this).val()) || 0;
        const maxQty = parseFloat($(this).attr('max')) || 0;
        
        if (qty > maxQty) {
            $(this).addClass('is-invalid');
            // Crear o actualizar mensaje de error
            let feedback = $(this).siblings('.invalid-feedback');
            if (feedback.length === 0) {
                $(this).after(`<div class="invalid-feedback">Máximo permitido: ${maxQty}</div>`);
            } else {
                feedback.text(`Máximo permitido: ${maxQty}`);
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Validación para cantidad-area
    $(document).on('input', '.cantidad-area', function() {
        const qty = parseFloat($(this).val()) || 0;
        const maxQty = parseFloat($(this).attr('max')) || 0;
        
        if (qty > maxQty) {
            $(this).addClass('is-invalid');
            let feedback = $(this).siblings('.invalid-feedback');
            if (feedback.length === 0) {
                $(this).after(`<div class="invalid-feedback">Máximo disponible: ${maxQty}</div>`);
            } else {
                feedback.text(`Máximo disponible: ${maxQty}`);
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    function editarOrden(contractId) {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('contracts.show', ':id') }}".replace(':id', contractId),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const c = response.contract || response;

                $('#editContractId').val(c.id);
                $('#edit_client_id').val(c.client_id ?? (c.client ? c.client.id : ''));
                $('#search_edit_client').val(c.client ? (c.client.business_name || c.client.contact_name || '') : (c.client_name || ''));
                $('#edit_location_id').val(c.location_id ?? c.location?.id ?? '');
                $('#edit_total').val(c.total ?? '');

                // Si el backend devuelve los productos del contrato, pásalos como contractProducts
                // expected format: [{ product_id, unit_price, quantity, subtotal }, ...]
                const contractProducts = c.details || [];

                console.log(contractProducts);

                // Cargar productos de la sede usando la misma ruta que en el formulario principal
                loadEditModalProducts(c.location_id ?? c.location?.id ?? '', contractProducts);

                $('#modalEditarContrato').modal('show');
            },
            error: function(xhr) {
                ToastError.fire({ text: 'Error al cargar los datos del contrato.' });
                console.error('Error cargar contrato:', xhr);
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    }

    function loadEditModalProducts(locationId, contractProducts = []) {
        const container = $('#edit_productos_container');

        if (!locationId) {
            container.html(`<div class="text-muted"><i class="bi bi-info-circle"></i> Seleccione una sede para ver los productos</div>`);
            return;
        }

        container.html(`
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <i class="bi bi-arrow-clockwise spin"></i> Cargando productos...
                </div>
            </div>
        `);

        $.ajax({
            url: "{{ route('contracts.products', ':id') }}".replace(':id', locationId),
            method: 'GET',
            success: function(products) {
                if (!products || products.length === 0) {
                    container.html(`
                        <div class="row mb-3">
                            <div class="col-12 text-center text-warning">
                                <i class="bi bi-exclamation-triangle"></i> No hay productos disponibles en esta sede
                            </div>
                        </div>
                    `);
                    return;
                }

                let html = '';
                products.forEach(function(product) {
                    // buscar valores preexistentes en contractProducts (si el contrato ya tiene precios/cantidades)
                    const existing = contractProducts.find(p => p.product_id === product.id) || {};
                    html += `
                        <div class="row mb-2 align-items-center producto-row-edit" data-product-id="${product.id}">
                            <div class="col-md-4">
                                <label class="form-label mb-0">${product.name}</label>
                                <input type="hidden" name="product_ids[]" value="${product.id}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control precio-input-edit" name="prices_edit[]" 
                                    placeholder="Precio unitario" step="0.01" value="${existing.unit_price ?? ''}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control cantidad-input-edit" name="quantities_edit[]" 
                                    placeholder="Cantidad (${product.measurement_unit || 'unidad'})" step="0.01" value="${existing.quantity ?? ''}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control subtotal-input-edit" name="subtotals_edit[]" 
                                    placeholder="Subtotal" step="0.01" readonly value="${existing.subtotal ? Number(existing.subtotal).toFixed(2) : ''}">
                            </div>
                        </div>
                    `;
                });

                container.html(html);
            },
            error: function() {
                container.html(`<div class="text-danger"><i class="bi bi-exclamation-circle"></i> Error al cargar los productos</div>`);
                ToastError.fire({ text: 'Error al cargar los productos de la sede' });
            }
        });
    }

    
    // También, si el usuario cambia la sede dentro del modal, recargar productos
    $(document).on('change', '#edit_location_id', function() {
        const loc = $(this).val();
        loadEditModalProducts(loc);
    });

    // Mantener cálculo de subtotales en modal editar (precio * cantidad)
    $(document).on('input', '.precio-input-edit, .cantidad-input-edit', function() {
        const row = $(this).closest('.producto-row-edit');
        const precio = parseFloat(row.find('.precio-input-edit').val()) || 0;
        const cantidad = parseFloat(row.find('.cantidad-input-edit').val()) || 0;
        const subtotal = precio * cantidad;
        row.find('.subtotal-input-edit').val(subtotal > 0 ? subtotal.toFixed(2) : '');
    });

    $(document).on('submit', '#editContractForm', function(e) {
        e.preventDefault();

        const id = $('#editContractId').val();
        if (!id) return ToastError.fire({ text: 'ID de contrato inválido.' });

        // Recolectar datos del modal
        const client_id = $('#edit_client_id').val() || null;
        const location_id = $('#edit_location_id').val() || null;
        // Recolectar filas de productos
        const productRows = $('#edit_productos_container .producto-row-edit');
        const product_ids = [];
        const prices_edit = [];
        const quantities_edit = [];
        const subtotals_edit = [];

        let total = 0;

        productRows.each(function() {
            const row = $(this);
            const pid = row.data('product-id');
            const price = parseFloat(row.find('.precio-input-edit').val()) || 0;
            const qty = parseFloat(row.find('.cantidad-input-edit').val()) || 0;
            const sub = parseFloat(row.find('.subtotal-input-edit').val()) || (price * qty);

            // Incluir solo si tiene cantidad > 0 (ajusta la condición si quieres otra)
            product_ids.push(pid);
            prices_edit.push(price);
            quantities_edit.push(qty);
            subtotals_edit.push(sub);

            total += (isNaN(sub) ? 0 : sub);
        });

        // Enviar por AJAX (PUT emulado)
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('contracts.update', ':id') }}".replace(':id', id),
            method: 'POST',
            dataType: 'json',
            headers: { Accept: 'application/json' },
            data: {
                _method: 'PUT',
                _token: '{{ csrf_token() }}',
                client_id: client_id,
                location_id: location_id,
                total: total.toFixed(2),
                product_ids: product_ids,
                prices_edit: prices_edit,
                quantities_edit: quantities_edit,
                subtotals_edit: subtotals_edit
            },
            success: function(response) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                if (response && response.success) {
                    ToastMessage.fire({ text: response.message || 'Contrato actualizado.' }).then(() => {
                        location.reload();
                    });
                } else {
                    ToastError.fire({ text: response.message || 'No se pudo actualizar el contrato.' });
                    console.error('Response update:', response);
                }
            },
            error: function(xhr) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                console.error('AJAX update error:', xhr);

                let mensaje = 'Error al actualizar el contrato.';
                if (xhr.responseJSON) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        mensaje = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                    } else {
                        mensaje = JSON.stringify(xhr.responseJSON);
                    }
                } else {
                    mensaje += ` (${xhr.status} ${xhr.statusText})`;
                }
                ToastError.fire({ text: mensaje });
            }
        });

        return false;
    });

    function verDetalles(id) {
        
        if (!id) return;

        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('contracts.details_modal', ':id') }}".replace(':id', id),
            method: 'GET',
            dataType: 'json',
            headers: { Accept: 'application/json' },
            success: function(resp) {
                if (resp.success && resp.html) {
                    $('#contractModalContainer').html(resp.html);
                    // bootstrap 5 show
                    const modalEl = document.getElementById('contractModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                } else {
                    ToastError.fire({ text: resp.message || 'No se pudo cargar' });
                }
            },
            error: function(xhr) {
                console.error('Error loading modal:', xhr);
                ToastError.fire({ text: 'Error al cargar datos' });
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    };
</script>
@endsection