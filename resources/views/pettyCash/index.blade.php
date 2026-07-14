@extends('template.index')

@section('header')
<div class="d-flex align-items-center mt-2 mb-4">
    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:64px;height:64px">
        <i class="bi bi-cash-stack fs-1 text-white"></i>
    </div>
    <div><h2 class="mb-1 fw-bold text-white">Caja chica</h2><p class="mb-0 text-white-50">Aperturas, movimientos y cierres por isla</p></div>
</div>
@endsection

@section('content')
<div class="container-fluid content-inner mt-0 py-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div><h5 class="fw-bold mb-1">Control de cajas</h5><span class="text-muted">Estado actual por isla</span></div>
                @if(auth()->user()->role->nombre !== 'worker')
                    <a href="{{ route('cashClose.index') }}" class="btn btn-outline-primary"><i class="bi bi-clock-history me-1"></i>Historial</a>
                @endif
            </div>
            <div class="row g-3">
                @forelse($isles as $isle)
                    @php($openCash = $openCashCloses->get($isle->id))
                    <div class="col-12 col-xl-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $isle->name }}</h5>
                                    @if($openCash)
                                        <div class="text-muted">Abierta por {{ $openCash->user->name ?? $openCash->user->email ?? 'Usuario' }} · {{ $openCash->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-muted fw-semibold mt-1">Base: S/ {{ number_format((float)$openCash->initial_cash_amount, 2) }}</div>
                                    @else
                                        <div class="text-muted">Sin turno de caja activo</div>
                                    @endif
                                </div>
                                <span class="badge {{ $openCash ? 'bg-success' : 'bg-secondary' }} align-self-start">● {{ $openCash ? 'Abierta' : 'Cerrada' }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                @if(!$openCash && auth()->user()->role->nombre !== 'worker')
                                    <button class="btn btn-primary js-cash-action" data-action="open" data-isle="{{ $isle->id }}" data-name="{{ $isle->name }}" data-location="{{ $isle->location_id }}"><i class="bi bi-unlock me-1"></i>Abrir caja</button>
                                @elseif($openCash)
                                    <button class="btn btn-outline-danger js-cash-action" data-action="expense" data-isle="{{ $isle->id }}" data-name="{{ $isle->name }}" data-location="{{ $isle->location_id }}"><i class="bi bi-box-arrow-right me-1"></i>Egreso</button>
                                    <button class="btn btn-outline-info js-cash-action" data-action="vault" data-isle="{{ $isle->id }}" data-name="{{ $isle->name }}" data-location="{{ $isle->location_id }}"><i class="bi bi-safe me-1"></i>Enviar a bóveda</button>
                                    @if(auth()->user()->role->nombre !== 'worker')
                                        <button class="btn btn-outline-secondary js-cash-action" data-action="close" data-isle="{{ $isle->id }}" data-name="{{ $isle->name }}" data-location="{{ $isle->location_id }}"><i class="bi bi-lock me-1"></i>Cerrar caja</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-warning mb-0">No hay islas configuradas para esta sede.</div></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cashOperationModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="cashModalTitle"></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" id="cashAction"><input type="hidden" id="cashIsle"><input type="hidden" id="cashCloseId">
        <div class="alert alert-light border py-2">Isla: <strong id="cashIsleName"></strong></div>
        <div id="closeSummary" class="d-none mb-3">
            <div class="row g-2 small">
                <div class="col-6">Monto inicial<br><strong>S/ <span id="sumInitial">0.00</span></strong></div>
                <div class="col-6">Ventas efectivo<br><strong>S/ <span id="sumSales">0.00</span></strong></div>
                <div class="col-6">Egresos<br><strong>S/ <span id="sumExpenses">0.00</span></strong></div>
                <div class="col-6">Saldo sistema<br><strong>S/ <span id="sumCalculated">0.00</span></strong></div>
            </div>
        </div>
        <div class="mb-3 d-none" id="vaultLocationGroup">
            <label class="form-label">Bóveda destino</label>
            <select class="form-select" id="cashVaultLocation">
                <option value="">Seleccione una bóveda</option>
                @foreach($vaultLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3"><label class="form-label" id="cashAmountLabel">Monto</label><input type="number" min="0.01" step="0.01" class="form-control" id="cashAmount"></div>
        <div class="mb-3 d-none" id="descriptionGroup"><label class="form-label">Descripción</label><input type="text" maxlength="255" class="form-control" id="cashDescription"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary" id="saveCashOperation">Guardar</button></div>
</div></div></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('cashOperationModal'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const urls = {
        open: @json(route('cash_closes.store')),
        detail: @json(url('cash_closes')),
        expense: @json(route('expenses.store')),
        vault: @json(route('vault.from_cash_close'))
    };
    const labels = {open: ['Abrir caja', 'Monto inicial'], expense: ['Registrar egreso', 'Monto del egreso'], vault: ['Enviar a bóveda', 'Monto a enviar'], close: ['Cerrar caja', 'Monto contado']};

    document.querySelectorAll('.js-cash-action').forEach(btn => btn.addEventListener('click', async () => {
        const action = btn.dataset.action;
        document.getElementById('cashAction').value = action;
        document.getElementById('cashIsle').value = btn.dataset.isle;
        document.getElementById('cashIsleName').textContent = btn.dataset.name;
        document.getElementById('cashModalTitle').textContent = labels[action][0];
        document.getElementById('cashAmountLabel').textContent = labels[action][1];
        document.getElementById('cashAmount').value = '';
        document.getElementById('cashDescription').value = '';
        document.getElementById('cashVaultLocation').value = btn.dataset.location || '';
        document.getElementById('vaultLocationGroup').classList.toggle('d-none', action !== 'vault');
        document.getElementById('descriptionGroup').classList.toggle('d-none', action !== 'expense');
        document.getElementById('closeSummary').classList.add('d-none');
        if (action === 'close') {
            try {
                const response = await fetch(`${urls.detail}/${btn.dataset.isle}`, {headers:{Accept:'application/json'}});
                const data = await response.json();
                if (!response.ok || !data.status) {
                    AppError.handle({status:response.status,responseJSON:data}, {context:'consultar la caja'});
                    return;
                }
                document.getElementById('cashCloseId').value = data.cash_close.id;
                document.getElementById('sumInitial').textContent = Number(data.initial_cash_amount).toFixed(2);
                document.getElementById('sumSales').textContent = Number(data.cash_sales).toFixed(2);
                document.getElementById('sumExpenses').textContent = Number(data.cash_expenses).toFixed(2);
                document.getElementById('sumCalculated').textContent = Number(data.calculated_cash_amount).toFixed(2);
                document.getElementById('closeSummary').classList.remove('d-none');
            } catch (error) { AppError.handle({status:0}, {context:'consultar la caja'}); return; }
        }
        modal.show();
    }));

    document.getElementById('saveCashOperation').addEventListener('click', async function () {
        const action = document.getElementById('cashAction').value;
        const isle = document.getElementById('cashIsle').value;
        const amount = Number(document.getElementById('cashAmount').value);
        const vaultLocation = document.getElementById('cashVaultLocation').value;
        if (!amount || amount <= 0) { Swal.fire('Dato requerido', 'Ingrese un monto mayor a cero.', 'warning'); return; }
        if (action === 'vault' && !vaultLocation) { Swal.fire('Dato requerido', 'Seleccione la bóveda destino.', 'warning'); return; }
        let url, method = 'POST', body;
        if (action === 'open') { url=urls.open; body={initial_cash_amount:amount,isle_id:isle}; }
        if (action === 'expense') { url=urls.expense; body={amount,isle_id:isle,description:document.getElementById('cashDescription').value}; }
        if (action === 'vault') { url=urls.vault; body={amount,isle_id:isle,location_id:vaultLocation}; }
        if (action === 'close') { url=`${urls.detail}/${document.getElementById('cashCloseId').value}`; method='PUT'; body={final_cash_amount:amount,real_cash_amount:Number(document.getElementById('sumCalculated').textContent)}; }
        this.disabled = true;
        try {
            const response = await fetch(url, {method, headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify(body)});
            const data = await response.json();
            if (!response.ok || data.status === false || data.success === false) {
                AppError.handle({status:response.status,responseJSON:data}, {context:'procesar el movimiento de caja'});
                return;
            }
            modal.hide();
            await Swal.fire({icon:'success', title:'Operación completada', text:data.message, timer:1400, showConfirmButton:false});
            location.reload();
        } catch (error) { AppError.handle({status:0}, {context:'procesar el movimiento de caja'}); } finally { this.disabled=false; }
    });
});
</script>
@endsection
