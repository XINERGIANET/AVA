    @extends('template.index')

@section('header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="bi bi-bar-chart-line me-2 text-primary"></i>Histórico de Contómetros
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Reportes</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Contómetros</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('flowmeters.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center rounded-pill">
                <i class="bi bi-plus-circle me-2"></i> Registrar Contómetro
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                    <div class="card-body border-bottom">
                        <form action="{{ route('flowmeters.historico') }}" method="GET" id="fromFilter">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Fecha inicial</label>
                                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                        value="{{ request()->start_date ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Fecha final</label>
                                    <input type="date" id="end_date" class="form-control form-control-sm" name="end_date"
                                        value="{{ request()->end_date ?? '' }}">
                                </div>

                                @if($isMaster)
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Sede</label>
                                    <select class="form-select form-select-sm" id="location_id" name="location_id">
                                        <option value="">Todas</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Isla</label>
                                    <select class="form-select form-select-sm" id="isle_id" name="isle_id">
                                        <option value="">Todas</option>
                                        @foreach ($isles as $isle)
                                            <option value="{{ $isle->id }}"
                                                {{ request()->isle_id == $isle->id ? 'selected' : '' }}>
                                                {{ $isle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Surtidor / Lado</label>
                                    <select class="form-select form-select-sm" id="pump_id" name="pump_id">
                                        <option value="">Todos</option>
                                        @foreach ($pumps as $pump)
                                            @php
                                                $pumpIsle = $pump->isle->name ?? 'Isla';
                                                $pumpSide = $pump->side ?? $pump->name ?? 'Surtidor';
                                                $pumpProduct = $pump->product->name ?? 'Producto';
                                                $label = $pumpIsle . ' - Lado ' . $pumpSide . ' - ' . $pumpProduct;
                                            @endphp
                                            <option value="{{ $pump->id }}"
                                                {{ request()->pump_id == $pump->id ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($isMaster || (!empty($users) && $users->count() > 0))
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold small mb-1">Usuario</label>
                                    <select class="form-select form-select-sm" id="user_id" name="user_id">
                                        <option value="">Todos</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-auto ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-medium px-3" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('flowmeters.historico') }}" class="btn btn-light btn-sm fw-medium px-3" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Lado</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Producto</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Inicial</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Final</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($measurements as $measurement)
                                        @php
                                            $diff = floatval($measurement->amount_difference ?? 0);
                                            $badgeClass = abs($diff) <= 0.02
                                                ? 'bg-success'
                                                : ($diff < -0.02 ? 'bg-danger' : 'bg-warning text-dark');
                                        @endphp
                                        <tr>
                                            <td>{{ ($measurements->currentPage() - 1) * $measurements->perPage() + $loop->iteration }}</td>
                                            <td>{{ $measurement->date ? $measurement->date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $measurement->location->name ?? 'N/A' }}</td>
                                            <td>{{ $measurement->pump->isle->name ?? 'N/A' }}</td>
                                            <td>{{ $measurement->pump->side ?? '-' }}</td>
                                            <td>{{ $measurement->pump->product->name ?? '-' }}</td>
                                            <td>{{ $measurement->user->name ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($measurement->amount_initial ?? 0, 3) }}</td>
                                            <td class="text-end">{{ number_format($measurement->amount_final ?? 0, 3) }}</td>
                                            <td class="text-end">
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ number_format($diff, 3) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No hay registros.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $measurements->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
