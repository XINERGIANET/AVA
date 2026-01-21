@extends('template.index')

@section('header')
    <h1>Modulo de Contómetros</h1>
    <p>Modulo de gestión de contómetros</p>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary"><i class="fas fa-tachometer-alt"></i> Registro de Contómetros</h2>
            </div>
            
            <div class="card card-body shadow-sm mb-4 bg-light">
                <div class="row g-3">
                    {{-- 1. FILTRO LOCATION (Recarga la página) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sede / Ubicación:</label>
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

                    {{-- 2. FILTRO ISLA (JavaScript) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filtrar por Isla:</label>
                        <select id="filter_isle" class="form-select">
                            <option value="all">Todas las Islas</option>
                            @foreach($islas as $isla)
                                <option value="{{ $isla->id }}">{{ $isla->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. FILTRO LADO (JavaScript) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filtrar por Lado:</label>
                        <select id="filter_side" class="form-select">
                            <option value="all">Todos los Lados</option>
                            @foreach($islas as $isla)
                                <optgroup label="{{ $isla->nombre }}">
                                    @foreach($isla->sides as $side)
                                        {{-- Usamos un value compuesto: islaID-ladoID --}}
                                        <option value="{{ $isla->id }}-{{ $side->id }}">
                                            Lado {{ $side->side }} ({{ $side->product->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO PRINCIPAL --}}
            <form action="{{ route('flowmeters.store') }}" method="POST" id="form-contometros">
                @csrf
                
                @foreach($islas as $isla)
                {{-- AGREGAMOS data-isle-id PARA IDENTIFICAR LA TARJETA --}}
                <div class="card mb-4 border-0 shadow-sm card-isla" data-isle-id="{{ $isla->id }}">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Isla: {{ $isla->nombre ?? 'Sin Nombre' }}</h5>
                        <small>ID: {{ $isla->id }}</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0 align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 15%">Surtidor</th>
                                        <th style="width: 10%">Lado</th>
                                        <th style="width: 15%">Producto</th>
                                        <th style="width: 15%" class="bg-light-yellow">Lectura Anterior</th>
                                        <th style="width: 15%" class="bg-light-blue">Lectura Actual</th>
                                        <th style="width: 15%">Venta (Galones)</th>
                                        <th style="width: 15%">Venta Sistema</th>
                                        <th style="width: 15%">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($isla->sides as $lado)
                                    {{-- AGREGAMOS data-side-id PARA IDENTIFICAR LA FILA --}}
                                    <tr class="row-lado" data-side-id="{{ $lado->id }}">
                                        {{-- DATOS INFORMATIVOS --}}
                                        <td class="fw-bold text-center">
                                            {{ $lado->name ?? 'Surtidor '.$lado->id }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">Lado {{ $lado->side }}</span>
                                            <input type="hidden" name="lecturas[{{ $lado->id }}][lado_id]" value="{{ $lado->id }}">
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-bg-light border">
                                                {{ $lado->product->name ?? 'Generico' }}
                                            </span>
                                        </td>
                                        
                                        {{-- 1. LECTURA ANTERIOR --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-light input-inicial" 
                                                name="lecturas[{{ $lado->id }}][inicial]" 
                                                value="{{ $lado->ultima_lectura ?? 0 }}" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 2. LECTURA ACTUAL --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end fw-bold border-primary input-final" 
                                                name="lecturas[{{ $lado->id }}][final]" 
                                                placeholder="0.000"
                                                required>
                                        </td>

                                        {{-- 3. VENTA CALCULADA --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-white input-venta" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 4. VENTA SISTEMA --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-light input-teorico" 
                                                name="lecturas[{{ $lado->id }}][teorico]" 
                                                value="{{ $lado->venta_sistema_actual ?? 0 }}" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 5. DIFERENCIA --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end fw-bold input-diferencia" 
                                                readonly tabindex="-1">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">
                                            No hay lados configurados para esta isla.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- BOTONES --}}
                <div class="card shadow-sm mb-5 sticky-bottom">
                    <div class="card-body d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-save me-2"></i> Guardar Lecturas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JAVASCRIPT: Lógica de Filtros y Cálculos --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DE FILTROS ---
    const filterIsle = document.getElementById('filter_isle');
    const filterSide = document.getElementById('filter_side');
    const allCards = document.querySelectorAll('.card-isla');
    const allRows = document.querySelectorAll('.row-lado');

    // 1. Evento Filtro Isla
    filterIsle.addEventListener('change', function() {
        const selectedIsleId = this.value;
        
        // Resetear filtro de lados al cambiar isla
        filterSide.value = 'all'; 
        allRows.forEach(row => row.style.display = ''); // Mostrar todas las filas internas

        if (selectedIsleId === 'all') {
            allCards.forEach(card => card.style.display = '');
        } else {
            allCards.forEach(card => {
                if (card.dataset.isleId === selectedIsleId) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    });

    // 2. Evento Filtro Lado
    filterSide.addEventListener('change', function() {
        const value = this.value; // Formato: "islaID-ladoID" o "all"
        
        if (value === 'all') {
            // Mostrar todo segun lo que diga el filtro de isla
            filterIsle.dispatchEvent(new Event('change'));
            return;
        }

        const [isleId, sideId] = value.split('-');

        // Primero ocultamos todas las islas excepto la que contiene el lado
        allCards.forEach(card => {
            if (card.dataset.isleId === isleId) {
                card.style.display = '';
                // Dentro de esta isla, filtramos las filas (tr)
                const rowsInCard = card.querySelectorAll('.row-lado');
                rowsInCard.forEach(row => {
                    if (row.dataset.sideId === sideId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            } else {
                card.style.display = 'none';
            }
        });
        
        // Sincronizar el select de isla visualmente
        filterIsle.value = isleId; 
    });


    // --- LÓGICA DE CÁLCULOS (Ya la tenías, la mantengo igual) ---
    const inputsFinal = document.querySelectorAll('.input-final');
    inputsFinal.forEach(input => {
        input.addEventListener('input', calcularFila);
        if(input.value) calcularFila.call(input); 
    });

    function calcularFila() {
        // ... (Tu código de cálculo de antes va aquí exacto igual) ...
        const row = this.closest('tr');
        const inicial = parseFloat(row.querySelector('.input-inicial').value) || 0;
        const final = parseFloat(this.value) || 0;
        const teorico = parseFloat(row.querySelector('.input-teorico').value) || 0;
        
        let ventaFisica = 0;
        if(final > 0) ventaFisica = final - inicial;
        
        row.querySelector('.input-venta').value = ventaFisica.toFixed(3);
        const diferencia = ventaFisica - teorico;
        const inputDiferencia = row.querySelector('.input-diferencia');
        inputDiferencia.value = diferencia.toFixed(3);

        inputDiferencia.className = 'form-control text-end fw-bold input-diferencia'; 
        if (Math.abs(diferencia) <= 0.1) {
            inputDiferencia.classList.add('bg-success', 'text-white'); 
        } else if (diferencia < -0.1) {
            inputDiferencia.classList.add('bg-danger', 'text-white'); 
        } else {
            inputDiferencia.classList.add('bg-warning', 'text-dark'); 
        }
    }
});
</script>

<style>
    .bg-light-yellow { background-color: #fffae6 !important; }
    .bg-light-blue { background-color: #e6f7ff !important; }
    .sticky-bottom { position: sticky; bottom: 0; z-index: 100; }
</style>
@endsection