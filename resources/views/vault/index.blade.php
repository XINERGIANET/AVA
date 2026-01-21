@extends('template.index')
@section('header')
    <h1>Historico Bóveda</h1>
    <p>Registro de movimientos de bóveda</p>
@endsection
@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <!--Card body-->
                    <div class="card-body">
                        <div class="row">
                            <form id="fromFilter">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="from_date" class="form-label small">Fecha Inicial</label>
                                        <input id="from_date" type="date" class="form-control" value="{{ request()->from_date ?? '' }}"
                                            name="from_date">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="to_date" class="form-label small">Fecha Final</label>
                                        <input type="date" class="form-control" value="{{ request()->to_date ?? '' }}"
                                            name="to_date" id="to_date">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="location_id" class="form-label small">Sede </label>
                                        <select class="form-control" name="location_id" id="location_id">
                                            <option value="" disabled selected>
                                                Seleccione una Sede
                                            </option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ request()->location_id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="isle_id" class="form-label small">Isla </label>
                                        <select class="form-control" name="isle_id" id="isle_id">
                                            <option value="" disabled selected>
                                                Seleccione una Isla
                                            </option>
                                            @foreach ($isles as $isle)
                                                <option value="{{ $isle->id }}"
                                                    {{ request()->isle_id == $isle->id ? 'selected' : '' }}>
                                                    {{ $isle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="user_id" class="form-label small">Usuario </label>
                                        <select class="form-control" name="user_id" id="user_id">
                                            <option value="" disabled selected>
                                                Seleccione un usuario
                                            </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!--Botones-->
                                <div class="row mt-3">
                                    <div class="col d-flex align-items-end mb-3">
                                        <div class="w-50s me-2">
                                            <button type="submit" id="btnFiltrar" class="btn btn-primary">Filtrar</button>
                                        </div>
                                        <div class="w-50 me-2">
                                            <a href="{{ route('vault.index') }}" class="btn btn-warning"
                                                id="btnLimpiar">Limpiar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Usuario</th>
                                        <th>Isla</th>
                                        <th>Monto</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Sede</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
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
