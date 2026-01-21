@extends('template.index')

@section('header')
<h1>Distribución</h1>
<p>Registro de transferencias de sede a sede</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form id="createTransferForm" class="row mb-5" action="{{ route('transfers.store') }}" method="POST">
                        @csrf
                        <div class="col-md-6 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="from_location" class="form-label mb-0">Sede de origen</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="from_location" required>
                                        <option value="" disabled selected>
                                            Seleccione una sede
                                        </option>
                                        @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 invisible" id="from_tank_container">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="from_tank_id" class="form-label mb-0">Tanque de origen</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="from_tank_id" name="from_tank_id" required>
                                        <option value="" disabled selected>
                                            Seleccione un tanque
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="to_location" class="form-label mb-0">Sede de destino</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="to_location" required>
                                        <option value="" disabled selected>
                                            Seleccione una sede
                                        </option>
                                        @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 invisible" id="to_tank_container">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="to_tank_id" class="form-label mb-0">Tanque de destino</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="to_tank_id" name="to_tank_id" required>
                                        <option value="" disabled selected>
                                            Seleccione un tanque de origen
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="quantity" class="form-label mb-0">Cantidad</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" id="quantity" name="quantity" required placeholder="Ingrese una cantidad">
                                </div>
                            </div>
                        </div>


                        <!-- Botón de Guardar (alineado a la derecha) -->
                        <div class="row mb-3">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Desde</th>
                                    <th>Hacia</th>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Recibido</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                <tr class="storage-row" data-location="{{ $transfer->location_id }}">
                                    <td>{{ $transfer->from_tank->name }} - {{ $transfer->from_tank->location->name }}</td>
                                    <td>{{ $transfer->to_tank->name }} - {{ $transfer->to_tank->location->name }}</td>
                                    <td>{{ $transfer->product->name }}</td>
                                    <td>{{ $transfer->product->measurement_unit }}</td>
                                    <td>{{ number_format($transfer->quantity, 2) }}</td>
                                    <td>{{ $transfer->date->format('d/m/y') }}</td>
                                    <td>{{ $transfer->recieved==0 ? 'No' : 'Si' }}</td>
                                    <td>
                                        @if ($transfer->recieved == 0)
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmTransferModal"
                                            data-id="{{ $transfer->id }}" title="Confirmar recepción">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTransferModal"
                                            data-id="{{ $transfer->id }}"
                                            data-recieved="{{ $transfer->recieved }}" title="Eliminar distribución">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $transfers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Eliminar -->
<div class="modal fade" id="deleteTransferModal" tabindex="-1" aria-labelledby="deleteTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteTransferForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar distribución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta distribución?</p>
                    <p id="delete-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar -->
<div class="modal fade" id="confirmTransferModal" tabindex="-1" aria-labelledby="confirmTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="confirmTransferForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar distribución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas confirmar esta distribución?</p>
                    <p>Se sumará el stock al tanque de recepción</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    const tanksByLocation = @json($tanksByLocation);

    document.addEventListener('DOMContentLoaded', function() {
        
        var deleteModal = document.getElementById('deleteTransferModal');
        deleteModal.addEventListener('show.bs.modal', function(event){
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var recieved = button.getAttribute('data-recieved');
            var message = document.getElementById('delete-message');
            if(recieved == '1') {
                message.textContent = 'Al estar confirmada, esto revertirá el stock en ambos tanques'
            }else{
                message.textContent = 'Al no estar confirmada, esto revertirá el stock solo en el tanque de origen'
            }
            
            document.getElementById('deleteTransferForm').setAttribute('action', `{{ url('transfers') }}/${id}`);

        });

        var confirmModal = document.getElementById('confirmTransferModal');
        confirmModal.addEventListener('show.bs.modal', function(event){
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            
            document.getElementById('confirmTransferForm').setAttribute('action', `{{ url('transfers') }}/${id}`);

        });


        document.getElementById('from_location').addEventListener('change', function() {
            const locationId = this.value;
            const tankSelect = document.getElementById('from_tank_id');
            tankSelect.innerHTML = '<option value="" disabled selected>Seleccione un tanque</option>';
            
            
            if(tanksByLocation[locationId]) {
                tanksByLocation[locationId].forEach(function(tank) {
                    const option = document.createElement('option');
                    option.value = tank.id;
                    option.textContent = tank.name + ' (' + tank.stored_quantity + ')';
                    option.setAttribute('data-product-id', tank.product_id);
                    tankSelect.appendChild(option);
                });
            }

            document.getElementById('from_tank_container').classList.remove('invisible');
        });

        document.getElementById('from_tank_id').addEventListener('change', function() {
            
            const toLocationSelect = document.getElementById('to_location');
            if (toLocationSelect.value){
                toLocationSelect.dispatchEvent(new Event('change')); //recarga opciones de
            }
        });

        document.getElementById('to_location').addEventListener('change', function() {
            const locationId = this.value;
            const tankSelect = document.getElementById('to_tank_id');
            tankSelect.innerHTML = '<option value="" disabled selected>Seleccione un tanque</option>';

            const fromTankSelect = document.getElementById('from_tank_id');
            const fromTankOption = fromTankSelect.options[fromTankSelect.selectedIndex];
            const fromProductId = fromTankOption ? fromTankOption.getAttribute('data-product-id') : null;
            const fromTankId = fromTankOption ? fromTankOption.value : null;

            if(tanksByLocation[locationId] && fromProductId) {
                tanksByLocation[locationId].forEach(function(tank) {
                    if (String(tank.product_id) === String(fromProductId) && String(tank.id) !== String(fromTankId)) {
                        const option = document.createElement('option');
                        option.value = tank.id;
                        option.textContent = tank.name + ' (' + tank.stored_quantity + ')';
                        tankSelect.appendChild(option);
                    }
                });
            }

            document.getElementById('to_tank_container').classList.remove('invisible');
        });

        document.getElementById('quantity').addEventListener('input', function() {
            const fromTankSelect = document.getElementById('from_tank_id');
            const selectedOption = fromTankSelect.options[fromTankSelect.selectedIndex];
            const maxStock = selectedOption ? parseFloat(selectedOption.textContent.match(/\(([\d.,]+)\)/)[1].replace(',', '')) : null;
            const quantityInput = this;

            if (maxStock !== null && quantityInput.value) {
                if (parseFloat(quantityInput.value) > maxStock) {
                    quantityInput.setCustomValidity('La cantidad no puede ser mayor al stock del tanque de origen (' + maxStock + ')');
                } else {
                    quantityInput.setCustomValidity('');
                }
            } else {
                quantityInput.setCustomValidity('');
            }
        });



    });
</script>
@endsection