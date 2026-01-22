@extends('template.index')

@section('header')
    <h1>Registro de Contómetros</h1>
    <p>Ingreso de lecturas por turno</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    
    <div class="card shadow">
        <div class="card-body">
            <div class="mb-4">
                {{-- SECCIÓN DE FILTROS --}}
                <div class="row align-items-end"> 
                    
                    {{-- 1. FILTRO SEDE --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold text-primary">1. Sede</label>
                        <form method="GET" action="{{ route('flowmeters.create') }}" id="form-location-filter">
                            <select name="location_id" class="form-select border-primary" onchange="document.getElementById('form-location-filter').submit()">
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ $currentLocationId == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    {{-- 2. FILTRO ISLA --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">2. Isla</label>
                        <select id="filter_isle" class="form-select">
                            <option value="all">Todas las Islas</option>
                            @foreach($islas as $isla)
                                <option value="{{ $isla->id }}">{{ $isla->name ?? $isla->nombre }}</option> 
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. FILTRO LADO --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">3. Lado</label>
                        <select id="filter_side_number" class="form-select">
                            <option value="all">Todos los Lados</option>
                            @php
                                $uniqueSides = $islas->pluck('sides')->flatten()->pluck('side')->unique()->sort();
                            @endphp
                            @foreach($uniqueSides as $sideNum)
                                <option value="{{ $sideNum }}">Lado {{ $sideNum }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. BOTÓN FILTRAR --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="button" id="btn_filter" class="btn btn-dark w-100">
                            <i class="fas fa-filter me-2"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">

            <form action="{{ route('flowmeters.store') }}" method="POST" id="form-contometros">
                @csrf
                {{-- IMPORTANTE: Enviamos la sede actual --}}
                <input type="hidden" name="location_id" value="{{ $currentLocationId }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 12%">Surtidor</th>
                                <th style="width: 8%">Lado</th>
                                <th style="width: 12%">Producto</th>
                                <th style="width: 15%">Valor Inicial</th>
                                <th style="width: 15%">Valor Final</th>
                                <th style="width: 10%">Valor Teórico</th>
                                <th style="width: 13%">Diferencia</th>
                            </tr>
                        </thead>
                        
                        @foreach($islas as $isla)
                        <tbody class="tbody-isla" data-isle-id="{{ $isla->id }}">
                            @foreach($isla->sides as $lado)
                            <tr class="row-lado" data-side-number="{{ $lado->side }}">
                                
                                <td class="text-center fw-bold">
                                    {{ $lado->name ?? 'Surtidor '.$lado->id }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary">Lado {{ $lado->side }}</span>
                                    <input type="hidden" name="lecturas[{{ $lado->id }}][lado_id]" value="{{ $lado->id }}">
                                </td>

                                <td class="text-center">
                                    <span class="text-muted small fw-bold">{{ $lado->product->name ?? 'Generico' }}</span>
                                </td>

                                <td>
                                    <input type="number" step="0.001" 
                                        class="form-control form-control-sm text-end bg-light input-inicial" 
                                        name="lecturas[{{ $lado->id }}][inicial]" 
                                        value="{{ $lado->ultima_lectura ?? 0 }}" 
                                        readonly tabindex="-1">
                                </td>

                                <td>
                                    <input type="number" step="0.001" 
                                        class="form-control form-control-sm text-end fw-bold border-primary input-final" 
                                        name="lecturas[{{ $lado->id }}][final]" 
                                        placeholder="">
                                </td>

                                {{-- Input Oculto para enviar Galones calculados (Opcional, se calcula en backend también) --}}
                                <input type="hidden" class="input-venta" name="lecturas[{{ $lado->id }}][galones]"> 

                                <td>
                                    <input type="number" step="0.001" 
                                        class="form-control form-control-sm text-end bg-light input-teorico" 
                                        name="lecturas[{{ $lado->id }}][teorico]" 
                                        value="{{ $lado->venta_sistema_actual ?? 0 }}" 
                                        readonly tabindex="-1">
                                </td>

                                <td>
                                    <input type="number" step="0.001" 
                                        class="form-control form-control-sm text-end fw-bold input-diferencia" 
                                        readonly tabindex="-1">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endforeach
                    </table>
                </div>

                <div class="row mt-4 mb-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success px-4">Guardar Registros</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE FILTROS ---
    const btnFilter = document.getElementById('btn_filter');
    const filterIsle = document.getElementById('filter_isle');
    const filterSide = document.getElementById('filter_side_number');
    
    btnFilter.addEventListener('click', function() {
        const selectedIsleId = filterIsle.value;
        const selectedSideNum = filterSide.value;
        const allTbodies = document.querySelectorAll('.tbody-isla');

        allTbodies.forEach(tbody => {
            const isleId = tbody.dataset.isleId;
            let visibleRowsCount = 0;
            const isIsleMatch = (selectedIsleId === 'all' || selectedIsleId === isleId);

            if (isIsleMatch) {
                const rows = tbody.querySelectorAll('.row-lado');
                rows.forEach(row => {
                    const rowSideNum = row.dataset.sideNumber;
                    const isSideMatch = (selectedSideNum === 'all' || selectedSideNum === rowSideNum);
                    if (isSideMatch) {
                        row.style.display = '';
                        visibleRowsCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                tbody.style.display = (visibleRowsCount > 0) ? '' : 'none';
            } else {
                tbody.style.display = 'none';
            }
        });
    });

    // --- CÁLCULOS ---
    const inputsFinal = document.querySelectorAll('.input-final');

    inputsFinal.forEach(input => {
        input.addEventListener('input', calcularFila);
        // Calcular al inicio
        if(input.value) calcularFila.call(input); 
    });

    function calcularFila() {
        const row = this.closest('tr');        
        const inicial = parseFloat(row.querySelector('.input-inicial').value) || 0;
        const finalVal = this.value; // Valor crudo para saber si está vacío
        const final = parseFloat(finalVal) || 0;
        const teorico = parseFloat(row.querySelector('.input-teorico').value) || 0;
        
        // Si está vacío, limpiamos y salimos (no mostramos error ni ceros)
        if (finalVal === '') {
            row.querySelector('.input-venta').value = '';
            row.querySelector('.input-diferencia').value = '';
            row.querySelector('.input-diferencia').className = 'form-control form-control-sm text-end fw-bold input-diferencia';
            return;
        }

        let ventaFisica = inicial - final;
        
        const inputVenta = row.querySelector('.input-venta');
        if (inputVenta) {
            inputVenta.value = ventaFisica.toFixed(3);
        }

        // 2. Diferencia: Física - Teórica
        // (2 - 1 = +1 Sobra) o (2 - 6 = -4 Falta)
        const diferencia = ventaFisica - teorico;        
        const inputDiferencia = row.querySelector('.input-diferencia');
        
        inputDiferencia.value = diferencia.toFixed(3);

        // 3. Semáforo
        inputDiferencia.className = 'form-control form-control-sm text-end fw-bold input-diferencia'; 
        
        if (Math.abs(diferencia) <= 0.02) {
            inputDiferencia.classList.add('bg-success', 'text-white'); // Verde (Ok)
        } else if (diferencia < -0.02) {
            inputDiferencia.classList.add('bg-danger', 'text-white'); // Rojo (Falta / Negativo)
        } else {
            inputDiferencia.classList.add('bg-warning', 'text-dark'); // Amarillo (Sobra / Positivo)
        }
    }
});
</script>
@endsection