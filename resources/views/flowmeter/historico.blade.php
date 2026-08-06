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
                                        <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
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
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-measurement"
                                                        data-id="{{ $measurement->id }}"
                                                        data-date="{{ $measurement->date ? $measurement->date->format('Y-m-d') : '' }}"
                                                        data-initial="{{ $measurement->amount_initial }}"
                                                        data-final="{{ $measurement->amount_final }}"
                                                        data-pump="{{ ($measurement->pump->isle->name ?? 'Isla') . ' - Lado ' . ($measurement->pump->side ?? '-') . ' (' . ($measurement->pump->product->name ?? 'Producto') . ')' }}"
                                                        title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>

                                                    <form action="{{ route('flowmeters.destroy', $measurement->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta lectura?');" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

    <!-- Modal Editar Lectura de Contómetro -->
    <div class="modal fade" id="editMeasurementModal" tabindex="-1" aria-labelledby="editMeasurementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="editMeasurementModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Editar Contómetro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMeasurementForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold small">Surtidor / Lado</label>
                            <input type="text" id="edit_pump_info" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold small">Fecha</label>
                            <input type="date" name="date" id="edit_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small">Valor Inicial</label>
                                <input type="number" step="0.001" name="amount_initial" id="edit_amount_initial" class="form-control form-control-sm text-end fw-bold" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small">Valor Final</label>
                                <input type="number" step="0.001" name="amount_final" id="edit_amount_final" class="form-control form-control-sm text-end fw-bold border-primary" required>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-dark fw-bold small">Diferencia (Inicial - Final)</label>
                            <input type="number" step="0.001" id="edit_amount_difference" class="form-control form-control-sm text-end fw-bold" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-medium px-4">
                            <i class="bi bi-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = new bootstrap.Modal(document.getElementById('editMeasurementModal'));
        const editForm = document.getElementById('editMeasurementForm');
        const editPumpInfo = document.getElementById('edit_pump_info');
        const editDate = document.getElementById('edit_date');
        const editInitial = document.getElementById('edit_amount_initial');
        const editFinal = document.getElementById('edit_amount_final');
        const editDifference = document.getElementById('edit_amount_difference');

        function calcModalDiff() {
            const initVal = parseFloat(editInitial.value) || 0;
            const finalVal = parseFloat(editFinal.value) || 0;
            const diff = initVal - finalVal;
            editDifference.value = diff.toFixed(3);

            editDifference.className = 'form-control form-control-sm text-end fw-bold';
            if (Math.abs(diff) <= 0.02) {
                editDifference.classList.add('bg-success', 'text-white');
            } else if (diff < -0.02) {
                editDifference.classList.add('bg-danger', 'text-white');
            } else {
                editDifference.classList.add('bg-warning', 'text-dark');
            }
        }

        editInitial.addEventListener('input', calcModalDiff);
        editFinal.addEventListener('input', calcModalDiff);

        document.querySelectorAll('.btn-edit-measurement').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const date = this.dataset.date;
                const initial = this.dataset.initial;
                const final = this.dataset.final;
                const pump = this.dataset.pump;

                editForm.action = `/flowmeters/${id}`;
                editPumpInfo.value = pump;
                editDate.value = date;
                editInitial.value = initial;
                editFinal.value = final;

                calcModalDiff();
                editModal.show();
            });
        });
    });
    </script>
@endsection
