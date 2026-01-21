@extends('template.index')

@section('header')
    <h1>Histórico de Pagos</h1>
    <p>Lista de ventas</p>
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
                                        <input type="date" id="start_date" class="form-control" name="start_date"
                                            value="{{ request()->start_date ? request()->start_date : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha final</label>
                                        <input type="date" id="end_date" class="form-control" name="end_date"
                                            value="{{ request()->end_date ? request()->end_date : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">N° Comprobante</label>
                                    <input type="number" name="number" id="num_comprobante" class="form-control"
                                        value="{{ request()->number ? request()->number : '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="hidden" id="client_id" name="client_id"
                                        value="{{ request()->client_id ?? '' }}">
                                    <input type="text" id="search-client" class="form-control"
                                        value="{{ request()->client_name ?? '' }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Comprobante</label>
                                    <select name="voucher_type" id="voucher_types" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="Boleta" {{ request('voucher_type') == 'Boleta' ? 'selected' : '' }}>
                                            Boleta</option>
                                        <option value="Factura"
                                            {{ request('voucher_type') == 'Factura' ? 'selected' : '' }}>Factura</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Método de Pago</label>
                                    <select name="payment_method_id" id="payment_method_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($payment_methods as $method)
                                            <option value="{{ $method->id }}"
                                                {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col d-flex align-items-end">
                                    <div class=" w-50s me-2">
                                        <button type="submit" class="btn btn-primary w-100"
                                            id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <button type="button" class="btn btn-danger w-100 btn-pdf">PDF</button>
                                    </div>
                                    <div class=" w-50s me-2">
                                        <button type="button" class="btn btn-success w-100" id="btnExcel">Excel</button>
                                    </div>
                                    <div class=" w-50s me-2">
                                        <a href="{{ route('payments.index') }}" class="btn btn-warning w-100"
                                            id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                                <!-- <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <h5>
                                                <strong>Total vendido: S/ 0.00</strong>
                                            </h5>
                                            <h6>
                                                Total pagado: S/ 0.00
                                            </h6>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </form>
                    </div>


                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° comprobante</th>
                                        <th>Tipo</th>
                                        <th>Método de pago</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
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
