@extends('template.index')

@section('header')
    <div class="d-flex align-items-center">
        <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Usuarios</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Usuarios</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid content-inner" style="padding-top: 1rem;">
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <!-- Toolbar superior de la tarjeta -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-9">
                    <form action="{{ route('users.index') }}" method="GET" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="filter_search" class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;">Buscar por Usuario o Email</label>
                                <input type="text" name="search" id="filter_search" class="form-control form-control-sm" placeholder="Ej. jperez..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createModal" style="border-radius: 6px;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border: 1px solid #e9ecef;">
                    <thead class="text-center">
                        <tr>
                            <th class="fw-bold text-uppercase" style="width: 5%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">N°</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Usuario</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Email</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Rol</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Sede</th>
                            <th class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Isla</th>
                            <th class="pe-4 text-center fw-bold text-uppercase" style="width: 15%; font-size: 0.75rem; letter-spacing: 0.5px; background-color: #2c3e50 !important; color: white !important;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($users as $user)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td class="text-dark">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                <td class="text-dark">{{ $user->name }}</td>
                                <td class="text-dark">{{ $user->email }}</td>
                                <td class="text-dark">{{ $user->role->nombre }}</td>
                                <td class="text-dark">{{ $user->location ? $user->location->name : '-' }}</td>
                                <td class="text-dark">{{ $user->isle ? $user->isle->name : '-' }}</td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-sm btn-warning text-white me-1 edit-user-btn" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#editModal" data-id="{{ $user->id }}" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger text-white delete-user-btn" style="border-radius: 4px; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formRegistro" action="{{ route('users.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label text-dark fw-bold">Usuario</label>
                        <input type="text" class="form-control" placeholder="Nombre de Usuario" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="rol_id" class="form-label text-dark fw-bold">Rol</label>
                        <select class="form-select" id="rol_id" name="rol_id" required>
                            <option value="">Seleccione un Rol</option>
                            @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label text-dark fw-bold">Email</label>
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label text-dark fw-bold">Contraseña</label>
                        <input type="password" class="form-control" placeholder="Contraseña" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label text-dark fw-bold">Sede</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="">Seleccione una sede</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="isle_id" class="form-label text-dark fw-bold">Isla</label>
                        <select class="form-select" id="isle_id" name="isle_id">
                            <option value="">Seleccione una isla</option>
                            @foreach ($isles as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Solo aplicable para Rol Worker.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de edición de rol -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editUserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_name" class="form-label text-dark fw-bold">Usuario</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_email" class="form-label text-dark fw-bold">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_rol_id" class="form-label text-dark fw-bold">Roles</label>
                        <select class="form-select" id="edit_rol_id" name="rol_id" required>
                            <option value="">Seleccione un rol</option>
                        </select>
                        <div class="invalid-feedback" id="edit_rol_id_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_location_id" class="form-label text-dark fw-bold">Sede</label>
                        <select class="form-select" id="edit_location_id" name="location_id" required>
                            <option value="">Seleccione una sede</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="edit_location_id_error"></div>
                    </div>
                    <div class="col-md-6 mb-3" id="edit-isle-field-container">
                        <label for="edit_isle_id" class="form-label text-dark fw-bold">Isla Asignada</label>
                        <select class="form-select" id="edit_isle_id" name="isle_id">
                            <option value="">-- Seleccione una isla --</option>
                            @foreach ($isles as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Solo para trabajadores</small>
                        <div class="invalid-feedback" id="edit_isle_id_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="new_pass" class="form-label text-dark fw-bold">Nueva contraseña</label>
                        <input type="password" class="form-control" id="new_pass" name="new_pass">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="editSaveBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">¿Estás seguro de que deseas eliminar el usuario <strong id="delete_user_name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            let currentUserId = null;

            $('.edit-user-btn').on('click', function() {
                currentUserId = $(this).data('id');

                $('#editUserForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#editModal').modal('show');

                $.ajax({
                    url: "{{ route('users.edit', ':id') }}".replace(':id', currentUserId),
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const user = response.data.user;
                            const roles = response.data.roles;

                            $('#edit_name').val(user.name);
                            $('#edit_email').val(user.email);
                            $('#edit_shift').val(user.shift);
                            $('#edit_location_id').val(user.location_id);
                            $('#edit_isle_id').val(user.isle_id);
                            //Llenar select de rol
                            $('#edit_rol_id').empty().append(
                                '<option value="">Seleccione un rol</option>'
                            )
                            roles.forEach(function(rol) {
                                const selected = (rol.id == user.role_id) ? 'selected' :
                                    '';
                                $('#edit_rol_id').append(
                                    `<option value="${rol.id}" ${selected}>${rol.nombre}</option>`
                                )
                            });
                        } else {
                            ToastMessage.fire({
                                icon: 'error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        ToastMessage.fire({
                            icon: 'error',
                            text: 'Error al cargar los datos del producto'
                        });
                    }
                });
            });

            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                const saveBtn = $('#editSaveBtn');
                const spinner = saveBtn.find('.spinner-border');

                // Mostrar loading
                saveBtn.prop('disabled', true);
                spinner.removeClass('d-none');

                // Limpiar errores previos
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: "{{ route('users.update', ':id') }}".replace(':id', currentUserId),
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: $('#edit_name').val(),
                        email: $('#edit_email').val(),
                        rol_id: $('#edit_rol_id').val(),
                        location_id: $('#edit_location_id').val(),
                        isle_id: $('#edit_isle_id').val(),
                        shift: $('#edit_shift').val(),
                        new_pass: $('#new_pass').val(),
                        // new_pass_confirmation: $('#new_pass_confirmation').val()
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#editModal').modal('hide');
                            ToastMessage.fire({
                                icon: 'success',
                                text: response.message
                            });
                            location.reload(); // o recarga la tabla
                        } else {
                            ToastMessage.fire({
                                icon: 'error',
                                text: 'No se pudo actualizar el usuario'
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Mostrar toast personalizado si el error es de contraseña
                            if (errors.new_pass) {
                                ToastMessage.fire({
                                    icon: 'error',
                                    text: 'Error al confirmar contraseña'
                                });
                            }

                            // Marcar inputs como inválidos
                            for (const field in errors) {
                                $(`#edit_${field}`).addClass('is-invalid');
                                $(`#edit_${field}_error`).text(errors[field][0]);
                            }
                        }
                    },
                    complete: function() {
                        // Ocultar loading
                        saveBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });
            $('.delete-user-btn').on('click', function() {
                currentUserId = $(this).data('id');
                const userName = $(this).data('name');

                $('#delete_user_name').text(userName);
                $('#deleteModal').modal('show');
            })

             // Confirmar eliminación
            $('#confirmDeleteBtn').on('click', function() {
                const deleteBtn = $(this);
                const spinner = deleteBtn.find('.spinner-border');

                // Mostrar loading
                deleteBtn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('users.destroy', ':id') }}".replace(':id', currentUserId),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');

                        if (response.status) {
                            ToastMessage.fire({
                                icon: 'success',
                                text: response.message
                            });

                            // Recargar la página después de 1 segundo
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            ToastMessage.fire({
                                icon: 'error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        const message = xhr.responseJSON?.message ||
                            'Error al eliminar el usuario';
                        ToastMessage.fire({
                            icon: 'error',
                            text: message
                        });
                    },
                    complete: function() {
                        // Ocultar loading
                        deleteBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            // Limpiar modales al cerrar
            $('#editModal').on('hidden.bs.modal', function() {
                $('#editUserForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                currentUserId = null;
            });

            $('#deleteModal').on('hidden.bs.modal', function() {
                currentUserId = null;
            });
        });
    </script>
@endsection
