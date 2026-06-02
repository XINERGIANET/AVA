@extends('template.index')

@section('header')
    <h1>Reporte de Ingresos y Dashboard</h1>
    <p>Sistema de Gestión AVA</p>
@endsection

@section('content')
<!-- Include Chart.js and FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Modern Palette */
    :root {
        --primary-blue: #1d4ed8;
        --soft-bg: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-gray: #64748b;
        --red-icon: #ef4444;
        --green-icon: #10b981;
        --purple-icon: #8b5cf6;
        --orange-icon: #f59e0b;
        --blue-icon: #3b82f6;
    }

    body {
        background-color: var(--soft-bg);
    }

    .kpi-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .kpi-card-blue {
        background: var(--primary-blue);
        color: white;
        border: none;
    }

    .kpi-title {
        font-size: 0.75rem;
        color: var(--text-gray);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    .kpi-card-blue .kpi-title {
        color: #bfdbfe;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 5px 0;
    }
    
    .kpi-card-blue .kpi-value {
        color: white;
    }

    .filter-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-dark);
        font-weight: 700;
        margin-bottom: 5px;
    }

    .btn-primary-blue {
        background-color: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px;
        font-weight: 600;
    }

    .btn-primary-blue:hover {
        background-color: #1e40af;
        color: white;
    }

    /* Ribbon */
    .ribbon {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .ribbon-item {
        flex: 1;
        min-width: 130px;
        padding: 15px;
        display: flex;
        align-items: center;
        color: white;
    }
    .ribbon-icon {
        font-size: 1.5rem;
        margin-right: 12px;
        opacity: 0.8;
    }
    .ribbon-text {
        display: flex;
        flex-direction: column;
    }
    .ribbon-title {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 2px;
        letter-spacing: 0.5px;
    }
    .ribbon-value {
        font-size: 1.1rem;
        font-weight: 800;
    }

    /* Payment Methods Cards */
    .pm-card {
        display: flex;
        align-items: center;
        padding: 20px;
    }
    .pm-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
    }
    .pm-icon-blue { background: #eff6ff; color: var(--blue-icon); }
    .pm-icon-green { background: #ecfdf5; color: var(--green-icon); }
    .pm-icon-purple { background: #f5f3ff; color: var(--purple-icon); }
    .pm-icon-red { background: #fef2f2; color: var(--red-icon); }

    .badge-soft-green {
        background: #dcfce7;
        color: #166534;
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .badge-blue {
        background: rgba(255,255,255,0.2);
        color: white;
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
    }

    .info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
    }
    .info-box i {
        font-size: 1.5rem;
        margin-right: 15px;
    }

    /* Estilos para pantalla completa */
    #dashboard-container:fullscreen {
        background-color: var(--soft-bg);
        overflow-y: auto;
        padding: 40px !important;
    }
    /* Webkit (Safari, Chrome) */
    #dashboard-container:-webkit-full-screen {
        background-color: var(--soft-bg);
        overflow-y: auto;
        padding: 40px !important;
    }
</style>

<div class="container-fluid content-inner py-4" id="dashboard-container">
    <div class="d-flex justify-content-end mb-3">
        <button id="fullscreenBtn" class="btn btn-white shadow-sm border text-primary" style="background: white;">
            <i class="fas fa-expand me-2"></i> Pantalla Completa
        </button>
    </div>

    <div class="row">
        <!-- Columna Izquierda: Filtros -->
        <div class="col-lg-3 col-md-12 mb-4">
            <div class="kpi-card p-4 justify-content-start">
                <div class="d-flex align-items-center mb-4 text-secondary">
                    <i class="fas fa-filter me-2"></i>
                    <h6 class="m-0 font-weight-bold">Filtros</h6>
                </div>
                
                <form method="GET" action="{{ route('dashboard.index') }}">
                    <div class="form-group mb-4">
                        <label class="filter-label">SEDE</label>
                        <select name="location_id" class="form-control" style="border-radius: 8px;">
                            <option value="">Todas las Sedes</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="filter-label">AÑO</label>
                        <select name="year" class="form-control" style="border-radius: 8px;">
                            <option value="{{ date('Y') }}" {{ $thisYear == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                            <option value="{{ date('Y')-1 }}" {{ $thisYear == date('Y')-1 ? 'selected' : '' }}>{{ date('Y')-1 }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="filter-label">MES</label>
                        <select name="month" class="form-control" style="border-radius: 8px;">
                            <option value="all" {{ $thisMonth == 'all' ? 'selected' : '' }}>Todos los meses</option>
                            <option value="01" {{ $thisMonth == '01' ? 'selected' : '' }}>Enero</option>
                            <option value="02" {{ $thisMonth == '02' ? 'selected' : '' }}>Febrero</option>
                            <option value="03" {{ $thisMonth == '03' ? 'selected' : '' }}>Marzo</option>
                            <option value="04" {{ $thisMonth == '04' ? 'selected' : '' }}>Abril</option>
                            <option value="05" {{ $thisMonth == '05' ? 'selected' : '' }}>Mayo</option>
                            <option value="06" {{ $thisMonth == '06' ? 'selected' : '' }}>Junio</option>
                            <option value="07" {{ $thisMonth == '07' ? 'selected' : '' }}>Julio</option>
                            <option value="08" {{ $thisMonth == '08' ? 'selected' : '' }}>Agosto</option>
                            <option value="09" {{ $thisMonth == '09' ? 'selected' : '' }}>Septiembre</option>
                            <option value="10" {{ $thisMonth == '10' ? 'selected' : '' }}>Octubre</option>
                            <option value="11" {{ $thisMonth == '11' ? 'selected' : '' }}>Noviembre</option>
                            <option value="12" {{ $thisMonth == '12' ? 'selected' : '' }}>Diciembre</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-blue w-100">
                        <i class="fas fa-sync-alt me-2"></i> Actualizar
                    </button>
                </form>
                
                @if($rentabilidad > 0)
                <div class="alert alert-success mt-4 p-3 d-flex align-items-center" style="font-size:0.85rem; border-radius: 8px;">
                    <i class="far fa-check-circle fs-4 me-2"></i>
                    Excelente, su negocio registra ingresos.
                </div>
                @endif
        </div>
        </div>

        <!-- Columna Principal -->
        <div class="col-lg-9 col-md-12">
            
            <!-- ROW 1: TOP KPIs -->
            <div class="row align-items-stretch mb-4">
                <!-- Blue Card -->
                <div class="col-lg-5 col-md-12 mb-3">
                    <div class="kpi-card kpi-card-blue p-4">
                        <div class="row align-items-center h-100">
                            <div class="col-7">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="kpi-title">TOTAL DE INGRESOS</div>
                                </div>
                                <div class="kpi-value">S/ {{ number_format($ventasTotalesMes, 2) }}</div>
                                <div class="badge-blue">{{ $thisMonth == 'all' ? 'TODOS LOS MESES' : 'MES: ' . $thisMonth }}</div>
                            </div>
                            <div class="col-5">
                                <div style="position: relative; height: 100px; width: 100%;">
                                    <canvas id="bigGaugeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Actual -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="kpi-card p-4">
                        <div class="row h-100 align-items-center">
                            <div class="col-6">
                                <div class="kpi-title text-success">BALANCE ACTUAL</div>
                                <div class="kpi-value">S/ {{ number_format($balanceActual, 2) }}</div>
                                @if($rentabilidadPorcentaje > 0)
                                    <span class="badge-soft-green">+{{ $rentabilidadPorcentaje }}% vs gastos</span>
                                @endif
                            </div>
                            <div class="col-6">
                                <div style="position: relative; height: 80px; width: 100%;">
                                    <canvas id="miniAreaChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gastos Totales -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="kpi-card p-4 text-center">
                        <div class="pm-icon-box pm-icon-red mx-auto mb-2">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="kpi-title text-danger">GASTOS TOTALES</div>
                        <div class="kpi-value text-dark" style="font-size:1.3rem;">S/ {{ number_format($gastosTotales, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- ROW 2: RIBBON -->
            <div class="ribbon">
                <div class="ribbon-item" style="background-color: #d4a373;">
                    <i class="fas fa-shopping-cart ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">VENTAS</div>
                        <div class="ribbon-value">S/ {{ number_format($ventasTotalesMes, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item" style="background-color: #10b981;">
                    <i class="fas fa-wallet ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">INGRESOS CAJA</div>
                        <div class="ribbon-value">S/ {{ number_format($ingresosCaja, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item" style="background-color: #059669;">
                    <i class="fas fa-balance-scale ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">BALANCE TOTAL</div>
                        <div class="ribbon-value">S/ {{ number_format($balanceActual, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item" style="background-color: #34d399;">
                    <i class="fas fa-chart-line ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">RENTABILIDAD</div>
                        <div class="ribbon-value">S/ {{ number_format($rentabilidad, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item" style="background-color: #ef4444;">
                    <i class="fas fa-credit-card ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">GASTOS</div>
                        <div class="ribbon-value">S/ {{ number_format($gastosTotales, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item" style="background-color: #9ca3af;">
                    <i class="fas fa-chart-pie ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">% RENTABILIDAD</div>
                        <div class="ribbon-value">{{ $rentabilidadPorcentaje }}%</div>
                    </div>
                </div>
            </div>

            <!-- ROW 3: PAYMENT METHODS & BAR CHART -->
            <div class="row align-items-stretch mb-4">
                <!-- 2x2 Grid Payment Methods -->
                <div class="col-lg-5 col-md-12 mb-3">
                    <div class="row h-100">
                        <div class="col-6 mb-3">
                            <div class="kpi-card pm-card">
                                <div class="pm-icon-box pm-icon-blue">
                                    <i class="far fa-credit-card"></i>
                                </div>
                                <div>
                                    <div class="kpi-value text-dark" style="font-size:1.1rem;">S/ {{ number_format($creditos, 2) }}</div>
                                    <div class="kpi-title text-danger" style="font-size:0.6rem;">PENDIENTE / CRÉDITO</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="kpi-card pm-card">
                                <div class="pm-icon-box pm-icon-green">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <div class="kpi-value text-dark" style="font-size:1.1rem;">S/ {{ number_format($efectivo, 2) }}</div>
                                    <div class="kpi-title text-success" style="font-size:0.6rem;">EFECTIVO</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="kpi-card pm-card">
                                <div class="pm-icon-box pm-icon-blue">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div>
                                    <div class="kpi-value text-dark" style="font-size:1.1rem;">S/ {{ number_format($transferencias, 2) }}</div>
                                    <div class="kpi-title text-primary" style="font-size:0.6rem;">TRANSFERENCIAS</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="kpi-card pm-card">
                                <div class="pm-icon-box pm-icon-purple">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <div class="kpi-value text-dark" style="font-size:1.1rem;">S/ {{ number_format($yapePlin, 2) }}</div>
                                    <div class="kpi-title" style="font-size:0.6rem; color: var(--purple-icon);">YAPE / PLIN</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="col-lg-7 col-md-12 mb-3">
                    <div class="kpi-card">
                        <h6 class="text-dark font-weight-bold mb-3 text-left">VENTA REAL VS TEÓRICA POR MES</h6>
                        <div style="position: relative; height: 200px; width: 100%;">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Colors
        const dangerColor = '#ef4444';
        const successColor = '#10b981';
        const primaryColor = '#1d4ed8';
        const grayColor = '#e2e8f0';
        const greenLight = '#34d399';
        
        // 1. Big Gauge Chart (Total Ventas Semicircular inside Blue Card)
        const ctxBigGauge = document.getElementById('bigGaugeChart').getContext('2d');
        new Chart(ctxBigGauge, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [100], 
                    backgroundColor: [greenLight], // Bright green over blue bg
                    borderWidth: 0,
                    cutout: '75%',
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { tooltip: { enabled: false } } }
        });

        // 2. Mini Area Chart (Balance Actual)
        const ctxMiniArea = document.getElementById('miniAreaChart').getContext('2d');
        new Chart(ctxMiniArea, {
            type: 'line',
            data: {
                labels: ['1','2','3','4','5','6','7'],
                datasets: [{
                    data: [10, 40, 30, 70, 50, 90, 60],
                    borderColor: successColor,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { x: { display: false }, y: { display: false, min: 0 } },
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });

        // 3. Bar Chart (Venta Real vs Teorica)
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartVentasTeoricas['labels'] ?? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']) !!},
                datasets: [
                    {
                        label: 'Venta Real',
                        data: {!! json_encode($chartVentasTeoricas['real']) !!},
                        backgroundColor: '#64748b', 
                        borderRadius: 4,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Venta Teórica',
                        data: {!! json_encode($chartVentasTeoricas['teorica']) !!},
                        backgroundColor: dangerColor, 
                        borderRadius: 4,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f8fafc' }, beginAtZero: true }
                },
                plugins: { legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10 } } }
            }
        });

        // Fullscreen Toggle
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const dashboardContainer = document.getElementById('dashboard-container');

        fullscreenBtn.addEventListener('click', function() {
            if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                // Enter fullscreen
                if (dashboardContainer.requestFullscreen) {
                    dashboardContainer.requestFullscreen();
                } else if (dashboardContainer.msRequestFullscreen) {
                    dashboardContainer.msRequestFullscreen();
                } else if (dashboardContainer.mozRequestFullScreen) {
                    dashboardContainer.mozRequestFullScreen();
                } else if (dashboardContainer.webkitRequestFullscreen) {
                    dashboardContainer.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
                fullscreenBtn.innerHTML = '<i class="fas fa-compress me-2"></i> Salir de Pantalla Completa';
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
                fullscreenBtn.innerHTML = '<i class="fas fa-expand me-2"></i> Pantalla Completa';
            }
        });

        // Reset button text if user exits fullscreen with ESC key
        document.addEventListener('fullscreenchange', exitHandler);
        document.addEventListener('webkitfullscreenchange', exitHandler);
        document.addEventListener('mozfullscreenchange', exitHandler);
        document.addEventListener('MSFullscreenChange', exitHandler);

        function exitHandler() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                fullscreenBtn.innerHTML = '<i class="fas fa-expand me-2"></i> Pantalla Completa';
            }
        }

    });
</script>
@endsection
