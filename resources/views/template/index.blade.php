<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AVA</title>
    <!-- <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" /> -->
    <link rel="icon" href="{{ asset('assets/icon/logo.svg') }}" type="image/svg+xml" />

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />

    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}" />

    <!-- Aos Animation Css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/aos/dist/aos.css') }}" />

    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=2.0.0') }}" />

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=2.0.0') }}" />

    <!-- Dark Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark.min.css') }}" />

    <!-- Customizer Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css') }}" />

    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css') }}" />


    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2-theme-material-ui.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <style>
        #notificaciones-body {
            max-height: 300px;
            /* Ajusta la altura máxima según tus necesidades */
            overflow-y: auto;
            /* Habilita el scroll vertical */
        }
    </style>

    @yield('styles')

</head>

<body class="">
    <!-- loader Start -->
    @include('components.spinner')
    <!-- loader END -->

    @if (auth()->user()->role->nombre != 'worker')
        <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
            <div class="sidebar-header d-flex align-items-center justify-content-start">
                <a href="" class="navbar-brand">
                    <!--Logo start-->
                    <div class="logo-main">
                        <div class="logo-normal">
                            <img src="{{ asset('assets/icon/logo.svg') }}" alt="Logo Normal" class="icon-30">
                        </div>
                        <div class="logo-mini">
                            <img src="{{ asset('assets/icon/logo.svg') }}" alt="Logo Mini" class="icon-30">
                        </div>
                    </div>
                    <!--logo End-->
                    <h4 class="logo-title">Ava</h4>
                </a>
                <!-- <div class="sidebar-toggle" data-toggle="sidebar" data-active="true" display="none">
                    <i class="icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </i>
                </div> -->
            </div>
            <div class="sidebar-body pt-0 data-scrollbar">
                <div class="sidebar-list">
                    <!-- Sidebar Menu Start -->
                    <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                        @if (auth()->user()->role->nombre != 'worker')
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="{{ route('dashboard.index') }}"
                                    title="Indicadores de gestión">
                                    <i class="icon">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.13478 20.7733V17.7156C9.13478 16.9351 9.77217 16.3023 10.5584 16.3023H13.4326C13.8102 16.3023 14.1723 16.4512 14.4393 16.7163C14.7063 16.9813 14.8563 17.3408 14.8563 17.7156V20.7733C14.8539 21.0978 14.9821 21.4099 15.2124 21.6402C15.4427 21.8705 15.7561 22 16.0829 22H18.0438C18.9596 22.0023 19.8388 21.6428 20.4872 21.0008C21.1356 20.3588 21.5 19.487 21.5 18.5778V9.86686C21.5 9.13246 21.1721 8.43584 20.6046 7.96467L13.934 2.67587C12.7737 1.74856 11.1111 1.7785 9.98539 2.74698L3.46701 7.96467C2.87274 8.42195 2.51755 9.12064 2.5 9.86686V18.5689C2.5 20.4639 4.04738 22 5.95617 22H7.87229C8.55123 22 9.103 21.4562 9.10792 20.7822L9.13478 20.7733Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </i>
                                    <span class="item-name">Indicadores de gestión</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-special" role="button"
                                    aria-expanded="false" aria-controls="sidebar-special">
                                    <i class="icon">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21 8L12 3L3 8V18L12 23L21 18V8ZM12 5.3L18 8.6L12 12L6 8.6L12 5.3ZM5 10.1L11 13.8V20.7L5 17.3V10.1ZM13 20.7V13.8L19 10.1V17.3L13 20.7Z"
                                                fill="currentColor" />
                                        </svg>
                                    </i>

                                    <span class="item-name">Base de Datos</span>
                                    <i class="right-icon">
                                        <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </i>
                                </a>
                                <ul class="sub-nav collapse" id="sidebar-special" data-bs-parent="#sidebar-menu">
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('clients.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> C </i>
                                            <span class="item-name">Clientes</span>
                                        </a>
                                    </li>
                                    @if (auth()->user()->role->nombre == 'master')
                                        <li class="nav-item">
                                            <a class="nav-link " href="{{ route('sedes.index') }}">
                                                <i class="icon">
                                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                        width="10" viewBox="0 0 24 24" fill="currentColor">
                                                        <g>
                                                            <circle cx="12" cy="12" r="8"
                                                                fill="currentColor">
                                                            </circle>
                                                        </g>
                                                    </svg>
                                                </i>
                                                <i class="sidenav-mini-icon"> S </i>
                                                <span class="item-name">Sedes</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('tanques.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> T </i>
                                            <span class="item-name">Tanques</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('isles.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> I </i>
                                            <span class="item-name">Islas</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('fuelpumps.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> Su </i>
                                            <span class="item-name">Surtidores</span>
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                <a class="nav-link " href="{{ route('sides.index') }}">
                                    <i class="icon">
                                        <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                                <circle cx="12" cy="12" r="8" fill="currentColor">
                                                </circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> LSu </i>
                                    <span class="item-name">Lados del Surtidor</span>
                                </a>
                            </li> -->

                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('collaborators.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> E </i>
                                            <span class="item-name">Colaboradores</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('products.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> P </i>
                                            <span class="item-name">Productos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('plaques.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> P </i>
                                            <span class="item-name">Placas</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('suppliers.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> P </i>
                                            <span class="item-name">Proveedores</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('users.create') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> U </i>
                                            <span class="item-name">Usuarios</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link " aria-current="page" href="{{ route('sales.index') }}">
                                <i class="icon">
                                    <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="6" width="18" height="12" rx="2"
                                            stroke="currentColor" stroke-width="2" />
                                        <circle cx="12" cy="12" r="2" stroke="currentColor"
                                            stroke-width="2" />
                                        <path d="M6 9H6.01M18 9H18.01M6 15H6.01M18 15H18.01" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </i>
                                <span class="item-name">Registrar Ventas</span>
                            </a>
                        </li>

                        @if (auth()->user()->role->nombre != 'worker')
                            <li class="nav-item">
                                <a class="nav-link " aria-current="page" href="{{ route('purchases.create') }}">
                                    <i class="icon">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21 11.5V5C21 3.9 20.1 3 19 3H12.5C12.2 3 11.9 3.1 11.7 3.3L3.3 11.7C3.1 11.9 3 12.2 3 12.5V19C3 20.1 3.9 21 5 21H11.5C11.8 21 12.1 20.9 12.3 20.7L20.7 12.3C20.9 12.1 21 11.8 21 11.5ZM17 7C17.6 7 18 7.4 18 8C18 8.6 17.6 9 17 9C16.4 9 16 8.6 16 8C16 7.4 16.4 7 17 7ZM5 12.9L12.9 5H19V11.1L11.1 19H5V12.9Z"
                                                fill="currentColor" />
                                        </svg>
                                    </i>
                                    <span class="item-name">Registrar Compras</span>
                                </a>
                            </li>
                            @if (auth()->user()->role->nombre != 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="{{ route('contracts.create') }}">
                                        <i class="icon">
                                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M14.06 9L15 9.94L5.92 19H5V18.08L14.06 9ZM17.66 3C17.41 3 17.15 3.1 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04C21.1 6.65 21.1 6 20.71 5.63L18.37 3.29C18.17 3.09 17.92 3 17.66 3ZM14.06 6.19L3 17.25V21H6.75L17.81 9.94L14.06 6.19Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </i>
                                        <span class="item-name">Registrar Contratos</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('credits.create') }}">
                                    <i class="icon">
                                        <!-- Créditos -->
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 10H21V14H3V10Z" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7 6H17V10H7V6Z" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7 14H17V18H7V14Z" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </i>
                                    <span class="item-name">Gestión de Créditos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('maintenances.index') }}">
                                    <i class="bi bi-truck"></i>
                                    <span class="item-name">Registrar Manten.</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('vault.create') }}">
                                    <i class="bi bi-safe-fill"></i>
                                    <span class="item-name">Bóveda</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('flowmeters.create') }}">
                                    <i class="bi bi-safe-fill"></i>
                                    <span class="item-name">Contómetro</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-manage" role="button"
                                    aria-expanded="false" aria-controls="sidebar-special">
                                    <i class="icon">
                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10 2H3C2.45 2 2 2.45 2 3V21C2 21.55 2.45 22 3 22H10C10.55 22 11 21.55 11 21V3C11 2.45 10.55 2 10 2ZM5 4H8V8H5V4ZM5 10H8V14H5V10ZM5 16H8V20H5V16ZM13 7C12.45 7 12 7.45 12 8V21C12 21.55 12.45 22 13 22H20C20.55 22 21 21.55 21 21V8C21 7.45 20.55 7 20 7H13ZM15 10H18V12H15V10ZM15 14H18V16H15V14ZM15 18H18V20H15V18ZM19 3C18.45 3 18 3.45 18 4V5H14V3H12V6H19V4C19 3.45 18.55 3 18 3Z"
                                                fill="currentColor" />
                                        </svg>
                                    </i>
                                    <span class="item-name">Operaciones Grifo</span>
                                    <i class="right-icon">
                                        <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </i>
                                </a>
                                <ul class="sub-nav collapse" id="sidebar-manage" data-bs-parent="#sidebar-menu">
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('storages.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> A </i>
                                            <span class="item-name">Almacén</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('transfers.index') }}">
                                            <i class="icon">
                                                <!-- Distribución -->
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <span class="item-name">Distribución</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('discharges.index') }}">
                                            <i class="icon">
                                                <!-- Distribución -->
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <span class="item-name">Descargas</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('measurements.create') }}">
                                            <i class="icon">
                                                <!-- Medición -->
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <span class="item-name">Mediciones</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-history" role="button"
                                    aria-expanded="false" aria-controls="sidebar-special">
                                    <i class="bi bi-bookmark"></i>
                                    <span class="item-name">Históricos</span>
                                    <i class="right-icon">
                                        <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </i>
                                </a>
                                <ul class="sub-nav collapse" id="sidebar-history" data-bs-parent="#sidebar-menu">
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('sales.historico') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HV </i>
                                            <span class="item-name">Ventas</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('recalibration.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> RB </i>
                                            <span class="item-name">Recalibración</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('flowmeters.historico') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HC </i>
                                            <span class="item-name">Contometros</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('payments.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HP </i>
                                            <span class="item-name">Pagos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('credits.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HC </i>
                                            <span class="item-name">Créditos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('contracts.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HC </i>
                                            <span class="item-name">Contratos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('purchases.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HP </i>
                                            <span class="item-name">Compras</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('expenses.historico') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> EG </i>
                                            <span class="item-name">Egresos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('transfers.historico') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HD </i>
                                            <span class="item-name">Distribuciones</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('cashClose.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HD </i>
                                            <span class="item-name">Cierre de Caja</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="{{ route('vault.index') }}">
                                            <i class="icon">
                                                <svg class="icon-10" xmlns="http://www.w3.org/2000/svg"
                                                    width="10" viewBox="0 0 24 24" fill="currentColor">
                                                    <g>
                                                        <circle cx="12" cy="12" r="8"
                                                            fill="currentColor">
                                                        </circle>
                                                    </g>
                                                </svg>
                                            </i>
                                            <i class="sidenav-mini-icon"> HD </i>
                                            <span class="item-name">Mov. de bóveda</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="sidebar-footer"></div>
        </aside>
    @endif
    <main class="main-content">
        <div class="position-relative iq-banner">
            <!--Nav Start-->
            <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
                <div class="container-fluid navbar-inner">
                    <a href="" class="navbar-brand">
                        <!--Logo start-->
                        <div class="logo-main">
                            <div class="logo-normal">
                                <img src="{{ asset('assets/icon/logo.svg') }}" alt="Logo Normal" class="icon-30">
                            </div>
                            <div class="logo-mini">
                                <img src="{{ asset('assets/icon/logo.svg') }}" alt="Logo Mini" class="icon-30">
                            </div>
                        </div>
                        <!--logo End-->
                        <h4 class="logo-title">Ava</h4>
                    </a>
                    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                        <i class="icon">
                            <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                            </svg>
                        </i>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <span class="mt-2 navbar-toggler-bar bar1"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                            {{-- <li class="nav-item dropdown">
                                <a href="#" class="nav-link" id="notification-drop" data-bs-toggle="dropdown">
                                    <svg class="icon-24" width="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.7695 11.6453C19.039 10.7923 18.7071 10.0531 18.7071 8.79716V8.37013C18.7071 6.73354 18.3304 5.67907 17.5115 4.62459C16.2493 2.98699 14.1244 2 12.0442 2H11.9558C9.91935 2 7.86106 2.94167 6.577 4.5128C5.71333 5.58842 5.29293 6.68822 5.29293 8.37013V8.79716C5.29293 10.0531 4.98284 10.7923 4.23049 11.6453C3.67691 12.2738 3.5 13.0815 3.5 13.9557C3.5 14.8309 3.78723 15.6598 4.36367 16.3336C5.11602 17.1413 6.17846 17.6569 7.26375 17.7466C8.83505 17.9258 10.4063 17.9933 12.0005 17.9933C13.5937 17.9933 15.165 17.8805 16.7372 17.7466C17.8215 17.6569 18.884 17.1413 19.6363 16.3336C20.2118 15.6598 20.5 14.8309 20.5 13.9557C20.5 13.0815 20.3231 12.2738 19.7695 11.6453Z"
                                            fill="currentColor"></path>
                                        <path opacity="0.4"
                                            d="M14.0088 19.2283C13.5088 19.1215 10.4627 19.1215 9.96275 19.2283C9.53539 19.327 9.07324 19.5566 9.07324 20.0602C9.09809 20.5406 9.37935 20.9646 9.76895 21.2335L9.76795 21.2345C10.2718 21.6273 10.8632 21.877 11.4824 21.9667C11.8123 22.012 12.1482 22.01 12.4901 21.9667C13.1083 21.877 13.6997 21.6273 14.2036 21.2345L14.2026 21.2335C14.5922 20.9646 14.8734 20.5406 14.8983 20.0602C14.8983 19.5566 14.4361 19.327 14.0088 19.2283Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span class="bg-danger dots"></span>
                                </a>
                                <div class="p-0 sub-drop dropdown-menu dropdown-menu-end"
                                    aria-labelledby="notification-drop">
                                    <div class="m-0 shadow-none card">
                                        <div class="py-3 card-header d-flex justify-content-between bg-primary">
                                            <div class="header-title">
                                                <h5 class="mb-0 text-white">All Notifications</h5>
                                            </div>
                                        </div>
                                        <div class="p-0 card-body">
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                        src="{{ asset('assets/images/shapes/01.png') }}"
                                                        alt="">
                                                    <div class="ms-3 w-100">
                                                        <h6 class="mb-0">Emma Watson Bni</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="mb-0">95 MB</p>
                                                            <small class="float-end font-size-12">Just Now</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/02.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3 w-100">
                                                        <h6 class="mb-0">New customer is join</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="mb-0">Cyst Bni</p>
                                                            <small class="float-end font-size-12">5 days ago</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                        src="{{ asset('assets/images/shapes/03.png') }}"
                                                        alt="">
                                                    <div class="ms-3 w-100">
                                                        <h6 class="mb-0">Two customer is left</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="mb-0">Cyst Bni</p>
                                                            <small class="float-end font-size-12">2 days ago</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                        src="{{ asset('assets/images/shapes/04.png') }}"
                                                        alt="">
                                                    <div class="w-100 ms-3">
                                                        <h6 class="mb-0">New Mail from Fenny</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="mb-0">Cyst Bni</p>
                                                            <small class="float-end font-size-12">3 days ago</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link" id="mail-drop" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <svg class="icon-24" width="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.4"
                                            d="M22 15.94C22 18.73 19.76 20.99 16.97 21H16.96H7.05C4.27 21 2 18.75 2 15.96V15.95C2 15.95 2.006 11.524 2.014 9.298C2.015 8.88 2.495 8.646 2.822 8.906C5.198 10.791 9.447 14.228 9.5 14.273C10.21 14.842 11.11 15.163 12.03 15.163C12.95 15.163 13.85 14.842 14.56 14.262C14.613 14.227 18.767 10.893 21.179 8.977C21.507 8.716 21.989 8.95 21.99 9.367C22 11.576 22 15.94 22 15.94Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M21.4759 5.67351C20.6099 4.04151 18.9059 2.99951 17.0299 2.99951H7.04988C5.17388 2.99951 3.46988 4.04151 2.60388 5.67351C2.40988 6.03851 2.50188 6.49351 2.82488 6.75151L10.2499 12.6905C10.7699 13.1105 11.3999 13.3195 12.0299 13.3195C12.0339 13.3195 12.0369 13.3195 12.0399 13.3195C12.0429 13.3195 12.0469 13.3195 12.0499 13.3195C12.6799 13.3195 13.3099 13.1105 13.8299 12.6905L21.2549 6.75151C21.5779 6.49351 21.6699 6.03851 21.4759 5.67351Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span class="bg-primary count-mail"></span>
                                </a>
                                <div class="p-0 sub-drop dropdown-menu dropdown-menu-end" aria-labelledby="mail-drop">
                                    <div class="m-0 shadow-none card">
                                        <div class="py-3 card-header d-flex justify-content-between bg-primary">
                                            <div class="header-title">
                                                <h5 class="mb-0 text-white">All Message</h5>
                                            </div>
                                        </div>
                                        <div class="p-0 card-body">
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/01.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">Bni Emma Watson</h6>
                                                        <small class="float-start font-size-12">13 Jun</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/02.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">Lorem Ipsum Watson</h6>
                                                        <small class="float-start font-size-12">20 Apr</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/03.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">Why do we use it?</h6>
                                                        <small class="float-start font-size-12">30 Jun</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/04.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">Variations Passages</h6>
                                                        <small class="float-start font-size-12">12 Sep</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="iq-sub-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="">
                                                        <img class="p-1 avatar-40 rounded-pill bg-soft-primary"
                                                            src="{{ asset('assets/images/shapes/05.png') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">Lorem Ipsum generators</h6>
                                                        <small class="float-start font-size-12">5 Dec</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li> --}}
                            @if (auth()->user()->role->nombre == 'master')
                                <li class="nav-item dropdown">
                                    @php
                                        $sedes = \App\Models\Location::where('deleted', 0)->get();
                                    @endphp
                                    <select class="form-select" id="selectSede" name="sede" style="width:auto;">
                                        <option value="" @if (auth()->user()->location_id == null) selected @endif>
                                            Seleccionar sede
                                        </option>
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}"
                                                @if (auth()->user()->location_id && auth()->user()->location_id == $sede->id) selected @endif>
                                                {{ $sede->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('assets/images/avatars/01.png') }}" alt="User-Profile"
                                        class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded">
                                    <img src="{{ asset('assets/images/avatars/avtar_1.png') }}" alt="User-Profile"
                                        class="theme-color-purple-img img-fluid avatar avatar-50 avatar-rounded">
                                    <img src="{{ asset('assets/images/avatars/avtar_2.png') }}" alt="User-Profile"
                                        class="theme-color-blue-img img-fluid avatar avatar-50 avatar-rounded">
                                    <img src="{{ asset('assets/images/avatars/avtar_4.png') }}" alt="User-Profile"
                                        class="theme-color-green-img img-fluid avatar avatar-50 avatar-rounded">
                                    <img src="{{ asset('assets/images/avatars/avtar_5.png') }}" alt="User-Profile"
                                        class="theme-color-yellow-img img-fluid avatar avatar-50 avatar-rounded">
                                    <img src="{{ asset('assets/images/avatars/avtar_3.png') }}" alt="User-Profile"
                                        class="theme-color-pink-img img-fluid avatar avatar-50 avatar-rounded">
                                    <div class="caption ms-3 d-none d-md-block">
                                        <h6 class="mb-0 caption-title">
                                            @auth
                                                {{ auth()->user()->email }} <!-- Nombre del usuario -->
                                            @else
                                                Usuario <!-- Texto por defecto si no hay usuario autenticado -->
                                            @endauth
                                        </h6>
                                        <p class="mb-0 caption-sub-title">
                                            @auth

                                                {{ auth()->user()->employee_id ? auth()->user()->employee->name . ' ' . auth()->user()->employee->last_name : 'Sin asignar' }}
                                            @else
                                                Sin empleado asignado
                                            @endauth
                                        </p>
                                    </div>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf <!-- Token de seguridad -->
                                            <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> <!-- Nav Header Component Start -->
            <div class="iq-navbar-header" style="height: 180px;">
                <div class="container-fluid iq-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="flex-wrap d-flex justify-content-between align-items-center">
                                <div>
                                    @yield('header')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iq-header-img" style="height: 220px;">
                    <img src="{{ asset('assets/images/dashboard/top-header.png') }}" alt="header"
                        class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
                    <img src="{{ asset('assets/images/dashboard/top-header1.png') }}" alt="header"
                        class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
                    <img src="{{ asset('assets/images/dashboard/top-header2.png') }}" alt="header"
                        class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
                    <img src="{{ asset('assets/images/dashboard/top-header3.png') }}" alt="header"
                        class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
                    <img src="{{ asset('assets/images/dashboard/top-header4.png') }}" alt="header"
                        class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
                    <img src="{{ asset('assets/images/dashboard/top-header5.png') }}" alt="header"
                        class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
                </div>
            </div> <!-- Nav Header Component End -->
            <!--Nav End-->
        </div>
        <div>
            @yield('content')
        </div>

        <!-- Footer Section Start -->
        <footer class="footer">
            <div class="footer-body">
                <div class="right-panel">
                    ©
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Ava
                    <span class="">
                    </span> by <a href="">Xinergia</a>.
                </div>
            </div>
        </footer>
        <!-- Footer Section End -->
    </main>

    @if (Auth::user()->role->nombre === 'worker')
    <div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ingresar PIN</h5>
                </div>

                <div class="modal-body">
                    <p class="mb-3">Introduce el PIN del empleado (4 dígitos):</p>

                    <div class="mb-3">
                        <input id="employeePin" type="password" inputmode="numeric" pattern="\d*" maxlength="4"
                            class="form-control form-control-lg text-center fw-bold fs-4" readonly />
                        <input type="hidden" id="pinEmployeeId" value="" />
                    </div>

                    <div class="d-grid gap-2" style="grid-template-columns: repeat(3,1fr); display:grid;">
                        @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 'clear', 0, 'back'] as $key)
                            @if ($key === 'clear')
                                <button type="button" class="btn btn-outline-secondary btn-pin"
                                    data-action="clear">C</button>
                            @elseif($key === 'back')
                                <button type="button" class="btn btn-outline-secondary btn-pin" data-action="back">
                                    <i class="bi bi-arrow-left"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-pin"
                                    data-digit="{{ $key }}">{{ $key }}</button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btnPinConfirm" type="button" class="btn btn-primary" disabled>Confirmar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Wrapper End-->
    <!-- offcanvas start -->

    <!-- Library Bundle Script -->
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>

    <!-- External Library Bundle Script -->
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>

    <!-- Widgetchart Script -->
    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>

    <!-- mapchart Script -->
    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('assets/js/charts/dashboard.js') }}"></script>

    <!-- fslightbox Script -->
    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>

    <!-- Settings Script -->
    <script src="{{ asset('assets/js/plugins/setting.js') }}"></script>

    <!-- Slider-tab Script -->
    <script src="{{ asset('assets/js/plugins/slider-tabs.js') }}"></script>

    <!-- Form Wizard Script -->
    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>

    <!-- AOS Animation Plugin-->
    <script src="{{ asset('assets/vendor/aos/dist/aos.js') }}"></script>

    <!-- App Script -->
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>

    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    <script>
        const ToastError = Swal.mixin({
            title: 'Error',
            icon: 'error',
            toast: true,
            position: 'bottom-end',
            timer: 3000,
            timerProgressBar: true
        });

        const ToastMessage = Swal.mixin({
            title: 'Mensaje',
            icon: 'success',
            toast: true,
            position: 'bottom-end',
            timer: 2000,
            timerProgressBar: false
        });

        const ToastConfirm = Swal.mixin({
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: 'Aceptar',
            denyButtonText: 'Cancelar',
            toast: true,
            position: 'bottom-end'
        });
    </script>

    @if (session('success'))
        <script>
            ToastMessage.fire({
                text: "{{ session('success') }}"
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            ToastError.fire({
                text: '{{ $errors->first() }}'
            });
        </script>
    @endif

    <script>
        const spinner = document.getElementById('global-spinner');
        $('form').on('submit', function() {
            $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).ajaxStart(function() {
                $('#global-spinner').removeClass('spinner-hidden').addClass('spinner-visible');
            });
            $(document).ajaxStop(function() {
                $('#global-spinner').removeClass('spinner-visible').addClass('spinner-hidden');
            });
        });
    </script>

    <style>
        .dropdown-custom-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 0.5rem;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
            z-index: 1050;
            min-width: 200px;
            list-style: none;
        }

        .dropdown-custom-menu .dropdown-item {
            padding: 8px 16px;
            display: block;
            color: #212529;
            text-decoration: none;
        }

        .dropdown-custom-menu .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>

    <script>
        // Cambiar sede del usuario
        $(document).ready(function() {
            $('#selectSede').on('change', function() {
                const locationId = $(this).val();

                // Mostrar spinner si existe
                const spinner = document.getElementById('global-spinner');
                if (spinner) {
                    spinner.classList.remove('spinner-hidden');
                    spinner.classList.add('spinner-visible');
                }

                $.ajax({
                    url: '{{ route('user.changeLocation') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        location_id: locationId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Mostrar mensaje de éxito
                            if (typeof ToastMessage !== 'undefined') {
                                ToastMessage.fire({
                                    icon: 'success',
                                    text: response.message ||
                                        'Sede actualizada correctamente'
                                }).then(() => {
                                    // Recargar la página para actualizar todos los datos
                                    location.reload();
                                });
                            } else {
                                alert(response.message || 'Sede actualizada correctamente');
                                location.reload();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cambiar la sede:', error);

                        // Ocultar spinner
                        if (spinner) {
                            spinner.classList.add('spinner-hidden');
                            spinner.classList.remove('spinner-visible');
                        }

                        // Mostrar mensaje de error
                        if (typeof ToastError !== 'undefined') {
                            ToastError.fire({
                                text: 'Error al cambiar la sede. Intente nuevamente.'
                            });
                        } else {
                            alert('Error al cambiar la sede. Intente nuevamente.');
                        }
                    }
                });
            });
        });

        function updateConfirmState() {
            const val = $('#employeePin').val() || '';
            $('#btnPinConfirm').prop('disabled', val.length < 4); // mínimo 4 dígitos
        }

        // limpiar al abrir; permite setear employee id con data-attribute antes de mostrar
        $('#pinModal').on('show.bs.modal', function(e) {
            $('#employeePin').val('');
            $('#pinEmployeeId').val($(this).data('employee-id') || '');
            updateConfirmState();
        });

        // gestionar pulsaciones del keypad
        $(document).on('click', '.btn-pin', function() {
            const $input = $('#employeePin');
            const digit = $(this).data('digit');
            const action = $(this).data('action');

            if (action === 'clear') {
                $input.val('');
            } else if (action === 'back') {
                $input.val($input.val().slice(0, -1));
            } else if (typeof digit !== 'undefined') {
                if ($input.val().length < 4) {
                    $input.val($input.val() + digit);
                }
            }
            updateConfirmState();
        });

        $('#btnPinConfirm').on('click', function() {
            const pin = $('#employeePin').val();
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: '{{ route('user.setEmployee') }}',
                method: 'POST',
                data: {
                    pin: pin,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        ToastError.fire({
                            text: response.message || 'PIN inválido'
                        });
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let msg = 'Error de conexión. Intente nuevamente.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    ToastError.fire({
                        text: msg
                    });
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>

    @if (session('show_pin_modal'))
        <script>
            $(document).ready(function() {
                $('#pinModal').modal('show');
                $('#pinModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $('.btn-turno').on('click', function() {
                    var turno = $(this).data('turno');
                    $.ajax({
                        url: "{{ route('user.setEmployee') }}",
                        method: "POST",
                        data: {
                            shift: turno,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#turnoModal').modal('hide');
                                ToastMessage.fire({
                                    text: 'Turno guardado correctamente.'
                                });
                                location.reload();
                            } else {
                                ToastError.fire({
                                    text: 'No se pudo guardar el turno.'
                                });
                            }
                        },
                        error: function() {
                            ToastError.fire({
                                text: 'Error de conexión.'
                            });
                        }
                    });
                });
            });
        </script>
    @endif

    @yield('scripts')

</body>

</html>

