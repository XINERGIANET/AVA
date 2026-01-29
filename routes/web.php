<?php

use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\PlaqueController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CashCloseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\IsleController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\SideController;
use App\Http\Controllers\TanqueController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DischargeController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RecalibrationController;
use App\Http\Controllers\VaultController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FlowMeterController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');


Route::get('/sales/prueba', function () {
    return view('sales.prueba');
});


Route::group(['middleware' => 'auth'], function () {
    // Permission Module

    // Cambiar sede del usuario
    Route::post('/user/change-location', function () {
        $user = auth()->user();
        $locationId = request()->input('location_id');
        
        $user->location_id = $locationId;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Sede actualizada correctamente',
            'location_id' => $locationId
        ]);
    })->name('user.changeLocation');

    // Rutas protegidas
    Route::get('/main', function () {
        //return view('dashboard.index');
        return view('reports.alternativo');
    })->name('dashboard.index');

    Route::get('/dashboard', function () {
        //return view('dashboard.index');
        return view('dashboard.index');
    })->name('dashboard');

    //CRUD CONTRATOS/CREDITOS

    Route::post('/user/set-employee', [UserController::class, 'setEmployee'])->name('user.setEmployee');
    Route::post('contracts/closing', [ContractController::class, 'generate_closing'])->name('contracts.generate_closing');
    Route::post('contracts/export_closing', [ContractController::class, 'export_closing_excel'])->name('contracts.export_closing');
    Route::get('contracts/{id}/details', [ContractController::class, 'details_modal'])->name('contracts.details_modal');
    Route::get('contracts/by-client', [ContractController::class, 'getContractsByClient'])->name('contracts.by.client');
    Route::get('contracts/orders-by-contract', [ContractController::class, 'getOrdersByContract'])->name('orders.by.contract');
    Route::get('contracts/products-by-contract', [ContractController::class, 'getProductsByContract'])->name('products.by.contract');
    Route::get('contracts/excel', [ContractController::class, 'excel'])->name('contracts.excel');
    Route::get('contracts/pdf', [ContractController::class, 'pdf'])->name('contracts.pdf');

    Route::resource('users', UserController::class);
    Route::resource('maintenances', MaintenanceController::class);
    Route::resource('contracts', ContractController::class);
    Route::group(['prefix' => 'contracts'], function () {
        Route::get('{id}/orders', [ContractController::class, 'getOrders'])->name('contracts.orders');
        Route::get('products/{locationId}', [ContractController::class, 'getProductsByLocation'])->name('contracts.products');
        Route::post('order-details', [ContractController::class, 'guardarOrderDetails'])->name('orderdetails.store');
        Route::prefix('details')->controller(ContractController::class)->group(function () {
            Route::post('{id}/remove-area', 'removeArea')->name('orderdetails.removeArea');
            Route::post('{id}/remove-order', 'removeOrder')->name('orderdetails.removeOrder');
        });
    });
    Route::resource('orders', OrderController::class);
    Route::group(['prefix' => 'orders'], function () {
        Route::post('areas', [OrderController::class, 'storeArea'])->name('orders.areas.store');
    });

    //CRUD CONTOMETRO
    Route::get('flowmeters/historico', [FlowMeterController::class, 'historico'])->name('flowmeters.historico');
    Route::resource('flowmeters', FlowMeterController::class);

    //CRUD PRODUCTO
    Route::get('products/prices', [ProductController::class, 'getProductsBySede'])->name('products.prices');
    Route::get('products/by-order/{orderId}', [ProductController::class, 'getProductsByOrder'])->name('products.by.order');
    Route::resource('products', ProductController::class);

    //CRUD VENTAS
    Route::get('sales/historico', [SaleController::class, 'historico'])->name('sales.historico');
    Route::get('sales/excelByIsle', [SaleController::class, 'excelByIsle'])->name('sales.excelByIsle');
    Route::get('sales/excel', [SaleController::class, 'excel'])->name('sales.excel');
    Route::get('sales/pdf_report', [SaleController::class, 'pdf'])->name('sales.pdf');
    Route::post('sales/credit-payment', [SaleController::class, 'registerCreditPayment'])->name('sales.creditPayment');
    Route::put('sales/{sale}/date', [SaleController::class, 'updateDate'])->name('sales.updateDate');
    Route::resource('sales', SaleController::class);
    Route::get('/sunat/consultar', [SaleController::class, 'consultarSunat']);
    
    //CRUD RECALIBRACION
    Route::get('recalibration/index', [RecalibrationController::class, 'index'])->name('recalibration.index');
    Route::get('recalibration/show/{id}', [RecalibrationController::class, 'show'])->name('recalibration.show');
    Route::post('recalibration/update-quantities', [RecalibrationController::class, 'updateQuantities'])->name('recalibration.updateQuantities');
    
    // Rutas para mediciones de contómetro
    Route::get('/sales/measurements/isles', [SaleController::class, 'getIslesByLocation'])->name('sales.measurements.isles');
    Route::get('/sales/measurements/pumps', [SaleController::class, 'getPumpsByIsle'])->name('sales.measurements.pumps');
    Route::get('/sales/measurements/last', [SaleController::class, 'getLastMeasurement'])->name('sales.measurements.last');
    Route::get('/sales/measurements/theoretical', [SaleController::class, 'getTheoreticalValue'])->name('sales.measurements.theoretical');
    Route::post('/sales/measurements/save', [SaleController::class, 'saveMeasurement'])->name('sales.measurements.save');
    
    Route::resource('expenses', ExpenseController::class);

    //CRUD CLIENTE
    Route::get('/api/search-client/', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('clients', ClientController::class);
    Route::group(['prefix' => 'clients'], function () {
        Route::post('/api/save-client/', [ClientController::class, 'save_ajax'])->name('clients.save');
    });

    Route::resource('plaques', PlaqueController::class);

    Route::resource('collaborators', CollaboratorController::class);


    Route::get('/api/search-supplier/', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('tanques', TanqueController::class);
    Route::resource('sedes', SedeController::class);
    Route::resource('isles', IsleController::class);
    Route::resource('fuelpumps', PumpController::class);
    Route::resource('sides', SideController::class);
    Route::post('vault/from-cash-close', [VaultController::class, 'storeVaultFromCashClose'])->name('vault.from_cash_close');
    Route::put('vault/approve/{id}', [VaultController::class, 'approve'])->name('vault.approve');
    Route::resource('vault', VaultController::class);
    // Endpoint para transferir desde caja a bóveda (usado por AJAX en la vista de ventas)

    Route::resource('areas', AreaController::class);

    Route::get('/purchases/pdf/product', [PurchaseController::class, 'generatePDFProduct'])->name('purchases.pdfProduct');
    Route::get('/purchases/pdf/allproducts', [PurchaseController::class, 'generatePDFAllProducts'])->name('purchases.pdfAllProducts');
    Route::get('/purchases/pdf_report', [PurchaseController::class, 'pdf'])->name('purchases.pdf');
    Route::get('/purchases/pdf_report_general', [PurchaseController::class, 'pdf_general'])->name('purchases.pdfGeneral');
    Route::get('purchases/excel', [PurchaseController::class, 'excel'])->name('purchases.excel');
    Route::resource('purchases', PurchaseController::class);
    Route::get('/buscar-supplier', [PurchaseController::class, 'buscarSuppliers'])->name('buscar.suppliers');
    Route::get('/buscar-product', [PurchaseController::class, 'buscarProducts'])->name('buscar.products');

    Route::get('payments/excel', [PaymentController::class, 'excel'])->name('payments.excel');
    Route::get('payments/pdf', [PaymentController::class, 'pdf'])->name('payments.pdf');
    //CRUD PAGOS
    Route::get('payments.get', [PaymentController::class, 'getPayments'])->name('payments.get');
    Route::resource('payments', PaymentController::class);

    Route::resource('cashClose', CashCloseController::class);

    Route::resource('discharges', DischargeController::class);

    Route::get('transfers/historico', [TransferController::class, 'historico'])->name('transfers.historico');
    Route::get('/transfers/excel', [TransferController::class, 'excel'])->name('transfers.excel');
    Route::get('/transfers/pdf', [TransferController::class, 'pdf'])->name('transfers.pdf');
    Route::resource('transfers', TransferController::class);

    Route::resource('storages', StorageController::class);

    Route::get('credits/historico', [CreditController::class, 'historico'])->name('credits.historico');
    Route::get('credits/pdf', [CreditController::class, 'pdf'])->name('credits.pdf');
    Route::get('/credits/excel', [CreditController::class, 'excel'])->name('credits.excel');
    Route::resource('credits', CreditController::class);

    Route::get('/api/search_purchase/', [PurchaseController::class, 'searchPurchase'])->name('purchases.search');

    Route::post('/api/save-supplier/', [SupplierController::class, 'save_ajax'])->name('suppliers.saveSupplier');


    Route::get('/intake', function () {
        return view('intake.index');
    })->name('intake.index');

    Route::resource('measurements', MeasurementController::class);
    // Route::get('/api/getMeasurements', [MeasurementController::class, 'getMeasurements'])->name('measurements.get');

    Route::get('/counts', function () {
        return view('counts.index');
    })->name('counts.index');


    Route::get('/prueba', function () {
        return view('prueba.index');
    });

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/reports_alternative', function () {
        return view('reports.alternativo');
    })->name('reports_alternative.index');

    Route::resource('cash_closes', CashCloseController::class);
});
