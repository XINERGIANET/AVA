@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-droplet-half me-2 text-primary"></i>Descargas</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Descargas</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Header Toolbar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 text-dark fw-bold">Registro de Descargas</h5>
                <button type="button" class="btn btn-primary fw-medium px-4" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                    <i class="bi bi-plus-lg me-2"></i>Nueva Descarga
                </button>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Compra Asociada</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tanque</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($discharges as $discharge)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td class="text-dark fw-medium">{{ $discharge->purchase ? $discharge->purchase->invoice_number : '-' }}</td>
                                <td class="text-dark">{{ $discharge->location->name }}</td>
                                <td class="text-dark">{{ $discharge->first_detail->tank->name }}</td>
                                <td class="text-dark">{{ $discharge->first_detail->product->name }}</td>
                                <td class="text-dark fw-bold">{{ $discharge->first_detail->quantity }}</td>
                                <td class="text-dark">{{ $discharge->date->format('d/m/Y') }}</td>                                    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay descargas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Descarga -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createModalLabel">Registrar Descarga a Tanque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formDischarge" action="{{ route('discharges.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <!-- Compra -->
                    <div class="mb-4">
                        <label class="form-label text-dark fw-bold mb-1">Compra Asociada:</label>
                        <input type="text" id="search-purchase" class="form-control" placeholder="Buscar razón social o número de comprobante">
                        <input type="hidden" id="purchase_id" name="purchase_id">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="location_id" class="form-label text-dark fw-bold mb-1">Sede</label>
                            <select id="location_id" name="location_id" class="form-select" required>
                                <option value="">Seleccione una sede</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tank_id" class="form-label text-dark fw-bold mb-1">Tanque</label>
                            <select id="tank_id" name="tank_id" class="form-select" required disabled>
                                <option value="">Seleccione un tanque</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label text-dark fw-bold mb-1">Producto</label>
                            <select id="product_id" name="product_id" class="form-select" required>
                                <option value="">Seleccione un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-unit="{{ $product->measurement_unit }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label text-dark fw-bold mb-1">Cantidad</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                                <span class="input-group-text" id="unitDisplay">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Descarga</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Convertir datos PHP a JSON accesible en JS
        const tanks = @json($tanks);

        // Mostrar unidad de medida
        $('#product_id').change(function() {
            const unit = $(this).find('option:selected').data('unit');
            $('#unitDisplay').text(unit || '');
        });

        // Cargar tanques cuando se selecciona location
        $('#location_id').change(function() {
            const locationId = $(this).val();
            const tankSelect = $('#tank_id');

            tankSelect.empty().append('<option value="">Seleccione tanque</option>');

            if (locationId && tanks[locationId]) {
                tankSelect.prop('disabled', false);
                tanks[locationId].forEach(tank => {
                    tankSelect.append(
                        `<option value="${tank.id}">${tank.name}</option>`
                    );
                });
            } else {
                tankSelect.prop('disabled', true);
            }
        });

        // Cargar producto cuando se selecciona tanque
        $('#tank_id').change(function() {
            const tankId = $(this).val();
            const productSelect = $('#product_id');

            // Buscar el tanque seleccionado en el array tanks
            let selectedTank = null;
            Object.values(tanks).forEach(tankList => {
                tankList.forEach(tank => {
                    if (String(tank.id) === String(tankId)) {
                        selectedTank = tank;
                    }
                });
            });

            if (selectedTank && selectedTank.product_id) {
                productSelect.val(selectedTank.product_id).trigger('change');
            } else {
                productSelect.val('').trigger('change');
            }
        });

        // Validar formulario
        // $('#formDischarge').submit(function(e) {
        //     e.preventDefault();
        //     const submitBtn = $(this).find('button[type="submit"]');
        //     submitBtn.prop('disabled', true).html('Procesando...');

        //     $.ajax({
        //         url: $(this).attr('action'),
        //         method: 'POST',
        //         data: $(this).serialize(),
        //         success: function(response) {
        //             alert('Distribución registrada exitosamente');
        //             location.reload();
        //         },
        //         error: function(xhr) {
        //             let errorMsg = 'Error al registrar';
        //             if (xhr.responseJSON?.errors) {
        //                 errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
        //             }
        //             alert(errorMsg);
        //         },
        //         complete: function() {
        //             submitBtn.prop('disabled', false).html('Registrar Distribución');
        //         }
        //     });
        // });
    });

    let purchaseSearchTimeout = null;

    $('#search-purchase').autocomplete({
        source: function(request, response) {
            clearTimeout(purchaseSearchTimeout);
            purchaseSearchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route('purchases.search') }}',
                    method: 'get',
                    data: {
                        query: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                id: item.id,
                            };
                        }));
                    }
                });
            }, 300); // 300 ms de espera después de dejar de tipear
        },
        appendTo: '.container-fluid',
        select: function(event, ui) {
            $('#purchase_id').val(ui.item.id);
        },
    }).autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
            .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
            .appendTo(ul);
    };
</script>
@endsection