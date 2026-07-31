@extends('template.index')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <style>
        .form-control,
        .form-select {
            border: 1px solid #000000ff;
        }

        .form-control-xs,
        .form-select-xs,
        .btn-xs {
            padding: 0.15rem 0.25rem;
            font-size: 0.875rem;
        }

        #tbl-products tr {
            cursor: pointer;
        }

        #tbl-products tr:hover {
            background-color: #f5f5f5;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .card-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .card-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem !important;
            }

            .bg-white {
                padding: 1rem !important;
            }

            h6 {
                font-size: 0.95rem;
            }

            .table-sm td,
            .table-sm th {
                padding: 0.3rem;
                font-size: 0.8rem;
            }
        }

        /* Botón flotante para abrir modal en móviles */
        .btn-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: none;
        }

        @media (max-width: 768px) {
            .btn-float {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .voucher-modal .form-label {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .payment-method-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .payment-method-item input[type="checkbox"] {
            flex-shrink: 0;
        }

        .payment-method-item label {
            flex-shrink: 0;
            margin: 0;
            min-width: 80px;
        }

        .payment-method-item input[type="text"] {
            flex: 1;
        }

        /* Nuevos estilos para la UI de Ventas */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .btn-square-action {
            border-radius: 10px;
            padding: 12px 5px;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        .btn-square-action:hover {
            transform: translateY(-2px);
        }
        
        /* Utility classes for soft backgrounds */
        .btn-soft-primary { background-color: rgba(13, 110, 253, 0.1) !important; color: #0d6efd !important; }
        .btn-soft-success { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }
        .btn-soft-danger { background-color: rgba(220, 53, 69, 0.1) !important; color: #dc3545 !important; }
        .btn-soft-info { background-color: rgba(13, 202, 240, 0.1) !important; color: #0abad9 !important; }
        .btn-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; color: #6c757d !important; }

        .empty-cart-icon { font-size: 5rem; opacity: 0.2; }
        #tbl-order-items:empty ~ #empty-cart-state { display: block; }
        #tbl-order-items:not(:empty) ~ #empty-cart-state { display: none; }
        .editable-sale-row td {
            vertical-align: middle;
        }
        .editable-sale-row .form-control,
        .editable-sale-row .form-select {
            min-width: 80px;
        }
        .editable-sale-row .sale-product-select {
            min-width: 160px;
        }
        .sales-create-page {
            padding: 0 !important;
            margin-top: 0 !important;
        }
        #chargeSection {
            padding: 0 !important;
            border-radius: 0 !important;
        }
        #chargeSection > .row {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
        }
        #chargeSection .sales-left-column,
        #chargeSection .sales-right-column {
            padding-top: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .sales-table-wrap {
            overflow-x: auto !important;
            overflow-y: auto;
            min-height: 0;
        }
        .sales-order-table {
            min-width: 760px;
        }
        .iq-navbar-header {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
        }
        :root {
            --sales-bg: #eef3f8;
            --sales-surface: #ffffff;
            --sales-surface-alt: #f6f8fc;
            --sales-border: #dbe4ef;
            --sales-text: #172554;
            --sales-muted: #64748b;
            --sales-accent: #2563eb;
            --sales-success: #15803d;
            --sales-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }
        body {
            background: linear-gradient(180deg, #f8fbff 0%, #eef3f8 100%);
        }
        #chargeSection {
            background: transparent !important;
        }
        .sales-shell {
            padding: 18px;
        }
        .sales-layout {
            align-items: stretch;
        }
        .sales-left-column,
        .sales-right-column {
            display: flex;
            flex-direction: column;
        }
        .sales-stack {
            display: grid;
            gap: 16px;
            height: 100%;
        }
        .sales-panel {
            background: var(--sales-surface);
            border: 1px solid var(--sales-border);
            border-radius: 18px;
            box-shadow: var(--sales-shadow);
            overflow: hidden;
        }
        .sales-panel-body {
            padding: 22px;
        }
        .sales-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 22px 14px;
        }
        .sales-panel-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sales-panel-title i {
            color: var(--sales-accent);
            font-size: 1.2rem;
        }
        .sales-panel-title h6 {
            margin: 0;
            color: var(--sales-text);
            font-size: 1.25rem;
            font-weight: 800;
        }
        .sales-panel-note {
            margin: 6px 0 0;
            color: var(--sales-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .sales-config-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .sales-config-grid {
            display: grid;
            gap: 18px;
        }
        .sales-field-block {
            padding: 16px;
            border: 1px solid var(--sales-border);
            border-radius: 14px;
            background: var(--sales-surface-alt);
        }
        .sales-field-block .form-label {
            color: var(--sales-text);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 10px;
        }
        .sales-credit-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--sales-border);
            border-radius: 14px;
            background: #f8fbff;
        }
        .sales-credit-toggle .form-check {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }
        .sales-credit-toggle .form-check-input {
            width: 1.3rem;
            height: 1.3rem;
            margin: 0;
        }
        .sales-credit-toggle .form-check-label {
            margin: 0;
            color: var(--sales-text);
            font-size: 1rem;
            font-weight: 700;
        }
        .sales-panel .form-control,
        .sales-panel .form-select,
        .sales-panel .input-group-text {
            min-height: 50px;
            border-color: #c9d4e5;
            border-radius: 12px;
            box-shadow: none;
        }
        .sales-panel .input-group > .form-control,
        .sales-panel .input-group > .form-select {
            border-radius: 12px;
        }
        .sales-panel .input-group > .btn {
            border-radius: 12px;
        }
        .sales-cart-panel {
            min-height: 100%;
        }
        .sales-cart-table-shell {
            padding: 0 22px;
        }
        .sales-table-wrap {
            border: 1px solid var(--sales-border);
            border-radius: 16px;
            background: var(--sales-surface-alt);
            overflow-x: auto !important;
            overflow-y: hidden;
        }
        .sales-order-table {
            margin: 0;
            min-width: 560px;
            background: transparent;
        }
        .sales-order-table thead th {
            background: transparent;
            border-bottom: 1px solid var(--sales-border);
            color: var(--sales-text);
            font-size: 1rem;
            font-weight: 700;
            text-transform: none;
            letter-spacing: normal;
            padding: 18px 16px;
            white-space: nowrap;
        }
        .sales-order-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(203, 213, 225, 0.7);
            background: #fff;
            white-space: nowrap;
        }
        .sales-order-table tbody tr:last-child td {
            border-bottom: 0;
        }
        .editable-sale-row .form-control,
        .editable-sale-row .form-select {
            min-width: 80px;
            min-height: 42px;
            background-color: #fff;
        }
        .editable-sale-row .sale-product-select {
            min-width: 160px;
            border-color: #3a57e8;
            color: #3a57e8;
            font-weight: 600;
        }
        .editable-sale-row .sale-product-select:focus {
            border-color: #3a57e8;
            box-shadow: 0 0 0 0.2rem rgba(58, 87, 232, 0.25);
        }
        .editable-sale-row .sale-product-select option {
            color: #3a57e8;
            font-weight: 600;
            background-color: #fff;
        }
        .editable-sale-row .sale-product-select option:disabled {
            color: #94a3b8;
            font-weight: 400;
            font-style: italic;
            background-color: #f8fafc;
        }

        /* Combo de producto personalizado (reemplaza visualmente al <select> nativo, que
           queda oculto pero sigue existiendo para no romper la lógica de cálculo/envío) */
        .custom-combo {
            position: relative;
            min-width: 160px;
        }
        .custom-combo-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            min-height: 42px;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            border: 1px solid #3a57e8;
            border-radius: 0.375rem;
            color: #3a57e8;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .custom-combo-trigger:focus {
            outline: none;
            border-color: #3a57e8;
            box-shadow: 0 0 0 0.2rem rgba(58, 87, 232, 0.25);
        }
        .custom-combo-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .custom-combo-label.is-placeholder {
            color: #94a3b8;
            font-weight: 400;
            font-style: italic;
        }
        .custom-combo-trigger .bi-chevron-down {
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }
        .custom-combo.is-open .custom-combo-trigger .bi-chevron-down {
            transform: rotate(180deg);
        }
        .custom-combo-menu {
            display: none;
            position: fixed;
            z-index: 3000;
            width: max-content;
            max-width: 320px;
            max-height: 220px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #3a57e8;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(23, 37, 84, 0.15);
            padding: 6px;
        }
        .custom-combo-menu.is-open {
            display: block;
        }
        #comboPortal {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
        }
        .custom-combo-option {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: 0;
            border-radius: 0.375rem;
            padding: 8px 10px;
            color: #3a57e8;
            font-weight: 600;
            font-size: 0.875rem;
            white-space: nowrap;
        }
        .custom-combo-option:hover,
        .custom-combo-option:focus {
            background-color: rgba(58, 87, 232, 0.1);
            outline: none;
        }
        .custom-combo-option.is-active {
            background-color: rgba(58, 87, 232, 0.15);
        }
        .custom-combo-option.is-placeholder {
            color: #94a3b8;
            font-style: italic;
            font-weight: 400;
        }
        .sales-empty-state {
            margin: 18px 22px 0;
            padding: 48px 24px;
            border: 1px dashed var(--sales-border);
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .sales-totals-bar {
            margin: 22px;
            padding: 22px 24px;
            border-radius: 18px;
            background: linear-gradient(135deg, #172554 0%, #2563eb 100%);
            color: #fff;
        }
        .sales-totals-bar h5,
        .sales-totals-bar .text-muted,
        .sales-totals-bar .text-primary {
            color: #fff !important;
        }
        .sales-totals-bar .text-muted.small {
            color: rgba(255,255,255,0.78) !important;
        }
        .sales-total-amount {
            font-size: clamp(2rem, 3vw, 3rem);
            font-weight: 800;
            line-height: 1;
        }
        .sales-checkout {
            margin: 0 22px 22px;
            padding: 22px;
            border: 1px solid var(--sales-border);
            border-radius: 18px;
            background: #fff;
        }
        .sales-checkout-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .sales-checkout-summary {
            min-width: 180px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--sales-surface-alt);
            text-align: right;
        }
        .sales-checkout-summary strong {
            color: var(--sales-accent);
            font-size: 1.4rem;
            font-weight: 800;
        }
        .sales-payment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .payment-method-item {
            display: block;
            margin: 0;
        }
        .sales-payment-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--sales-border);
            border-radius: 14px;
            background: var(--sales-surface-alt);
        }
        .sales-payment-card .input-group-text {
            min-height: 44px;
            border: 0;
            background: transparent;
            padding: 0;
        }
        .sales-payment-card label {
            color: var(--sales-text);
            font-weight: 700;
        }
        .sales-payment-card .form-control {
            min-height: 44px;
            background: #fff;
        }
        .sales-actions-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 22px;
            padding: 18px;
            border-radius: 16px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            border: 1px solid var(--sales-border);
        }
        .sales-actions-bar .btn-success {
            min-height: 52px;
            padding-inline: 28px;
            border-radius: 14px;
            font-weight: 700;
        }
        @media (max-width: 1199.98px) {
            .sales-shell {
                padding: 14px;
            }
            .sales-layout {
                gap: 14px;
            }
            .sales-left-column,
            .sales-right-column {
                width: 100%;
            }
        }
        @media (max-width: 991.98px) {
            .sales-panel-header,
            .sales-panel-body,
            .sales-cart-table-shell,
            .sales-checkout,
            .sales-totals-bar,
            .sales-empty-state {
                margin-left: 0;
                margin-right: 0;
            }
            .sales-cart-table-shell {
                padding: 0 16px;
            }
            .sales-totals-bar,
            .sales-checkout {
                margin: 16px;
            }
            .sales-payment-grid {
                grid-template-columns: 1fr;
            }
            .sales-checkout-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .sales-checkout-summary {
                width: 100%;
                text-align: left;
            }
        }
        @media (max-width: 767.98px) {
            .sales-shell {
                padding: 10px;
            }
            .sales-panel-header,
            .sales-panel-body,
            .sales-checkout {
                padding: 16px;
            }
            .sales-cart-table-shell {
                padding: 0 12px;
            }
            .sales-totals-bar {
                margin: 12px;
                padding: 16px;
            }
            .sales-checkout {
                margin: 12px;
            }
            .sales-empty-state {
                margin: 12px;
                padding: 28px 16px;
            }
            .sales-actions-bar {
                padding: 14px;
            }
            .sales-actions-bar > * {
                width: 100%;
            }
        /* =========================================================
           ESTILOS DE ALTO CONTRASTE: TEXTO NEGRO INTENSO Y EN NEGRITA
           (Elimina cualquier tono plomo/gris en inputs, labels, tablas)
           ========================================================= */
        .form-control,
        .form-select,
        .form-check-label,
        .form-label,
        label,
        input,
        select,
        textarea,
        button,
        table,
        th,
        td,
        .input-group-text,
        .editable-sale-row input,
        .editable-sale-row select,
        #tbl-order-items input,
        #tbl-order-items select,
        #tbl-order-items td,
        .sales-order-table input,
        .sales-order-table select,
        .sales-order-table td,
        .sales-order-table th {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="email"],
        select,
        .form-control,
        .form-select,
        .input-group-text {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            border-color: #334155 !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
        }

        /* Deshabilitados o Readonly (para que no se vean grises) */
        input:disabled,
        input[readonly],
        select:disabled,
        .form-control:disabled,
        .form-control[readonly],
        .form-select:disabled {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            opacity: 1 !important;
        }

        /* Opciones de select */
        select option,
        .form-select option,
        .custom-combo-option {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            background-color: #ffffff !important;
        }

        /* Placeholders oscuros y en negrita */
        ::-webkit-input-placeholder { color: #334155 !important; font-weight: 700 !important; opacity: 1 !important; -webkit-text-fill-color: #334155 !important; }
        ::-moz-placeholder { color: #334155 !important; font-weight: 700 !important; opacity: 1 !important; }
        :-ms-input-placeholder { color: #334155 !important; font-weight: 700 !important; opacity: 1 !important; }
        ::placeholder { color: #334155 !important; font-weight: 700 !important; opacity: 1 !important; }
        .form-control::placeholder,
        input::placeholder {
            color: #334155 !important;
            -webkit-text-fill-color: #334155 !important;
            font-weight: 700 !important;
            opacity: 1 !important;
        }

        /* Forzar texto muted fuera de barras de estado/totales a ser oscuro y negrita */
        .text-muted:not(.sales-totals-bar *):not(.badge *):not(.bg-primary *) {
            color: #1e293b !important;
            font-weight: 700 !important;
        }
    </style>
@endsection

@section('header')
<div class="d-flex align-items-center mt-2 mb-4">
    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px;">
        <i class="bi bi-cart3 fs-1 text-white"></i>
    </div>
    <div>
        <h2 class="mb-1 fw-bold text-white">Módulo de Ventas</h2>
        <p class="mb-0 text-white-50 fs-6">Gestión de ventas rápida y eficiente</p>
    </div>
</div>
@endsection

@section('content')
    <style>
        /* RESET ABSOLUTO DE VARIABLES DE COLOR CSS */
        :root, body, html {
            --bs-body-color: #000000 !important;
            --bs-body-color-rgb: 0, 0, 0 !important;
            --bs-secondary-color: #000000 !important;
            --bs-secondary-rgb: 0, 0, 0 !important;
            --bs-tertiary-color: #000000 !important;
            --bs-heading-color: #000000 !important;
            --sales-text: #000000 !important;
            --sales-muted: #1e293b !important;
        }

        /* REGLA PARA FORZAR NEGRO INTENSO EN CONTROLES Y TEXTO (EXCLUYENDO BOTONES Y BADGES DE COLOR) */
        .sales-create-page label,
        .sales-create-page .form-label,
        .sales-create-page .form-check-label,
        .sales-create-page h1, .sales-create-page h2, .sales-create-page h3, .sales-create-page h4, .sales-create-page h5, .sales-create-page h6,
        .sales-create-page input:not(.btn),
        .sales-create-page select,
        .sales-create-page p,
        .sales-create-page td,
        .sales-create-page th {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
        }

        /* REGLAS ESPECÍFICAS PARA INPUTS, SELECTS, LABELS Y CONTROLES */
        .sales-create-page input,
        .sales-create-page select,
        .sales-create-page textarea,
        .sales-create-page .form-control,
        .sales-create-page .form-select,
        .sales-create-page .input-group-text,
        .sales-create-page label,
        .sales-create-page .form-label,
        .sales-create-page .form-check-label,
        .sales-create-page th,
        .sales-create-page td,
        #tbl-order-items input,
        #tbl-order-items select,
        #tbl-order-items td,
        .editable-sale-row input,
        .editable-sale-row select {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            border-color: #475569 !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
        }

        /* DESHABILITADOS O READONLY (evita que Chrome/Firefox los pinte de plomo) */
        .sales-create-page input:disabled,
        .sales-create-page input[readonly],
        .sales-create-page select:disabled,
        .sales-create-page .form-control:disabled,
        .sales-create-page .form-control[readonly],
        .sales-create-page .form-select:disabled {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            opacity: 1 !important;
        }

        /* PLACEHOLDERS EN INPUTS */
        .sales-create-page ::-webkit-input-placeholder { color: #1e293b !important; -webkit-text-fill-color: #1e293b !important; font-weight: 700 !important; opacity: 1 !important; }
        .sales-create-page ::-moz-placeholder { color: #1e293b !important; font-weight: 700 !important; opacity: 1 !important; }
        .sales-create-page :-ms-input-placeholder { color: #1e293b !important; font-weight: 700 !important; opacity: 1 !important; }
        .sales-create-page ::placeholder { color: #1e293b !important; -webkit-text-fill-color: #1e293b !important; font-weight: 700 !important; opacity: 1 !important; }
        .sales-create-page .form-control::placeholder,
        .sales-create-page input::placeholder {
            color: #1e293b !important;
            -webkit-text-fill-color: #1e293b !important;
            font-weight: 700 !important;
            opacity: 1 !important;
        }

        /* SELECT OPTIONS */
        .sales-create-page select option,
        .sales-create-page .form-select option,
        .sales-create-page .custom-combo-option {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
            background-color: #ffffff !important;
        }

        /* TEXTO SILENCIADO (TEXT-MUTED) EXCEPTO TARJETAS DE TOTALES / BOTONES */
        .sales-create-page .text-muted:not(.sales-totals-bar *):not(.badge *):not(.bg-primary *):not(.btn *) {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            font-weight: 700 !important;
        }

        /* EXCEPCIÓN CON ALTA ESPECIFICIDAD DE ID: BARRA DE TOTALES EN BLANCO PURO */
        #chargeSection #sales-totals-banner,
        #chargeSection #sales-totals-banner *,
        #chargeSection .sales-totals-bar,
        #chargeSection .sales-totals-bar *,
        #sales-totals-banner,
        #sales-totals-banner *,
        .sales-totals-bar,
        .sales-totals-bar * {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            font-weight: 700 !important;
        }

        #chargeSection #sales-totals-banner .text-muted,
        #chargeSection #sales-totals-banner .text-primary,
        #sales-totals-banner .text-muted,
        #sales-totals-banner .text-primary {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        /* EXCEPCIÓN CON ALTA ESPECIFICIDAD DE ID: BOTONES Y BADGES CON FONDO EN BLANCO PURO */
        #chargeSection .btn-primary,
        #chargeSection .btn-primary *,
        #chargeSection .btn-primary i,
        #chargeSection .btn-primary span,
        #chargeSection .btn-success,
        #chargeSection .btn-success *,
        #chargeSection .btn-success i,
        #chargeSection .btn-success span,
        #chargeSection .btn-danger,
        #chargeSection .btn-danger *,
        #chargeSection .btn-danger i,
        #chargeSection .btn-danger span,
        #chargeSection .btn-info,
        #chargeSection .btn-info *,
        #chargeSection .btn-info i,
        #chargeSection .bg-primary,
        #chargeSection .bg-primary *,
        #chargeSection .bg-primary i,
        #chargeSection .bg-success,
        #chargeSection .bg-success *,
        #chargeSection .bg-success i,
        #chargeSection .bg-danger,
        #chargeSection .bg-danger *,
        #chargeSection .bg-danger i,
        .sales-create-page .btn-primary,
        .sales-create-page .btn-primary *,
        .sales-create-page .btn-primary i,
        .sales-create-page .btn-primary span,
        .sales-create-page .btn-success,
        .sales-create-page .btn-success *,
        .sales-create-page .btn-success i,
        .sales-create-page .btn-success span,
        .sales-create-page .btn-danger,
        .sales-create-page .btn-danger *,
        .sales-create-page .btn-danger i,
        .sales-create-page .btn-danger span,
        .sales-create-page .bg-primary,
        .sales-create-page .bg-primary *,
        .sales-create-page .bg-primary i,
        .sales-create-page .bg-success,
        .sales-create-page .bg-success *,
        .sales-create-page .bg-success i,
        .sales-create-page .bg-danger,
        .sales-create-page .bg-danger *,
        .sales-create-page .bg-danger i,
        .btn-primary,
        .btn-primary *,
        .btn-success,
        .btn-success *,
        .btn-danger,
        .btn-danger * {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* ESTILOS Y COMPRESIÓN DE ALTA CALIDAD PARA MÓDULO DE VENTAS */
        body {
            background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        }

        .card-custom {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.03) !important;
            background: #ffffff !important;
        }

        .sales-panel-title h6 {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: -0.01em !important;
        }

        /* BARRA DE TOTALES FLOTANTE BALANCEADA Y SIMÉTRICA */
        #chargeSection #sales-totals-banner,
        #chargeSection .sales-totals-bar,
        #sales-totals-banner {
            margin: 12px 20px 14px auto !important;
            padding: 10px 22px !important;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%) !important;
            box-shadow: 0 6px 20px -3px rgba(37, 99, 235, 0.35) !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            width: fit-content !important;
            min-width: 260px !important;
            max-width: 320px !important;
        }

        #chargeSection #sales-totals-banner h5,
        #sales-totals-banner h5 {
            font-size: 1.05rem !important;
            letter-spacing: 0.06em !important;
            margin-bottom: 0 !important;
        }

        #chargeSection #sales-totals-banner .sales-total-amount,
        #sales-totals-banner .sales-total-amount {
            font-size: 1.75rem !important;
            letter-spacing: -0.02em !important;
        }

        /* Ocultar resumen duplicado */
        .sales-checkout-summary {
            display: none !important;
        }

        #chargeSection .sales-checkout {
            margin: 0 20px 20px !important;
            padding: 18px 20px !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04) !important;
        }

        #chargeSection .sales-checkout-head {
            margin-bottom: 10px !important;
        }

        #chargeSection #payment-methods-section {
            margin-top: 10px !important;
        }

        #chargeSection #payment-methods-section .form-label {
            margin-bottom: 8px !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            color: #475569 !important;
        }

        /* METODOS DE PAGO CON DISEÑO INTERACTIVO */
        #chargeSection .sales-payment-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr)) !important;
            gap: 10px !important;
        }

        #chargeSection .payment-method-item {
            width: 100% !important;
            margin: 0 !important;
        }

        #chargeSection .sales-payment-card {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 6px 12px !important;
            gap: 6px !important;
            border-radius: 12px !important;
            border: 1.5px solid #cbd5e1 !important;
            background: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
        }

        #chargeSection .sales-payment-card:hover {
            border-color: #2563eb !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1) !important;
        }

        #chargeSection .sales-payment-card.active-payment {
            border-color: #2563eb !important;
            background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%) !important;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.15) !important;
        }

        #chargeSection .sales-payment-card .input-group-text {
            padding: 0 !important;
            margin: 0 !important;
            border: 0 !important;
            background: transparent !important;
            min-height: auto !important;
        }

        #chargeSection .sales-payment-card label {
            font-size: 0.88rem !important;
            white-space: nowrap !important;
            margin-bottom: 0 !important;
            cursor: pointer !important;
        }

        #chargeSection .sales-payment-card .form-control {
            width: 90px !important;
            max-width: 95px !important;
            min-width: 80px !important;
            min-height: 34px !important;
            height: 34px !important;
            font-size: 0.9rem !important;
            padding: 2px 8px !important;
            border-radius: 8px !important;
            border: 1.5px solid #64748b !important;
            text-align: right !important;
            box-shadow: none !important;
        }

        #chargeSection .sales-payment-card .form-control:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        /* BARRA DE ACCIONES (GUARDAR VENTA) */
        #chargeSection .sales-actions-bar {
            margin-top: 14px !important;
            padding: 12px 18px !important;
            border-radius: 14px !important;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border: 1.5px solid #cbd5e1 !important;
        }

        #chargeSection .sales-actions-bar .btn-success {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
            border: none !important;
            min-height: 44px !important;
            height: 44px !important;
            padding: 6px 28px !important;
            font-size: 1rem !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 16px rgba(22, 163, 74, 0.32) !important;
            transition: all 0.2s ease !important;
        }

        #chargeSection .sales-actions-bar .btn-success:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.42) !important;
        }

        /* ESTILOS DE LA TABLA DE PRODUCTOS */
        .sales-order-table thead th {
            background-color: #f1f5f9 !important;
            border-bottom: 2px solid #cbd5e1 !important;
            color: #1e293b !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            padding: 12px 14px !important;
        }
    </style>
    <div class="container-fluid content-inner mt-0 py-0 px-0 sales-create-page">
        <div id="chargeSection" class="text-dark fw-semibold rounded sales-shell">
            <div class="row g-3 sales-layout">
                <!-- Columna IZQUIERDA: Productos, Contratos y Creditos -->
                <div class="col-12 col-xl-4 sales-left-column">
                    <div class="sales-stack">
                    {{-- <div
                        class="bg-white p-3 rounded shadow-sm mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <h6 class="mb-3 text-center">Contómetro de Surtidores</h6>
                        <div class="btn-group d-flex justify-content-center" role="group"
                            aria-label="Basic outlined example">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#finalMeasurementModal" title="Cerrar Contometro">
                                <i class="bi bi-speedometer2"> Cerrar Contómetro</i>
                            </button>
                        </div>
                    </div> --}}

                    <!-- Card 1: Tipo de venta -->
                    <div class="sales-panel sales-config-card card-custom">
                        <div class="sales-panel-header">
                            <div>
                                <div class="sales-panel-title align-items-center">
                                    <div class="p-2 rounded-3 bg-primary text-white me-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;"><i class="bi bi-file-earmark-text-fill text-white fs-5"></i></div>
                                    <h6 class="mb-0 fw-bold">Datos de la Venta</h6>
                                </div>
                            </div>
                        </div>
                        <div class="sales-panel-body">
                            <div class="sales-config-grid">
                                <div class="sales-field-block">
                                    <label for="tipo-venta" class="form-label text-muted small fw-semibold">Tipo de venta</label>
                                    <select id="tipo-venta" class="form-select form-select-sm border-1 bg-white py-2">
                                        <option value="directa">Venta Directa</option>
                                        <option value="contrato">Contrato</option>
                                    </select>
                                    <input type="hidden" id="type_sale">
                                </div>
                            </div>

                            <!-- Block de búsqueda de cliente para contrato -->
                            <div id="cliente-search-card" class="mt-2 mb-3 p-3 bg-light rounded border border-primary border-opacity-25" style="display: none;">
                                <label for="search-client" class="form-label text-primary small fw-bold mb-1">
                                    <i class="bi bi-search me-1"></i>Buscar Cliente (Contrato)
                                </label>
                                <div class="input-group">
                                    <input type="text" id="search-client" class="form-control bg-white border-1 py-2" placeholder="Escriba nombre o RUC/DNI...">
                                    <button type="button" class="btn btn-primary" id="btn-show-client-list" data-bs-toggle="modal" data-bs-target="#modalClientList" title="Ver lista de clientes">
                                        <i class="bi bi-list-task me-1"></i>Ver lista
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i>Escriba para buscar o haga clic en <strong>Ver lista</strong> para ver todos los clientes.
                                </small>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2 shadow-sm fw-semibold" id="btn-open-contracts-modal" style="display: none;" onclick="reabrirContratosModal()">
                                    <i class="bi bi-journal-text me-1"></i>Ver Contratos del Cliente
                                </button>
                                <input type="hidden" id="client_id" name="client_id">
                                <input type="hidden" id="current-agreement-id">
                                <input type="hidden" id="current-order-detail-id">
                            </div>

                            <div id="credit-checkbox-container" class="sales-credit-toggle">
                                <div class="form-check">
                                    <input type="checkbox" id="is-credit-sale" class="form-check-input"> 
                                    <label class="form-check-label text-muted small" for="is-credit-sale">Venta a Crédito</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-card-heading text-primary me-1"></i>Documento</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" id="document" maxlength="11" placeholder="DNI o RUC">
                                        <button type="button" class="btn btn-primary" id="btn-search-ruc" onclick="searchDocumentApi()"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-calendar-event text-primary me-1"></i>Fecha</label>
                                    <input type="date" class="form-control form-control-sm" id="sale_date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-person-badge text-primary me-1"></i>Responsable</label>
                                    <select class="form-select form-select-sm" id="user_id"><option value="">-- Por defecto --</option>@foreach($users ?? [] as $u)<option value="{{ $u->id }}" {{ auth()->id() == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-car-front text-primary me-1"></i>Placa</label>
                                    <input type="text" class="form-control form-control-sm" id="vehicle_plate" placeholder="Opcional">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark mb-1"><i class="bi bi-person text-primary me-1"></i>Cliente</label>
                                    <div class="input-group input-group-sm"><button class="btn btn-outline-primary fw-bold" type="button" id="btn_c_varios" onclick="document.getElementById('client_name').value='CLIENTES VARIOS'">C. Varios</button><input type="text" class="form-control" id="client_name" placeholder="Nombre o razón social"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card de productos para contrato/crédito -->
                    <div id="products-contract-credit" class="sales-panel card-custom" style="display: none;">
                        <div class="sales-panel-header">
                            <div class="sales-panel-title">
                                <i class="bi bi-journal-text"></i>
                                <h6>Contratos del Cliente</h6>
                            </div>
                        </div>
                        <div class="sales-panel-body" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-hover small">
                                <tbody id="tbl-products-contract"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Selector de Isla (Legacy/Unused) -->
                    <div id="isle-select-card" class="sales-panel card-custom" style="display: none;">
                    </div>

                    <!-- Card 2: Productos para venta directa -->
                    <div id="products-direct-card" class="bg-white p-4 card-custom mb-3" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-seam text-primary me-2 fs-5"></i>
                                <h6 class="mb-0 fw-bold">Productos Disponibles</h6>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-3">
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text bg-white border border-end-0 text-muted ps-3 pe-2 rounded-start-3"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control bg-white border border-start-0 py-2 rounded-end-3 shadow-none" placeholder="Buscar producto...">
                            </div>
                            <select class="form-select bg-white border border-1 w-auto text-dark rounded-3 shadow-none">
                                <option>Categorías</option>
                            </select>
                            <button class="btn btn-primary rounded-3 px-3"><i class="bi bi-grid-fill"></i></button>
                            <button class="btn btn-light border border-1 text-muted rounded-3 px-3 bg-white"><i class="bi bi-list-ul"></i></button>
                        </div>

                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover small border-top">
                                <tbody id="tbl-products"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8 sales-right-column">
                    <div class="sales-panel sales-cart-panel card-custom d-flex flex-column h-100">
                        <div class="sales-panel-header">
                            <div>
                                <div class="sales-panel-title align-items-center">
                                    <div class="p-2 rounded-3 bg-primary text-white me-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;"><i class="bi bi-cart-check-fill text-white fs-5"></i></div>
                                    <h6 class="mb-0 fw-bold">Productos Agregados</h6>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold text-white" id="btn-add-editable-product-row" style="display: none;">
                                <i class="bi bi-plus-circle-fill me-1 text-white"></i><span class="text-white">Agregar fila</span>
                            </button>
                        </div>

                        <!-- Selector en cascada para Productos Agregados: Isla -> Surtidor -> Lado (Opcional) -->
                        <div class="px-3 py-2 bg-light border-bottom border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="chk-detailed-mode" onchange="toggleDetailedMode()">
                                    <label class="form-check-label small fw-bold text-dark ms-1" style="cursor: pointer;" for="chk-detailed-mode">
                                        <i class="bi bi-funnel-fill text-primary me-1"></i>¿Deseas un registro detallado por surtidor e isla?
                                    </label>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;" id="detailed-mode-status-text">
                                    <i class="bi bi-info-circle me-1"></i>Opcional: Desactivado
                                </small>
                            </div>

                            <div id="detailed-selectors-container" class="row g-2 align-items-center opacity-50" style="pointer-events: none; transition: all 0.3s ease;">
                                <div class="col-4">
                                    <label for="select-isle" class="form-label small fw-bold text-dark mb-1">
                                        <i class="bi bi-geo-alt-fill text-primary me-1"></i>1. Isla
                                    </label>
                                    <select id="select-isle" class="form-select form-select-sm border-1 bg-white py-1 fw-bold" disabled onchange="onIsleChange()">
                                        <option value="">-- Seleccionar Isla --</option>
                                        @foreach ($isles ?? [] as $isle)
                                            <option value="{{ $isle->id }}" {{ (isset($assignedIsle) && $assignedIsle == $isle->id) ? 'selected' : '' }}>{{ $isle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="select-pump" class="form-label small fw-bold text-dark mb-1">
                                        <i class="bi bi-fuel-pump-fill text-primary me-1"></i>2. Surtidor
                                    </label>
                                    <select id="select-pump" class="form-select form-select-sm border-1 bg-white py-1 fw-bold" disabled onchange="onPumpChange()">
                                        <option value="">-- Seleccionar Surtidor --</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="select-side" class="form-label small fw-bold text-dark mb-1">
                                        <i class="bi bi-signpost-split-fill text-primary me-1"></i>3. Lado
                                    </label>
                                    <select id="select-side" class="form-select form-select-sm border-1 bg-white py-1 fw-bold" disabled onchange="onSideChange()">
                                        <option value="">-- Seleccionar Lado --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="sales-cart-table-shell">
                        <div class="flex-grow-1 sales-table-wrap">
                            <table class="table table-borderless table-hover small mb-0 sales-order-table">
                                <thead class="text-muted">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th width="50" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl-order-items"></tbody>
                            </table>
                            
                        </div>
                        </div>
                            <!-- Empty State -->
                            <div id="empty-cart-state" class="text-center sales-empty-state">
                                <i class="bi bi-cart-plus text-primary empty-cart-icon"></i>
                                <h5 class="text-muted mt-4 fw-bold">Aún no hay productos agregados</h5>
                                <p class="text-muted small">La primera fila se carga automaticamente y puedes agregar mas si lo necesitas.</p>
                            </div>

                        <!-- Total flotante -->
                        <!-- Total flotante -->
                        <!-- Total flotante alineado a la derecha -->
                        <div id="sales-totals-banner" class="total-display sales-totals-bar mt-auto ms-auto">
                            <div class="d-flex justify-content-between align-items-center gap-4">
                                <div class="me-3">
                                    <h5 class="mb-0 fw-bold text-uppercase text-white" style="font-size: 0.9rem;">Total</h5>
                                    <span class="text-white small" style="font-size: 0.78rem;"><span id="items-count" class="text-white">0</span> productos</span>
                                </div>
                                <h2 class="mb-0 fw-bold text-white sales-total-amount" style="font-size: 1.75rem;">S/ <span id="total" class="text-white">0.00</span></h2>
                            </div>
                        </div>

                        <div class="sales-checkout" id="inline-checkout">
                            <div class="sales-checkout-head">
                                <div class="sales-panel-title align-items-center">
                                    <div class="p-2 rounded-3 bg-primary text-white me-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;"><i class="bi bi-wallet2 text-white fs-5"></i></div>
                                    <h6 class="mb-0 fw-bold">Datos y Pago de la Venta</h6>
                                </div>
                                <div class="sales-checkout-summary">
                                    <span class="text-muted small d-block">Total a cobrar</span>
                                    <strong>S/ <span id="charge-total">0.00</span></strong>
                                </div>
                            </div>
                            <input type="hidden" id="number"><input type="hidden" id="address"><input type="hidden" id="orden"><input type="hidden" id="area">
                            <input type="radio" class="voucher_type d-none" name="voucher_type" id="voucher_type_1" value="Ticket" checked>
                            <div class="row g-3">
                                <div class="col-md-6 credit-extra-fields" style="display:none"><label class="form-label small fw-semibold">N.º de crédito</label><input type="text" class="form-control" id="credit_number" placeholder="Número de crédito"></div>
                                <div class="col-md-6 credit-extra-fields" style="display:none"><label class="form-label small fw-semibold">Código de Vale</label><input type="text" class="form-control" id="voucher_code" placeholder="Código"></div>
                                <div class="col-md-6 credit-extra-fields" style="display:none"><label class="form-label small fw-semibold">Responsable (Sede)</label><select class="form-select" id="responsible_id"><option value="">-- Seleccione --</option>@foreach($employees ?? [] as $emp)<option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->last_name }}</option>@endforeach</select></div>
                                <div class="col-md-6 credit-extra-fields" style="display:none"><label class="form-label small fw-semibold">Detalle</label><input type="text" class="form-control" id="detail" placeholder="Observación"></div>
                            </div>
                            <template id="payment-options-template">
                                <option value="">Seleccionar cuenta</option>
                                @foreach ($payment_methods as $index => $pm)
                                    <option value="{{ $pm->id }}" {{ $index == 0 ? 'selected' : '' }}>{{ $pm->name }}</option>
                                @endforeach
                            </template>

                            <div class="mt-3" id="payment-methods-section">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label small fw-bold text-uppercase text-dark mb-0">
                                        <i class="bi bi-wallet2 me-1 text-primary"></i>MÉTODOS DE PAGO
                                    </label>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3 shadow-sm" id="btn-add-payment-row" onclick="addPaymentRow()">
                                        <i class="bi bi-plus-lg me-1"></i>Agregar otro
                                    </button>
                                </div>

                                <div id="dynamic-payment-rows">
                                    <div class="row g-2 align-items-center mb-2 dynamic-payment-row">
                                        <div class="col-7 col-md-7">
                                            <select class="form-select payment-method-select fw-bold border-1 py-2" onchange="validateSelectedPaymentMethods(this)">
                                                <option value="">Seleccionar cuenta</option>
                                                @foreach ($payment_methods as $index => $pm)
                                                    <option value="{{ $pm->id }}" {{ $index == 0 ? 'selected' : '' }}>{{ $pm->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4 col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold small">S/</span>
                                                <input type="number" step="0.01" min="0" class="form-control text-end fw-bold py-2 payment-method-amount" oninput="calculateDynamicPaymentTotal()" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-1 col-md-1 text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-remove-payment-row" onclick="removePaymentRow(this)" style="display: none; width: 32px; height: 32px; padding: 0;" title="Eliminar método">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen de Saldo Restante -->
                                <div id="payment-balance-summary" class="mt-2 p-2 px-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                                    <span class="text-dark small fw-bold"><i class="bi bi-calculator text-primary me-1"></i>Saldo Restante:</span>
                                    <span id="lbl-saldo-restante" class="badge bg-danger fs-6 px-3 py-1 fw-bold">S/ 0.00</span>
                                </div>
                            </div>
                            <div id="vuelto-adicional-container" class="mt-3" style="display:none"><label><input type="checkbox" id="is-vuelto-adicional" class="form-check-input me-1"> Vuelto adicional</label></div>
                            <div id="vuelto-adicional-section" class="mt-2" style="display:none"><label class="form-label small fw-semibold">Vuelto adicional</label><input type="number" step="0.01" class="form-control" name="adicional" id="adicional" placeholder="0.00"></div>
                            <div class="sales-actions-bar">
                                <div><span class="text-muted small d-block mb-1">Estado</span><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fs-6 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Lista para registrar</span></div>
                                <button type="button" class="btn btn-success btn-lg px-4" id="btn-save"><i class="bi bi-check-circle me-1"></i>Guardar venta <span id="spinner-save" class="spinner-border spinner-border-sm ms-1" style="display:none"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para móviles -->
    @if (false)
    <button type="button" class="btn btn-primary btn-float d-md-none" data-bs-toggle="modal"
        data-bs-target="#voucherModal" id="btn-float-voucher">
        <i class="bi bi-receipt fs-4"></i>
    </button>

    <!-- MODAL DE COMPROBANTE -->
    <div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="voucherModalLabel">
                        <i class="bi bi-receipt"></i> Datos del Comprobante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body voucher-modal">
                    <!-- Tipo de Comprobante -->
                    <div class="mb-3">
                        <label class="form-label">Comprobante</label>
                        <div class="d-flex gap-2 flex-wrap">
                            
                            <input type="radio" class="btn-check voucher_type" name="voucher_type_modal" id="voucher_type_modal_1" value="Ticket" checked>
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_modal_1">Ticket de Venta</label>

                            <input type="radio" class="btn-check voucher_type" name="voucher_type_modal" id="voucher_type_modal_2" value="Boleta" style="display: none;">
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_modal_2" style="display: none;">Boleta</label>

                            <input type="radio" class="btn-check voucher_type" name="voucher_type_modal" id="voucher_type_modal_3" value="Factura" style="display: none;">
                            <label class="btn btn-outline-primary btn-sm" for="voucher_type_modal_3" style="display: none;">Factura</label>

                        </div>
                    </div>

                    <!-- Número de Comprobante -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">N° de Comprobante</label>
                        <input type="text" class="form-control" id="number_modal" placeholder="-">
                    </div>

                    <!-- Documento -->
                    <div class="mb-3">
                        <label class="form-label">Documento</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="document_modal" maxlength="11" placeholder="Ingrese DNI o RUC">
                            <button type="button" class="btn btn-primary" id="btn-search-ruc-modal"
                                onclick="searchDocumentApi()">
                                <i class="bi bi-search"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="sale_date_modal" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <!--Campos de credito-->
                    <div class="mb-3 credit-extra-fields" style="display: none;">
                        <label class="form-label">N° de Crédito</label>
                        <input type="text" class="form-control" id="credit_number_modal" placeholder="-">
                    </div>
                    <div class="mb-3 credit-extra-fields" style="display: none;">
                        <label class="form-label">Código de Vale</label>
                        <input type="text" class="form-control" id="voucher_code_modal" placeholder="-">
                    </div>
                    <div class="mb-3 credit-extra-fields" style="display: none;">
                        <label class="form-label">Responsable (Sede)</label>
                        <select class="form-select" id="responsible_id_modal">
                            <option value="">-- Seleccione --</option>
                            @foreach($employees ?? [] as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 credit-extra-fields" style="display: none;">
                        <label class="form-label">Detalle</label>
                        <input type="text" class="form-control" id="detail_modal" placeholder="-">
                    </div>

                    <!-- Cliente -->
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <div class="input-group">
                            
                            <button class="btn btn-outline-secondary" type="button" 
                                id="btn_c_varios_modal" 
                                onclick="document.getElementById('client_name_modal').value = 'CLIENTES VARIOS'">
                                C. Varios
                            </button>

                            {{-- El input va DESPUÉS del botón para que quede a la derecha --}}
                            <input type="text" class="form-control" id="client_name_modal" placeholder="-">
                            
                        </div>
                    </div>

                    <!-- Placa de Vehículo -->
                    <div class="mb-3">
                        <label class="form-label">Placa de Vehículo</label>
                        <input type="text" class="form-control" id="vehicle_plate_modal" placeholder="-">
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="address_modal" placeholder="-">
                    </div>

                    <!-- Orden -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Orden</label>
                        <input type="text" class="form-control" id="orden_modal" placeholder="-">
                    </div>

                    <!-- Área -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Área</label>
                        <input type="text" class="form-control" id="area_modal" placeholder="-">
                    </div>

                    <!-- Forma de pago (solo para venta directa) -->
                    <div class="mb-3" id="payment-methods-section">
                        <label class="form-label fw-bold">Forma de pago</label>
                        <table class="w-100 small">
                            @foreach ($payment_methods as $index => $payment_method)
                                <tr class="payment-method-item">
                                    <td width="150">
                                        <input type="checkbox" class="form-check-input me-2"
                                            onchange="togglePaymentMethod(event, '#amount_{{ $payment_method->id }}')"
                                            id="cbx_amount_{{ $payment_method->id }}" {{ $index == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $payment_method->name }}</label>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                            id="amount_{{ $payment_method->id }}" oninput="calculateDifference(event)"
                                            {{ $index == 0 ? '' : 'disabled' }} placeholder="0.00">
                                    </td>
                                </tr>
                            @endforeach
                            
                        </table>
                    </div>

                    <div id="vuelto-adicional-container" class="mb-3" style="display: none;">
                        <label>
                            <input type="checkbox" id="is-vuelto-adicional" class="form-check-input"> Vuelto adicional
                        </label>
                    </div>
                    <!-- Vuelto adicional -->
                    <div id="vuelto-adicional-section" style="display: none;" class="mb-3">
                        <label class="form-label">Vuelto adicional</label>
                        <input type="number" step="0.01" class="form-control" name="adicional" id="adicional" placeholder="0.00">
                    </div>

                    <!-- Resumen del Total -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total de la Venta:</strong>
                            <h5 class="mb-0">S/ <span id="charge-total">0.00</span></h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn-save">
                        <i class="bi bi-check-circle"></i> Guardar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    @endif

    <!-- Modal Cerrar Contómetro -->
    <div class="modal fade" id="finalMeasurementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Contómetro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Isla:</label>
                    <select id="select-isle-measurement" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                    </select>

                    <label>Surtidor:</label>
                    <select id="select-pump-measurement" class="form-select mb-2">
                        <option value="">Seleccione un surtidor</option>
                    </select>

                    <label>Lado:</label>
                    <input type="text" class="form-control mb-2" id="pump_side" disabled>

                    <label>Valor Inicial:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="initial_measurement_value"
                        readonly>

                    <label>Valor Final:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="final_measurement_value">

                    <label>Valor Teórico:</label>
                    <input type="number" step="0.001" class="form-control mb-2" id="theorical_measurement_value"
                        readonly>

                    <label>Diferencia:</label>
                    <input type="number" step="0.001" class="form-control" id="difference_measurement_value"
                        readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-measurement">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Abrir Caja -->
    <div class="modal fade" id="initialCashModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Abrir Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Monto Inicial:</label>
                    <input type="number" step="0.01" class="form-control" id="initial_cash_amount">
                    <label>Isla:</label>
                    <select id="select-isle-initial" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                        @foreach ($isles ?? [] as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-initial">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cerrar Caja -->
    <div class="modal fade" id="finalCashModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Monto Inicial:</label>
                        <input type="number" step="0.01" class="form-control" id="initial_cash_amount_final"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>+ Ventas en Efectivo:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_sales_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>- Egresos del Día:</label>
                        <input type="number" step="0.01" class="form-control" id="expenses_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>- Prestamos Otorgados:</label>
                        <input type="number" step="0.01" class="form-control" id="loans_granted_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>+ Recuperacion de Prestamos:</label>
                        <input type="number" step="0.01" class="form-control" id="loans_recovered_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="@if (auth()->user()->role->nombre === 'worker') d-none @endif mb-3"> <label>- Adicional (Vuelto):</label>
                        <input type="number" step="0.01" class="form-control" id="adicional_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Monto Calculado:</label>
                        <input type="number" step="0.01" class="form-control" id="real_cash_amount"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Isla:</label>
                        <select id="select-isle-final" class="form-select mb-2">
                            <option value="">Seleccione una isla</option>
                            @foreach ($isles ?? [] as $isle)
                                <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Monto Final:</label>
                        <input type="number" step="0.01" class="form-control" id="final_cash_amount"
                            placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-final">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Egreso -->
    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Egreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Isla:</label>
                        <select id="select-isle-expense" class="form-select mb-2">
                            <option value="">Seleccione una isla</option>
                            @foreach ($isles ?? [] as $isle)
                                <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div
                        class="mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label class="form-label">Monto Caja Chica:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_amount" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto:</label>
                        <input type="number" step="0.01" class="form-control" id="expense_amount"
                            placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <input type="text" class="form-control" id="expense_description"
                            placeholder="Descripción del egreso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría:</label>
                        <input type="text" class="form-control" id="expense_category"
                            placeholder="Categoría del egreso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Método de Pago:</label>
                        <select class="form-select" id="expense_payment_method">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones:</label>
                        <textarea class="form-control" id="expense_observation" placeholder="Observaciones adicionales"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-expenses">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bóveda -->
    <div class="modal fade" id="vaultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar a Bóveda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- CORRECCIÓN: Cambiado ID a 'select-isle-vault' --}}
                    <select id="select-isle-vault" class="form-select mb-2">
                        <option value="">Seleccione una isla</option>
                        @foreach ($isles ?? [] as $isle)
                            <option value="{{ $isle->id }}">{{ $isle->name }}</option>
                        @endforeach
                    </select>
                    <div class="mb-3 
                    @if (auth()->user()->role->nombre === 'worker') d-none @endif
                    ">
                        <label>Total Caja Chica:</label>
                        <input type="number" step="0.01" class="form-control" id="cash_amount_acumulated"
                            placeholder="0.00" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Monto a enviar:</label>
                        <input type="number" step="0.01" class="form-control" id="vault_amount" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-vault">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal agregar productos-->
    <div class="modal fade" id="addProductsModal" tabindex="-1" aria-labelledby="addProductsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductsModalLabel">
                        <i class="bi bi-cart-plus me-2"></i>Agregar Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Campos ocultos -->
                    <input type="hidden" id="product_id">
                    <input type="hidden" id="tank_id">
                    <input type="hidden" id="pump_id">

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted mb-1">
                                    <i class="bi bi-box-seam me-1"></i>Producto:
                                </label>
                                <input type="text" class="form-control form-control-sm fw-semibold" id="lbl-name"
                                    disabled>
                            </div>
                            <div class="row g-2">
                                <div class="col-8">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bi bi-currency-dollar me-1"></i>Precio Unitario:
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/</span>
                                        <input type="text" class="form-control text-end fw-semibold" id="lbl-price"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-4 d-flex align-items-end">
                                    <div class="form-check form-switch w-100">
                                        <input class="form-check-input" type="checkbox" id="checkPrecioM"
                                            role="switch">
                                        <label class="form-check-label small" for="checkPrecioM">
                                            <i class="bi bi-tag me-1"></i>Mayorista
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modo de Ingreso -->
                    <div class="card border-primary mb-3">
                        <div class="card-body p-3">
                            <label class="form-label small fw-bold mb-2 d-block">
                                <i class="bi bi-calculator me-1"></i>Modo de Ingreso:
                            </label>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small">
                                    <i class="bi bi-cash-coin me-1"></i>Por Subtotal
                                </span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggleGalonesSubtotal"
                                        role="switch" value="false" checked>
                                </div>
                                <span class="small">
                                    <i class="bi bi-droplet me-1"></i>Por Galones
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad / Subtotal -->
                    <div class="row g-3 mb-3">
                        <div class="col-12" id="galonesSection">
                            <label class="form-label small fw-bold">
                                <i class="bi bi-droplet-fill me-1 text-primary"></i>Cantidad (Galones):
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.001" class="form-control text-end" id="txt-quantity"
                                    value="1" min="0.001" oninput="calcularSubtotal()" placeholder="0.000">
                                <span class="input-group-text">gal</span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Ingrese la cantidad en galones
                            </small>
                        </div>
                        <div class="col-12" id="subtotalSection" style="display: none;">
                            <label class="form-label small fw-bold">
                                <i class="bi bi-cash-stack me-1 text-success"></i>Subtotal (S/):
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" class="form-control text-end" id="txt-subtotal"
                                    value="1" min="0.01" oninput="calcularGalones()" placeholder="0.00">
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Ingrese el monto total a cobrar
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="addProductDirect()">
                        <i class="bi bi-plus-circle me-1"></i>Agregar Producto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lista de Clientes con Contrato -->
    <div class="modal fade" id="modalClientList" tabindex="-1" aria-labelledby="modalClientListLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalClientListLabel">
                        <i class="bi bi-person-lines-fill me-2"></i>Lista de Clientes con Contrato
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="filter-client-modal-list" class="form-control" placeholder="Buscar por cliente o N° documento (DNI / RUC)...">
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Documento</th>
                                    <th>Cliente / Razón Social</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-modal-clients-list">
                                <!-- Se carga dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Seleccionar Contrato del Cliente -->
    <div class="modal fade" id="modalSelectContract" tabindex="-1" aria-labelledby="modalSelectContractLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalSelectContractLabel">
                        <i class="bi bi-journal-check me-2"></i>Contratos del Cliente: <span id="modal-contract-client-name" class="fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="modal-contract-navigation" class="mb-2" style="display:none;">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-modal-contract-back">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Contrato / Documento</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-modal-select-contract">
                                <!-- Se carga dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/TextoComoTabla.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var expenseModal = document.getElementById('expenseModal');
            if (expenseModal) {
                expenseModal.addEventListener('shown.bs.modal', function() {
                    var select = document.getElementById('select-isle-expense');
                    if (select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                });
            }

            var finalCashModal = document.getElementById('finalCashModal');
            if (finalCashModal) {
                finalCashModal.addEventListener('hidden.bs.modal', function(event) {
                    document.getElementById('select-isle-final').value = "";
                    document.getElementById('final_cash_amount').value = "";
                });
            }

            const checkVentaFicticia = document.getElementById('venta_ficticia');
            const inputCliente = document.getElementById('client_name');
            const inputPlaca = document.getElementById('vehicle_plate');
            const inputDocumento = document.getElementById('document');
            const btnCVarios = document.getElementById('btn_c_varios');
            const paymentSection = document.getElementById('payment-methods-section');

            if (checkVentaFicticia) {
                checkVentaFicticia.addEventListener('change', function() {
                    const paymentInputs = paymentSection.querySelectorAll('input');

                    if (this.checked) {
                        inputCliente.value = 'CALIBRACIÓN';
                        inputCliente.disabled = true;                        
                        inputPlaca.value = '0-0';
                        inputPlaca.disabled = true;
                        
                        if (inputDocumento) {
                            inputDocumento.value = '00000000';
                            inputDocumento.disabled = true;
                        }

                        if (btnCVarios) {
                            btnCVarios.disabled = true;
                        }

                        paymentInputs.forEach(input => {
                            input.disabled = true;
                        });

                    } else {
                        inputCliente.value = '';
                        inputCliente.disabled = false;                        
                        inputPlaca.value = '';
                        inputPlaca.disabled = false;
                        
                        if (inputDocumento) {
                            inputDocumento.value = '';
                            inputDocumento.disabled = false;
                        }

                        if (btnCVarios) {
                            btnCVarios.disabled = false;
                        }

                        paymentInputs.forEach(input => {
                            if (input.type === 'checkbox') {
                                input.disabled = false;
                            } else if (input.type === 'number') {
                                let relatedCheckboxId = 'cbx_' + input.id;
                                let relatedCheckbox = document.getElementById(relatedCheckboxId);

                                if (relatedCheckbox && relatedCheckbox.checked) {
                                    input.disabled = false; // Si estaba marcado antes, lo habilitamos
                                } else {
                                    input.disabled = true; // Si no, se queda bloqueado
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
    
    <script>
        var isles = @json($isles ?? []);
        var pumps = @json($pumps ?? []);
        var assignedIsle = @json($assignedIsle ?? null);
        var directSaleProducts = [];

        // Ocultar sidebar automáticamente al cargar la págin

        var clients = @json($clients);
        var paymentMethods = @json($payment_methods);

        function setDefaultIsle() {
            const currentVal = $('#select-isle').val();
            if (currentVal && currentVal !== '' && currentVal !== 'null') {
                return currentVal;
            }
            const defaultIsle = assignedIsle || (Array.isArray(isles) && isles.length > 0 ? isles[0].id : '');
            if (defaultIsle) {
                $('#select-isle').val(defaultIsle);
            }
            return defaultIsle || '';
        }

        $(document).ready(function() {
            if ($('#tipo-venta').val() === 'directa') {
                $('#type_sale').val('0');
                $('#isle-select-card').hide();
                $('#products-direct-card').hide();
                $('#btn-add-editable-product-row').show();
                setDefaultIsle();

                loadProductsBySede();
                $('#credit-checkbox-container').show(); // Mostrar checkbox para venta directa
            } else {
                $('#credit-checkbox-container').hide(); // Ocultar checkbox para otros tipos
                $('#is-credit-sale').prop('checked', false); // Desmarcar checkbox
            }

            if ($('#toggleGalonesSubtotal').val() == 'false') {
                $('#galonesSection').hide();
                $('#subtotalSection').show();
            } else {
                $('#galonesSection').show();
                $('#subtotalSection').hide();
            }
        })

        $('#btn-save').click(function() {
            guardarVenta();
        });
        // Ocultar/mostrar métodos de pago cuando se marca el checkbox de crédito
        $('#is-credit-sale').on('change', function() {
            if ($(this).is(':checked')) {
                $('#payment-methods-section').hide();
                $('#credit-checkbox-container').show();
                $('.credit-extra-fields').show();
                $('#type_sale').val('2');
                resetPaymentMethods();
                recalculateTotal();
            } else {
                $('#payment-methods-section').show();
                $('#type_sale').val('0');
                $('.credit-extra-fields').hide();
                $('#credit_number').val('');
                $('#voucher_code').val('');
                $('#responsible_id').val('');
                $('#detail').val('');
                $('#credit-number-section').hide();
                $('#is-vuelto-adicional').prop('checked', false);
                $('#vuelto-adicional-container').hide();
            }
        });
            $('#is-vuelto-adicional').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#vuelto-adicional-section').show();
                    calculateDifference();
                } else {
                    $('#vuelto-adicional-section').hide();
                    $('#adicional').val('0.00');
                }
            });
            
            $('input[id^="amount_"]').on('input', function() {
                calculateDifference();
        });
        (function() {
            let tipoAnterior = $('#tipo-venta').val();

            function aplicarTipoVenta(tipoVenta) {
                const map = {
                    directa: 0,
                    contrato: 1
                };
                $('#type_sale').val(map[tipoVenta]);
                $('#cliente-search-card').hide();
                $('#products-contract-credit').hide();
                $('#products-direct-card').hide();
                // Por defecto ocultar selector de islas; sólo mostrar para venta directa
                $('#isle-select-card').hide();
                $('#btn-add-editable-product-row').hide();
                $('#quick-add-product-subtotal').hide();
                $('#tbl-products').empty();
                $('#tbl-products-contract').empty();
                $('#orden').val('');
                $('#area').val('');
                $('#current-order-detail-id').val('');

                if (tipoVenta === 'directa') {
                    // Mostrar productos directos y selector de islas para venta directa
                    $('#products-direct-card').hide();
                    $('#btn-add-editable-product-row').show();
                    $('#credit-checkbox-container').show(); // Mostrar checkbox
                    $('#is-credit-sale').prop('checked', false); // Desmarcar por defecto
                    $('#payment-methods-section').show(); // Mostrar pagos por defecto
                    $('#paga-con-section').show();
                    $('#isle-select-card').hide();
                    setDefaultIsle();
                    loadProductsBySede();
                } else {
                    // Para contrato mostramos búsqueda de cliente
                    $('#cliente-search-card').show();
                    $('#isle-select-card').hide();
                    $('#btn-add-editable-product-row').hide();
                    $('#credit-checkbox-container').hide(); // Ocultar checkbox
                    $('#is-credit-sale').prop('checked', false);
                    resetClientSearch();
                    setTimeout(function() {
                        $('#search-client').focus();
                    }, 100);
                }

                tipoAnterior = tipoVenta;
            }

            $('#tipo-venta').on('focus', function() {
                tipoAnterior = this.value;
            });

            $('#tipo-venta').on('change', function() {
                const nuevoTipo = this.value;
                const hayProductos = $('#tbl-order-items tr').length > 0;

                if (hayProductos) {
                    Swal.fire({
                        title: '¿Cambiar tipo de venta?',
                        text: 'Se eliminarán todos los productos cargados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            $('#tbl-order-items').empty();
                            $('#total').text('0.00');

                            aplicarTipoVenta(nuevoTipo);
                            ToastMessage.fire({
                                text: 'Tipo de venta cambiado y productos limpiados.'
                            });
                        } else {
                            $('#tipo-venta').val(tipoAnterior);
                        }
                    });
                } else {
                    aplicarTipoVenta(nuevoTipo);
                }
            });
        })();
        

        function resetClientSearch() {
            $('#search-client').val('');
            $('#client_id').val('');

            var autocompleteConfig = {
                source: function(request, response) {
                    var results = $.map(clients, function(item) {
                        if (!item) {
                            return null;
                        }

                        var displayName = (item.business_name || item.contact_name || item.commercial_name || '').toString();
                        var doc = (item.document || '').toString();
                        var nameLower = displayName.toLowerCase();
                        var searchTerm = request.term.toLowerCase();

                        if (nameLower.includes(searchTerm) || doc.includes(request.term)) {
                            return {
                                label: `${displayName} ${doc ? '(' + doc + ')' : ''}`,
                                value: displayName,
                                id: item.id,
                                document: doc
                            };
                        }
                    });
                    response(results);
                },
                appendTo: '.container-fluid',
                select: function(event, ui) {
                    $('#client_id').val(ui.item.id);
                    $('#document').val(ui.item.document);
                    $('#client_name').val(ui.item.value);
                    $('#search-client').val(ui.item.value);

                    // Cargar contratos específicos del cliente solo si el ID no es nulo
                    if (ui.item.id && ui.item.id !== null) {
                        cargarContratosCliente(ui.item.id);
                    }
                },
                minLength: 2
            };

            if ($('#search-client').length) {
                $('#search-client').autocomplete(autocompleteConfig).autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append(`<div class="d-flex justify-content-between">
                                <span>${item.label}</span>
                             </div>`)
                        .appendTo(ul);
                };
            }

            if ($('#client_name').length) {
                $('#client_name').autocomplete(autocompleteConfig).autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append(`<div class="d-flex justify-content-between">
                                <span>${item.label}</span>
                             </div>`)
                        .appendTo(ul);
                };
            }
        }

        function renderModalClientsList(filterText = '') {
            const $tbody = $('#tbl-modal-clients-list').empty();
            const term = (filterText || '').toLowerCase().trim();

            const filtered = (clients || []).filter(function(item) {
                if (!item) return false;
                const name = (item.business_name || item.contact_name || item.commercial_name || '').toString().toLowerCase();
                const doc = (item.document || '').toString().toLowerCase();
                return name.includes(term) || doc.includes(term);
            });

            if (filtered.length === 0) {
                $tbody.append(`
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                            No se encontraron clientes registrados.
                        </td>
                    </tr>
                `);
                return;
            }

            filtered.forEach(function(item) {
                const clientDisplayName = item.business_name || item.contact_name || item.commercial_name || 'Sin nombre';
                const clientDoc = item.document || '-';
                const safeName = clientDisplayName.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                const safeDoc = clientDoc.toString().replace(/'/g, "\\'").replace(/"/g, "&quot;");

                $tbody.append(`
                    <tr style="cursor: pointer;" onclick="selectClientFromModal('${item.id}', '${safeName}', '${safeDoc}')">
                        <td><span class="badge bg-secondary px-2 py-1">${clientDoc}</span></td>
                        <td class="fw-semibold">${clientDisplayName}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>Seleccionar
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function selectClientFromModal(clientId, clientName, clientDoc) {
            $('#client_id').val(clientId);
            $('#document').val(clientDoc);
            $('#client_name').val(clientName);
            $('#search-client').val(clientName);

            if (clientId && clientId !== 'null') {
                cargarContratosCliente(clientId);
            }

            const modalEl = document.getElementById('modalClientList');
            if (modalEl) {
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    $(modalEl).modal('hide');
                }
            }
        }

        $(document).ready(function() {
            $('#modalClientList').on('shown.bs.modal', function () {
                $('#filter-client-modal-list').val('').focus();
                renderModalClientsList('');
            });

            $('#filter-client-modal-list').on('input', function () {
                renderModalClientsList($(this).val());
            });

            if ($('#select-isle').val()) {
                onIsleChange();
            }
        });

        // ---------------------------------------------------------
        // CASCADA DE SELECTORES: ISLA -> SURTIDOR -> LADO
        // ---------------------------------------------------------
        function onIsleChange() {
            var selectedIsle = $('#select-isle').val();
            var $pumpSelect = $('#select-pump');
            var $sideSelect = $('#select-side');

            $pumpSelect.empty().append('<option value="">-- Seleccionar --</option>');
            $sideSelect.empty().append('<option value="">-- Seleccionar --</option>').prop('disabled', true);

            if (!selectedIsle || !Array.isArray(pumps) || !pumps.length) {
                $pumpSelect.prop('disabled', true);
                loadProductsBySede();
                return;
            }

            var pumpsInIsle = pumps.filter(function(p) {
                var pIsleId = p.isle_id !== undefined && p.isle_id !== null ? p.isle_id : (p.isle ? p.isle.id : null);
                return String(pIsleId) === String(selectedIsle)
                    && (p.deleted == 0 || p.deleted === false || p.deleted === null || p.deleted === undefined);
            });

            if (!pumpsInIsle.length) {
                $pumpSelect.prop('disabled', true);
                loadProductsBySede();
                return;
            }

            var uniqueMachineNames = [];
            pumpsInIsle.forEach(function(p) {
                var mName = p.name || ('Surtidor #' + p.id);
                if (!uniqueMachineNames.includes(mName)) {
                    uniqueMachineNames.push(mName);
                }
            });

            uniqueMachineNames.forEach(function(mName) {
                $pumpSelect.append('<option value="' + mName + '">' + mName + '</option>');
            });

            $pumpSelect.prop('disabled', false);
            loadProductsBySede();
        }

        function onPumpChange() {
            var selectedIsle = $('#select-isle').val();
            var selectedMachineName = $('#select-pump').val();
            var $sideSelect = $('#select-side');

            $sideSelect.empty().append('<option value="">-- Seleccionar Lado --</option>');

            if (!selectedIsle || !selectedMachineName || !Array.isArray(pumps) || !pumps.length) {
                $sideSelect.prop('disabled', true);
                return;
            }

            var matchingPumps = pumps.filter(function(p) {
                var pIsleId = p.isle_id !== undefined && p.isle_id !== null ? p.isle_id : (p.isle ? p.isle.id : null);
                var mName = p.name || ('Surtidor #' + p.id);
                return String(pIsleId) === String(selectedIsle)
                    && mName === selectedMachineName
                    && (p.deleted == 0 || p.deleted === false || p.deleted === null || p.deleted === undefined);
            });

            if (!matchingPumps.length) {
                $sideSelect.prop('disabled', true);
                return;
            }

            var uniqueSides = [];
            matchingPumps.forEach(function(p) {
                var sVal = p.side || 1;
                if (!uniqueSides.includes(sVal)) {
                    uniqueSides.push(sVal);
                }
            });
            uniqueSides.sort(function(a, b) { return a - b; });

            uniqueSides.forEach(function(sVal) {
                $sideSelect.append('<option value="' + sVal + '">Lado ' + sVal + '</option>');
            });

            $sideSelect.prop('disabled', false);
        }

        function onSideChange() {
            var chosenSide = $('#select-side').val();
            var selectedIsle = $('#select-isle').val();
            var selectedMachineName = $('#select-pump').val();
            if (!chosenSide || !selectedIsle || !selectedMachineName) return;

            var sidePumps = pumps.filter(function(p) {
                var pIsleId = p.isle_id !== undefined && p.isle_id !== null ? p.isle_id : (p.isle ? p.isle.id : null);
                var mName = p.name || ('Surtidor #' + p.id);
                return String(pIsleId) === String(selectedIsle)
                    && mName === selectedMachineName
                    && String(p.side || 1) === String(chosenSide)
                    && (p.deleted == 0 || p.deleted === false || p.deleted === null || p.deleted === undefined);
            });

            if (!sidePumps.length) return;

            var chosenPump = sidePumps[0];
            var productId = chosenPump.product_id;

            appendEditableProductRow(productId, chosenSide, selectedIsle, selectedMachineName, chosenPump.id);
        }

        function toggleDetailedMode() {
            var isChecked = $('#chk-detailed-mode').is(':checked');
            var $container = $('#detailed-selectors-container');
            var $statusText = $('#detailed-mode-status-text');
            var $isleSelect = $('#select-isle');
            var $pumpSelect = $('#select-pump');
            var $sideSelect = $('#select-side');

            if (isChecked) {
                $container.removeClass('opacity-50').css('pointer-events', 'auto');
                $isleSelect.prop('disabled', false);
                $statusText.html('<i class="bi bi-check-circle-fill text-success me-1"></i>Modo detallado activo');
                if ($isleSelect.val()) {
                    onIsleChange();
                }
            } else {
                $container.addClass('opacity-50').css('pointer-events', 'none');
                $isleSelect.prop('disabled', true);
                $pumpSelect.empty().append('<option value="">-- Seleccionar Surtidor --</option>').prop('disabled', true);
                $sideSelect.empty().append('<option value="">-- Seleccionar Lado --</option>').prop('disabled', true);
                $statusText.html('<i class="bi bi-info-circle me-1"></i>Opcional: Desactivado');
            }
        }

        function updateRowPumpBadge($row, productId, chosenPumpId) {
            let $pumpBadge = $row.find('.row-pump-details');
            if (!$pumpBadge.length) {
                $row.find('td:first-child').append('<div class="row-pump-details mt-1"></div>');
                $pumpBadge = $row.find('.row-pump-details');
            }

            if (!$('#chk-detailed-mode').is(':checked')) {
                $pumpBadge.empty();
                return;
            }

            var infoText = '';
            var rowSide = $row.data('side') || $('#select-side').val();
            var rowIsle = $row.data('isle') || $('#select-isle').val();
            var rowMachine = $row.data('machine') || $('#select-pump').val();

            if (chosenPumpId && Array.isArray(pumps)) {
                var found = pumps.find(p => String(p.id) === String(chosenPumpId));
                if (found) {
                    var pName = found.name || ('Surtidor #' + found.id);
                    var sName = found.side ? ('Lado ' + found.side) : '';
                    var iName = found.isle ? (found.isle.name || ('Isla #' + found.isle_id)) : '';
                    var parts = [];
                    if (pName) parts.push(pName);
                    if (sName) parts.push(sName);
                    if (iName) parts.push(iName);
                    infoText = parts.join(' • ');
                }
            }
            if (!infoText) {
                var details = getPumpDetailsForProduct(productId, rowIsle, rowSide, rowMachine);
                if (details) infoText = details.full_info;
            }

            if (infoText) {
                $pumpBadge.html('<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-2 px-2 py-1 small fw-bold" style="font-size: 0.75rem;"><i class="bi bi-fuel-pump me-1"></i>' + infoText + '</span>');
            } else {
                $pumpBadge.empty();
            }
        }

        function buildRowPumpSelectHtml(productId, currentPumpId, rowSide, rowIsle, rowMachine) {
            if (!$('#chk-detailed-mode').is(':checked')) return '';
            var details = getPumpDetailsForProduct(productId, rowIsle || $('#select-isle').val(), rowSide || $('#select-side').val(), rowMachine || $('#select-pump').val());
            var infoText = details ? details.full_info : '';
            if (!infoText) return '';
            return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-2 px-2 py-1 small fw-bold" style="font-size: 0.75rem;"><i class="bi bi-fuel-pump me-1"></i>' + infoText + '</span>';
        }

        function getPumpDetailsForProduct(productId, selectedIsleId, selectedSide, selectedMachineName) {
            if (!Array.isArray(pumps) || !pumps.length || !productId) return null;
            
            let matches = pumps.filter(function(p) {
                var pIsleId = p.isle_id !== undefined && p.isle_id !== null ? p.isle_id : (p.isle ? p.isle.id : null);
                var mName = p.name || ('Surtidor #' + p.id);
                var isIsleMatch = !selectedIsleId || String(pIsleId) === String(selectedIsleId);
                var isMachineMatch = !selectedMachineName || mName === selectedMachineName;
                var isSideMatch = !selectedSide || String(p.side || 1) === String(selectedSide);

                return String(p.product_id) === String(productId)
                    && isIsleMatch
                    && isMachineMatch
                    && isSideMatch
                    && (p.deleted == 0 || p.deleted === false || p.deleted === null || p.deleted === undefined);
            });

            if (!matches.length) return null;

            const formattedList = matches.map(function(m) {
                const pumpName = m.name || ('Surtidor #' + m.id);
                const sideName = m.side ? ('Lado ' + m.side) : '';
                const isleName = m.isle ? (m.isle.name || ('Isla #' + m.isle_id)) : (m.isle_id ? ('Isla #' + m.isle_id) : '');

                const parts = [];
                if (pumpName) parts.push(pumpName);
                if (sideName) parts.push(sideName);
                if (isleName) parts.push(isleName);
                return parts.join(' • ');
            });

            return {
                pump_id: matches[0].id,
                full_info: formattedList.join(' | ')
            };
        }

        function buildDirectSaleProducts(data) {
            const selectedIsle = $('#select-isle').val();
            const seen = {};
            directSaleProducts = [];

            (Array.isArray(data) ? data : []).forEach(function(tank) {
                (tank.products || []).forEach(function(product) {
                    const pumpInfoObj = getPumpDetailsForProduct(product.id, selectedIsle);
                    const key = product.id + '-' + (tank.id || '') + '-' + (pumpInfoObj ? pumpInfoObj.pump_id : '');
                    if (seen[key]) return;
                    seen[key] = true;

                    const stockVal = parseFloat(product.stock !== undefined ? product.stock : (tank.stored_quantity !== undefined ? tank.stored_quantity : 0));

                    directSaleProducts.push({
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price || 0),
                        stock: stockVal,
                        measurement_unit: product.measurement_unit || '',
                        tank_id: tank.id || '',
                        pump_id: pumpInfoObj ? pumpInfoObj.pump_id : '',
                        pump_info: pumpInfoObj ? pumpInfoObj.full_info : '',
                        order_detail_id: product.order_detail_id || '',
                    });
                });
            });
        }

        function buildProductOptionsData(filterSide, filterIsle, filterMachineName) {
            const nameCounts = {};

            let filteredProducts = directSaleProducts;

            if ($('#chk-detailed-mode').is(':checked') && filterSide && filterIsle && filterMachineName && Array.isArray(pumps) && pumps.length) {
                const validFuelProductIds = pumps.filter(function(p) {
                    var pIsleId = p.isle_id !== undefined && p.isle_id !== null ? p.isle_id : (p.isle ? p.isle.id : null);
                    var mName = p.name || ('Surtidor #' + p.id);
                    return String(pIsleId) === String(filterIsle)
                        && mName === filterMachineName
                        && String(p.side || 1) === String(filterSide)
                        && (p.deleted == 0 || p.deleted === false || p.deleted === null || p.deleted === undefined);
                }).map(p => String(p.product_id));

                filteredProducts = directSaleProducts.filter(function(product) {
                    if (validFuelProductIds.includes(String(product.id))) {
                        return true;
                    }
                    if (!product.pump_id && (!product.tank_id || product.tank_id === '')) {
                        return true;
                    }
                    return false;
                });
            }

            filteredProducts.forEach(function(product) {
                nameCounts[product.name] = (nameCounts[product.name] || 0) + 1;
            });
            const nameSeen = {};

            return filteredProducts.map(function(product) {
                let label = product.name;
                if (nameCounts[product.name] > 1) {
                    nameSeen[product.name] = (nameSeen[product.name] || 0) + 1;
                    label = `${product.name} (Tanque ${product.tank_id || nameSeen[product.name]})`;
                }

                const stockFormatted = parseFloat(product.stock || 0).toFixed(3);
                label = `${label} (Stock: ${stockFormatted})`;

                return {
                    id: product.id,
                    label: label,
                    price: product.price,
                    stock: product.stock,
                    tank_id: product.tank_id || '',
                    pump_id: product.pump_id || '',
                    pump_info: product.pump_info || '',
                    order_detail_id: product.order_detail_id || '',
                };
            });
        }

        function productOptionsHtml(selectedId, filterSide, filterIsle, filterMachineName) {
            return buildProductOptionsData(filterSide, filterIsle, filterMachineName).map(function(opt) {
                const selected = String(opt.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${opt.id}"
                    data-price="${opt.price}"
                    data-original-price="${opt.price}"
                    data-tank-id="${opt.tank_id}"
                    data-pump-id="${opt.pump_id}"
                    data-pump-info="${opt.pump_info || ''}"
                    data-order-detail-id="${opt.order_detail_id}"
                    ${selected}>${opt.label}</option>`;
            }).join('');
        }

        function productComboLabel(selectedId) {
            if (!selectedId) return '(Seleccione un producto...)';
            const match = buildProductOptionsData().find(o => String(o.id) === String(selectedId));
            return match ? match.label : '(Seleccione un producto...)';
        }

        function productComboMenuHtml(selectedId, filterSide, filterIsle, filterMachineName) {
            const placeholderActive = !selectedId ? 'is-active' : '';
            let html = `<button type="button" class="custom-combo-option is-placeholder ${placeholderActive}" data-value="">(Seleccione un producto...)</button>`;
            html += buildProductOptionsData(filterSide, filterIsle, filterMachineName).map(function(opt) {
                const active = String(opt.id) === String(selectedId) ? 'is-active' : '';
                return `<button type="button" class="custom-combo-option ${active}"
                    data-value="${opt.id}"
                    data-price="${opt.price}"
                    data-tank-id="${opt.tank_id}"
                    data-pump-id="${opt.pump_id}"
                    data-pump-info="${opt.pump_info || ''}"
                    data-order-detail-id="${opt.order_detail_id}">${opt.label}</button>`;
            }).join('');
            return html;
        }

        function appendEditableProductRow(selectedProductId, filterSide, filterIsle, filterMachineName, chosenPumpId) {
            if (!directSaleProducts.length) {
                ToastError.fire({
                    title: 'Sin productos',
                    text: 'No hay productos disponibles para agregar a la venta.'
                });
                return;
            }

            const availableOptions = buildProductOptionsData(filterSide, filterIsle, filterMachineName);
            const initialProductId = selectedProductId || (availableOptions.length ? availableOptions[0].id : directSaleProducts[0].id);

            const product = directSaleProducts.find(p => String(p.id) === String(initialProductId));

            const price = product ? parseFloat(product.price || 0) : 0;
            const quantity = product ? 1 : 0;
            const subtotal = price * quantity;
            const placeholderSelected = product ? '' : 'selected';
            const comboSelectedId = product ? product.id : null;
            
            const rowPumpId = chosenPumpId || (product ? product.pump_id : '');
            const pumpSelectHtml = buildRowPumpSelectHtml(comboSelectedId, rowPumpId);

            const row = `
                <tr class="editable-sale-row"
                    data-product-id="${product ? product.id : ''}"
                    data-tank-id="${product ? (product.tank_id || '') : ''}"
                    data-pump-id="${rowPumpId}"
                    data-side="${filterSide || ''}"
                    data-isle="${filterIsle || ''}"
                    data-machine="${filterMachineName || ''}"
                    data-original-price="${price}"
                    data-current-price="${price}"
                    data-subtotal="${subtotal.toFixed(2)}"
                    data-calc-source="quantity">
                    <td>
                        <div class="custom-combo">
                            <button type="button" class="custom-combo-trigger">
                                <span class="custom-combo-label${product ? '' : ' is-placeholder'}">${productComboLabel(comboSelectedId)}</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="custom-combo-menu">
                                ${productComboMenuHtml(comboSelectedId, filterSide, filterIsle, filterMachineName)}
                            </div>
                        </div>
                        <div class="row-pump-details mt-1">
                            ${pumpSelectHtml}
                        </div>
                        <select class="form-select form-select-sm sale-product-select d-none">
                            <option value="" data-price="0" data-original-price="0" ${placeholderSelected} disabled>(Seleccione un producto...)</option>
                            ${productOptionsHtml(comboSelectedId, filterSide, filterIsle, filterMachineName)}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end sale-unit-price" value="${price.toFixed(2)}">
                    </td>
                    <td>
                        <input type="number" step="0.001" min="0" class="form-control form-control-sm text-end sale-quantity" value="${quantity.toFixed(3)}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end sale-subtotal" value="${subtotal.toFixed(2)}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-xs" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#tbl-order-items').append(row);
            const $newRow = $('#tbl-order-items tr.editable-sale-row').last();
            if (chosenPumpId) {
                updateRowPumpBadge($newRow, initialProductId, chosenPumpId);
            }
            recalculateTotal();
        }

        function recalculateEditableRow(row, source) {
            const $row = $(row);
            const price = parseFloat($row.find('.sale-unit-price').val()) || 0;
            let quantity = parseFloat($row.find('.sale-quantity').val()) || 0;
            let subtotal = parseFloat($row.find('.sale-subtotal').val()) || 0;

            if (source === 'subtotal') {
                quantity = price > 0 ? subtotal / price : 0;
                $row.find('.sale-quantity').val(quantity.toFixed(3));
            } else {
                subtotal = price * quantity;
                $row.find('.sale-subtotal').val(subtotal.toFixed(2));
            }

            const selected = $row.find('.sale-product-select option:selected');
            $row.data('product-id', selected.val());
            $row.data('tank-id', selected.data('tank-id') || '');
            $row.data('pump-id', selected.data('pump-id') || '');
            $row.data('original-price', parseFloat(selected.data('original-price')) || price);
            $row.data('current-price', price);
            $row.data('subtotal', Math.round(subtotal * 100) / 100);
            $row.attr('data-product-id', selected.val());
            $row.attr('data-tank-id', selected.data('tank-id') || '');
            $row.attr('data-pump-id', selected.data('pump-id') || '');
            $row.attr('data-original-price', parseFloat(selected.data('original-price')) || price);
            $row.attr('data-current-price', price);
            $row.attr('data-subtotal', (Math.round(subtotal * 100) / 100).toFixed(2));
        }

        $('#btn-add-editable-product-row').on('click', function() {
            appendEditableProductRow();
        });

        $(document).on('change', '.row-pump-select', function() {
            const $row = $(this).closest('tr');
            const chosenPumpId = $(this).val();
            $row.data('pump-id', chosenPumpId).attr('data-pump-id', chosenPumpId);
        });

        $('#tbl-order-items').on('change', '.sale-product-select', function() {
            const $row = $(this).closest('tr');
            const selected = $(this).find('option:selected');
            const productId = selected.val();
            const price = parseFloat(selected.data('price')) || 0;
            const currentPumpId = selected.data('pump-id') || '';

            if (parseFloat($row.find('.sale-quantity').val()) <= 0) {
                $row.find('.sale-quantity').val((1).toFixed(3));
                $row.data('calc-source', 'quantity');
            }

            $row.find('.sale-unit-price').val(price.toFixed(2));

            // Actualizar la lista desplegable de surtidores en la fila
            let $pumpBadge = $row.find('.row-pump-details');
            if (!$pumpBadge.length) {
                $row.find('td:first-child').append('<div class="row-pump-details mt-1"></div>');
                $pumpBadge = $row.find('.row-pump-details');
            }

            const newPumpSelectHtml = buildRowPumpSelectHtml(productId, currentPumpId);
            $pumpBadge.html(newPumpSelectHtml);

            var firstPumpVal = $pumpBadge.find('.row-pump-select').val() || currentPumpId;
            $row.data('pump-id', firstPumpVal).attr('data-pump-id', firstPumpVal);

            if (price === 0) {
                ToastError.fire({
                    icon: 'warning',
                    text: 'Este producto no tiene un precio configurado para la sede actual. Revísalo antes de cobrar.'
                });
            }

            recalculateEditableRow($row, $row.data('calc-source') || 'quantity');
            recalculateTotal();
        });

        // Combo de producto personalizado: abre/cierra el menú y sincroniza el <select> oculto.
        // El menú se mueve a #comboPortal (hijo directo de body) mientras está abierto, para
        // escapar del overflow:auto de la tabla (si no, quedaba recortado/oculto detrás del total).
        if (!$('#comboPortal').length) {
            $('body').append('<div id="comboPortal"></div>');
        }

        function positionComboMenu($combo, $menu) {
            const rect = $combo.find('.custom-combo-trigger')[0].getBoundingClientRect();
            $menu.css({
                top: rect.bottom + 4,
                left: rect.left,
                minWidth: rect.width
            });
        }

        function closeComboMenu($combo) {
            const $menu = $combo.data('open-menu');
            if (!$menu) return;
            $menu.removeClass('is-open').appendTo($combo);
            $combo.removeClass('is-open').removeData('open-menu');
        }

        $(document).on('click', '.custom-combo-trigger', function(e) {
            e.stopPropagation();
            const $combo = $(this).closest('.custom-combo');
            const wasOpen = $combo.hasClass('is-open');

            $('.custom-combo.is-open').each(function() {
                closeComboMenu($(this));
            });

            if (!wasOpen) {
                const $menu = $combo.find('.custom-combo-menu');
                $combo.addClass('is-open').data('open-menu', $menu);
                $menu.data('owner-combo', $combo).appendTo('#comboPortal').addClass('is-open');
                positionComboMenu($combo, $menu);
            }
        });

        $(document).on('click', '.custom-combo-option', function(e) {
            e.stopPropagation();
            const $option = $(this);
            const $menu = $option.closest('.custom-combo-menu');
            const $combo = $menu.data('owner-combo');
            if (!$combo) return;
            const $row = $combo.closest('tr');
            const value = $option.data('value') === undefined || $option.data('value') === null ? '' : String($option.data('value'));
            const isPlaceholder = $option.hasClass('is-placeholder');

            $menu.find('.custom-combo-option').removeClass('is-active');
            $option.addClass('is-active');
            $combo.find('.custom-combo-label')
                .text($option.text())
                .toggleClass('is-placeholder', isPlaceholder);

            closeComboMenu($combo);
            $row.find('.sale-product-select').val(value).trigger('change');
        });

        $(document).on('click', function() {
            $('.custom-combo.is-open').each(function() {
                closeComboMenu($(this));
            });
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.custom-combo.is-open').each(function() {
                    closeComboMenu($(this));
                });
            }
        });

        $(window).on('scroll resize', function() {
            $('.custom-combo.is-open').each(function() {
                closeComboMenu($(this));
            });
        });

        $('#tbl-order-items').on('input', '.sale-quantity', function() {
            const $row = $(this).closest('tr');
            $row.data('calc-source', 'quantity');
            recalculateEditableRow($row, 'quantity');
            recalculateTotal();
        });

        $('#tbl-order-items').on('input', '.sale-subtotal', function() {
            const $row = $(this).closest('tr');
            $row.data('calc-source', 'subtotal');
            recalculateEditableRow($row, 'subtotal');
            recalculateTotal();
        });

        $('#tbl-order-items').on('input', '.sale-unit-price', function() {
            const $row = $(this).closest('tr');
            recalculateEditableRow($row, $row.data('calc-source') || 'quantity');
            recalculateTotal();
        });

        function loadProductsBySede() {
            const isDirecta = $('#tipo-venta').val() === 'directa';
            // 1. Determinar isla seleccionada
            const selectedIsle = setDefaultIsle();
            
            // Referencias al botón y mensajes
            const btnProcesar = $('#btn-save');
            const containerBtn = btnProcesar.parent(); // El div contenedor
            
            // Limpiar alertas de bloqueo previas
            containerBtn.find('.alert-caja-cerrada').remove();

            if (!selectedIsle && !isDirecta) {
                $('#tbl-products').html('<div class="alert alert-info text-center">Seleccione una isla...</div>');
                btnProcesar.prop('disabled', true); // Bloquear si no hay isla
                return;
            }

            // --- NUEVA LÓGICA: VERIFICAR ESTADO DE CAJA (AJAX) ---
            if (selectedIsle) {
                // Bloqueamos temporalmente mientras consulta
                btnProcesar.prop('disabled', true); 

                // Construimos la URL dinámicamente. 
                // Nota: Asegúrate de tener una variable base o usar replace si estás en un archivo .js externo
                let urlCheck = "{{ route('cash_closes.check_status', ':id') }}";
                urlCheck = urlCheck.replace(':id', selectedIsle);

                $.ajax({
                    url: urlCheck,
                    method: 'GET',
                    success: function(response) {
                        if (response.isOpen) {
                            // CAJA ABIERTA: Habilitar botón
                            btnProcesar.prop('disabled', false);
                        } else {
                            // CAJA CERRADA: Mantener bloqueado y mostrar mensaje
                            btnProcesar.prop('disabled', true);
                            containerBtn.append(`
                                <small class="text-danger d-block mt-1 alert-caja-cerrada">
                                    <i class="bi bi-lock-fill"></i> Caja cerrada o no iniciada para esta isla, y no hay caja general abierta.
                                </small>
                            `);
                        }
                    },
                    error: function() {
                        console.error('Error verificando estado de caja');
                    }
                });
            } else if (isDirecta) {
                btnProcesar.prop('disabled', false);
            }
            // -----------------------------------------------------

            // TU CÓDIGO ORIGINAL PARA CARGAR PRODUCTOS CONTINÚA AQUÍ...
            $.ajax({
                url: "{{ route('products.prices') }}",
                method: 'GET',
                success: function(data) {
                    $('#tbl-products').empty(); 
                    buildDirectSaleProducts(data);

                    if ($('#tipo-venta').val() === 'directa') {
                        $('#tbl-order-items').empty();
                        if (directSaleProducts.length === 0) {
                            ToastError.fire({
                                title: 'Sin productos',
                                text: 'No hay productos configurados para esta sede.'
                            });
                        }
                        recalculateTotal();
                        return;
                    }

                    // Filtrar bombas para la isla seleccionada
                    const pumpsForIsle = Array.isArray(pumps) ? pumps.filter(p => parseInt(p.isle_id) ===
                        parseInt(selectedIsle) && (p.deleted == 0 || p.deleted === false)) : [];

                    if (pumpsForIsle.length === 0) {
                        $('#tbl-products').append(
                            '<div class="alert alert-warning text-center">No hay surtidores configurados para esta isla</div>'
                        );
                        return;
                    }

                    // ... (RESTO DE TU CÓDIGO DE CREACIÓN DE TARJETAS, COLORES, ETC. SE MANTIENE IGUAL) ...
                    
                    // Crear estructura de dos columnas: LADO 1 y LADO 2
                    const mainContainer = $('<div class="row">');
                    // ... (Copiar todo tu código de renderizado visual aquí) ...
                    const lado1Container = $('<div class="col-md-6"><h5 class="text-center mb-3 text-primary">LADO 1</h5><div class="pumps-column"></div></div>');
                    const lado2Container = $('<div class="col-md-6"><h5 class="text-center mb-3 text-primary">LADO 2</h5><div class="pumps-column"></div></div>');
                    
                    const lado1Pumps = pumpsForIsle.filter(p => parseInt(p.side) === 1);
                    const lado2Pumps = pumpsForIsle.filter(p => parseInt(p.side) === 2);

                                        function getProductColor(productName) {
                        const name = productName.toLowerCase();
                        if (name.includes('diesel') || name.includes('diésel') || name.includes('db5')) {
                            return {
                                bg: '#2c3e50',
                                text: '#ffffff'
                            }; // Azul oscuro
                        } else if (name.includes('premium') || name.includes('97')) {
                            return {
                                bg: '#e74c3c',
                                text: '#ffffff'
                            }; // Rojo
                        } else if (name.includes('regular') || name.includes('90') || name.includes('84')) {
                            return {
                                bg: '#27ae60',
                                text: '#ffffff'
                            }; // Verde
                        } else if (name.includes('gasolina')) {
                            return {
                                bg: '#3498db',
                                text: '#ffffff'
                            }; // Azul
                        } else if (name.includes('kerosene') || name.includes('keroseno')) {
                            return {
                                bg: '#f39c12',
                                text: '#ffffff'
                            }; // Naranja
                        } else {
                            return {
                                bg: '#95a5a6',
                                text: '#ffffff'
                            }; // Gris por defecto
                        }
                    }

                    function crearTarjetaSurtidor(pump, data) {
                        const pumpCard = $(`
                            <div class="mb-4">
                                <div class="text-center mb-3">
                                    <span class="badge" style="background-color: #0b2240; font-size: 14px; padding: 8px 16px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <i class="bi bi-fuel-pump-fill me-1"></i> ${pump.name || 'Surtidor ' + pump.id}
                                    </span>
                                </div>
                                <div class="products-buttons row g-3 justify-content-center">
                                </div>
                            </div>
                        `);

                        let found = false;

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function(tank) {
                                if (Array.isArray(tank.products) && tank.products.length > 0) {
                                    tank.products.forEach(function(product) {
                                        if (product.id == pump.product_id) {
                                            found = true;

                                            const colors = getProductColor(product.name);

                                            // Tarjeta estilo mockup
                                            const productBtn = $(`
                                                <div class="col-10 col-sm-8 col-lg-7"
                                                    data-product-id="${product.id}" 
                                                    data-product-name="${product.name}"
                                                    data-price="${parseFloat(product.price).toFixed(2)}"
                                                    data-observations="${product.observations || ''}" 
                                                    data-tank-id="${tank.id}" 
                                                    data-pump-id="${pump.id}" 
                                                    data-order-detail-id="${product.order_detail_id}">
                                                    <div class="card border border-1 shadow-sm h-100 bg-white" style="cursor: pointer; border-radius: 12px; transition: all 0.2s;">
                                                        <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                                                            <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 60px;">
                                                                <img src="{{ asset('assets/images/nozzle.png') }}" alt="Nozzle" style="height: 50px; object-fit: contain;" onerror="this.outerHTML='<i class=\\'bi bi-fuel-pump-fill fs-1\\' style=\\'color: ${colors.bg};\\'></i>'">
                                                            </div>
                                                            <h6 class="fw-bold mb-1 text-uppercase text-dark" style="font-size: 0.75rem;">${product.name}</h6>
                                                            <h5 class="fw-bold mb-1" style="color: #0d6efd;">S/ ${parseFloat(product.price || 0).toFixed(2)}</h5>
                                                            <small class="fw-semibold" style="color: #198754; font-size: 0.65rem;">Stock: 1,000.00</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            `);

                                            // Efectos hover
                                            productBtn.find('.card').hover(
                                                function() {
                                                    $(this).css({
                                                        'transform': 'translateY(-3px)',
                                                        'box-shadow': '0 8px 20px rgba(0,0,0,0.1)'
                                                    });
                                                },
                                                function() {
                                                    $(this).css({
                                                        'transform': 'translateY(0)',
                                                        'box-shadow': '0 4px 15px rgba(0,0,0,0.05)'
                                                    });
                                                }
                                            );

                                            productBtn.on('click', function() {
                                                // Efecto de click
                                                $(this).css('transform', 'scale(0.95)');
                                                setTimeout(() => {
                                                    $(this).css('transform',
                                                        'scale(1)');
                                                }, 100);

                                                const productId = $(this).data(
                                                    'product-id');
                                                const productName = $(this).data(
                                                    'product-name');
                                                const price = $(this).data('price');
                                                const observations = $(this).data(
                                                    'observations');
                                                const tankId = $(this).data('tank-id');
                                                const orderDetailId = $(this).data(
                                                    'order-detail-id');
                                                const pumpId = $(this).data('pump-id');

                                                // Llenar el modal con los datos del producto
                                                $('#addProductsModal #product_id').val(
                                                    productId);
                                                $('#addProductsModal #tank_id').val(
                                                    tankId || '');
                                                $('#addProductsModal #pump_id').val(
                                                    pumpId || '');
                                                $('#addProductsModal #lbl-name').val(
                                                    productName);
                                                $('#addProductsModal #lbl-price').val(
                                                    price);

                                                // Guardar el precio original
                                                $('#addProductsModal #lbl-price').data(
                                                    'original-price', price);

                                                // Guardar order_detail_id si existe
                                                if (orderDetailId) {
                                                    $('#addProductsModal #product_id')
                                                        .data('order-detail-id',
                                                            orderDetailId);
                                                } else {
                                                    $('#addProductsModal #product_id')
                                                        .removeData('order-detail-id');
                                                }

                                                // Resetear valores
                                                $('#addProductsModal #txt-quantity')
                                                    .val(1);
                                                $('#addProductsModal #txt-subtotal')
                                                    .val(price);
                                                $('#addProductsModal #checkPrecioM')
                                                    .prop('checked', false);
                                                $('#addProductsModal #toggleGalonesSubtotal')
                                                    .prop('checked', false);

                                                // Abrir el modal
                                                $('#addProductsModal').modal('show');
                                            });
                                            $('#quick-add-product').hide();
                                            $('#quick-add-product-subtotal').hide();

                                            pumpCard.find('.products-buttons').append(
                                                productBtn);
                                        }
                                    });
                                }
                            });
                        }

                        if (!found) {
                            pumpCard.find('.products-buttons').append(
                                `<div class="text-center text-muted small py-2">
                                    <i class="bi bi-exclamation-circle"></i> Sin producto
                                </div>`
                            );
                        }

                        return pumpCard;
                    }

                    lado1Pumps.forEach(p => lado1Container.find('.pumps-column').append(crearTarjetaSurtidor(p, data)));
                    lado2Pumps.forEach(p => lado2Container.find('.pumps-column').append(crearTarjetaSurtidor(p, data)));

                    mainContainer.append(lado1Container).append(lado2Container);
                    $('#tbl-products').append(mainContainer);

                    ToastMessage.fire({ text: `${pumpsForIsle.length} surtidores cargados` });
                },
                error: function(err) {
                    console.error('Error al cargar productos:', err);
                    ToastError.fire({ title: 'Error', text: 'No se pudieron cargar los productos' });
                }
            });
        }

        var clientAgreementsData = [];

        function reabrirContratosModal() {
            const clientName = $('#client_name').val() || 'Cliente';
            $('#modal-contract-client-name').text(clientName);
            renderModalAgreementsList();
            const modalEl = document.getElementById('modalSelectContract');
            if (modalEl) {
                let bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                bsModal.show();
            }
        }

        function renderModalAgreementsList() {
            $('#modal-contract-navigation').hide();
            const $tbody = $('#tbl-modal-select-contract').empty();
            const tipoVenta = $('#tipo-venta').val();

            if (!clientAgreementsData || clientAgreementsData.length === 0) {
                $tbody.append(`
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-journal-x fs-3 d-block mb-1"></i>
                            No hay ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} disponibles para este cliente.
                        </td>
                    </tr>
                `);
                return;
            }

            clientAgreementsData.forEach(function(agreement) {
                const fechaFormateada = new Date(agreement.date).toLocaleDateString('es-PE');
                const estadoTexto = agreement.status == 0 ? 'Activo' : 'Inactivo';
                const estadoClass = agreement.status == 0 ? 'bg-success' : 'bg-danger';
                const numContract = agreement.number ? agreement.number : String(agreement.id).padStart(5, '0');

                $tbody.append(`
                    <tr style="cursor: pointer;" onclick="modalCargarProductosContrato(${agreement.id}, '${numContract}')">
                        <td><strong class="text-primary">${tipoVenta === 'contrato' ? 'Contrato' : 'Crédito'} #${numContract}</strong></td>
                        <td>${fechaFormateada}</td>
                        <td><span class="badge ${estadoClass}">${estadoTexto}</span></td>
                        <td class="text-end fw-bold">S/ ${parseFloat(agreement.total).toFixed(2)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i>Ver Órdenes
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function modalCargarProductosContrato(agreementId, numContract) {
            $('#current-agreement-id').val(agreementId);
            const tipoVenta = $('#tipo-venta').val();

            $.ajax({
                url: "{{ route('orders.by.contract') }}",
                method: 'GET',
                data: { agreement_id: agreementId },
                success: function(data) {
                    $('#modal-contract-navigation').show();
                    $('#btn-modal-contract-back').attr('onclick', 'renderModalAgreementsList()');

                    const $tbody = $('#tbl-modal-select-contract').empty();

                    if (data.orders && data.orders.length > 0) {
                        data.orders.forEach(function(order) {
                            const fechaFormateada = new Date(order.date).toLocaleDateString('es-PE');

                            $tbody.append(`
                                <tr style="cursor: pointer;" onclick="modalCargarProductosOrden(${order.id})">
                                    <td><strong class="text-primary">Orden #${order.number}</strong></td>
                                    <td>${fechaFormateada}</td>
                                    <td><span class="badge bg-info text-dark">Disponible</span></td>
                                    <td class="text-end fw-bold">S/ ${parseFloat(order.total).toFixed(2)}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-success">
                                            <i class="bi bi-box-seam me-1"></i>Ver Productos
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        $tbody.append(`<tr><td colspan="5" class="text-center text-muted py-3">No hay órdenes disponibles en este ${tipoVenta}.</td></tr>`);
                    }
                }
            });
        }

        function modalCargarProductosOrden(orderId) {
            cargarProductosOrden(orderId);
            const modalEl = document.getElementById('modalSelectContract');
            if (modalEl) {
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        }

        function cargarContratosCliente(clienteId) {
            const tipoVenta = $('#tipo-venta').val();

            $.ajax({
                url: "{{ route('contracts.by.client') }}",
                method: 'GET',
                data: {
                    client_id: clienteId,
                    type: tipoVenta
                },
                success: function(data) {
                    clientAgreementsData = data || [];
                    $('#tbl-products-contract').empty();

                    if (data.length > 0) {
                        $('#btn-open-contracts-modal').show();
                        // Mostrar lista de contratos/créditos en panel lateral
                        data.forEach(function(agreement) {
                            const fechaFormateada = new Date(agreement.date).toLocaleDateString('es-PE');
                            const estadoTexto = agreement.status == 0 ? 'Activo' : 'Inactivo';
                            const estadoClass = agreement.status == 0 ? 'text-success' : 'text-danger';
                            const numContract = agreement.number ? agreement.number : String(agreement.id).padStart(5, '0');

                            $('#tbl-products-contract').append(`
                            <tr onclick="cargarProductosContrato(${agreement.id})" style="cursor: pointer;" class="table-row-hover">
                                <td>
                                    <strong>${tipoVenta === 'contrato' ? 'Contrato' : 'Crédito'} #${numContract}</strong><br>
                                    <small class="text-muted">Fecha: ${fechaFormateada}</small>
                                </td>
                                <td class="text-end">
                                    <span class="${estadoClass}"><strong>${estadoTexto}</strong></span><br>
                                    <small class="text-muted">Total: S/ ${parseFloat(agreement.total).toFixed(2)}</small>
                                </td>
                            </tr>
                        `);
                        });

                        $('#products-contract-credit').show();
                        $('#products-contract-credit h6').text(tipoVenta === 'contrato' ? 'Contratos del Cliente' : 'Créditos del Cliente');

                        ToastMessage.fire({
                            title: 'Cliente Seleccionado',
                            text: `Se encontraron ${data.length} ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'}`
                        });

                        // Abrir modal espacioso automáticamente
                        reabrirContratosModal();

                    } else {
                        $('#btn-open-contracts-modal').hide();
                        $('#tbl-products-contract').append(
                            `<tr><td colspan="2" class="text-center text-muted">No hay ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} disponibles para este cliente</td></tr>`
                        );
                        $('#products-contract-credit').show();

                        ToastMessage.fire({
                            title: 'Sin datos',
                            text: `Este cliente no tiene ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} activos`
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar contratos del cliente:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: `No se pudieron cargar los ${tipoVenta === 'contrato' ? 'contratos' : 'créditos'} del cliente`
                    });
                }
            });
        }


        function cargarProductosContrato(agreementId) {
            // Guardar el agreement_id actual para poder volver
            $('#current-agreement-id').val(agreementId);

            $.ajax({
                url: "{{ route('orders.by.contract') }}",
                method: 'GET',
                data: {
                    agreement_id: agreementId
                },
                success: function(data) {
                    $('#tbl-products-contract').empty();

                    if (data.orders && data.orders.length > 0) {
                        // Mostrar órdenes del contrato
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaContratos()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de ${$('#tipo-venta').val() === 'contrato' ? 'contratos' : 'créditos'}
                                </button>
                            </td>
                        </tr>
                    `);

                        data.orders.forEach(function(order) {
                            const fechaFormateada = new Date(order.date).toLocaleDateString('es-PE');

                            $('#tbl-products-contract').append(`
                            <tr onclick="cargarProductosOrden(${order.id})" style="cursor: pointer;" class="table-row-hover">
                                <td>
                                    <strong>Orden #${order.number}</strong><br>
                                    <small class="text-muted">Fecha: ${fechaFormateada}</small>
                                </td>
                                <td class="text-end">
                                    <small class="text-muted">Total: S/ ${parseFloat(order.total).toFixed(2)}</small>
                                </td>
                            </tr>
                        `);
                        });

                        // Cambiar título del card
                        $('#products-contract-credit h6').text(
                            `Órdenes del ${$('#tipo-venta').val() === 'contrato' ? 'Contrato' : 'Crédito'} #${agreementId}`
                        );

                        ToastMessage.fire({
                            text: `${data.orders.length} órdenes cargadas del ${$('#tipo-venta').val()}`
                        });

                    } else {
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaContratos()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de ${$('#tipo-venta').val() === 'contrato' ? 'contratos' : 'créditos'}
                                </button>
                            </td>
                        </tr>
                        <tr><td colspan="3" class="text-center text-muted">No hay órdenes disponibles en este ${$('#tipo-venta').val()}</td></tr>
                    `);

                        ToastMessage.fire({
                            text: `No hay órdenes disponibles en este ${$('#tipo-venta').val()}`
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar órdenes del contrato:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar las órdenes del contrato/crédito'
                    });
                }
            });
        }

        function cargarProductosOrden(orderId) {
            $.ajax({
                url: "{{ route('products.by.order', ':orderId') }}".replace(':orderId', orderId),
                method: 'GET',
                data: {
                    order_id: orderId
                },
                success: function(data) {
                    $('#tbl-products-contract').empty();

                    // Llenar campo de orden con el número de la orden
                    if (data.order && data.order.number) {
                        $('#orden').val(data.order.number);
                    }

                    if (data.tanks && data.tanks.length > 0) {
                        // Agregar botón para volver a la lista de órdenes
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaOrdenes()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de órdenes
                                </button>
                            </td>
                        </tr>
                    `);

                        // Agrupar tanques por producto
                        const productosAgrupados = {};

                        // Determinar isla seleccionada (si el usuario tiene una asignada o seleccionó una)
                        const selectedIsleForOrder = assignedIsle || $('#select-isle').val() || null;

                        data.tanks.forEach(function(tank) {
                            const product = tank.product;
                            if (!product) return;

                            if (!productosAgrupados[product.id]) {
                                productosAgrupados[product.id] = {
                                    product: product,
                                    tanks: []
                                };
                            }

                            // Intentar obtener surtidor/isla a partir del arreglo global `pumps` si la respuesta no los incluye
                            var pumpName = '';
                            var pumpId = null;
                            var isleName = '';
                            var isleId = null;

                            try {
                                if (Array.isArray(pumps) && pumps.length > 0) {
                                    // Buscar bombas que sirvan este producto
                                    var candidatePumps = pumps.filter(function(p) {
                                        return parseInt(p.product_id) === parseInt(product
                                            .id) && (p.deleted == 0 || p.deleted === false);
                                    });

                                    // Si hay una isla seleccionada, priorizar bombas de esa isla
                                    if (selectedIsleForOrder) {
                                        var pumpInIsle = candidatePumps.find(function(cp) {
                                            return parseInt(cp.isle_id) === parseInt(
                                                selectedIsleForOrder);
                                        });
                                        if (pumpInIsle) {
                                            candidatePumps = [pumpInIsle];
                                        }
                                    }

                                    if (candidatePumps.length > 0) {
                                        var chosen = candidatePumps[0];
                                        pumpName = chosen.name || chosen.display_name || '';
                                        pumpId = chosen.id || chosen.ID || null;

                                        // Buscar nombre de isla en el arreglo global `isles` si existe
                                        if (Array.isArray(isles) && isles.length > 0) {
                                            var isleObj = isles.find(function(i) {
                                                return parseInt(i.id) === parseInt(chosen
                                                    .isle_id);
                                            });
                                            if (isleObj) {
                                                isleName = isleObj.name || '';
                                                isleId = isleObj.id || null;
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.error('Error buscando pump/isle desde globals:', e);
                            }

                            productosAgrupados[product.id].tanks.push({
                                tank_id: tank.id,
                                tank_name: tank.name,
                                stored_quantity: tank.stored_quantity,
                                isle_name: isleName || tank.isle_name || (tank.isle && tank.isle
                                    .name) || '',
                                isle_id: isleId || tank.isle_id || (tank.isle && tank.isle
                                    .id) || null,
                                pump_name: pumpName || tank.pump_name || (tank.pump && tank.pump
                                    .name) || '',
                                pump_id: pumpId || tank.pump_id || (tank.pump && tank.pump
                                    .id) || null
                            });
                        });

                        // Mostrar productos agrupados con sus tanques usando el estilo de surtidores
                        Object.values(productosAgrupados).forEach(function(grupo) {
                            const product = grupo.product;

                            // (No se muestra cabecera por producto para mantener formato Isla - Surtidor - Producto)

                            // Listar tanques para este producto con dos columnas (nombre + precio)
                            grupo.tanks.forEach(function(tank) {
                                // Obtener nombre de isla y surtidor si vienen en la respuesta (varias formas posibles)
                                var isleName = tank.isle_name || (tank.isle && tank.isle
                                        .name) || tank.isle || tank.island_name || tank
                                    .island ||
                                    '';
                                var pumpName = tank.pump_name || (tank.pump && tank.pump
                                        .name) || tank.pump || tank.surtidor_name || tank
                                    .surtidor || '';

                                var extraLine = '';
                                if (isleName || pumpName) {
                                    extraLine = `<small class="text-muted d-block">` + (
                                        isleName ? `Isla: ${isleName}` : '') + (isleName &&
                                        pumpName ? ' | ' : '') + (pumpName ?
                                        `Surtidor: ${pumpName}` : '') + `</small>`;
                                }

                                const $tankRow = $(`
                                    <tr class="product-row" style="cursor:pointer;">
                                        <td style="padding-left:20px">
                                            <div>
                                                <span class="small text-muted">Isla: ${isleName || '-'}</span>
                                                <span class="small text-muted"> | Surtidor: ${pumpName || '-'}</span>
                                                <strong class="ms-2">${product.name}</strong>
                                            </div>
                                            <div class="small text-muted">Tanque: ${tank.tank_name || '-'} (Stock: ${parseFloat(tank.stored_quantity).toFixed(3)})</div>
                                        </td>
                                        <td align="right">S/ ${parseFloat(product.price).toFixed(2)}</td>
                                    </tr>
                                `);

                                // Guardar datos en el elemento para usar al hacer click
                                // Guardar datos en el elemento para usar al hacer click
                                $tankRow.data('product-id', product.id);
                                $tankRow.data('product-name', product.name);
                                $tankRow.data('price', parseFloat(product.price));
                                $tankRow.data('observations', product.observations || '');
                                $tankRow.data('tank-id', tank.tank_id);
                                // Guardar pump_id en la fila para enviarlo en la venta
                                $tankRow.data('pump-id', tank.pump_id || null);
                                $tankRow.data('area', product.area || '');
                                $tankRow.data('order-detail-id', (function() {
                                    const odId = $tankRow.data('order-detail-id');
                                    // Validar que sea un número válido
                                    if (odId && odId !== null && odId !== '' &&
                                        odId !== 'null') {
                                        const parsed = parseInt(odId);
                                        return (!isNaN(parsed) && parsed > 0) ?
                                            parsed : null;
                                    }
                                    return null;
                                })());

                                $tankRow.on('click', function() {
                                    const prodId = $(this).data('product-id');
                                    const prodName = $(this).data('product-name');
                                    const price = $(this).data('price');
                                    const obs = $(this).data('observations');
                                    const tankId = $(this).data('tank-id');
                                    const area = $(this).data('area');
                                    const orderDetailId = $(this).data(
                                        'order-detail-id');
                                    const pumpId = $(this).data('pump-id');

                                    // Llenar área si existe
                                    if (area) {
                                        $('#area').val(area);
                                    }

                                    // Guardar order_detail_id también en el campo global (compatibilidad)
                                    if (orderDetailId) {
                                        $('#current-order-detail-id').val(
                                            orderDetailId);
                                    } else {
                                        $('#current-order-detail-id').val('');
                                    }

                                    // Llamar a la función de agregar producto pasando el orderDetailId y pumpId
                                    addOrder(prodId, prodName, price, obs, tankId,
                                        orderDetailId, pumpId);
                                });

                                $('#tbl-products-contract').append($tankRow);
                            });
                        });

                        // Cambiar título del card
                        $('#products-contract-credit h6').text(`Productos de la Orden #${data.order.number}`);

                        ToastMessage.fire({
                            text: `${Object.keys(productosAgrupados).length} productos cargados de la orden`
                        });

                    } else {
                        $('#tbl-products-contract').append(`
                        <tr>
                            <td colspan="3" class="text-center bg-light">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="volverListaOrdenes()">
                                    <i class="bi bi-arrow-left"></i> Volver a lista de órdenes
                                </button>
                            </td>
                        </tr>
                        <tr><td colspan="3" class="text-center text-muted">No hay productos disponibles en esta orden</td></tr>
                    `);

                        ToastMessage.fire({
                            text: 'No hay productos disponibles en esta orden'
                        });
                    }
                },
                error: function(err) {
                    console.error('Error al cargar productos de la orden:', err);
                    ToastError.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar los productos de la orden'
                    });
                }
            });
        }

        function volverListaOrdenes() {
            // Aquí necesitamos guardar el agreement_id cuando entramos a cargarProductosContrato
            const agreementId = $('#current-agreement-id').val(); // Lo agregaremos después
            if (agreementId) {
                // Limpiar campo de orden al volver a la lista de órdenes
                $('#orden').val('');
                $('#current-order-detail-id').val(''); // Limpiar order_detail_id
                cargarProductosContrato(agreementId);
            }
        }

        function volverListaContratos() {
            const clienteId = $('#client_id').val();
            if (clienteId) {
                // Limpiar campos de orden y área al volver a la lista
                $('#orden').val('');
                $('#area').val('');
                $('#current-agreement-id').val(''); // Limpiar agreement_id
                $('#current-order-detail-id').val(''); // Limpiar order_detail_id
                cargarContratosCliente(clienteId);
            }
        }

        function isDecimal(evt) {
            evt = evt || window.event;
            var charCode = evt.which || evt.keyCode;
            if ((charCode >= 48 && charCode <= 57) || charCode === 46) {
                var input = evt.target || evt.srcElement;
                if (charCode === 46 && input.value.includes('.')) {
                    evt.preventDefault();
                    return false;
                }
                return true;
            } else {
                evt.preventDefault();
                return false;
            }
        }



        $('#toggleGalonesSubtotal').on('change', function() {
            const galonesSection = $('#galonesSection');
            const subtotalSection = $('#subtotalSection');

            if (this.checked) {
                // Cambiar a modo "Por Galones"
                galonesSection.show();
                subtotalSection.hide();

                // Si hay precio y subtotal, calcular cantidad automáticamente
                const precio = parseFloat($('#lbl-price').val()) || 0;
                const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
                if (precio > 0 && subtotal > 0) {
                    const cantidad = subtotal / precio;
                    $('#txt-quantity').val(cantidad.toFixed(3));
                }
            } else {
                // Cambiar a modo "Por Subtotal"
                galonesSection.hide();
                subtotalSection.show();

                // Si hay precio y cantidad, calcular subtotal automáticamente
                const precio = parseFloat($('#lbl-price').val()) || 0;
                const cantidad = parseFloat($('#txt-quantity').val()) || 1;
                if (precio > 0 && cantidad > 0) {
                    const subtotal = precio * cantidad;
                    $('#txt-subtotal').val(subtotal.toFixed(2));
                }
            }
        });

        // Cargar productos cuando se selecciona una isla manualmente
        $('#btn-load-by-isle').on('click', function() {
            loadProductsBySede();
        });

        $('#select-isle').on('change', function() {
            // Si se cambia la isla manualmente, recargar lista
            loadProductsBySede();
        });

        function clearSwitch() {
            $('#toggleGalonesSubtotal').prop('checked', false);
            $('#switchLabel').text('Por Subtotal');
            $('#galonesSection').hide();
            $('#subtotalSection').show();
        }

        function calcularGalones() {
            const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const quantity = price > 0 ? (subtotal / price) : 0;
            $('#txt-quantity').val(quantity.toFixed(3));
            updateCalculationSummary();
        }
        // Ocultar alerta cuando se cierra el modal
        $('#addProductsModal').on('hidden.bs.modal', function() {
            $('#calculationSummary').addClass('d-none');
            $('#toggleGalonesSubtotal').val('false');

            // También limpiar los valores del resumen
            $('#summary-quantity').text('0.000');
            $('#summary-price').text('S/ 0.00');
            $('#summary-total').text('S/ 0.00');
            $('#galonesSection').hide();
            $('#subtotalSection').show();
            $('#lbl-price').prop('disabled', true);
            $('#lbl-price').addClass('bg-light');
        });

        // Ocultar alerta cuando se abre el modal (resetear)
        $('#addProductsModal').on('show.bs.modal', function() {
            $('#calculationSummary').addClass('d-none');
        });

        function updateCalculationSummary() {
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = parseFloat($('#txt-subtotal').val()) || 0;
            const isSubtotalMode = $('#toggleGalonesSubtotal').prop('checked');

            if (isSubtotalMode) {
                // Modo subtotal
                const calculatedQuantity = price > 0 ? (subtotal / price) : 0;
                $('#summary-quantity').text(calculatedQuantity.toFixed(3));
                $('#summary-price').text('S/ ' + price.toFixed(2));
                $('#summary-total').text('S/ ' + subtotal.toFixed(2));
            } else {
                // Modo galones
                const calculatedSubtotal = (quantity * price).toFixed(2);
                $('#summary-quantity').text(quantity.toFixed(3));
                $('#summary-price').text('S/ ' + price.toFixed(2));
                $('#summary-total').text('S/ ' + calculatedSubtotal);
            }

            // Mostrar el resumen si hay valores
            if (quantity > 0 || subtotal > 0) {
                $('#calculationSummary').removeClass('d-none');

                $('#quick-add-product').show();
                $('#quick-add-product-subtotal').show();
            } else {
                $('#calculationSummary').addClass('d-none');
            }
        }
        // Nueva función para calcular subtotal cuando se cambia la cantidad en modo galones
        function calcularSubtotal() {
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const price = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = (quantity * price).toFixed(2);
            $('#txt-subtotal').val(subtotal);
            updateCalculationSummary();
        }

        $('#checkPrecioM').on('change', function() {
            if (this.checked) {
                $('#lbl-price').prop('disabled', false);
                $('#lbl-price').removeClass('bg-light');
            } else {
                $('#lbl-price').prop('disabled', true);
                $('#lbl-price').addClass('bg-light');
            }
        });

        // Actualizar resumen cuando cambie el toggle
        $('#toggleGalonesSubtotal').on('change', function() {
            updateCalculationSummary();
        });

        // Actualizar resumen cuando cambie el precio
        $('#lbl-price').on('change', function() {
            updateCalculationSummary();
        });

        function searchTable() {
            var area_id = state.area_id;
            var search = $('#search-table').val();
            getTables(area_id, search);
        }

        function addOrder(product_id, name, price, observations, tank_id, order_detail_id, pump_id) {
            $('#quick-add-product').show();
            $('#quick-add-product-subtotal').show();
            $('#product_id').val(product_id);
            // Guardar order_detail_id temporalmente en el campo product_id como data
            if (typeof order_detail_id !== 'undefined' && order_detail_id !== null) {
                $('#product_id').data('order-detail-id', order_detail_id);
            } else {
                // limpiar cualquier valor previo
                $('#product_id').removeData('order-detail-id');
            }
            // Guardar pump_id temporalmente
            if (typeof pump_id !== 'undefined' && pump_id !== null) {
                $('#pump_id').val(pump_id);
            } else {
                $('#pump_id').val('');
            }
            // Guardar el tanque seleccionado (si viene)
            if (typeof tank_id !== 'undefined' && tank_id !== null) {
                $('#tank_id').val(tank_id);
            } else {
                $('#tank_id').val('');
            }
            $('#lbl-name').val(name);
            $('#lbl-price').val(price);

            // Guardar el precio original por sede en un campo oculto
            $('#lbl-price').data('original-price', price);

            $('#txt-quantity').val(1);

            /*Subtotal*/
            const quantity = parseFloat($('#txt-quantity').val()) || 0;
            const unitPrice = parseFloat($('#lbl-price').val()) || 0;
            const subtotal = (quantity * unitPrice).toFixed(2);
            /*Fin de Subtotal*/

            $('#txt-subtotal').val(subtotal);

            $('#txt-note').val('');

            $('#divObservations').empty();
            var obs = observations && observations.length > 0 ? observations.split(',') : [];
            obs.forEach(function(observation) {
                $('#divObservations').append(`
                <div class="form-check form-check-inline">
                    <label><input type="radio" class="form-check-input" name="observation" value="${observation}">${observation}</label>
                </div>
            `);
            });
        }

        function addProductDirect() {
            // Determinar si estamos usando el modal o el formulario rápido
            const isModalOpen = $('#addProductsModal').hasClass('show') || $('#addProductsModal').is(':visible');
            const modalPrefix = isModalOpen ? '#addProductsModal ' : '';

            // Leer valores del formulario activo (modal o formulario rápido)
            var product_id = $(modalPrefix + '#product_id').val();
            var tank_id = $(modalPrefix + '#tank_id').val();
            var pump_id = $(modalPrefix + '#pump_id').val() || '';
            var nombre = $(modalPrefix + '#lbl-name').val();
            var precio = parseFloat($(modalPrefix + '#lbl-price').val()) || 0;
            var nota = $(modalPrefix + '#txt-note').val() || '';
            var subtotalInput = parseFloat($(modalPrefix + '#txt-subtotal').val()) || 0;
            var switchActivo = $(modalPrefix + '#toggleGalonesSubtotal').prop('checked');
            var cantidad, subtotal;

            if (switchActivo) {
                // Modo "Por Galones": cantidad * precio = subtotal
                cantidad = parseFloat($(modalPrefix + '#txt-quantity').val()) || 1;
                // Usar Math.round para evitar problemas de redondeo
                subtotal = Math.round(precio * cantidad * 100) / 100;
                console.log('=== MODO GALONES ===');
                console.log('Precio:', precio);
                console.log('Cantidad:', cantidad);
                console.log('Subtotal calculado:', subtotal);
            } else {
                // Modo "Por Subtotal": usar exactamente el subtotal introducido y calcular cantidad
                subtotal = Math.round(subtotalInput * 100) / 100;
                cantidad = precio > 0 ? (subtotalInput / precio) : 0;
                console.log('=== MODO SUBTOTAL ===');
                console.log('Subtotal input:', subtotalInput);
                console.log('Subtotal redondeado:', subtotal);
                console.log('Cantidad calculada:', cantidad);
            }

            if (!product_id || !precio || cantidad <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Faltan datos del producto o los valores son inválidos.'
                });
                return;
            }

            // Verificar si se usó precio mayorista
            var isMayorista = $(modalPrefix + '#checkPrecioM').is(':checked');

            // Obtener precio original por sede y redondear
            var precioOriginal = Math.round((parseFloat($(modalPrefix + '#lbl-price').data('original-price')) || precio) *
                100) / 100;

            // Redondear el precio actual también
            precio = Math.round(precio * 100) / 100;

            console.log('=== PRECIOS REDONDEADOS ===');
            console.log('Precio Original:', precioOriginal);
            console.log('Precio Actual:', precio);
            console.log('Es Mayorista:', isMayorista);

            // Obtener order_detail_id (del elemento product_id del formulario activo)
            const rowOrderDetailId = $(modalPrefix + '#product_id').data('order-detail-id') || null;

            // Validar order_detail_id
            let validOrderDetailId = null;
            if (rowOrderDetailId && rowOrderDetailId !== null && rowOrderDetailId !== '' && rowOrderDetailId !== 'null') {
                const parsed = parseInt(rowOrderDetailId);
                if (!isNaN(parsed) && parsed > 0) {
                    validOrderDetailId = parsed;
                }
            }

            // Validar tank_id
            let validTankId = null;
            if (tank_id && tank_id !== null && tank_id !== '' && tank_id !== 'null') {
                const parsed = parseInt(tank_id);
                if (!isNaN(parsed) && parsed > 0) {
                    validTankId = parsed;
                }
            }

            // Validar pump_id
            let validPumpId = null;
            if (pump_id && pump_id !== null && pump_id !== '' && pump_id !== 'null') {
                const parsed = parseInt(pump_id);
                if (!isNaN(parsed) && parsed > 0) {
                    validPumpId = parsed;
                }
            }

            // Agregar la fila a la tabla con todos los datos validados
            let row = `
        <tr data-product-id="${product_id}" 
            ${validTankId ? `data-tank-id="${validTankId}"` : ''} 
            ${validPumpId ? `data-pump-id="${validPumpId}"` : ''} 
            ${validOrderDetailId ? `data-order-detail-id="${validOrderDetailId}"` : ''}
            data-is-wholesale="${isMayorista ? 'true' : 'false'}"
            data-original-price="${precioOriginal}"
            data-current-price="${precio}"
            data-subtotal="${subtotal}">
            <td>${nombre}</td>
            <td>S/ ${precio.toFixed(2)}</td>
            <td>${cantidad.toFixed(3)}</td>
            <td>S/ ${subtotal.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-xs" onclick="removeProduct(this)"><i class="bi bi-trash"></i></button></td>
        </tr>
        `;

            $('#tbl-order-items').append(row);

            // Limpiar el formulario del modal
            $('#addProductsModal #product_id').val('');
            $('#addProductsModal #tank_id').val('');
            $('#addProductsModal #pump_id').val('');
            $('#addProductsModal #lbl-name').val('');
            $('#addProductsModal #lbl-price').val('');
            $('#addProductsModal #lbl-price').removeData('original-price');
            $('#addProductsModal #txt-quantity').val(1);
            $('#addProductsModal #txt-subtotal').val('');
            $('#addProductsModal #checkPrecioM').prop('checked', false);
            $('#addProductsModal #toggleGalonesSubtotal').prop('checked', false);
            $('#addProductsModal #product_id').removeData('order-detail-id');

            // Limpiar el formulario rápido
            $('#product_id').val('');
            $('#tank_id').val('');
            $('#pump_id').val('');
            $('#lbl-name').val('');
            $('#lbl-price').val('');
            $('#lbl-price').removeData('original-price');
            $('#txt-quantity').val(1);
            $('#txt-subtotal').val('');
            $('#txt-note').val('');
            $('#divObservations').empty();
            $('#quick-add-product').hide();
            $('#quick-add-product-subtotal').hide();
            $('#product_id').removeData('order-detail-id');

            // Cerrar el modal si estaba abierto
            $('#addProductsModal').modal('hide');

            // Recalcular total
            recalculateTotal();
            clearSwitch();

            ToastMessage.fire({
                text: `${nombre} agregado correctamente`
            });
        }

        function removeProduct(btn) {
            $(btn).closest('tr').remove();
            recalculateTotal();
        }

        function recalculateTotal() {
            console.log('=== RECALCULANDO TOTAL ===');
            let total = 0;
            let itemCount = 0;
            $('#tbl-order-items tr').each(function(index) {
                itemCount++;
                const $row = $(this);
                const $tds = $row.find('td');
                // Leer el subtotal de la columna 4 (índice 3)
                const subtotalText = $tds.eq(3).text().replace('S/', '').replace(/\s/g, '').trim();
                const subtotal = $row.find('.sale-subtotal').length
                    ? parseFloat($row.find('.sale-subtotal').val())
                    : parseFloat(subtotalText);

                console.log(`Producto ${index + 1}: Subtotal=${subtotalText} -> Parseado=${subtotal}`);

                if (!isNaN(subtotal) && subtotal > 0) {
                    total += subtotal;
                    console.log(`  Total acumulado: ${total}`);
                }
            });

            // Redondear correctamente el total usando Math.round
            total = Math.round(total * 100) / 100;
            console.log('Total FINAL redondeado:', total);
            const totalFormatted = total.toFixed(2);
            $('#total').text(totalFormatted);
            $('#charge-total').text(totalFormatted);
            $('#lbl-charge-total').text(totalFormatted);
            $('#lbl-charge-total-pay').text(totalFormatted);

            // Actualizar dinámicamente las filas de métodos de pago
            updateDynamicPaymentAmounts(total);

            $('#difference').val('0.00');
            $('#cash').val('');
            $('#change').val('');
            
            // Actualizar contador de items
            $('#items-count').text(itemCount);
            
            // Mostrar u ocultar el estado vacío
            if (itemCount > 0) {
                $('#empty-cart-state').hide();
            } else {
                $('#empty-cart-state').show();
            }
        }


        var sent = false;

        function resetProductForm() {
            $('#product_id').val('');
            $('#edit_order_id').val('');
            $('#lbl-name').val('');
            $('#edit-lbl-name').val('');
            $('#lbl-price').val('');
            $('#edit-lbl-price').val('');
            $('#txt-quantity').val(1);
            $('#edit-txt-quantity').val('');
            $('#divObservations').empty();
            $('#txt-note').val('');
            $('#edit-txt-note').val('');
            $('#orderModal').modal('show');
            $('#addProductModal').modal('hide');
            $('#editProductModal').modal('hide');
            $('#pump_id').val('');
        }


        function returnTable() {
            confirmOrder(false);
            $('#orderModal').modal('hide');
        }

        $('#btn-charge').click(function() {
            // Recalcular el total antes de abrir el modal
            recalculateTotal();

            const tipoVenta = $('#tipo-venta').val();
            const totalCalculado = $('#total').text();
            $('#lbl-charge-total').text(totalCalculado);
            $('#lbl-charge-total-pay').text(totalCalculado);
            $('#lbl-charge-discount').text('0.00');
            $('#difference').val('0.00');
            $('#amount_1').val(totalCalculado);
            $('#amount_2').val('');
            $('#amount_3').val('');
            $('#cbx_amount_1').prop('checked', true);
            $('#cbx_amount_2').prop('checked', false);
            $('#cbx_amount_3').prop('checked', false);

            // Mostrar/ocultar métodos de pago y 'Paga con' según tipo de venta
            const isCreditSale = $('#is-credit-sale').is(':checked');
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                // Para contrato y crédito ocultar ambas secciones
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }

            $('#orderModal').modal('hide');
            $('#chargeModal').modal('show');
        });

        // Evento para actualizar el total cuando se muestra el modal de carga
        $('#chargeModal').on('show.bs.modal', function() {
            recalculateTotal();

            // Controlar visibilidad de secciones según tipo de venta
            const tipoVenta = $('#tipo-venta').val();
            const isCreditSale = $('#is-credit-sale').is(':checked');
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }
        });

        // Evento para controlar visibilidad en voucherModal
        $('#voucherModal').on('show.bs.modal', function() {
            const tipoVenta = $('#tipo-venta').val();
            const isCreditSale = $('#is-credit-sale').is(':checked');
            setCurrentSaleDate();
            if (tipoVenta === 'directa' && !isCreditSale) {
                $('#payment-methods-section').show();
                $('#paga-con-section').show();
            } else {
                $('#payment-methods-section').hide();
                $('#paga-con-section').hide();
            }
        });

        $('#btn-add-payment-method').click(function() {

            if ($('.divPaymentMethod').length < $('select.payment_method').first().find('option').length - 1) {

                var div = $('.divPaymentMethod').first().clone();

                div.find('input').val('');

                $('.divPaymentMethod').last().after(div);

            }

        });

        $('#btn-delete-payment-method').click(function() {
            if ($('.divPaymentMethod').length > 1) {

                $('.divPaymentMethod').last().remove();

            }
        });

        function isNumber(evt) {
            evt = evt || window.event;
            var charCode = evt.which || evt.keyCode;
            if (charCode < 48 || charCode > 57) {
                evt.preventDefault();
                return false;
            }
            return true;
        }

        function searchAPI(docEl, nameEl, addressEl) {
            const doc = $(docEl).val().trim();

            // Limpiar campos
            $(nameEl).val('');
            $(addressEl).val('');
            $('#client_name').val('');

            // Validar longitud del documento
            if (doc.length !== 8 && doc.length !== 11) {
                ToastError.fire({
                    text: 'El documento debe tener 8 (DNI) o 11 dígitos (RUC).'
                });
                return;
            }

            Swal.showLoading();

            $.ajax({
                url: "{{ url('sunat/consultar') }}?doc=" + doc,
                method: 'GET',
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        const data = response.data;
                        let fullName = '';

                        if (doc.length === 8) {
                            fullName = `${data.nombre} ${data.apellido_paterno} ${data.apellido_materno}`
                                .trim();
                        } else {
                            fullName = data.nombre?.trim() || '';
                        }

                        $(nameEl).val(fullName);
                        $(addressEl).val(data.domicilio?.direccion || '');
                        $('#client_name').val(fullName);
                    } else {
                        ToastError.fire({
                            text: response.message || 'No se encontró información en SUNAT/RENIEC'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    ToastError.fire({
                        text: 'Error al consultar SUNAT/RENIEC'
                    });
                }
            });
        }

        function setCurrentSaleDate() {
            const today = new Date();
            const localDate = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
                .toISOString()
                .split('T')[0];

            $('#sale_date').val(localDate);
        }

        function searchDocumentApi() {
            const doc = $('#document').val().trim();

            $('#client_name').val('');
            $('#address').val('');
            $('#client_id').val('');

            if (!/^\d{8}$|^\d{11}$/.test(doc)) {
                ToastError.fire({
                    text: 'El documento debe tener 8 digitos para DNI o 11 digitos para RUC.'
                });
                return;
            }

            Swal.showLoading();

            $.ajax({
                url: "{{ url('sunat/consultar') }}",
                method: 'GET',
                data: {
                    doc: doc
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        const data = response.data;

                        $('#document').val(data.document || doc);
                        $('#client_name').val(data.name || '');
                        $('#address').val(data.address || '');
                    } else {
                        ToastError.fire({
                            text: response.message || 'No se encontro informacion para ese documento.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    ToastError.fire({
                        text: xhr.responseJSON?.message || 'Error al consultar el documento.'
                    });
                }
            });
        }

        function validateSelectedPaymentMethods(selectElem) {
            var selectedVal = $(selectElem).val();
            if (!selectedVal) return;

            var count = 0;
            $('#dynamic-payment-rows .payment-method-select').each(function() {
                if ($(this).val() === selectedVal) {
                    count++;
                }
            });

            if (count > 1) {
                ToastError.fire({
                    title: 'Método repetido',
                    text: 'Este método de pago ya ha sido seleccionado en otra fila'
                });
                $(selectElem).val('');
                calculateDynamicPaymentTotal();
            }
        }

        function addPaymentRow() {
            var optionsHtml = $('#payment-options-template').html();
            var rowHtml = `
                <div class="row g-2 align-items-center mb-2 dynamic-payment-row">
                    <div class="col-7 col-md-7">
                        <select class="form-select payment-method-select fw-bold border-1 py-2" onchange="validateSelectedPaymentMethods(this)">
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold small">S/</span>
                            <input type="number" step="0.01" min="0" class="form-control text-end fw-bold py-2 payment-method-amount" oninput="calculateDynamicPaymentTotal()" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-1 col-md-1 text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-remove-payment-row" onclick="removePaymentRow(this)" style="width: 32px; height: 32px; padding: 0;" title="Eliminar método">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            var $newRow = $(rowHtml);
            $('#dynamic-payment-rows').append($newRow);

            var usedVals = [];
            $('#dynamic-payment-rows .payment-method-select').each(function() {
                var v = $(this).val();
                if (v) usedVals.push(v);
            });

            var $select = $newRow.find('.payment-method-select');
            $select.find('option').each(function() {
                var val = $(this).val();
                if (val && !usedVals.includes(val)) {
                    $select.val(val);
                    return false;
                }
            });

            updatePaymentRemoveButtons();
            calculateDynamicPaymentTotal();
        }

        function removePaymentRow(btn) {
            $(btn).closest('.dynamic-payment-row').remove();
            updatePaymentRemoveButtons();
            calculateDynamicPaymentTotal();
        }

        function updatePaymentRemoveButtons() {
            var rows = $('#dynamic-payment-rows .dynamic-payment-row');
            if (rows.length <= 1) {
                rows.find('.btn-remove-payment-row').hide();
            } else {
                rows.find('.btn-remove-payment-row').show();
            }
        }

        function calculateDynamicPaymentTotal() {
            var total = parseFloat($('#total').text()) || 0;
            var totalPayments = 0;

            $('#dynamic-payment-rows .dynamic-payment-row').each(function() {
                var amt = parseFloat($(this).find('.payment-method-amount').val()) || 0;
                totalPayments += amt;
            });

            var difference = total - totalPayments;
            $('#difference').val(difference.toFixed(2));

            // Actualizar badge de Saldo Restante
            var $lblSaldo = $('#lbl-saldo-restante');
            if (Math.abs(difference) < 0.01) {
                $lblSaldo.removeClass('bg-danger bg-warning bg-info').addClass('bg-success').text('S/ 0.00 (Completado)');
            } else if (difference > 0) {
                $lblSaldo.removeClass('bg-success bg-info bg-warning').addClass('bg-danger').text('Falta S/ ' + difference.toFixed(2));
            } else {
                var sobrante = Math.abs(difference);
                $lblSaldo.removeClass('bg-success bg-danger bg-warning').addClass('bg-info').text('Vuelto S/ ' + sobrante.toFixed(2));
            }

            if ($('#is-vuelto-adicional').is(':checked')) {
                if (totalPayments > total) {
                    var adicional = totalPayments - total;
                    $('#adicional').val(adicional.toFixed(2));
                } else {
                    $('#adicional').val('0.00');
                }
            }

            calculateChange();
        }

        function updateDynamicPaymentAmounts(total) {
            var rows = $('#dynamic-payment-rows .dynamic-payment-row');
            if (rows.length === 1) {
                rows.find('.payment-method-amount').val(total.toFixed(2));
            }
            calculateDynamicPaymentTotal();
        }

        function calculateDifference(e) {
            calculateDynamicPaymentTotal();
        }

        function calculateDiscount(e) {
            var percentage = isNaN(parseFloat($('#percentage').val())) ? 0 : parseFloat($('#percentage').val());
            var discount = isNaN(parseFloat($('#discount').val())) ? 0 : parseFloat($('#discount').val());
            var total = isNaN(parseFloat($('#lbl-charge-total').text())) ? 0 : parseFloat($('#lbl-charge-total').text());

            if (e.target.id == 'percentage') {
                var discount = (total * percentage) / 100;
                var total_pay = total - discount;
                $('#discount').val(discount.toFixed(2));
            } else if (e.target.id == 'discount') {
                var percentage = (discount / total) * 100;
                var total_pay = total - discount;
                $('#percentage').val(percentage.toFixed(2));
            }

            $('#lbl-charge-discount').text(discount.toFixed(2));
            $('#lbl-charge-total-pay').text(total_pay.toFixed(2));

            calculateDifference();
        }

        function calculateChange(e) {
            var firstPaymentAmount = parseFloat($('#dynamic-payment-rows .payment-method-amount').first().val()) || 0;
            var cash = isNaN(parseFloat($('#cash').val())) ? 0 : parseFloat($('#cash').val());
            var change = cash - firstPaymentAmount;

            if (cash > 0) {
                $('#change').val(change.toFixed(2));
            } else {
                $('#change').val('');
            }
        }

        function resetChargeModal() {
            $('voucher_type').prop('checked', false);
            $('#voucher_type_1').prop('checked', true);
            $('#document').val('');
            $('#name').val('');
            $('#address').val('');

            $('#cbx_credit').prop('checked', false);
            $('#payment_days').val('');
            $('#difference').val('');
            $('#percentage').val('');
            $('#discount').val('');
            $('#cash').val('');
            $('#date').val('');
            $('#change').val('');
            $('#tbl-charge-items').html('');
            $('#lbl-charge-total').text('0.00');
            $('#lbl-charge-discount').text('0.00');
            $('#lbl-charge-total-pay').text('0.00');
            $('#observation').val('');
            $('#is-vuelto-adicional').prop('checked', false);
            $('#vuelto-adicional-section').hide();
            $('#adicional').val('0.00');
            $('#chargeModal').modal('hide');
        }

        function guardarVenta() {
            // Obtener el tipo de venta al inicio de la función
            const tipoVenta = $('#tipo-venta').val();
            const selectedIsleId = $('#select-isle').val() || setDefaultIsle(); 
            const isCreditSale = $('#is-credit-sale').is(':checked');
            const isVueltoAdicional = $('#is-vuelto-adicional').is(':checked');
            let vehiclePlate = $('#vehicle_plate').val();
            const adicional = $('#adicional').val();
            console.log('adicional', adicional);
            if ($('#tbl-order-items tr').length === 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe agregar al menos un producto a la venta'
                });
                return;
            }

            // Validar nombre de cliente para crédito
            if (tipoVenta === 'directa' && isCreditSale) {
                let clientName = $('#client_name').val();
                vehiclePlate = $('#vehicle_plate').val()?.trim() || null;
                if (!clientName || clientName.trim() === '') {
                    ToastError.fire({
                        title: 'Error',
                        text: 'Debe ingresar el nombre del cliente para venta a crédito'
                    });
                    return;
                }
            }

            // Validar formas de pago solo para venta directa
            let totalPayments = 0;
            let paymentMethods = [];

            if (tipoVenta === 'directa' && !isCreditSale) {
                var usedMethods = [];
                var duplicateFound = false;

                // Verificar dinámicamente las filas de métodos de pago
                $('#dynamic-payment-rows .dynamic-payment-row').each(function() {
                    var paymentId = $(this).find('.payment-method-select').val();
                    var amount = parseFloat($(this).find('.payment-method-amount').val()) || 0;

                    if (paymentId && amount > 0) {
                        if (usedMethods.includes(paymentId)) {
                            duplicateFound = true;
                        }
                        usedMethods.push(paymentId);
                        totalPayments += amount;
                        paymentMethods.push({
                            payment_method_id: parseInt(paymentId),
                            amount: amount,
                            adicional: adicional,
                            voucher_type: $('input[name="voucher_type"]:checked').val(),
                            voucher_id: null,
                            number: null
                        });
                    }
                });

                if (duplicateFound) {
                    ToastError.fire({
                        title: 'Método duplicado',
                        text: 'No puede registrar una venta con métodos de pago repetidos'
                    });
                    return;
                }

                // Validaciones solo para venta directa
                if (paymentMethods.length === 0) {
                    ToastError.fire({
                        title: 'Error',
                        text: 'Debe seleccionar al menos un método de pago'
                    });
                    return;
                }

                // Validar que el total de pagos coincida con el total de la venta
                const totalVenta = parseFloat($('#total').text()) || 0;
                const isVueltoAdicionalEnabled = $('#is-vuelto-adicional').is(':checked');

                if (!isVueltoAdicionalEnabled) {
                    // Validación estricta: los pagos deben coincidir exactamente
                    if (Math.abs(totalPayments - totalVenta) > 0.01) {
                        ToastError.fire({
                            title: 'Error',
                            text: 'El total de los pagos no coincide con el total de la venta'
                        });
                        return;
                    }
                } else {
                    // Con vuelto adicional: los pagos deben ser mayor o igual al total
                    if (totalPayments < totalVenta) {
                        ToastError.fire({
                            title: 'Error',
                            text: 'El total de los pagos no puede ser menor al total de la venta'
                        });
                        return;
                    }
                    // Calcular y guardar el vuelto adicional
                    const vueltoAdicionalCalculado = totalPayments - totalVenta;
                    $('#adicional').val(vueltoAdicionalCalculado.toFixed(2));
                }
            }

            // Recopilar datos de los productos
            console.log('=== RECOPILANDO PRODUCTOS PARA ENVIAR ===');
            let products = [];
            $('#tbl-order-items tr').each(function(index) {
                const $row = $(this);
                const isEditableRow = $row.find('.sale-product-select').length > 0;
                const productId = isEditableRow ? $row.find('.sale-product-select').val() : $row.data('product-id');
                const $tds = $row.find('td');

                if (productId) {
                    const tankId = $row.data('tank-id') || null;
                    const precioTexto = isEditableRow ? $row.find('.sale-unit-price').val() : $tds.eq(1).text().replace('S/', '').trim();
                    const cantidadTexto = isEditableRow ? $row.find('.sale-quantity').val() : $tds.eq(2).text().trim();
                    const subtotalTexto = isEditableRow ? $row.find('.sale-subtotal').val() : $tds.eq(3).text().replace('S/', '').trim();
                    const clientName = $('#client_name').val();
                    const cantidad = parseFloat(cantidadTexto);
                    const precioMostrado = parseFloat(precioTexto);
                    const subtotal = parseFloat(subtotalTexto);

                    // Leer el subtotal exacto del data attribute
                    const subtotalExacto = parseFloat($row.data('subtotal')) || subtotal;

                    console.log(`Producto ${index + 1} (ID: ${productId}):`);
                    console.log('  Precio texto:', precioTexto, '-> Parseado:', precioMostrado);
                    console.log('  Cantidad texto:', cantidadTexto, '-> Parseado:', cantidad);
                    console.log('  Subtotal texto:', subtotalTexto, '-> Parseado:', subtotal);
                    console.log('  Subtotal exacto (data):', subtotalExacto);

                    // Validar que los valores son válidos
                    if (isNaN(cantidad) || cantidad <= 0) {
                        console.error('Cantidad inválida para producto:', productId);
                        return;
                    }

                    if (isNaN(precioMostrado) || precioMostrado <= 0) {
                        console.error('Precio inválido para producto:', productId);
                        return;
                    }

                    // Calcular el precio real basado en subtotal/cantidad y redondear
                    const precioReal = cantidad > 0 ? Math.round((subtotal / cantidad) * 100) / 100 :
                        precioMostrado;

                    // Verificar si se usó precio mayorista (guardado en data del row)
                    const isMayorista = $row.data('is-wholesale') === true || $row.data('is-wholesale') === 'true';

                    // Obtener precios originales guardados en data y redondear
                    const precioOriginal = Math.round((parseFloat($row.data('original-price')) || precioMostrado) *
                        100) / 100;
                    const precioActual = Math.round((parseFloat($row.data('current-price')) || precioMostrado) *
                        100) / 100;
                    const vehiclePlate = $('#vehicle_plate').val()?.trim() || null;
                    // Determinar el tipo de venta para el manejo de precios
                    const pumpIdRow = $row.data('pump-id') || null;

                    console.log('  Precio Original (data):', $row.data('original-price'), '-> Redondeado:',
                        precioOriginal);
                    console.log('  Precio Actual (data):', $row.data('current-price'), '-> Redondeado:',
                        precioActual);
                    console.log('  Cantidad final:', cantidad);
                    console.log('  Subtotal que se enviará:', subtotal);

                    // Debug log
                    console.log('Producto:', productId, '| Pump ID:', pumpIdRow, '| Tank ID:', tankId,
                        '| Tipo venta:', tipoVenta);

                    if (tipoVenta === 'contrato' || isCreditSale) {
                        // Para contratos y créditos: unit_price = precio por sede (BD), discounted_price = precio mostrado/cobrado
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio por sede de la BD
                            discounted_price: precioActual, // Precio que se está cobrando
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: false,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    } else if (isMayorista) {
                        // Mayorista: unit_price = precio original por sede, discounted_price = precio modificado
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio original por sede
                            discounted_price: precioActual, // Precio modificado por usuario
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: true,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    } else {
                        // Venta directa normal: unit_price = precio tablero (por sede).
                        // discounted_price solo se envía si el precio cobrado realmente
                        // difiere del de catálogo (ej. el cajero lo editó a mano), para
                        // poder distinguir "Precio Tablero" vs "Precio Vendido" en el detalle.
                        const huboAjusteManual = Math.abs(precioActual - precioOriginal) > 0.009;
                        products.push({
                            product_id: productId,
                            quantity: cantidad,
                            unit_price: precioOriginal, // Precio tablero (por sede)
                            discounted_price: huboAjusteManual ? precioActual : null, // Precio vendido, si difiere
                            subtotal: subtotalExacto, // Enviar subtotal exacto
                            vehicle_plate: vehiclePlate,
                            is_wholesale: false,
                            tank_id: tankId,
                            order_detail_id: (function() {
                                const odId = $row.data('order-detail-id');
                                // Validar que sea un número válido
                                if (odId && odId !== null && odId !== '' && odId !== 'null') {
                                    const parsed = parseInt(odId);
                                    return (!isNaN(parsed) && parsed > 0) ? parsed : null;
                                }
                                return null;
                            })(),
                            pump_id: pumpIdRow
                        });
                    }
                }
            });

            // Después de la línea 2241, antes de preparar saleData, agregar:

            // Limpiar y validar order_detail_id en los productos
            products = products.map(p => {
                // Limpiar order_detail_id: solo incluir si es un número válido
                if (p.order_detail_id) {
                    const orderDetailId = parseInt(p.order_detail_id);
                    // Si es un número válido y mayor a 0, incluirlo, sino eliminarlo
                    if (isNaN(orderDetailId) || orderDetailId <= 0) {
                        delete p.order_detail_id;
                    } else {
                        p.order_detail_id = orderDetailId;
                    }
                } else {
                    // Si es null, undefined, string vacío, o "null", eliminarlo
                    delete p.order_detail_id;
                }

                // Limpiar otros campos opcionales de la misma manera
                if (p.tank_id) {
                    const tankId = parseInt(p.tank_id);
                    if (isNaN(tankId) || tankId <= 0) {
                        delete p.tank_id;
                    } else {
                        p.tank_id = tankId;
                    }
                } else {
                    delete p.tank_id;
                }

                if (p.pump_id) {
                    const pumpId = parseInt(p.pump_id);
                    if (isNaN(pumpId) || pumpId <= 0) {
                        delete p.pump_id;
                    } else {
                        p.pump_id = pumpId;
                    }
                } else {
                    delete p.pump_id;
                }

                // Asegurar que los campos requeridos sean números
                p.product_id = parseInt(p.product_id);
                p.quantity = parseFloat(p.quantity);
                p.unit_price = parseFloat(p.unit_price);

                // Limpiar discounted_price
                if (p.discounted_price && p.discounted_price !== null && p.discounted_price !== '') {
                    p.discounted_price = parseFloat(p.discounted_price);
                } else {
                    delete p.discounted_price;
                }

                // Limpiar vehicle_plate
                if (p.vehicle_plate && p.vehicle_plate.trim() !== '') {
                    p.vehicle_plate = p.vehicle_plate.trim();
                } else {
                    delete p.vehicle_plate;
                }

                return p;
            });

            // Determinar pump_id a nivel venta (usar el primer pump_id no nulo que encontremos)
            let salePumpId = null;
            $('#tbl-order-items tr').each(function() {
                const pid = $(this).data('pump-id');
                if (pid) {
                    salePumpId = pid;
                    return false; // break
                }
            });

            // Usar el tipo de venta ya obtenido arriba
            let clientId = null;
            let clientName = null;
            let orderDetailId = null;

            if (tipoVenta === 'contrato') {
                clientId = $('#client_id').val();
                clientName = $('#client_name').val() || $('#search-client').val();

                // Obtener el order_detail_id guardado cuando se seleccionó un producto
                orderDetailId = $('#current-order-detail-id').val() || null;
            } else {
                // Para venta directa, tomar datos del formulario
                clientName = $('#client_name').val();
            }

            // Determinar el tipo de venta final
            let typeSaleValue = $('#type_sale').val();
            // Si es venta directa con checkbox de crédito marcado, cambiar a 2
            if (tipoVenta === 'directa' && isCreditSale) {
                typeSaleValue = '2';
            }

            // Calcular vuelto adicional solo si es mayor a 0
            var adicionalValue = null;
            if ($('#is-vuelto-adicional').is(':checked')) {
                var adicionalInput = parseFloat($('#adicional').val()) || 0;
                if (adicionalInput > 0) {
                    adicionalValue = adicionalInput;
                }
            }

            // Preparar datos para enviar
            const saleData = {
                isle_id: selectedIsleId,
                client_id: clientId,
                client: clientName,
                client_name: clientName,
                phone: null, // Agregar campo de teléfono si es necesario
                vehicle_plate: vehiclePlate,
                order_detail_id: orderDetailId,
                pump_id: salePumpId,
                type_sale: typeSaleValue, // Tipo de venta: 0=directa, 1=contrato, 2=crédito
                products: products,
                payment_methods: isCreditSale ? [] : paymentMethods, // Array vacío si es crédito
                voucher_type: $('input[name="voucher_type"]:checked').val(),
                voucher_number: $('#number').val(), // Número de comprobante para payments
                credit_number: $('#credit_number').val() && $('#credit_number').val().trim() !== '' ? parseInt($('#credit_number').val()) : null, // Número de crédito (solo para ventas a crédito)
                voucher_code: isCreditSale ? $('#voucher_code').val() : null,
                responsible_id: isCreditSale ? $('#responsible_id').val() : null,
                detail: isCreditSale ? $('#detail').val() : null,
                date: $('#sale_date').val(),
                document: $('#document').val(),
                address: $('#address').val(),
                orden: $('#orden').val(),
                placa: $('#placa').val(),
                user_id: $('#user_id').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Solo agregar adicional si es mayor a 0
            if (adicionalValue !== null && adicionalValue > 0) {
                saleData.adicional = adicionalValue;
            }

            // Después de la línea 2286 (después de preparar saleData), agregar:

            // Validar que hay productos
            if (!products || products.length === 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe agregar al menos un producto a la venta'
                });
                return;
            }

            // Validar métodos de pago para venta directa
            const typeSale = saleData.type_sale;
            if (typeSale == 0 && (!paymentMethods || paymentMethods.length === 0)) {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar al menos un método de pago para venta directa'
                });
                return;
            }

            // Actualizar saleData con los productos limpios
            saleData.products = products;

            // Mostrar loading
            /* Swal.fire({
                title: 'Guardando venta...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }); */

            // Enviar datos al servidor
            const $saveButton = $('#btn-save');
            $saveButton.prop('disabled', true);
            $('#spinner-save').show();
            $.ajax({
                url: "{{ route('sales.store') }}",
                method: 'POST',
                data: saleData,
                success: function(response) {
                    // Swal.close();

                    if (response.status) {
                        // Mostrar toast de éxito
                        ToastMessage.fire({
                            text: response.message || 'Venta guardada correctamente'
                        });

                        $('#voucherModal').modal('hide');
                        $("#spinner-save").hide();
                        // Limpiar formulario
                        limpiarFormulario();

                        // Si había productos de contrato/orden cargados, recargarlos para actualizar stock
                        const tipoVentaActual = $('#tipo-venta').val();
                        if (tipoVentaActual === 'contrato') {
                            const currentAgreementId = $('#current-agreement-id').val();
                            if (currentAgreementId) {
                                setTimeout(() => {
                                    cargarProductosContrato(currentAgreementId);
                                }, 500); // Pequeño delay para que se complete la limpieza
                            }
                        }
                    } else {
                        AppError.handle({status: 422, responseJSON: response}, {context: 'registrar la venta'});
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    AppError.handle(xhr, {context: 'registrar la venta'});
                },
                complete: function() {
                    $saveButton.prop('disabled', false);
                    $('#spinner-save').hide();
                }
            });
        }

        function limpiarFormulario() {
            // Limpiar tabla de productos
            $('#tbl-order-items').empty();

            // Resetear totales
            $('#total').text('0.00');
            $('#charge-total').text('0.00');
            $('#lbl-charge-total').text('0.00');
            $('#lbl-charge-total-pay').text('0.00');

            // Limpiar campos de cliente
            $('#document').val('');
            $('#client_name').val('');
            $('#vehicle_plate').val('');
            $('#address').val('');
            $('#orden').val('');
            $('#area').val('');
            $('#number').val('');
            setCurrentSaleDate();
            $('#current-order-detail-id').val('');
            $('#current-agreement-id').val('');
            $('#current-order-id').val('');
            $('#search-client').val('');
            $('#client_id').val('');
            $('#vehicle_plate').val('');
            $('#credit_number').val('');
            setCurrentSaleDate();

            // Limpiar formulario rápido
            $('#product_id').val('');
            $('#tank_id').val('');
            $('#pump_id').val('');
            $('#lbl-name').val('');
            $('#lbl-price').val('');
            $('#lbl-price').removeData('original-price');
            $('#txt-quantity').val(1);
            $('#txt-subtotal').val('');
            $('#txt-note').val('');
            $('#divObservations').empty();
            $('#product_id').removeData('order-detail-id');
            $('#quick-add-product').hide();
            $('#quick-add-product-subtotal').hide();

            // Limpiar modal de agregar productos
            $('#addProductsModal #product_id').val('');
            $('#addProductsModal #tank_id').val('');
            $('#addProductsModal #pump_id').val('');
            $('#addProductsModal #lbl-name').val('');
            $('#addProductsModal #lbl-price').val('');
            $('#addProductsModal #lbl-price').removeData('original-price');
            $('#addProductsModal #txt-quantity').val(1);
            $('#addProductsModal #txt-subtotal').val('');
            $('#addProductsModal #checkPrecioM').prop('checked', false);
            $('#addProductsModal #toggleGalonesSubtotal').prop('checked', false);
            $('#addProductsModal #product_id').removeData('order-detail-id');
            $('#addProductsModal').modal('hide');

            // Resetear tipo de venta a directa
            $('#type-sale').val('directa');
            $('.credit-extra-fields').hide();
            $('#credit_number').val('');   
            // Ocultar sección de crédito y desmarcar checkbox
            $('#is-credit-sale').prop('checked', false);
            $('#credit-number-section').hide();
            $('#payment-methods-section').show();
            $('#paga-con-section').show();

            // Resetear formas de pago dinámicas
            $('#dynamic-payment-rows').empty();
            addPaymentRow();

            // Limpiar diferencia y cambio
            $('#difference').val('0.00');
            $('#cash').val('');
            $('#change').val('');

            // Resetear comprobante
            $('input[name="voucher_type"]').prop('checked', false);
            $('#voucher_type_1').prop('checked', true);

            if ($('#tipo-venta').val() === 'directa') {
                $('#btn-add-editable-product-row').show();
                setDefaultIsle();
                loadProductsBySede();
            }
        }
    </script>

    <script>
        // Location actual del usuario (se utiliza para consultar el monto calculado y el registro del día)
        const currentLocationId = {{ auth()->user()->location_id ?? 'null' }};

    $('#finalCashModal').on('show.bs.modal', function() {
        // Resetear selector
        $('#select-isle-final').val('');
        
        // Resetear inputs a 0.00
        $('#initial_cash_amount_final').val('0.00');
        $('#cash_sales_amount').val('0.00');
        $('#expenses_amount').val('0.00');
        $('#loans_granted_amount').val('0.00');
        $('#loans_recovered_amount').val('0.00');
        $('#adicional_amount').val('0.00');
        $('#real_cash_amount').val('0.00');
        $('#final_cash_amount').val(''); // Campo vacío para que escriban
        
        // Borrar ID guardado
        $(this).data('cash-close-id', null);
    });

    // 2. Al SELECCIONAR LA ISLA: Cargar datos desde el controlador
    $('#select-isle-final').on('change', function() {
        const isleId = $(this).val();

        // Si selecciona la opción por defecto ("-- Seleccione --"), limpiar y salir
        if (!isleId) {
            $('#real_cash_amount').val('0.00');
            $('#initial_cash_amount_final').val('0.00');
            return;
        }

        // Indicador de carga
        $('#real_cash_amount').val('Calculando...');

        $.ajax({
            url: "{{ url('cash_closes') }}" + '/' + isleId,
            method: 'GET',
            success: function(resp) {
                console.log('Datos Cierre recibidos:', resp);

                if (resp && resp.status) {
                    
                    // --- MAPEO DE VARIABLES PHP A INPUTS HTML ---

                    // 1. Monto Inicial (initial_cash_amount)
                    $('#initial_cash_amount_final').val(parseFloat(resp.initial_cash_amount || 0).toFixed(2));

                    // 2. Ventas Efectivo (cash_sales)
                    $('#cash_sales_amount').val(parseFloat(resp.cash_sales || 0).toFixed(2));

                    // 3. Egresos (cash_expenses)
                    $('#expenses_amount').val(parseFloat(resp.cash_expenses || 0).toFixed(2));

                    // 4. Prestamos otorgados y recuperados en efectivo
                    $('#loans_granted_amount').val(parseFloat(resp.cash_loans_granted || 0).toFixed(2));
                    $('#loans_recovered_amount').val(parseFloat(resp.cash_loans_recovered || 0).toFixed(2));

                    // 5. Adicional/Vuelto (total_adicional)
                    $('#adicional_amount').val(parseFloat(resp.total_adicional || 0).toFixed(2));

                    // 6. Saldo Real del Sistema (calculated_cash_amount) -> Viene de tabla isles
                    $('#real_cash_amount').val(parseFloat(resp.calculated_cash_amount || 0).toFixed(2));

                    // 7. ID del Registro de Cierre (cash_close.id)
                    // Necesario para hacer el UPDATE al guardar
                    if (resp.cash_close && resp.cash_close.id) {
                        $('#finalCashModal').data('cash-close-id', resp.cash_close.id);
                    } else {
                        $('#finalCashModal').data('cash-close-id', null);
                        // Opcional: Mostrar alerta si no se abrió caja
                        ToastError.fire({ text: 'Advertencia: No se encontró apertura de caja hoy para esta isla.' });
                    }

                } else {
                    $('#real_cash_amount').val('0.00');
                    ToastError.fire({ text: resp.message || 'Error al obtener datos.' });
                }
            },
            error: function(xhr, status, error) {
            // 1. Imprimir todo el error en la consola del navegador (F12)
            console.error("--- DETALLES DEL ERROR ---");
            console.error("Estado:", status);
            console.error("Error:", error);
            console.error("Respuesta Servidor:", xhr.responseText);

            // 2. Intentar capturar el mensaje específico
            let mensajeError = 'Error desconocido en el servidor.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                // Caso: Laravel devolvió un JSON con error controlado
                mensajeError = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                // Caso: JSON con campo 'error'
                mensajeError = xhr.responseJSON.error;
            } else if (xhr.responseText) {
                // Caso: Error fatal de PHP (pantalla roja de Laravel en texto)
                // Intentamos extraer una parte pequeña para no llenar la pantalla
                mensajeError = 'Error Fatal (Ver Consola): ' + xhr.statusText;
            }

            // 3. Mostrar el error REAL en la alerta
            $('#real_cash_amount').val('0.00');
            ToastError.fire({ 
                text: 'Error: ' + mensajeError 
            });
        }
        });
    });

    // 3. Al dar clic en GUARDAR (Procesar Cierre)
    $('#btn-save-final').on('click', function() {
        
        // Recuperar el ID que guardamos en el paso anterior
        const cashCloseId = $('#finalCashModal').data('cash-close-id');
        
        // Recuperar montos
        const finalAmount = parseFloat($('#final_cash_amount').val()); // Lo que el usuario cuenta
        const realAmount = parseFloat($('#real_cash_amount').val()) || 0; // Lo que dice el sistema

        // Validaciones
        if (!cashCloseId) {
            ToastError.fire({ text: 'No hay una caja abierta válida para cerrar.' });
            return;
        }

        if (isNaN(finalAmount) || finalAmount < 0) {
            ToastError.fire({ text: 'Ingrese el Monto Final (dinero físico contado).' });
            return;
        }

        // Deshabilitar botón para evitar doble click
        const $btn = $(this);
        $btn.prop('disabled', true);

        // Enviar Petición PUT
        $.ajax({
            url: "{{ url('cash_closes') }}" + '/' + cashCloseId,
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: {
                final_cash_amount: finalAmount, // Dinero físico
                real_cash_amount: realAmount    // Dinero sistema
            },
            success: function(resp) {
                if (resp && resp.status) {
                    ToastMessage.fire({ text: resp.message });
                    $('#finalCashModal').modal('hide');
                    // Recargar la página para ver el cierre en la tabla (opcional)
                    setTimeout(() => location.reload(), 1000);
                } else {
                    ToastError.fire({ text: resp.message || 'Error al cerrar caja.' });
                }
            },
            error: function(xhr) {
                console.error(xhr);
                ToastError.fire({ 
                    text: xhr.responseJSON?.message || 'Error al procesar el cierre.' 
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

        // Scripts para manejo de apertura y cierre de caja
        $('#btn-save-initial').on('click', function() {
            const initialCashAmount = parseFloat($('#initial_cash_amount').val()) || 0;
            const isleId = $('#select-isle-initial').val();

            if (initialCashAmount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto inicial debe ser mayor a cero.'
                });
                return;
            }

            if (!isleId || isleId === '') {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una isla.'
                });
                return;
            }

            $.ajax({
                url: "{{ route('cash_closes.store') }}",
                method: 'POST',
                data: {
                    initial_cash_amount: initialCashAmount,
                    isle_id: isleId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        ToastMessage.fire({
                            text: response.message
                        });
                        $('#initialCashModal').modal('hide');
                        $('#initial_cash_amount').val('');
                        $('#select-isle-initial').val('');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Mensaje por defecto
                    let msg = 'Error al guardar la apertura de caja.';

                    // Si la respuesta ya es JSON y contiene 'message' o 'errors'
                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            try {
                                msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                            } catch (e) {
                                msg = JSON.stringify(xhr.responseJSON.errors);
                            }
                        }
                    } else if (xhr && xhr.responseText) {
                        // Intentar parsear responseText si no se detectó responseJSON
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            if (parsed && parsed.message) msg = parsed.message;
                        } catch (e) {
                            // No es JSON: usar statusText o el error provisto
                            msg = xhr.statusText || error || msg;
                        }
                    } else if (error) {
                        msg = error;
                    }

                    ToastError.fire({
                        title: 'Error',
                        text: msg
                    });

                    console.error('Error saving expense:', xhr, status, error);
                }
            });
        });
        $('#select-isle-expense').on('change', function() {
            const isleId = $(this).val();
            if (isleId) {
                $.ajax({
                    url: "{{ url('cash_closes') }}" + '/' + isleId,
                    method: 'GET',
                    success: function(resp) {
                        if (resp && resp.status) {
                            $('#cash_amount').val(parseFloat(resp.calculated_cash_amount || 0).toFixed(
                                2));
                        } else {
                            $('#cash_amount').val('0.00');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al obtener cierre:', xhr);
                        $('#cash_amount').val('0.00');
                        ToastError.fire({
                            text: 'Error al obtener información de caja.'
                        });
                    }
                });
            } else {
                $('#cash_amount').val('0.00');
                ToastError.fire({
                    text: 'Debe seleccionar una isla.'
                });
            }
        });

        // Limpiar campos cuando se cierra el modal
        $('#expenseModal').on('hidden.bs.modal', function() {
            $('#select-isle-expense').val('');
            $('#cash_amount').val('0.00');
            $('#expense_amount').val('');
            $('#expense_description').val('');
            $('#expense_category').val('');
            $('#expense_payment_method').val('Efectivo');
            $('#expense_observation').val('');
        });

        $('#expenseModal').on('show.bs.modal', function() {
            // Resetear al abrir el modal
            $('#select-isle-expense').val('');
            $('#cash_amount').val('0.00');
            $('#expense_amount').val('');
            $('#expense_description').val('');
            $('#expense_category').val('');
            $('#expense_payment_method').val('Efectivo');
            $('#expense_observation').val('');
        });

        $('#btn-save-expenses').on('click', function() {
            const isleId = $('#select-isle-expense').val();
            const description = $('#expense_description').val().trim();
            const category = $('#expense_category').val().trim();
            const payment_method = $('#expense_payment_method').val();
            const observation = $('#expense_observation').val().trim();
            const amount = parseFloat($('#expense_amount').val()) || 0;

            // Validar que se haya seleccionado una isla
            if (!isleId || isleId === '') {
                ToastError.fire({
                    title: 'Error',
                    text: 'Debe seleccionar una isla.'
                });
                return;
            }

            // Solo validar monto (descripción es opcional)
            if (amount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto debe ser mayor a cero.'
                });
                return;
            }

            // Deshabilitar botón para evitar doble envío
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('expenses.store') }}",
                method: 'POST',
                data: {
                    description: description || null, // Enviar null si está vacío
                    category: category || null,
                    payment_method: payment_method || null,
                    observation: observation || null,
                    amount: amount,
                    isle_id: isleId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        ToastMessage.fire({
                            text: response.message
                        });

                        // Recargar el monto desde el servidor para obtener el valor actualizado
                        const isleId = $('#select-isle-expense').val();
                        if (isleId) {
                            $.ajax({
                                url: "{{ url('cash_closes') }}" + '/' + isleId,
                                method: 'GET',
                                success: function(resp) {
                                    if (resp && resp.status) {
                                        $('#cash_amount').val(parseFloat(resp
                                            .calculated_cash_amount || 0).toFixed(
                                            2));
                                    }
                                },
                                error: function() {
                                    // Si falla, simplemente restar localmente
                                    const currentAmount = parseFloat($('#cash_amount')
                                        .val()) || 0;
                                    const newAmount = Math.max(0, currentAmount - amount);
                                    $('#cash_amount').val(newAmount.toFixed(2));
                                }
                            });
                        }

                        $('#expense_description').val('');
                        $('#expense_category').val('');
                        $('#expense_payment_method').val('Efectivo');
                        $('#expense_observation').val('');
                        $('#expense_amount').val('');
                        $('#expenseModal').modal('hide');
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.responseJSON?.message || 'Error al procesar la solicitud';
                    ToastError.fire({
                        title: 'Error',
                        text: errorMessage
                    });
                    console.error('Error:', error, xhr);
                },
                complete: function() {
                    // Rehabilitar botón
                    $btn.prop('disabled', false);
                }
            });
        });

        // ==========================================
    // LÓGICA CORREGIDA PARA BÓVEDA (VAULT)
    // ==========================================
    
    // 1. Al abrir el modal, limpiar campos
        $('#vaultModal').on('show.bs.modal', function() {
            $('#select-isle-vault').val(''); // Resetear select
            $('#cash_amount_acumulated').val('0.00'); // Resetear monto visual
            $('#vault_amount').val(''); // Resetear input de monto
        });

        // 2. Al cambiar la isla, traer el saldo de la BD
        $('#select-isle-vault').on('change', function() {
            const isleId = $(this).val();

            if (!isleId) {
                $('#cash_amount_acumulated').val('0.00');
                return;
            }

            // Indicador de carga
            $('#cash_amount_acumulated').val('Cargando...');

            $.ajax({
                url: "{{ url('cash_closes') }}" + '/' + isleId,
                method: 'GET',
                success: function(resp) {
                    if (resp && resp.status) {
                        // Usamos calculated_cash_amount que viene directo de la tabla isles->cash_amount
                        const saldo = parseFloat(resp.calculated_cash_amount || 0);
                        $('#cash_amount_acumulated').val(saldo.toFixed(2));
                    } else {
                        $('#cash_amount_acumulated').val('0.00');
                        ToastError.fire({
                            text: resp.message || 'No se pudo obtener info de caja.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al obtener cierre:', xhr);
                    $('#cash_amount_acumulated').val('0.00');
                    ToastError.fire({
                        text: 'Error al obtener información de caja.'
                    });
                }
            });
        });

        $('#btn-save-vault').on('click', function() {
            const amount = parseFloat($('#vault_amount').val()) || 0;
            // Obtenemos el saldo acumulado que mostramos en pantalla
            const amount_vault = parseFloat($('#cash_amount_acumulated').val()) || 0;
            const isleId = $('#select-isle-vault').val(); // Obtenemos la isla seleccionada
            const $btn = $(this);

            if (!isleId) {
                ToastError.fire({ title: 'Error', text: 'Debe seleccionar una isla.' });
                return;
            }

            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('vault.from_cash_close') }}", 
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    amount: amount,
                    isle_id: isleId 
                },
                success: function(resp) {
                    if (resp && resp.success) {
                        ToastMessage.fire({ text: resp.message || 'Enviado a bóveda correctamente.' });
                        const newCash = amount_vault - amount;
                        $('#cash_amount_acumulated').val(newCash.toFixed(2));
                        
                        $('#vaultModal').modal('hide');
                    } else {
                        ToastError.fire({ title: 'Error', text: resp.message || 'Error al guardar.' });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    ToastError.fire({ title: 'Error', text: 'Error al procesar la solicitud.' });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        
        $('#btn-save-vault').on('click', function() {
            const amount = parseFloat($('#vault_amount').val()) || 0;
            const amount_vault = parseFloat($('#cash_amount_acumulated').val()) || 0;
            const $btn = $(this);

            if (amount <= 0) {
                ToastError.fire({
                    title: 'Error',
                    text: 'El monto debe ser mayor a cero.'
                });
                return;
            }

            // Deshabilitar el botón para evitar dobles envíos
            $btn.prop('disabled', true);
        });

        // ==================== FUNCIONALIDAD DE MEDICIONES DE CONTÓMETRO ====================

        // Cargar islas cuando se abre el modal
        $('#finalMeasurementModal').on('show.bs.modal', function() {
            // Limpiar campos
            $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
            $('#pump_side').val('');
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');

            // Cargar islas de la sede del usuario
            $.ajax({
                url: "{{ route('sales.measurements.isles') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success && response.isles) {
                        $('#select-isle-measurement').html(
                            '<option value="">Seleccione una isla</option>');
                        response.isles.forEach(function(isle) {
                            $('#select-isle-measurement').append(
                                `<option value="${isle.id}">${isle.name}</option>`
                            );
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar islas:', xhr);
                    ToastError.fire({
                        text: 'Error al cargar las islas'
                    });
                }
            });
        });

        // Cuando se selecciona una isla, cargar sus surtidores
        $('#select-isle-measurement').on('change', function() {
            const isleId = $(this).val();

            // Limpiar campos
            $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
            $('#pump_side').val('');
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');

            if (!isleId) return;

            // Cargar surtidores de la isla seleccionada
            $.ajax({
                url: "{{ route('sales.measurements.pumps') }}",
                method: 'GET',
                data: {
                    isle_id: isleId
                },
                success: function(response) {
                    if (response.success && response.pumps) {
                        response.pumps.forEach(function(pump) {
                            const sideName = pump.side == 1 ? 'Lado 1' : (pump.side == 2 ?
                                'Lado 2' : 'N/A');
                            const productName = pump.product ? pump.product.name :
                                'Sin producto';
                            $('#select-pump-measurement').append(
                                `<option value="${pump.id}" data-side="${sideName}">${pump.name} - ${sideName} (${productName})</option>`
                            );
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar surtidores:', xhr);
                    ToastError.fire({
                        text: 'Error al cargar los surtidores'
                    });
                }
            });
        });

        // Cuando se selecciona un surtidor, obtener su última medición y calcular el teórico
        $('#select-pump-measurement').on('change', function() {
            const pumpId = $(this).val();
            const sideName = $(this).find('option:selected').data('side');

            // Mostrar lado
            $('#pump_side').val(sideName || '');

            // Limpiar valores
            $('#initial_measurement_value').val('');
            $('#final_measurement_value').val('');
            $('#theorical_measurement_value').val('');
            $('#difference_measurement_value').val('');

            // Deshabilitar botón de guardar por defecto
            $('#btn-save-measurement').prop('disabled', true);

            if (!pumpId) return;

            // Obtener última medición
            $.ajax({
                url: "{{ route('sales.measurements.last') }}",
                method: 'GET',
                data: {
                    pump_id: pumpId
                },
                success: function(response) {
                    if (response.success) {
                        // Verificar si ya existe una medición hoy
                        if (response.has_today_measurement) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Medición ya registrada',
                                text: 'Ya existe una medición para este surtidor el día de hoy. Solo se permite una medición diaria por surtidor.',
                                confirmButtonText: 'Entendido'
                            });

                            // Limpiar selector
                            $('#select-pump-measurement').val('');
                            $('#pump_side').val('');
                            return;
                        }

                        // Si no hay medición hoy, cargar el valor inicial (del día anterior o 0)
                        const lastValue = response.measurement ?
                            parseFloat(response.measurement.amount_final || 0).toFixed(3) :
                            '0.000';
                        $('#initial_measurement_value').val(lastValue);

                        // Habilitar botón de guardar
                        $('#btn-save-measurement').prop('disabled', false);

                        // Obtener valor teórico
                        obtenerValorTeorico(pumpId, response.measurement ? response.measurement.date :
                            null);
                    } else {
                        ToastError.fire({
                            text: response.message || 'Error al obtener última medición'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al obtener última medición:', xhr);
                    const errorMessage = xhr.responseJSON?.message ||
                        'Error al obtener última medición';
                    ToastError.fire({
                        text: errorMessage
                    });
                    $('#initial_measurement_value').val('0.000');
                    $('#btn-save-measurement').prop('disabled', false);
                    obtenerValorTeorico(pumpId, null);
                }
            });
        });

        // Función para obtener el valor teórico
        function obtenerValorTeorico(pumpId, startDate) {
            $.ajax({
                url: "{{ route('sales.measurements.theoretical') }}",
                method: 'GET',
                data: {
                    pump_id: pumpId,
                    start_date: startDate
                },
                success: function(response) {
                    if (response.success) {
                        $('#theorical_measurement_value').val(
                            parseFloat(response.total_sold || 0).toFixed(3)
                        );
                        // Calcular diferencia al cargar el valor teórico
                        calcularDiferenciaMedicion();
                    }
                },
                error: function(xhr) {
                    console.error('Error al calcular valor teórico:', xhr);
                    $('#theorical_measurement_value').val('0.000');
                }
            });
        }

        // Guardar medición
        $('#btn-save-measurement').on('click', function() {
            const pumpId = $('#select-pump-measurement').val();
            const initialValue = parseFloat($('#initial_measurement_value').val()) || 0;
            const finalValue = parseFloat($('#final_measurement_value').val()) || 0;
            const theoreticalValue = parseFloat($('#theorical_measurement_value').val()) || 0;

            // Validaciones
            if (!pumpId) {
                ToastError.fire({
                    text: 'Debe seleccionar un surtidor'
                });
                return;
            }

            if (finalValue <= 0) {
                ToastError.fire({
                    text: 'El valor final debe ser mayor a cero'
                });
                return;
            }

            if (finalValue < initialValue) {
                Swal.fire({
                    title: '¿Confirmar medición?',
                    text: 'El valor final es menor que el inicial. ¿Desea continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue);
                    }
                });
            } else {
                guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue);
            }
        });

        // Calcular diferencia automáticamente cuando cambie el valor final
        $('#final_measurement_value').on('input', function() {
            calcularDiferenciaMedicion();
        });

        function calcularDiferenciaMedicion() {
            const initial = parseFloat($('#initial_measurement_value').val()) || 0;
            const final = parseFloat($('#final_measurement_value').val()) || 0;
            const teorico = parseFloat($('#theorical_measurement_value').val()) || 0;

            // Diferencia = (Final - Inicial) - Teórico
            const diferencia = (initial - final) - teorico

            $('#difference_measurement_value').val(diferencia.toFixed(3));
        }

        // Función para guardar la medición
        function guardarMedicion(pumpId, initialValue, finalValue, theoreticalValue) {
            const $btn = $('#btn-save-measurement');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('sales.measurements.save') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pump_id: pumpId,
                    initial_value: initialValue,
                    final_value: finalValue,
                    theoretical_value: theoreticalValue
                },
                success: function(response) {
                    if (response.success) {
                        ToastMessage.fire({
                            text: response.message || 'Medición guardada correctamente'
                        });

                        // Cerrar modal y limpiar campos
                        $('#finalMeasurementModal').modal('hide');
                        $('#select-isle-measurement').val('');
                        $('#select-pump-measurement').html('<option value="">Seleccione un surtidor</option>');
                        $('#pump_side').val('');
                        $('#initial_measurement_value').val('');
                        $('#final_measurement_value').val('');
                        $('#theorical_measurement_value').val('');
                    } else {
                        ToastError.fire({
                            text: response.message || 'Error al guardar medición'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error al guardar medición:', xhr);

                    // Manejar error 422 (validación)
                    if (xhr.status === 422) {
                        const errorMessage = xhr.responseJSON?.message ||
                            'Ya existe una medición para este surtidor hoy';
                        Swal.fire({
                            icon: 'error',
                            title: 'No se puede guardar',
                            text: errorMessage,
                            confirmButtonText: 'Entendido'
                        });
                    } else {
                        const errorMessage = xhr.responseJSON?.message || 'Error al guardar la medición';
                        ToastError.fire({
                            text: errorMessage
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        }

        /**
         * FUNCIÓN DE EJEMPLO: Registrar pago parcial para venta a crédito
         * Esta función muestra cómo usar el nuevo endpoint de pagos parciales
         * que soporta múltiples métodos de pago para un mismo pago
         * 
         * Uso:
         * registerCreditPayment(saleId, [
         *   { payment_method_id: 1, amount: 50.00 },  // Efectivo
         *   { payment_method_id: 2, amount: 30.00 }   // Tarjeta
         * ]);
         */
        function registrarPagoParcialCredito(saleId, paymentMethods) {
            $.ajax({
                url: '{{ route('sales.creditPayment') }}',
                method: 'POST',
                data: {
                    sale_id: saleId,
                    payment_methods: paymentMethods,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        ToastSuccess.fire({
                            title: 'Éxito',
                            text: response.message
                        });
                        console.log('Total pagado:', response.data.total_pagado);
                        console.log('Saldo restante:', response.data.saldo_restante);
                        // Aquí puedes actualizar la UI, recargar tabla de pagos, etc.
                    } else {
                        ToastError.fire({
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error al registrar pago';
                    ToastError.fire({
                        title: 'Error',
                        text: errorMsg
                    });
                }
            });
        }
    </script>
@endsection
