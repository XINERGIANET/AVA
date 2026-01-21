@extends('template.index')

@section('header')
    <h1>Modulo de Contómetros</h1>
    <p>Modulo de gestión de contómetros</p>
@endsection
@extends('layouts.app') 

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary"><i class="fas fa-tachometer-alt"></i> Registro de Contómetros</h2>
            </div>

            <form action="{{ route('flowmeters.store') }}" method="POST" id="form-contometros">
                @csrf
                
                {{-- ITERAMOS POR CADA ISLA --}}
                @foreach($islas as $isla)
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📍 Isla: {{ $isla->nombre ?? 'Sin Nombre' }}</h5>
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
                                    {{-- ITERAMOS LOS LADOS (MANGUERAS) DE LA ISLA --}}
                                    @forelse($isla->lados as $lado)
                                    <tr>
                                        {{-- DATOS INFORMATIVOS --}}
                                        <td class="fw-bold text-center">
                                            {{ $lado->surtidor->nombre ?? 'Surt. '.$lado->surtidor_id }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $lado->nombre ?? 'Lado '.$lado->id }}</span>
                                            {{-- INPUT OCULTO CON EL ID DEL LADO --}}
                                            <input type="hidden" name="lecturas[{{ $lado->id }}][lado_id]" value="{{ $lado->id }}">
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-bg-light border">
                                                {{ $lado->producto->nombre ?? 'Producto Genérico' }}
                                            </span>
                                        </td>
                                        
                                        {{-- 1. LECTURA ANTERIOR (Solo lectura) --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-light input-inicial" 
                                                name="lecturas[{{ $lado->id }}][inicial]" 
                                                {{-- Usa el valor calculado en controller o 0 por defecto --}}
                                                value="{{ $lado->ultima_lectura ?? 0 }}" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 2. LECTURA ACTUAL (El usuario escribe aquí) --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end fw-bold border-primary input-final" 
                                                name="lecturas[{{ $lado->id }}][final]" 
                                                placeholder="0.000"
                                                required>
                                        </td>

                                        {{-- 3. VENTA CALCULADA (Final - Inicial) --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-white input-venta" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 4. VENTA SISTEMA (Dato teórico) --}}
                                        <td>
                                            <input type="number" step="0.001" 
                                                class="form-control text-end bg-light input-teorico" 
                                                name="lecturas[{{ $lado->id }}][teorico]" 
                                                value="{{ $lado->venta_sistema_actual ?? 0 }}" 
                                                readonly tabindex="-1">
                                        </td>

                                        {{-- 5. DIFERENCIA (Venta - Sistema) --}}
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

                {{-- BOTONES DE ACCIÓN --}}
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

{{-- SCRIPT PARA EL CÁLCULO AUTOMÁTICO EN EL NAVEGADOR --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos todos los inputs de lectura final
    const inputsFinal = document.querySelectorAll('.input-final');

    inputsFinal.forEach(input => {
        // Escuchamos cuando el usuario escribe
        input.addEventListener('input', calcularFila);
        // También calculamos al cargar por si hay datos viejos (old inputs)
        calcularFila.call(input); 
    });

    function calcularFila() {
        const row = this.closest('tr');
        
        // Obtenemos valores (convirtiendo a float, o 0 si está vacío)
        const inicial = parseFloat(row.querySelector('.input-inicial').value) || 0;
        const final = parseFloat(this.value) || 0;
        const teorico = parseFloat(row.querySelector('.input-teorico').value) || 0;
        
        // 1. Calcular Venta Física (Diferencia de contómetro)
        let ventaFisica = 0;
        if(final > 0) {
             ventaFisica = final - inicial;
        }
        
        // Actualizar input de venta
        row.querySelector('.input-venta').value = ventaFisica.toFixed(3);

        // 2. Calcular Diferencia (Físico vs Sistema)
        const diferencia = ventaFisica - teorico;
        const inputDiferencia = row.querySelector('.input-diferencia');
        
        inputDiferencia.value = diferencia.toFixed(3);

        // 3. Colores de alerta
        inputDiferencia.className = 'form-control text-end fw-bold input-diferencia'; // Reset clases
        
        if (Math.abs(diferencia) <= 0.1) {
            inputDiferencia.classList.add('bg-success', 'text-white'); // Todo OK
        } else if (diferencia < -0.1) {
            inputDiferencia.classList.add('bg-danger', 'text-white'); // Falta combustible
        } else {
            inputDiferencia.classList.add('bg-warning', 'text-dark'); // Sobra combustible
        }
    }
});
</script>

<style>
    /* Estilos opcionales para mejorar lectura */
    .bg-light-yellow { background-color: #fffae6 !important; }
    .bg-light-blue { background-color: #e6f7ff !important; }
    .sticky-bottom { position: sticky; bottom: 0; z-index: 100; }
</style>
@endsection