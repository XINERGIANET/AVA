@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-rulers me-2 text-primary"></i>Mediciones de Sede
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none text-muted">Ventas y Caja</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Mediciones de Sede</li>
        </ol>
    </nav>
@endsection

@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">

                {{-- FORMULARIO DE REGISTRO --}}
                {{-- <form id="createMeasurementForm" class="mb-5" action="{{ route('measurements.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sede</label>
                            <select class="form-control" id="location_id" name="location_id" required>
                                <option value="">-- Seleccione una sede --</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Surtidor</label>
                            <select class="form-control" id="pump_id" name="pump_id">
                                <option value="">-- Seleccione un surtidor --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Inicial</label>
                            <input type="number" step="0.001" class="form-control" placeholder="Ingrese galones iniciales"
                                id="amount_initial" name="amount_initial" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Final</label>
                            <input type="number" step="0.001" class="form-control" placeholder="Ingrese galones finales"
                                id="amount_final" name="amount_final" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Teórico</label>
                            <input type="number" step="0.001" class="form-control" placeholder="Ingrese galones teóricos"
                                id="amount_theorical" name="amount_theorical" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diferencia (Gal)</label>
                            <input type="number" step="0.001" class="form-control" placeholder="Diferencia (calculada)"
                                id="amount_difference" name="amount_difference" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" required
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" placeholder="Ingrese descripción (opcional)"
                                id="description" name="description">
                        </div>

                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Guardar medición</button>
                    </div>

                </form> --}}

                {{-- TABLA DE REGISTROS --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                        <thead class="text-center">
                            <tr>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Surtidor</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Galones Inicial</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Galones Final</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Galones Teórico</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Diferencia (Gal)</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                            {{-- Iterar sobre mediciones --}}
                            @foreach ($measurements as $measurement)
                                <tr>
                                    <td>{{ ($measurements->currentPage() - 1) * $measurements->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $measurement->location->name ?? 'Sin sede' }}</td>
                                    <td>{{ $measurement->pump->name ?? '-' }}</td>
                                    <td>{{ number_format($measurement->amount_initial, 3) }} Gal</td>
                                    <td>{{ number_format($measurement->amount_final, 3) }} Gal</td>
                                    <td>{{ number_format($measurement->amount_theorical, 3) }} Gal</td>
                                    <td class="{{ $measurement->amount_difference < 0 ? 'text-danger' : ($measurement->amount_difference > 0 ? 'text-success' : '') }}">
                                        {{ number_format($measurement->amount_difference, 3) }} Gal
                                    </td>
                                    <td>{{ $measurement->date ? $measurement->date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $measurement->description ?? '-' }}</td>

                                    <td>
                                        {{-- Botón editar --}}
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $measurement->id }}"
                                            data-location="{{ $measurement->location_id }}"
                                            data-pump="{{ $measurement->pump_id }}"
                                            data-initial="{{ $measurement->amount_initial }}"
                                            data-final="{{ $measurement->amount_final }}"
                                            data-theorical="{{ $measurement->amount_theorical }}"
                                            data-difference="{{ $measurement->amount_difference }}"
                                            data-date="{{ $measurement->date ? $measurement->date->format('Y-m-d') : '' }}"
                                            data-description="{{ $measurement->description }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Botón eliminar --}}
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $measurement->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    {{-- Paginación --}}
                    {{ $measurements->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editMeasurementForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Medición</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sede</label>
                            <select name="location_id" id="edit_location_id" class="form-control" required>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Surtidor</label>
                            <select name="pump_id" id="edit_pump_id" class="form-control">
                                <option value="">-- Seleccione un surtidor --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Inicial</label>
                            <input type="number" step="0.001" class="form-control" id="edit_amount_initial"
                                name="amount_initial" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Final</label>
                            <input type="number" step="0.001" class="form-control" id="edit_amount_final"
                                name="amount_final" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Galones Teórico</label>
                            <input type="number" step="0.001" class="form-control" id="edit_amount_theorical"
                                name="amount_theorical" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diferencia (Gal)</label>
                            <input type="number" step="0.001" class="form-control" id="edit_amount_difference"
                                name="amount_difference" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="edit_description" name="description">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="deleteMeasurementForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar medición</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar esta medición?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // Calcular diferencia automáticamente
            function calculateDifference(prefix = '') {
                const initial = parseFloat($(`#${prefix}amount_initial`).val()) || 0;
                const final = parseFloat($(`#${prefix}amount_final`).val()) || 0;
                const theorical = parseFloat($(`#${prefix}amount_theorical`).val()) || 0;
                
                const difference = (final - initial) - theorical;
                $(`#${prefix}amount_difference`).val(difference.toFixed(3));
            }

            // Para formulario de creación
            $('#amount_initial, #amount_final, #amount_theorical').on('input', function() {
                calculateDifference();
            });

            // Para formulario de edición
            $('#edit_amount_initial, #edit_amount_final, #edit_amount_theorical').on('input', function() {
                calculateDifference('edit_');
            });

            // EDITAR
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const location_id = button.data('location');
                const pump_id = button.data('pump');
                const initial = button.data('initial');
                const final = button.data('final');
                const theorical = button.data('theorical');
                const difference = button.data('difference');
                const date = button.data('date');
                const description = button.data('description');

                $('#editMeasurementForm').attr('action', `{{ url('measurements') }}/${id}`);
                $('#edit_location_id').val(location_id);
                $('#edit_pump_id').val(pump_id);
                $('#edit_amount_initial').val(initial);
                $('#edit_amount_final').val(final);
                $('#edit_amount_theorical').val(theorical);
                $('#edit_amount_difference').val(difference);
                $('#edit_date').val(date);
                $('#edit_description').val(description);
            });

            // ELIMINAR
            $('#deleteModal').on('show.bs.modal', function(event) {
                const id = $(event.relatedTarget).data('id');
                $('#deleteMeasurementForm').attr('action', `{{ url('measurements') }}/${id}`);
            });

            // Spinner
            $('form').on('submit', function() {
                $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
            });

        });
    </script>
@endsection