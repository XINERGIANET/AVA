@extends('template.index')

@section('header')
    <h1>Toma de Inventario</h1>
    <p>Registro de lectura física (varillaje) por tanque</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-0">

    <div class="card shadow">
        <div class="card-body">
            <div class="mb-4">
                <div class="row align-items-end">

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-primary">1. Sede</label>
                        <form method="GET" action="{{ route('merma.create') }}" id="form-location-filter">
                            <select name="location_id" class="form-select border-primary" onchange="document.getElementById('form-location-filter').submit()">
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $currentLocationId == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                </div>
            </div>

            <hr class="my-4">

            @if($tanks->isEmpty())
                <div class="alert alert-warning">No hay tanques activos registrados para esta sede.</div>
            @else
                <form action="{{ route('merma.store') }}" method="POST" id="form-merma">
                    @csrf
                    <input type="hidden" name="location_id" value="{{ $currentLocationId }}">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha de la toma</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 16%">Tanque</th>
                                    <th style="width: 14%">Producto</th>
                                    <th style="width: 12%">Capacidad</th>
                                    <th style="width: 14%">Referencia anterior</th>
                                    <th style="width: 16%">Lectura Física</th>
                                    <th style="width: 12%">Diferencia (aprox.)</th>
                                    <th style="width: 16%">Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tanks as $tank)
                                <tr class="row-tanque">
                                    <td class="text-center fw-bold">
                                        {{ $tank->name }}
                                        <input type="hidden" name="lecturas[{{ $tank->id }}][tank_id]" value="{{ $tank->id }}">
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small fw-bold">{{ $tank->product->name ?? 'Genérico' }}</span>
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($tank->capacity, 3) }}
                                    </td>
                                    <td class="text-end input-referencia" data-referencia="{{ $tank->referencia }}">
                                        {{ number_format($tank->referencia, 3) }}
                                    </td>
                                    <td>
                                        <input type="number" step="0.001"
                                            class="form-control form-control-sm text-end fw-bold border-primary input-fisico"
                                            name="lecturas[{{ $tank->id }}][fisico]"
                                            placeholder="">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm text-end fw-bold input-diferencia"
                                            readonly tabindex="-1">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm"
                                            name="lecturas[{{ $tank->id }}][notas]"
                                            placeholder="Opcional">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small">
                        La columna "Diferencia (aprox.)" es solo una referencia visual contra la última lectura o el stock contable actual.
                        El cálculo oficial (considerando compras, descargas y ventas del período) se hace al guardar.
                    </p>

                    <div class="row mt-4 mb-3">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success px-4">Guardar Toma de Inventario</button>
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputsFisico = document.querySelectorAll('.input-fisico');

    inputsFisico.forEach(input => {
        input.addEventListener('input', calcularFila);
    });

    function calcularFila() {
        const row = this.closest('tr');
        const referencia = parseFloat(row.querySelector('.input-referencia').dataset.referencia) || 0;
        const fisicoVal = this.value;
        const inputDiferencia = row.querySelector('.input-diferencia');

        if (fisicoVal === '') {
            inputDiferencia.value = '';
            inputDiferencia.className = 'form-control form-control-sm text-end fw-bold input-diferencia';
            return;
        }

        const fisico = parseFloat(fisicoVal) || 0;
        const diferencia = fisico - referencia;
        const tolerancia = Math.abs(referencia) * 0.02;

        inputDiferencia.value = diferencia.toFixed(3);
        inputDiferencia.className = 'form-control form-control-sm text-end fw-bold input-diferencia';

        if (Math.abs(diferencia) <= tolerancia) {
            inputDiferencia.classList.add('bg-success', 'text-white');
        } else if (diferencia < 0) {
            inputDiferencia.classList.add('bg-danger', 'text-white');
        } else {
            inputDiferencia.classList.add('bg-warning', 'text-dark');
        }
    }
});
</script>
@endsection
