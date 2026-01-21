@extends('template.index')

@section('header')
    <h1>Gestión de Créditos</h1>
    <p>Administración de créditos</p>
@endsection


@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <!-- Card que contiene el formulario y la tabla -->
        <div class="card shadow">
            <!-- Cuerpo del Card -->
            <div class="card-body border-bottom">
                <form id="formFiltros" method="GET" action="{{ route('credits.create') }}">
                    <div class="row d-flex">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cliente</label>
                            <input type="text" id="search-client-filter" class="form-control" placeholder="Buscar cliente..." value="{{ $client ? ($client->business_name ?? $client->contact_name) : '' }}">
                            <input type="hidden" id="client_id_filter" name="client_id" value="{{ request()->client_id ?? '' }}">
                        </div>
                        <!-- Numero de credito -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">N° de Crédito</label>
                            <input type="text" id="number_filter" name="number" class="form-control" placeholder="Buscar crédito..." value="{{ request()->number ?? '' }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sede</label>
                            <select class="form-select" id="sedeSelectFilter" name="location_id">
                                <option value="">Todas las sedes</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" {{ request()->location_id == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="payment_date_filter" class="form-label small">Fecha de Pago</label>
                            <input type="date" class="form-control" name="payment_date" id="payment_date_filter" value="{{ request()->payment_date ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btnFiltrar">Filtrar</button>
                                <a href="{{ route('credits.create') }}" class="btn btn-warning" id="btnLimpiar">Limpiar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-3">
                <!-- Tabla de créditos -->
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>N° de Crédito</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Fecha Pago</th>
                                <th>Sede</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($credits as $credit)
                                <tr>
                                    <td>{{ $credit->client ? ($credit->client->business_name ?? $credit->client->contact_name) : ($credit->client_name ?? $credit->client ?? 'Sin cliente') }}</td>
                                    <td>{{ $credit->number ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            // Si tiene agreement, obtener productos del agreement
                                            if ($credit->agreement && $credit->agreement->agreement_details) {
                                                $productos = $credit->agreement->agreement_details;
                                            } 
                                            // Si es venta directa, obtener productos de la venta
                                            elseif ($credit->sale && $credit->sale->sale_details) {
                                                $productos = $credit->sale->sale_details;
                                            } else {
                                                $productos = [];
                                            }
                                        @endphp 
                                        @if (count($productos) > 0)
                                            @foreach ($productos as $producto)
                                                <li>{{ $producto->product ? $producto->product->name : 'Producto desconocido' }}</li>
                                            @endforeach
                                        @else
                                            <li>No hay productos</li>
                                        @endif
                                    </td>
                                    <td>S/ {{ number_format($credit->amount ?? 0, 2) }}</td>
                                    <td>{{ $credit->date ? \Carbon\Carbon::parse($credit->date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        @if ($credit->agreement && $credit->agreement->location)
                                            {{ $credit->agreement->location->name }}
                                        @elseif ($credit->sale && $credit->sale->location)
                                            {{ $credit->sale->location->name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($credit->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @else
                                            <span class="badge bg-success">Pagado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success"
                                            onclick="openPaymentsModal({{ $credit->id }})" title="Gestionar Pagos">
                                            <i class="bi bi-currency-dollar"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="{{ $credit->id }}"
                                            data-bs-toggle="modal" data-bs-target="#eliminarModal" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $credits->links('pagination::bootstrap-4') }}
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
                                    <label for="document" class="form-label">Documento <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="document" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_name" class="form-label">Nombre / Razón Social <span
                                            class="text-danger">*</span></label>
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

    <div class="modal fade modal-lg" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="eliminarModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas anular este crédito?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnEliminarCredito">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-payments-modal />
    <script>
        var productosContratoActual = [];
        let clientSearchTimeout = null;
        
        // Autocomplete para el filtro
        $('#search-client-filter').autocomplete({
            source: function(request, response) {
                clearTimeout(clientSearchTimeout);
                clientSearchTimeout = setTimeout(function() {
                    let currentTerm = $('#search-client-filter').val();
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
                        response([]);
                    }
                }, 750);
            },
            appendTo: '.container-fluid',
            select: function(event, ui) {
                $('#client_id_filter').val(ui.item.id);
            },
        }).autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>")
                .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
                .appendTo(ul);
        };
        // Autocomplete para el formulario de creación
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
                                        label: item.business_name ? item
                                            .business_name : item.contact_name,
                                        value: item.business_name ? item
                                            .business_name : item.contact_name,
                                        id: item.id,
                                    };
                                }));
                            }
                        });
                    } else {
                        // Si no hay letras, limpia el autocomplete
                        response([]);
                    }
                }, 750);
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
        // El botón filtrar ya está configurado con type="submit" en el formulario de filtros
        // No necesita JavaScript adicional

      


        // Validación del formulario de contrato con ToastError
        $('#formContrato').on('submit', function(e) {
            e.preventDefault();

            $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

            // Validar que hay cliente seleccionado
            if (!$('#client_id').val()) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                ToastError.fire({
                    text: 'Por favor selecciona un cliente'
                });
                return false;
            }

            if (!$('#payment_date').val()) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                ToastError.fire({
                    text: 'Por favor selecciona una fecha de pago'
                });
                e.preventDefault();
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
                ToastError.fire({
                    text: errores.join('\n')
                });
                return false;
            }

            if (!hasProducts) {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                ToastError.fire({
                    text: 'Por favor ingresa al menos un producto con precio y cantidad válidos'
                });
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
                        ToastMessage.fire({
                            text: response.message || 'Crédito guardado correctamente'
                        });
                        location.reload();

                    } else {
                        // backend devolvió success:false
                        ToastError.fire({
                            text: response.message || 'Error al guardar el crédito'
                        });
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

                    ToastError.fire({
                        text: mensaje
                    });
                }
            });

            return false;

            // Si todas las validaciones pasan, el spinner se mantiene activo
            // hasta que la página redirija o se recargue
        });

        let creditoAEliminar = null;

        document.addEventListener('DOMContentLoaded', function() {
            const eliminarModal = document.getElementById('eliminarModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                creditoAEliminar = button.getAttribute('data-id');
            });

            document.getElementById('btnEliminarCredito').addEventListener('click', function() {
                if (!creditoAEliminar) return;
                $.ajax({
                    url: '{{ route('credits.destroy', ':id') }}'.replace(':id', creditoAEliminar),
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
        });
    </script>
@endsection
