@extends('template.index')
@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold">
            <i class="bi bi-safe me-2 text-primary"></i>Histórico de Bóveda
        </h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none text-muted">Ventas y Caja</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Histórico Bóveda</li>
        </ol>
    </nav>
@endsection
@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Acumulado en bóveda</div>
                        <div class="fs-3 fw-bold text-primary">S/ {{ number_format($vaultAccumulated, 2) }}</div>
                        <div class="text-muted small">{{ $currentLocation ? 'Sede: ' . $currentLocation->name : 'Selecciona una sede arriba' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Enviado a Banco</div>
                        <div class="fs-3 fw-bold text-secondary">S/ 0.00</div>
                        <div class="text-muted small">Sin movimientos aún</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Enviado al Dueño</div>
                        <div class="fs-3 fw-bold text-secondary">S/ 0.00</div>
                        <div class="text-muted small">Sin movimientos aún</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Enviado a Otra Bóveda</div>
                        <div class="fs-3 fw-bold text-secondary">S/ 0.00</div>
                        <div class="text-muted small">Sin movimientos aún</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body">
                        <form id="fromFilter">
                            <div class="row g-2 align-items-end mb-4">
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Inicial</label>
                                    <input id="from_date" type="date" class="form-control form-control-sm" value="{{ request()->from_date ?? '' }}" name="from_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha Final</label>
                                    <input type="date" class="form-control form-control-sm" value="{{ request()->to_date ?? '' }}" name="to_date" id="to_date">
                                </div>
                                <div class="col-md-2">
                                    <label for="location_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Sede</label>
                                    <select class="form-select form-select-sm" name="location_id" id="location_id">
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ request()->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="isle_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Isla</label>
                                    <select class="form-select form-select-sm" name="isle_id" id="isle_id">
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach ($isles as $isle)
                                            <option value="{{ $isle->id }}" {{ request()->isle_id == $isle->id ? 'selected' : '' }}>{{ $isle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="user_id" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Usuario</label>
                                    <select class="form-select form-select-sm" name="user_id" id="user_id">
                                        <option value="" disabled selected>Seleccione</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="submit" id="btnFiltrar" class="btn btn-primary btn-sm px-3 fw-medium w-100 mb-1" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('vault.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium w-100" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-eraser me-1"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Monto</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Tipo</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($transactions as $trans)
                                    <tr>
                                        <td>{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                                        <td>{{ $trans->user->name }}</td>
                                        <td>{{ $trans->isle->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($trans->amount,2) }}</td>
                                        <td>{{ $trans->type == 'eb' ? 'Entrada' : 'Salida' }}</td>
                                        <td>{{ $trans->date->format('d/m/Y') }}</td>
                                        <td>{{ $trans->location->name }}</td>
                                        <td>{{ $trans->description ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay transacciones registradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#fromFilter').on('submit', function(e) {
                e.preventDefault();

                // Mostrar indicador de carga
                $('#btnFiltrar').html('<i class="bi bi-search"></i> Filtrando...').prop('disabled', true);

                // Obtener datos del formulario
                const formData = $(this).serialize();

                // Redirigir con los parámetros
                window.location.href = "{{ route('vault.index') }}?" + formData;
            });
        });
    </script>
@endsection
