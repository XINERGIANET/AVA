@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-wallet2 me-2 text-primary"></i>Histórico de Pagos
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Clientes y Crédito</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Pagos</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body">
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
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">N° Comprobante</label>
                                    <input type="number" name="number" id="num_comprobante" class="form-control form-control-sm" value="{{ request()->number ? request()->number : '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Cliente</label>
                                    <input type="hidden" id="client_id" name="client_id" value="{{ request()->client_id ?? '' }}">
                                    <input type="text" id="search-client" class="form-control form-control-sm" value="{{ request()->client_name ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Tipo de Comprobante</label>
                                    <select name="voucher_type" id="voucher_types" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        <option value="Boleta" {{ request('voucher_type') == 'Boleta' ? 'selected' : '' }}>Boleta</option>
                                        <option value="Factura" {{ request('voucher_type') == 'Factura' ? 'selected' : '' }}>Factura</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Método de Pago</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-select form-select-sm">
                                        <option value="">Todos</option>
                                        @foreach ($payment_methods as $method)
                                            <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 text-end d-flex justify-content-end gap-1 mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-medium" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium" id="btnLimpiar" style="border-radius: 6px;">
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
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N° comprobante</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Método de pago</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cliente</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Total</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->number ?? 'N/A' }}</td>
                                            <td>{{ $payment->voucher_type }}</td>
                                            <td>{{ $payment->payment_method ? $payment->payment_method->name : 'N/A' }}</td>
                                            <td>{{ $payment->client ? $payment->client->business_name : $payment->client_name ?? 'varios' }}
                                            </td>
                                            <td>{{ $payment->date->format('d/m/Y') }}</td>
                                            <td>{{ $payment->amount }}</td>
                                            <td>{{ $payment->deleted == 0 ? 'Activo' : 'Anulado' }}</td>
                                            <td>
                                                @if ($payment->photo_url)
                                                    <a href="{{ asset('/public/storage/' . $payment->photo_url) }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    class="btn btn-primary btn-sm btn-icon me-1"
                                                    title="Ver foto">
                                                        <i class="bi bi-image"></i>
                                                    </a>
                                                @endif
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon btn-anular-pago"
                                                    data-payment-id="{{ $payment->id }}"
                                                    title="{{ $payment->deleted == 1 ? 'Pago anulada' : 'Eliminar pago' }}"
                                                    {{ $payment->deleted == 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $payments->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.btn-pdf', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const num_comprobante = document.getElementById('num_comprobante').value;
            const client_id = document.getElementById('client_id').value;
            const voucher_type = document.getElementById('voucher_types').value;
            const payment_method_id = document.getElementById('payment_method_id').value;

            let pdfUrl = '{{ route('payments.pdf') }}';
            const params = new URLSearchParams();

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (num_comprobante) params.append('number', num_comprobante);
            if (client_id) params.append('client_id', client_id);
            if (voucher_type) params.append('voucher_type', voucher_type);
            if (payment_method_id) params.append('payment_method_id', payment_method_id);

            if (params.toString()) {
                pdfUrl += '?' + params.toString();
            }

            console.log('URL generada:', pdfUrl);

            // Crear un enlace temporal para forzar la descarga
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'reporte_pagos' + '.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        $(document).on('click', '.btn-anular-pago', function() {
            const payment_id = $(this).data('payment-id');

            Swal.fire({
                title: '¿Anular pago?',
                text: "Esta acción cambiará el estado del pago a ANULADO.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular',
                cancelButtonText: 'Cancelar',
                customClass: {
                    title: 'text-dark',
                    htmlContainer: 'text-dark',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

                    $.ajax({
                        url: "{{ route('payments.destroy', ':id') }}".replace(':id', payment_id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            ToastMessage.fire({
                                text: data.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(response) {
                            ToastError.fire({
                                text: 'Ocurrió un error al anular pago'
                            })
                        }
                    });
                }
            });
        });

        document.getElementById('btnExcel').addEventListener('click', function() {
            const form = document.getElementById('fromFilter');
            const formData = new FormData(form);

            // Construir la query string con todos los campos del formulario
            const params = new URLSearchParams(formData).toString();

            // Ruta a la que quieres enviar los datos (ajusta según tu ruta)
            const url = '{{ route('payments.excel') }}' + '?' + params;

            // Redirigir para descargar el Excel (GET)
            window.open(url, '_blank');

        });

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
    </script>
    <style>
        .swal-confirm-btn {
            background-color: #dc3545 !important;
            /* rojo Bootstrap */
            color: #fff !important;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            margin-right: 10px;
            font-weight: 500;
        }

        .swal-cancel-btn {
            background-color: #6c757d !important;
            /* gris Bootstrap */
            color: #fff !important;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
        }
    </style>
@endsection
