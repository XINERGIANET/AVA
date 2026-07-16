@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Distribución (Semaforo)</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Distribución</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner mt-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <!-- Toolbar de Filtros y Acciones -->
                        <div class="col-md-12 mb-4">
                            <form action="{{ route('transfers.index') }}" method="GET" id="filterForm">
                                <div class="row g-2 align-items-end justify-content-between">
                                    <div class="col-auto d-flex gap-2 align-items-end">
                                        <div>
                                            <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                            <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                        </div>
                                        <div>
                                            <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                            <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request('end_date') }}">
                                        </div>
                                        @if(auth()->user()->role->nombre == 'master')
                                        <div>
                                            <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                            <select name="location_id" id="location_id" class="form-select form-select-sm" style="min-width: 150px;">
                                                <option value="">Todas</option>
                                                @foreach(\App\Models\Location::all() as $loc)
                                                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="d-flex gap-2 ms-2">
                                            <button type="submit" class="btn btn-secondary btn-sm fw-medium px-3"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                                            <a href="{{ route('transfers.index') }}" class="btn btn-light btn-sm text-muted fw-medium px-3"><i class="bi bi-eraser me-1"></i>Limpiar</a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary fw-medium px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#createTransferModal" style="border-radius: 6px;">
                                            <i class="bi bi-plus-lg me-1"></i>Nueva Transferencia
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <!-- Modal Nueva Transferencia -->
                    <div class="modal fade" id="createTransferModal" tabindex="-1" aria-labelledby="createTransferModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="createTransferModalLabel">Registrar Transferencia</h5>
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
                                                        <option value="" selected>Seleccione una sede</option>
                                                        @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Tanks grid for origen -->
                                                <div id="from_tanks_grid" class="mt-3 d-none"></div>
                                                <input type="hidden" id="from_tank_id" name="from_tank_id" required>
                                            </div>

                                            <!-- Destino -->
                                            <div class="col-md-6">
                                                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-box-arrow-down me-2"></i>Destino</h6>
                                                <div class="mb-3">
                                                    <label for="to_location" class="form-label text-dark fw-bold mb-1">Sede de destino</label>
                                                    <select class="form-select" id="to_location" required>
                                                        <option value="" selected>Seleccione una sede</option>
                                                        @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Tanks grid for destino -->
                                                <div id="to_tanks_grid" class="mt-3 d-none"></div>
                                                <input type="hidden" id="to_tank_id" name="to_tank_id" required>
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
                                        <button type="submit" class="btn btn-primary px-4 fw-medium"><i class="bi bi-save me-1"></i>Guardar Transferencia</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- rest of view (table, modals, etc.) remain unchanged -->
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
                                    <th class="fw-bold text-uppercase text-center pe-4" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important; width: 10%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($transfers as $transfer)
                                <tr class="storage-row" data-location="{{ $transfer->location_id }}">
                                    <td>{{ $transfer->from_tank->name }} - {{ $transfer->from_tank->location->name }}</td>
                                    <td>{{ $transfer->to_tank->name }} - {{ $transfer->to_tank->location->name }}</td>
                                    <td>{{ $transfer->product->name }}</td>
                                    <td>{{ $transfer->product->measurement_unit }}</td>
                                    <td>{{ number_format($transfer->quantity, 2) }}</td>
                                    <td>{{ $transfer->date->format('d/m/y') }}</td>
                                    <td>
                                        @if ($transfer->recieved == 0)
                                        <!-- <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmTransferModal"
                                            data-id="{{ $transfer->id }}" title="Confirmar recepción">
                                            <i class="bi bi-check-lg"></i>
                                        </button> -->
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
@endsection
<!-- existing modals (deleteConfirm/confirm) remain the same -->

@section('styles')
<style>
/* simple tank card + fill visualization */
.tanks-grid {
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}
.tank-card {
    width: 120px;
    cursor: pointer;
    border:1px solid #e6e6e6;
    border-radius:8px;
    padding:8px;
    text-align:center;
    background:#fff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: transform .12s, box-shadow .12s;
}
.tank-card.selected {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(-4px);
    border-color: #2b8a3e;
}
.tank-icon {
    height:48px;
    margin-bottom:6px;
    position:relative;
    background:#f5f5f5;
    border-radius:6px;
    overflow:hidden;
}
.tank-fill {
    position:absolute;
    left:0;
    bottom:0;
    width:100%;
    height:0%;
    transition: height .4s ease;
}
.tank-label {
    font-size:13px;
    font-weight:600;
}
.tank-sub {
    font-size:12px;
    color:#666;
}
.tank-status {
    height:8px;
    border-radius:4px;
    margin-top:6px;
    overflow:hidden;
}
.tank-status .bar {
    height:100%;
    transition: width .3s linear;
}
</style>
@endsection

@section('scripts')
<script>
const tanksByLocation = @json($tanksByLocation);

// util: compute color class by percent
function getColorByPercent(p) {
    if (p >= 70) return '#28a745'; // green
    if (p >= 30) return '#ffc107'; // yellow
    return '#dc3545'; // red
}

function renderTankGrid(side, locationId, productFilter = null, excludeTankId = null) {
    const grid = document.getElementById(side + '_tanks_grid');
    grid.classList.add('d-none');
    grid.innerHTML = '';

    if (!locationId || !tanksByLocation[locationId]) {
        return;
    }

    const tanks = tanksByLocation[locationId];
    if (!tanks || tanks.length === 0) {
        grid.innerHTML = '<div class="text-muted">No hay tanques en esta sede</div>';
        grid.classList.remove('d-none');
        return;
    }

    const container = document.createElement('div');
    container.className = 'tanks-grid';

    tanks.forEach(tank => {
        if (excludeTankId && String(tank.id) === String(excludeTankId)) return;
        if (productFilter && String(tank.product_id) !== String(productFilter)) return;

        // capacity fallback
        const capacity = tank.capacity ?? tank.max_capacity ?? 100;
        const stored = parseFloat(tank.stored_quantity ?? 0);
        const percent = Math.min(100, Math.round((stored / (capacity || 1)) * 100));

        // si es reserva (is_reserve === 1) pintar azul independientemente del nivel
        const isReserve = (String(tank.is_reserve) === '1' || tank.is_reserve === 1 || tank.is_reserve === true);
        const color = isReserve ? '#007bff' : getColorByPercent(percent);

        const card = document.createElement('div');
        card.className = 'tank-card';
        card.setAttribute('data-tank-id', tank.id);
        card.setAttribute('data-product-id', tank.product_id);
        card.setAttribute('data-stored', stored);
        card.setAttribute('data-capacity', capacity);

        const icon = document.createElement('div');
        icon.className = 'tank-icon';
        // fill overlay for visual inside icon
        const fill = document.createElement('div');
        fill.className = 'tank-fill';
        fill.style.height = percent + '%';
        fill.style.background = color;
        icon.appendChild(fill);

        const label = document.createElement('div');
        label.className = 'tank-label';
        label.textContent = tank.name;

        const sub = document.createElement('div');
        sub.className = 'tank-sub';
        sub.textContent = stored.toFixed(2) + ' / ' + capacity;

        const status = document.createElement('div');
        status.className = 'tank-status';
        const bar = document.createElement('div');
        bar.className = 'bar';
        bar.style.width = percent + '%';
        bar.style.background = color;
        status.appendChild(bar);

        card.appendChild(icon);
        card.appendChild(label);
        card.appendChild(sub);
        card.appendChild(status);

        // click handler: select this tank for the side
        card.addEventListener('click', function() {
            // deselect others
            grid.querySelectorAll('.tank-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById(side + '_tank_id').value = tank.id;

            // if selecting from: when chosen, re-render to_tanks to only show same product and exclude same tank
            if (side === 'from') {
                const productId = tank.product_id;
                const toLocation = document.getElementById('to_location').value;
                if (toLocation) renderTankGrid('to', toLocation, productId, tank.id);
            }
        });

        container.appendChild(card);
    });

    grid.appendChild(container);
    grid.classList.remove('d-none');
}

document.addEventListener('DOMContentLoaded', function() {
    // location change handlers
    document.getElementById('from_location').addEventListener('change', function() {
        const locId = this.value;
        // reset selected from tank input
        document.getElementById('from_tank_id').value = '';
        renderTankGrid('from', locId);
        // clear to tank selection & grid (will be filtered when to_location change)
        document.getElementById('to_tank_id').value = '';
        document.getElementById('to_tanks_grid').innerHTML = '';
        document.getElementById('to_tanks_grid').classList.add('d-none');
    });

    document.getElementById('to_location').addEventListener('change', function() {
        const locId = this.value;
        // if from tank selected, filter by that product and exclude same tank
        const fromTankId = document.getElementById('from_tank_id').value;
        const fromProduct = fromTankId ? document.querySelector('#from_tanks_grid .tank-card.selected')?.getAttribute('data-product-id') : null;
        // reset selected to tank input
        document.getElementById('to_tank_id').value = '';
        renderTankGrid('to', locId, fromProduct, fromTankId || null);
    });

    // quantity validation uses selected from tank stored value
    document.getElementById('quantity').addEventListener('input', function() {
        const fromSelected = document.querySelector('#from_tanks_grid .tank-card.selected');
        if (!fromSelected) {
            this.setCustomValidity('Seleccione un tanque de origen primero.');
            return;
        }
        const maxStock = parseFloat(fromSelected.getAttribute('data-stored') || '0');
        if (this.value && parseFloat(this.value) > maxStock) {
            this.setCustomValidity('La cantidad no puede ser mayor al stock del tanque de origen (' + maxStock + ')');
        } else {
            this.setCustomValidity('');
        }
    });

    var deleteModal = document.getElementById('deleteTransferModal');
    deleteModal.addEventListener('show.bs.modal', function(event){
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var recieved = button.getAttribute('data-recieved');
        var message = document.getElementById('delete-message');
        if(recieved == '1') {
            message.textContent = 'Esto revertirá el stock en ambos tanques'
        }else{
            message.textContent = 'Al no estar confirmada, esto revertirá el stock solo en el tanque de origen'
        }
        
        document.getElementById('deleteTransferForm').setAttribute('action', `{{ url('transfers') }}/${id}`);

    });

    // if page loads with preselected values (optional), render them
    const preFromLoc = document.getElementById('from_location').value;
    if (preFromLoc) renderTankGrid('from', preFromLoc);

    const preToLoc = document.getElementById('to_location').value;
    if (preToLoc) renderTankGrid('to', preToLoc);
});
</script>
@endsection