@extends('template.index')

@section('header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Histórico de Cierre de Caja
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none text-muted">Ventas y Caja</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Cierre de Caja</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('petty_cash.index') }}" class="btn btn-primary shadow-sm d-flex align-items-center rounded-pill px-3 text-white fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Volver a Caja Chica
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner" style="padding-top: 1rem;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0" style="border-radius: 10px;">

                    {{-- FILTRO --}}
                    <div class="card-body">
                        <form action="{{ route('cashClose.index') }}" id="fromFilter" method="GET">
                            <div class="row g-2 align-items-end mb-4">
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha inicial</label>
                                    <input type="date" id="start_date" class="form-control form-control-sm" name="start_date" value="{{ request()->start_date ? request()->start_date : '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Fecha final</label>
                                    <input type="date" id="end_date" class="form-control form-control-sm" name="end_date" value="{{ request()->end_date ? request()->end_date : '' }}">
                                </div>

                                @php
                                    $user = auth()->user();
                                @endphp

                                @if ($user && (int) $user->role_id === 3)
                                <div class="col-md-3">
                                    <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Locación</label>
                                    <select name="location_id" class="form-select form-select-sm">
                                        <option value="">Todas</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-5 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-medium" id="btnFiltrar" style="border-radius: 6px;">
                                        <i class="bi bi-funnel me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('cashClose.index') }}" class="btn btn-secondary btn-sm px-3 fw-medium ms-1" id="btnLimpiar" style="border-radius: 6px;">
                                        <i class="bi bi-eraser me-1"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                                <thead class="text-center">
                                    <tr>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Locación</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Inicial</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Real</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Final</th>
                                    </tr>
                                </thead>

                                <tbody class="text-center">
                                    @forelse ($cashCloses as $cashClose)
                                        <tr>
                                            <td>{{ $cashClose->user->name ?? $cashClose->user->email ?? 'N/A' }}</td>
                                            <td>{{ $cashClose->location->name ?? 'N/A' }}</td>
                                            <td>{{ $cashClose->isle->name ?? 'Sin isla' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($cashClose->date)->format('d/m/Y') }}</td>
                                            <td>{{ number_format((float) ($cashClose->initial_cash_amount ?? 0), 2, '.', '') }}</td>
                                            <td>{{ number_format((float) ($cashClose->real_cash_amount ?? 0), 2, '.', '') }}</td>
                                            <td>{{ number_format((float) ($cashClose->final_cash_amount ?? 0), 2, '.', '') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                No hay cierres de caja registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $cashCloses->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .swal-confirm-btn {
            background-color: #dc3545 !important;
            color: #fff !important;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            margin-right: 10px;
            font-weight: 500;
        }

        .swal-cancel-btn {
            background-color: #6c757d !important;
            color: #fff !important;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
        }
    </style>
@endsection
