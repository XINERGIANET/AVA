@extends('template.index')

@section('header')
    <h1>Histórico de Cierre de Caja</h1>
    <p>Lista de cierres de caja</p>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    {{-- FILTRO --}}
                    <div class="card-body border-bottom">
                        <form action="{{ route('cashClose.index') }}" id="fromFilter" method="GET">
                            <div class="row d-flex">

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha inicial</label>
                                        <input
                                            type="date"
                                            id="start_date"
                                            class="form-control"
                                            name="start_date"
                                            value="{{ request()->start_date ? request()->start_date : '' }}"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha final</label>
                                        <input
                                            type="date"
                                            id="end_date"
                                            class="form-control"
                                            name="end_date"
                                            value="{{ request()->end_date ? request()->end_date : '' }}"
                                        >
                                    </div>
                                </div>

                                @php
                                    $user = auth()->user();
                                @endphp

                                @if ($user && (int) $user->role_id === 3)
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Locación</label>
                                        <select name="location_id" class="form-control">
                                            <option value="">Todas</option>
                                            @foreach ($locations as $location)
                                                <option
                                                    value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}
                                                >
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <div class="col d-flex align-items-end">
                                    <div class="me-2" style="min-width: 140px;">
                                        <button type="submit" class="btn btn-primary w-100" id="btnFiltrar">
                                            Filtrar
                                        </button>
                                    </div>
                                    <div class="me-2" style="min-width: 140px;">
                                        <a href="{{ route('cashClose.index') }}" class="btn btn-warning w-100" id="btnLimpiar">
                                            Limpiar
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Locación</th>
                                        <th>Isla</th>
                                        <th>Fecha</th>
                                        <th>Inicial</th>
                                        <th>Real</th>
                                        <th>Final</th>
                                    </tr>
                                </thead>

                                <tbody>
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
