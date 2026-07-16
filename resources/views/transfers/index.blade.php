@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Distribución</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Distribución (Traspaso de Tanques)</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar de Filtros y Acciones -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('transfers.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request('end_date') }}">
                            </div>
                            @if(auth()->user()->role->nombre == 'master')
                            <div class="col-md-3">
                                <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                <select name="location_id" id="location_id" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    @foreach(\App\Models\Location::all() as $loc)
                                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-secondary btn-sm w-100 fw-medium"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                                <a href="{{ route('transfers.index') }}" class="btn btn-light btn-sm text-muted fw-medium w-100"><i class="bi bi-eraser me-1"></i>Limpiar</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-primary fw-medium px-4 btn-sm w-100" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-2"></i>Nueva Transferencia
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Desde</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Hacia</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Unidad</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Recibido</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($transfers as $transfer)
                        <tr class="storage-row" data-location="{{ $transfer->location_id }}" style="border-bottom: 1px solid #e9ecef;">
                            <td class="text-dark">
                                <span class="fw-medium">{{ $transfer->from_tank->name }}</span><br>
                                <small class="text-muted">{{ $transfer->from_tank->location->name }}</small>
                            </td>
                            <td class="text-dark">
                                <span class="fw-medium">{{ $transfer->to_tank->name }}</span><br>
                                <small class="text-muted">{{ $transfer->to_tank->location->name }}</small>
                            </td>
                            <td class="text-dark">{{ $transfer->product->name }}</td>
                            <td class="text-dark">{{ $transfer->product->measurement_unit }}</td>
                            <td class="text-dark fw-bold">{{ number_format($transfer->quantity, 2) }}</td>
                            <td class="text-dark">{{ $transfer->date->format('d/m/y') }}</td>
                            <td>
                                @if($transfer->recieved == 1)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                @if ($transfer->recieved == 0)
                                <button class="btn btn-sm btn-success text-white fw-medium me-1" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#confirmTransferModal"
                                    data-id="{{ $transfer->id }}" title="Confirmar recepción">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-danger text-white fw-medium" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteTransferModal"
                                    data-id="{{ $transfer->id }}"
                                    data-recieved="{{ $transfer->recieved }}" title="Eliminar distribución">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No hay transferencias registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $transfers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Transferencia -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createModalLabel">Registrar Transferencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createTransferForm" action="{{ route('transfers.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <!-- Origen -->
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary fw-bold mb-3"><i class="bi bi-box-arrow-up me-2"></i>Origen</h6>
                            <div class="mb-3">
                                <label for="from_location" class="form-label text-dark fw-bold mb-1">Sede de origen</label>
                                <select class="form-select" id="from_location" required>
                                    <option value="" disabled selected>Seleccione una sede</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 invisible" id="from_tank_container">
                                <label for="from_tank_id" class="form-label text-dark fw-bold mb-1">Tanque de origen</label>
                                <select class="form-select" id="from_tank_id" name="from_tank_id" required>
                                    <option value="" disabled selected>Seleccione un tanque</option>
                                </select>
                            </div>
                        </div>

                        <!-- Destino -->
                        <div class="col-md-6">
                            <h6 class="text-primary fw-bold mb-3"><i class="bi bi-box-arrow-down me-2"></i>Destino</h6>
                            <div class="mb-3">
                                <label for="to_location" class="form-label text-dark fw-bold mb-1">Sede de destino</label>
                                <select class="form-select" id="to_location" required>
                                    <option value="" disabled selected>Seleccione una sede</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 invisible" id="to_tank_container">
                                <label for="to_tank_id" class="form-label text-dark fw-bold mb-1">Tanque de destino</label>
                                <select class="form-select" id="to_tank_id" name="to_tank_id" required>
                                    <option value="" disabled selected>Seleccione un tanque de origen primero</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 border-top pt-3">
                        <div class="col-md-6 offset-md-3">
                            <label for="quantity" class="form-label text-dark fw-bold mb-1">Cantidad a Transferir</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required placeholder="Ingrese una cantidad" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Transferencia</button>
                </div>
            </form>
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
                    <h5 class="modal-title text-dark fw-bold" id="deleteTransferModalLabel">Eliminar distribución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">¿Estás seguro de que deseas eliminar esta distribución?</p>
                    <p id="delete-message" class="text-muted small"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Eliminar</button>
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
                    <h5 class="modal-title text-dark fw-bold" id="confirmTransferModalLabel">Confirmar distribución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">¿Estás seguro de que deseas confirmar esta distribución?</p>
                    <div class="alert alert-info border-0 shadow-sm py-2 px-3 mt-2 mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>Se sumará el stock al tanque de recepción
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">Confirmar</button>
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