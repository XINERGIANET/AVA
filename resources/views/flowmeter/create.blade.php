@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-speedometer2 me-2 text-primary"></i>Registro de Contómetros</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('flowmeters.historico') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Registro de Contómetros</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
                {{-- SECCIÓN DE FILTROS --}}
            <!-- Toolbar de Filtros y Acciones -->
            <form method="GET" action="{{ route('flowmeters.create') }}" id="form-filter">
                <div class="row g-2 align-items-end mb-4">
                    <div class="col-md-2">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                        <select name="location_id" class="form-select form-select-sm">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ $currentLocationId == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">
                            <i class="bi bi-calendar-event me-1 text-primary"></i>Fecha
                        </label>
                        <input type="date" name="date" id="filter_date" class="form-control form-control-sm fw-bold border-primary" 
                            value="{{ $selectedDate }}"
                            onchange="document.getElementById('post_date').value = this.value">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Isla</label>
                        <select id="filter_isle" class="form-select form-select-sm">
                            <option value="all">Todas las Islas</option>
                            @foreach($islas as $isla)
                                <option value="{{ $isla->id }}">{{ $isla->name ?? $isla->nombre }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Máquina / Surtidor</label>
                        <select id="filter_pump_name" class="form-select form-select-sm">
                            <option value="all">Todas las Máquinas</option>
                            @php
                                $uniquePumpNames = $islas->pluck('sides')->flatten()->pluck('name')->filter()->unique()->sort();
                            @endphp
                            @foreach($uniquePumpNames as $pumpName)
                                <option value="{{ $pumpName }}">{{ $pumpName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Lado</label>
                        <select id="filter_side_number" class="form-select form-select-sm">
                            <option value="all">Todos</option>
                            @php
                                $uniqueSides = $islas->pluck('sides')->flatten()->pluck('side')->unique()->sort();
                            @endphp
                            @foreach($uniqueSides as $sideNum)
                                <option value="{{ $sideNum }}">Lado {{ $sideNum }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm px-2 fw-medium w-100" style="border-radius: 6px;" title="Filtrar lecturas por fecha">
                            <i class="bi bi-funnel me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2 text-end ms-auto">
                        <button type="submit" form="form-contometros" class="btn btn-success btn-sm px-3 fw-medium w-100" style="border-radius: 6px;">
                            <i class="bi bi-save me-1"></i> Guardar Registros
                        </button>
                    </div>
                </div>
            </form>

            <form action="{{ route('flowmeters.store') }}" method="POST" id="form-contometros">
                @csrf
                <input type="hidden" name="location_id" value="{{ $currentLocationId }}">
                <input type="hidden" name="date" id="post_date" value="{{ $selectedDate }}">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                        <thead class="text-center">
                            <tr>
                                <th class="fw-bold text-uppercase" style="width: 12%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Surtidor</th>
                                <th class="fw-bold text-uppercase" style="width: 8%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Lado</th>
                                <th class="fw-bold text-uppercase" style="width: 12%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                <th class="fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Valor Inicial</th>
                                <th class="fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Valor Final</th>
                                <th class="fw-bold text-uppercase" style="width: 13%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Diferencia</th>
                            </tr>
                        </thead>
                        
                        @foreach($islas as $isla)
                        <tbody class="tbody-isla" data-isle-id="{{ $isla->id }}">
                            @foreach($isla->sides as $lado)
                            <tr class="row-lado" data-side-number="{{ $lado->side }}" data-pump-name="{{ $lado->name }}">
                                
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
                                        class="form-control form-control-sm text-end fw-bold input-inicial" 
                                        name="lecturas[{{ $lado->id }}][inicial]" 
                                        value="{{ $lado->ultima_lectura ?? 0 }}">
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
                                        class="form-control form-control-sm text-end fw-bold input-diferencia" 
                                        readonly tabindex="-1">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE FILTROS ---
    const filterIsle = document.getElementById('filter_isle');
    const filterPump = document.getElementById('filter_pump_name');
    const filterSide = document.getElementById('filter_side_number');

    function aplicarFiltros() {
        const selectedIsleId = filterIsle ? filterIsle.value : 'all';
        const selectedPumpName = filterPump ? filterPump.value : 'all';
        const selectedSideNum = filterSide ? filterSide.value : 'all';
        const allTbodies = document.querySelectorAll('.tbody-isla');

        allTbodies.forEach(tbody => {
            const isleId = tbody.dataset.isleId;
            let visibleRowsCount = 0;
            const isIsleMatch = (selectedIsleId === 'all' || selectedIsleId === isleId);

            if (isIsleMatch) {
                const rows = tbody.querySelectorAll('.row-lado');
                rows.forEach(row => {
                    const rowSideNum = row.dataset.sideNumber;
                    const rowPumpName = row.dataset.pumpName;
                    const isSideMatch = (selectedSideNum === 'all' || selectedSideNum === rowSideNum);
                    const isPumpMatch = (selectedPumpName === 'all' || selectedPumpName === rowPumpName);

                    if (isSideMatch && isPumpMatch) {
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
    }

    if (filterIsle) filterIsle.addEventListener('change', aplicarFiltros);
    if (filterPump) filterPump.addEventListener('change', aplicarFiltros);
    if (filterSide) filterSide.addEventListener('change', aplicarFiltros);

    // --- CÁLCULOS ---
    const inputsInicial = document.querySelectorAll('.input-inicial');
    const inputsFinal = document.querySelectorAll('.input-final');

    inputsFinal.forEach(input => {
        input.addEventListener('input', calcularFila);
        // Calcular al inicio
        if(input.value) calcularFila.call(input); 
    });

    inputsInicial.forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const finalInput = row.querySelector('.input-final');
            if (finalInput) calcularFila.call(finalInput);
        });
    });

    function calcularFila() {
        const row = this.closest('tr');        
        const inicial = parseFloat(row.querySelector('.input-inicial').value) || 0;
        const finalVal = this.value; // Valor crudo para saber si está vacío
        const final = parseFloat(finalVal) || 0;
        
        // Si está vacío, limpiamos y salimos (no mostramos error ni ceros)
        if (finalVal === '') {
            row.querySelector('.input-venta').value = '';
            row.querySelector('.input-diferencia').value = '';
            row.querySelector('.input-diferencia').className = 'form-control form-control-sm text-end fw-bold input-diferencia';
            return;
        }

        let ventaFisica = final - inicial;
        
        const inputVenta = row.querySelector('.input-venta');
        if (inputVenta) {
            inputVenta.value = ventaFisica.toFixed(3);
        }

        const diferencia = final - inicial;        
        const inputDiferencia = row.querySelector('.input-diferencia');
        
        inputDiferencia.value = diferencia.toFixed(3);

        inputDiferencia.className = 'form-control form-control-sm text-end fw-bold input-diferencia'; 
        
        if (diferencia < 0) {
            inputDiferencia.classList.add('bg-danger', 'text-white'); // Rojo si Final < Inicial (Lectura inválida)
        } else {
            inputDiferencia.classList.add('bg-success', 'text-white'); // Verde si Final >= Inicial (Lectura correcta)
        }
    }
});
</script>
@endsection
