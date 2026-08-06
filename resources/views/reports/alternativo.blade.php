@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard & Ingresos
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection

@section('content')
<!-- Include Chart.js and FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
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
    /* Clickable Cards */
    .kpi-card-clickable, .ribbon-item-clickable {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card-clickable:hover, .ribbon-item-clickable:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.08) !important;
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
                    <div class="kpi-card kpi-card-blue p-4 kpi-card-clickable" data-kpi-type="ventas" title="Haz clic para ver el detalle de ventas">
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
                    <div class="kpi-card p-4 text-center kpi-card-clickable" data-kpi-type="gastos" title="Haz clic para ver el detalle de gastos">
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
                <div class="ribbon-item ribbon-item-clickable" data-kpi-type="ventas" style="background-color: #d4a373;" title="Ver ventas">
                    <i class="fas fa-shopping-cart ribbon-icon"></i>
                    <div class="ribbon-text">
                        <div class="ribbon-title">VENTAS</div>
                        <div class="ribbon-value">S/ {{ number_format($ventasTotalesMes, 2) }}</div>
                    </div>
                </div>
                <div class="ribbon-item ribbon-item-clickable" data-kpi-type="caja" style="background-color: #10b981;" title="Ver ingresos de caja">
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
                <div class="ribbon-item ribbon-item-clickable" data-kpi-type="gastos" style="background-color: #ef4444;" title="Ver gastos">
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
                            <div class="kpi-card pm-card kpi-card-clickable" data-kpi-type="creditos" title="Ver detalle de pendientes / créditos">
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
                            <div class="kpi-card pm-card kpi-card-clickable" data-kpi-type="efectivo" title="Ver detalle de efectivo">
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
                            <div class="kpi-card pm-card kpi-card-clickable" data-kpi-type="transferencias" title="Ver detalle de transferencias">
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
                            <div class="kpi-card pm-card kpi-card-clickable" data-kpi-type="yape_plin" title="Ver detalle de Yape / Plin">
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

            <!-- ROW 4: FLUJO DE CAJA -->
            <div class="row align-items-stretch mb-4">
                <div class="col-12 mb-3">
                    <div class="kpi-card">
                        <div id="flujoCajaChart" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Detalle de KPI -->
<div class="modal fade" id="kpiDetailModal" tabindex="-1" aria-labelledby="kpiDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold" id="kpiDetailModalLabel">
                    <i class="fas fa-list me-2"></i><span id="kpiModalTitle">Detalle de Registros</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="kpiModalLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 small">Cargando desglose de registros...</p>
                </div>
                <div id="kpiModalContent" style="display: none;">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="small text-uppercase text-muted fw-bold">Fecha</th>
                                    <th class="small text-uppercase text-muted fw-bold">Comprobante / Desc.</th>
                                    <th class="small text-uppercase text-muted fw-bold">Cliente / Cat.</th>
                                    <th class="small text-uppercase text-muted fw-bold">Sede</th>
                                    <th class="small text-uppercase text-muted fw-bold text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="kpiModalTableBody">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <span class="text-muted small fw-bold">Total de registros: <span id="kpiModalTotalCount">0</span></span>
                        <div class="text-end">
                            <span class="text-dark fw-bold me-2">SUMA TOTAL:</span>
                            <span class="fs-5 font-weight-bold text-primary" id="kpiModalTotalAmount">S/ 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Modal & Click Handler
        const kpiModal = new bootstrap.Modal(document.getElementById('kpiDetailModal'));
        const modalTitle = document.getElementById('kpiModalTitle');
        const modalLoading = document.getElementById('kpiModalLoading');
        const modalContent = document.getElementById('kpiModalContent');
        const tableBody = document.getElementById('kpiModalTableBody');
        const totalCount = document.getElementById('kpiModalTotalCount');
        const totalAmount = document.getElementById('kpiModalTotalAmount');

        document.querySelectorAll('.kpi-card-clickable, .ribbon-item-clickable').forEach(elem => {
            elem.addEventListener('click', function() {
                const type = this.dataset.kpiType;
                if (!type) return;

                const year = "{{ $thisYear }}";
                const month = "{{ $thisMonth }}";
                const locationId = "{{ $locationId }}";

                modalTitle.innerText = "Cargando detalle...";
                modalLoading.style.display = 'block';
                modalContent.style.display = 'none';
                tableBody.innerHTML = '';
                kpiModal.show();

                const url = `{{ route('dashboard.details') }}?type=${type}&year=${year}&month=${month}&location_id=${locationId}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            modalTitle.innerText = data.title;
                            tableBody.innerHTML = '';
                            let sumTotal = 0;

                            if (!data.items || data.items.length === 0) {
                                tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-inbox me-2 fs-4 d-block mb-2"></i>No se encontraron registros para esta selección.</td></tr>`;
                            } else {
                                data.items.forEach(item => {
                                    const rawVal = parseFloat(item.monto.replace(/,/g, '')) || 0;
                                    sumTotal += rawVal;

                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td class="small font-weight-bold text-secondary">${item.fecha}</td>
                                        <td class="small font-weight-bold text-dark">${item.numero}</td>
                                        <td class="small text-muted">${item.cliente}</td>
                                        <td class="small text-muted">${item.sede}</td>
                                        <td class="small font-weight-bold text-end text-primary">S/ ${item.monto}</td>
                                    `;
                                    tableBody.appendChild(tr);
                                });
                            }

                            totalCount.innerText = data.items ? data.items.length : 0;
                            totalAmount.innerText = `S/ ${sumTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            
                            modalLoading.style.display = 'none';
                            modalContent.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        modalLoading.style.display = 'none';
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Error al obtener el detalle.</td></tr>`;
                        modalContent.style.display = 'block';
                    });
            });
        });

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

        // 4. Flujo de Caja (Highcharts)
        Highcharts.chart('flujoCajaChart', {
            chart: {
                type: 'column',
                backgroundColor: 'transparent'
            },
            title: {
                text: 'FLUJO DE CAJA',
                style: {
                    color: '#1d4ed8',
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            },
            xAxis: {
                categories: [
                    'VENTAS',
                    'GASTOS',
                    'FLUJO NETO',
                    'CUENTAS POR COBRAR',
                    'LIQUIDEZ',
                    'SALIDAS DE TESORERIA',
                    'SALDO DISPONIBLE REAL'
                ],
                labels: {
                    style: {
                        fontWeight: 'bold',
                        fontSize: '10px'
                    }
                },
                gridLineWidth: 0
            },
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    format: 'S/. {value:,.2f}'
                },
                gridLineColor: '#e2e8f0',
                gridLineWidth: 1,
                gridZIndex: 0
            },
            plotOptions: {
                column: {
                    colorByPoint: true,
                    borderRadius: 4,
                    dataLabels: {
                        enabled: true,
                        format: 'S/. {y:,.2f}',
                        style: {
                            textOutline: 'none',
                            fontWeight: 'bold',
                            color: 'white'
                        },
                        inside: true,
                        verticalAlign: 'bottom',
                        y: -10
                    }
                }
            },
            colors: [
                '#38761D', // VENTAS (Verde Oscuro)
                '#E06666', // GASTOS (Rojo)
                '#4A86E8', // FLUJO NETO (Azul)
                '#6D9EEB', // CUENTAS POR COBRAR (Azul Claro)
                '#00FF00', // LIQUIDEZ (Verde Brillante)
                '#4A86E8', // SALIDAS DE TESORERIA (Azul)
                '#6D9EEB'  // SALDO DISPONIBLE REAL (Azul Claro)
            ],
            legend: {
                enabled: false
            },
            series: [{
                name: 'Monto',
                data: [
                    {{ $ventasTotalesMes ?? 579989.04 }},
                    {{ $gastosTotales ?? 40518.31 }},
                    {{ ($ventasTotalesMes ?? 579989.04) - ($gastosTotales ?? 40518.31) }},
                    {{ $creditos ?? 190680.29 }},
                    {{ $ingresosCaja ?? 348790.44 }},
                    284416.00, // Ajustar según variable real
                    64374.44   // Ajustar según variable real
                ]
            }],
            credits: {
                enabled: false
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
