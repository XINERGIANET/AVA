@extends('template.index')

@section('header')
<h1>Registro Usuario</h1>
<p>Registrar un nuevo usuario</p>
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card shadow">
        <div class="card-body">
            <form id="formRegistro" action="{{ route('users.store') }}" method="POST" autocomplete="off">
                @csrf
                <!-- Campo Nombre -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="name" class="form-label mb-0">Usuario</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" placeholder="Nombre de Usuario"
                                    id="name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="rol_id" class="form-label mb-0">Rol</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="rol_id" name="rol_id" required>
                                    <option value="">Seleccione un Rol</option>
                                    @foreach ($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="email" class="form-label mb-0">Email</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" placeholder="Email" id="email"
                                    name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="password" class="form-label mb-0">Contraseña</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" placeholder="Contraseña" id="password"
                                    name="password" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="location_id" class="form-label mb-0">Sede</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="location_id" name="location_id" required>
                                    <option value="">Seleccione una sede</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="isle_id" class="form-label mb-0">Isla</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="isle_id" name="isle_id">
                                    <option value="">Seleccione una isla</option>
                                    @foreach ($isles as $isle)
                                    <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Botones -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Sede</th>
                            <th>Isla</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->nombre }}</td>
                            <td>{{ $user->location ? $user->location->name : '-' }}</td>
                            <td>{{ $user->isle ? $user->isle->name : '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-user-btn" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $user->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-user-btn" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay usuarios registrados.</td>
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

<!--Modal de edicion de rol-->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editUserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Editar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label for="edit_name" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_rol_id" class="form-label">Roles</label>
                        <select class="form-control" id="edit_rol_id" name="rol_id" required>
                            <option value="">Seleccione un rol</option>
                        </select>
                        <div class="invalid-feedback" id="edit_rol_id_error"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_location_id" class="form-label">Sede</label>
                        <select class="form-control" id="edit_location_id" name="location_id" required>
                            <option value="">Seleccione una sede</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="edit_location_id_error"></div>
                    </div>
                    <div class="col-md-6 mb-3" id="edit-isle-field-container">
                        <label for="edit_isle_id" class="form-label">Isla Asignada</label>
                        <select class="form-control" id="edit_isle_id" name="isle_id">
                            <option value="">-- Seleccione una isla --</option>
                            @foreach ($isles as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                        <!--workers-->
                        <small class="text-muted">Solo para trabajadores </small>
                        <div class="invalid-feedback" id="edit_isle_id_error"></div>
                    </div>
                    <!-- <div class="col-md-6 mb-3">
                                <label for="shift" class="form-label">Turno</label>
                                <select class="form-control" name="edit_shift" id="edit_shift">
                                    <option value="">Seleccione un turno</option>
                                    <option value="0">Mañana</option>
                                    <option value="1">Tarde</option>
                                </select>
                            </div> -->
                    <div class="col-md-6 mb-3">
                        <label for="new_pass" class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="new_pass" name="new_pass">
                    </div>
                    <!-- <div class="col-md-6 mb-3">
                                <label for="confirm_pass" class="form-label">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="new_pass_confirmation"
                                    name="new_pass_confirmation">
                            </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="editSaveBtn">
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
                <h5 class="modal-title">Eliminar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el usuario <strong id="delete_user_name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
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
                        if (user.role.nombre === 'worker') {
                            $('#edit-isle-field-container').show();
                        } else {
                            $('#edit-isle-field-container').hide();
                        }
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