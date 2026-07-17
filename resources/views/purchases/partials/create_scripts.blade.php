    <style>
        .cantidad-input {
            width: 100px;
        }

        /* Limita la altura del menú y añade scroll vertical */
        .ui-autocomplete {
            max-height: 200px;
            /* ajusta la altura a tu gusto */
            overflow-y: auto;
            /* habilita scroll vertical */
            overflow-x: hidden;
            /* evita scroll horizontal */
            /* opcional: para que no tape otros elementos */
            z-index: 1000;
        }

        /* Opcional: mejorar visibilidad de cada ítem */
        .ui-menu-item-wrapper {
            white-space: nowrap;
            padding: 4px 8px;
        }

        /* Resalta toda la fila del tanque marcado, no solo el checkbox */
        .tank-item.is-checked,
        .tank-item:has(.tank-checkbox:checked) {
            background-color: rgba(58, 87, 232, 0.08);
            border-radius: 0.375rem;
        }
        .tank-item.is-checked .tank-name,
        .tank-item:has(.tank-checkbox:checked) .tank-name {
            font-weight: 600;
            color: #3a57e8;
        }
    </style>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <script>
        function collectTableData() {
            const products = [];

            $('#purchaseTable tbody tr').each(function() {
                const row = $(this);
                const productId = row.data('product-id');
                const tankId = row.data('tank-id') || null;
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.unit_price').val()) || 0;
                const subtotal = parseFloat(row.find('.subtotal').val()) || 0;

                if (quantity > 0) {
                    const productData = {
                        product_id: productId,
                        tank_id: tankId,
                        quantity: quantity,
                        price: price,
                        subtotal: subtotal
                    };

                    // Si es un producto nuevo (ID negativo), agregar datos adicionales
                    if (productId < 0) {
                        productData.category_id = row.data('category-id');
                        productData.nombre = row.data('nombre');
                        productData.unidad_medida = row.data('unidad-medida');
                    }

                    products.push(productData);
                }
            });

            return products;
        }



        var suppliers = @json($suppliers);
        var newproducts = @json($products);
        var selectedProducts = [];
        var purchaseSpinner = document.getElementById('spinner');


        function handleProductClickSelect(productId) {
            // Buscar el producto en la lista
            const selectedProduct = newproducts.find(p => p.id === productId);

            if (!selectedProduct) return;

            // Verificar si ya existe en la tabla
            const existingRow = $(`#purchaseTable tr[data-product-id="${productId}"]`);

            if (existingRow.length > 0) {
                // Si existe, incrementar cantidad
                const quantityInput = existingRow.find('.quantity');
                const currentQty = parseInt(quantityInput.val()) || 0;
                quantityInput.val(currentQty + 1);
            } else {
                // Si no existe, agregar nueva fila
                const newRow = `
                <tr data-product-id="${productId}" data-unit="${selectedProduct.measurement_unit}">
                    <td>${selectedProduct.name}</td>
                    <td>${selectedProduct.measurement_unit}</td>
                    <td><input type="number" class="form-control text-end unit_price" step="0.01" min="0" disabled></td>
                    <td><input type="number" class="form-control text-end quantity" min="0.001" step="0.001"></td>
                    <td><input type="number" class="form-control text-end subtotal" min="0.001" step="0.001"></td>
                    <td><input type="number" class="form-control text-end waste" step="0.001" value="0"></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#purchaseTable tbody').append(newRow);
                attachEventsToRows();
            }

            // Limpiar campo de búsqueda
            $('#search-product').val('');
        }

        $('#search-supplier').autocomplete({
                source: function(request, response) {
                    var matches = $.grep(suppliers, function(item) {
                        return item.company_name.toLowerCase()
                            .includes(request.term.toLowerCase());
                    });
                    matches = matches.slice(0, 10);
                    var results = $.map(matches, function(item) {
                        return {
                            label: item.company_name,
                            value: item.company_name,
                            id: item.id
                        };
                    });
                    response(results);
                },
                select: function(event, ui) {
                    $('#supplier_id').val(ui.item.id); // Guardar el ID en campo oculto
                    //cargarProductosProveedor(ui.item.id); no hay productos por proveedor
                },
                appendTo: '#createPurchaseModal .modal-body'
            })
            .autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append(`<div class="d-flex justify-content-between"><span>${item.label}</span></div>`)
                    .appendTo(ul);
            };

        function attachEventsToRows() {
            // Cuando cambien cantidad o precio unitario, recalcular subtotal = precio * cantidad
            $('#purchaseTable').on('input', '.quantity, .unit_price', function() {
                const row = $(this).closest('tr');
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const unitPrice = parseFloat(row.find('.unit_price').val()) || 0;
                const subtotalField = row.find('.subtotal');

                if (quantity > 0 && unitPrice > 0) {
                    const subtotal = (unitPrice * quantity).toFixed(2);
                    subtotalField.val(subtotal);
                } else {
                    // Si no hay datos suficientes, limpiar subtotal
                    subtotalField.val('');
                }

                // Actualizar total general
                updateTotal();
            });
        }


        $('#purchaseForm').on('submit', function(e) {
            e.preventDefault();

            let productsCart = [];
            let suppliesCart = [];

            $('#purchaseTable tbody tr').each(function() {
                let row = $(this);
                let productId = row.data('product-id') || null;
                let tankId = row.data('tank-id') || null;
                let isManualRow = !!row.data('manual-row');
                let description = isManualRow ? row.find('.description-input').val() : null;
                let measurement_unit = isManualRow ? row.find('.unit-input').val() : row.data('unit');
                let quantity = parseFloat(row.find('.quantity').val());
                let subtotal = parseFloat(row.find('.subtotal').val());
                let unit_price = parseFloat(row.find('.unit_price').val());
                let waste = parseFloat(row.find('.waste').val());

                const hasIdentity = productId || (description && description.trim() !== '');

                if (hasIdentity && quantity >= 0.01 && subtotal >= 0 && unit_price >= 0) {
                    const item = {
                        product_id: productId,
                        tank_id: tankId,
                        description: description,
                        quantity: quantity,
                        unit_price: unit_price,
                        subtotal: subtotal,
                        waste: waste,
                        measurement_unit: measurement_unit
                    };

                    productsCart.push(item);

                }
            });

            if (productsCart.length === 0) {
                purchaseSpinner.classList.add('spinner-hidden');
                purchaseSpinner.classList.remove('spinner-visible');

                ToastError.fire({
                    icon: 'warning',
                    text: 'Debe agregar al menos un producto'
                });

                return;
            }

            // Mostrar spinner
            purchaseSpinner.classList.remove('spinner-hidden');
            purchaseSpinner.classList.add('spinner-visible');

            // Preparar los datos para enviar
            let data = {
                _token: $('input[name="_token"]').val(),
                supplier_id: $('#supplier_id').val(),
                voucher_type: $('#voucherType').val(),
                invoice_number: $('#invoiceNumber').val(),
                payment_method_id: $('#paymentMethod').val(),
                purchase_concept_id: $('#purchaseConcept').val(),
                glosa: $('#glosa').val(),
                date: $('#purchaseDate').val(),
                purchase_temp: $('#purchase_temp').val(),
                real_temp: $('#real_temp').val(),
                tank_id: $('#tank_id').val(),
                products: JSON.stringify(productsCart)
            };

            // Debug: mostrar los datos que se van a enviar
            console.log("Datos a enviar:", data);
            console.log("Carrito:", productsCart);

            // Enviar los datos mediante AJAX
            $.ajax({
                url: '{{ route('purchases.store') }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    purchaseSpinner.classList.add('spinner-hidden');
                    purchaseSpinner.classList.remove('spinner-visible');

                    if (response.status) {
                        ToastMessage.fire({
                            icon: 'success',
                            text: response.message || 'Operación exitosa'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Error del backend
                        ToastError.fire({
                            text: response.error || 'Ocurrió un error'
                        });
                    }
                },

                error: function(xhr, status, error) {
                    purchaseSpinner.classList.add('spinner-hidden');
                    purchaseSpinner.classList.remove('spinner-visible');

                    console.log("Error en la petición:");
                    console.log("Products enviados:", productsCart);
                    console.log("Supplies enviados:", suppliesCart);
                    console.log("XHR Response:", xhr);
                    console.log("XHR Status:", status);
                    console.log("XHR Error:", error);

                    let mensaje = 'Ocurrió un error al procesar la compra';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            mensaje = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                    } else if (xhr.responseText) {
                        mensaje = xhr.responseText;
                    }

                    ToastError.fire({
                        text: mensaje
                    });
                }

            });
        });

        function updateTotal() {
            let total = 0;

            $('#purchaseTable tbody tr').each(function() {
                let subtotal = parseFloat($(this).find('.subtotal').val()) || 0;
                total += subtotal;
            });

            $('#totalAmount').text(total.toFixed(2));
        }

        document.getElementById('saveSupplier').addEventListener('click', function() {
            var docum = document.getElementById('document').value.trim();
            var companyName = document.getElementById('company_name').value.trim();

            if (docum === "" || companyName === "") {
                alert("Los campos son obligatorios");
                return;
            }

            var data = {
                document: docum,
                company_name: companyName
            };

            var saveBtn = this;
            var originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            saveBtn.disabled = true;

            fetch('{{ route('suppliers.saveSupplier') }}', {
                    method: 'POST', // o el método que necesites
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta:', data);

                    if (data.success) {
                        // Mostrar mensaje de éxito
                        ToastMessage.fire({
                            icon: 'success',
                            text: data.message ||
                                'Operación exitosa' // Corregido: usar data.message en lugar de response.message
                        }).then(() => {
                            console.log(data.supplier);
                            suppliers.push(data.supplier);
                        });

                        // Cerrar modal
                        const modal = document.getElementById('providerModal');
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            //limpiar y esconder
                            document.getElementById('document').value = "";
                            document.getElementById('company_name').value = "";
                            bsModal.hide();
                        }

                    } else {
                        ToastError.fire({
                            text: data.message || 'Error al agregar el proveedor'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    alert('Error: ' + error.message);
                })
                .finally(() => {
                    // Restaurar estado del botón
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                });
        });

        $(document).ready(function() {
            $('#busquedaProducto').on('keyup', function() {
                var valor = $(this).val().toLowerCase();
                $('#purchaseTable tbody tr').each(function() {
                    var $row = $(this);
                    var descripcionInput = $row.find('.description-input');
                    var nombre = (descripcionInput.length ? descripcionInput.val() : $row.find('td:eq(0)').text()).toLowerCase();
                    if (nombre.includes(valor) || valor === '') {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            });
        });

        // Evento para eliminar fila
        $('#purchaseTable').on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            updateTotal(); // actualizar total
        });

        $(function() {
            const tankList = $('#tankList');
            const searchInput = $('#tankSearch');
            let manualRowCounter = 0;

            function getVisibleSelectedTanks() {
                return tankList.find('.tank-checkbox:checked').closest('.tank-item').filter(function() {
                    return $(this).is(':visible');
                });
            }

            function rebuildTankRows() {
                const selectedItems = getVisibleSelectedTanks();
                $('#purchaseTable tbody').empty();

                if (selectedItems.length === 0) {
                    updateTotal();
                    return;
                }

                // Cada tanque trae su propio producto: se arma una fila por tanque
                // con SU producto (ya no se exige que todos coincidan), porque una
                // misma compra puede traer productos distintos para tanques distintos.
                const invalidItems = [];
                const rowsHtml = [];

                selectedItems.each(function() {
                    const $item = $(this);
                    const productId = $item.data('product_id');
                    const product = newproducts.find(p => Number(p.id) === Number(productId));

                    if (!product) {
                        invalidItems.push($item);
                        return;
                    }

                    const tankId = $item.find('.tank-checkbox').val();
                    const tankName = $item.find('.tank-name').text();
                    rowsHtml.push(`
                        <tr data-product-id="${product.id}" data-tank-id="${tankId}" data-unit="${product.measurement_unit}">
                            <td>${product.name}</td>
                            <td>${product.measurement_unit}</td>
                            <td><input type="number" class="form-control text-end unit_price" step="0.01" min="0"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">${tankName}:</span>
                                    <input type="number" class="form-control text-end quantity cantidad-input" min="0.001" step="0.001">
                                </div>
                            </td>
                            <td><input type="number" class="form-control text-end subtotal" min="0.001" step="0.001" disabled></td>
                            <td><input type="number" class="form-control text-end waste" step="0.001" value="0"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                if (invalidItems.length > 0) {
                    invalidItems.forEach(function($item) {
                        $item.find('.tank-checkbox').prop('checked', false).trigger('markchange');
                    });
                    ToastError.fire({
                        icon: 'error',
                        text: (invalidItems.length > 1 ?
                                'El producto asociado a algunos tanques no está disponible o ha sido eliminado.' :
                                'El producto asociado a ese tanque no está disponible o ha sido eliminado.') +
                            ' Se ' + (invalidItems.length > 1 ? 'desmarcaron' : 'desmarcó') + ' automáticamente.'
                    });
                }

                $('#purchaseTable tbody').append(rowsHtml.join(''));

                attachEventsToRows();
                updateTotal();
            }

            function addManualPurchaseRow() {
                manualRowCounter++;
                const rowId = 'manual-' + manualRowCounter;
                const newRow = `
                    <tr data-product-id="" data-tank-id="" data-unit="" data-manual-row="${rowId}">
                        <td><input type="text" class="form-control text-start description-input" placeholder="Describe el ítem..."></td>
                        <td><input type="text" class="form-control text-start unit-input" placeholder="unidad"></td>
                        <td><input type="number" class="form-control text-end unit_price" step="0.01" min="0"></td>
                        <td><input type="number" class="form-control text-end quantity cantidad-input" min="0.001" step="0.001"></td>
                        <td><input type="number" class="form-control text-end subtotal" min="0.001" step="0.001" disabled></td>
                        <td><input type="number" class="form-control text-end waste" step="0.001" value="0" disabled></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#purchaseTable tbody').append(newRow);
                attachEventsToRows();
                updateTotal();
            }

            function toggleConceptSections() {
                const isFuel = $('#purchaseConcept option:selected').data('is-fuel') == 1;

                $('.fuel-only-field').toggle(isFuel);
                $('#glosaSection').toggle(!isFuel);

                if (!isFuel) {
                    $('#purchase_temp, #real_temp').val('');
                } else {
                    $('#glosa').val('');
                }

                // Al cambiar de concepto se limpia la selección anterior para no mezclar
                // tanques (combustible) con líneas manuales (otros gastos).
                $('#purchaseTable tbody').empty();
                tankList.find('.tank-checkbox').prop('checked', false).trigger('markchange');
                updateTotal();
            }

            $('#purchaseConcept').on('change', toggleConceptSections);
            $('#addManualRow').on('click', addManualPurchaseRow);
            toggleConceptSections();

            function applyTankFilters() {
                const locationId = String($('#location_id').val() || '');
                const term = searchInput.val().toLowerCase().trim();

                tankList.find('.tank-item').each(function() {
                    const $item = $(this);
                    const itemLoc = String($item.data('location_id') || '');
                    const itemName = String($item.data('name') || '');
                    const matchesLocation = !locationId || itemLoc === locationId;
                    const matchesSearch = !term || itemName.includes(term);
                    const visible = matchesLocation && matchesSearch;

                    $item.toggle(visible);
                    if (!visible) {
                        $item.find('.tank-checkbox').prop('checked', false).trigger('markchange');
                    }
                });

                rebuildTankRows();
            }

            function syncTankMark() {
                const $checkbox = $(this);
                $checkbox.closest('.tank-item').toggleClass('is-checked', $checkbox.is(':checked'));
            }

            $('#location_id').on('change', applyTankFilters);
            searchInput.on('input', applyTankFilters);
            tankList.on('change markchange', '.tank-checkbox', syncTankMark);
            tankList.on('change', '.tank-checkbox', rebuildTankRows);
            tankList.find('.tank-checkbox').each(syncTankMark);

            if ($('#location_id').val()) {
                applyTankFilters();
            }
        });
        // Búsqueda de documento (DNI/RUC) mediante API
        function searchDocumentApi() {
            const doc = $('#document').val().trim();

            $('#company_name').val('');

            if (!/^\d{8}$|^\d{11}$/.test(doc)) {
                if (typeof ToastError !== 'undefined') {
                    ToastError.fire({ text: 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.' });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.', 'error');
                } else {
                    alert('El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.');
                }
                return;
            }

            if (typeof Swal !== 'undefined' && false) { // Removed Swal in favor of global spinner
                Swal.fire({
                    title: 'Buscando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
            }

            const globalSpinner = document.getElementById('spinner');
            if (globalSpinner) {
                globalSpinner.classList.remove('spinner-hidden');
                globalSpinner.classList.add('spinner-visible');
            }

            $.ajax({
                url: "{{ url('sunat/consultar') }}",
                method: 'GET',
                data: { doc: doc },
                success: function(response) {
                    if (globalSpinner) {
                        globalSpinner.classList.add('spinner-hidden');
                        globalSpinner.classList.remove('spinner-visible');
                    }

                    if (response.success) {
                        const data = response.data;
                        $('#document').val(data.document || doc);
                        $('#company_name').val(data.name || '');
                    } else {
                        if (typeof ToastError !== 'undefined') {
                            ToastError.fire({ text: response.message || 'No se encontró información para ese documento.' });
                        } else {
                            alert(response.message || 'No se encontró información para ese documento.');
                        }
                    }
                },
                error: function(xhr) {
                    if (globalSpinner) {
                        globalSpinner.classList.add('spinner-hidden');
                        globalSpinner.classList.remove('spinner-visible');
                    }
                    const msg = xhr.responseJSON?.message || 'Error al consultar el documento.';
                    if (typeof ToastError !== 'undefined') {
                        ToastError.fire({ text: msg });
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    </script>
