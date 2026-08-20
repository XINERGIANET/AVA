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
use App\Models\Supplier;
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
        $isMaster = ($user->role && strtolower($user->role->nombre) === 'master');
        $activeLocationId = $user->location_id;

        $query = PurchasePlan::with(['location', 'supplier', 'user', 'reviewer', 'details.product', 'details.tank'])
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

        $allLocations = Location::where('deleted', 0)->orderBy('name', 'asc')->get();

        $locations = Location::where('deleted', 0)
            ->when($activeLocationId, fn($q) => $q->where('id', $activeLocationId))
            ->get();

        $suppliers = Supplier::where('deleted', '0')->orWhere('deleted', 0)->orderBy('company_name', 'asc')->get();

        // Cálculo de Dinero Total Disponible exclusivo para rol Master (Global o por sede filtrada)
        $masterMoneyData = null;
        if ($isMaster) {
            $filterLocationId = $request->location_id ?: 'all';
            $masterMoneyData = $this->calculateLocationFinances($filterLocationId, false);
            $selectedLocationName = ($filterLocationId !== 'all') 
                ? ($allLocations->firstWhere('id', $filterLocationId)->name ?? 'Sede')
                : 'Todas las Sedes';
            $masterMoneyData['scope_name'] = $selectedLocationName;
            $masterMoneyData['is_all'] = ($filterLocationId === 'all');

            // Detalle por cada una de las sedes para el modal
            $sedesBreakdown = [];
            $grandTotalMasterAvailable = 0;
            $grandTotalMasterVault = 0;

            foreach ($allLocations as $loc) {
                $f = $this->calculateLocationFinances($loc->id, false);
                $sedesBreakdown[] = [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'available_money' => $f['available_money'],
                    'vault_money' => $f['vault_money'],
                    'cash_money' => $f['cash_money'],
                    'payment_methods' => $f['payment_methods']
                ];
                $grandTotalMasterAvailable += $f['available_money'];
                $grandTotalMasterVault += $f['vault_money'];
            }
            $masterMoneyData['sedes_breakdown'] = $sedesBreakdown;
            $masterMoneyData['grand_total_available'] = $grandTotalMasterAvailable;
            $masterMoneyData['grand_total_vault'] = $grandTotalMasterVault;
        }

        return view('purchase_plans.index', compact(
            'plans',
            'locations',
            'suppliers',
            'totalPlans',
            'pendingPlans',
            'approvedPlans',
            'rejectedPlans',
            'confirmationRate',
            'avgCompliance',
            'isMaster',
            'masterMoneyData'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->role && strtolower($user->role->nombre) === 'master';

        $locations = Location::where('deleted', 0)->orderBy('name', 'asc')->get();
        $selectedLocationId = $request->location_id ?: ($user->location_id ?: ($locations->first() ? $locations->first()->id : null));

        // Calcular datos financieros de la sede seleccionada
        $sedeData = $this->calculateLocationFinances($selectedLocationId);
        $tanks = $sedeData['tanks'];
        $vaultMoney = $sedeData['vault_money'];
        $cashMoney = $sedeData['cash_money'];
        $paymentMethodsBreakdown = $sedeData['payment_methods'];
        $availableMoney = $sedeData['available_money'];

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
            
        // Proveedores activos
        $suppliers = Supplier::where('deleted', '0')->orWhere('deleted', 0)->orderBy('company_name', 'asc')->get();

        return view('purchase_plans.create', compact(
            'locations',
            'selectedLocationId',
            'suppliers',
            'tanks',
            'availableMoney',
            'vaultMoney',
            'cashMoney',
            'paymentMethodsBreakdown',
            'fuelProducts',
            'isMaster'
        ));
    }

    private function calculateLocationFinances($locationId, $withTanks = true)
    {
        $tanks = [];
        $vaultMoney = 0;
        $cashMoney = 0;
        $paymentMethodsBreakdown = [];
        $availableMoney = 0;

        if ($locationId === 'all') {
            // CONSOLIDADO DE TODAS LAS SEDES
            if ($withTanks) {
                $tanks = Tank::with(['product', 'location'])
                    ->where('deleted', 0)
                    ->whereNotNull('product_id')
                    ->get();
            }

            // 1. Dinero en Bóveda (Todas las sedes)
            $vaultMoney = (float) Isle::where('deleted', 0)->sum('vault');

            // 2. Dinero en Cajas (Todas las sedes)
            $generalCash = (float) Location::where('deleted', 0)->sum('cash_amount');
            $islesCash = (float) Isle::where('deleted', 0)->sum('cash_amount');
            $cashMoney = $generalCash + $islesCash;

            // 3. Métodos de pago globales
            $paymentMethodsBreakdown = PaymentMethod::where('deleted', 0)
                ->get()
                ->map(function ($pm) use ($cashMoney) {
                    if ($pm->id == 1 || mb_strtolower(trim($pm->name)) === 'efectivo') {
                        $total = $cashMoney;
                    } else {
                        $total = (float) Payment::where('payment_method_id', $pm->id)
                            ->where('deleted', 0)
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
        } elseif ($locationId) {
            // UNA SEDE ESPECÍFICA
            if ($withTanks) {
                $tanks = Tank::with(['product', 'location'])
                    ->where('location_id', $locationId)
                    ->where('deleted', 0)
                    ->whereNotNull('product_id')
                    ->get();
            }

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
                        // Sumar pagos de ese método en la sede
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
        }

        return [
            'tanks' => $tanks,
            'vault_money' => $vaultMoney,
            'cash_money' => $cashMoney,
            'payment_methods' => $paymentMethodsBreakdown,
            'available_money' => $availableMoney
        ];
    }

    public function getSedeInfo(Request $request)
    {
        $locationId = $request->location_id;
        if (!$locationId) {
            return response()->json(['success' => false, 'message' => 'Sede no especificada']);
        }

        $financeData = $this->calculateLocationFinances($locationId, true);

        return response()->json([
            'success' => true,
            'tanks' => $financeData['tanks'],
            'available_money' => $financeData['available_money'],
            'vault_money' => $financeData['vault_money'],
            'cash_money' => $financeData['cash_money'],
            'payment_methods' => $financeData['payment_methods']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
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
                'supplier_id' => $request->supplier_id,
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
        $plan = PurchasePlan::with(['location', 'supplier', 'user', 'reviewer', 'details.product', 'details.tank'])
            ->findOrFail($id);

        return view('purchase_plans.show', compact('plan'));
    }

    public function review(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->role || strtolower($user->role->nombre) !== 'master') {
            return back()->with('error', 'Acceso denegado: Únicamente el usuario Master puede autorizar o rechazar solicitudes de compra.');
        }

        $plan = PurchasePlan::findOrFail($id);
        $request->validate([
            'action' => 'required|in:approve,reject',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'manager_notes' => 'nullable|string',
            'approved_quantities' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $plan->status = 'approved';
                
                // Actualizar proveedor si se seleccionó uno o se cambió
                if ($request->has('supplier_id')) {
                    $plan->supplier_id = $request->supplier_id ?: null;
                }
                
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
            'justification_notes' => 'nullable|string',
            'voucher_files.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240'
        ]);

        DB::beginTransaction();
        try {
            $totalTarget = 0;
            $totalPurchased = 0;

            foreach ($request->purchased_quantities as $detailId => $purchasedQty) {
                $detail = PurchasePlanDetail::where('purchase_plan_id', $plan->id)->find($detailId);
                if ($detail) {
                    $purchasedQty = max(0, floatval($purchasedQty));
                    $detail->purchased_quantity = $purchasedQty;
                    $detail->save();

                    // La meta es la aprobada por gerencia (si no hubo ajuste, es la solicitada)
                    $target = $detail->approved_quantity !== null ? $detail->approved_quantity : $detail->requested_quantity;
                    $totalTarget += $target;
                    $totalPurchased += $purchasedQty;
                }
            }

            $compliance = $totalTarget > 0 ? round(($totalPurchased / $totalTarget) * 100, 2) : 100;
            $plan->compliance_percentage = $compliance;
            $plan->justification_notes = $request->justification_notes;

            // Procesar imágenes/archivos de vouchers o comprobantes
            $currentImages = is_array($plan->voucher_images) ? $plan->voucher_images : [];
            if ($request->hasFile('voucher_files')) {
                foreach ($request->file('voucher_files') as $file) {
                    $path = $file->store('purchase_vouchers', 'public');
                    $currentImages[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'uploaded_at' => Carbon::now()->toDateTimeString()
                    ];
                }
            }
            $plan->voucher_images = $currentImages;

            if ($compliance >= 100) {
                $plan->status = 'completed';
            } else {
                $plan->status = 'partially_completed';
            }

            $plan->save();

            DB::commit();
            return back()->with('success', 'Cantidades reales compradas y comprobantes actualizados correctamente. Eficacia: ' . $compliance . '%');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando compra real: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar compras reales: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $plan = PurchasePlan::with(['location', 'supplier', 'user', 'reviewer', 'details.product', 'details.tank'])
            ->findOrFail($id);

        $data = [
            'plan' => $plan,
            'title' => 'PLANIFICACIÓN DE COMPRA DE COMBUSTIBLE - ' . strtoupper($plan->location->name)
        ];

        $pdf = Pdf::loadView('purchase_plans.pdf', $data)->setPaper('A4', 'portrait');
        return $pdf->stream('planificacion_compra_' . $plan->id . '.pdf');
    }
}
