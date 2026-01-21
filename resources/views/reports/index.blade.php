@extends('template.index')

@section('header')
<h1>Indicadores de Gestión</h1>
<p>Reportes Variados</p>
@endsection

@section('content')

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="row row-cols-1">
                <div class="overflow-hidden d-slider1 ">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Fecha inicial</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request()->start_date }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">Fecha final</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request()->end_date }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="card-title">Ventas teóricas</h4>
                            </div>

                            <!-- Detalle credito -->
                            <div class="row">
                                <h4>S/7600.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="card-title">Ventas reales</h4>
                            </div>

                            <!-- Detalle credito -->
                            <div class="row">
                                <h4>S/9600.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-1">
                <!-- Card ventas crédito -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h5 class="card-title">Ventas Tradicionales</h5>
                                    <p class="card-text">Total ventas realizadas.</p>
                                </div>
                            </div>

                            <!-- Detalle tradicional -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Ventas teóricas: S/3600.00</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>Ventas reales: S/3700.00</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card ventas crédito -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h5 class="card-title">Ventas por Crédito</h5>
                                    <p class="card-text">Total ventas realizadas.</p>
                                </div>
                            </div>

                            <!-- Detalle credito -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Ventas teóricas: S/2600.00</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>Ventas reales: S/3600.00</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card ventas mayorista -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h5 class="card-title">Ventas por Mayorista</h5>
                                    <p class="card-text">Total ventas realizadas.</p>
                                </div>
                            </div>

                            <!-- Detalle mayorista -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Ventas teóricas: S/2600.00</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>Ventas reales: S/3600.00</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card ventas contrato -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h5 class="card-title">Ventas por Contrato</h5>
                                    <p class="card-text">Total ventas realizadas.</p>
                                </div>
                            </div>

                            <!-- Detalle contrato -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Ventas teóricas: S/2600.00</h6>
                                </div>
                                <div class="col-md-6">
                                    <h6>Ventas reales: S/3600.00</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compras Generales -->
            <div class="row row-cols-1 row-cols-md-2 g-4">

                <!-- Card de Compras por Tipo de producto (Stock) -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Compras por Tipo de Producto (Stock)</h5>
                            <p class="card-text">S/7200</p>
                        </div>
                    </div>
                </div>

                <!-- Card de Compras por Tipo de Combustible (Servicios) -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Compras por Tipo de Combustible (Servicios)</h5>
                            <p class="card-text">S/2600</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles -->
            <div class="row row-cols-1 row-cols-md-1 g-4">

                <!-- Card de Tabla de Platos Más Vendidos -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Combustible más vendido</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Vendida (Gal.)</th>
                                        <th>Total (S/.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <td>Premiun</td>
                                        <td>10</td>
                                        <td>S/ 100.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-4">

                <!-- Card de Dinero en Caja (Pastel) -->
                <div class="col">
                    <div class="card shadow-sm card-compact">
                        <div class="card-body">
                            <h5 class="card-title">Dinero en Caja por Métodos de Pago</h5>
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Card de Tipo de Comprobante (Barra) -->
                <div class="col">
                    <div class="card shadow-sm card-compact">
                        <div class="card-body">
                            <h5 class="card-title">Ventas por Tipo de Comprobante</h5>
                            <canvas id="voucherTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagos por Hacer -->
            <div class="row row-cols-1 row-cols-md-2 g-4">

                <!-- Card de Tabla de Pagos por Crédito -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Pagos por Crédito</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Monto Pendiente</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cliente 1</td>
                                        <td>S/ 50.00</td>
                                        <td>2025-05-17</td>
                                    </tr>
                                    <tr>
                                        <td>Cliente 2</td>
                                        <td>S/ 30.00</td>
                                        <td>2025-05-18</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card de Tabla de Pagos por Contrato -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Pagos por Contrato</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Monto Pendiente</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cliente 3</td>
                                        <td>S/ 100.00</td>
                                        <td>2022-12-12</td>
                                    </tr>
                                    <tr>
                                        <td>Cliente 4</td>
                                        <td>S/ 120.00</td>
                                        <td>2022-12-13</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card shadow-sm card-compact">
                        <div class="card-body">
                            <h5 class="card-title">Dinero Almacenado</h5>
                            <canvas id="moneyStoredChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Por Sede -->
            <div class="row">
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Por Sede</h5>
                            <select class="form-select" id="userTypeSelect" onchange="handleUserTypeChange()">
                                <option selected value="">Seleccionar Sede</option>
                                <option value="admin">Riojas</option>
                                <option value="cocina">Naranjos</option>
                                <option value="mantenimiento">Central</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Riojas -->
            <div id="adminSection" class="row" style="display: none;">

                <!-- Card Grande para Riojas -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Riojas</h3>
                            <p class="card-text">Crecimiento Ventas</p>
                        </div>

                        <!-- Fila 1: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Fin de Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Mes</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 1 -->

                        <!-- Fila 2: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Clima Laboral</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Rotación</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">NPS</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 2 -->

                        <!-- Fila 3: 2 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Retanbilidad</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Cumplimiento</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 3 -->

                    </div>
                </div>

            </div>

            <!-- Sección de Naranjos -->
            <div id="cocinaSection" class="row" style="display: none;">

                <!-- Card Grande para Naranjos -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Naranjos</h3>
                            <p class="card-text">Crecimiento Venta</p>
                        </div>

                        <!-- Fila 1: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Fin de Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Mes</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 1 -->

                        <!-- Fila 2: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Efectividad Almacen</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Devolución</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Rotación</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 2 -->

                    </div>
                </div>

            </div>

            <!-- Sección de Central -->
            <div id="mantenimientoSection" class="row" style="display: none;">

                <!-- Card Grande para Central -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Central</h3>
                            <p class="card-text">Crecimiento Venta</p>
                        </div>

                        <!-- Fila 1: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Fin de Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Semana</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Mes</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 1 -->

                        <!-- Fila 2: 3 Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Efectividad Almacen</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Devolución</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Rotación</h5>
                                        <p>...</p>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Fila 2 -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Dinero en Caja por Métodos de Pago
    const ctxPaymentMethods = document.getElementById('paymentMethodsChart');
    new Chart(ctxPaymentMethods, {
        type: 'pie',
        data: {
            labels: ['Efectivo', 'Yape', 'Tarjeta', 'Transferencia'],
            datasets: [{
                label: 'Métodos de Pago',
                data: [20, 30, 40, 10],
                backgroundColor: ['#ff6384', '#36a2eb', '#ff9f40', '#4bc7d2'],
                borderColor: ['#ff6384', '#36a2eb', '#ff9f40', '#4bc7d2'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Gráfico de Ventas por Tipo de Comprobante
    const ctxVoucherType = document.getElementById('voucherTypeChart');
    new Chart(ctxVoucherType, {
        type: 'bar',
        data: {
            labels: ['Factura', 'Boleta'],
            datasets: [{
                label: 'Ventas por Tipo de Comprobante',
                data: [50, 30],
                backgroundColor: '#36a2eb',
                borderColor: '#36a2eb',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Dinero Almacenado
    const ctxMoneyStored = document.getElementById('moneyStoredChart');
    new Chart(ctxMoneyStored, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Dinero Almacenado',
                data: [300, 500, 700, 600, 200, 460, 800, 900, 1000, 1200, 1500, 2000],
                backgroundColor: '#ff9f40',
                borderColor: '#ff9f40',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
<style>
    .card-compact {
        height: 300px;
        /* Ajusta la altura según sea necesario */
        overflow: hidden;
        /* Evita que el contenido se desborde */
    }

    .card-compact .card-body {
        padding: 10px;
        /* Ajusta el padding para un tamaño de contenido más pequeño */
    }

    .card-compact .card-title {
        font-size: 1rem;
        /* Ajusta el tamaño de la fuente del título para que se vea más pequeño */
    }

    .card-compact canvas {
        max-height: 250px;
        /* Limita la altura del canvas */
        width: 100%;
        /* Asegura que el canvas ocupe el 100% del contenedor */
    }
</style>

<script>
    // Al cargar la página, se deben mostrar todas las secciones
    window.onload = function() {
        handleUserTypeChange(); // Muestra las secciones al cargar la página
    };

    function handleUserTypeChange() {
        var selectedUserType = document.getElementById('userTypeSelect').value;

        // Si no se selecciona nada, mostrar todas las secciones
        if (!selectedUserType) {
            document.getElementById('adminSection').style.display = 'block';
            document.getElementById('cocinaSection').style.display = 'block';
            document.getElementById('mantenimientoSection').style.display = 'block';
        }

        // Si se selecciona un tipo de usuario, mostrar solo la sección correspondiente
        else {
            // Ocultar todas las secciones
            document.getElementById('adminSection').style.display = 'none';
            document.getElementById('cocinaSection').style.display = 'none';
            document.getElementById('mantenimientoSection').style.display = 'none';

            // Mostrar la sección correspondiente al tipo de usuario seleccionado
            if (selectedUserType === 'admin') {
                document.getElementById('adminSection').style.display = 'block';
            } else if (selectedUserType === 'cocina') {
                document.getElementById('cocinaSection').style.display = 'block';
            } else if (selectedUserType === 'mantenimiento') {
                document.getElementById('mantenimientoSection').style.display = 'block';
            }
        }
    }
</script>
@endsection