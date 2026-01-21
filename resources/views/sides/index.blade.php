@extends('template.index')

@section('header')
    <h1>Lados</h1>
    <p>Lista de Lados de Surtidores</p>
@endsection

@section('content')
    @include('components.spinner')

    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card shadow">
            <div class="card-body">

                {{-- FORMULARIO DE REGISTRO --}}
                <form id="createSideForm" class="mb-5" action="{{ route('sides.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">

                        {{-- Nombre --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Lado</label>
                            <input type="text" class="form-control" placeholder="Ingrese nombre" id="name"
                                name="name" required>
                        </div>

                        {{-- Surtidor --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Surtidor</label>
                            <select name="fuel_pump_id" id="fuel_pump_id" class="form-control" required>
                                <option value="">-- Seleccione un surtidor --</option>
                                @foreach ($fuelpumps as $fuelpump)
                                    <option value="{{ $fuelpump->id }}">{{ $fuelpump->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Agregar lado</button>
                    </div>

                </form>

                {{-- TABLA DE REGISTROS --}}
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Surtidor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- Iterar sobre lados --}}
                            @foreach ($sides as $side)
                                <tr>
                                    <td>{{ ($sides->currentPage() - 1) * $sides->perPage() + $loop->iteration }}</td>
                                    <td>{{ $side->name }}</td>
                                    <td>{{ $side->fuelpump->name ?? 'Sin surtidor' }}</td>

                                    <td>
                                        {{-- Botón editar --}}
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $side->id }}"
                                            data-name="{{ $side->name }}" data-fuelpump="{{ $side->fuel_pump_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Botón eliminar --}}
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $side->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    {{-- Paginación --}}
                    {{ $sides->links() }}

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editSideForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Lado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Surtidor</label>
                            <select name="fuel_pump_id" id="edit_fuel_pump_id" class="form-control" required>
                                @foreach ($fuelpumps as $fuelpump)
                                    <option value="{{ $fuelpump->id }}">{{ $fuelpump->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="deleteSideForm" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar lado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar este lado?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // EDITAR
            $('#editModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');
                const fuel_pump_id = button.data('fuelpump');

                $('#editSideForm').attr('action', `{{ url('sides') }}/${id}`);
                $('#edit_name').val(name);
                $('#edit_fuel_pump_id').val(fuel_pump_id);
            });

            // ELIMINAR
            $('#deleteModal').on('show.bs.modal', function(event) {
                const id = $(event.relatedTarget).data('id');
                $('#deleteSideForm').attr('action', `{{ url('sides') }}/${id}`);
            });

            // Spinner
            $('form').on('submit', function() {
                $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
            });

        });
    </script>
@endsection
