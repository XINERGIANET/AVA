<div id="spinner" class="justify-content-center align-items-center flex-column spinner-overlay spinner-hidden"
     style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 99999; transition: opacity 0.5s ease, visibility 0.5s ease;">
    
    <div class="d-flex align-items-center mb-3">
        <!-- Logo Animado -->
        <img src="{{ asset('assets/icon/logo.svg') }}" alt="Logo Ava" style="width: 120px; animation: pulse-logo 1.5s infinite ease-in-out;">
        <h1 class="ms-3 mb-0" style="color: #465fff; font-weight: 800; font-size: 3.5rem; letter-spacing: 2px;">AVA</h1>
    </div>
    
    <!-- Barra de carga -->
    <div class="progress" style="width: 300px; height: 10px; border-radius: 10px; background-color: #e2e8f0; overflow: hidden;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%; background-color: #465fff;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    
    <span class="mt-3 font-weight-bold" style="color: #465fff; font-size: 1.2rem;">Cargando...</span>
</div>

<style>
    .spinner-overlay {
        display: flex !important;
    }

    .spinner-hidden {
        opacity: 0 !important;
        visibility: hidden !important;
    }

    .spinner-visible {
        opacity: 1 !important;
        visibility: visible !important;
    }

    @keyframes pulse-logo {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>