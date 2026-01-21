@extends('template.index')

@section('header')
    <h1>Histórico de Contratos</h1>
    <p>Lista de contratos</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label small">Fecha Inicial</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ request()->start_date ? request()->start_date : '' }}">
                                </div>
                                <!-- Fecha final -->
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label small">Fecha Final</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ request()->end_date ? request()->end_date : '' }}">
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sede</label>
                                        <select class="form-select" id="location_id" name="location_id">
                                            <option value="">Todas las sedes</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}

                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cliente</label>
                                    <input type="text" id="search-client" class="form-control"
                                        value="{{ request()->client_name ?? '' }}">
                                    <input type="hidden" id="client_id" name="client_id"
                                        value="{{ request()->client_id ?? '' }}">
                                </div>

                                <div class="col d-flex align-items-end mb-3">
                                    <div class=" w-50s me-2">
                                        <button type="submit" class="btn btn-primary w-100"
                                            id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <button id="btnExcel" class="btn btn-success">Excel</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <button id="btnPdf" class="btn btn-danger">PDF</button>
                                    </div>
                                    <div class=" w-50s me-2">
                                        <a href="{{ route('contracts.index') }}" class="btn btn-warning w-100"
                                            id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Documento</th>
                                        <th>Cliente</th>
                                        <th>Productos</th>
                                        <th>Fecha Generación</th>
                                        <th>Total</th>
                                        <th>Sede</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->client->document }}</td>
                                            <td>{{ $contract->client->commercial_name }}</td>
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
                    <button type="button" class="btn btn-danger" id="btnEliminarcontracto">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-payments-modal />
    <script>
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
                                        label: item.business_name,
                                        value: item.business_name,
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
