@extends('template.index')

@section('header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Histórico de Contratos
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Clientes y Crédito</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Contratos</li>
                </ol>
            </nav>
        </div>
        @if (auth()->user()->role->nombre != 'admin')
        <div>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center rounded-pill">
                <i class="bi bi-plus-circle me-2"></i> Registrar Contrato
            </a>
        </div>
        @endif
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body">
                        <!-- Boton de modal -->
<div class="d-flex justify-content-end mb-3">
<button type="button" class="btn btn-primary fw-medium px-4" data-bs-toggle="modal" data-bs-target="#modalRegistrarContrato" style="border-radius: 8px;"><i class="bi bi-plus-circle me-2"></i>Registrar Contrato</button>
</div>
<form action="" id="fromFilter">
                            <div class="row g-2 align-items-end mb-4">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                    <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->start_date ? request()->start_date : '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->end_date ? request()->end_date : '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                    <select class="form-select form-select-sm" id="location_id" name="location_id">
                                        <option value="">Todas las sedes</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Cliente</label>
                                    <input type="text" id="search-client" class="form-control form-control-sm" value="{{ request()->client_name ?? '' }}">
                                    <input type="hidden" id="client_id" name="client_id" value="{{ request()->client_id ?? '' }}">
                                </div>
                                <div class="col-md-3 text-end d-flex justify-content-end gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-medium" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-eraser me-1"></i>Limpiar
                                    </a>
                                    <button id="btnExcel" type="button" class="btn btn-success btn-sm px-2" style="border-radius: 6px;" title="Exportar Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </button>
                                    <button id="btnPdf" type="button" class="btn btn-danger btn-sm px-2" style="border-radius: 6px;" title="Exportar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
<div class="table-responsive">
                            <table id="datatable" class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Documento</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cliente</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Productos</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha Generación</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->client->document }}</td>
                                            <td>{{ $contract->client->commercial_name ?? $contract->client->business_name ?? $contract->client->contact_name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $productos = $contract->totalProductos();
                                                @endphp
                                                @if (count($productos) > 0)
                                                    @foreach ($productos as $producto)
                                                        <li>{{ $producto['product_name'] }}:
                                                            {{ $producto['total_quantity'] }}</li>
                                                    @endforeach
                                                @else
                                                    <li>No hay productos</li>
                                                @endif
                                            </td>
                                            <td>{{ $contract->date->format('d/m/Y') }}</td>
                                            <td>{{ $contract->total }}</td>
                                            <td>{{ $contract->location->name }}</td>
                                            <td>{{ $contract->paid == 0 ? 'No Pagado' : 'Pagado' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-success"
                                                    onclick="openPaymentsModal({{ $contract->id }})"
                                                    title="Gestionar Pagos">
                                                    <i class="bi bi-currency-dollar"></i>
                                                </button>

                                                <button class="btn btn-danger btn-sm btn-eliminar"
                                                    data-id="{{ $contract->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#eliminarModal" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $contracts->links('pagination::bootstrap-4') }}
                        </div>
                    
</div>
</div>
</div>
</div>
</div>
<!-- Modal de Registro de Contrato -->
            <div class="modal fade" id="modalRegistrarContrato" tabindex="-1" aria-labelledby="modalRegistrarContratoLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light border-bottom-0">
                            <h5 class="modal-title fw-bold text-dark" id="modalRegistrarContratoLabel"><i class="bi bi-file-earmark-plus me-2 text-primary"></i>Registrar Contrato</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <form id="formContrato" method="POST" action="{{ route('contracts.store') }}">
                            <div class="modal-body">
                                @csrf
                                <!-- Fila 1: Cliente, búsqueda y botón -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-bold small">Cliente</label>
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
                                        <label class="form-label text-dark fw-bold small">N° de Contrato</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" id="number" class="form-control" value="{{ $nextContractNumber }}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-bold small">Fecha</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="date" id="contract_date" name="date" class="form-control" value="{{ now()->toDateString() }}">
                                    </div>
                                </div>
                
                                <!-- Fila 2: Sede del contrato -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-bold small">Sede</label>
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
                                            <label class="form-check-label text-dark fw-bold small" for="checkOrdenes">Generar Órdenes</label>
                                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Esto generará un número de órdenes automáticas según el número ingresado."></i>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" id="ordenesInput" name="number_of_orders" class="form-control border-dark"
                                            placeholder="N° Órdenes" disabled min="1">
                                    </div>
                                </div>
                
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-bold small">Pago</label>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="paymentType" name="payment_type" class="form-select">
                                            <option value="contado">Al contado</option>
                                            <option value="credito">Al credito</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" id="contractPaidGroup">
                                        <select id="contractPaid" name="contract_paid" class="form-select">
                                            <option value="0">No pagado</option>
                                            <option value="1">Ya pagado</option>
                                        </select>
                                    </div>
                                </div>
                
                                <div class="row mb-3 align-items-center d-none" id="paymentDetailsGroup">
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-bold small">Medio de pago</label>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="paymentMethod" name="payment_method_id" class="form-select">
                                            <option value="">Seleccione</option>
                                            @foreach($paymentMethods as $method)
                                                <option value="{{ $method->id }}" data-name="{{ strtolower(trim($method->name)) }}">{{ $method->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-none" id="paymentIsleGroup">
                                        <select id="paymentIsle" name="payment_isle_id" class="form-select">
                                            <option value="">Caja de isla</option>
                                            @foreach($isles as $isle)
                                                <option value="{{ $isle->id }}" data-location="{{ $isle->location_id }}">{{ $isle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-footer border-top-0 bg-light">
                                <!-- Botón de Guardar Contrato -->
                                <div class="w-100 d-flex justify-content-end align-items-center gap-3">
                                    <div class="d-flex align-items-center">
                                        <label for="totalContrato" class="form-label mb-0 me-2 fw-bold text-dark">Total S/:</label>
                                        <input type="number" id="totalContrato" class="form-control form-control-sm text-end fw-bold text-primary" style="width: 120px;" name="total"
                                            placeholder="0.00" readonly step="0.01">
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-medium"><i class="bi bi-save me-1"></i> Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
@endsection

@section("scripts")
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
                                    label: item.commercial_name || item.business_name || item.contact_name || '',
                                    value: item.commercial_name || item.business_name || item.contact_name || '',
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
        appendTo: '#modalRegistrarContrato',
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
                                    label: item.commercial_name || item.business_name || item.contact_name || '',
                                    value: item.commercial_name || item.business_name || item.contact_name || '',
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
        function filterPaymentIsles() {
            const locationId = $('#sedeSelect').val();
            $('#paymentIsle option').each(function() {
                const optionLocation = $(this).data('location');
                const shouldShow = !optionLocation || String(optionLocation) === String(locationId);
                $(this).toggle(shouldShow);
            });

            const selectedOption = $('#paymentIsle option:selected');
            if (selectedOption.length && selectedOption.data('location') && String(selectedOption.data('location')) !== String(locationId)) {
                $('#paymentIsle').val('');
            }
        }

        function togglePaymentFields() {
            const paymentType = $('#paymentType').val();
            const isPaid = $('#contractPaid').val() === '1';
            const methodName = ($('#paymentMethod option:selected').data('name') || '').toString().toLowerCase();
            const isCash = methodName === 'efectivo';

            $('#contractPaidGroup').toggleClass('d-none', paymentType !== 'contado');
            $('#paymentDetailsGroup').toggleClass('d-none', !(paymentType === 'contado' && isPaid));
            $('#paymentIsleGroup').toggleClass('d-none', !(paymentType === 'contado' && isPaid && isCash));

            if (paymentType !== 'contado') {
                $('#contractPaid').val('0');
            }

            if (!(paymentType === 'contado' && isPaid)) {
                $('#paymentMethod').val('');
                $('#paymentIsle').val('');
            }

            if (!isCash) {
                $('#paymentIsle').val('');
            }

            filterPaymentIsles();
        }

        $('#paymentType, #contractPaid, #paymentMethod').on('change', togglePaymentFields);
        togglePaymentFields();
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
            filterPaymentIsles();

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

        if (!$('#contract_date').val()) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({ text: 'Seleccione la fecha del contrato' });
            return false;
        }

        if (!$('#sedeSelect').val()) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({ text: 'Seleccione una sede' });
            return false;
        }

        if ($('#paymentType').val() === 'contado' && $('#contractPaid').val() === '1') {
            if (!$('#paymentMethod').val()) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                ToastError.fire({ text: 'Seleccione el medio de pago utilizado' });
                return false;
            }

            const methodName = ($('#paymentMethod option:selected').data('name') || '').toString().toLowerCase();
            if (methodName === 'efectivo' && !$('#paymentIsle').val()) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                ToastError.fire({ text: 'Seleccione la caja de isla donde ingresara el efectivo' });
                return false;
            }
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
                            productColumns += `<td>${totalQuantity > 0 ? totalQuantity : '0'}</td>`;
                        });
                        return `
                            <tr id="row-orden-${orden.id}">
                                <td>${orden.number}</td>
                                ${productColumns}
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleAgregarProductos(${orden.id})" title="Agregar Productos">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                    <!--<button type="button" class="btn btn-sm btn-warning" onclick="toggleAgregarProductos(${orden.id})" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>-->
                                    <button type="button" class="btn btn-sm btn-info" onclick="toggleAreas(${orden.id})" title="Ver Area">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarOrden(${orden.id})" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr id="form-agregar-${orden.id}" style="display: none;" class="bg-light">
                                <td colspan="${data.products.length + 2}" class="p-0">
                                    <div class="p-3 m-2 border rounded shadow-sm bg-white border-primary" style="border-left: 4px solid #0d6efd !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary mb-0"><i class="bi bi-box-seam"></i> Asignar productos a <b>${orden.number}</b></h6>
                                            <button type="button" class="btn-close" onclick="toggleAgregarProductos(${orden.id})"></button>
                                        </div>
                                        <div class="row" id="inputs-orden-${orden.id}">
                                            <!-- Inputs rendered here by JS -->
                                        </div>
                                        <div class="d-flex justify-content-end mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary me-2" onclick="toggleAgregarProductos(${orden.id})">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="guardarProductosInline(${orden.id})">
                                                <i class="bi bi-check-circle"></i> Guardar Cantidades
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr id="form-areas-${orden.id}" style="display: none;" class="bg-light">
                                <td colspan="${data.products.length + 2}" class="p-0">
                                    <div class="p-3 m-2 border rounded shadow-sm bg-white border-info" style="border-left: 4px solid #0dcaf0 !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-info mb-0"><i class="bi bi-diagram-3"></i> Áreas de la orden <b>${orden.number}</b></h6>
                                            <button type="button" class="btn-close" onclick="toggleAreas(${orden.id})"></button>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label small fw-bold text-dark mb-1">Nombre del Área</label>
                                                <input type="text" class="form-control form-control-sm border-info" id="area-input-${orden.id}" placeholder="Ej: Limpieza Pública">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row" id="productos-area-${orden.id}">
                                                    <!-- inputs generados por js -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mb-3">
                                            <button type="button" class="btn btn-sm btn-info text-white shadow-sm" onclick="guardarAreasInline(${orden.id})">
                                                <i class="bi bi-plus-circle"></i> Agregar Detalle con Área
                                            </button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Área</th>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabla-areas-list-${orden.id}">
                                                    <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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

    function toggleAgregarProductos(ordenId) {
        const formRow = $(`#form-agregar-${ordenId}`);
        
        if (formRow.is(':visible')) {
            formRow.fadeOut('fast');
            return;
        }
        
        let html = '';
        productosContratoActual.forEach(function(product) {
            html += `
                <div class="col-md-3 mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        ${product.name}
                    </label>
                    <div class="input-group input-group-sm mb-1">
                        <input type="hidden" name="inline_product_ids_${ordenId}[]" value="${product.id}">
                        <input type="number" name="inline_quantities_${ordenId}[]" 
                               class="form-control border-primary shadow-none" 
                               placeholder="Cantidad" min="0.01" max="${product.total_restante}" step="0.01"
                               ${product.total_restante <= 0 ? 'disabled' : ''}>
                    </div>
                    <small class="text-muted"><span class="badge ${product.total_restante > 0 ? 'bg-success' : 'bg-danger'}">Restante: ${product.total_restante}</span></small>
                </div>
            `;
        });
        $(`#inputs-orden-${ordenId}`).html(html);
        
        $('[id^="form-agregar-"]').not(formRow).fadeOut('fast');
        formRow.fadeIn('fast');
    }

    function guardarProductosInline(ordenId) {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        
        let product_ids = [];
        let quantities = [];
        let hasValidData = false;
        let errores = [];
        
        $(`input[name="inline_product_ids_${ordenId}[]"]`).each(function(i) {
            const productId = $(this).val();
            const quantityInput = $(`input[name="inline_quantities_${ordenId}[]"]`).eq(i);
            const quantity = parseFloat(quantityInput.val()) || 0;
            const maxQuantity = parseFloat(quantityInput.attr('max')) || 0;
            const productName = quantityInput.closest('.col-md-3').find('label').text().trim();
            
            if (quantity > 0) {
                if (quantity > maxQuantity) {
                    errores.push(`${productName}: No puede agregar ${quantity}, máximo permitido: ${maxQuantity}`);
                    return;
                }
                
                if (maxQuantity <= 0) {
                    errores.push(`${productName}: No hay cantidades disponibles`);
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
                order_id: ordenId,
                product_ids: product_ids,
                quantities: quantities,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                ToastMessage.fire({text: response.message});
                const contratoId = $('#contratoId').val();
                verOrdenes(contratoId);
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
            confirmButtonText: 'Sí, eliminar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger text-white mx-2',
                cancelButton: 'btn btn-secondary text-white mx-2'
            }
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

    // --- LÓGICA DE ÁREAS (INLINE) ---
    function toggleAreas(ordenId) {
        const formRow = $(`#form-areas-${ordenId}`);
        
        if (formRow.is(':visible')) {
            formRow.fadeOut('fast');
            return;
        }
        
        $('[id^="form-agregar-"]').fadeOut('fast');
        $('[id^="form-areas-"]').not(formRow).fadeOut('fast');
        formRow.fadeIn('fast');
        
        cargarDatosAreas(ordenId);
    }

    function cargarDatosAreas(ordenId) {
        $.ajax({
            url: "{{ route('orders.show', ':id') }}".replace(':id', ordenId),
            method: 'GET',
            success: function(data) {
                if (data && data.order_details && data.order_details.length > 0) {
                    let productosHTML = '';
                    const productosDisponibles = data.order_details.filter(detail => !detail.area);
                    
                    productosDisponibles.forEach(function(detail) {
                        productosHTML += `
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-2">
                                <label class="form-label small fw-bold text-dark mb-1 text-truncate w-100" title="${detail.product ? detail.product.name : 'Producto'}">
                                    ${detail.product ? detail.product.name : 'Producto'}
                                </label>
                                <div class="input-group input-group-sm mb-1">
                                    <input type="number" class="form-control cantidad-area-inline border-info shadow-none" 
                                           data-product-id="${detail.product_id}" 
                                           placeholder="Cant." 
                                           max="${detail.quantity}" 
                                           step="0.01">
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Restante: ${detail.quantity}</small>
                            </div>
                        `;
                    });
                    
                    if (productosHTML) {
                        $(`#productos-area-${ordenId}`).html(productosHTML);
                    } else {
                        $(`#productos-area-${ordenId}`).html(`<span class="text-muted small"><i class="bi bi-info-circle"></i> No hay productos disponibles para asignar áreas</span>`);
                    }
                } else {
                    $(`#productos-area-${ordenId}`).html(`<span class="text-muted small"><i class="bi bi-info-circle"></i> No hay productos en esta orden</span>`);
                }
                
                const tablaAreas = document.getElementById(`tabla-areas-list-${ordenId}`);
                if (data && data.order_details && data.order_details.length > 0) {
                    tablaAreas.innerHTML = data.order_details.map((detail, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.area || '<span class="text-muted">Sin área</span>'}</td>
                            <td>${detail.product ? detail.product.name : '-'}</td>
                            <td>${detail.quantity || 'N/A'}</td>
                            <td>
                                ${detail.area ? `
                                    <button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="eliminarDetalleInline(${detail.id}, ${ordenId})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tablaAreas.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No hay detalles para esta orden.</td></tr>`;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                ToastError.fire({text: 'Error al obtener áreas.'});
            }
        });
    }

    function guardarAreasInline(ordenId) {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        
        const area = $(`#area-input-${ordenId}`).val();
        
        if (!area) {
            $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            ToastError.fire({text: 'Por favor ingrese un área'});
            return;
        }
        
        let productos = [];
        let hasValidData = false;
        let errores = [];
        
        $(`#productos-area-${ordenId} .cantidad-area-inline`).each(function() {
            const productId = $(this).data('product-id');
            const qty = parseFloat($(this).val()) || 0;
            const maxQty = parseFloat($(this).attr('max')) || 0;
            
            if (qty > 0) {
                if (qty > maxQty) {
                    errores.push(`No puede asignar ${qty}, máximo disponible: ${maxQty}`);
                    return;
                }
                productos.push({ product_id: productId, quantity: qty });
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
                            ToastMessage.fire({text: `Área asignada correctamente`});
                        } else {
                            ToastMessage.fire({text: `Área asignada parcialmente.`});
                        }
                        $(`#area-input-${ordenId}`).val('');
                        cargarDatosAreas(ordenId);
                        $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                    }
                },
                error: function(xhr) {
                    completedRequests++;
                    errors.push('Error al asignar área');
                    if (completedRequests === totalRequests) {
                        ToastError.fire({text: `Errores al guardar.`});
                        cargarDatosAreas(ordenId);
                        $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                    }
                }
            });
        });
    }

    function eliminarDetalleInline(detailId, ordenId) {
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('orderdetails.removeArea', ':id') }}".replace(':id', detailId),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                ToastMessage.fire({ text: response.message });
                cargarDatosAreas(ordenId);
            },
            error: function(xhr) {
                ToastError.fire({ text: 'Error al eliminar el área del detalle.' });
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            }
        });
    }
    // --- FIN LÓGICA DE ÁREAS (INLINE) ---

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

    // Validación en tiempo real para quantities en la fila expandida (inline)
    $(document).on('input', 'input[name^="inline_quantities_"]', function() {
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
                                const clientName = item.commercial_name || item.business_name || item.contact_name || '';
                                return {
                                    label: clientName,
                                    value: clientName,
                                    id: item.id,
                                };
                            }));
                            }
                        });
                    } else {
                        // Si no hay letras, limpia el autocomplete
                        response([]);
                    }
                }, 1500);
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

        let contractoAEliminar = null;

        document.addEventListener('DOMContentLoaded', function() {
            const eliminarModal = document.getElementById('eliminarModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                contractoAEliminar = button.getAttribute('data-id');
            });

            document.getElementById('btnEliminarcontracto').addEventListener('click', function() {
                if (!contractoAEliminar) return;
                $.ajax({
                    url: '{{ route('contracts.destroy', ':id') }}'.replace(':id',
                        contractoAEliminar),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#eliminarModal').modal('hide');
                        ToastMessage.fire({
                            text: "Crédito eliminado correctamente"
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        $('#eliminarModal').modal('hide');
                        ToastError.fire({
                            text: "Ocurrió un error al eliminar el crédito"
                        });
                    }
                });
            });
            $('#btnExcel').on('click', function() {
                const formData = $('#fromFilter').serialize();

                // Crear URL para descargar Excel con los filtros actuales
                const excelUrl = "{{ route('contracts.excel') }}?" + formData;

                // Mostrar indicador de carga
                $(this).html('<i class="bi bi-download"></i> Descargando...').prop('disabled', true);

                // Crear un enlace temporal para descargar
                const link = document.createElement('a');
                link.href = excelUrl;
                link.download = 'contratos_historico.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Restaurar el botón después de un momento
                setTimeout(() => {
                    $(this).html('Excel').prop('disabled', false);
                }, 2000);
            });

            $('#btnPdf').on('click', function() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const location_id = document.getElementById('location_id').value;
                const client_id = document.getElementById('client_id').value;

                let pdfUrl = '{{ route('contracts.pdf') }}';
                const params = new URLSearchParams();

                if (startDate) params.append('start_date', startDate);
                if (endDate) params.append('end_date', endDate);
                if (location_id) params.append('location_id', location_id);
                if (client_id) params.append('client_id', client_id);

                if (params.toString()) {
                    pdfUrl += '?' + params.toString();
                }

                console.log('URL generada:', pdfUrl);

                // Crear un enlace temporal para forzar la descarga
                const link = document.createElement('a');
                link.href = pdfUrl;
                link.download = 'reporte_contratos' + '.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    
</script>
@endsection
