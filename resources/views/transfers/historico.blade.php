@extends('template.index')
@section('header')
    <h1>Historico Distribución</h1>
    <p>Registro de distribución</p>
@endsection
@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <!--Card body-->
                    <div class="card-body">
                        <div class="row">
                            <form id="fromFilter">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="from_date" class="form-label small">Fecha Inicial</label>
                                        <input id="from_date" type="date" class="form-control" value="{{ request()->from_date ?? '' }}"
                                            name="from_date">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to_date" class="form-label small">Fecha Final</label>
                                        <input type="date" class="form-control" value="{{ request()->to_date ?? '' }}"
                                            name="to_date" id="to_date">
                                    </div>
                                    <!--Filtros de sede y tanque origen-->
                                    <div class="col-md-2">
                                        <label for="from_location_id" class="form-label small">Sede origen</label>
                                        <select class="form-control" name="from_location_id" id="from_location_id">
                                            <option value="" disabled selected>
                                                Seleccione una Sede
                                            </option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->from_location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!--Filtros de sede y tanque destino-->
                                    <div class="col-md-2">
                                        <label for="from_tank_id" class="form-label small">Tanque origen</label>
                                        <select class="form-control" name="from_tank_id" id="from_tank_id">
                                            <option value="">
                                                Seleccione un tanque
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to_location_id" class="form-label small">Sede destino</label>
                                        <select class="form-control" name="to_location_id" id="to_location_id">
                                            <option value="" disabled selected>
                                                Seleccione una Sede
                                            </option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->to_location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to_tank_id" class="form-label small">Tanque destino</label>
                                        <select class="form-control" name="to_tank_id" id="to_tank_id">
                                            <option value="" disabled>
                                                Seleccione un tanque
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!--Botones-->
                                <div class="row mt-3">
                                    <div class="col d-flex align-items-end mb-3">
                                        <div class="w-50s me-2">
                                            <button type="submit" id="btnFiltrar" class="btn btn-primary">Filtrar</button>
                                        </div>
                                        <div class="w-50s me-2">
                                            <button id="btnExcel" class="btn btn-success">Excel</button>
                                        </div>
                                        <div class="w-50s me-2">
                                            <button id="btnPdf" class="btn btn-danger">PDF</button>
                                        </div>
                                        <div class="w-50 me-2">
                                            <a href="{{ route('transfers.historico') }}" class="btn btn-warning"
                                                id="btnLimpiar">Limpiar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Desde</th>
                                        <th>Hacia</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                        <th>Recibido</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($distribuciones as $distribucion)
                                        <tr>
                                            <td>{{ $distribucion->from_tank->name }} -
                                                {{ $distribucion->from_tank->location->name }}</td>
                                            <td>{{ $distribucion->to_tank->name }} -
                                                {{ $distribucion->to_tank->location->name }}</td>
                                            <td>{{ $distribucion->product->name }}</td>
                                            <td>{{ $distribucion->quantity }}</td>
                                            <td>{{ $distribucion->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($distribucion->received)
                                                    <span class="badge bg-success">Sí</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm open-details-modal"
                                                    title="Detalles"
                                                    style="--bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                    <i class="bi bi-list-task"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#btnExcel').on('click', function() {
                const formData = $('#fromFilter').serialize();

                // Crear URL para descargar Excel con los filtros actuales
                const excelUrl = "{{ route('transfers.excel') }}?" + formData;

                // Mostrar indicador de carga
                $(this).html('<i class="bi bi-download"></i> Descargando...').prop('disabled', true);

                // Crear un enlace temporal para descargar
                const link = document.createElement('a');
                link.href = excelUrl;
                link.download = 'transfers_historico.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Restaurar el botón después de un momento
                setTimeout(() => {
                    $(this).html('Excel').prop('disabled', false);
                }, 2000);
            });

            $('#btnPdf').on('click', function() {
                const from_date = document.getElementById('from_date').value;
                const to_date = document.getElementById('to_date').value;
                const from_location_id = document.getElementById('from_location_id').value;
                const from_tank_id = document.getElementById('from_tank_id').value;
                const to_location_id = document.getElementById('from_tank_id').value;
                const to_tank_id = document.getElementById('to_tank_id').value;

                let pdfUrl = '{{ route('transfers.pdf') }}';
                const params = new URLSearchParams();

                if (from_date) params.append('from_date', from_date);
                if (to_date) params.append('to_date', to_date);
                if (from_location_id) params.append('from_location_id', from_location_id);
                if (from_tank_id) params.append('from_tank_id', from_tank_id);
                if (to_location_id) params.append('to_location_id', to_location_id);
                if (to_tank_id) params.append('to_tank_id', to_tank_id);
                
                if (params.toString()) {
                    pdfUrl += '?' + params.toString();
                }

                console.log('URL generada:', pdfUrl);

                // Crear un enlace temporal para forzar la descarga
                const link = document.createElement('a');
                link.href = pdfUrl;
                link.download = 'reporte_transferencia' + '.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            $('#fromFilter').on('submit', function(e) {
                e.preventDefault();

                // Mostrar indicador de carga
                $('#btnFiltrar').html('<i class="bi bi-search"></i> Filtrando...').prop('disabled', true);

                // Obtener datos del formulario
                const formData = $(this).serialize();

                // Redirigir con los parámetros
                window.location.href = "{{ route('transfers.historico') }}?" + formData;
            });
            document.getElementById('from_tank_id').addEventListener('change', function() {

                const toLocationSelect = document.getElementById('to_location');
                if (toLocationSelect.value) {
                    toLocationSelect.dispatchEvent(new Event('change')); //recarga opciones de
                }
            });
            const tanksByLocation = @json($tanksByLocation);
            //Para los origenes
            // Traemos los valores seleccionados desde Blade
            const selectedFromTank = "{{ request()->from_tank_id ?? '' }}";
            const selectedToTank = "{{ request()->to_tank_id ?? '' }}";

            // Para el origen
            $('#from_location_id').on('change', function() {
                const locationId = $(this).val();
                const $tankSelect = $('#from_tank_id');
                $tankSelect.empty();
                $tankSelect.append('<option value="" disabled selected>Seleccione un tanque</option>');

                if (locationId && tanksByLocation[locationId]) {
                    tanksByLocation[locationId].forEach(function(tank) {
                        $tankSelect.append(
                            '<option value="' + tank.id + '"' +
                            (tank.id == selectedFromTank ? ' selected' : '') +
                            '>' + tank.name + '</option>'
                        );
                    });
                }
            });

            // Para el destino
            $('#to_location_id').on('change', function() {
                const locationId = $(this).val();
                const $tankSelect = $('#to_tank_id');
                $tankSelect.empty();
                $tankSelect.append('<option value="" disabled selected>Seleccione un tanque</option>');

                if (locationId && tanksByLocation[locationId]) {
                    tanksByLocation[locationId].forEach(function(tank) {
                        $tankSelect.append(
                            '<option value="' + tank.id + '"' +
                            (tank.id == selectedToTank ? ' selected' : '') +
                            '>' + tank.name + '</option>'
                        );
                    });
                }
            });

            // Disparar eventos al cargar la página
            $('#from_location_id').trigger('change');
            $('#to_location_id').trigger('change');
        });
    </script>
@endsection
