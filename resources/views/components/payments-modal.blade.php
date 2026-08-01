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
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <!-- Modal Header Sobrio -->
            <div class="modal-header px-4 py-3 text-white" style="background-color: #1e293b;">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center mb-0" id="paymentsModalLabel" style="font-size: 1.1rem;">
                    <i class="bi bi-wallet2 me-2"></i>Gestión de Pagos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" style="background-color: #ffffff;">
                <!-- Resumen Superior Simétrico en 3 Cajas Blancas de Limpios Bordes -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded border text-center bg-white shadow-xs">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Monto Total</small>
                            <span class="fs-5 fw-bold text-dark" id="modal-total-display">S/ 0.00</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded border text-center bg-white shadow-xs">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Pagado</small>
                            <span class="fs-5 fw-bold text-success" id="modal-pagado-display">S/ 0.00</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded border text-center bg-white shadow-xs">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Saldo Pendiente</small>
                            <span class="fs-5 fw-bold text-danger" id="modal-saldo">S/ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Disposición Simétrica de 2 Columnas -->
                <div class="row g-4">
                    <!-- Columna Izquierda: Historial de Pagos -->
                    <div class="col-lg-5 border-end pe-lg-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-clock-history me-2 text-secondary"></i>Historial de Pagos
                            </h6>
                        </div>

                        <div class="table-responsive rounded border" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-hover table-sm align-middle mb-0 text-center">
                                <thead style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th class="fw-bold text-uppercase small py-2" style="background-color: #2c3e50 !important; color: white !important;">Ticket</th>
                                        <th class="fw-bold text-uppercase small py-2" style="background-color: #2c3e50 !important; color: white !important;">Método</th>
                                        <th class="fw-bold text-uppercase small py-2" style="background-color: #2c3e50 !important; color: white !important;">Fecha</th>
                                        <th class="fw-bold text-uppercase small py-2" style="background-color: #2c3e50 !important; color: white !important;">Monto</th>
                                        <th class="fw-bold text-uppercase small py-2" style="background-color: #2c3e50 !important; color: white !important;"></th>
                                    </tr>
                                </thead>
                                <tbody id="modal-pagos">
                                    <tr>
                                        <td colspan="5" class="text-muted py-4">
                                            No hay pagos registrados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Columna Derecha: Formulario de Registro -->
                    <div class="col-lg-7 ps-lg-4" id="payment-form-container">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-plus-circle me-2 text-success"></i>Registrar Pago
                            </h6>
                            <span class="badge bg-white text-dark border fw-normal" style="font-size: 0.75rem;">
                                Máx. a pagar: <strong id="max-amount" class="text-primary">S/ 0.00</strong>
                            </span>
                        </div>

                        <form id="paymentForm">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="clientName" class="form-label small fw-bold text-secondary mb-1">Cliente</label>
                                    <input type="text" class="form-control form-control-sm bg-white fw-bold" disabled id="clientName"
                                        name="client_name" placeholder="Cliente" style="color: #1e293b !important;">
                                </div>
                                <div class="col-md-6">
                                    <label for="foto" class="form-label small fw-bold text-secondary mb-1">Foto (opcional)</label>
                                    <input type="file" class="form-control form-control-sm" id="foto" name="foto" accept="image/*">
                                    <div id="payment-photo-preview" class="mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>

                            <!-- Métodos de Pago Simétricos -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary mb-2">Métodos de Pago</label>
                                <div class="p-3 rounded border bg-white">
                                    <div class="row g-2">
                                        @foreach ($paymentMethods as $index => $payment_method)
                                            <div class="col-6">
                                                <div class="p-2 border rounded bg-white d-flex align-items-center justify-content-between">
                                                    <div class="form-check mb-0 flex-grow-1 text-truncate">
                                                        <input type="checkbox" class="form-check-input modal-payment-checkbox me-2"
                                                            onchange="toggleModalPaymentMethod(event, '#modal_amount_{{ $payment_method->id }}')"
                                                            id="modal_cbx_amount_{{ $payment_method->id }}" {{ $index == 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark small fw-medium" for="modal_cbx_amount_{{ $payment_method->id }}">
                                                            {{ $payment_method->name }}
                                                        </label>
                                                    </div>
                                                    <div class="input-group input-group-sm ms-1" style="width: 110px;">
                                                        <span class="input-group-text bg-light text-muted px-1" style="font-size: 0.75rem;">S/</span>
                                                        <input type="number" step="0.01" class="form-control form-control-sm modal-payment-amount fw-bold px-1"
                                                            id="modal_amount_{{ $payment_method->id }}" 
                                                            oninput="validateModalPaymentAmount(event)"
                                                            {{ $index == 0 ? '' : 'disabled' }} placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="invalid-feedback d-block fw-bold mt-2" id="payment-amount-error"></div>
                            </div>

                            <!-- Botón agregar pago -->
                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-success btn-sm fw-bold px-4 py-2" type="submit" id="btn-register-payment" style="border-radius: 6px;">
                                    <i class="bi bi-check-lg me-1"></i> Registrar Pago
                                </button>
                            </div>

                            <!-- Hidden field -->
                            <input type="hidden" id="modal-agreement-id" name="payment_id" value="">
                        </form>
                    </div>

                    <!-- Mensaje cuando está completamente pagado -->
                    <div class="col-lg-7 ps-lg-4 d-none" id="payment-complete-message">
                        <div class="alert alert-success text-center border-0 p-4 rounded">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                            <h5 class="mt-2 fw-bold text-success">¡Pago Completado!</h5>
                            <p class="mb-0 text-muted small">Este crédito ha sido pagado en su totalidad.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white px-4 py-2 border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 6px;">
                    <i class="bi bi-x-circle me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales para el modal de pagos
    let currentId = null;
    let currentType = 'agreement';
    let currentSaldo = 0;
    let currentTotal = 0;
    let currentPagado = 0;

    // Función para abrir el modal de pagos
    function openPaymentsModal(id, type = 'agreement') {
        currentId = id;
        currentType = type;

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
        $('#modal-agreement-id').val(id);
        // Cambiar el nombre del campo oculto según el tipo
        $('#modal-agreement-id').attr('name', type === 'payment' ? 'payment_id' : 'agreement_id');

        // Cargar pagos existentes
        loadPayments();

        // Mostrar el modal
        $('#paymentsModal').modal('show');
    }

    // Función para cargar pagos existentes
    function loadPayments() {
        if (!currentId) return;

        let requestData = {};
        if (currentType === 'payment') {
            requestData.payment_id = currentId;
        } else {
            requestData.agreement_id = currentId;
        }

        $.ajax({
            url: "{{ route('payments.get') }}",
            method: 'GET',
            data: requestData,
            success: function(response) {
                if (response.success) {
                    // Actualizar variables globales
                    currentTotal = parseFloat(response.total.replace(',', ''));
                    currentPagado = parseFloat(response.total_pagado.replace(',', ''));
                    currentSaldo = parseFloat(response.saldo.replace(',', ''));

                    // Actualizar resumen y saldo
                    updatePaymentsTable(response.payments);
                    $('#modal-total-display').text('S/ ' + response.total);
                    $('#modal-pagado-display').text('S/ ' + response.total_pagado);
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
            tbody.html('<tr><td colspan="5" class="text-muted py-4 small">No hay pagos registrados</td></tr>');
            return;
        }

        let html = '';
        payments.forEach(function(payment) {
            const fecha = new Date(payment.created_at).toLocaleDateString('es-ES');
            const metodoPagoNombre = payment.payment_method ? payment.payment_method.name : '-';
            html += `
            <tr>
                <td class="fw-bold text-dark small">${payment.number || '-'}</td>
                <td><span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">${metodoPagoNombre}</span></td>
                <td class="text-muted small">${fecha}</td>
                <td class="fw-bold text-success small">S/ ${parseFloat(payment.amount).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deletePayment(${payment.id})" title="Eliminar">
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
            errorDiv.text(`La suma (S/ ${totalAmount.toFixed(2)}) supera el saldo pendiente (S/ ${currentSaldo.toFixed(2)})`);
            hasError = true;
            submitBtn.prop('disabled', true);
        } else if (totalAmount <= 0) {
            errorDiv.text('Ingrese un monto mayor a 0');
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
                text: `La suma (S/ ${totalAmount.toFixed(2)}) supera el saldo pendiente (S/ ${currentSaldo.toFixed(2)})`
            });
            return false;
        }

        if (paymentMethods.length === 0 || totalAmount <= 0) {
            ToastError.fire({
                text: 'Debe seleccionar al menos un método de pago con un monto mayor a 0'
            });
            return false;
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

        return true;
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
