@extends('template.index')

@section('header')
    <h1>Historico de Contometros</h1>
    <p>Registro de lecturas</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <form action="{{ route('flowmeters.historico') }}" method="GET" id="fromFilter">
                            <div class="row d-flex">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha inicial</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request()->start_date ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha final</label>
                                        <input type="date" id="end_date" class="form-control" name="end_date"
                                            value="{{ request()->end_date ?? '' }}">
                                    </div>
                                </div>

                                @if($isMaster)
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Sede</label>
                                        <select class="form-select" id="location_id" name="location_id">
                                            <option value="">Todas</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Isla</label>
                                        <select class="form-select" id="isle_id" name="isle_id">
                                            <option value="">Todas</option>
                                            @foreach ($isles as $isle)
                                                <option value="{{ $isle->id }}"
                                                    {{ request()->isle_id == $isle->id ? 'selected' : '' }}>
                                                    {{ $isle->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Surtidor / Lado</label>
                                        <select class="form-select" id="pump_id" name="pump_id">
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
                                </div>

                                @if($isMaster || (!empty($users) && $users->count() > 0))
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <select class="form-select" id="user_id" name="user_id">
                                            <option value="">Todos</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="col d-flex align-items-end mt-3">
                                    <div class="me-2" style="min-width: 140px;">
                                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">Filtrar</button>
                                    </div>
                                    <div class="me-2" style="min-width: 140px;">
                                        <a href="{{ route('flowmeters.historico') }}" class="btn btn-warning w-100"
                                            id="btnLimpiar">Limpiar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Fecha</th>
                                        <th>Sede</th>
                                        <th>Isla</th>
                                        <th>Lado</th>
                                        <th>Producto</th>
                                        <th>Usuario</th>
                                        <th>Inicial</th>
                                        <th>Final</th>
                                        <th>Teorico</th>
                                        <th>Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                            <td class="text-end">{{ number_format($measurement->amount_theorical ?? 0, 3) }}</td>
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
