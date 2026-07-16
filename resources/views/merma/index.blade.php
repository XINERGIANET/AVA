@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Control de Merma</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none text-muted">Abastecimiento</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Control de Merma</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar de Filtros -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-10">
                    <form action="{{ route('merma.index') }}" method="GET" id="fromFilter">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label for="start_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->start_date ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->end_date ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                <select class="form-select form-select-sm" id="location_id" name="location_id">
                                    <option value="">Todas las sedes</option>
                                    @foreach ($locations ?? [] as $location)
                                        <option value="{{ $location->id }}" {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="product_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Producto</label>
                                <select class="form-select form-select-sm" id="product_id" name="product_id">
                                    <option value="">Todos los productos</option>
                                    @foreach ($products ?? [] as $product)
                                        <option value="{{ $product->id }}" {{ request()->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-secondary btn-sm w-100 fw-medium"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                                <a href="{{ route('merma.index') }}" class="btn btn-light btn-sm text-muted fw-medium w-100"><i class="bi bi-eraser me-1"></i>Limpiar</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" id="btnExcel" class="btn btn-success btn-sm w-100 fw-medium">
                        <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Responsable</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Proveedor</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Stock Inicial</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Teórico</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Real</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Diferencia</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Estado</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Nivel de Stock</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($readings as $reading)
                            @php
                                $estadoBadge = [
                                    'falta' => 'bg-danger',
                                    'cuadra' => 'bg-success',
                                    'sobra' => 'bg-warning text-dark',
                                ][$reading->status] ?? 'bg-secondary';

                                $stockBadge = [
                                    'SIN STOCK' => 'bg-danger',
                                    'STOCK BAJO' => 'bg-warning text-dark',
                                    'STOCK MEDIO' => 'bg-info text-dark',
                                    'STOCK ALTO' => 'bg-success',
                                ][$reading->stock_level] ?? 'bg-secondary';
                            @endphp
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td class="text-dark">{{ $reading->date ? $reading->date->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-dark">{{ $reading->user->name ?? 'N/A' }}</td>
                                <td class="text-dark">{{ $reading->tank->location->name ?? 'N/A' }}</td>
                                <td class="text-dark">{{ $reading->tank->product->name ?? 'N/A' }}</td>
                                <td class="text-dark">{{ $reading->last_supplier->commercial_name ?? ($reading->last_supplier->company_name ?? 'N/A') }}</td>
                                <td class="text-dark text-end fw-medium">{{ number_format($reading->previous_stock, 3) }}</td>
                                <td class="text-dark text-end fw-medium">{{ number_format($reading->theoretical_stock, 3) }}</td>
                                <td class="text-primary text-end fw-bold">{{ number_format($reading->physical_quantity, 3) }}</td>
                                <td class="text-dark text-end fw-medium">{{ number_format($reading->difference, 3) }}</td>
                                <td>
                                    <span class="badge {{ $estadoBadge }}">{{ ucfirst($reading->status) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $stockBadge }}">{{ $reading->stock_level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">No hay registros de merma para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $readings->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.getElementById('btnExcel').addEventListener('click', function() {
            const formData = $('#fromFilter').serialize();
            const excelUrl = "{{ route('merma.excel') }}?" + formData;

            this.innerHTML = 'Descargando...';
            this.disabled = true;

            const link = document.createElement('a');
            link.href = excelUrl;
            link.download = 'control_merma.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const btn = this;
            setTimeout(() => {
                btn.innerHTML = 'Excel';
                btn.disabled = false;
            }, 2000);
        });
    </script>
@endsection
