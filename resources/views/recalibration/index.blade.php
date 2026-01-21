@extends('template.index')

@section('header')
    <h1>Histórico de Recalibraciones</h1>
    <p>Lista de recalibraciones</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha inicial</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request()->start_date ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha final</label>
                                        <input type="date" id="end_date" class="form-control" name="end_date"
                                            value="{{ request()->end_date ?? '' }}">
                                    </div>
                                </div>

                                @if($isMaster)
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sede</label>
                                        <select class="form-select" id="location_id" name="location_id">
                                            <option value="">Todos</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                @if($isMaster || !empty($users) && $users->count() > 0)
                                <div class="col-md-3">
                                    <label class="form-label">Usuario de recalibración</label>
                                    <select class="form-select" id="user_id" name="user_id">
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

                                <div class="col d-flex align-items-end mt-3">
                                    <div class=" w-50s me-2">
                                        <button type="submit" class="btn btn-primary w-100"
                                            id="btnFiltrar">Filtrar</button>
                                    </div>
                                    {{-- <div class="w-50s me-2">
                                        <button type="button" class="btn btn-danger btn-pdf w-100">
                                            PDF
                                        </button>
                                    </div>
                                    <div class=" w-50s me-2">
                                        <button type="button" class="btn btn-success w-100" id="btnExcel">Excel</button>
                                    </div> --}}
                                    <div class=" w-50s me-2">
                                        <a href="{{ route('sales.historico') }}" class="btn btn-warning w-100"
                                            id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <h5>
                                                <strong>Total de recalibración: S/ {{ number_format($total, 2, '.', ',') }}</strong>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                        <th>Sede</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                                    style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-list-task"></i>
                                                </button>
                                                <button type="button" style="display: none;"
                                                    class="btn btn-danger btn-sm btn-icon btn-anular-venta"
                                                    data-sale-id="{{ $sale->id }}"
                                                    title="{{ $sale->estado === 1 ? 'Venta anulada' : 'Eliminar venta' }}"
                                                    {{ $sale->estado === 1 ? 'disabled' : '' }}>
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
            <div class="modal-content">
                <form id="formEditarCantidad" method="POST">
                    @csrf
                    <input type="hidden" name="sale_id" id="modal-sale-id" value="">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="saleDetailsModalLabel">Productos de la Venta #<span
                                id="sale-number"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-productos">
                                    <tr>
                                        <td colspan="4" class="text-center">Cargando productos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
