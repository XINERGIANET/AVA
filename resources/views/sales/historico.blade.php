@extends('template.index')

@section('header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="bi bi-cart-check me-2 text-primary"></i>Histórico de Ventas
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Reportes</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Ventas</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('sales.index') }}" class="btn btn-primary shadow-sm d-flex align-items-center rounded-pill">
                <i class="bi bi-plus-circle me-2"></i> Registrar Venta
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body border-bottom">
                        <form action="" id="fromFilter">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Fecha inicial</label>
                                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                        value="{{ request()->start_date ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Fecha final</label>
                                    <input type="date" id="end_date" class="form-control form-control-sm" name="end_date"
                                        value="{{ request()->end_date ?? '' }}">
                                </div>

                                @if($isMaster)
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Sede</label>
                                    <select class="form-select form-select-sm" id="location_id" name="location_id">
                                        <option value="">Todos</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">N° Comprobante</label>
                                    <input type="number" id="num_comprobante" name="number" class="form-control form-control-sm"
                                        value="{{ request()->number ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Cliente</label>
                                    <input type="hidden" id="client_id" name="client_id"
                                        value="{{ request()->client_id ?? '' }}">
                                    <input type="text" id="search-client" class="form-control form-control-sm"
                                        value="{{ request()->client_name ?? '' }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Comprobante</label>
                                    <select class="form-select form-select-sm" id="voucher_type" name="voucher_type">
                                        <option value="">Todos</option>
                                        <option value="Boleta" {{ request()->voucher_type == 'Boleta' ? 'selected' : '' }}>Boleta</option>
                                        <option value="Factura" {{ request()->voucher_type == 'Factura' ? 'selected' : '' }}>Factura</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Método de Pago</label>
                                    <select class="form-select form-select-sm" id="payment_method_id" name="payment_method_id">
                                        <option value="">Todos</option>
                                        @foreach ($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}"
                                                {{ request()->payment_method_id == $pm->id ? 'selected' : '' }}>
                                                {{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Tipo de venta</label>
                                    <select class="form-select form-select-sm" id="type_sale" name="type_sale">
                                        <option value="">Todos</option>
                                        <option value="0" {{ request()->type_sale === '0' ? 'selected' : '' }}>Venta Directa</option>
                                        <option value="3" {{ (request()->type_sale === '3' || request()->type_sale === 'directa_descuento') ? 'selected' : '' }}>Venta Directa con Descuento</option>
                                        <option value="1" {{ request()->type_sale === '1' ? 'selected' : '' }}>Contrato</option>
                                        <option value="2" {{ request()->type_sale === '2' ? 'selected' : '' }}>Crédito</option>
                                    </select>
                                </div>
                                @if($isMaster || (!empty($users) && $users->count() > 0))
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Usuario de venta</label>
                                    <select class="form-select form-select-sm" id="user_id" name="user_id">
                                        <option value="">Todos</option>
                                        @foreach ($users as $user)
                                            @php
                                                $nombrePersona = ($user->employee && !empty(trim($user->employee->name))) 
                                                    ? trim($user->employee->name . ' ' . ($user->employee->last_name ?? '')) 
                                                    : $user->name;
                                            @endphp
                                            <option value="{{ $user->id }}"
                                                {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $nombrePersona }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-auto ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-medium px-3" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm fw-medium px-3" data-bs-toggle="modal" data-bs-target="#importSaleModal" style="border-radius: 6px;">
                                        <i class="bi bi-file-earmark-excel me-1"></i>Importar
                                    </button>
                                    <a href="{{ route('sales.historico') }}" class="btn btn-light btn-sm fw-medium px-3" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                                    </a>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="d-flex justify-content-end">
                                        <div class="bg-light px-3 py-2 rounded border">
                                            <h6 class="mb-0 text-dark">
                                                <strong>Total vendido: S/ {{ number_format($total, 2, '.', ',') }}</strong>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N° comprobante</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cliente</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Placa</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Método de pago</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($sales as $sale)
                                        <tr data-sale-id="{{ $sale->id }}" data-sale-date="{{ $sale->date->format('Y-m-d') }}">
                                            <td>{{ $sale->payments->pluck('number')->first() ?? 'N/A' }}</td>
                                            <td>
                                                @if($sale->type_sale === 3)
                                                    <span class="badge bg-info text-dark">Venta Directa con Descuento</span>
                                                @elseif($sale->type_sale === 0)
                                                    @php
                                                        $hasDiscount = $sale->sale_details->contains(function($detail) {
                                                            return !is_null($detail->discounted_price) && $detail->discounted_price > 0 && abs((float)$detail->discounted_price - (float)$detail->unit_price) > 0.009;
                                                        });
                                                    @endphp
                                                    @if($hasDiscount)
                                                        <span class="badge bg-info text-dark">Venta Directa con Descuento</span>
                                                    @else
                                                        <span class="badge bg-success">Venta Directa</span>
                                                    @endif
                                                @elseif($sale->type_sale === 1)
                                                    <span class="badge bg-primary">Contrato</span>
                                                @elseif($sale->type_sale === 2)
                                                    <span class="badge bg-warning text-dark">Crédito</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $sale->client_name ?? 'Varios' }}</td>
                                            <td>{{ $sale->vehicle_plate ?? 'N/A' }}</td>
                                            <td>{{ $sale->date->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    if (request()->payment_method_id) {
                                                        // Si hay filtro por método de pago, mostrar solo el monto de ese método
                                                        $filteredPayment = $sale->payments->where('payment_method_id', request()->payment_method_id)->where('deleted', 0)->whereNotNull('payment_method_id')->first();
                                                        $amountToShow = $filteredPayment ? $filteredPayment->amount : 0;
                                                    } else {
                                                        // Si no hay filtro, mostrar el total de la venta
                                                        $amountToShow = $sale->total;
                                                    }
                                                @endphp
                                                S/ {{ number_format($amountToShow, 2) }}
                                            </td>
                                            <td>
                                                @php
                                                    // Mostrar solo payments con método de pago (excluir pendientes sin método)
                                                    $paymentsToShow = $sale->payments->where('deleted', 0)->whereNotNull('payment_method_id');
                                                    if (request()->payment_method_id) {
                                                        $paymentsToShow = $paymentsToShow->where('payment_method_id', request()->payment_method_id);
                                                    }
                                                    $paymentMethodsDisplay = [];
                                                    foreach ($paymentsToShow as $payment) {
                                                        if ($payment->payment_method) {
                                                            $paymentMethodsDisplay[] = $payment->payment_method->name . ': S/ ' . number_format($payment->amount, 2);
                                                        }
                                                    }
                                                @endphp
                                                {!! !empty($paymentMethodsDisplay) ? implode('<br>', $paymentMethodsDisplay) : 'N/A' !!}
                                            </td>
                                            <td>{{ $sale->location->name ?? 'N/A' }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm open-details-modal" title="Detalles"
                                                    data-bs-venta_id="{{ $sale->id }}"
                                                    style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-list-task"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-info btn-sm btn-edit-date"
                                                    data-sale-id="{{ $sale->id }}"
                                                    title="Editar fecha"
                                                    style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-calendar-event"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon btn-anular-venta"
                                                    data-sale-id="{{ $sale->id }}"
                                                    title="{{ $sale->deleted ? 'Venta anulada' : 'Eliminar venta' }}"
                                                    {{ $sale->deleted ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $sales->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar detalles de productos de la venta -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="saleDetailsModalLabel">
                        <i class="bi bi-list-task me-2 text-primary"></i>Productos de la Venta #<span id="sale-number"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                            <thead class="text-center">
                                <tr>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Precio Tablero</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Precio Vendido</th>
                                    <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail-productos">
                                <tr>
                                    <td colspan="5" class="text-center">Cargando productos...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr id="total-row" style="display: none;">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th id="total-amount">S/ 0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                        <p id="adicional-row" style="display: none;">
                            <strong>Adicional:</strong> S/ <span id="adicional-amount">0.00</span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary px-4 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación de venta -->
    <div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="deleteSaleModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-3">¿Estás seguro de que deseas eliminar esta venta? Esta acción no se puede deshacer.</p>
                    <p class="mb-0 text-muted"><strong>N° Comprobante:</strong> <span id="delete-sale-number"></span></p>
                    <input type="hidden" id="delete-sale-id" value="">
                </div>
                <div class="modal-footer border-top-0 bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 fw-medium" id="confirmDeleteBtn">Eliminar Venta</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar fecha de venta -->
    <div class="modal fade" id="editSaleDateModal" tabindex="-1" aria-labelledby="editSaleDateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="editSaleDateModalLabel"><i class="bi bi-calendar-event me-2 text-primary"></i>Editar fecha de venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted"><strong>N° Comprobante:</strong> <span id="edit-sale-number"></span></p>
                    <input type="hidden" id="edit-sale-id" value="">
                    <div class="mb-3">
                        <label for="edit-sale-date" class="form-label text-dark fw-bold mb-1">Fecha</label>
                        <input type="date" class="form-control" id="edit-sale-date">
                    </div>
                    <div class="alert alert-danger d-none" id="edit-sale-date-error"></div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
                    <button type="button" class="btn btn-primary px-4 fw-medium btn-sm" id="confirmEditDateBtn"><i class="bi bi-save me-1"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para importar Excel -->
    <div class="modal fade" id="importSaleModal" tabindex="-1" aria-labelledby="importSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="formImportExcel" action="{{ route('sales.importExcel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark" id="importSaleModalLabel"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Importar Ventas de Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Aquí puedes subir el archivo Excel para importar las ventas. Asegúrate de respetar el formato de la plantilla.</p>
                        
                        <div class="mb-4 text-center">
                            <a href="{{ route('sales.template') }}" class="btn btn-outline-success btn-sm fw-medium">
                                <i class="bi bi-download"></i> Descargar Plantilla Modelo
                            </a>
                        </div>

                        <div class="mb-3">
                            <label for="excelFile" class="form-label text-dark fw-bold mb-1">Archivo Excel (.xlsx, .xls)</label>
                            <input class="form-control" type="file" id="excelFile" name="file" accept=".xlsx, .xls" required>
                        </div>
                        <div class="alert alert-danger d-none" id="import-sale-error"></div>
                        <div class="alert alert-success d-none" id="import-sale-success"></div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light">
                        <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success px-4 fw-medium btn-sm" id="btnProcesarImport"><i class="bi bi-upload me-1"></i> Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.btn-pdf', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const location_id = document.getElementById('location_id').value;
            const num_comprobante = document.getElementById('num_comprobante').value;
            const client_id = document.getElementById('client_id').value;
            const voucher_type = document.getElementById('voucher_type').value;
            const payment_method_id = document.getElementById('payment_method_id').value;
            const type_sale = document.getElementById('type_sale').value;
            const user_id = document.getElementById('user_id').value;
            // Usar la nueva ruta
            let pdfUrl = '{{ route('sales.pdf') }}';
            const params = new URLSearchParams();

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (location_id) params.append('location_id', location_id);
            if (num_comprobante) params.append('number', num_comprobante);
            if (client_id) params.append('client_id', client_id);
            if (voucher_type) params.append('voucher_type', voucher_type);
            if (payment_method_id) params.append('payment_method_id', payment_method_id);
            if (type_sale) params.append('type_sale', type_sale);
            if (user_id) params.append('user_id', user_id);

            if (params.toString()) {
                pdfUrl += '?' + params.toString();
            }

            console.log('URL generada:', pdfUrl);

            // Crear un enlace temporal para forzar la descarga
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'reporte_ventas' + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        $(document).ready(function() {
            //Para el boton de pdf

            // Funcionalidad del botón Excel
            $('#btnExcel').on('click', function() {
                // Obtener los valores del formulario
                const formData = $('#fromFilter').serialize();

                // Crear URL para descargar Excel con los filtros actuales
                const excelUrl = "{{ route('sales.excel') }}?" + formData;

                // Mostrar indicador de carga
                $(this).html('<i class="bi bi-download"></i> Descargando...').prop('disabled', true);

                // Crear un enlace temporal para descargar
                const link = document.createElement('a');
                link.href = excelUrl;
                link.download = 'ventas_historico.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Restaurar el botón después de un momento
                setTimeout(() => {
                    $(this).html('Excel').prop('disabled', false);
                }, 2000);
            });

            // Funcionalidad del formulario de filtros
            $('#fromFilter').on('submit', function(e) {
                e.preventDefault();

                // Mostrar indicador de carga
                $('#btnFiltrar').html('<i class="bi bi-search"></i> Filtrando...').prop('disabled', true);

                // Obtener datos del formulario
                const formData = $(this).serialize();

                // Redirigir con los parámetros
                window.location.href = "{{ route('sales.historico') }}?" + formData;
            });

            // Funcionalidad del botón de detalles
            $('.open-details-modal').on('click', function() {
                const saleId = $(this).data('bs-venta_id');

                // Limpiar modal y mostrar
                $('#sale-number').text(saleId);
                $('#detail-productos').html(
                    '<tr><td colspan="4" class="text-center">Cargando productos...</td></tr>');
                $('#productos-total').text('S/ 0.00');
                $('#adicional-row').hide();
                $('#adicional-amount').text('0.00');
                $('#saleDetailsModal').modal('show');
                
                console.log('saleId', saleId);
                // Cargar productos de la venta
                loadSaleProducts(saleId);
            });
        });


        // Función para cargar solo los productos de la venta
        function loadSaleProducts(saleId) {
            $.ajax({
                url: "{{ route('sales.show', ':id') }}".replace(':id', saleId),
                method: 'GET',
                data: {
                    sale_id: saleId
                },
                success: function(response) {
                    if (response.status) {
                        // Llenar tabla de productos
                        let productosHtml = '';
                        let total = 0;

                        if (response.productos && response.productos.length > 0) {
                            response.productos.forEach(function(producto) {
                                total += parseFloat(producto.subtotal);
                                const boardPrice = parseFloat(producto.board_price ?? producto.unit_price);
                                const soldPrice = parseFloat(producto.sold_price ?? producto.unit_price);
                                const priceChanged = Math.abs(soldPrice - boardPrice) > 0.009;
                                productosHtml += `
                            <tr>
                                <td>${producto.name}</td>
                                <td>${producto.quantity}</td>
                                <td>S/ ${boardPrice.toFixed(2)}</td>
                                <td class="${priceChanged ? 'text-warning fw-semibold' : ''}">S/ ${soldPrice.toFixed(2)}</td>
                                <td>S/ ${producto.subtotal.toFixed(2)}</td>
                            </tr>
                        `;
                            });
                        } else {
                            productosHtml =
                                '<tr><td colspan="5" class="text-center">No hay productos en esta venta</td></tr>';
                        }

                        $('#detail-productos').html(productosHtml);
                        $('#productos-total').text('S/ ' + total.toFixed(2));
                        
                        // Mostrar adicional si está disponible
                        const adicional = response.adicional ? parseFloat(response.adicional) : 0;
                        
                        if (adicional > 0) {
                            $('#adicional-amount').text(adicional.toFixed(2));
                            $('#adicional-row').show();
                        } else {
                            $('#adicional-row').hide();
                        }
                    } else {
                        $('#detail-productos').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error al cargar productos</td></tr>'
                        );
                        console.error('Error:', response.error);
                    }
                },
                error: function(xhr) {
                    $('#detail-productos').html(
                        '<tr><td colspan="4" class="text-center text-danger">Error al cargar productos</td></tr>'
                    );
                    console.error('Error AJAX:', xhr);
                }
            });
        }

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

        $('#search-client').on('input', function() {
            $('#client_id').val('');
        });

        // Funcionalidad del botón de eliminar venta (soft delete)
        $(document).on('click', '.btn-anular-venta', function() {
            const saleId = $(this).data('sale-id');
            const saleNumber = $(this).closest('tr').find('td:first-child').text().trim();
            $('#delete-sale-id').val(saleId);
            $('#delete-sale-number').text(saleNumber);
            $('#deleteSaleModal').modal('show');
        });

        // Confirmar eliminación desde el modal
        $('#confirmDeleteBtn').on('click', function() {
            const saleId = $('#delete-sale-id').val();

            // Deshabilitar botón y mostrar loading
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...');

            $.ajax({
                url: '{{ url("sales") }}/' + saleId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    $('#deleteSaleModal').modal('hide');
                    if (response.success) {
                        // Usar SweetAlert si está disponible, sino usar alert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Venta eliminada exitosamente.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('Venta eliminada exitosamente.');
                            location.reload();
                        }
                    } else {
                        alert(response.message || 'Error al eliminar la venta.');
                    }
                },
                error: function(xhr) {
                    $('#deleteSaleModal').modal('hide');
                    let errorMsg = 'Error al eliminar la venta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                    console.error('Error AJAX:', xhr);
                },
                complete: function() {
                    // Restaurar botón
                    $('#confirmDeleteBtn').prop('disabled', false).html('Eliminar Venta');
                }
            });
        });

        // Abrir modal para editar fecha
        $(document).on('click', '.btn-edit-date', function() {
            const saleId = $(this).data('sale-id');
            const row = $(this).closest('tr');
            const saleNumber = row.find('td:first-child').text().trim();
            const saleDate = row.data('sale-date');

            $('#edit-sale-id').val(saleId);
            $('#edit-sale-number').text(saleNumber);
            $('#edit-sale-date').val(saleDate);
            $('#edit-sale-date-error').addClass('d-none').text('');
            $('#editSaleDateModal').modal('show');
        });

        // Guardar fecha editada
        $('#confirmEditDateBtn').on('click', function() {
            const saleId = $('#edit-sale-id').val();
            const newDate = $('#edit-sale-date').val();

            $('#edit-sale-date-error').addClass('d-none').text('');
            $(this).prop('disabled', true).text('Guardando...');

            $.ajax({
                url: '{{ route('sales.updateDate', ['sale' => ':id']) }}'.replace(':id', saleId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    date: newDate
                },
                success: function(response) {
                    if (response.success) {
                        const row = $('tr[data-sale-id="' + saleId + '"]');
                        row.data('sale-date', response.date);
                        row.find('td:nth-child(5)').text(formatDateToDisplay(response.date));
                        reorderSalesTable();
                        $('#editSaleDateModal').modal('hide');
                    } else {
                        $('#edit-sale-date-error').removeClass('d-none').text(response.message || 'Error al actualizar la fecha.');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error al actualizar la fecha.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.date) {
                        errorMsg = xhr.responseJSON.errors.date[0];
                    }
                    $('#edit-sale-date-error').removeClass('d-none').text(errorMsg);
                },
                complete: function() {
                    $('#confirmEditDateBtn').prop('disabled', false).text('Guardar');
                }
            });
        });

        function formatDateToDisplay(dateString) {
            if (!dateString) return '';
            const parts = dateString.split('-');
            if (parts.length !== 3) return dateString;
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        function reorderSalesTable() {
            const tbody = $('.table tbody');
            const rows = tbody.find('tr').get();

            rows.sort(function(a, b) {
                const dateA = $(a).data('sale-date') || '';
                const dateB = $(b).data('sale-date') || '';

                if (dateA < dateB) return 1;
                if (dateA > dateB) return -1;

                const idA = parseInt($(a).data('sale-id'), 10) || 0;
                const idB = parseInt($(b).data('sale-id'), 10) || 0;
                return idB - idA;
            });

            $.each(rows, function(_, row) {
                tbody.append(row);
            });
        }

        $(document).ready(function() {
            $('#formImportExcel').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                let btn = $('#btnProcesarImport');
                
                $('#import-sale-error').addClass('d-none').html('');
                $('#import-sale-success').addClass('d-none').html('');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.status) {
                            $('#import-sale-success').removeClass('d-none').html(response.message);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else {
                            $('#import-sale-error').removeClass('d-none').html(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error al procesar el archivo.';
                        if (xhr.responseJSON) {
                            if(xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                            if(xhr.responseJSON.errors) {
                                if(Array.isArray(xhr.responseJSON.errors)) {
                                    errorMsg += '<br><ul class="mb-0 text-start"><li>' + xhr.responseJSON.errors.slice(0, 5).join('</li><li>') + '</li></ul>';
                                    if(xhr.responseJSON.errors.length > 5) errorMsg += '<small>y más errores...</small>';
                                } else {
                                    let errs = [];
                                    for(let key in xhr.responseJSON.errors) {
                                        errs.push(xhr.responseJSON.errors[key][0]);
                                    }
                                    errorMsg += '<br><ul class="mb-0 text-start"><li>' + errs.join('</li><li>') + '</li></ul>';
                                }
                            }
                        }
                        $('#import-sale-error').removeClass('d-none').html(errorMsg);
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Importar');
                    }
                });
            });
        });
    </script>
@endsection
