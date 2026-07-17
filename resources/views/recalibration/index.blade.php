@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-gear-fill me-2 text-primary"></i>Histórico de Recalibraciones
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Control y Soporte</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Recalibración</li>
        </ol>
    </nav>
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

                                @if($isMaster || !empty($users) && $users->count() > 0)
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Usuario</label>
                                    <select class="form-select form-select-sm" id="user_id" name="user_id">
                                        <option value="">Todos</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-auto ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-medium px-3" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('recalibration.index') }}" class="btn btn-light btn-sm fw-medium px-3" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                                    </a>
                                </div>
                                
                                <div class="col-12 mt-3 pt-3 border-top">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="mb-0 text-muted fw-semibold">Total de recalibración:</h6>
                                            <h5 class="mb-0 fw-bold text-primary">S/ {{ number_format($total, 2, '.', ',') }}</h5>
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
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->date->format('d/m/Y') }}</td>
                                            <td>{{ number_format($sale->sale_details->sum('quantity'), 3) }}</td>
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
                                            <td>{{ $sale->location->name ?? 'N/A' }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm open-details-modal" title="Detalles"
                                                    data-bs-venta_id="{{ $sale->id }}"
                                                    style="border-radius: 6px;">
                                                    <i class="bi bi-list-task"></i>
                                                </button>
                                                <button type="button" style="display: none;"
                                                    class="btn btn-danger btn-sm btn-icon btn-anular-venta"
                                                    data-sale-id="{{ $sale->id }}"
                                                    title="{{ $sale->estado === 1 ? 'Venta anulada' : 'Eliminar venta' }}"
                                                    {{ $sale->estado === 1 ? 'disabled' : '' }} style="border-radius: 6px;">
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

    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <form id="formEditarCantidad" method="POST">
                    @csrf
                    <input type="hidden" name="sale_id" id="modal-sale-id" value="">
                    
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark" id="saleDetailsModalLabel"><i class="bi bi-box me-2 text-primary"></i>Productos de la Venta #<span
                                id="sale-number"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Precio Unitario</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-productos" class="text-center">
                                    <tr>
                                        <td colspan="4" class="text-center">Cargando productos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 bg-light">
                        <button type="button" class="btn btn-secondary px-3 btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Cerrar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-medium btn-sm">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Funcionalidad del formulario de filtros
            $('#fromFilter').on('submit', function(e) {
                e.preventDefault();
                $('#btnFiltrar').html('<i class="bi bi-search"></i> Filtrando...').prop('disabled', true);
                const formData = $(this).serialize();
                window.location.href = "{{ route('recalibration.index') }}?" + formData;
            });

            // Funcionalidad del botón de detalles
            $('.open-details-modal').on('click', function() {
                const saleId = $(this).data('bs-venta_id');

                // Limpiar modal y mostrar
                $('#sale-number').text(saleId);
                $('#modal-sale-id').val(saleId); // IMPORTANTE: Guardar el sale_id en el input hidden
                $('#detail-productos').html(
                    '<tr><td colspan="4" class="text-center">Cargando productos...</td></tr>');
                $('#total-amount').text('S/ 0.00');
                $('#adicional-row').hide();
                $('#adicional-amount').text('0.00');
                $('#saleDetailsModal').modal('show');
                
                console.log('saleId', saleId);
                // Cargar productos de la venta
                loadSaleProducts(saleId);
            });

            // Manejo del envío del formulario de edición
            $('#formEditarCantidad').on('submit', function(e) {
                e.preventDefault();

                var submitBtn = $(this).find('button[type="submit"]');
                var originalHtml = submitBtn.html();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('recalibration.updateQuantities') }}",
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#saleDetailsModal').modal('hide');
                            
                            // Usar SweetAlert si está disponible, sino usar alert
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
                                alert('¡Éxito! ' + response.message);
                                location.reload();
                            }
                        } else {
                            alert('Error: ' + response.message);
                            submitBtn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Ocurrió un error al guardar.';
                        if(xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                        console.error(xhr);
                        submitBtn.prop('disabled', false).html(originalHtml);
                    }
                });
            });
        });

        // Función para cargar los productos de la venta
        function loadSaleProducts(saleId) {
            $.ajax({
                url: "{{ route('recalibration.show', ':id') }}".replace(':id', saleId),
                method: 'GET',
                data: {
                    sale_id: saleId
                },
                success: function(response) {
                    if (response.status) {
                        let productosHtml = '';
                        // let total = 0; // No calcular total, usar el del response

                        if (response.productos && response.productos.length > 0) {
                            response.productos.forEach(function(producto) {
                                // total += parseFloat(producto.subtotal); // No sumar
                                productosHtml += `
                                    <tr>
                                        <td>${producto.name}</td>
                                        <td>
                                            <input type="number" 
                                                class="form-control form-control-sm" 
                                                name="quantities[${producto.product_id}]" 
                                                value="${producto.quantity}" 
                                                step="0.001" 
                                                min="0">
                                        </td>
                                        <td>S/ ${parseFloat(producto.unit_price).toFixed(2)}</td>
                                        <td>S/ ${parseFloat(producto.subtotal).toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            productosHtml = '<tr><td colspan="4" class="text-center">No hay productos en esta venta</td></tr>';
                        }

                        $('#detail-productos').html(productosHtml);
                        $('#total-amount').text('S/ ' + response.total); // Usar el total fijo del response
                        $('#total-row').show();
                        
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
    </script>
@endsection
