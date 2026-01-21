@extends('template.index')

@section('header')
<h1>Descargas</h1>
<p>Registro de descargas a tanques</p>
@endsection

@section('content')
<div class="acontainer-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <form id="formDischarge" action="{{ route('discharges.store') }}" method="POST">
                        @csrf
                        <!-- Fila 1: sede y Tanque -->
                        <div class="mb-2 row">
                            <label class="col-sm-3 col-form-label text-start">Compra:</label>
                            <div class="col-sm-7">
                                <input type="text" id="search-purchase" class="form-control" placeholder="Buscar razon social o número de comprobante">
                                <input type="hidden" id="purchase_id" name="purchase_id">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="location_id" class="col-sm-3 col-form-label text-start">Sede</label>
                            <div class="col-sm-3">
                                <select id="location_id" name="location_id" class="form-select border-dark" required>
                                    <option value="">Seleccione una sede</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="tank_id" class="col-sm-3 col-form-label text-start">Tanque</label>
                            <div class="col-sm-3">
                                <select id="tank_id" name="tank_id" class="form-select border-dark" required disabled>
                                    <option value="">Seleccione un tanque</option>
                                </select>
                            </div>
                        </div>

                        <!-- Fila 2: Producto y Cantidad -->
                        <div class="mb-3 row">
                            <label for="product_id" class="col-sm-3 col-form-label text-start">Producto</label>
                            <div class="col-sm-3">
                                <select id="product_id" name="product_id" class="form-select border-dark" required>
                                    <option value="">Seleccione un producto</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-unit="{{ $product->measurement_unit }}">
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="quantity" class="col-sm-3 col-form-label text-start">Cantidad</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control border-dark" id="quantity" name="quantity" step="0.01" min="0.01" required>
                                <small id="unitDisplay" class="text-muted"></small>
                            </div>
                        </div>
                        <!-- Botón de Guardar -->
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>

                     <!-- Tabla de Registros -->
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Compra asociada</th>
                                <th>Sede</th>
                                <th>Tanque</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($discharges as $discharge)
                                <tr>
                                    <td>{{ $discharge->purchase ? $discharge->purchase->invoice_number : '-' }}</td>
                                    <td>{{ $discharge->location->name }}</td>
                                    <td>{{ $discharge->first_detail->tank->name }}</td>
                                    <td>{{ $discharge->first_detail->product->name }}</td>
                                    <td>{{ $discharge->first_detail->quantity }}</td>
                                    <td>{{ $discharge->date->format('d/m/Y') }}</td>                                    
                                </tr>
                            @empty
                                <tr>
                                <td colspan="5" class="text-center">No hay colaboradores registrados.</td>
                                </tr>
                            @endforelse
                            </tbody>
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