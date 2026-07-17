@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-cart-check me-2 text-primary"></i>Histórico de Compras</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Compras</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar de Filtros -->
            <form action="" method="GET" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->start_date ? request()->start_date : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                        <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->end_date ? request()->end_date : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Proveedor</label>
                        <input type="text" id="search-suppliers" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request()->company_name ?? '' }}">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{ request()->supplier_id ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search-product" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Producto</label>
                        <input hidden type="number" id="product_id" name="product_id">
                        <input type="text" class="form-control form-control-sm" id="search-product" placeholder="Todos">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-primary btn-sm w-100" type="submit"><i class="bi bi-search me-1"></i>Filtrar</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-warning btn-sm w-100 text-white"><i class="bi bi-eraser-fill me-1"></i>Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle fw-medium" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 6px;">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Informes PDF
                        </button>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item btn-pdf" href="#"><i class="bi bi-file-earmark-text me-2"></i>Detalle por Proveedor</a></li>
                            <li><a class="dropdown-item btn-pdf-general" href="#"><i class="bi bi-file-earmark-ruled me-2"></i>Total por Proveedor</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item btn-producto" href="#"><i class="bi bi-file-earmark-text me-2"></i>Detalle por Producto</a></li>
                            <li><a class="dropdown-item btn-producto-todo" href="#"><i class="bi bi-file-earmark-ruled me-2"></i>Total por Producto</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success btn-sm fw-medium" type="button" id="excelBtn" style="border-radius: 6px;">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm fw-medium" data-bs-toggle="modal" data-bs-target="#createPurchaseModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Compra
                    </button>
                </div>
                <div>
                    <h5 class="mb-0 text-dark fw-bold">Total: <span class="text-primary">S/ {{ number_format($total, 2, '.', ',') }}</span></h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="invoiceHistoryTable" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N° Comprobante</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Proveedor</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Método de Pago</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total (S/)</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if ($purchases->count())
                            @foreach ($purchases as $purchase)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td class="text-dark fw-medium">{{ $purchase->invoice_number ?? '---' }}</td>
                                    <td class="text-dark">{{ $purchase->supplier->company_name ?? 'Sin proveedor' }}</td>
                                    <td class="text-dark">{{ $purchase->location->name ?? 'Sin sede' }}</td>
                                    <td class="text-dark">{{ $purchase->date->format('d/m/Y') }}</td>
                                    <td class="text-dark">{{ $purchase->payment_method->name ?? '---' }}</td>
                                    <td class="text-dark fw-bold">{{ number_format($purchase->total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $purchase->deleted == 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $purchase->deleted == 0 ? 'Activo' : 'Anulado' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button class="btn btn-sm btn-info text-white me-1 btn-show" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-id="{{ $purchase->id }}" title="Ver Detalle">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        @if($purchase->deleted == 0)
                                            <button class="btn btn-sm btn-warning text-white me-1 btn-edit" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-id="{{ $purchase->id }}" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger text-white btn-eliminar" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-id="{{ $purchase->id }}" data-bs-toggle="modal" data-bs-target="#eliminarModal" title="Anular">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">No hay compras registradas.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $purchases->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles -->
<div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="showModalLabel">Detalle de la compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="border: 1px solid #e9ecef;">
                        <thead class="text-center">
                            <tr>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Precio Unitario</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; background-color: #2c3e50 !important; color: white !important;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-items" class="text-center"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar compra -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold">Editar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <!-- Fila 1: N° Comprobante y Proveedor -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editInvoiceNumber" class="form-label text-dark fw-bold">N° Comprobante</label>
                            <input type="text" class="form-control" id="editInvoiceNumber" name="invoice_number">
                        </div>
                        <div class="col-md-6">
                            <label for="editSupplier" class="form-label text-dark fw-bold">Proveedor</label>
                            <select class="form-select" id="editSupplier" name="supplier_id">
                                <option value="">Seleccionar proveedor</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Fila 2: Fecha, Método de Pago y Total -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="editDate" class="form-label text-dark fw-bold">Fecha</label>
                            <input type="date" class="form-control" id="editDate" name="date" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editPaymentMethod" class="form-label text-dark fw-bold">Método de Pago</label>
                            <select class="form-select" id="editPaymentMethod" name="payment_method_id" required>
                                <option value="">Seleccionar método de pago</option>
                                @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editTotal" class="form-label text-dark fw-bold">Total</label>
                            <input type="number" class="form-control" id="editTotal" name="total" step="0.01" disabled>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="eliminarModalLabel">Confirmar Anulación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">¿Estás seguro de que deseas anular esta compra?</p>
            </div>
            <div class="modal-footer">
                <form id="formEliminar" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Anular Compra</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('purchases.partials.create_modal')

<div id="global-spinner" class="d-flex justify-content-center align-items-center spinner-hidden"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 1000;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<style>
    .spinner-hidden {
        display: none !important;
    }

    .spinner-visible {
        display: flex !important;
    }
</style>

<!-- Script para manejar la solicitud AJAX -->


@endsection

@section('scripts')
@include('purchases.partials.create_scripts')

<script>
    let supplierSearchTimeout = null;
    $('#search-suppliers').autocomplete({
        source: function(request, response) {
            clearTimeout(supplierSearchTimeout);
            supplierSearchTimeout = setTimeout(function() {
                let currentTerm = $('#search-suppliers').val();
                // Solo buscar si hay al menos una letra
                if (currentTerm && currentTerm.length > 0) {
                    $.ajax({
                        url: "{{ route('suppliers.search') }}",
                        method: 'GET',
                        data: {
                            query: currentTerm
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.company_name,
                                    value: item.company_name,
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
            $('#supplier_id').val(ui.item.id);
        },
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
            .appendTo(ul);
    };

    var products = @json($products);
    $('#search-product').autocomplete({
        source: function(request, response) {
            var results = [];
            if (products && products.length) {
                for (var i = 0; i < products.length; i++) {
                    var product = products[i];
                    if (product && product.name &&
                        product.name.toLowerCase().indexOf(request.term.toLowerCase()) !== -1) {
                        results.push({
                            label: product.name,
                            value: product.name,
                            id: product.id
                        });
                    }
                }
            }
            response(results.slice(0, 15));
        },
        select: function(event, ui) {
            if (ui.item && ui.item.id) {
                $('#product_id').val(ui.item.id);
            }
        },
        change: function(event, ui) {
            if (!ui.item) {
                $('#product_id').val('');
            }
        }
    });

    // Limpiar cuando se borra el texto
    $('#search-product').on('input', function() {
        if ($(this).val() === '') {
            $('#product_id').val('');
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.getElementById('global-spinner');
        const editForm = document.getElementById('editForm');

    });

    document.addEventListener('DOMContentLoaded', function() {
        const eliminarModal = document.getElementById('eliminarModal');
        eliminarModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            // Ruta al controlador que actualiza el estado
            const form = document.getElementById('formEliminar');
            form.action = '{{ route("purchases.destroy", ":id") }}'.replace(':id', id);
        });
    });

    $(document).ready(function() {
        // Cuando se hace clic en el botón "Ver Detalle"
        $('.btn-show').click(function() {

            var id = $(this).data('id');
            $('#tbl-items').html('');

            $.ajax({
                url: '{{ route("purchases.show", "") }}/' + id,
                method: 'GET',
                success: function(data) {
                    var html = '';

                    // Construir las filas de la tabla con los detalles
                    // Nota: en compras de "Otros Gastos" la línea no tiene producto de
                    // catálogo (product_id nulo), por eso se usa display_name en vez de
                    // product.name.
                    data.details.forEach(function(detail) {
                        html += `
                            <tr>
                                <td>${detail.display_name}</td>
                                <td>${detail.quantity} ${detail.measurement_unit || ''}</td>
                                <td>${detail.unit_price}</td>
                                <td>${detail.subtotal}</td>
                            </tr>
                        `;
                    });

                    // Insertar las filas en la tabla
                    $('#tbl-items').html(html);

                    // Mostrar el modal
                    $('#showModal').modal('show');
                },
                error: function(xhr) {
                    ToastError.fire({
                        text: "Error al cargar detalles"
                    });
                    console.error('Error al cargar los detalles:', xhr.responseText);
                }
            });
        });
    });

    // Manejar el clic en el botón "Editar"
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');

        $('#editForm').data('id', id);

        // Construimos la URL dinámicamente usando la ruta Laravel
        var url = '{{ route("purchases.edit", ":id") }}'.replace(':id', id);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                const registro = data.registro;

                $('#editInvoiceNumber').val(registro.invoice_number);
                $('#editSupplier').val(registro.supplier_id);
                $('#editDate').val(registro.date.split('T')[0]);
                $('#editTotal').val(registro.total);
                $('#editPaymentMethod').val(registro.payment_method_id);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                ToastError.fire({
                    text: 'No se pudo cargar los datos'
                });
            }
        });

        $('#editModal').modal('show');
    });

    // Manejar el envío del formulario de edición
    $('#editForm').submit(function(e) {
        e.preventDefault();

        var id = $(this).data('id');

        var url = '{{ route("purchases.update", ":id") }}'.replace(':id', id);

        var token = $('input[name="_token"]').val();

        var formData = {
            invoice_number: $('#editInvoiceNumber').val(),
            supplier_id: $('#editSupplier').val(),
            date: $('#editDate').val(),
            payment_method_id: $('#editPaymentMethod').val(),
            _token: token,
            _method: 'PUT'
        };

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                ToastMessage.fire({
                    icon: 'success',
                    text: 'Registro actualizado correctamente.'
                });
                location.reload();
            },
            error: function(xhr) {
                ToastError.fire({
                    text: 'No se pudo actualizar el registro'
                });
            }
        });
    });

    $(document).on('click', '.btn-pdf', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const supplierId = document.getElementById('supplier_id').value;

        // Usar la nueva ruta
        let pdfUrl = '{{ route("purchases.pdf") }}';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (supplierId) params.append('supplier_id', supplierId);

        if (params.toString()) {
            pdfUrl += '?' + params.toString();
        }

        console.log('URL generada:', pdfUrl);

        // Crear un enlace temporal para forzar la descarga
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = 'reporte_compras' + '.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $(document).on('click', '.btn-pdf-general', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const supplierId = document.getElementById('supplier_id').value;

        // Usar la nueva ruta
        let pdfUrl = '{{ route("purchases.pdfGeneral") }}';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (supplierId) params.append('supplier_id', supplierId);
        if (params.toString()) {
            pdfUrl += '?' + params.toString();
        }

        console.log('URL generada:', pdfUrl);

        // Crear un enlace temporal para forzar la descarga
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = 'reporte_compras_general' + '.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Alternativa: abrir en nueva ventana
        // window.open(pdfUrl, '_blank');
    });

    $(document).on('click', '.btn-producto', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const supplierId = document.getElementById('supplier_id').value;
        const productId = document.getElementById('product_id').value;

        // Validar que hay un producto seleccionado
        if (!productId) {
            ToastError.fire({
                text: 'Seleccione un Producto a filtrar'
            });
            return;
        }

        let pdfUrl = '{{ route("purchases.pdfProduct") }}';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (supplierId) params.append('supplier_id', supplierId);
        params.append('product_id', productId);

        if (params.toString()) {
            pdfUrl += '?' + params.toString();
        }

        const productName = document.getElementById('search-product').value || 'producto';
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = `reporte_${productName.toLowerCase().replace(/\s+/g, '_')}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });


    $(document).on('click', '.btn-producto-todo', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const supplierId = document.getElementById('supplier_id').value;

        let pdfUrl = '{{ route("purchases.pdfAllProducts") }}';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (supplierId) params.append('supplier_id', supplierId);

        if (params.toString()) {
            pdfUrl += '?' + params.toString();
        }

        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = 'reporte_todos_los_productos_compras.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // NUEVO: Botón PDF Todos los Productos (agrupados por producto)
    document.getElementById('excelBtn').addEventListener('click', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        let excelUrl = '{{ route("purchases.excel") }}';
        const params = new URLSearchParams();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        if (params.toString()) {
            excelUrl += '?' + params.toString();
        }

        const link = document.createElement('a');
        link.href = excelUrl;
        link.download = 'reporte_compras.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endsection
