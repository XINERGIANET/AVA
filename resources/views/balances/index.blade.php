@extends('template.index')

@section('header')
<h1>Mediciones</h1>
<p>Registro de mediciones diarias</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title w-100">
                        <div class="mb-3 row">
                            <label for="location_id" class="col-sm-3 col-form-label text-start">Sede</label>
                            <div class="col-sm-3">
                                <select id="location_id" name="location_id" class="form-select border-dark">
                                    <option value="">Seleccione una sede</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="date" class="col-sm-3 col-form-label text-start">Fecha</label>
                            <div class="col-sm-3">
                                <input type="date" name="date" id="date" class="form-control border-dark">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Tanque</th>
                                    <th scope="col">Medición Inicial</th>
                                    <th scope="col">Compras</th>
                                    <th scope="col">Medición Final</th>
                                    <th scope="col">Cantidad Vendida</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMediciones">

                            </tbody>
                        </table>
                    </div>
                </div> 

                <div class="d-flex justify-content-end p-4">
                    <button class="btn btn-primary" id="btn-save">Guardar</button>
                </div>
                
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

    const locations = @json($locations);

    $(document).ready(function() {

        $('#location_id, #date').change(function() {
            const date = $('#date').val();
            const locationId = $('#location_id').val();

            if(date !== '' && locationId !== ''){ //llama a la función cuando ambos campos estén llenos
                $.ajax({
                    url: "{{ route('measurements.get') }}",
                    method: 'GET',
                    data:{
                        date: date,
                        location_id: locationId
                    },
                    success: function(data){
                        renderEmptyTable(locationId);
                        fillTable(data);
                    },
                    error: function(data){
                        ToastError.fire({
                            text: 'Error al obtener mediciones'
                        });
                    }
                })
            }
        });


    });

    function renderEmptyTable(locationId){
        const tablaMediciones = $('#tablaMediciones');

        tablaMediciones.empty();

        // Buscar el objeto location correspondiente
        const location = locations.find(l => l.id == locationId);

        location.tanks.forEach(tank => {
            tablaMediciones.append(
                `<tr>
                    <td>
                        ${tank.name}
                    </td>
                    <td>
                        <input type="number"
                            class="form-control cantidad-input"
                            data-tank-id="${tank.id}"
                            data-value="initial_measurement"
                            min="0.001"
                            step="0.001"
                            placeholder="0.000">
                    </td>
                    <td>
                        <input type="number"
                            class="form-control cantidad-input"
                            data-tank-id="${tank.id}"
                            data-value="purchased_quantity"
                            min="0.001"
                            step="0.001"
                            placeholder="0.000">
                    </td>
                    <td>
                        <input type="number"
                            class="form-control cantidad-input"
                            data-tank-id="${tank.id}"
                            data-value="final_measurement"
                            min="0.001"
                            step="0.001"
                            placeholder="0.000">
                    </td>
                    <td>
                        <input type="number"
                            class="form-control"
                            data-tank-id="${tank.id}"
                            data-value="sold_quantity"
                            min="0.001"
                            step="0.001"
                            placeholder="0.000">
                    </td>
                </tr>`
            );
        });
        
    }

    function fillTable(measurements) {
        measurements.forEach(m => {
            ['initial_measurement', 'final_measurement', 'purchased_quantity', 'sold_quantity'].forEach(key => {
                // llena con bd
                $(`input[data-tank-id="${m.tank_id}"][data-value="${key}"]`).val(m[key] ?? '');
            });

            // Para que recalcule cant vendida
            $(`input[data-tank-id="${m.tank_id}"][data-value="final_measurement"]`).trigger('input');
        });
    }

    $(document).on('input', '.cantidad-input', function() {
        const tankId = $(this).data('tank-id');

        const initial = parseFloat($(`input[data-tank-id="${tankId}"][data-value="initial_measurement"]`).val()) || 0;
        const purchased = parseFloat($(`input[data-tank-id="${tankId}"][data-value="purchased_quantity"]`).val()) || 0;
        const final = parseFloat($(`input[data-tank-id="${tankId}"][data-value="final_measurement"]`).val()) || 0;

        const sold = initial + purchased - final;

        $(`input[data-tank-id="${tankId}"][data-value="sold_quantity"]`).val(
            (isNaN(sold) ? '' : sold.toFixed(3))
        );
    });

    $('#btn-save').on('click', function(e) {
        e.preventDefault();

        // Ejemplo de datos a enviar
        const date = $('#date').val();
        const locationId = $('#location_id').val();
        let measurements = [];

        // Recorre cada fila de la tabla y arma el array de mediciones
        $('#tablaMediciones tr').each(function() {
            const tankId = $(this).find('input[data-value="initial_measurement"]').data('tank-id');
            if (tankId) {
                measurements.push({
                    tank_id: tankId,
                    initial_measurement: $(this).find('input[data-value="initial_measurement"]').val(),
                    purchased_quantity: $(this).find('input[data-value="purchased_quantity"]').val(),
                    final_measurement: $(this).find('input[data-value="final_measurement"]').val(),
                    sold_quantity: $(this).find('input[data-value="sold_quantity"]').val()
                });
            }
        });

        console.log(date, location_id, measurements);

        $.ajax({
            url: "{{ route('measurements.store') }}",
            method: 'POST',
            data: {
                date: date,
                location_id: locationId,
                measurements: measurements,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Maneja la respuesta aquí
                ToastMessage.fire({
                    text: response.message || 'Guardado correctamente'
                });
            },
            error: function(xhr) {
                ToastError.fire({
                    text: 'Error al guardar mediciones'
                });
            }
        });
    });

</script>

@endsection