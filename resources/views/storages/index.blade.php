@extends('template.index')

@section('header')
<h1>Almacén</h1>
<p>Stock actual por sede y producto</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Stock Disponible</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role->nombre == 'master')
                    <div class="mb-3">
                        <label for="locationFilter" class="form-label">Filtrar por sede:</label>
                        <select id="locationFilter" class="form-select">
                            <option value="">Todas las sedes</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sede</th>
                                    <th>Tanque</th>
                                    <th>Producto</th>
                                    <th>Stock Disponible</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tanks as $tank)
                                <tr class="storage-row" data-location="{{ $tank->location_id }}">
                                    <td>{{ $tank->location->name }}</td>
                                    <td>{{ $tank->name }}</td>
                                    <td>{{ $tank->product->name }}</td>
                                    <td>{{ number_format($tank->stored_quantity, 3) }}</td>
                                    <td>{{ $tank->product->measurement_unit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationFilter = document.getElementById('locationFilter');
        const rows = document.querySelectorAll('.storage-row');

        locationFilter.addEventListener('change', function() {
            const locationId = this.value;

            rows.forEach(row => {
                if (locationId === '' || row.dataset.location === locationId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection