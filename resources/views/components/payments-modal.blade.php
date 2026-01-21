@php
    $colors = [
        'btn-outline-primary',
        'btn-outline-success',
        'btn-outline-info',
        'btn-outline-warning',
        'btn-outline-danger',
        'btn-outline-dark',
    ];
@endphp
<div class="modal fade" id="paymentsModal" tabindex="-1" aria-labelledby="paymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="paymentsModalLabel">Gestión de Pagos</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Tabla de pagos existentes -->
                <div class="row mb-4">
                    <div class="col-12 d-flex justify-content-center">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>N° Comprobante</th>
                                    <th>Operación</th>
                                    <th>Método</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="modal-pagos">
                                <tr>
                                    <td colspan="7" class="text-center">No hay pagos registrados</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Saldo Pendiente:</th>
                                    <th id="modal-saldo" class="text-danger">S/ 0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row" id="payment-form-container">
                    <div class="col-12">
                        <form id="paymentForm">


                            <!-- Fila 3: Cliente Information -->
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="clientName" class="form-label">Cliente</label>
                                    <input type="text" class="form-control disabled" disabled id="clientName"
                                        name="client_name" placeholder="Nombre del cliente">
                                </div>
                            </div>

                            <!-- Fila 4: Métodos de Pago -->
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Métodos de Pago <span class="text-muted">(Máx: <span
                                                id="max-amount">S/ 0.00</span>)</span></label>
                                    <table class="w-50 small">
                                        @foreach ($paymentMethods as $index => $payment_method)
                                            <tr class="payment-method-item">
                                                <td width="150">
                                                    <input type="checkbox" class="form-check-input me-2 modal-payment-checkbox"
                                                        onchange="toggleModalPaymentMethod(event, '#modal_amount_{{ $payment_method->id }}')"
                                                        id="modal_cbx_amount_{{ $payment_method->id }}" {{ $index == 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $payment_method->name }}</label>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">S/</span>
                                                        <input type="number" step="0.01" class="form-control form-control-sm modal-payment-amount"
                                                            id="modal_amount_{{ $payment_method->id }}" 
                                                            oninput="validateModalPaymentAmount(event)"
                                                            {{ $index == 0 ? '' : 'disabled' }} placeholder="0.00">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    <div class="invalid-feedback d-block" id="payment-amount-error"></div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label for="foto" class="form-label">Foto (opcional)</label>
                                    <input type="file" class="form-control" id="foto" name="foto"
                                        accept="image/*">
                                    <div id="payment-photo-preview" class="mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>

                            <!-- Botón agregar pago -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-success" type="submit" id="btn-register-payment">
                                        <i class="bi bi-plus-circle"></i> Registrar Pago
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden field -->
                            <input type="hidden" id="modal-agreement-id" name="payment_id" value="">
                        </form>
                    </div>
                </div>

                <!-- Mensaje cuando está completamente pagado -->
                <div class="row d-none" id="payment-complete-message">
                    <div class="col-12">
                        <div class="alert alert-success text-center">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                            <h5 class="mt-2">¡Pago Completado!</h5>
                            <p class="mb-0">Este acuerdo ha sido pagado en su totalidad.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales para el modal de pagos
    let currentAgreementId = null;
    let currentSaldo = 0;
    let currentTotal = 0;
    let currentPagado = 0;

    // Función para abrir el modal de pagos
    function openPaymentsModal(paymentId) {
        // Usar payment_id directamente
        currentAgreementId = paymentId;

        // Limpiar campos del formulario
        $('#paymentForm')[0].reset();
        
        // Limpiar todos los checkboxes y inputs de métodos de pago
        $('.modal-payment-checkbox').prop('checked', false);
        $('.modal-payment-amount').val('').prop('disabled', true).removeClass('is-invalid');
        // Marcar el primero como checked por defecto
        $('.modal-payment-checkbox').first().prop('checked', true);
        $('.modal-payment-amount').first().prop('disabled', false);
        $('#payment-amount-error').text('');

        // Establecer el ID en el campo hidden
        $('#modal-agreement-id').val(paymentId);

        // Cargar pagos existentes
        loadPayments();

        // Mostrar el modal
        $('#paymentsModal').modal('show');
    }

    // Función para cargar pagos existentes
    function loadPayments() {
        if (!currentAgreementId) return;

        $.ajax({
            url: "{{ route('payments.get') }}",
            method: 'GET',
            data: {
                payment_id: currentAgreementId // Usamos payment_id directamente
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar variables globales
                    currentTotal = parseFloat(response.total.replace(',', ''));
                    currentPagado = parseFloat(response.total_pagado.replace(',', ''));
                    currentSaldo = parseFloat(response.saldo.replace(',', ''));

                    // Actualizar tabla y saldo
                    updatePaymentsTable(response.payments);
                    $('#modal-saldo').text('S/ ' + response.saldo);
                    $('#max-amount').text('S/ ' + response.saldo);

                    // Actualizar el nombre del cliente
                    if (response.client_name) {
                        $('#clientName').val(response.client_name);
                    }

                    // Validar estado del formulario
                    validatePaymentForm();
                }
            },
            error: function(xhr) {
                console.error('Error al cargar pagos:', xhr);
                ToastError.fire({
                    text: 'Error al cargar los pagos'
                });
            }
        });
    }

    // Función para validar el estado del formulario
    function validatePaymentForm() {
        if (currentSaldo <= 0) {
            // Ocultar formulario y mostrar mensaje de completado
            $('#payment-form-container').addClass('d-none');
            $('#payment-complete-message').removeClass('d-none');
            $('#modal-saldo').removeClass('text-danger').addClass('text-success');
        } else {
            // Mostrar formulario y ocultar mensaje
            $('#payment-form-container').removeClass('d-none');
            $('#payment-complete-message').addClass('d-none');
            $('#modal-saldo').removeClass('text-success').addClass('text-danger');

            // Establecer el máximo del input amount
            $('#amount').attr('max', currentSaldo.toFixed(2));
        }
    }

    // Función para actualizar la tabla de pagos
    function updatePaymentsTable(payments) {
        const tbody = $('#modal-pagos');

        if (payments.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center">No hay pagos registrados</td></tr>');
            return;
        }

        let html = '';
        payments.forEach(function(payment) {
            const fecha = new Date(payment.created_at).toLocaleDateString('es-ES');
            html += `
            <tr>
                <td>${payment.number || '-'}</td>
                
                <td>S/ ${parseFloat(payment.amount).toFixed(2)}</td>
                <td>${payment.payment_method ? payment.payment_method.name : '-'}</td>
                <td>${fecha}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deletePayment(${payment.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        });

        tbody.html(html);
    }

    // Función para habilitar/deshabilitar inputs de métodos de pago
    function toggleModalPaymentMethod(event, inputId) {
        const isChecked = $(event.target).is(':checked');
        $(inputId).prop('disabled', !isChecked);
        if (!isChecked) {
            $(inputId).val('');
        }
        validateModalPaymentAmount();
    }

    // Validación en tiempo real de los montos de métodos de pago
    function validateModalPaymentAmount(event) {
        if (event) {
            validateModalPaymentAmount();
            return;
        }

        let totalAmount = 0;
        let hasError = false;
        const errorDiv = $('#payment-amount-error');
        const submitBtn = $('#btn-register-payment');

        // Calcular el total de todos los métodos de pago seleccionados
        $('.modal-payment-checkbox:checked').each(function() {
            const paymentId = $(this).attr('id').replace('modal_cbx_amount_', '');
            const amount = parseFloat($('#modal_amount_' + paymentId).val()) || 0;
            totalAmount += amount;

            // Validar cada input individual
            const amountInput = $('#modal_amount_' + paymentId);
            if (amount <= 0 && amountInput.val() !== '') {
                amountInput.addClass('is-invalid');
                hasError = true;
            } else {
                amountInput.removeClass('is-invalid');
            }
        });

        // Limpiar error previo
        errorDiv.text('');
        submitBtn.prop('disabled', false);

        // Validar que la suma no exceda el saldo
        if (totalAmount > currentSaldo) {
            errorDiv.text(`La suma de los montos (S/ ${totalAmount.toFixed(2)}) no puede ser mayor al saldo pendiente (S/ ${currentSaldo.toFixed(2)})`);
            hasError = true;
            submitBtn.prop('disabled', true);
        } else if (totalAmount <= 0) {
            errorDiv.text('Debe ingresar al menos un monto mayor a 0');
            hasError = true;
            submitBtn.prop('disabled', true);
        }

        return !hasError;
    }

    // Función para eliminar un pago
    function deletePayment(paymentId) {
        if (confirm('¿Está seguro de que desea eliminar este pago?')) {
            $.ajax({
                url: `/payments/${paymentId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        ToastMessage.fire({
                            text: response.message
                        });
                        // SOLO recargar los datos del modal, NO la página
                        loadPayments();
                    }
                },
                error: function(xhr) {
                    ToastError.fire({
                        text: 'Error al eliminar el pago'
                    });
                }
            });
        }
    }

    // Manejar el envío del formulario de pagos
    $(document).on('submit', '#paymentForm', function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario

        // Recopilar todos los métodos de pago seleccionados
        let totalAmount = 0;
        let paymentMethods = [];

        $('.modal-payment-checkbox:checked').each(function() {
            const paymentId = $(this).attr('id').replace('modal_cbx_amount_', '');
            const amount = parseFloat($('#modal_amount_' + paymentId).val()) || 0;

            if (amount > 0) {
                totalAmount += amount;
                paymentMethods.push({
                    payment_method_id: parseInt(paymentId),
                    amount: amount
                });
            }
        });

        // Validación final antes de enviar
        if (totalAmount > currentSaldo) {
            ToastError.fire({
                text: `La suma de los montos (S/ ${totalAmount.toFixed(2)}) no puede ser mayor al saldo pendiente (S/ ${currentSaldo.toFixed(2)})`
            });
            return false; // Detener el proceso
        }

        if (paymentMethods.length === 0 || totalAmount <= 0) {
            ToastError.fire({
                text: 'Debe seleccionar al menos un método de pago con un monto mayor a 0'
            });
            return false; // Detener el proceso
        }

        // Mostrar spinner solo en el botón
        const submitBtn = $('#btn-register-payment');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="bi bi-arrow-clockwise spin"></i> Procesando...').prop('disabled', true);

        const formData = new FormData(this);
        
        // Agregar los métodos de pago como array
        paymentMethods.forEach(function(pm, index) {
            formData.append(`payment_methods[${index}][payment_method_id]`, pm.payment_method_id);
            formData.append(`payment_methods[${index}][amount]`, pm.amount);
        });

        $.ajax({
            url: "{{ route('payments.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    ToastMessage.fire({
                        icon: 'success',
                        text: response.message
                    });

                    // Limpiar SOLO los campos del formulario, mantener el agreement_id
                    $('#tipoComprobante').val('');
                    $('#numeroComprobante').val('');
                    $('#nombreOperacion').val('');
                    $('#clientDocument').val('');
                    $('#clientName').val('');
                    // Limpiar todos los checkboxes y inputs de métodos de pago
                    $('.modal-payment-checkbox').prop('checked', false);
                    $('.modal-payment-amount').val('').prop('disabled', true).removeClass('is-invalid');
                    // Marcar el primero como checked por defecto
                    $('.modal-payment-checkbox').first().prop('checked', true);
                    $('.modal-payment-amount').first().prop('disabled', false);
                    $('#payment-amount-error').text('');
                    $('#foto').val('');
                    $('#payment-photo-preview').empty();

                    // SOLO recargar los datos del modal, NO la página
                    loadPayments();
                    if (response.message && response.message.includes('totalmente')) {
                        setTimeout(function() {
                            $('#paymentsModal').modal('hide');
                            location.reload();
                        }, 1500);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    ToastError.fire({
                        text: errors.join('\n')
                    });
                } else {
                    ToastError.fire({
                        text: 'Error al registrar el pago'
                    });
                }
            },
            complete: function() {
                // Restaurar el botón
                submitBtn.html(originalText).prop('disabled', false);
            }
        });

        return true; // Asegurar que no se recargue la página
    });

    $(document).on('change', '#foto', function() {
        const preview = $('#payment-photo-preview').empty();
        const file = this.files && this.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) {
            $(this).val('');
            ToastError.fire({
                text: 'El archivo seleccionado no es una imagen.'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = $('<img>')
                .attr('src', e.target.result)
                .css({
                    'width': '120px',
                    'height': '80px',
                    'object-fit': 'cover',
                    'border-radius': '4px',
                    'border': '1px solid #ddd'
                });
            preview.append(img);
        };
        reader.readAsDataURL(file);
    });
</script>
