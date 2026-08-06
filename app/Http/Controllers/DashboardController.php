<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        $thisMonth = $request->input('month', $today->format('m'));
        $thisYear = $request->input('year', $today->format('Y'));

        $user = auth()->user();
        $isMaster = $user->role->nombre === 'master';

        // Determinar si la sede viene fijada desde la barra superior o rol de usuario
        if (!$isMaster) {
            $locationId = $user->location_id;
            $isLocationFixed = true;
        } else {
            if (!empty($user->location_id)) {
                $locationId = $user->location_id;
                $isLocationFixed = true;
            } else {
                $locationId = $request->input('location_id');
                $isLocationFixed = false;
            }
        }

        $activeLocationName = '';
        if ($locationId) {
            $loc = Location::find($locationId);
            if ($loc) $activeLocationName = $loc->name;
        }

        $locations = $isMaster
            ? Location::where('deleted', 0)->get()
            : Location::where('id', $user->location_id)->get();

        // Base Queries with location filter
        $salesQuery = Sale::whereYear('date', $thisYear)->where('deleted', 0);
        $ventasHoyQuery = Sale::whereDate('date', $today->format('Y-m-d'))->where('deleted', 0);
        $gastosQuery = DB::table('transactions')->where('type', 'scc')->whereYear('date', $thisYear);
        $cajaQuery = DB::table('cash_closes')->whereYear('created_at', $thisYear);
        
        $paymentsQuery = DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->whereYear('payments.created_at', $thisYear)
            ->where('payments.deleted', 0)
            ->where('sales.deleted', 0);

        if ($thisMonth != 'all') {
            $salesQuery->whereMonth('date', $thisMonth);
            $gastosQuery->whereMonth('date', $thisMonth);
            $cajaQuery->whereMonth('created_at', $thisMonth);
            $paymentsQuery->whereMonth('payments.created_at', $thisMonth);
        }

        if ($locationId) {
            $salesQuery->where('location_id', $locationId);
            $ventasHoyQuery->where('location_id', $locationId);
            // In transactions, location is location_id
            $gastosQuery->where('location_id', $locationId);
            $cajaQuery->where('location_id', $locationId);
            $paymentsQuery->where('sales.location_id', $locationId);
        }

        $ventasTotalesMes = $salesQuery->sum('total');
        $ventasHoy = $ventasHoyQuery->sum('total');
        $cantidadVentasHoy = $ventasHoyQuery->count();
        
        $gastosTotales = $gastosQuery->sum('amount') ?: 0;
        $ingresosCaja = $cajaQuery->sum('real_cash_amount') ?: 0;

        $rentabilidad = $ventasTotalesMes - $gastosTotales;
        $balanceActual = $rentabilidad;

        $rentabilidadPorcentaje = 0;
        if($ventasTotalesMes > 0) {
            $rentabilidadPorcentaje = round(($rentabilidad / $ventasTotalesMes) * 100);
        }

        $efectivo = (clone $paymentsQuery)->where('payment_methods.name', 'like', '%Efectivo%')->sum('payments.amount') ?: 0;
        $yapePlin = (clone $paymentsQuery)->where(function($q) {
                $q->where('payment_methods.name', 'like', '%Yape%')->orWhere('payment_methods.name', 'like', '%Plin%');
            })->sum('payments.amount') ?: 0;
        $transferencias = (clone $paymentsQuery)->where('payment_methods.name', 'like', '%Transferencia%')->sum('payments.amount') ?: 0;
        $creditos = (clone $paymentsQuery)->where('payment_methods.name', 'like', '%Credito%')->sum('payments.amount') ?: 0;

        // REAL DATA FOR CHARTS
        $labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $dataGalones = [];
        $dataVentaReal = [];
        $dataVentaTeorica = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthPad = str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Galones reales (Combustible)
            $gQuery = DB::table('sale_details')
                ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->where(function($q) {
                    $q->where('products.measurement_unit', 'like', '%galon%')
                      ->orWhere('products.category', 'like', '%Combustible%');
                })
                ->whereYear('sales.date', $thisYear)
                ->whereMonth('sales.date', $monthPad)
                ->where('sales.deleted', 0);
            
            if ($locationId) $gQuery->where('sales.location_id', $locationId);
            
            $dataGalones[] = round($gQuery->sum('sale_details.quantity'), 2) ?: 0;

            // Venta Real por mes
            $vQuery = Sale::whereYear('date', $thisYear)->whereMonth('date', $monthPad)->where('deleted', 0);
            if ($locationId) $vQuery->where('location_id', $locationId);
            $dataVentaReal[] = round($vQuery->sum('total'), 2) ?: 0;

            // Venta Teórica por mes (Desde Measurements)
            $mQuery = DB::table('measurements')->whereYear('date', $thisYear)->whereMonth('date', $monthPad)->where('deleted', 0);
            if ($locationId) $mQuery->where('location_id', $locationId);
            $dataVentaTeorica[] = round($mQuery->sum('amount_theorical'), 2) ?: 0;
        }

        $chartGalones = [
            'labels' => $labels,
            'data' => $dataGalones
        ];

        $chartVentasTeoricas = [
            'labels' => $labels,
            'real' => $dataVentaReal,
            'teorica' => $dataVentaTeorica
        ];

        return view('reports.alternativo', compact(
            'thisYear', 'thisMonth', 'locationId', 'locations', 'isLocationFixed', 'activeLocationName',
            'ventasTotalesMes', 'ventasHoy', 'cantidadVentasHoy',
            'gastosTotales', 'ingresosCaja', 'rentabilidad', 'balanceActual', 'rentabilidadPorcentaje',
            'efectivo', 'yapePlin', 'transferencias', 'creditos',
            'chartGalones', 'chartVentasTeoricas'
        ));
    }

    public function details(Request $request)
    {
        $type = $request->input('type');
        $thisYear = $request->input('year', date('Y'));
        $thisMonth = $request->input('month', date('m'));

        $user = auth()->user();
        $isMaster = $user->role->nombre === 'master';
        $canDelete = in_array($user->role->nombre ?? '', ['master', 'admin'], true);
        
        if (!$isMaster) {
            $locationId = $user->location_id;
        } else {
            $locationId = !empty($user->location_id) ? $user->location_id : $request->input('location_id');
        }

        $title = 'Detalle de Registros';
        $data = [];

        switch ($type) {
            case 'ventas':
                $title = 'Detalle de Ventas (' . ($thisMonth == 'all' ? 'Año ' . $thisYear : 'Mes ' . $thisMonth . '/' . $thisYear) . ')';
                $q = Sale::with(['client', 'location'])
                    ->whereYear('date', $thisYear)
                    ->where('deleted', 0);
                if ($thisMonth != 'all') $q->whereMonth('date', $thisMonth);
                if ($locationId) $q->where('location_id', $locationId);

                $data = $q->orderBy('date', 'desc')->orderBy('id', 'desc')->get()->map(function ($s) {
                    return [
                        'sale_id' => $s->id,
                        'fecha' => $s->date ? $s->date->format('d/m/Y') : 'N/A',
                        'numero' => ($s->voucher_code ?? $s->type_sale ?? 'Venta') . ' #' . $s->id,
                        'cliente' => $s->client->business_name ?? $s->client->contact_name ?? $s->client_name ?? 'Cliente Genérico',
                        'sede' => $s->location->name ?? 'N/A',
                        'monto' => number_format($s->total, 2)
                    ];
                });
                break;

            case 'gastos':
                $title = 'Detalle de Gastos (' . ($thisMonth == 'all' ? 'Año ' . $thisYear : 'Mes ' . $thisMonth . '/' . $thisYear) . ')';
                $q = DB::table('transactions')
                    ->leftJoin('locations', 'transactions.location_id', '=', 'locations.id')
                    ->where('type', 'scc')
                    ->whereYear('date', $thisYear);
                if ($thisMonth != 'all') $q->whereMonth('date', $thisMonth);
                if ($locationId) $q->where('location_id', $locationId);

                $data = $q->orderBy('date', 'desc')->orderBy('transactions.id', 'desc')->get()->map(function ($t) {
                    return [
                        'fecha' => $t->date ? date('d/m/Y', strtotime($t->date)) : 'N/A',
                        'numero' => $t->description ?? 'Gasto de caja',
                        'cliente' => $t->category ?? 'General',
                        'sede' => $t->name ?? 'N/A',
                        'monto' => number_format($t->amount, 2)
                    ];
                });
                break;

            case 'caja':
                $title = 'Detalle de Ingresos de Caja (' . ($thisMonth == 'all' ? 'Año ' . $thisYear : 'Mes ' . $thisMonth . '/' . $thisYear) . ')';
                $q = DB::table('cash_closes')
                    ->leftJoin('locations', 'cash_closes.location_id', '=', 'locations.id')
                    ->whereYear('cash_closes.created_at', $thisYear);
                if ($thisMonth != 'all') $q->whereMonth('cash_closes.created_at', $thisMonth);
                if ($locationId) $q->where('cash_closes.location_id', $locationId);

                $data = $q->orderBy('cash_closes.created_at', 'desc')->get()->map(function ($c) {
                    return [
                        'fecha' => date('d/m/Y H:i', strtotime($c->created_at)),
                        'numero' => 'Cierre #' . $c->id,
                        'cliente' => $c->observation ?? 'Cierre de caja',
                        'sede' => $c->name ?? 'N/A',
                        'monto' => number_format($c->real_cash_amount ?? 0, 2)
                    ];
                });
                break;

            case 'efectivo':
            case 'yape_plin':
            case 'transferencias':
            case 'creditos':
                $titles = [
                    'efectivo' => 'Detalle de Pagos en Efectivo',
                    'yape_plin' => 'Detalle de Pagos Yape / Plin',
                    'transferencias' => 'Detalle de Pagos por Transferencia',
                    'creditos' => 'Detalle de Pendientes / Crédito',
                ];
                $title = ($titles[$type] ?? 'Detalle de Pagos') . ' (' . ($thisMonth == 'all' ? 'Año ' . $thisYear : 'Mes ' . $thisMonth . '/' . $thisYear) . ')';

                $q = DB::table('payments')
                    ->join('sales', 'payments.sale_id', '=', 'sales.id')
                    ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
                    ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
                    ->leftJoin('locations', 'sales.location_id', '=', 'locations.id')
                    ->whereYear('payments.created_at', $thisYear)
                    ->where('payments.deleted', 0)
                    ->where('sales.deleted', 0);

                if ($thisMonth != 'all') $q->whereMonth('payments.created_at', $thisMonth);
                if ($locationId) $q->where('sales.location_id', $locationId);

                if ($type === 'efectivo') {
                    $q->where('payment_methods.name', 'like', '%Efectivo%');
                } elseif ($type === 'yape_plin') {
                    $q->where(function ($sub) {
                        $sub->where('payment_methods.name', 'like', '%Yape%')->orWhere('payment_methods.name', 'like', '%Plin%');
                    });
                } elseif ($type === 'transferencias') {
                    $q->where('payment_methods.name', 'like', '%Transferencia%');
                } elseif ($type === 'creditos') {
                    $q->where('payment_methods.name', 'like', '%Credito%');
                }

                $data = $q->select(
                    'payments.created_at',
                    'sales.voucher_code',
                    'sales.type_sale',
                    'sales.id as sale_id',
                    'sales.client_name as sale_client_name',
                    'clients.business_name',
                    'clients.contact_name',
                    'locations.name as location_name',
                    'payment_methods.name as pm_name',
                    'payments.amount'
                )->orderBy('payments.created_at', 'desc')->get()->map(function ($p) {
                    return [
                        'sale_id' => $p->sale_id,
                        'fecha' => date('d/m/Y H:i', strtotime($p->created_at)),
                        'numero' => ($p->voucher_code ?? $p->type_sale ?? 'Venta') . ' #' . $p->sale_id,
                        'cliente' => $p->business_name ?? $p->contact_name ?? $p->sale_client_name ?? 'Cliente Genérico',
                        'sede' => $p->location_name ?? 'N/A',
                        'metodo' => $p->pm_name,
                        'monto' => number_format($p->amount, 2)
                    ];
                });
                break;
        }

        return response()->json([
            'success' => true,
            'title' => $title,
            'can_delete' => $canDelete,
            'items' => $data
        ]);
    }
}
