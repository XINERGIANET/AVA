<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\PurchasePlan;
use App\Models\PurchasePlanDetail;
use App\Models\Tank;
use App\Models\Isle;
use App\Models\PaymentMethod;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchasePlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMasterOrManager = in_array($user->role->nombre, ['master', 'gerente', 'admin']);
        $activeLocationId = $user->location_id;

        $query = PurchasePlan::with(['location', 'user', 'reviewer', 'details.product', 'details.tank'])
            ->where('deleted', 0)
            ->when($activeLocationId, function ($q) use ($activeLocationId) {
                $q->where('location_id', $activeLocationId);
            })
            ->when($request->location_id, fn($q, $loc) => $q->where('location_id', $loc))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->start_date, fn($q, $date) => $q->whereDate('scheduled_date', '>=', $date))
            ->when($request->end_date, fn($q, $date) => $q->whereDate('scheduled_date', '<=', $date))
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('id', 'desc');

        $plans = $query->paginate(15)->withQueryString();

        // Cálculo de KPIs filtrados por la sede activa
        $baseStatsQuery = PurchasePlan::where('deleted', 0)
            ->when($activeLocationId, function ($q) use ($activeLocationId) {
                $q->where('location_id', $activeLocationId);
            });

        $totalPlans = (clone $baseStatsQuery)->count();
        $pendingPlans = (clone $baseStatsQuery)->where('status', 'pending')->count();
        $approvedPlans = (clone $baseStatsQuery)->whereIn('status', ['approved', 'completed', 'partially_completed'])->count();
        $rejectedPlans = (clone $baseStatsQuery)->where('status', 'rejected')->count();

        // Tasa de Confirmación/Aprobación por Gerencia
        $confirmationRate = $totalPlans > 0 ? round(($approvedPlans / $totalPlans) * 100, 1) : 0;

        // Eficacia promedio de compras finalizadas
        $completedPlans = (clone $baseStatsQuery)->whereIn('status', ['completed', 'partially_completed'])->with('details')->get();
        $avgCompliance = 0;
        if ($completedPlans->isNotEmpty()) {
            $sumCompliance = $completedPlans->sum(fn($p) => $p->effective_compliance);
            $avgCompliance = round($sumCompliance / $completedPlans->count(), 1);
        }

        $locations = Location::where('deleted', 0)
            ->when($activeLocationId, fn($q) => $q->where('id', $activeLocationId))
            ->get();

        return view('purchase_plans.index', compact(
            'plans',
            'locations',
            'totalPlans',
            'pendingPlans',
            'approvedPlans',
            'rejectedPlans',
            'confirmationRate',
            'avgCompliance',
            'isMasterOrManager'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $locations = Location::where('deleted', 0)
            ->when($user->role->nombre !== 'master' && $user->location_id, fn($q) => $q->where('id', $user->location_id))
            ->get();

        $selectedLocationId = $request->location_id ?: ($user->location_id ?: ($locations->first() ? $locations->first()->id : null));

        // Obtener Tanques, Dinero y Métodos de Pago de la sede
        $tanks = [];
        $vaultMoney = 0;
        $cashMoney = 0;
        $paymentMethodsBreakdown = [];

        if ($selectedLocationId) {
            $tanks = Tank::with('product')
                ->where('location_id', $selectedLocationId)
                ->where('deleted', 0)
                ->whereNotNull('product_id')
                ->get();

            // 1. Dinero en Bóveda
            $vaultMoney = (float) Isle::where('location_id', $selectedLocationId)
                ->where('deleted', 0)
                ->sum('vault');

            // 2. Dinero en Cajas de la sede (saldo actual en islas + caja general)
            $locationObj = Location::find($selectedLocationId);
            $generalCash = $locationObj ? (float)$locationObj->cash_amount : 0;
            $islesCash = (float) Isle::where('location_id', $selectedLocationId)
                ->where('deleted', 0)
                ->sum('cash_amount');
            $cashMoney = $generalCash + $islesCash;

            // 3. Montos por Métodos de Pago acumulados en la sede
            $paymentMethodsBreakdown = PaymentMethod::where('deleted', 0)
                ->where(function ($q) use ($selectedLocationId) {
                    $q->whereNull('location_id')
                      ->orWhere('location_id', $selectedLocationId);
                })
                ->get()
                ->map(function ($pm) use ($selectedLocationId, $cashMoney) {
                    if ($pm->id == 1 || mb_strtolower(trim($pm->name)) === 'efectivo') {
                        $total = $cashMoney;
                    } else {
                        // Sumar pagos de ese método en la sede
                        $total = (float) Payment::where('payment_method_id', $pm->id)
                            ->where('deleted', 0)
                            ->where(function ($q) use ($selectedLocationId) {
                                $q->whereHas('sale', fn($q2) => $q2->where('location_id', $selectedLocationId))
                                  ->orWhereHas('agreement', fn($q2) => $q2->where('location_id', $selectedLocationId));
                            })
                            ->sum('amount');
                    }

                    return [
                        'id' => $pm->id,
                        'name' => $pm->name,
                        'amount' => $total
                    ];
                })
                ->values()
                ->toArray();

            // Suma total de todos los métodos de pago
            $totalPaymentMethods = array_sum(array_column($paymentMethodsBreakdown, 'amount'));
            // Dinero Total Disponible = Bóveda + Total de todos los Métodos de Pago (Efectivo + Tarjeta + Yape + Transferencias...)
            $availableMoney = $vaultMoney + $totalPaymentMethods;
        }

        // Productos de combustible (categoría combustible o asignados a tanques)
        $fuelProducts = Product::where('deleted', 0)
            ->whereHas('tanks', function ($q) use ($selectedLocationId) {
                if ($selectedLocationId) {
                    $q->where('location_id', $selectedLocationId)->where('deleted', 0);
                }
            })
            ->orWhere(function ($q) {
                $q->where('name', 'LIKE', '%DIESEL%')
                  ->orWhere('name', 'LIKE', '%GASOHOL%')
                  ->orWhere('name', 'LIKE', '%GASOLINA%')
                  ->orWhere('name', 'LIKE', '%GLP%')
                  ->orWhere('name', 'LIKE', '%GNV%');
            })
            ->distinct()
            ->get();

        return view('purchase_plans.create', compact(
            'locations',
            'selectedLocationId',
            'tanks',
            'availableMoney',
            'vaultMoney',
            'cashMoney',
            'paymentMethodsBreakdown',
            'fuelProducts'
        ));
    }

    public function getSedeInfo(Request $request)
    {
        $locationId = $request->location_id;
        if (!$locationId) {
            return response()->json(['success' => false, 'message' => 'Sede no especificada']);
        }

        $tanks = Tank::with('product')
            ->where('location_id', $locationId)
            ->where('deleted', 0)
            ->whereNotNull('product_id')
            ->get();

        // 1. Dinero en Bóveda
        $vaultMoney = (float) Isle::where('location_id', $locationId)
            ->where('deleted', 0)
            ->sum('vault');

        // 2. Dinero en Cajas de la sede (saldo actual en islas + caja general)
        $locationObj = Location::find($locationId);
        $generalCash = $locationObj ? (float)$locationObj->cash_amount : 0;
        $islesCash = (float) Isle::where('location_id', $locationId)
            ->where('deleted', 0)
            ->sum('cash_amount');
        $cashMoney = $generalCash + $islesCash;

        // 3. Montos por Métodos de Pago acumulados en la sede
        $paymentMethodsBreakdown = PaymentMethod::where('deleted', 0)
            ->where(function ($q) use ($locationId) {
                $q->whereNull('location_id')
                  ->orWhere('location_id', $locationId);
            })
            ->get()
            ->map(function ($pm) use ($locationId, $cashMoney) {
                if ($pm->id == 1 || mb_strtolower(trim($pm->name)) === 'efectivo') {
                    $total = $cashMoney;
                } else {
                    $total = (float) Payment::where('payment_method_id', $pm->id)
                        ->where('deleted', 0)
                        ->where(function ($q) use ($locationId) {
                            $q->whereHas('sale', fn($q2) => $q2->where('location_id', $locationId))
                              ->orWhereHas('agreement', fn($q2) => $q2->where('location_id', $locationId));
                        })
                        ->sum('amount');
                }

                return [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'amount' => $total
                ];
            })
            ->values()
            ->toArray();

        $totalPaymentMethods = array_sum(array_column($paymentMethodsBreakdown, 'amount'));
        $availableMoney = $vaultMoney + $totalPaymentMethods;

        return response()->json([
            'success' => true,
            'tanks' => $tanks,
            'availableMoney' => $availableMoney,
            'vaultMoney' => $vaultMoney,
            'cashMoney' => $cashMoney,
            'paymentMethods' => $paymentMethodsBreakdown
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'scheduled_date' => 'required|date',
            'available_money' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_quantity' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $plan = PurchasePlan::create([
                'location_id' => $request->location_id,
                'user_id' => Auth::id(),
                'scheduled_date' => $request->scheduled_date,
                'available_money' => $request->available_money,
                'status' => 'pending',
                'notes' => $request->notes,
                'deleted' => 0
            ]);

            foreach ($request->items as $item) {
                // Obtener stock actual del tanque si se especificó, sino calcular
                $currentStock = isset($item['current_stock']) ? $item['current_stock'] : 0;
                $tankId = isset($item['tank_id']) && !empty($item['tank_id']) ? $item['tank_id'] : null;

                if ($tankId && !$currentStock) {
                    $tank = Tank::find($tankId);
                    $currentStock = $tank ? $tank->stored_quantity : 0;
                }

                $unitPrice = isset($item['unit_price_estimate']) ? $item['unit_price_estimate'] : null;
                $estimatedTotal = $unitPrice ? ($unitPrice * $item['requested_quantity']) : null;

                PurchasePlanDetail::create([
                    'purchase_plan_id' => $plan->id,
                    'product_id' => $item['product_id'],
                    'tank_id' => $tankId,
                    'current_stock' => $currentStock,
                    'requested_quantity' => $item['requested_quantity'],
                    'unit_price_estimate' => $unitPrice,
                    'estimated_total' => $estimatedTotal,
                ]);
            }

            DB::commit();
            return redirect()->route('purchase_plans.index')->with('success', 'Planificación de compra registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrando purchase plan: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar la planificación: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $plan = PurchasePlan::with(['location', 'user', 'reviewer', 'details.product', 'details.tank'])
            ->findOrFail($id);

        return view('purchase_plans.show', compact('plan'));
    }

    public function review(Request $request, $id)
    {
        $plan = PurchasePlan::findOrFail($id);
        $request->validate([
            'action' => 'required|in:approve,reject',
            'manager_notes' => 'nullable|string',
            'approved_quantities' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            if ($request->action === 'approve') {
                $plan->status = 'approved';
                
                // Actualizar cantidades autorizadas por ítem
                if ($request->has('approved_quantities')) {
                    foreach ($request->approved_quantities as $detailId => $qty) {
                        $detail = PurchasePlanDetail::where('purchase_plan_id', $plan->id)->find($detailId);
                        if ($detail) {
                            $detail->approved_quantity = max(0, floatval($qty));
                            $detail->save();
                        }
                    }
                } else {
                    // Por defecto se aprueba lo solicitado
                    foreach ($plan->details as $detail) {
                        $detail->approved_quantity = $detail->requested_quantity;
                        $detail->save();
                    }
                }
            } else {
                $plan->status = 'rejected';
            }

            $plan->reviewed_by = $user->id;
            $plan->reviewed_at = Carbon::now();
            $plan->manager_notes = $request->manager_notes;
            $plan->save();

            DB::commit();
            return back()->with('success', 'Solicitud evaluada correctamente por gerencia.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error revisando purchase plan: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar la revisión: ' . $e->getMessage());
        }
    }

    public function updatePurchased(Request $request, $id)
    {
        $plan = PurchasePlan::with('details')->findOrFail($id);
        $request->validate([
            'purchased_quantities' => 'required|array',
            'justification_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $totalTarget = 0;
            $totalPurchased = 0;

            foreach ($request->purchased_quantities as $detailId => $qty) {
                $detail = PurchasePlanDetail::where('purchase_plan_id', $plan->id)->find($detailId);
                if ($detail) {
                    $purchasedQty = max(0, floatval($qty));
                    $detail->purchased_quantity = $purchasedQty;
                    $detail->save();

                    $target = $detail->approved_quantity !== null ? $detail->approved_quantity : $detail->requested_quantity;
                    $totalTarget += $target;
                    $totalPurchased += $purchasedQty;
                }
            }

            $compliance = $totalTarget > 0 ? round(($totalPurchased / $totalTarget) * 100, 2) : 100;
            $plan->compliance_percentage = $compliance;
            $plan->justification_notes = $request->justification_notes;

            if ($compliance >= 100) {
                $plan->status = 'completed';
            } else {
                $plan->status = 'partially_completed';
            }

            $plan->save();

            DB::commit();
            return back()->with('success', 'Cantidades reales compradas actualizadas correctamente. Eficacia: ' . $compliance . '%');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando compra real: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar compras reales: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $plan = PurchasePlan::with(['location', 'user', 'reviewer', 'details.product', 'details.tank'])
            ->findOrFail($id);

        $data = [
            'plan' => $plan,
            'title' => 'PLANIFICACIÓN DE COMPRA DE COMBUSTIBLE - ' . strtoupper($plan->location->name)
        ];

        $pdf = Pdf::loadView('purchase_plans.pdf', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('planificacion_compra_' . $plan->id . '.pdf');
    }
}
