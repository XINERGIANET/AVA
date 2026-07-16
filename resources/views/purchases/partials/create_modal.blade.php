
<div class="modal fade" id="createPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold">Registrar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                            <form id="purchaseForm">
                                @csrf
                                
                                <!-- SECCIÃ“N: DATOS GENERALES -->
                                <h5 class="mb-3 text-primary" style="font-weight: 600; font-size: 1.1rem; color: #465fff !important;">
                                    <i class="bi bi-truck me-2"></i>Datos Generales
                                </h5>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-building me-1 text-secondary"></i>Proveedor:
                                        </label>
                                        <div class="input-group">
                                            <input type="text" id="search-supplier" class="form-control" placeholder="Buscar proveedor por RUC o RazÃ³n Social...">
                                            <input type="hidden" id="supplier_id" name="supplier_id">
                                            <button type="button" class="btn btn-success text-white" id="openProviderModal" data-bs-toggle="modal" data-bs-target="#providerModal" data-bs-toggle="tooltip" title="Agregar nuevo proveedor">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-calendar3 me-1 text-secondary"></i>Fecha de Compra
                                        </label>
                                        <input type="date" class="form-control" id="purchaseDate" name="date" required>
                                    </div>
                                </div>

                                <!-- SECCIÃ“N: DETALLE DE COMPRA -->
                                <h5 class="mb-3 mt-4 text-primary border-top pt-4" style="font-weight: 600; font-size: 1.1rem; color: #465fff !important;">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Detalle del Comprobante y Almacenamiento
                                </h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">Tipo de Comprobante</label>
                                                <select class="form-select" id="voucherType" name="voucher_type" required>
                                                    <option value="">Seleccione</option>
                                                    <option value="1">Factura</option>
                                                    <option value="2">Boleta</option>
                                                    <option value="3">Nota de Venta</option>
                                                    <option value="4">Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">NÂ° Comprobante (*)</label>
                                                <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" placeholder="Ej. F001-000123">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">CondiciÃ³n</label>
                                                <div class="form-control bg-light text-muted d-flex align-items-center text-nowrap overflow-hidden" style="min-height: 42px;" title="Se registra como Cuenta por Pagar">
                                                    <i class="bi bi-journal-check me-2 flex-shrink-0"></i>Cuenta por Pagar
                                                </div>
                                                <input type="hidden" id="paymentMethod" name="payment_method_id" value="">
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-0">
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">TÂ° de Compra</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="purchase_temp" name="purchase_temp" placeholder="0.00">
                                                    <span class="input-group-text">Â°C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">TÂ° de Llegada</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="real_temp" name="real_temp" placeholder="0.00">
                                                    <span class="input-group-text">Â°C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="min-width: 0;">
                                                <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                                    <i class="bi bi-geo-alt me-1 text-secondary"></i>Sede
                                                </label>
                                                <select class="form-select" id="location_id" name="location_id" {{ auth()->user()->role->nombre != 'master' ? 'disabled' : '' }}>
                                                    @if (auth()->user()->role->nombre == 'master')
                                                        <option value="" disabled {{ auth()->user()->location_id ? '' : 'selected' }}>Seleccione una sede</option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->id }}" {{ auth()->user()->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{ auth()->user()->location_id }}" selected>
                                                            {{ auth()->user()->location->name }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="min-width: 0;">
                                        <label class="form-label mb-1" style="font-weight: 500; color: #4b5563;">
                                            <i class="bi bi-database me-1 text-secondary"></i>Tanque(s)
                                        </label>
                                        <input type="text" class="form-control mb-2" id="tankSearch" placeholder="Buscar tanque...">
                                        <div id="tankList" class="border rounded-3 p-2 bg-white" style="max-height: 100px; overflow-y: auto;">
                                            @foreach ($tanks as $tank)
                                                <label class="d-flex align-items-start gap-2 mb-2 tank-item" data-location_id="{{ $tank->location_id }}" data-product_id="{{ $tank->product_id }}" data-name="{{ strtolower($tank->name) }}" style="cursor: pointer;">
                                                    <input class="form-check-input mt-1 tank-checkbox" type="checkbox" name="tank_id[]" value="{{ $tank->id }}">
                                                    <span class="small text-dark tank-name">{{ $tank->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="form-text small text-muted">Marca uno o varios tanques. El listado se filtra por sede.</div>
                                    </div>
                                </div>

                                <!-- SECCIÃ“N: PRODUCTOS -->
                                <div class="p-3 mt-4 mb-4" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-4">
                                            <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar Producto</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                                <input type="text" class="form-control" id="busquedaProducto" placeholder="Filtrar producto en la lista...">
                                            </div>
                                        </div>
                                        <div class="col-md-8 text-end">
                                            <div class="d-inline-block me-4">
                                                <span class="text-muted fw-bold" style="font-size: 1rem;">Total:</span>
                                                <strong class="text-primary ms-1" style="font-size: 1.25rem;">S/ <span id="totalAmount">0.00</span></strong>
                                            </div>
                                            <button type="submit" class="btn btn-primary px-4 btn-sm fw-medium" id="savePurchase" style="border-radius: 6px;">
                                                <i class="bi bi-save me-2"></i>Guardar Compra
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de productos -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="purchaseTable" style="border: 1px solid #e9ecef;">
                                        <thead class="text-center">
                                            <tr>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Unidad</th>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Precio Unitario</th>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Cantidad</th>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Subtotal</th>
                                                <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Merma</th>
                                                <th class="pe-4 text-center fw-bold text-uppercase" style="width: 10%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">AcciÃ³n</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center align-middle"></tbody>
                                    </table>
                                </div>
                            </form>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="providerModalLabel">Agregar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="providerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="document" class="form-label text-dark fw-bold">RUC/DNI</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="document" name="document" placeholder="Ingrese RUC o DNI" required>
                                    <button class="btn btn-outline-primary" type="button" onclick="searchDocumentApi()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label text-dark fw-bold">RazÃ³n Social</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" id="saveSupplier">Guardar</button>
                </div>
            </div>
        </div>
    </div>
