<div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contractModalLabel">Detalles de ventas asociadas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        @php

        $agreementId = optional(optional(optional($saleDetails->first())->order_detail)->order)->agreement_id ?? null;
        // Agrupado robusto: usar data_get para leer sale.closing_number,
        // normalizar clave: 'sin_asignar' o 'cierre_#{closing_number}'
        $groups = $saleDetails->groupBy(function($sd){
            $cn = data_get($sd, 'closing_number');
            return (is_null($cn) || (string)$cn === '') ? 'sin_asignar' : 'cierre_' . (string) $cn;
        });

        // obtener todas las keys
        $allKeys = $groups->keys()->all();

        // separar 'sin_asignar' de las demás
        $otherKeys = array_values(array_filter($allKeys, function($k){
            return $k !== 'sin_asignar';
        }));

        // ordenar las demás por el número que sigue a 'cierre_'
        usort($otherKeys, function($a, $b){
            $prefixLen = strlen('cierre_');
            $na = (int) substr($a, $prefixLen);
            $nb = (int) substr($b, $prefixLen);
            return $na <=> $nb;
        });

        // reconstruir keys: 'sin_asignar' primero si existe, luego los cierres ordenados asc
        $keys = [];
        if (in_array('sin_asignar', $allKeys, true)) {
            $keys[] = 'sin_asignar';
        }
        $keys = array_merge($keys, $otherKeys);

        // primer tab activo: preferir 'sin_asignar' si existe
        $activeKey = in_array('sin_asignar', $keys, true) ? 'sin_asignar' : ($keys[0] ?? null);
        @endphp

        @if(empty($keys))
        <div class="text-center text-muted">No hay detalles de venta asociados.</div>
        @else
        <ul class="nav nav-tabs mb-3" role="tablist">
            @foreach($keys as $k)
            @php
                $isActive = ($k === $activeKey) ? 'active' : '';
                // label: si es sin_asignar mostrar "Sin asignar", si es cierre_# extraer #
                if ($k === 'sin_asignar') {
                $label = 'Sin asignar';
                } else {
                $num = substr($k, strlen('cierre_'));
                $label = 'Cierre N°' . $num;
                }
                $count = $groups[$k]->count();
                $tabId = 'tab_' . $k; // ya es seguro como id (sin espacios)
            @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $isActive }}" id="{{ $tabId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $tabId }}" type="button" role="tab" aria-controls="{{ $tabId }}" aria-selected="{{ $isActive ? 'true' : 'false' }}">
                 {{ $label }} 
                <!-- <span class="badge bg-secondary ms-1">{{ $count }}</span> -->
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($keys as $k)
            @php
                $isActive = ($k === $activeKey) ? 'show active' : '';
                $tabId = 'tab_' . $k;
                $items = $groups[$k];
            @endphp

            <div class="tab-pane fade {{ $isActive }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                <!-- tabla como ya la tienes usando $items -->
                <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            @if($k === 'sin_asignar')
                            <th style="width:40px;"></th>
                            @endif
                            <th>Comprobante</th>
                            <th>Orden</th>
                            <th>Área</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $sd)
                        <tr>
                            @if($k === 'sin_asignar')
                            <td>
                                <!-- agrego data-sale-id, data-product-id y data-agreement-id -->
                                <input type="checkbox"
                                    class="unassigned-checkbox form-check-input"
                                    value="{{ $sd->id }}"
                                    data-sale-id="{{ $sd->sale_id }}"
                                    data-product-id="{{ optional($sd->order_detail->product)->id ?? $sd->product_id }}"
                                    data-agreement-id="{{ optional(optional($sd->order_detail)->order)->agreement_id ?? '' }}" />
                            </td>
                            @endif
                            <td>Venta #{{ $sd->sale_id }}</td>
                            <td>{{ $sd->order_detail->order->number }}</td>
                            <td>{{ optional($sd->order_detail)->area ?? '-' }}</td>
                            <td>{{ $sd->order_detail->product->name }}</td>
                            <td>{{ $sd->quantity }}</td>
                            <td>{{ $sd->subtotal }}</td>
                            <td>{{ optional($sd->sale->date)->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total: {{ $items->sum('subtotal') }}</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
            @endforeach
        </div>
        @endif
      </div>

      <div class="modal-footer">
        @php
            $showExcel = $activeKey && strpos($activeKey, 'cierre_') === 0;
        @endphp
        <button id="btnExcel" type="button" class="btn btn-success" onclick="generateExcel()" @if(!$showExcel) style="display:none;" @endif>
            Excel
        </button>
        <button id="btnGenerateClosing" type="button" class="btn btn-primary" disabled onclick="generateClosing()">
          Generar cierre <span id="selectedCount" class="badge bg-light text-dark ms-2">0</span>
        </button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script>
    // habilitar/deshabilitar el botón si hay al menos un checkbox marcado
    function toggleGenerateButton() {
        const checkedCount = $('.unassigned-checkbox:checked').length;
        $('#btnGenerateClosing').prop('disabled', checkedCount === 0);
        $('#selectedCount').text(checkedCount);
    }

    function updateExcelButtonState() {
        const $activeTabLink = $('.nav-tabs .nav-link.active');
        let target = $activeTabLink.attr('data-bs-target') || $activeTabLink.data('bs-target');

        if (!target) {
            const activePaneId = $('.tab-content .tab-pane.show.active').attr('id') || '';
            target = activePaneId ? '#' + activePaneId : '';
        }

        const key = target ? target.replace('#tab_', '') : null;
        const show = key && key.startsWith('cierre_');
        $('#btnExcel').toggle(show);
    }


    // cuando cambien checkboxes
    $(document).on('change', '.unassigned-checkbox', function() {
        toggleGenerateButton();
        updateExcelButtonState();
    });

    // al mostrar el modal, asegurar estado correcto
    $(document).on('shown.bs.modal', '#contractModal', function () {
        toggleGenerateButton();
        updateExcelButtonState();
    });

    // al cerrar el modal, limpiar selección
    $(document).on('hidden.bs.modal', '#contractModal', function () {
        $('.unassigned-checkbox').prop('checked', false);
        toggleGenerateButton();
        updateExcelButtonState();
    });

    $(document).on('shown.bs.tab', '.nav-tabs .nav-link', function () {
        updateExcelButtonState();
    });

    // acción del botón: ejemplo de envío AJAX con los ids seleccionados
    function generateClosing(){
        const $btn = $(this);
        const $checked = $('.unassigned-checkbox:checked');

        if ($checked.length === 0) return;

        // construir details array { sale_id, product_id }
        const details = $checked.map(function() {
            return {
                sale_id: Number($(this).data('sale-id')) || null,
                product_id: Number($(this).data('product-id')) || null
            };
        }).get();

        // intentar obtener agreementId (desde checkbox o variable blade)
        let agreementId = $checked.first().data('agreement-id') || {{ $agreementId !== null ? $agreementId : 'null' }};

        $btn.prop('disabled', true);
        $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');

        $.ajax({
            url: "{{ route('contracts.generate_closing') }}", // asegúrate de tener esta ruta
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                details: details,
                agreement_id: agreementId,
            },
            success: function(resp) {
                if (resp && resp.success) {
                    // regenerar modal: llamar a verDetalles(agreementId) si existe
                    if (typeof verDetalles === 'function') {
                        $('#contractModal').modal('hide');
                        verDetalles(agreementId);
                    } else {
                        // fallback: recargar página
                        location.reload();
                    }
                } else {
                    ToastError.fire({ text: resp.message || 'Error al generar cierre.' });
                    $btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                let msg = 'Error al generar cierre.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                ToastError.fire({ text: msg });
                $btn.prop('disabled', false);
            },
            complete: function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
                toggleGenerateButton();
            }
        });
    };

    let excelSending = false;

    function generateExcel() {
        if (excelSending) return;

        // obtener el target de la pestaña activa (misma lógica que updateExcelButtonState)
        const $activeTabLink = $('.nav-tabs .nav-link.active');
        let target = $activeTabLink.attr('data-bs-target') || $activeTabLink.data('bs-target');

        if (!target) {
            const activePaneId = $('.tab-content .tab-pane.show.active').attr('id') || '';
            target = activePaneId ? '#' + activePaneId : '';
        }

        const key = target ? target.replace('#tab_', '') : null;

        if (!key || key === 'sin_asignar') {
            ToastError.fire({ text: 'No hay cierre seleccionado para exportar.' });
            return;
        }

        // extraer número del key 'cierre_X'
        let closingNumber = null;
        if (key.startsWith('cierre_')) {
            closingNumber = parseInt(key.replace('cierre_', ''), 10);
        }

        if (!closingNumber || isNaN(closingNumber)) {
            ToastError.fire({ text: 'Número de cierre inválido.' });
            return;
        }

        const agreementId = {{ $agreementId !== null ? $agreementId : 'null' }};
        if (!agreementId) {
            ToastError.fire({ text: 'Agreement no disponible.' });
            return;
        }

        // crear formulario dinámico para POST y descargar el Excel
        excelSending = true;
        $('#btnExcel').prop('disabled', true);
        const $form = $('<form>', {
            method: 'POST',
            action: "{{ route('contracts.export_closing') }}",
            css: { display: 'none' }
        });

        // token y campos
        $form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
        $form.append($('<input>', { type: 'hidden', name: 'closing_number', value: closingNumber }));
        $form.append($('<input>', { type: 'hidden', name: 'agreement_id', value: agreementId }));

        // anexar, enviar y limpiar
        $('body').append($form);
        $form.get(0).submit();

        // limpiar (no podemos detectar descarga completa fácilmente)
        setTimeout(function(){
            $form.remove();
            excelSending = false;
            updateExcelButtonState();
            $('#btnExcel').prop('disabled', false);
        }, 1500);
    }
</script>