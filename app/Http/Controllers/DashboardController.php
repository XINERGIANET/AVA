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
        // No-master siempre ve su propia sede: se ignora cualquier location_id
        // que venga en el request para que no pueda mirar cifras de otra sede.
        $locationId = $isMaster ? $request->input('location_id') : $user->location_id;

        $locations = $isMaster
            ? Location::where('deleted', 0)->get()
            : Location::where('id', $user->location_id)->get();

        // Base Queries with location filter
        $salesQuery = Sale::whereYear('date', $thisYear)->where('deleted', 0);
        $ventasHoyQuery = Sale::whereDate('date', $today->format('Y-m-d'))->where('deleted', 0);
        $gastosQuery = DB::table('transactions')->where('type', 'scc')->whereYear('date', $thisYear);
        $cajaQuery = DB::table('cash_closes')->whereYear('created_at', $thisYear);
        
        $paymentsQuery = DB::table('payments')
            ->whereYear('payments.created_at', $thisYear)
            ->where('payments.deleted', 0)
            ->join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id');

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
            $paymentsQuery->join('sales', 'payments.sale_id', '=', 'sales.id')->where('sales.location_id', $locationId);
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
            'thisYear', 'thisMonth', 'locationId', 'locations',
            'ventasTotalesMes', 'ventasHoy', 'cantidadVentasHoy',
            'gastosTotales', 'ingresosCaja', 'rentabilidad', 'balanceActual', 'rentabilidadPorcentaje',
            'efectivo', 'yapePlin', 'transferencias', 'creditos',
            'chartGalones', 'chartVentasTeoricas'
        ));
    }
}
