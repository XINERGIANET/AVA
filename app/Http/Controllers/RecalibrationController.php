<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Tank;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Isle;
use App\Models\Pump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecalibrationController extends Controller
{

    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $number = $request->number;
        $paymentmethod = $request->payment_method_id;
        $client_id = $request->client_id;
        $type_sale = $request->type_sale;
        $voucher_type = $request->voucher_type;
        $user_id = $request->user_id;

        $client = Client::find($client_id);
        if ($client) {
            // Agrega el nombre al request usando merge
            $request->merge(['client_name' => $client->business_name ? $client->business_name : $client->contact_name]);
        }

        $paymentMethods = PaymentMethod::where('deleted', false)->get();
        $locations = Location::where('deleted', false)->get();

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        // Filtrar usuarios según el rol
        if ($isMaster) {
            // Master ve todos los usuarios
            $users = User::where('deleted', false)->get();
        } else {
            // Admin u otros solo ven workers de su sede
            $users = User::whereHas('role', function ($q) {
                $q->where('nombre', 'worker');
            })
                ->where('location_id', $currentUser->location_id)
                ->where('deleted', false)
                ->get();
        }

        $query = Sale::with(['payments.payment_method', 'sale_details', 'location', 'payments'])
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('date', '>=', $start_date);
            })
            ->whereHas('payments', function ($q) {
                $q->where('status', 'paid')
                    ->where('deleted', false);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('date', '<=', $end_date);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('location_id', $location_id);
            })
            ->when($number, function ($query) use ($number) {
                $query->whereHas('payments', function ($q) use ($number) {
                    $q->where('number', 'like', '%' . $number . '%');
                });
            })
            ->when($paymentmethod, function ($query) use ($paymentmethod) {
                $query->whereHas('payments', function ($q) use ($paymentmethod) {
                    $q->where('payment_method_id', $paymentmethod);
                });
            })
            ->when($client_id, function ($query) use ($client_id) {
                $query->where('client_id', $client_id);
            })
            ->when(!is_null($type_sale) && $type_sale !== '' && $type_sale !== 'all', function ($query) use ($type_sale) {
                $query->where('type_sale', (int)$type_sale);
            })
            ->when($voucher_type, function ($query) use ($voucher_type) {
                $query->whereHas('payments', function ($q) use ($voucher_type) {
                    $q->where('voucher_type', $voucher_type);
                });
            })
            ->when($user_id, function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->where('deleted', false)
            ->where('vehicle_plate', '=', '0-0')
            ->orderBy('date', 'desc');

        // Aplicar filtro de sede para usuarios no master
        if (auth()->user()->role->nombre != 'master' && auth()->user()->location_id) {
            $query->where('location_id', auth()->user()->location_id);
        }

        // Calcular el total de ventas directas y creditos()(type_sale = 0, 2)
        $totalQuery = clone $query;
        // Sumar todas las ventas directas (type_sale = 0)
        $totalDirectas = (clone $totalQuery)
            ->where('type_sale', 0)
            ->sum('total');

        // Sumar solo los créditos completamente pagados (type_sale = 2)
        // Un crédito está pagado cuando la suma de payments con status='paid' >= total de la venta
        $totalCreditosPagados = (clone $totalQuery)
            ->where('type_sale', 2)
            ->get()
            ->filter(function ($sale) {
                $totalPagado = Payment::where('sale_id', $sale->id)
                    ->where('status', 'paid')
                    ->where('deleted', false)
                    ->sum('amount');
                return $totalPagado >= $sale->total && $totalPagado > 0;
            })
            ->sum('total');

        $total = $totalDirectas + $totalCreditosPagados;

        $sales = $query->paginate(20);

        return view('recalibration.index', compact('sales', 'paymentMethods', 'locations', 'users', 'total', 'isMaster'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id)
    {
        try {
            $sale = Sale::with(['sale_details.product'])->findOrFail($id);
            $adicional = ($sale->adicional) ? $sale->adicional : 0;
            $isDirectSale = ($sale->type_sale === 0);

            $productos = $sale->sale_details->map(function ($detail) use ($isDirectSale) {
                $priceToShow = $isDirectSale 
                    ? $detail->unit_price 
                    : ($detail->discounted_price ?? $detail->unit_price);

                return [
                    'id' => $detail->id, // Por si acaso
                    'product_id' => $detail->product_id,
                    'pump_id' => $detail->pump_id, // <--- AGREGADO: Punto de partida para el stock
                    'name' => $detail->product->name ?? 'Producto',
                    'quantity' => round($detail->quantity, 3),
                    'unit_price' => round($priceToShow, 2),
                    'subtotal' => round($detail->subtotal, 2),
                ];
            });

            return response()->json([
                'status' => true,
                'productos' => $productos,
                'adicional' => number_format($adicional, 2),
                'sale_id' => $sale->id,
                'total' => number_format($sale->total, 2)
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateQuantities(Request $request)
    {
        $saleId = $request->input('sale_id');
        $quantities = $request->input('quantities');

        // Validación de seguridad básica
        if (!$quantities) {
            return response()->json(['status' => false, 'message' => 'No hay datos para actualizar']);
        }

        try {
            DB::beginTransaction();

            foreach ($quantities as $productId => $newQuantity) {
                if ($productId == 'null' || empty($productId)) continue;

                $newQuantity = floatval($newQuantity);

                // 1. Buscamos el detalle actual para obtener pump_id y cantidad vieja
                $detail = DB::table('sale_details')
                    ->where('sale_id', $saleId)
                    ->where('product_id', $productId)
                    ->first();

                if ($detail && $detail->pump_id) {
                    $oldQuantity = floatval($detail->quantity);
                    $diff = $newQuantity - $oldQuantity;

                    if ($diff != 0) {
                        // --- PASO B: RUTA HACIA EL TANQUE ---
                        
                        // A. En PUMPS buscamos isle_id y product_id
                        $pump = DB::table('pumps')->where('id', $detail->pump_id)->first();
                        
                        if ($pump) {
                            $isleId = $pump->isle_id;
                            $p_id = $pump->product_id; // Guardado momentáneamente

                            // B. En ISLES buscamos el location_id
                            $isle = DB::table('isles')->where('id', $isleId)->first();

                            if ($isle) {
                                $locationId = $isle->location_id;

                                // C. En TANKS buscamos por location_id y product_id
                                $tank = DB::table('tanks')
                                    ->where('location_id', $locationId)
                                    ->where('product_id', $p_id)
                                    ->first();

                                if ($tank) {
                                    // D. Modificamos el stock en stored_quantity
                                    DB::table('tanks')->where('id', $tank->id)->update([
                                        'stored_quantity' => $tank->stored_quantity - $diff
                                    ]);
                                }
                            }
                        }

                        // --- PASO C: ACTUALIZAR EL DETALLE DE VENTA ---
                        DB::table('sale_details')
                            ->where('sale_id', $saleId)
                            ->where('product_id', $productId)
                            ->update([
                                'quantity' => $newQuantity,
                                'subtotal' => $detail->unit_price * $newQuantity
                            ]);
                    }
                }
            }

            // El total de la venta no se recalcula para mantenerlo fijo
            // $nuevoTotal = DB::table('sale_details')
            //     ->where('sale_id', $saleId)
            //     ->where('deleted', 0)
            //     ->sum('subtotal');

            // DB::table('sales')->where('id', $saleId)->update(['total' => $nuevoTotal]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Cantidades actualizadas correctamente. El total permanece igual.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
