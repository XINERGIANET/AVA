<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::select('id', 'oac_id', 'prod1', 'cantidad1', 'prod2', 'cantidad2', 'prod3', 'cantidad3', 'estado', 'created_at', 'updated_at')
            ->where('estado', 0)
            ->get();
        return response()->json($orders);
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
            $order = Order::with(['order_details' => function($query) {
                    $query->with('product')
                          ->orderByRaw('CASE WHEN area IS NULL THEN 1 ELSE 0 END, area ASC');
                }])
                ->where('deleted', 0)
                ->findOrFail($id);

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la orden'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|integer|exists:agreements,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'cantidad' => 'required|array',
            'cantidad.*' => 'numeric|min:0.01',
            'number_of_orders' => 'nullable|integer|min:1'
        ]);

        $orders = [];
        $contratoId = $validated['contrato_id'];
        $numberOfOrders = $request->input('number_of_orders', 1);

        // Obtener el último número correlativo para este contrato
        $lastOrder = Order::where('agreement_id', $contratoId)
            ->orderByDesc('id')
            ->first();
        $lastCorrelative = 0;
        if ($lastOrder && preg_match('/ORD-' . $contratoId . '-(\d{3})/', $lastOrder->number, $matches)) {
            $lastCorrelative = intval($matches[1]);
        }

        for ($j = 1; $j <= $numberOfOrders; $j++) {
            $correlative = $lastCorrelative + $j;
            $numeroOrden = 'ORD-' . $contratoId . '-' . str_pad($correlative, 3, '0', STR_PAD_LEFT);

            $order = Order::create([
                'agreement_id' => $contratoId,
                'number' => $numeroOrden,
                'date' => now(),
                'estado' => 0,
            ]);

            // Guardar los productos asociados a la orden SOLO en la primera orden (ajusta según tu lógica)
            if ($j === 1) {
                foreach ($validated['product_ids'] as $i => $productId) {
                    $qty = $validated['cantidad'][$productId] ?? 0;
                    if ($qty > 0) {
                        $order->order_details()->create([
                            'product_id' => $productId,
                            'quantity' => $qty,
                            'remaining' => $qty,
                        ]);
                    }
                }
            }

            $orders[] = $order->load('order_details.product');
        }

        return response()->json([
            'success' => true,
            'message' => 'Órdenes registradas correctamente.',
            'orders' => $orders
        ]);
    }

    /**
     * Store area assignment for order details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'area' => 'required|string',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);
            
            // Buscar si ya existe un OrderDetail para este producto sin área asignada
            $existingDetail = $order->order_details()
                ->where('product_id', $validated['product_id'])
                ->whereNull('area')
                ->first();

            if (!$existingDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró cantidad disponible para este producto en la orden.'
                ], 422);
            }

            $cantidadDisponible = $existingDetail->quantity;
            $cantidadParaArea = $validated['quantity'];

            if ($cantidadParaArea > $cantidadDisponible) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$cantidadDisponible} unidades disponibles. No se pueden asignar {$cantidadParaArea}."
                ], 422);
            }

            // 1. Crear nuevo OrderDetail con el área asignada
            $order->order_details()->create([
                'product_id' => $validated['product_id'],
                'area' => $validated['area'],
                'quantity' => $cantidadParaArea,
                'remaining' => $cantidadParaArea
            ]);

            // 2. Actualizar el OrderDetail existente con la cantidad restante
            $cantidadRestante = $cantidadDisponible - $cantidadParaArea;
            
            if ($cantidadRestante > 0) {
                $existingDetail->update([
                    'quantity' => $cantidadRestante,
                    'remaining' => $cantidadRestante
                ]);
            } else {
                // Si no queda nada, eliminar el detalle sin área
                $existingDetail->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Área asignada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar área: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $order = Order::select('id', 'oac_id', 'prod1', 'cantidad1', 'prod2', 'cantidad2', 'prod3', 'cantidad3', 'estado', 'created_at', 'updated_at')
            ->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'oac_id' => 'required|integer',
            'prod1' => 'required|string',
            'cantidad1' => 'required|integer',
            'prod2' => 'nullable|string',
            'cantidad2' => 'nullable|integer',
            'prod3' => 'nullable|string',
            'cantidad3' => 'nullable|integer',
        ]);

        $order = Order::findOrFail($id);
        $order->update($request->all());

        return response()->json(['success' => true, 'message' => 'Orden actualizada exitosamente.', 'order' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['estado' => 1]);

        return response()->json(['success' => true, 'message' => 'Orden eliminada correctamente.']);
    }
}
