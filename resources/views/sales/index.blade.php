@extends('template.index')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <style>
        .form-control,
        .form-select {
            border: 1px solid #aaa;
        }

        .form-control-xs,
        .form-select-xs,
        .btn-xs {
            padding: 0.15rem 0.25rem;
            font-size: 0.875rem;
        }

        #tbl-products tr {
            cursor: pointer;
        }

        #tbl-products tr:hover {
            background-color: #f5f5f5;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .card-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .card-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem !important;
            }

            .bg-white {
                padding: 1rem !important;
            }

            h6 {
                font-size: 0.95rem;
            }

            .table-sm td,
            .table-sm th {
                padding: 0.3rem;
                font-size: 0.8rem;
            }
        }

        /* Botón flotante para abrir modal en móviles */
        .btn-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: none;
        }

        @media (max-width: 768px) {
            .btn-float {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .voucher-modal .form-label {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .payment-method-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .payment-method-item input[type="checkbox"] {
            flex-shrink: 0;
        }

        .payment-method-item label {
            flex-shrink: 0;
            margin: 0;
            min-width: 80px;
        }

        .payment-method-item input[type="text"] {
            flex: 1;
        }
    </style>
@endsection

@section('header')
    <h1>Modulo de Ventas</h1>
    <p>Modulo de gestión de ventas</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div id="chargeSection" class="bg-light text-dark fw-semibold p-3 rounded">
            <div class="row g-3">
                <!-- Columna IZQUIERDA: Productos, Contratos y Creditos -->
                <div class="col-md-5">
                    {{-- <div
                        class="bg-white p-3 rounded shadow-sm mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <h6 class="mb-3 text-center">Contómetro de Surtidores</h6>
                        <div class="btn-group d-flex justify-content-center" role="group"
                            aria-label="Basic outlined example">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#finalMeasurementModal" title="Cerrar Contometro">
                                <i class="bi bi-speedometer2"> Cerrar Contómetro</i>
                            </button>
                        </div>
                    </div> --}}

                    <!-- Card 1: Tipo de venta -->
                    <div class="bg-white p-3 rounded shadow-sm mb-3">
                        <div class="mb-3">
                            <label for="tipo-venta" class="form-label small">Tipo de venta</label>
                            <select id="tipo-venta" class="form-select form-select-sm">
                                <option value="directa">Venta Directa</option>
                                <option value="contrato">Contrato</option>
                            </select>
                            <input type="hidden" id="type_sale">
                        </div>
                        <div id="credit-checkbox-container" class="mb-3">
                            <label>
                                <input type="checkbox" id="is-credit-sale" class="form-check-input"> Venta a Crédito
                            </label>
                        </div>
                        <div class="mb-3 align-items-center d-flex justify-content-between">
                            <button type="button"
                                class="btn btn-sm btn-primary 
                            @if (auth()->user()->role->nombre === 'worker') d-none @endif
                            "
                                data-bs-toggle="modal" data-bs-target="#initialCashModal" title="Abrir Caja">
                                <i class="bi bi-cash"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#expenseModal" title="Egreso de Caja">
                                <i class="bi bi-pen"></i>
                            </button>

                            @if (auth()->user()->role_id != 3)
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#finalCashModal" title="Cerrar Caja">
                                <i class="bi bi-arrow-down-circle"></i>
                            </button>
                            @endif

                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#vaultModal" title="Enviar Bóveda">
                                <i class="bi bi-box"></i>
                            </button>

                            <button type="button"
                                class="btn btn-sm btn-success 
                                @if (auth()->user()->role->nombre === 'worker') d-none @endif
                            "
                                title="Exportar ventas por isla"
                                onclick="window.location.href='{{ route('sales.excelByIsle') }}'">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Card de búsqueda de cliente -->
                    <div id="cliente-search-card" class="bg-white p-3 rounded shadow-sm mb-3" style="display: none;">
                        <h6 class="mb-3 text-center">Buscar Cliente</h6>
                        <div class="mb-3">
                            <label class="form-label small">Cliente:</label>
                            <input type="text" id="search-client" class="form-control" placeholder="Buscar cliente...">
                            <input type="hidden" id="client_id" name="client_id">
                            <input type="hidden" id="current-agreement-id">
                            <input type="hidden" id="current-order-detail-id">
                        </div>
                    </div>

                    <!-- Card de productos para contrato/crédito -->
                    <div id="products-contract-credit" class="bg-white p-3 rounded shadow-sm mb-3" style="display: none;">
                        <h6 class="mb-3 text-center">Contratos del Cliente</h6>
                        <div style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-sm table-hover small">
                                <tbody id="tbl-products-contract"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Selector de Isla -->
                    <div id="isle-select-card" class="bg-white p-3 rounded shadow-sm mb-3" style="display: none;">
                        <h6 class="mb-3 text-center">Seleccione Isla</h6>
                        <div class="mb-2">
                            <select id="select-isle" class="form-select form-select-sm">
                                <option value="">-- Seleccione --</option>
                                @foreach ($isles ?? [] as $isle)
                                    <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Card 2: Productos para venta directa -->
                    <div id="products-direct-card" class="bg-white p-3 rounded shadow-sm mb-3">
                        <h6 class="mb-3 text-center">Productos Disponibles</h6>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover small">
                                <tbody id="tbl-products"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="bg-white p-3 rounded shadow-sm" style="min-height: 500px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Productos Agregados</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#voucherModal" id="btn-open-voucher">
                                <i class="bi bi-receipt"></i> Procesar Venta
                            </button>
                        </div>

                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-sm table-hover small">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Hora</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="tbl-order-items"></tbody>
                            </table>
                        </div>

                        <!-- Total flotante -->
                        <div class="total-display">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">TOTAL:</h5>
                                <h4 class="mb-0 text-primary">S/ <span id="total">0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para móviles -->
    <button type="button" class="btn btn-primary btn-float d-md-none" data-bs-toggle="modal"
        data-bs-target="#voucherModal" id="btn-float-voucher">
        <i class="bi bi-receipt fs-4"></i>
    </button>

    <!-- MODAL DE COMPROBANTE -->
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voucherModalLabel">
                        <i class="bi bi-receipt"></i> Datos del Comprobante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body voucher-modal">
                    <!-- Tipo de Comprobante -->
                    <div class="mb-3">
                        <label class="form-label">Comprobante</label>
                        <div class="d-flex gap-2 flex-wrap">
                            
                            <input type="radio" class="btn-check voucher_type" name="voucher_type" id="voucher_type_1" value="Ticket" checked>
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_1">Ticket de Venta</label>

                            <input type="radio" class="btn-check voucher_type" name="voucher_type" id="voucher_type_2" value="Boleta" style="display: none;">
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_2" style="display: none;">Boleta</label>

                            <input type="radio" class="btn-check voucher_type" name="voucher_type" id="voucher_type_3" value="Factura" style="display: none;">
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_3" style="display: none;">Factura</label>

                            <input type="checkbox" class="btn-check" id="venta_ficticia" name="venta_ficticia" value="1">
                            <label class="btn btn-outline-danger btn-sm ms-auto" for="venta_ficticia">
                                VENTA FICTICIA
                            </label>

                        </div>
                    </div>

                    <!-- Número de Comprobante -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">N° de Comprobante</label>
                        <input type="text" class="form-control" id="number" placeholder="-">
                    </div>

                    <!-- Documento -->
                    <div class="mb-3">
                        <label class="form-label">Documento</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="document" maxlength="11">
                            <button type="button" class="btn btn-primary" style="display: none;"
                                onclick="searchAPI('#document','#client','#address')">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <!--Numero de credito solo para venta a credito-->
                    <div class="mb-3" id="credit-number-section" style="display: none;">
                        <label class="form-label">N° de Crédito</label>
                        <input type="text" class="form-control" id="credit_number" placeholder="-">
                    </div>

                    <!-- Cliente -->
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <div class="input-group">
                            
                            <button class="btn btn-outline-secondary" type="button" 
                                id="btn_c_varios" 
                                onclick="document.getElementById('client_name').value = 'CLIENTES VARIOS'">
                                C. Varios
                            </button>

                            {{-- El input va DESPUÉS del botón para que quede a la derecha --}}
                            <input type="text" class="form-control" id="client_name" placeholder="-">
                            
                        </div>
                    </div>

                    <!-- Placa de Vehículo -->
                    <div class="mb-3">
                        <label class="form-label">Placa de Vehículo</label>
                        <input type="text" class="form-control" id="vehicle_plate" placeholder="-">
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="address" placeholder="-">
                    </div>

                    <!-- Orden -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Orden</label>
                        <input type="text" class="form-control" id="orden" placeholder="-">
                    </div>

                    <!-- Área -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Área</label>
                        <input type="text" class="form-control" id="area" placeholder="-">
                    </div>

                    <!-- Forma de pago (solo para venta directa) -->
                    <div class="mb-3" id="payment-methods-section">
                        <label class="form-label fw-bold">Forma de pago</label>
                        <table class="w-100 small">
                            @foreach ($payment_methods as $index => $payment_method)
                                <tr class="payment-method-item">
                                    <td width="150">
                                        <input type="checkbox" class="form-check-input me-2"
                                            onchange="togglePaymentMethod(event, '#amount_{{ $payment_method->id }}')"
                                            id="cbx_amount_{{ $payment_method->id }}" {{ $index == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $payment_method->name }}</label>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                            id="amount_{{ $payment_method->id }}" oninput="calculateDifference(event)"
                                            {{ $index == 0 ? '' : 'disabled' }} placeholder="0.00">
                                    </td>
                                </tr>
                            @endforeach
                            
                        </table>
                    </div>

                    <div id="vuelto-adicional-container" class="mb-3" style="display: none;">
                        <label>
                            <input type="checkbox" id="is-vuelto-adicional" class="form-check-input"> Vuelto adicional
                        </label>
                    </div>
                    <!-- Vuelto adicional -->
                    <div id="vuelto-adicional-section" style="display: none;" class="mb-3">
                        <label class="form-label">Vuelto adicional</label>
                        <input type="number" step="0.01" class="form-control" name="adicional" id="adicional" placeholder="0.00">
                    </div>

                    <!-- Resumen del Total -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total de la Venta:</strong>
                            <h5 class="mb-0">S/ <span id="charge-total">0.00</span></h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn-save">
                        <i class="bi bi-check-circle"></i> Guardar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cerrar Contómetro -->
    <div class="modal fade" id="finalMeasurementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Contómetro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Isla:</label>
                    <select id="select-isle-measurement" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                    </select>

                    <label>Surtidor:</label>
                    <select id="select-pump-measurement" class="form-select mb-2">
                        <option value="">Seleccione un surtidor</option>
                    </select>

                    <label>Lado:</label>
                    <input type="text" class="form-control mb-2" id="pump_side" disabled>

                    <label>Valor Inicial:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="initial_measurement_value"
                        readonly>

                    <label>Valor Final:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="final_measurement_value">

                    <label>Valor Teórico:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="theorical_measurement_value"
                        readonly>

                    <label>Diferencia:</label>
                    <input type="number" step="0.001" class="form-control" id="difference_measurement_value"
                        readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-measurement">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Abrir Caja -->
    <div class="modal fade" id="initialCashModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Abrir Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Monto Inicial:</label>
                    <input type="number" step="0.01" class="form-control" id="initial_cash_amount">
                    <label>Isla:</label>
                    <select id="select-isle-initial" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                        @foreach ($isles ?? [] as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-initial">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cerrar Caja -->
    <div class="modal fade" id="finalCashModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Monto Inicial:</label>
                        <input type="number" step="0.01" class="form-control" id="initial_cash_amount_final"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>+ Ventas en Efectivo:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_sales_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>- Egresos del Día:</label>
                        <input type="number" step="0.01" class="form-control" id="expenses_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>- Adicional (Vuelto):</label>
                        <input type="number" step="0.01" class="form-control" id="adicional_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Monto Calculado:</label>
                        <input type="number" step="0.01" class="form-control" id="real_cash_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Isla:</label>
                        <select id="select-isle-final" class="form-select mb-2">
                            <option value="">Seleccione una isla</option>
                            @foreach ($isles ?? [] as $isle)
                                <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Monto Final:</label>
                        <input type="number" step="0.01" class="form-control" id="final_cash_amount"
                            placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-final">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Egreso -->
    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Egreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Isla:</label>
                        <select id="select-isle-expense" class="form-select mb-2">
                            <option value="">Seleccione una isla</option>
                            @foreach ($isles ?? [] as $isle)
                                <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label class="form-label">Monto Caja Chica:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_amount" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto:</label>
                        <input type="number" step="0.01" class="form-control" id="expense_amount"
                            placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <input type="text" class="form-control" id="expense_description"
                            placeholder="Descripción del egreso">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-expenses">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bóveda -->
    <div class="modal fade" id="vaultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar a Bóveda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- CORRECCIÓN: Cambiado ID a 'select-isle-vault' --}}
                    <select id="select-isle-vault" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                        @foreach ($isles ?? [] as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                        @endforeach
                    </select>
                    <div class="mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Total Caja Chica:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_amount_acumulated"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Monto a enviar:</label>
                        <input type="number" step="0.01" class="form-control" id="vault_amount" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-vault">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal agregar productos-->
    <div class="modal fade" id="addProductsModal" tabindex="-1" aria-labelledby="addProductsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductsModalLabel">
                        <i class="bi bi-cart-plus me-2"></i>Agregar Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Campos ocultos -->
                    <input type="hidden" id="product_id">
                    <input type="hidden" id="tank_id">
                    <input type="hidden" id="pump_id">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted mb-1">
                                    <i class="bi bi-box-seam me-1"></i>Producto:
                                </label>
                                <input type="text" class="form-control form-control-sm fw-semibold" id="lbl-name"
                                    disabled>
                            </div>
                            <div class="row g-2">
                                <div class="col-8">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bi bi-currency-dollar me-1"></i>Precio Unitario:
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/</span>
                                        <input type="text" class="form-control text-end fw-semibold" id="lbl-price"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-4 d-flex align-items-end">
                                    <div class="form-check form-switch w-100">
                                        <input class="form-check-input" type="checkbox" id="checkPrecioM"
                                            role="switch">
                                        <label class="form-check-label small" for="checkPrecioM">
                                            <i class="bi bi-tag me-1"></i>Mayorista
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modo de Ingreso -->
                    <div class="card border-primary mb-3">
                        <div class="card-body p-3">
                            <label class="form-label small fw-bold mb-2 d-block">
                                <i class="bi bi-calculator me-1"></i>Modo de Ingreso:
                            </label>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small">
                                    <i class="bi bi-cash-coin me-1"></i>Por Subtotal
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggleGalonesSubtotal"
                                        role="switch" value="false" checked>
                                </div>
                                <span class="small">
                                    <i class="bi bi-droplet me-1"></i>Por Galones
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad / Subtotal -->
                    <div class="row g-3 mb-3">
                        <div class="col-12" id="galonesSection">
                            <label class="form-label small fw-bold">
                                <i class="bi bi-droplet-fill me-1 text-primary"></i>Cantidad (Galones):
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.001" class="form-control text-end" id="txt-quantity"
                                    value="1" min="0.001" oninput="calcularSubtotal()" placeholder="0.000">
                                <span class="input-group-text">gal</span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Ingrese la cantidad en galones
                            </small>
                        </div>
                        <div class="col-12" id="subtotalSection" style="display: none;">
                            <label class="form-label small fw-bold">
                                <i class="bi bi-cash-stack me-1 text-success"></i>Subtotal (S/):
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" class="form-control text-end" id="txt-subtotal"
                                    value="1" min="0.01" oninput="calcularGalones()" placeholder="0.00">
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Ingrese el monto total a cobrar
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="addProductDirect()">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('scripts')
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/TextoComoTabla.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var expenseModal = document.getElementById('expenseModal');
            if (expenseModal) {
                expenseModal.addEventListener('shown.bs.modal', function() {
                    var select = document.getElementById('select-isle-expense');
                    if (select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                });
            }

            var finalCashModal = document.getElementById('finalCashModal');
            if (finalCashModal) {
                finalCashModal.addEventListener('hidden.bs.modal', function(event) {
                    document.getElementById('select-isle-final').value = "";
                    document.getElementById('final_cash_amount').value = "";
                });
            }

            const checkVentaFicticia = document.getElementById('venta_ficticia');
            const inputCliente = document.getElementById('client_name');
            const inputPlaca = document.getElementById('vehicle_plate');
            const inputDocumento = document.getElementById('document');
            const btnCVarios = document.getElementById('btn_c_varios');
            const paymentSection = document.getElementById('payment-methods-section');

            if (checkVentaFicticia) {
                checkVentaFicticia.addEventListener('change', function() {
                    const paymentInputs = paymentSection.querySelectorAll('input');

                    if (this.checked) {
                        inputCliente.value = 'CALIBRACIÓN';
                        inputCliente.disabled = true;                        
                        inputPlaca.value = '0-0';
                        inputPlaca.disabled = true;
                        
                        if (inputDocumento) {
                            inputDocumento.value = '00000000';
                            inputDocumento.disabled = true;
                        }

                        if (btnCVarios) {
                            btnCVarios.disabled = true;
                        }

                        paymentInputs.forEach(input => {
                            input.disabled = true;
                        });

                    } else {
                        inputCliente.value = '';
                        inputCliente.disabled = false;                        
                        inputPlaca.value = '';
                        inputPlaca.disabled = false;
                        
                        if (inputDocumento) {
                            inputDocumento.value = '';
                            inputDocumento.disabled = false;
                        }

                        if (btnCVarios) {
                            btnCVarios.disabled = false;
                        }

                        paymentInputs.forEach(input => {
                            if (input.type === 'checkbox') {
                                input.disabled = false;
                            } else if (input.type === 'number') {
                                let relatedCheckboxId = 'cbx_' + input.id;
                                let relatedCheckbox = document.getElementById(relatedCheckboxId);

                                if (relatedCheckbox && relatedCheckbox.checked) {
                                    input.disabled = false; // Si estaba marcado antes, lo habilitamos
                                } else {
                                    input.disabled = true; // Si no, se queda bloqueado
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
    
    <script>
        var isles = @json($isles ?? []);
        var pumps = @json($pumps ?? []);
        var assignedIsle = @json($assignedIsle ?? null);

        // Ocultar sidebar automáticamente al cargar la págin

        var clients = @json($clients);
        var paymentMethods = @json($payment_methods);

        $(document).ready(function() {
            if ($('#tipo-venta').val() === 'directa') {
                // Si el usuario NO tiene isla asignada, mostrar selector para elegir isla
                if (!assignedIsle) {
                    $('#isle-select-card').show();
                } else {
                    // Ocultar selector si el usuario tiene isla asignada
                    $('#isle-select-card').hide();
                    $('#select-isle').val(assignedIsle);
                }

                loadProductsBySede();
                $('#credit-checkbox-container').show(); // Mostrar checkbox para venta directa
            } else {
                $('#credit-checkbox-container').hide(); // Ocultar checkbox para otros tipos
                $('#is-credit-sale').prop('checked', false); // Desmarcar checkbox
            }

            if ($('#toggleGalonesSubtotal').val() == 'false') {
                $('#galonesSection').hide();
                $('#subtotalSection').show();
            } else {
                $('#galonesSection').show();
                $('#subtotalSection').hide();
            }
        })

        $('#btn-save').click(function() {
            guardarVenta();
        });
        // Ocultar/mostrar métodos de pago cuando se marca el checkbox de crédito
        $('#is-credit-sale').on('change', function() {
            if ($(this).is(':checked')) {
                // Ocultar sección de métodos de pago
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
                $('#credit-number-section').show();
                $('#is-vuelto-adicional').prop('checked', false);
                $('#vuelto-adicional-container').hide();
            } else {
                // Mostrar métodos de pago
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
                $('#credit-number-section').hide();
                $('#credit_number').val('');
                $('#vuelto-adicional-container').show();
            }
        });
            $('#is-vuelto-adicional').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#vuelto-adicional-section').show();
                    calculateDifference();
                } else {
                    $('#vuelto-adicional-section').hide();
                    $('#adicional').val('0.00');
                }
            });
            
            $('input[id^="amount_"]').on('input', function() {
                calculateDifference();
        });
        (function() {
            let tipoAnterior = $('#tipo-venta').val();

            function aplicarTipoVenta(tipoVenta) {
                const map = {
                    directa: 0,
                    contrato: 1
                };
                $('#type_sale').val(map[tipoVenta]);
                $('#cliente-search-card').hide();
                $('#products-contract-credit').hide();
                $('#products-direct-card').hide();
                // Por defecto ocultar selector de islas; sólo mostrar para venta directa
                $('#isle-select-card').hide();
                $('#quick-add-product-subtotal').hide();
                $('#tbl-products').empty();
                $('#tbl-products-contract').empty();
                $('#orden').val('');
                $('#area').val('');
                $('#current-order-detail-id').val('');

                if (tipoVenta === 'directa') {
                    // Mostrar productos directos y selector de islas para venta directa
                    $('#products-direct-card').show();
                    $('#credit-checkbox-container').show(); // Mostrar checkbox
                    $('#is-credit-sale').prop('checked', false); // Desmarcar por defecto
                    $('#payment-methods-section').show(); // Mostrar pagos por defecto
                    $('#paga-con-section').show();
                    if (!assignedIsle) {
                        $('#isle-select-card').show();
                    } else {
                        $('#isle-select-card').hide();
                        $('#select-isle').val(assignedIsle);
                    }
                    loadProductsBySede();
                } else {
                    // Para contrato mostramos búsqueda de cliente
                    $('#cliente-search-card').show();
                    $('#isle-select-card').hide();
                    $('#credit-checkbox-container').hide(); // Ocultar checkbox
                    $('#is-credit-sale').prop('checked', false);
                    resetClientSearch();
                }

                tipoAnterior = tipoVenta;
            }

            $('#tipo-venta').on('focus', function() {
                tipoAnterior = this.value;
            });

            $('#tipo-venta').on('change', function() {
                const nuevoTipo = this.value;
                const hayProductos = $('#tbl-order-items tr').length > 0;

                if (hayProductos) {
                    Swal.fire({
                        title: '¿Cambiar tipo de venta?',
                        text: 'Se eliminarán todos los productos cargados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            $('#tbl-order-items').empty();
                            $('#total').text('0.00');

                            aplicarTipoVenta(nuevoTipo);
                            ToastMessage.fire({
                                text: 'Tipo de venta cambiado y productos limpiados.'
                            });
                        } else {
                            $('#tipo-venta').val(tipoAnterior);
                        }
                    });
                } else {
                    aplicarTipoVenta(nuevoTipo);
                }
            });
        })();
        

        function resetClientSearch() {
            $('#search-client').val('');
            $('#client_id').val('');

            $('#search-client').autocomplete({
                source: function(request, response) {
                    var results = $.map(clients, function(item) {
                        // Validar que las propiedades necesarias existan y no sean null
                        if (!item || !item.business_name || !item.document) {
                            return null;
                        }

                        var businessName = item.business_name.toString().toLowerCase();
                        var document = item.document.toString();
                        var searchTerm = request.term.toLowerCase();

                        if (businessName.includes(searchTerm) || document.includes(request.term)) {
                            return {
                                label: `${item.business_name}`,
                                value: item.business_name,
                                id: item.id,
                                document: item.document
                            };
                        }
                    });
                    response(results);
                },
                appendTo: '.container-fluid',
                select: function(event, ui) {
                    $('#client_id').val(ui.item.id);

                    // Llenar también los campos del panel derecho
                    $('#document').val(ui.item.document);
                    $('#client').val(ui.item.value);

                    // Cargar contratos específicos del cliente solo si el ID no es nulo
                    if (ui.item.id && ui.item.id !== null) {
                        cargarContratosCliente(ui.item.id);
                    }
                },
                minLength: 2
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append(`<div class="d-flex justify-content-between">
                            <span>${item.label}</span>
                         </div>`)
                    .appendTo(ul);
            };
        }

        function loadProductsBySede() {
            // 1. Determinar isla seleccionada
            const selectedIsle = assignedIsle || $('#select-isle').val();
            
            // Referencias al botón y mensajes
            const btnProcesar = $('#btn-open-voucher');
            const containerBtn = btnProcesar.parent(); // El div contenedor
            
            // Limpiar alertas de bloqueo previas
            containerBtn.find('.alert-caja-cerrada').remove();

            if (!selectedIsle) {
                $('#tbl-products').html('<div class="alert alert-info text-center">Seleccione una isla...</div>');
                btnProcesar.prop('disabled', true); // Bloquear si no hay isla
                return;
            }

            // --- NUEVA LÓGICA: VERIFICAR ESTADO DE CAJA (AJAX) ---
            // Bloqueamos temporalmente mientras consulta
            btnProcesar.prop('disabled', true); 

            // Construimos la URL dinámicamente. 
            // Nota: Asegúrate de tener una variable base o usar replace si estás en un archivo .js externo
            let urlCheck = "{{ route('cash_closes.check_status', ':id') }}";
            urlCheck = urlCheck.replace(':id', selectedIsle);

            $.ajax({
                url: urlCheck,
                method: 'GET',
                success: function(response) {
                    if (response.isOpen) {
                        // CAJA ABIERTA: Habilitar botón
                        btnProcesar.prop('disabled', false);
                    } else {
                        // CAJA CERRADA: Mantener bloqueado y mostrar mensaje
                        btnProcesar.prop('disabled', true);
                        containerBtn.append(`
                            <small class="text-danger d-block mt-1 alert-caja-cerrada">
                                <i class="bi bi-lock-fill"></i> Caja cerrada o no iniciada para esta isla.
                            </small>
                        `);
                    }
                },
                error: function() {
                    console.error('Error verificando estado de caja');
                }
            });
            // -----------------------------------------------------

            // TU CÓDIGO ORIGINAL PARA CARGAR PRODUCTOS CONTINÚA AQUÍ...
            $.ajax({
                url: "{{ route('products.prices') }}",
                method: 'GET',
                success: function(data) {
                    $('#tbl-products').empty(); 

                    // Filtrar bombas para la isla seleccionada
                    const pumpsForIsle = Array.isArray(pumps) ? pumps.filter(p => parseInt(p.isle_id) ===
                        parseInt(selectedIsle) && (p.deleted == 0 || p.deleted === false)) : [];

                    if (pumpsForIsle.length === 0) {
                        $('#tbl-products').append(
                            '<div class="alert alert-warning text-center">No hay surtidores configurados para esta isla</div>'
                        );
                        return;
                    }

                    // ... (RESTO DE TU CÓDIGO DE CREACIÓN DE TARJETAS, COLORES, ETC. SE MANTIENE IGUAL) ...
                    
                    // Crear estructura de dos columnas: LADO 1 y LADO 2
                    const mainContainer = $('<div class="row">');
                    // ... (Copiar todo tu código de renderizado visual aquí) ...
                    const lado1Container = $('<div class="col-md-6"><h5 class="text-center mb-3 text-primary">LADO 1</h5><div class="pumps-column"></div></div>');
                    const lado2Container = $('<div class="col-md-6"><h5 class="text-center mb-3 text-primary">LADO 2</h5><div class="pumps-column"></div></div>');
                    
                    const lado1Pumps = pumpsForIsle.filter(p => parseInt(p.side) === 1);
                    const lado2Pumps = pumpsForIsle.filter(p => parseInt(p.side) === 2);

                                        function getProductColor(productName) {
                        const name = productName.toLowerCase();
                        if (name.includes('diesel') || name.includes('diésel') || name.includes('db5')) {
                            return {
                                bg: '#2c3e50',
                                text: '#ffffff'
                            }; // Azul oscuro
                        } else if (name.includes('premium') || name.includes('97')) {
                            return {
                                bg: '#e74c3c',
                                text: '#ffffff'
                            }; // Rojo
                        } else if (name.includes('regular') || name.includes('90') || name.includes('84')) {
                            return {
                                bg: '#27ae60',
                                text: '#ffffff'
                            }; // Verde
                        } else if (name.includes('gasolina')) {
                            return {
                                bg: '#3498db',
                                text: '#ffffff'
                            }; // Azul
                        } else if (name.includes('kerosene') || name.includes('keroseno')) {
                            return {
                                bg: '#f39c12',
                                text: '#ffffff'
                            }; // Naranja
                        } else {
                            return {
                                bg: '#95a5a6',
                                text: '#ffffff'
                            }; // Gris por defecto
                        }
                    }

                    // Función para crear tarjeta de surtidor
                    function crearTarjetaSurtidor(pump, data) {
                        const pumpCard = $(`
                            <div class="mb-4">
                                <div class="text-center mb-2">
                                    <span class="badge bg-secondary" style="font-size: 16px; padding: 8px 12px;">
                                        <i class="bi bi-fuel-pump"></i> ${pump.name || 'Surtidor ' + pump.id}
                                    </span>
                                </div>
                                <div class="products-buttons d-grid gap-2">
                                </div>
                            </div>
                        `);

                        let found = false;

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function(tank) {
                                if (Array.isArray(tank.products) && tank.products.length > 0) {
                                    tank.products.forEach(function(product) {
                                        if (product.id == pump.product_id) {
                                            found = true;

                                            const colors = getProductColor(product.name);

                                            const productBtn = $(`
                                                <button type="button" class="btn btn-lg shadow" 
                                                    style="background: ${colors.bg}; color: ${colors.text}; border: none; padding: 20px; font-size: 18px; font-weight: bold; transition: all 0.2s ease;"
                                                    data-product-id="${product.id}" 
                                                    data-product-name="${product.name}"
                                                    data-price="${parseFloat(product.price).toFixed(2)}"
                                                    data-observations="${product.observations || ''}" 
                                                    data-tank-id="${tank.id}" 
                                                    data-pump-id="${pump.id}" 
                                                    data-order-detail-id="${product.order_detail_id}">
                                                    <div>${product.name}</div>
                                                    <div style="font-size: 24px; margin-top: 5px;">S/ ${parseFloat(product.price || 0).toFixed(2)}</div>
                                                </button>
                                            `);

                                            // Efectos hover
                                            productBtn.hover(
                                                function() {
                                                    $(this).css({
                                                        'transform': 'translateY(-3px)',
                                                        'box-shadow': '0 6px 12px rgba(0,0,0,0.4)',
                                                        'opacity': '0.9'
                                                    });
                                                },
                                                function() {
                                                    $(this).css({
                                                        'transform': 'translateY(0)',
                                                        'box-shadow': '',
                                                        'opacity': '1'
                                                    });
                                                }
                                            );

                                            productBtn.on('click', function() {
                                                // Efecto de click
                                                $(this).css('transform', 'scale(0.95)');
                                                setTimeout(() => {
                                                    $(this).css('transform',
                                                        'scale(1)');
                                                }, 100);

                                                const productId = $(this).data(
                                                    'product-id');
                                                const productName = $(this).data(
                                                    'product-name');
                                                const price = $(this).data('price');
                                                const observations = $(this).data(
                                                    'observations');
                                                const tankId = $(this).data('tank-id');
                                                const orderDetailId = $(this).data(
                                                    'order-detail-id');
                                                const pumpId = $(this).data('pump-id');

                                                // Llenar el modal con los datos del producto
                                                $('#addProductsModal #product_id').val(
                                                    productId);
                                                $('#addProductsModal #tank_id').val(
                                                    tankId || '');
                                                $('#addProductsModal #pump_id').val(
                                                    pumpId || '');
                                                $('#addProductsModal #lbl-name').val(
                                                    productName);
                                                $('#addProductsModal #lbl-price').val(
                                                    price);

                                                // Guardar el precio original
                                                $('#addProductsModal #lbl-price').data(
                                                    'original-price', price);

                                                // Guardar order_detail_id si existe
                                                if (orderDetailId) {
                                                    $('#addProductsModal #product_id')
                                                        .data('order-detail-id',
                                                            orderDetailId);
                                                } else {
                                                    $('#addProductsModal #product_id')
                                                        .removeData('order-detail-id');
                                                }

                                                // Resetear valores
                                                $('#addProductsModal #txt-quantity')
                                                    .val(1);
                                                $('#addProductsModal #txt-subtotal')
                                                    .val(price);
                                                $('#addProductsModal #checkPrecioM')
                                                    .prop('checked', false);
                                                $('#addProductsModal #toggleGalonesSubtotal')
                                                    .prop('checked', false);

                                                // Abrir el modal
                                                $('#addProductsModal').modal('show');
                                            });
                                            $('#quick-add-product').hide();
                                            $('#quick-add-product-subtotal').hide();

                                            pumpCard.find('.products-buttons').append(
                                                productBtn);
                                        }
                                    });
                                }
                            });
                        }

                        if (!found) {
                            pumpCard.find('.products-buttons').append(
                                `<div class="text-center text-muted small py-2">
                                    <i class="bi bi-exclamation-circle"></i> Sin producto
                                </div>`
                            );
                        }

                        return pumpCard;
                    }

                    lado1Pumps.forEach(p => lado1Container.find('.pumps-column').append(crearTarjetaSurtidor(p, data)));
                    lado2Pumps.forEach(p => lado2Container.find('.pumps-column').append(crearTarjetaSurtidor(p, data)));

                    mainContainer.append(lado1Container).append(lado2Container);
                    $('#tbl-products').append(mainContainer);

                    ToastMessage.fire({ text: `${pumpsForIsle.length} surtidores cargados` });
                },
                error: function(err) {
                    console.error('Error al cargar productos:', err);
                    ToastError.fire({ title: 'Error', text: 'No se pudieron cargar los productos' });
                }
            });
        }

        function cargarContratosCliente(clienteId) {
            const tipoVenta = $('#tipo-venta').val();

            $.ajax({
                url: "{{ route('contracts.by.client') }}",
                method: 'GET',
                data: {
                    client_id: clienteId,
                    type: tipoVenta
                },
                success: function(data) {
                    $('#tbl-products-contract').empty();

                    if (data.length > 0) {
                        // Mostrar lista de contratos/créditos
                        data.forEach(function(agreement) {
                            const fechaFormateada = new Date(agreement.date).toLocaleDateString(
                                'es-PE');
                            const estadoTexto = agreement.status == 0 ? 'Activo' : 'Inactivo';
                            const estadoClass = agreement.status == 0 ? 'text-success' : 'text-danger';

                            $('#tbl-products-contract').append(`
                            <tr onclick="cargarProductosContrato(${agreement.id})" style="cursor: pointer;" class="table-row-hover">
                                <td>
                                    <strong>${tipoVenta === 'contrato' ? 'Contrato' : 'Crédito'} #${agreement.id}</strong><br>
                                    <small class="text-muted">Fecha: ${fechaFormateada}</small>
                                </td>
                                <td class="text-end">
                                    <span class="${estadoClass}"><strong>${estadoTexto}</strong></span><br>
                                    <small class="text-muted">Total: S/ ${parseFloat(agreement.total).toFixed(2)}</small>
                                </td>
                            </tr>
                        `);
                        });

                        // 👉 NO OCULTAR la búsqueda, solo mostrar el card de contratos
                        $('#products-contract-credit').show();

                        // Cambiar título del card
                        $('#products-contract-credit h6').text(tipoVenta === 'contrato' ?
                            'Contratos del Cliente' : 'Créditos del Cliente');

                        ToastMessage.fire({
                            title: 'Cliente Seleccionado',
                            text: `Se encontraron ${data.length} ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'}`
                        });

                    } else {
                        $('#tbl-products-contract').append(
                            `<tr><td colspan="2" class="text-center text-muted">No hay ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} disponibles para este cliente</td></tr>`
                        );
                        $('#products-contract-credit').show();

                        ToastMessage.fire({
                            title: 'Sin datos',
                            text: `Este cliente no tiene ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} activos`
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar contratos del cliente:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: `No se pudieron cargar los ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} del cliente`
                    });
                }
            });
        }


        function cargarProductosContrato(agreementId) {
            // Guardar el agreement_id actual para poder volver
            $('#current-agreement-id').val(agreementId);

            $.ajax({
                url: "{{ route('orders.by.contract') }}",
                method: 'GET',
                data: {
                    agreement_id: agreementId
                },
                success: function(data) {
                    $('#tbl-products-contract').empty();

                    if (data.orders && data.orders.length > 0) {
                        // Mostrar órdenes del contrato
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaContratos()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de ${$('#tipo-venta').val() === 'contrato' ? 'contratos' : 'créditos'}
                                </button>
                            </td>
                        </tr>
                    `);

                        data.orders.forEach(function(order) {
                            const fechaFormateada = new Date(order.date).toLocaleDateString('es-PE');

                            $('#tbl-products-contract').append(`
                            <tr onclick="cargarProductosOrden(${order.id})" style="cursor: pointer;" class="table-row-hover">
                                <td>
                                    <strong>Orden #${order.number}</strong><br>
                                    <small class="text-muted">Fecha: ${fechaFormateada}</small>
                                </td>
                                <td class="text-end">
                                    <small class="text-muted">Total: S/ ${parseFloat(order.total).toFixed(2)}</small>
                                </td>
                            </tr>
                        `);
                        });

                        // Cambiar título del card
                        $('#products-contract-credit h6').text(
                            `Órdenes del ${$('#tipo-venta').val() === 'contrato' ? 'Contrato' : 'Crédito'} #${agreementId}`
                        );

                        ToastMessage.fire({
                            text: `${data.orders.length} órdenes cargadas del ${$('#tipo-venta').val()}`
                        });

                    } else {
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaContratos()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de ${$('#tipo-venta').val() === 'contrato' ? 'contratos' : 'créditos'}
                                </button>
                            </td>
                        </tr>
                        <tr><td colspan="3" class="text-center text-muted">No hay órdenes disponibles en este ${$('#tipo-venta').val()}</td></tr>
                    `);

                        ToastMessage.fire({
                            text: `No hay órdenes disponibles en este ${$('#tipo-venta').val()}`
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar órdenes del contrato:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar las órdenes del contrato/crédito'
                    });
                }
            });
        }

        function cargarProductosOrden(orderId) {
            $.ajax({
                url: "{{ route('products.by.order', ':orderId') }}".replace(':orderId', orderId),
                method: 'GET',
                data: {
                    order_id: orderId
                },
                success: function(data) {
                    $('#tbl-products-contract').empty();

                    // Llenar campo de orden con el número de la orden
                    if (data.order && data.order.number) {
                        $('#orden').val(data.order.number);
                    }

                    if (data.tanks && data.tanks.length > 0) {
                        // Agregar botón para volver a la lista de órdenes
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaOrdenes()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de órdenes
                                </button>
                            </td>
                        </tr>
                    `);

                        // Agrupar tanques por producto
                        const productosAgrupados = {};

                        // Determinar isla seleccionada (si el usuario tiene una asignada o seleccionó una)
                        const selectedIsleForOrder = assignedIsle || $('#select-isle').val() || null;

                        data.tanks.forEach(function(tank) {
                            const product = tank.product;
                            if (!product) return;

                            if (!productosAgrupados[product.id]) {
                                productosAgrupados[product.id] = {
                                    product: product,
                                    tanks: []
                                };
                            }

                            // Intentar obtener surtidor/isla a partir del arreglo global `pumps` si la respuesta no los incluye
                            var pumpName = '';
                            var pumpId = null;
                            var isleName = '';
                            var isleId = null;

                            try {
                                if (Array.isArray(pumps) && pumps.length > 0) {
                                    // Buscar bombas que sirvan este producto
                                    var candidatePumps = pumps.filter(function(p) {
                                        return parseInt(p.product_id) === parseInt(product
                                            .id) && (p.deleted == 0 || p.deleted === false);
                                    });

                                    // Si hay una isla seleccionada, priorizar bombas de esa isla
                                    if (selectedIsleForOrder) {
                                        var pumpInIsle = candidatePumps.find(function(cp) {
                                            return parseInt(cp.isle_id) === parseInt(
                                                selectedIsleForOrder);
                                        });
                                        if (pumpInIsle) {
                                            candidatePumps = [pumpInIsle];
                                        }
                                    }

                                    if (candidatePumps.length > 0) {
                                        var chosen = candidatePumps[0];
                                        pumpName = chosen.name || chosen.display_name || '';
                                        pumpId = chosen.id || chosen.ID || null;

                                        // Buscar nombre de isla en el arreglo global `isles` si existe
                                        if (Array.isArray(isles) && isles.length > 0) {
                                            var isleObj = isles.find(function(i) {
                                                return parseInt(i.id) === parseInt(chosen
                                                    .isle_id);
                                            });
                                            if (isleObj) {
                                                isleName = isleObj.name || '';
                                                isleId = isleObj.id || null;
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.error('Error buscando pump/isle desde globals:', e);
                            }

                            productosAgrupados[product.id].tanks.push({
                                tank_id: tank.id,
                                tank_name: tank.name,
                                stored_quantity: tank.stored_quantity,
                                isle_name: isleName || tank.isle_name || (tank.isle && tank.isle
                                    .name) || '',
                                isle_id: isleId || tank.isle_id || (tank.isle && tank.isle
                                    .id) || null,
                                pump_name: pumpName || tank.pump_name || (tank.pump && tank.pump
                                    .name) || '',
                                pump_id: pumpId || tank.pump_id || (tank.pump && tank.pump
                                    .id) || null
                            });
                        });

                        // Mostrar productos agrupados con sus tanques usando el estilo de surtidores
                        Object.values(productosAgrupados).forEach(function(grupo) {
                            const product = grupo.product;

                            // (No se muestra cabecera por producto para mantener formato Isla - Surtidor - Producto)

                            // Listar tanques para este producto con dos columnas (nombre + precio)
                            grupo.tanks.forEach(function(tank) {
                                // Obtener nombre de isla y surtidor si vienen en la respuesta (varias formas posibles)
                                var isleName = tank.isle_name || (tank.isle && tank.isle
                                        .name) || tank.isle || tank.island_name || tank
                                    .island ||
                                    '';
                                var pumpName = tank.pump_name || (tank.pump && tank.pump
                                        .name) || tank.pump || tank.surtidor_name || tank
                                    .surtidor || '';

                                var extraLine = '';
                                if (isleName || pumpName) {
                                    extraLine = `<small class="text-muted d-block">` + (
                                        isleName ? `Isla: ${isleName}` : '') + (isleName &&
                                        pumpName ? ' | ' : '') + (pumpName ?
                                        `Surtidor: ${pumpName}` : '') + `</small>`;
                                }

                                const $tankRow = $(`
                                    <tr class="product-row" style="cursor:pointer;">
                                        <td style="padding-left:20px">
                                            <div>
                                                <span class="small text-muted">Isla: ${isleName || '-'}</span>
                                                <span class="small text-muted"> | Surtidor: ${pumpName || '-'}</span>
                                                <strong class="ms-2">${product.name}</strong>
                                            </div>
                                            <div class="small text-muted">Tanque: ${tank.tank_name || '-'} (Stock: ${parseFloat(tank.stored_quantity).toFixed(3)})</div>
                                        </td>
                                        <td align="right">S/ ${parseFloat(product.price).toFixed(2)}</td>
                                    </tr>
                                `);

                                // Guardar datos en el elemento para usar al hacer click
                                // Guardar datos en el elemento para usar al hacer click
                                $tankRow.data('product-id', product.id);
                                $tankRow.data('product-name', product.name);
                                $tankRow.data('price', parseFloat(product.price));
                                $tankRow.data('observations', product.observations || '');
                                $tankRow.data('tank-id', tank.tank_id);
                                // Guardar pump_id en la fila para enviarlo en la venta
                                $tankRow.data('pump-id', tank.pump_id || null);
                                $tankRow.data('area', product.area || '');
                                $tankRow.data('order-detail-id', (function() {
                                    const odId = $tankRow.data('order-detail-id');
                                    // Validar que sea un número válido
                                    if (odId && odId !== null && odId !== '' &&
                                        odId !== 'null') {
                                        const parsed = parseInt(odId);
                                        return (!isNaN(parsed) && parsed > 0) ?
                                            parsed : null;
                                    }
                                    return null;
                                })());

                                $tankRow.on('click', function() {
                                    const prodId = $(this).data('product-id');
                                    const prodName = $(this).data('product-name');
                                    const price = $(this).data('price');
                                    const obs = $(this).data('observations');
                                    const tankId = $(this).data('tank-id');
                                    const area = $(this).data('area');
                                    const orderDetailId = $(this).data(
                                        'order-detail-id');
                                    const pumpId = $(this).data('pump-id');

                                    // Llenar área si existe
                                    if (area) {
                                        $('#area').val(area);
                                    }

                                    // Guardar order_detail_id también en el campo global (compatibilidad)
                                    if (orderDetailId) {
                                        $('#current-order-detail-id').val(
                                            orderDetailId);
                                    } else {
                                        $('#current-order-detail-id').val('');
                                    }

                                    // Llamar a la función de agregar producto pasando el orderDetailId y pumpId
                                    addOrder(prodId, prodName, price, obs, tankId,
                                        orderDetailId, pumpId);
                                });

                                $('#tbl-products-contract').append($tankRow);
                            });
                        });

                        // Cambiar título del card
                        $('#products-contract-credit h6').text(`Productos de la Orden #${data.order.number}`);

                        ToastMessage.fire({
                            text: `${Object.keys(productosAgrupados).length} productos cargados de la orden`
                        });

                    } else {
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaOrdenes()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de órdenes
                                </button>
                            </td>
                        </tr>
                        <tr><td colspan="3" class="text-center text-muted">No hay productos disponibles en esta orden</td></tr>
                    `);

                        ToastMessage.fire({
                            text: 'No hay productos disponibles en esta orden'
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar productos de la orden:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar los productos de la orden'
                    });
                }
            });
        }

        function volverListaOrdenes() {
            // Aquí necesitamos guardar el agreement_id cuando entramos a cargarProductosContrato
            const agreementId = $('#current-agreement-id').val(); // Lo agregaremos después
            if (agreementId) {
                // Limpiar campo de orden al volver a la lista de órdenes
                $('#orden').val('');
                $('#current-order-detail-id').val(''); // Limpiar order_detail_id
                cargarProductosContrato(agreementId);
            }
        }

        function volverListaContratos() {
            const clienteId = $('#client_id').val();
            if (clienteId) {
                // Limpiar campos de orden y área al volver a la lista
                $('#orden').val('');
                $('#area').val('');
                $('#current-agreement-id').val(''); // Limpiar agreement_id
                $('#current-order-detail-id').val(''); // Limpiar order_detail_id
                cargarContratosCliente(clienteId);
            }
        }

        function isDecimal(evt) {
            evt = evt || window.event;
            var charCode = evt.which || evt.keyCode;
            if ((charCode >= 48 && charCode <= 57) || charCode === 46) {
                var input = evt.target || evt.srcElement;
                if (charCode === 46 && input.value.includes('.')) {
                    evt.preventDefault();
                    return false;
                }
                return true;
            } else {
                evt.preventDefault();
                return false;
            }
        }



        $('#toggleGalonesSubtotal').on('change', function() {
            const galonesSection = $('#galonesSection');
            const subtotalSection = $('#subtotalSection');

            if (this.checked) {
                // Cambiar a modo "Por Galones"
                galonesSection.show();
                subtotalSection.hide();

                // Si hay precio y subtotal, calcular cantidad automáticamente
                const precio = parseFloat($('#lbl-price').val()) || 0;
                const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
                if (precio > 0 && subtotal > 0) {
                    const cantidad = subtotal / precio;
                    $('#txt-quantity').val(cantidad.toFixed(3));
                }
            } else {
                // Cambiar a modo "Por Subtotal"
                galonesSection.hide();
                subtotalSection.show();

                // Si hay precio y cantidad, calcular subtotal automáticamente
                const precio = parseFloat($('#lbl-price').val()) || 0;
                const cantidad = parseFloat($('#txt-quantity').val()) || 1;
                if (precio > 0 && cantidad > 0) {
                    const subtotal = precio * cantidad;
                    $('#txt-subtotal').val(subtotal.toFixed(2));
                }
            }
        });

        // Cargar productos cuando se selecciona una isla manualmente
        $('#btn-load-by-isle').on('click', function() {
            loadProductsBySede();
        });

        $('#select-isle').on('change', function() {
            // Si se cambia la isla manualmente, recargar lista
            loadProductsBySede();
        });

        function clearSwitch() {
            $('#toggleGalonesSubtotal').prop('checked', false);
            $('#switchLabel').text('Por Subtotal');
            $('#galonesSection').hide();
            $('#subtotalSection').show();
        }

        function calcularGalones() {
            const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const quantity = price > 0 ? (subtotal / price) : 0;
            $('#txt-quantity').val(quantity.toFixed(3));
            updateCalculationSummary();
        }
        // Ocultar alerta cuando se cierra el modal
        $('#addProductsModal').on('hidden.bs.modal', function() {
            $('#calculationSummary').addClass('d-none');
            $('#toggleGalonesSubtotal').val('false');

            // También limpiar los valores del resumen
            $('#summary-quantity').text('0.000');
            $('#summary-price').text('S/ 0.00');
            $('#summary-total').text('S/ 0.00');
            $('#galonesSection').hide();
            $('#subtotalSection').show();
            $('#lbl-price').prop('disabled', true);
            $('#lbl-price').addClass('bg-light');
        });

        // Ocultar alerta cuando se abre el modal (resetear)
        $('#addProductsModal').on('show.bs.modal', function() {
            $('#calculationSummary').addClass('d-none');
        });

        function updateCalculationSummary() {
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
            const isSubtotalMode = $('#toggleGalonesSubtotal').prop('checked');

            if (isSubtotalMode) {
                // Modo subtotal
                const calculatedQuantity = price > 0 ? (subtotal / price) : 0;
                $('#summary-quantity').text(calculatedQuantity.toFixed(3));
                $('#summary-price').text('S/ ' + price.toFixed(2));
                $('#summary-total').text('S/ ' + subtotal.toFixed(2));
            } else {
                // Modo galones
                const calculatedSubtotal = (quantity * price).toFixed(2);
                $('#summary-quantity').text(quantity.toFixed(3));
                $('#summary-price').text('S/ ' + price.toFixed(2));
                $('#summary-total').text('S/ ' + calculatedSubtotal);
            }

            // Mostrar el resumen si hay valores
            if (quantity > 0 || subtotal > 0) {
                $('#calculationSummary').removeClass('d-none');

                $('#quick-add-product').show();
                $('#quick-add-product-subtotal').show();
            } else {
                $('#calculationSummary').addClass('d-none');
            }
        }
        // Nueva función para calcular subtotal cuando se cambia la cantidad en modo galones
        function calcularSubtotal() {
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = (quantity * price).toFixed(2);
            $('#txt-subtotal').val(subtotal);
            updateCalculationSummary();
        }

        $('#checkPrecioM').on('change', function() {
            if (this.checked) {
                $('#lbl-price').prop('disabled', false);
                $('#lbl-price').removeClass('bg-light');
            } else {
                $('#lbl-price').prop('disabled', true);
                $('#lbl-price').addClass('bg-light');
            }
        });

        // Actualizar resumen cuando cambie el toggle
        $('#toggleGalonesSubtotal').on('change', function() {
            updateCalculationSummary();
        });

        // Actualizar resumen cuando cambie el precio
        $('#lbl-price').on('change', function() {
            updateCalculationSummary();
        });

        function searchTable() {
            var area_id = state.area_id;
            var search = $('#search-table').val();
            getTables(area_id, search);
        }

        function addOrder(product_id, name, price, observations, tank_id, order_detail_id, pump_id) {
            $('#quick-add-product').show();
            $('#quick-add-product-subtotal').show();
            $('#product_id').val(product_id);
            // Guardar order_detail_id temporalmente en el campo product_id como data
            if (typeof order_detail_id !== 'undefined' && order_detail_id !== null) {
                $('#product_id').data('order-detail-id', order_detail_id);
            } else {
                // limpiar cualquier valor previo
                $('#product_id').removeData('order-detail-id');
            }
            // Guardar pump_id temporalmente
            if (typeof pump_id !== 'undefined' && pump_id !== null) {
                $('#pump_id').val(pump_id);
            } else {
                $('#pump_id').val('');
            }
            // Guardar el tanque seleccionado (si viene)
            if (typeof tank_id !== 'undefined' && tank_id !== null) {
                $('#tank_id').val(tank_id);
            } else {
                $('#tank_id').val('');
            }
            $('#lbl-name').val(name);
            $('#lbl-price').val(price);

            // Guardar el precio original por sede en un campo oculto
            $('#lbl-price').data('original-price', price);

            $('#txt-quantity').val(1);

            /*Subtotal*/
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const unitPrice = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = (quantity * unitPrice).toFixed(2);
            /*Fin de Subtotal*/

            $('#txt-subtotal').val(subtotal);

            $('#txt-note').val('');

            $('#divObservations').empty();
            var obs = observations && observations.length > 0 ? observations.split(',') : [];
            obs.forEach(function(observation) {
                $('#divObservations').append(`
                <div class="form-check form-check-inline">
                    <label><input type="radio" class="form-check-input" name="observation" value="${observation}">${observation}</label>
                </div>
            `);
            });
        }

        function addProductDirect() {
            // Determinar si estamos usando el modal o el formulario rápido
            const isModalOpen = $('#addProductsModal').hasClass('show') || $('#addProductsModal').is(':visible');
            const modalPrefix = isModalOpen ? '#addProductsModal ' : '';

            // Leer valores del formulario activo (modal o formulario rápido)
            var product_id = $(modalPrefix + '#product_id').val();
            var tank_id = $(modalPrefix + '#tank_id').val();
            var pump_id = $(modalPrefix + '#pump_id').val() || '';
            var nombre = $(modalPrefix + '#lbl-name').val();
            var precio = parseFloat($(modalPrefix + '#lbl-price').val()) || 0;
            var nota = $(modalPrefix + '#txt-note').val() || '';
            var hora = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            var subtotalInput = parseFloat($(modalPrefix + '#txt-subtotal').val()) || 0;
            var switchActivo = $(modalPrefix + '#toggleGalonesSubtotal').prop('checked');
            var cantidad, subtotal;

            if (switchActivo) {
                // Modo "Por Galones": cantidad * precio = subtotal
                cantidad = parseFloat($(modalPrefix + '#txt-quantity').val()) || 1;
                // Usar Math.round para evitar problemas de redondeo
                subtotal = Math.round(precio * cantidad * 100) / 100;
                console.log('=== MODO GALONES ===');
                console.log('Precio:', precio);
                console.log('Cantidad:', cantidad);
                console.log('Subtotal calculado:', subtotal);
            } else {
                // Modo "Por Subtotal": usar exactamente el subtotal introducido y calcular cantidad
                subtotal = Math.round(subtotalInput * 100) / 100;
                cantidad = precio > 0 ? (subtotalInput / precio) : 0;
                console.log('=== MODO SUBTOTAL ===');
                console.log('Subtotal input:', subtotalInput);
                console.log('Subtotal redondeado:', subtotal);
                console.log('Cantidad calculada:', cantidad);
            }

            if (!product_id || !precio || cantidad <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Faltan datos del producto o los valores son inválidos.'
                });
                return;
            }

            // Verificar si se usó precio mayorista
            var isMayorista = $(modalPrefix + '#checkPrecioM').is(':checked');

            // Obtener precio original por sede y redondear
            var precioOriginal = Math.round((parseFloat($(modalPrefix + '#lbl-price').data('original-price')) || precio) *
                100) / 100;

            // Redondear el precio actual también
            precio = Math.round(precio * 100) / 100;

            console.log('=== PRECIOS REDONDEADOS ===');
            console.log('Precio Original:', precioOriginal);
            console.log('Precio Actual:', precio);
            console.log('Es Mayorista:', isMayorista);

            // Obtener order_detail_id (del elemento product_id del formulario activo)
            const rowOrderDetailId = $(modalPrefix + '#product_id').data('order-detail-id') || null;

            // Validar order_detail_id
            let validOrderDetailId = null;
            if (rowOrderDetailId && rowOrderDetailId !== null && rowOrderDetailId !== '' && rowOrderDetailId !== 'null') {
                const parsed = parseInt(rowOrderDetailId);
                if (!isNaN(parsed) && parsed > 0) {
                    validOrderDetailId = parsed;
                }
            }

            // Validar tank_id
            let validTankId = null;
            if (tank_id && tank_id !== null && tank_id !== '' && tank_id !== 'null') {
                const parsed = parseInt(tank_id);
                if (!isNaN(parsed) && parsed > 0) {
                    validTankId = parsed;
                }
            }

            // Validar pump_id
            let validPumpId = null;
            if (pump_id && pump_id !== null && pump_id !== '' && pump_id !== 'null') {
                const parsed = parseInt(pump_id);
                if (!isNaN(parsed) && parsed > 0) {
                    validPumpId = parsed;
                }
            }

            // Agregar la fila a la tabla con todos los datos validados
            let row = `
        <tr data-product-id="${product_id}" 
            ${validTankId ? `data-tank-id="${validTankId}"` : ''} 
            ${validPumpId ? `data-pump-id="${validPumpId}"` : ''} 
            ${validOrderDetailId ? `data-order-detail-id="${validOrderDetailId}"` : ''}
            data-is-wholesale="${isMayorista ? 'true' : 'false'}"
            data-original-price="${precioOriginal}"
            data-current-price="${precio}"
            data-subtotal="${subtotal}">
            <td>${nombre}</td>
            <td>S/ ${precio.toFixed(2)}</td>
            <td>${cantidad.toFixed(3)}</td>
            <td>S/ ${subtotal.toFixed(2)}</td>
            <td>${hora}</td>
            <td><button class="btn btn-danger btn-xs" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button></td>
        </tr>
        `;

            $('#tbl-order-items').append(row);

            // Limpiar el formulario del modal
            $('#addProductsModal #product_id').val('');
            $('#addProductsModal #tank_id').val('');
            $('#addProductsModal #pump_id').val('');
            $('#addProductsModal #lbl-name').val('');
            $('#addProductsModal #lbl-price').val('');
            $('#addProductsModal #lbl-price').removeData('original-price');
            $('#addProductsModal #txt-quantity').val(1);
            $('#addProductsModal #txt-subtotal').val('');
            $('#addProductsModal #checkPrecioM').prop('checked', false);
            $('#addProductsModal #toggleGalonesSubtotal').prop('checked', false);
            $('#addProductsModal #product_id').removeData('order-detail-id');

            // Limpiar el formulario rápido
            $('#product_id').val('');
            $('#tank_id').val('');
            $('#pump_id').val('');
            $('#lbl-name').val('');
            $('#lbl-price').val('');
            $('#lbl-price').removeData('original-price');
            $('#txt-quantity').val(1);
            $('#txt-subtotal').val('');
            $('#txt-note').val('');
            $('#divObservations').empty();
            $('#quick-add-product').hide();
            $('#quick-add-product-subtotal').hide();
            $('#product_id').removeData('order-detail-id');

            // Cerrar el modal si estaba abierto
            $('#addProductsModal').modal('hide');

            // Recalcular total
            recalculateTotal();
            clearSwitch();

            ToastMessage.fire({
                text: `${nombre} agregado correctamente`
            });
        }

        function removeProduct(btn) {
            $(btn).closest('tr').remove();
            recalculateTotal();
        }

        function recalculateTotal() {
            console.log('=== RECALCULANDO TOTAL ===');
            let total = 0;
            $('#tbl-order-items tr').each(function(index) {
                const $tds = $(this).find('td');
                // Leer el subtotal de la columna 4 (índice 3)
                const subtotalText = $tds.eq(3).text().replace('S/', '').replace(/\s/g, '').trim();
                const subtotal = parseFloat(subtotalText);

                console.log(`Producto ${index + 1}: Subtotal=${subtotalText} -> Parseado=${subtotal}`);

                if (!isNaN(subtotal) && subtotal > 0) {
                    total += subtotal;
                    console.log(`  Total acumulado: ${total}`);
                }
            });

            // Redondear correctamente el total usando Math.round
            total = Math.round(total * 100) / 100;
            console.log('Total FINAL redondeado:', total);
            const totalFormatted = total.toFixed(2);
            $('#total').text(totalFormatted);
            $('#charge-total').text(totalFormatted);
            $('#lbl-charge-total').text(totalFormatted);
            $('#lbl-charge-total-pay').text(totalFormatted);

            // Actualizar dinámicamente el primer método de pago que esté checkeado
            $('input[type="checkbox"][id^="cbx_amount_"]').each(function() {
                if ($(this).is(':checked')) {
                    var paymentId = $(this).attr('id').replace('cbx_amount_', '');
                    $('#amount_' + paymentId).val(total.toFixed(2));
                    return false; // Break del each
                }
            });

            $('#difference').val('0.00');
            $('#cash').val('');
            $('#change').val('');
        }


        var sent = false;

        function resetProductForm() {
            $('#product_id').val('');
            $('#edit_order_id').val('');
            $('#lbl-name').val('');
            $('#edit-lbl-name').val('');
            $('#lbl-price').val('');
            $('#edit-lbl-price').val('');
            $('#txt-quantity').val(1);
            $('#edit-txt-quantity').val('');
            $('#divObservations').empty();
            $('#txt-note').val('');
            $('#edit-txt-note').val('');
            $('#orderModal').modal('show');
            $('#addProductModal').modal('hide');
            $('#editProductModal').modal('hide');
            $('#pump_id').val('');
        }


        function returnTable() {
            confirmOrder(false);
            $('#orderModal').modal('hide');
        }

        $('#btn-charge').click(function() {
            // Recalcular el total antes de abrir el modal
            recalculateTotal();

            const tipoVenta = $('#tipo-venta').val();
            const totalCalculado = $('#total').text();
            $('#lbl-charge-total').text(totalCalculado);
            $('#lbl-charge-total-pay').text(totalCalculado);
            $('#lbl-charge-discount').text('0.00');
            $('#difference').val('0.00');
            $('#amount_1').val(totalCalculado);
            $('#amount_2').val('');
            $('#amount_3').val('');
            $('#cbx_amount_1').prop('checked', true);
            $('#cbx_amount_2').prop('checked', false);
            $('#cbx_amount_3').prop('checked', false);

            // Mostrar/ocultar métodos de pago y 'Paga con' según tipo de venta
            const isCreditSale = $('#is-credit-sale').is(':checked');
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                // Para contrato y crédito ocultar ambas secciones
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }

            $('#orderModal').modal('hide');
            $('#chargeModal').modal('show');
        });

        // Evento para actualizar el total cuando se muestra el modal de carga
        $('#chargeModal').on('show.bs.modal', function() {
            recalculateTotal();

            // Controlar visibilidad de secciones según tipo de venta
            const tipoVenta = $('#tipo-venta').val();
            const isCreditSale = $('#is-credit-sale').is(':checked');
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }
        });

        // Evento para controlar visibilidad en voucherModal
        $('#voucherModal').on('show.bs.modal', function() {
            const tipoVenta = $('#tipo-venta').val();
            const isCreditSale = $('#is-credit-sale').is(':checked');
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }
        });

        $('#btn-add-payment-method').click(function() {

            if ($('.divPaymentMethod').length < $('select.payment_method').first().find('option').length - 1) {

                var div = $('.divPaymentMethod').first().clone();

                div.find('input').val('');

                $('.divPaymentMethod').last().after(div);

            }

        });

        $('#btn-delete-payment-method').click(function() {
            if ($('.divPaymentMethod').length > 1) {

                $('.divPaymentMethod').last().remove();

            }
        });

        function isNumber(evt) {
            evt = evt || window.event;
            var charCode = evt.which || evt.keyCode;
            if (charCode < 48 || charCode > 57) {
                evt.preventDefault();
                return false;
            }
            return true;
        }

        function searchAPI(docEl, nameEl, addressEl) {
            const doc = $(docEl).val().trim();

            // Limpiar campos
            $(nameEl).val('');
            $(addressEl).val('');
            $('#client').val('');

            // Validar longitud del documento
            if (doc.length !== 8 && doc.length !== 11) {
                ToastError.fire({
                    text: 'El documento debe tener 8 (DNI) o 11 dígitos (RUC).'
                });
                return;
            }

            Swal.showLoading();

            $.ajax({
                url: "{{ url('sunat/consultar') }}?doc=" + doc,
                method: 'GET',
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        const data = response.data;
                        let fullName = '';

                        if (doc.length === 8) {
                            fullName = `${data.nombre} ${data.apellido_paterno} ${data.apellido_materno}`
                                .trim();
                        } else {
                            fullName = data.nombre?.trim() || '';
                        }

                        $(nameEl).val(fullName);
                        $(addressEl).val(data.domicilio?.direccion || '');
                        $('#client').val(fullName);
                    } else {
                        ToastError.fire({
                            text: response.message || 'No se encontró información en SUNAT/RENIEC'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    ToastError.fire({
                        text: 'Error al consultar SUNAT/RENIEC'
                    });
                }
            });
        }

        function calculateDifference(e) {
            var total = parseFloat($('#total').text()) || 0;
            var totalPayments = 0;

            // Calcular dinámicamente el total de todos los métodos de pago activos
            $('input[id^="amount_"]').each(function() {
                var paymentId = $(this).attr('id').replace('amount_', '');
                var checkbox = $('#cbx_amount_' + paymentId);
                
                // Solo sumar si el checkbox está marcado
                if (checkbox.is(':checked')) {
                    var amount = parseFloat($(this).val()) || 0;
                    totalPayments += amount;
                }
            });

            var difference = total - totalPayments;
            $('#difference').val(difference.toFixed(2));

            // Calcular vuelto adicional automáticamente si el checkbox está marcado
            if ($('#is-vuelto-adicional').is(':checked')) {
                // Si el total pagado es mayor que el total de la venta, hay vuelto adicional
                if (totalPayments > total) {
                    var adicional = totalPayments - total;
                    $('#adicional').val(adicional.toFixed(2));
                } else {
                    // Si el total pagado es menor o igual, no hay vuelto adicional
                    $('#adicional').val('0.00');
                }
            }

            calculateChange();
        }

        function calculateDiscount(e) {
            var percentage = isNaN(parseFloat($('#percentage').val())) ? 0 : parseFloat($('#percentage').val());
            var discount = isNaN(parseFloat($('#discount').val())) ? 0 : parseFloat($('#discount').val());
            var total = isNaN(parseFloat($('#lbl-charge-total').text())) ? 0 : parseFloat($('#lbl-charge-total').text());

            if (e.target.id == 'percentage') {

                var discount = (total * percentage) / 100;
                var total_pay = total - discount;

                $('#discount').val(discount.toFixed(2));



            } else if (e.target.id == 'discount') {

                var percentage = (discount / total) * 100;
                var total_pay = total - discount;

                $('#percentage').val(percentage.toFixed(2));

            }

            $('#lbl-charge-discount').text(discount.toFixed(2));
            $('#lbl-charge-total-pay').text(total_pay.toFixed(2));

            calculateDifference();

        }

        function calculateChange(e) {
            // Buscar el primer método de pago que esté checkeado y con valor
            var firstPaymentAmount = 0;
            $('input[type="checkbox"][id^="cbx_amount_"]').each(function() {
                if ($(this).is(':checked')) {
                    var paymentId = $(this).attr('id').replace('cbx_amount_', '');
                    firstPaymentAmount = parseFloat($('#amount_' + paymentId).val()) || 0;
                    return false; // Break del each
                }
            });

            var cash = isNaN(parseFloat($('#cash').val())) ? 0 : parseFloat($('#cash').val());
            var change = cash - firstPaymentAmount;

            if (cash > 0) {
                $('#change').val(change.toFixed(2));
            } else {
                $('#change').val('');
            }
        }


        function resetChargeModal() {
            $('voucher_type').prop('checked', false);
            $('#voucher_type_1').prop('checked', true);
            $('#document').val('');
            $('#name').val('');
            $('#address').val('');

            // Resetear dinámicamente todos los checkboxes y inputs de métodos de pago
            $('input[type="checkbox"][id^="cbx_amount_"]').prop('checked', false);
            $('input[id^="amount_"]').val('');
            $('input[id^="operation_number_"]').val('');

            $('#cbx_credit').prop('checked', false);
            $('#payment_days').val('');
            $('#difference').val('');
            $('#percentage').val('');
            $('#discount').val('');
            $('#cash').val('');
            $('#date').val('');
            $('#change').val('');
            $('#tbl-charge-items').html('');
            $('#lbl-charge-total').text('0.00');
            $('#lbl-charge-discount').text('0.00');
            $('#lbl-charge-total-pay').text('0.00');
            $('#observation').val('');
            $('#is-vuelto-adicional').prop('checked', false);
            $('#vuelto-adicional-section').hide();
            $('#adicional').val('0.00');
            $('#chargeModal').modal('hide');
        }


        function togglePaymentMethod(event, inputSelector) {
            // Habilitar o deshabilitar el input según el checkbox
            if (event.target.checked) {
                $(inputSelector).prop('disabled', false).focus();
            } else {
                $(inputSelector).prop('disabled', true).val('');
            }

            // Recalcular diferencia
            calculateDifference();
        }

        function guardarVenta() {
            // Obtener el tipo de venta al inicio de la función
            const tipoVenta = $('#tipo-venta').val();
            const selectedIsleId = $('#select-isle').val(); 
            const isCreditSale = $('#is-credit-sale').is(':checked');
            const isVueltoAdicional = $('#is-vuelto-adicional').is(':checked');
            let vehiclePlate = $('#vehicle_plate').val();
            const adicional = $('#adicional').val();
            console.log('adicional', adicional);
            if ($('#tbl-order-items tr').length === 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe agregar al menos un producto a la venta'
                });
                return;
            }

            // Validar nombre de cliente para crédito
            if (tipoVenta === 'directa' && isCreditSale) {
                let clientName = $('#client_name').val();
                vehiclePlate = $('#vehicle_plate').val()?.trim() || null;
                if (!clientName || clientName.trim() === '') {
                    $('#client_name').val($('#client').val());
                    clientName = $('#client_name').val();
                    ToastError.fire({
                        title: 'Error',
                        text: 'Debe ingresar el nombre del cliente para venta a crédito'
                    });
                    return;
                }
            }

            // Validar formas de pago solo para venta directa
            let totalPayments = 0;
            let paymentMethods = [];

            if (tipoVenta === 'directa' && !isCreditSale) {
                // Verificar dinámicamente todos los métodos de pago activos
                $('input[type="checkbox"][id^="cbx_amount_"]').each(function() {
                    if ($(this).is(':checked')) {
                        var paymentId = $(this).attr('id').replace('cbx_amount_', '');
                        var amount = parseFloat($('#amount_' + paymentId).val()) || 0;

                        if (amount > 0) {
                            totalPayments += amount;
                            paymentMethods.push({
                                payment_method_id: parseInt(paymentId),
                                amount: amount,
                                adicional: adicional,
                                voucher_type: $('input[name="voucher_type"]:checked').val(),
                                voucher_id: null,
                                number: null
                            });
                        }
                    }
                });

                // Validaciones solo para venta directa
                if (paymentMethods.length === 0) {
                    ToastError.fire({
                        title: 'Error',
                        text: 'Debe seleccionar al menos un método de pago'
                    });
                    return;
                }

                // Validar que el total de pagos coincida con el total de la venta
                const totalVenta = parseFloat($('#total').text()) || 0;
                const isVueltoAdicionalEnabled = $('#is-vuelto-adicional').is(':checked');

                if (!isVueltoAdicionalEnabled) {
                    // Validación estricta: los pagos deben coincidir exactamente
                    if (Math.abs(totalPayments - totalVenta) > 0.01) {
                        ToastError.fire({
                            title: 'Error',
                            text: 'El total de los pagos no coincide con el total de la venta'
                        });
                        return;
                    }
                } else {
                    // Con vuelto adicional: los pagos deben ser mayor o igual al total
                    if (totalPayments < totalVenta) {
                        ToastError.fire({
                            title: 'Error',
                            text: 'El total de los pagos no puede ser menor al total de la venta'
                        });
                        return;
                    }
                    // Calcular y guardar el vuelto adicional
                    const vueltoAdicionalCalculado = totalPayments - totalVenta;
                    $('#adicional').val(vueltoAdicionalCalculado.toFixed(2));
                }
            }

            // Recopilar datos de los productos
            console.log('=== RECOPILANDO PRODUCTOS PARA ENVIAR ===');
            let products = [];
            $('#tbl-order-items tr').each(function(index) {
                const $row = $(this);
                const productId = $row.data('product-id');
                const $tds = $row.find('td');

                if (productId) {
                    const tankId = $row.data('tank-id') || null;
                    const precioTexto = $tds.eq(1).text().replace('S/', '').trim();
                    const cantidadTexto = $tds.eq(2).text().trim();
                    const subtotalTexto = $tds.eq(3).text().replace('S/', '').trim();
                    const clientName = $('#client_name').val();
                    const cantidad = parseFloat(cantidadTexto);
                    const precioMostrado = parseFloat(precioTexto);
                    const subtotal = parseFloat(subtotalTexto);

                    // Leer el subtotal exacto del data attribute
                    const subtotalExacto = parseFloat($row.data('subtotal')) || subtotal;

                    console.log(`Producto ${index + 1} (ID: ${productId}):`);
                    console.log('  Precio texto:', precioTexto, '-> Parseado:', precioMostrado);
                    console.log('  Cantidad texto:', cantidadTexto, '-> Parseado:', cantidad);
                    console.log('  Subtotal texto:', subtotalTexto, '-> Parseado:', subtotal);
                    console.log('  Subtotal exacto (data):', subtotalExacto);

                    // Validar que los valores son válidos
                    if (isNaN(cantidad) || cantidad <= 0) {
                        console.error('Cantidad inválida para producto:', productId);
                        return;
                    }

                    if (isNaN(precioMostrado) || precioMostrado <= 0) {
                        console.error('Precio inválido para producto:', productId);
                        return;
                    }

                    // Calcular el precio real basado en subtotal/cantidad y redondear
                    const precioReal = cantidad > 0 ? Math.round((subtotal / cantidad) * 100) / 100 :
                        precioMostrado;

                    // Verificar si se usó precio mayorista (guardado en data del row)
                    const isMayorista = $row.data('is-wholesale') === true || $row.data('is-wholesale') === 'true';

                    // Obtener precios originales guardados en data y redondear
                    const precioOriginal = Math.round((parseFloat($row.data('original-price')) || precioMostrado) *
                        100) / 100;
                    const precioActual = Math.round((parseFloat($row.data('current-price')) || precioMostrado) *
                        100) / 100;
                    const vehiclePlate = $('#vehicle_plate').val()?.trim() || null;
                    // Determinar el tipo de venta para el manejo de precios
                    const pumpIdRow = $row.data('pump-id') || null;

                    console.log('  Precio Original (data):', $row.data('original-price'), '-> Redondeado:',
                        precioOriginal);
                    console.log('  Precio Actual (data):', $row.data('current-price'), '-> Redondeado:',
                        precioActual);
                    console.log('  Cantidad final:', cantidad);
                    console.log('  Subtotal que se enviará:', subtotal);

                    // Debug log
                    console.log('Producto:', productId, '| Pump ID:', pumpIdRow, '| Tank ID:', tankId,
                        '| Tipo venta:', tipoVenta);

                    if (tipoVenta === 'contrato' || isCreditSale) {
                        // Para contratos y créditos: unit_price = precio por sede (BD), discounted_price = precio mostrado/cobrado
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio por sede de la BD
                            discounted_price: precioActual, // Precio que se está cobrando
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: false,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    } else if (isMayorista) {
                        // Mayorista: unit_price = precio original por sede, discounted_price = precio modificado
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio original por sede
                            discounted_price: precioActual, // Precio modificado por usuario
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: true,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    } else {
                        // Venta directa normal: unit_price = precio por sede, discounted_price = null
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio por sede
                            discounted_price: null, // Sin descuento
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: false,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    }
                }
            });

            // Después de la línea 2241, antes de preparar saleData, agregar:

            // Limpiar y validar order_detail_id en los productos
            products = products.map(p => {
                // Limpiar order_detail_id: solo incluir si es un número válido
                if (p.order_detail_id) {
                    const orderDetailId = parseInt(p.order_detail_id);
                    // Si es un número válido y mayor a 0, incluirlo, sino eliminarlo
                    if (isNaN(orderDetailId) || orderDetailId <= 0) {
                        delete p.order_detail_id;
                    } else {
                        p.order_detail_id = orderDetailId;
                    }
                } else {
                    // Si es null, undefined, string vacío, o "null", eliminarlo
                    delete p.order_detail_id;
                }

                // Limpiar otros campos opcionales de la misma manera
                if (p.tank_id) {
                    const tankId = parseInt(p.tank_id);
                    if (isNaN(tankId) || tankId <= 0) {
                        delete p.tank_id;
                    } else {
                        p.tank_id = tankId;
                    }
                } else {
                    delete p.tank_id;
                }

                if (p.pump_id) {
                    const pumpId = parseInt(p.pump_id);
                    if (isNaN(pumpId) || pumpId <= 0) {
                        delete p.pump_id;
                    } else {
                        p.pump_id = pumpId;
                    }
                } else {
                    delete p.pump_id;
                }

                // Asegurar que los campos requeridos sean números
                p.product_id = parseInt(p.product_id);
                p.quantity = parseFloat(p.quantity);
                p.unit_price = parseFloat(p.unit_price);

                // Limpiar discounted_price
                if (p.discounted_price && p.discounted_price !== null && p.discounted_price !== '') {
                    p.discounted_price = parseFloat(p.discounted_price);
                } else {
                    delete p.discounted_price;
                }

                // Limpiar vehicle_plate
                if (p.vehicle_plate && p.vehicle_plate.trim() !== '') {
                    p.vehicle_plate = p.vehicle_plate.trim();
                } else {
                    delete p.vehicle_plate;
                }

                return p;
            });

            // Determinar pump_id a nivel venta (usar el primer pump_id no nulo que encontremos)
            let salePumpId = null;
            $('#tbl-order-items tr').each(function() {
                const pid = $(this).data('pump-id');
                if (pid) {
                    salePumpId = pid;
                    return false; // break
                }
            });

            // Usar el tipo de venta ya obtenido arriba
            let clientId = null;
            let clientName = null;
            let orderDetailId = null;

            if (tipoVenta === 'contrato') {
                clientId = $('#client_id').val();
                clientName = $('#client').val() || $('#search-client').val();

                // Obtener el order_detail_id guardado cuando se seleccionó un producto
                orderDetailId = $('#current-order-detail-id').val() || null;
            } else {
                // Para venta directa, tomar datos del formulario
                // Priorizar client_name si existe, sino usar client
                clientName = $('#client_name').val() || $('#client').val();
            }

            // Determinar el tipo de venta final
            let typeSaleValue = $('#type_sale').val();
            // Si es venta directa con checkbox de crédito marcado, cambiar a 2
            if (tipoVenta === 'directa' && isCreditSale) {
                typeSaleValue = '2';
            }

            // Calcular vuelto adicional solo si es mayor a 0
            var adicionalValue = null;
            if ($('#is-vuelto-adicional').is(':checked')) {
                var adicionalInput = parseFloat($('#adicional').val()) || 0;
                if (adicionalInput > 0) {
                    adicionalValue = adicionalInput;
                }
            }

            // Preparar datos para enviar
            const saleData = {
                isle_id: selectedIsleId,
                client_id: clientId,
                client: clientName,
                client_name: clientName,
                phone: null, // Agregar campo de teléfono si es necesario
                vehicle_plate: vehiclePlate,
                order_detail_id: orderDetailId,
                pump_id: salePumpId,
                type_sale: typeSaleValue, // Tipo de venta: 0=directa, 1=contrato, 2=crédito
                products: products,
                payment_methods: isCreditSale ? [] : paymentMethods, // Array vacío si es crédito
                voucher_type: $('input[name="voucher_type"]:checked').val(),
                voucher_number: $('#number').val(), // Número de comprobante para payments
                credit_number: $('#credit_number').val() && $('#credit_number').val().trim() !== '' ? parseInt($('#credit_number').val()) : null, // Número de crédito (solo para ventas a crédito)
                document: $('#document').val(),
                address: $('#address').val(),
                orden: $('#orden').val(),
                placa: $('#placa').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Solo agregar adicional si es mayor a 0
            if (adicionalValue !== null && adicionalValue > 0) {
                saleData.adicional = adicionalValue;
            }

            // Después de la línea 2286 (después de preparar saleData), agregar:

            // Validar que hay productos
            if (!products || products.length === 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe agregar al menos un producto a la venta'
                });
                return;
            }

            // Validar métodos de pago para venta directa
            const typeSale = saleData.type_sale;
            if (typeSale == 0 && (!paymentMethods || paymentMethods.length === 0)) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar al menos un método de pago para venta directa'
                });
                return;
            }

            // Actualizar saleData con los productos limpios
            saleData.products = products;

            // Mostrar loading
            /* Swal.fire({
                title: 'Guardando venta...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }); */

            // Enviar datos al servidor
            $.ajax({
                url: "{{ route('sales.store') }}",
                method: 'POST',
                data: saleData,
                success: function(response) {
                    // Swal.close();

                    if (response.status) {
                        // Mostrar toast de éxito
                        ToastMessage.fire({
                            text: response.message || 'Venta guardada correctamente'
                        });

                        $('#voucherModal').modal('hide');
                        $("#spinner-save").hide();
                        // Limpiar formulario
                        limpiarFormulario();

                        // Si había productos de contrato/orden cargados, recargarlos para actualizar stock
                        const tipoVentaActual = $('#tipo-venta').val();
                        if (tipoVentaActual === 'contrato') {
                            const currentAgreementId = $('#current-agreement-id').val();
                            if (currentAgreementId) {
                                setTimeout(() => {
                                    cargarProductosContrato(currentAgreementId);
                                }, 500); // Pequeño delay para que se complete la limpieza
                            }
                        }
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.error || 'Error al guardar la venta'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();

                    let errorMessage = 'Error al guardar la venta';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Mostrar errores de validación
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join(', ');
                    }

                    ToastError.fire({
                        title: 'Error',
                        text: errorMessage
                    });

                    console.error('Error:', xhr.responseJSON);
                }
            });
        }

        function limpiarFormulario() {
            // Limpiar tabla de productos
            $('#tbl-order-items').empty();

            // Resetear totales
            $('#total').text('0.00');
            $('#charge-total').text('0.00');
            $('#lbl-charge-total').text('0.00');
            $('#lbl-charge-total-pay').text('0.00');

            // Limpiar campos de cliente
            $('#document').val('');
            $('#client').val('');
            $('#client_name').val('');
            $('#vehicle_plate').val('');
            $('#address').val('');
            $('#orden').val('');
            $('#area').val('');
            $('#number').val('');
            $('#current-order-detail-id').val('');
            $('#current-agreement-id').val('');
            $('#current-order-id').val('');
            $('#search-client').val('');
            $('#client_id').val('');
            $('#vehicle_plate').val('');
            $('#credit_number').val('');

            // Limpiar formulario rápido
            $('#product_id').val('');
            $('#tank_id').val('');
            $('#pump_id').val('');
            $('#lbl-name').val('');
            $('#lbl-price').val('');
            $('#lbl-price').removeData('original-price');
            $('#txt-quantity').val(1);
            $('#txt-subtotal').val('');
            $('#txt-note').val('');
            $('#divObservations').empty();
            $('#product_id').removeData('order-detail-id');
            $('#quick-add-product').hide();
            $('#quick-add-product-subtotal').hide();

            // Limpiar modal de agregar productos
            $('#addProductsModal #product_id').val('');
            $('#addProductsModal #tank_id').val('');
            $('#addProductsModal #pump_id').val('');
            $('#addProductsModal #lbl-name').val('');
            $('#addProductsModal #lbl-price').val('');
            $('#addProductsModal #lbl-price').removeData('original-price');
            $('#addProductsModal #txt-quantity').val(1);
            $('#addProductsModal #txt-subtotal').val('');
            $('#addProductsModal #checkPrecioM').prop('checked', false);
            $('#addProductsModal #toggleGalonesSubtotal').prop('checked', false);
            $('#addProductsModal #product_id').removeData('order-detail-id');
            $('#addProductsModal').modal('hide');

            // Resetear tipo de venta a directa
            $('#tipo-venta').val('directa').trigger('change');
            $('#type_sale').val('0');
            
            // Ocultar sección de crédito y desmarcar checkbox
            $('#is-credit-sale').prop('checked', false);
            $('#credit-number-section').hide();
            $('#payment-methods-section').show();
            $('#paga-con-section').show();

            // Resetear formas de pago
            $('input[type="checkbox"][id^="cbx_amount_"]').prop('checked', false);
            $('input[id^="amount_"]').val('');

            // Marcar el primer método de pago como predeterminado si existe
            if ($('input[type="checkbox"][id^="cbx_amount_"]').length > 0) {
                $('input[type="checkbox"][id^="cbx_amount_"]').first().prop('checked', true);
            }

            // Limpiar diferencia y cambio
            $('#difference').val('0.00');
            $('#cash').val('');
            $('#change').val('');

            // Resetear comprobante
            $('input[name="voucher_type"]').prop('checked', false);
            $('#voucher_type_3').prop('checked', true); // Factura por defecto
        }
    </script>

    <script>
        // Location actual del usuario (se utiliza para consultar el monto calculado y el registro del día)
        const currentLocationId = {{ auth()->user()->location_id ?? 'null' }};

    $('#finalCashModal').on('show.bs.modal', function() {
        // Resetear selector
        $('#select-isle-final').val('');
        
        // Resetear inputs a 0.00
        $('#initial_cash_amount_final').val('0.00');
        $('#cash_sales_amount').val('0.00');
        $('#expenses_amount').val('0.00');
        $('#adicional_amount').val('0.00');
        $('#real_cash_amount').val('0.00');
        $('#final_cash_amount').val(''); // Campo vacío para que escriban
        
        // Borrar ID guardado
        $(this).data('cash-close-id', null);
    });

    // 2. Al SELECCIONAR LA ISLA: Cargar datos desde el controlador
    $('#select-isle-final').on('change', function() {
        const isleId = $(this).val();

        // Si selecciona la opción por defecto ("-- Seleccione --"), limpiar y salir
        if (!isleId) {
            $('#real_cash_amount').val('0.00');
            $('#initial_cash_amount_final').val('0.00');
            return;
        }

        // Indicador de carga
        $('#real_cash_amount').val('Calculando...');

        $.ajax({
            url: "{{ url('cash_closes') }}" + '/' + isleId,
            method: 'GET',
            success: function(resp) {
                console.log('Datos Cierre recibidos:', resp);

                if (resp && resp.status) {
                    
                    // --- MAPEO DE VARIABLES PHP A INPUTS HTML ---

                    // 1. Monto Inicial (initial_cash_amount)
                    $('#initial_cash_amount_final').val(parseFloat(resp.initial_cash_amount || 0).toFixed(2));

                    // 2. Ventas Efectivo (cash_sales)
                    $('#cash_sales_amount').val(parseFloat(resp.cash_sales || 0).toFixed(2));

                    // 3. Egresos (cash_expenses)
                    $('#expenses_amount').val(parseFloat(resp.cash_expenses || 0).toFixed(2));

                    // 4. Adicional/Vuelto (total_adicional)
                    $('#adicional_amount').val(parseFloat(resp.total_adicional || 0).toFixed(2));

                    // 5. Saldo Real del Sistema (calculated_cash_amount) -> Viene de tabla isles
                    $('#real_cash_amount').val(parseFloat(resp.calculated_cash_amount || 0).toFixed(2));

                    // 6. ID del Registro de Cierre (cash_close.id)
                    // Necesario para hacer el UPDATE al guardar
                    if (resp.cash_close && resp.cash_close.id) {
                        $('#finalCashModal').data('cash-close-id', resp.cash_close.id);
                    } else {
                        $('#finalCashModal').data('cash-close-id', null);
                        // Opcional: Mostrar alerta si no se abrió caja
                        ToastError.fire({ text: 'Advertencia: No se encontró apertura de caja hoy para esta isla.' });
                    }

                } else {
                    $('#real_cash_amount').val('0.00');
                    ToastError.fire({ text: resp.message || 'Error al obtener datos.' });
                }
            },
            error: function(xhr, status, error) {
            // 1. Imprimir todo el error en la consola del navegador (F12)
            console.error("--- DETALLES DEL ERROR ---");
            console.error("Estado:", status);
            console.error("Error:", error);
            console.error("Respuesta Servidor:", xhr.responseText);

            // 2. Intentar capturar el mensaje específico
            let mensajeError = 'Error desconocido en el servidor.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                // Caso: Laravel devolvió un JSON con error controlado
                mensajeError = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                // Caso: JSON con campo 'error'
                mensajeError = xhr.responseJSON.error;
            } else if (xhr.responseText) {
                // Caso: Error fatal de PHP (pantalla roja de Laravel en texto)
                // Intentamos extraer una parte pequeña para no llenar la pantalla
                mensajeError = 'Error Fatal (Ver Consola): ' + xhr.statusText;
            }

            // 3. Mostrar el error REAL en la alerta
            $('#real_cash_amount').val('0.00');
            ToastError.fire({ 
                text: 'Error: ' + mensajeError 
            });
        }
        });
    });

    // 3. Al dar clic en GUARDAR (Procesar Cierre)
    $('#btn-save-final').on('click', function() {
        
        // Recuperar el ID que guardamos en el paso anterior
        const cashCloseId = $('#finalCashModal').data('cash-close-id');
        
        // Recuperar montos
        const finalAmount = parseFloat($('#final_cash_amount').val()); // Lo que el usuario cuenta
        const realAmount = parseFloat($('#real_cash_amount').val()) || 0; // Lo que dice el sistema

        // Validaciones
        if (!cashCloseId) {
            ToastError.fire({ text: 'No hay una caja abierta válida para cerrar.' });
            return;
        }

        if (isNaN(finalAmount) || finalAmount < 0) {
            ToastError.fire({ text: 'Ingrese el Monto Final (dinero físico contado).' });
            return;
        }

        // Deshabilitar botón para evitar doble click
        const $btn = $(this);
        $btn.prop('disabled', true);

        // Enviar Petición PUT
        $.ajax({
            url: "{{ url('cash_closes') }}" + '/' + cashCloseId,
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                final_cash_amount: finalAmount, // Dinero físico
                real_cash_amount: realAmount    // Dinero sistema
            },
            success: function(resp) {
                if (resp && resp.status) {
                    ToastMessage.fire({ text: resp.message });
                    $('#finalCashModal').modal('hide');
                    // Recargar la página para ver el cierre en la tabla (opcional)
                    setTimeout(() => location.reload(), 1000);
                } else {
                    ToastError.fire({ text: resp.message || 'Error al cerrar caja.' });
                }
            },
            error: function(xhr) {
                console.error(xhr);
                ToastError.fire({ 
                    text: xhr.responseJSON?.message || 'Error al procesar el cierre.' 
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

        // Scripts para manejo de apertura y cierre de caja
        $('#btn-save-initial').on('click', function() {
            const initialCashAmount = parseFloat($('#initial_cash_amount').val()) || 0;
            const isleId = $('#select-isle-initial').val();

            if (initialCashAmount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto inicial debe ser mayor a cero.'
                });
                return;
            }

            if (!isleId || isleId === '') {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una isla.'
                });
                return;
            }

            $.ajax({
                url: "{{ route('cash_closes.store') }}",
                method: 'POST',
                data: {
                    initial_cash_amount: initialCashAmount,
                    isle_id: isleId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        ToastMessage.fire({
                            text: response.message
                        });
                        $('#initialCashModal').modal('hide');
                        $('#initial_cash_amount').val('');
                        $('#select-isle-initial').val('');
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Mensaje por defecto
                    let msg = 'Error al guardar la apertura de caja.';

                    // Si la respuesta ya es JSON y contiene 'message' o 'errors'
                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            try {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                            } catch (e) {
                                msg = JSON.stringify(xhr.responseJSON.errors);
                            }
                        }
                    } else if (xhr && xhr.responseText) {
                        // Intentar parsear responseText si no se detectó responseJSON
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            if (parsed && parsed.message) msg = parsed.message;
                        } catch (e) {
                            // No es JSON: usar statusText o el error provisto
                            msg = xhr.statusText || error || msg;
                        }
                    } else if (error) {
                        msg = error;
                    }

                    ToastError.fire({
                        title: 'Error',
                        text: msg
                    });

                    console.error('Error saving expense:', xhr, status, error);
                }
            });
        });
        $('#select-isle-expense').on('change', function() {
            const isleId = $(this).val();
            if (isleId) {
                $.ajax({
                    url: "{{ url('cash_closes') }}" + '/' + isleId,
                    method: 'GET',
                    success: function(resp) {
                        if (resp && resp.status) {
                            $('#cash_amount').val(parseFloat(resp.calculated_cash_amount || 0).toFixed(
                                2));
                        } else {
                            $('#cash_amount').val('0.00');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al obtener cierre:', xhr);
                        $('#cash_amount').val('0.00');
                        ToastError.fire({
                            text: 'Error al obtener información de caja.'
                        });
                    }
                });
            } else {
                $('#cash_amount').val('0.00');
                ToastError.fire({
                    text: 'Debe seleccionar una isla.'
                });
            }
        });

        // Limpiar campos cuando se cierra el modal
        $('#expenseModal').on('hidden.bs.modal', function() {
            $('#select-isle-expense').val('');
            $('#cash_amount').val('0.00');
            $('#expense_amount').val('');
            $('#expense_description').val('');
        });

        $('#expenseModal').on('show.bs.modal', function() {
            // Resetear al abrir el modal
            $('#select-isle-expense').val('');
            $('#cash_amount').val('0.00');
            $('#expense_amount').val('');
            $('#expense_description').val('');
        });

        $('#btn-save-expenses').on('click', function() {
            const isleId = $('#select-isle-expense').val();
            const description = $('#expense_description').val().trim();
            const amount = parseFloat($('#expense_amount').val()) || 0;

            // Validar que se haya seleccionado una isla
            if (!isleId || isleId === '') {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una isla.'
                });
                return;
            }

            // Solo validar monto (descripción es opcional)
            if (amount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto debe ser mayor a cero.'
                });
                return;
            }

            // Deshabilitar botón para evitar doble envío
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('expenses.store') }}",
                method: 'POST',
                data: {
                    description: description || null, // Enviar null si está vacío
                    amount: amount,
                    isle_id: isleId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        ToastMessage.fire({
                            text: response.message
                        });

                        // Recargar el monto desde el servidor para obtener el valor actualizado
                        const isleId = $('#select-isle-expense').val();
                        if (isleId) {
                            $.ajax({
                                url: "{{ url('cash_closes') }}" + '/' + isleId,
                                method: 'GET',
                                success: function(resp) {
                                    if (resp && resp.status) {
                                        $('#cash_amount').val(parseFloat(resp
                                            .calculated_cash_amount || 0).toFixed(
                                            2));
                                    }
                                },
                                error: function() {
                                    // Si falla, simplemente restar localmente
                                    const currentAmount = parseFloat($('#cash_amount')
                                        .val()) || 0;
                                    const newAmount = Math.max(0, currentAmount - amount);
                                    $('#cash_amount').val(newAmount.toFixed(2));
                                }
                            });
                        }

                        $('#expense_description').val('');
                        $('#expense_amount').val('');
                        $('#expenseModal').modal('hide');
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.responseJSON?.message || 'Error al procesar la solicitud';
                    ToastError.fire({
                        title: 'Error',
                        text: errorMessage
                    });
                    console.error('Error:', error, xhr);
                },
                complete: function() {
                    // Rehabilitar botón
                    $btn.prop('disabled', false);
                }
            });
        });

        // ==========================================
    // LÓGICA CORREGIDA PARA BÓVEDA (VAULT)
    // ==========================================
    
    // 1. Al abrir el modal, limpiar campos
        $('#vaultModal').on('show.bs.modal', function() {
            $('#select-isle-vault').val(''); // Resetear select
            $('#cash_amount_acumulated').val('0.00'); // Resetear monto visual
            $('#vault_amount').val(''); // Resetear input de monto
        });

        // 2. Al cambiar la isla, traer el saldo de la BD
        $('#select-isle-vault').on('change', function() {
            const isleId = $(this).val();

            if (!isleId) {
                $('#cash_amount_acumulated').val('0.00');
                return;
            }

            // Indicador de carga
            $('#cash_amount_acumulated').val('Cargando...');

            $.ajax({
                url: "{{ url('cash_closes') }}" + '/' + isleId,
                method: 'GET',
                success: function(resp) {
                    if (resp && resp.status) {
                        // Usamos calculated_cash_amount que viene directo de la tabla isles->cash_amount
                        const saldo = parseFloat(resp.calculated_cash_amount || 0);
                        $('#cash_amount_acumulated').val(saldo.toFixed(2));
                    } else {
                        $('#cash_amount_acumulated').val('0.00');
                        ToastError.fire({
                            text: resp.message || 'No se pudo obtener info de caja.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al obtener cierre:', xhr);
                    $('#cash_amount_acumulated').val('0.00');
                    ToastError.fire({
                        text: 'Error al obtener información de caja.'
                    });
                }
            });
        });

        $('#btn-save-vault').on('click', function() {
            const amount = parseFloat($('#vault_amount').val()) || 0;
            // Obtenemos el saldo acumulado que mostramos en pantalla
            const amount_vault = parseFloat($('#cash_amount_acumulated').val()) || 0;
            const isleId = $('#select-isle-vault').val(); // Obtenemos la isla seleccionada
            const $btn = $(this);

            if (!isleId) {
                ToastError.fire({ title: 'Error', text: 'Debe seleccionar una isla.' });
                return;
            }

            if (amount <= 0) {
                ToastError.fire({ title: 'Error', text: 'El monto debe ser mayor a cero.' });
                return;
            }

            if (amount > amount_vault) {
                ToastError.fire({ title: 'Error', text: 'El monto debe ser menor o igual al saldo en caja.' });
                return;
            }

            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('vault.from_cash_close') }}", 
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    amount: amount,
                    isle_id: isleId 
                },
                success: function(resp) {
                    if (resp && resp.success) {
                        ToastMessage.fire({ text: resp.message || 'Enviado a bóveda correctamente.' });
                        const newCash = amount_vault - amount;
                        $('#cash_amount_acumulated').val(newCash.toFixed(2));
                        
                        $('#vaultModal').modal('hide');
                    } else {
                        ToastError.fire({ title: 'Error', text: resp.message || 'Error al guardar.' });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    ToastError.fire({ title: 'Error', text: 'Error al procesar la solicitud.' });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        
        $('#btn-save-vault').on('click', function() {
            const amount = parseFloat($('#vault_amount').val()) || 0;
            const amount_vault = parseFloat($('#cash_amount_acumulated').val()) || 0;
            const $btn = $(this);

            if (amount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto debe ser mayor a cero.'
                });
                return;
            }

            if (amount > amount_vault) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto debe ser menor o igual al monto acumulado en caja.'
                });
                return;
            }

            // Deshabilitar el botón para evitar dobles envíos
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('vault.from_cash_close') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    amount: amount
                },
                success: function(resp) {
                    if (resp && resp.success) {
                        ToastMessage.fire({
                            text: resp.message || 'Monto enviado a la bóveda correctamente.'
                        });

                        // Actualizar UI localmente restando el monto enviado
                        const newCash = (amount_vault - amount);
                        $('#cash_amount_acumulated').val(parseFloat(newCash || 0).toFixed(2));
                        // También actualizar el campo del modal de egreso si está presente
                        if ($('#cash_amount').length) {
                            const currentCash = parseFloat($('#cash_amount').val()) || 0;
                            $('#cash_amount').val(parseFloat(Math.max(0, currentCash - amount)).toFixed(
                                2));
                        }

                        $('#vault_amount').val('');
                        $('#vaultModal').modal('hide');
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: resp.message || 'No se pudo enviar el monto a la bóveda.'
                        });
                    }
                },
                error: function(xhr) {
                    let msg = 'Error al procesar la solicitud';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    ToastError.fire({
                        title: 'Error',
                        text: msg
                    });
                    console.error('Error vault from cash close:', xhr);
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        // ==================== FUNCIONALIDAD DE MEDICIONES DE CONTÓMETRO ====================

        // Cargar islas cuando se abre el modal
        $('#finalMeasurementModal').on('show.bs.modal', function() {
            // Limpiar campos
            $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
            $('#pump_side').val('');
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');

            // Cargar islas de la sede del usuario
            $.ajax({
                url: "{{ route('sales.measurements.isles') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success && response.isles) {
                        $('#select-isle-measurement').html(
                            '<option value="">Seleccione una isla</option>');
                        response.isles.forEach(function(isle) {
                            $('#select-isle-measurement').append(
                                `<option value="${isle.id}">${isle.name}</option>`
                            );
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar islas:', xhr);
                    ToastError.fire({
                        text: 'Error al cargar las islas'
                    });
                }
            });
        });

        // Cuando se selecciona una isla, cargar sus surtidores
        $('#select-isle-measurement').on('change', function() {
            const isleId = $(this).val();

            // Limpiar campos
            $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
            $('#pump_side').val('');
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');

            if (!isleId) return;

            // Cargar surtidores de la isla seleccionada
            $.ajax({
                url: "{{ route('sales.measurements.pumps') }}",
                method: 'GET',
                data: {
                    isle_id: isleId
                },
                success: function(response) {
                    if (response.success && response.pumps) {
                        response.pumps.forEach(function(pump) {
                            const sideName = pump.side == 1 ? 'Lado 1' : (pump.side == 2 ?
                                'Lado 2' : 'N/A');
                            const productName = pump.product ? pump.product.name :
                                'Sin producto';
                            $('#select-pump-measurement').append(
                                `<option value="${pump.id}" data-side="${sideName}">${pump.name} - ${sideName} (${productName})</option>`
                            );
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar surtidores:', xhr);
                    ToastError.fire({
                        text: 'Error al cargar los surtidores'
                    });
                }
            });
        });

        // Cuando se selecciona un surtidor, obtener su última medición y calcular el teórico
        $('#select-pump-measurement').on('change', function() {
            const pumpId = $(this).val();
            const sideName = $(this).find('option:selected').data('side');

            // Mostrar lado
            $('#pump_side').val(sideName || '');

            // Limpiar valores
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');
            $('#difference_measurement_value').val('');

            // Deshabilitar botón de guardar por defecto
            $('#btn-save-measurement').prop('disabled', true);

            if (!pumpId) return;

            // Obtener última medición
            $.ajax({
                url: "{{ route('sales.measurements.last') }}",
                method: 'GET',
                data: {
                    pump_id: pumpId
                },
                success: function(response) {
                    if (response.success) {
                        // Verificar si ya existe una medición hoy
                        if (response.has_today_measurement) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Medición ya registrada',
                                text: 'Ya existe una medición para este surtidor el día de hoy. Solo se permite una medición diaria por surtidor.',
                                confirmButtonText: 'Entendido'
                            });

                            // Limpiar selector
                            $('#select-pump-measurement').val('');
                            $('#pump_side').val('');
                            return;
                        }

                        // Si no hay medición hoy, cargar el valor inicial (del día anterior o 0)
                        const lastValue = response.measurement ?
                            parseFloat(response.measurement.amount_final || 0).toFixed(3) :
                            '0.000';
                        $('#initial_measurement_value').val(lastValue);

                        // Habilitar botón de guardar
                        $('#btn-save-measurement').prop('disabled', false);

                        // Obtener valor teórico
                        obtenerValorTeorico(pumpId, response.measurement ? response.measurement.date :
                            null);
                    } else {
                        ToastError.fire({
                            text: response.message || 'Error al obtener última medición'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al obtener última medición:', xhr);
                    const errorMessage = xhr.responseJSON?.message ||
                        'Error al obtener última medición';
                    ToastError.fire({
                        text: errorMessage
                    });
                    $('#initial_measurement_value').val('0.000');
                    $('#btn-save-measurement').prop('disabled', false);
                    obtenerValorTeorico(pumpId, null);
                }
            });
        });

        // Función para obtener el valor teórico
        function obtenerValorTeorico(pumpId, startDate) {
            $.ajax({
                url: "{{ route('sales.measurements.theoretical') }}",
                method: 'GET',
                data: {
                    pump_id: pumpId,
                    start_date: startDate
                },
                success: function(response) {
                    if (response.success) {
                        $('#theorical_measurement_value').val(
                            parseFloat(response.total_sold || 0).toFixed(3)
                        );
                        // Calcular diferencia al cargar el valor teórico
                        calcularDiferenciaMedicion();
                    }
                },
                error: function(xhr) {
                    console.error('Error al calcular valor teórico:', xhr);
                    $('#theorical_measurement_value').val('0.000');
                }
            });
        }

        // Guardar medición
        $('#btn-save-measurement').on('click', function() {
            const pumpId = $('#select-pump-measurement').val();
            const initialValue = parseFloat($('#initial_measurement_value').val()) || 0;
            const finalValue = parseFloat($('#final_measurement_value').val()) || 0;
            const theoreticalValue = parseFloat($('#theorical_measurement_value').val()) || 0;

            // Validaciones
            if (!pumpId) {
                ToastError.fire({
                    text: 'Debe seleccionar un surtidor'
                });
                return;
            }

            if (finalValue <= 0) {
                ToastError.fire({
                    text: 'El valor final debe ser mayor a cero'
                });
                return;
            }

            if (finalValue < initialValue) {
                Swal.fire({
                    title: '¿Confirmar medición?',
                    text: 'El valor final es menor que el inicial. ¿Desea continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue);
                    }
                });
            } else {
                guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue);
            }
        });

        // Calcular diferencia automáticamente cuando cambie el valor final
        $('#final_measurement_value').on('input', function() {
            calcularDiferenciaMedicion();
        });

        function calcularDiferenciaMedicion() {
            const initial = parseFloat($('#initial_measurement_value').val()) || 0;
            const final = parseFloat($('#final_measurement_value').val()) || 0;
            const teorico = parseFloat($('#theorical_measurement_value').val()) || 0;

            // Diferencia = (Final - Inicial) - Teórico
            const diferencia = (initial - final) - teorico

            $('#difference_measurement_value').val(diferencia.toFixed(3));
        }

        // Función para guardar la medición
        function guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue) {
            const $btn = $('#btn-save-measurement');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('sales.measurements.save') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pump_id: pumpId,
                    initial_value: initialValue,
                    final_value: finalValue,
                    theoretical_value: theoreticalValue
                },
                success: function(response) {
                    if (response.success) {
                        ToastMessage.fire({
                            text: response.message || 'Medición guardada correctamente'
                        });

                        // Cerrar modal y limpiar campos
                        $('#finalMeasurementModal').modal('hide');
                        $('#select-isle-measurement').val('');
                        $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
                        $('#pump_side').val('');
                        $('#initial_measurement_value').val('');
                        $('#final_measurement_value').val('');
                        $('#theorical_measurement_value').val('');
                    } else {
                        ToastError.fire({
                            text: response.message || 'Error al guardar medición'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al guardar medición:', xhr);

                    // Manejar error 422 (validación)
                    if (xhr.status === 422) {
                        const errorMessage = xhr.responseJSON?.message ||
                            'Ya existe una medición para este surtidor hoy';
                        Swal.fire({
                            icon: 'error',
                            title: 'No se puede guardar',
                            text: errorMessage,
                            confirmButtonText: 'Entendido'
                        });
                    } else {
                        const errorMessage = xhr.responseJSON?.message || 'Error al guardar la medición';
                        ToastError.fire({
                            text: errorMessage
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        }

        /**
         * FUNCIÓN DE EJEMPLO: Registrar pago parcial para venta a crédito
         * Esta función muestra cómo usar el nuevo endpoint de pagos parciales
         * que soporta múltiples métodos de pago para un mismo pago
         * 
         * Uso:
         * registerCreditPayment(saleId, [
         *   { payment_method_id: 1, amount: 50.00 },  // Efectivo
         *   { payment_method_id: 2, amount: 30.00 }   // Tarjeta
         * ]);
         */
        function registrarPagoParcialCredito(saleId, paymentMethods) {
            $.ajax({
                url: '{{ route('sales.creditPayment') }}',
                method: 'POST',
                data: {
                    sale_id: saleId,
                    payment_methods: paymentMethods,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        ToastSuccess.fire({
                            title: 'Éxito',
                            text: response.message
                        });
                        console.log('Total pagado:', response.data.total_pagado);
                        console.log('Saldo restante:', response.data.saldo_restante);
                        // Aquí puedes actualizar la UI, recargar tabla de pagos, etc.
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error al registrar pago';
                    ToastError.fire({
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        }
    </script>
@endsection
