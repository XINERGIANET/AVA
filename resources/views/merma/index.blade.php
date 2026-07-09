@extends('template.index')

@section('header')
    <h1>Control de Merma</h1>
    <p>Histórico de tomas de inventario y reconciliación real vs. teórico</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="{{ route('merma.index') }}" method="GET" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label small">Fecha Inicial</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ request()->start_date ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label small">Fecha Final</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ request()->end_date ?? '' }}">
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sede</label>
                                        <select class="form-select" id="location_id" name="location_id">
                                            <option value="">Todas las sedes</option>
                                            @foreach ($locations ?? [] as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Producto</label>
                                        <select class="form-select" id="product_id" name="product_id">
                                            <option value="">Todos los productos</option>
                                            @foreach ($products ?? [] as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ request()->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col d-flex align-items-end mb-3">
                                    <div class="w-50s me-2">
                                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <button type="button" id="btnExcel" class="btn btn-success">Excel</button>
                                    </div>
                                    <div class="w-50s me-2">
                                        <a href="{{ route('merma.index') }}" class="btn btn-warning w-100" id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Responsable</th>
                                        <th>Sede</th>
                                        <th>Producto</th>
                                        <th>Proveedor</th>
                                        <th>Stock Inicial</th>
                                        <th>Teórico</th>
                                        <th>Real</th>
                                        <th>Diferencia</th>
                                        <th>Estado</th>
                                        <th>Nivel de Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($readings as $reading)
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
                                        <tr>
                                            <td>{{ $reading->date ? $reading->date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $reading->user->name ?? 'N/A' }}</td>
                                            <td>{{ $reading->tank->location->name ?? 'N/A' }}</td>
                                            <td>{{ $reading->tank->product->name ?? 'N/A' }}</td>
                                            <td>{{ $reading->last_supplier->commercial_name ?? ($reading->last_supplier->company_name ?? 'N/A') }}</td>
                                            <td class="text-end">{{ number_format($reading->previous_stock, 3) }}</td>
                                            <td class="text-end">{{ number_format($reading->theoretical_stock, 3) }}</td>
                                            <td class="text-end">{{ number_format($reading->physical_quantity, 3) }}</td>
                                            <td class="text-end">{{ number_format($reading->difference, 3) }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $estadoBadge }}">{{ ucfirst($reading->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $stockBadge }}">{{ $reading->stock_level }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $readings->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
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
