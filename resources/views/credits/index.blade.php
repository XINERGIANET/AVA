@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-credit-card me-2 text-primary"></i>Histórico de Créditos
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Clientes y Crédito</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Créditos</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body">
                        <form action="{{ route('credits.index') }}" method="GET" id="fromFilter">
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
                                        @foreach ($locations ?? [] as $location)
                                            <option value="{{ $location->id }}" {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Estado</label>
                                    <select class="form-select form-select-sm" id="status" name="status">
                                        <option value="" {{ request()->has('status') && request()->status == '' ? 'selected' : '' }}>Todos</option>
                                        <option value="pending" {{ (!request()->has('status') || request()->status == 'pending') ? 'selected' : '' }}>No Pagado / Pendiente</option>
                                        <option value="paid" {{ request()->status == 'paid' ? 'selected' : '' }}>Pagado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Cliente</label>
                                    <div class="position-relative">
                                        <input type="text" id="search-client" name="client_name" class="form-control form-control-sm" placeholder="Buscar cliente..." value="{{ request()->client_name ?? ($client ? ($client->business_name ?: $client->contact_name) : '') }}" autocomplete="off">
                                        <input type="hidden" id="client_id" name="client_id" value="{{ request()->client_id ?? ($client ? $client->id : '') }}">
                                    </div>
                                </div>
                                <div class="col-md-4 text-end d-flex justify-content-end gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-medium" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('credits.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-eraser me-1"></i>Limpiar
                                    </a>
                                    <button id="btnExcel" type="button" class="btn btn-success btn-sm px-2" style="border-radius: 6px;" title="Exportar Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-pdf btn-sm px-2" style="border-radius: 6px;" title="Exportar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="d-flex flex-column align-items-end mb-3">
                            <h6 class="mb-1 text-muted fw-bold">Total Deuda: <span id="total-deuda-display" class="text-danger fw-bold">S/ {{ number_format($totalDeuda, 2, '.', ',') }}</span></h6>
                            <h6 class="mb-0 text-muted fw-bold">Total Pagado: <span id="total-pagado-display" class="text-success fw-bold">S/ {{ number_format($totalPagado, 2, '.', ',') }}</span></h6>
                        </div>

                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important; width: 150px;">Documento</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cliente</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Productos</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha Generación</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha Pago</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($credits as $credit)
                                        @php
                                            $creditAmount = $credit->amount;
                                            if ($credit->sale) {
                                                $creditAmount = $credit->sale->total;
                                            } elseif ($credit->agreement) {
                                                $creditAmount = $credit->agreement->total;
                                            }
                                        @endphp
                                        <tr class="credit-main-row" data-credit-id="{{ $credit->id }}" data-amount="{{ (float)$creditAmount }}" data-status="{{ $credit->status }}">
                                            <td class="text-start">
                                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                                    <button class="btn btn-primary p-0 d-flex align-items-center justify-content-center" 
                                                            style="width: 22px; height: 22px; border-radius: 50%;" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#row-{{ $credit->id }}" 
                                                            title="Ver más detalles">
                                                        <i class="bi bi-plus text-white" style="font-size: 16px; line-height: 1;"></i>
                                                    </button>
                                                    <span class="fw-medium">{{ $credit->client ? $credit->client->document : 'N/A' }}</span>
                                                </div>
                                            </td>
                                            <td style="white-space: normal; word-break: break-word; min-width: 250px; max-width: 350px;">
                                                {{ $credit->client_name ? $credit->client_name : 'Sin cliente' }}
                                            </td>
                                            <td class="text-start">
                                                @php
                                                    // Determinar si es de sale o agreement
                                                    if ($credit->sale && $credit->sale->sale_details) {
                                                        $productos = $credit->sale->totalProductos();
                                                    } elseif ($credit->agreement && $credit->agreement->agreement_details) {
                                                        $productos = [];
                                                        foreach ($credit->agreement->agreement_details as $detail) {
                                                            $productId = $detail->product_id;
                                                            if (!isset($productos[$productId])) {
                                                                $productos[$productId] = [
                                                                    'product_name' => $detail->product->name ?? 'Producto desconocido',
                                                                    'total_quantity' => 0
                                                                ];
                                                            }
                                                            $productos[$productId]['total_quantity'] += $detail->quantity;
                                                        }
                                                    } else {
                                                        $productos = [];
                                                    }
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
                                            <td>
                                                @if ($credit->sale)
                                                    {{ $credit->sale->date ? $credit->sale->date->format('d/m/Y') : 'N/A' }}
                                                @elseif ($credit->agreement)
                                                    {{ $credit->agreement->date ? $credit->agreement->date->format('d/m/Y') : 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="fw-bold">
                                                @if ($credit->sale)
                                                    S/ {{ $credit->sale->total }}
                                                @elseif ($credit->agreement)
                                                    S/ {{ $credit->agreement->total }}
                                                @else
                                                    S/ {{ $credit->amount }}
                                                @endif
                                            </td>
                                            <td>{{ $credit->date ? $credit->date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @if ($credit->sale && $credit->sale->location)
                                                    {{ $credit->sale->location->name }}
                                                @elseif ($credit->agreement && $credit->agreement->location)
                                                    {{ $credit->agreement->location->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $credit->status == 'paid' ? 'Pagado' : 'No Pagado' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @if ($credit->status != 'paid')
                                                        <button class="btn btn-sm btn-success"
                                                            onclick="openPaymentsModal({{ $credit->id }}, 'payment')" title="Gestionar Pagos">
                                                            <i class="bi bi-currency-dollar"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-danger btn-sm btn-eliminar"
                                                        data-id="{{ $credit->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#eliminarModal" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Fila colapsable de Ver más -->
                                        <tr id="row-{{ $credit->id }}" class="collapse bg-light">
                                            <td colspan="10" class="text-start p-3">
                                                <div class="row w-100">
                                                    <div class="col-md-4">
                                                        <strong class="text-muted"><i class="bi bi-ticket-detailed me-1"></i> Código Vale:</strong> 
                                                        <span class="ms-1">{{ $credit->sale && $credit->sale->voucher_code ? $credit->sale->voucher_code : 'N/A' }}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong class="text-muted"><i class="bi bi-person-badge me-1"></i> Responsable:</strong>
                                                        <span class="ms-1">{{ $credit->sale && $credit->sale->responsible ? $credit->sale->responsible->name . ' ' . $credit->sale->responsible->last_name : 'N/A' }}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong class="text-muted"><i class="bi bi-card-text me-1"></i> Detalle:</strong>
                                                        <span class="ms-1">{{ $credit->sale && $credit->sale->detail ? $credit->sale->detail : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $credits->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="eliminarModalLabel"><i class="bi bi-trash me-2 text-danger"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0 text-muted">¿Estás seguro de que deseas anular este crédito?</p>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger px-4" id="btnEliminarCredito">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-payments-modal />
    <script>
        let clientSearchTimeout = null;
        $(document).on('click', '.btn-pdf', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const location_id = document.getElementById('location_id').value;
            const client_id = document.getElementById('client_id').value;

            let pdfUrl = '{{ route('credits.pdf') }}';
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
            link.download = 'reporte_creditos' + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        const initialTotalDeuda = {{ (float)$totalDeuda }};
        const initialTotalPagado = {{ (float)$totalPagado }};

        // Función para filtrar en tiempo real en la tabla y actualizar totales
        function filterTableByClient(term) {
            term = (term || '').trim().toLowerCase();
            if (!term) {
                $('#datatable tbody tr.credit-main-row').show();
                // Restaurar display limpio en las filas collapse (respetando Bootstrap)
                $('#datatable tbody tr.collapse').each(function() {
                    if (!$(this).hasClass('show')) {
                        $(this).css('display', '');
                    }
                });
                $('#total-deuda-display').text('S/ ' + initialTotalDeuda.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#total-pagado-display').text('S/ ' + initialTotalPagado.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            } else {
                let sumDeuda = 0;
                let sumPagado = 0;

                $('#datatable tbody tr.credit-main-row').each(function() {
                    const $row = $(this);
                    const creditId = $row.data('credit-id');
                    const $collapseRow = $('#row-' + creditId);
                    const clientText = ($row.find('td:nth-child(2)').text() || '').toLowerCase();
                    const docText = ($row.find('td:nth-child(1)').text() || '').toLowerCase();
                    const amount = parseFloat($row.data('amount')) || 0;
                    const status = $row.data('status');

                    if (clientText.includes(term) || docText.includes(term)) {
                        $row.show();
                        if ($collapseRow.hasClass('show')) {
                            $collapseRow.show();
                        } else {
                            $collapseRow.css('display', '');
                        }

                        if (status === 'paid') {
                            sumPagado += amount;
                        } else {
                            sumDeuda += amount;
                        }
                    } else {
                        $row.hide();
                        $collapseRow.hide();
                    }
                });

                $('#total-deuda-display').text('S/ ' + sumDeuda.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#total-pagado-display').text('S/ ' + sumPagado.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        }

        // Filtrado en tiempo real de la tabla al escribir
        $('#search-client').on('input', function() {
            const val = $(this).val();
            if (!val.trim()) {
                $('#client_id').val('');
            }
            filterTableByClient(val);
        });

        // Al cargar la página, si ya hay un valor de cliente en el input, aplicar el cálculo
        if ($('#search-client').val()) {
            filterTableByClient($('#search-client').val());
        }

        // Auto-submit al cambiar Sede o Estado
        $('#location_id, #status').on('change', function() {
            $('#fromFilter').submit();
        });

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
                                        label: item.business_name || item.contact_name,
                                        value: item.business_name || item.contact_name,
                                        id: item.id,
                                    };
                                }));
                            }
                        });
                    } else {
                        // Si no hay letras, limpia el autocomplete
                        response([]);
                    }
                }, 200);
            },
            appendTo: '.container-fluid',
            select: function(event, ui) {
                $('#client_id').val(ui.item.id);
                $('#search-client').val(ui.item.value);
                // Enviar el formulario para recalcular totales y paginación exacta
                $('#fromFilter').submit();
            },
        }).autocomplete("instance")._renderItem = function(ul, item) {
            return $("<li>")
                .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
                .appendTo(ul);
        };

        let creditoAEliminar = null;

        document.addEventListener('DOMContentLoaded', function() {
            const eliminarModal = document.getElementById('eliminarModal');
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                creditoAEliminar = button.getAttribute('data-id');
            });
            $('#btnExcel').on('click', function() {
                // Obtener los valores del formulario
                const formData = $('#fromFilter').serialize();

                // Crear URL para descargar Excel con los filtros actuales
                const excelUrl = "{{ route('credits.excel') }}?" + formData;

                // Mostrar indicador de carga
                $(this).html('<i class="bi bi-download"></i> Descargando...').prop('disabled', true);

                // Crear un enlace temporal para descargar
                const link = document.createElement('a');
                link.href = excelUrl;
                link.download = 'creditos_historico.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Restaurar el botón después de un momento
                setTimeout(() => {
                    $(this).html('Excel').prop('disabled', false);
                }, 2000);
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
