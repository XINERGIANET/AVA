<?php

namespace App\Http\Controllers;

use App\Exports\ClosingExport;
use App\Exports\ContractExport;
use App\Models\Agreement;
use App\Models\AgreementDetail;
use App\Models\Client;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SaleDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Svg\Tag\Rect;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;

        $client = Client::find($client_id);
        if ($client) {
            // Agrega el nombre al request usando merge
            $request->merge(['client_name' => $client->business_name]);
        }

        $location_id = $request->location_id;


        $contracts = Agreement::with(['agreement_details.product', 'orders.order_details.product', 'client', 'location'])
            ->where('type', 'contract')
            ->where('deleted', 0)
            ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
            ->when($client_id, fn($q) => $q->where('client_id', $client_id))
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
            ->paginate(10);

        $locations = Location::where('deleted', 0)
            ->get();

        return view('contracts.index', compact('contracts', 'locations'));
    }
    public function excel(Request $request)
    {
        Log::info('entrando a controller');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;

        $client = Client::find($client_id);
        if ($client) {
            // Agrega el nombre al request usando merge
            $request->merge(['client_name' => $client->business_name ? $client->business_name : $client->contact_name]);
        }

        $location_id = $request->location_id;
        $location = Location::find($location_id);
        if ($location) {
            // Agrega el nombre al request usando merge
            $request->merge(['location_name' => $location->name]);
        }
        $products = $request->products ? explode(',', $request->products) : null;
        $payment_day = $request->payment_day;
        $filename = 'contratos_historico.xlsx';

        Log::info('export a excel');

        return Excel::download(
            new ContractExport(
                $start_date,
                $end_date,
                $location_id,
                $client_id,
                $products,
                $payment_day
            ),
            $filename,
        );
    }

    public function pdf()
    {
        try {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            $client_id = request()->get('client_id');
            $location_id = request()->get('location_id');

            $client = Client::find($client_id);
            $location = Location::find($location_id);
            $contracts = Agreement::with(['agreement_details.product', 'orders.order_details.product', 'client', 'location'])
                ->where('type', 'contract')
                ->where('deleted', 0)
                ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
                ->when($client_id, fn($q) => $q->where('client_id', $client_id))
                ->when($location_id, fn($q) => $q->where('location_id', $location_id))
                ->get();

            $data = [
                "title" => "REPORTE DE CONTRATOS",
                "subtitle" => "LISTADO DE CONTRATOS",
                "contracts" => $contracts,
                "filters" => [
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "client" => $client ? ($client->business_name ? $client->business_name : $client->contact_name) : null,
                    "location" => $location ? $location->name : null,
                ]
            ];

            $pdf = Pdf::loadView('contracts.pdf.pdf_contracts', $data)->setPaper('A4', 'portrait');
            $filename = 'reporte_contratos' . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating PDF in CreditController@pdf:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => request()->all()
            ]);
            return response()->json(['status' => false, 'message' => 'Error generating PDF. Please try again later.'], 500);
        }
    }

    /**
     * Get products available in tanks by location
     *
     * @param int $locationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByLocation($locationId)
    {
        try {
            // Obtener productos únicos que tienen tanques en esta sede
            $products = Product::whereHas('tanks', function ($query) use ($locationId) {
                $query->where('location_id', $locationId)
                    ->where('deleted', 0);
            })
                ->where('deleted', 0)
                ->select('id', 'name')
                ->get();

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Error al obtener productos por sede:', [
                'location_id' => $locationId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Error al obtener productos'], 500);
        }
    }

    /**
     * Get orders for a specific agreement
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrders($id)
    {
        try {
            $agreement = Agreement::with([
                'orders' => function ($query) {
                    $query->where('deleted', 0);
                },
                'orders.order_details',
                'agreement_details.product'
            ])->findOrFail($id);

            // Construir productos con info de cantidades
            $productos = $agreement->agreement_details->map(function ($detail) use ($agreement) {
                $asignado = OrderDetail::whereHas('order', function ($q) use ($agreement) {
                    $q->where('agreement_id', $agreement->id)
                        ->where('deleted', 0);
                })
                    ->where('product_id', $detail->product_id)
                    ->sum('quantity');
                return [
                    'id' => $detail->product->id,
                    'name' => $detail->product->name,
                    'total_permitido' => $detail->quantity,
                    'total_asignado' => $asignado,
                    'total_restante' => max(0, $detail->quantity - $asignado),
                ];
            })->values();

            return response()->json([
                'orders' => $agreement->orders,
                'products' => $productos
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las órdenes'], 500);
        }
    }

    public function guardarOrderDetails(Request $request)
    {
        $validated = $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities'  => 'required|array',
            'quantities.*' => 'numeric|min:0.01',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $agreementId = $order->agreement_id;

        $errores = [];

        foreach ($validated['product_ids'] as $i => $productId) {
            $quantityToAdd = $validated['quantities'][$i];

            // Buscar el detalle del contrato para este producto
            $agreementDetail = AgreementDetail::where('agreement_id', $agreementId)
                ->where('product_id', $productId)
                ->first();

            if (!$agreementDetail) {
                $errores[] = "El producto seleccionado no pertenece al contrato.";
                continue;
            }

            // Sumar todas las cantidades ya asignadas en OrderDetail para este producto y contrato
            $cantidadAsignada = OrderDetail::whereHas('order', function ($q) use ($agreementId) {
                $q->where('agreement_id', $agreementId);
            })
                ->where('product_id', $productId)
                ->sum('quantity');

            // Verificar que no se supere el máximo permitido
            if (($cantidadAsignada + $quantityToAdd) > $agreementDetail->quantity) {
                $errores[] = "No puedes agregar más de {$agreementDetail->quantity} unidades de {$agreementDetail->product->name}. Ya asignadas: $cantidadAsignada.";
                continue;
            }

            // Crear el detalle de la orden
            if ($quantityToAdd > 0) {
                OrderDetail::create([
                    'order_id' => $validated['order_id'],
                    'product_id' => $productId,
                    'quantity' => $quantityToAdd,
                    'remaining' => $quantityToAdd
                ]);
            }
        }

        if (count($errores) > 0) {
            return response()->json([
                'message' => 'Algunos productos no se pudieron agregar.',
                'errors' => $errores,
                'toast_type' => 'error'
            ], 422);
        }

        return response()->json([
            'message' => 'Productos agregados correctamente a la orden',
            'toast_type' => 'success'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $agreements = Agreement::with(['agreement_details.product', 'orders.order_details.product', 'client'])
            ->where('type', 'contract')
            ->paginate(10);
        $products = Product::all();
        $areas = Location::all();
        $clients = Client::all();
        return view(
            'contracts.create',
            compact(
                'agreements',
                'products',
                'clients',
                'areas'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Debug: Log de todos los datos recibidos
        Log::info('Datos recibidos en AgreementController@store:', $request->all());

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'location_id' => 'required|exists:locations,id',
            'number' => 'required|string',
            'total' => 'required|numeric|min:0',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0',
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|numeric|min:0',
            'subtotals' => 'required|array',
            'subtotals.*' => 'nullable|numeric|min:0',
            'generate_orders' => 'nullable|boolean',
            'number_of_orders' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear el contrato (agreement)
            $agreement = Agreement::create([
                'client_id' => $validated['client_id'],
                'number' => $validated['number'],
                'location_id' => $validated['location_id'],
                'total' => $validated['total'],
                'total_pay' => $validated['total'],
                'type' => 'contract',
                'status' => 0,
                'date' => now(),
                'deleted' => false
            ]);

            // 2. Crear los detalles del contrato (agreement_details)
            for ($i = 0; $i < count($validated['product_ids']); $i++) {
                if ($validated['quantities'][$i]){

                    AgreementDetail::create([
                        'agreement_id' => $agreement->id,
                        'product_id' => $validated['product_ids'][$i],
                        'unit_price' => $validated['prices'][$i],
                        'quantity' => $validated['quantities'][$i],
                        'subtotal' => $validated['subtotals'][$i]
                    ]);
                    
                }
            }

            // 3. Si está marcado "Generar Órdenes", crear las órdenes
            if ($request->has('generate_orders') && $request->generate_orders) {
                $numberOfOrders = $validated['number_of_orders'] ?? 1;

                for ($j = 1; $j <= $numberOfOrders; $j++) {
                    Order::create([
                        'agreement_id' => $agreement->id,
                        'number' => 'ORD-' . $agreement->id . '-' . str_pad($j, 3, '0', STR_PAD_LEFT),
                        'date' => now(),
                        'deleted' => false
                    ]);
                }
            } else {
                Order::create([
                    'agreement_id' => $agreement->id,
                    'number' => 'ORD-' . $agreement->id . '-001',
                    'date' => now(),
                    'deleted' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato creado exitosamente.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error
            Log::error('Error al crear contrato:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar contrato.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeArea($id)
    {
        try {
            $merged = false;

            DB::transaction(function () use ($id, &$merged) {
                $detail = OrderDetail::lockForUpdate()->findOrFail($id);

                $siblingQuery = OrderDetail::lockForUpdate()
                    ->where('order_id', $detail->order_id)
                    ->whereNull('area')
                    ->where('id', '!=', $detail->id);

                // Asegurar que el merge sea solo del mismo producto (o ambos null)
                if (is_null($detail->product_id)) {
                    $siblingQuery->whereNull('product_id');
                } else {
                    $siblingQuery->where('product_id', $detail->product_id);
                }

                $sibling = $siblingQuery->first();

                if ($sibling) {
                    $sibling->quantity  = (float)$sibling->quantity  + (float)$detail->quantity;

                    if (!is_null($detail->remaining)) {
                        $sibling->remaining = (float)$sibling->remaining + (float)$detail->remaining;
                    }
                    $sibling->save();

                    $detail->delete();
                    $merged = true;
                } else {
                    $detail->area = null;
                    $detail->save();
                    $merged = false;
                }
            });

            return response()->json([
                'success' => true,
                'message' => $merged
                    ? 'Área eliminada y detalle fusionado correctamente.'
                    : 'Área eliminada correctamente.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar/fusionar área de detalle', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar/fusionar el área.'
            ], 500);
        }
    }

    public function removeOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->deleted = 1;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Orden eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la orden:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar la orden.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Agreement  $agreement
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        try {
            // Cargar relaciones necesarias: client (cliente) y location (sede)
            $contract = Agreement::with(['client', 'location'])->findOrFail($id);

            // Normalizar salida (evitar exposiciones innecesarias)
            $payload = [
                'id' => $contract->id,
                'number' => $contract->number,
                'client_id' => $contract->client_id,
                'location_id' => $contract->location_id,
                'total' => (float) ($contract->total ?? 0),
                'details' => $contract->agreement_details,
                'client' => $contract->client ? [
                    'id' => $contract->client->id,
                    'business_name' => $contract->client->business_name ?? $contract->client->contact_name ?? null,
                    'document' => $contract->client->document ?? null,
                ] : null,
                'location' => $contract->location ? [
                    'id' => $contract->location->id,
                    'name' => $contract->location->name,
                ] : null,
            ];

            return response()->json([
                'success' => true,
                'contract' => $payload
            ], 200);
        } catch (\Exception $e) {
            // Loguear si quieres: \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el contrato.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Agreement  $agreement
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Agreement $agreement)
    {
        return view('contracts.edit', compact('agreement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Agreement $agreement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validación básica
        $validated = $request->validate([
            'client_id' => 'nullable|integer|exists:clients,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'total' => 'nullable|numeric',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'prices_edit' => 'nullable|array',
            'quantities_edit' => 'nullable|array',
            'subtotals_edit' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $contract = Agreement::findOrFail($id);

            // Actualizar campos principales si vienen
            if (array_key_exists('client_id', $validated)) {
                $contract->client_id = $validated['client_id'];
            }
            if (array_key_exists('location_id', $validated)) {
                $contract->location_id = $validated['location_id'];
            }
            if (array_key_exists('total', $validated)) {
                $contract->total = $validated['total'];
            }
            $contract->save();

            // Borrar todos los detalles previos
            // Ajusta el nombre de la relación si es distinto (details, contractDetails, items, etc.)
            $contract->agreement_details()->delete();

            // Regenerar detalles
            $productIds = $validated['product_ids'] ?? [];
            $prices = $validated['prices_edit'] ?? [];
            $quantities = $validated['quantities_edit'] ?? [];
            $subtotals = $validated['subtotals_edit'] ?? [];

            foreach ($productIds as $i => $productId) {
                $price = isset($prices[$i]) ? floatval($prices[$i]) : 0;
                $qty = isset($quantities[$i]) ? floatval($quantities[$i]) : 0;
                $subtotal = isset($subtotals[$i]) ? floatval($subtotals[$i]) : ($price * $qty);

                // Omitir detalles sin cantidad positiva (ajusta según tu lógica)
                if ($productId && $qty > 0) {
                    $contract->agreement_details()->create([
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato actualizado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando contract: '.$e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el contrato.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Agreement  $agreement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Agreement $agreement)
    {
        $agreement->delete();

        return redirect()->route('agreements.index')
            ->with('success', 'Acuerdo eliminado exitosamente.');
    }

    public function getContractsByClient(Request $request)
    {
        Log::info('=== INICIO getAgreementsByClient ===');
        Log::info('Client ID: ' . $request->client_id . ', Type: ' . $request->type);

        try {
            $clientId = $request->client_id;
            $type = $request->type; // 'contrato' o 'credito'
            $userLocationId = auth()->user()->location_id;

            if (!$clientId || !$type) {
                return response()->json([]);
            }

            $agreements = Agreement::where('client_id', $clientId)
                ->where('location_id', $userLocationId)
                ->where('type', $type === 'contrato' ? 'contract' : 'credit')
                ->where('deleted', 0)
                ->select('id', 'total', 'date', 'status')
                ->get();

            Log::info('Agreements encontrados: ' . $agreements->count());

            return response()->json($agreements);
        } catch (\Exception $e) {
            Log::error('ERROR en getAgreementsByClient: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function getOrdersByContract(Request $request)
    {
        Log::info('=== INICIO getOrdersByAgreement ===');
        Log::info('Agreement ID: ' . $request->agreement_id);

        try {
            $agreementId = $request->agreement_id;

            if (!$agreementId) {
                return response()->json([]);
            }

            $agreement = Agreement::where('id', $agreementId)
                ->where('deleted', 0)
                ->first();

            if (!$agreement) {
                return response()->json(['error' => 'Contrato no encontrado'], 404);
            }

            $orders = Order::where('agreement_id', $agreementId)
                ->where('deleted', 0)
                ->with(['order_details.product'])
                ->select('id', 'number', 'date')
                ->get();

            // Obtener los precios del acuerdo
            $agreementDetails = AgreementDetail::where('agreement_id', $agreementId)
                ->get()
                ->keyBy('product_id');

            // Calcular el total para cada orden sumando sus detalles
            $ordersWithTotal = $orders->map(function ($order) use ($agreementDetails) {
                $total = $order->order_details->sum(function ($detail) use ($agreementDetails) {
                    $agreementDetail = $agreementDetails->get($detail->product_id);
                    $unitPrice = $agreementDetail ? $agreementDetail->unit_price : 0;
                    return $detail->quantity * $unitPrice;
                });

                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'date' => $order->date,
                    'total' => $total
                ];
            });

            Log::info('Órdenes encontradas: ' . $orders->count());

            return response()->json([
                'agreement' => $agreement,
                'orders' => $ordersWithTotal
            ]);
        } catch (\Exception $e) {
            Log::error('ERROR en getOrdersByAgreement: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function getProductsByContract(Request $request)
    {
        Log::info('=== INICIO getProductsByAgreement ===');
        Log::info('Agreement ID: ' . $request->agreement_id);

        try {
            $agreementId = $request->agreement_id;

            if (!$agreementId) {
                return response()->json([]);
            }

            $agreement = Agreement::with(['agreement_details.product'])
                ->where('id', $agreementId)
                ->where('deleted', 0)
                ->first();

            if (!$agreement) {
                return response()->json(['error' => 'Contrato no encontrado'], 404);
            }

            $products = collect();

            foreach ($agreement->agreement_details as $detail) {
                if ($detail->product && $detail->product->deleted == 0) {

                    // Calcular stock disponible
                    $cantidadUsada = OrderDetail::whereHas('order', function ($q) use ($agreement) {
                        $q->where('agreement_id', $agreement->id);
                    })
                        ->where('product_id', $detail->product_id)
                        ->sum('quantity');

                    $stockDisponible = max(0, $detail->quantity - $cantidadUsada);

                    // Solo agregar si hay stock disponible
                    if ($stockDisponible > 0) {
                        $products->push([
                            'id' => $detail->product->id,
                            'name' => $detail->product->name,
                            'price' => $detail->unit_price,
                            'stock' => $stockDisponible,
                            'total_contrato' => $detail->quantity,
                            'usado' => $cantidadUsada,
                            'measurement_unit' => $detail->product->measurement_unit ?? 'Galones',
                            'observations' => '',
                            'agreement_id' => $agreement->id
                        ]);
                    }
                }
            }

            Log::info('Productos encontrados: ' . $products->count());

            return response()->json([
                'agreement' => $agreement,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('ERROR en getProductsByAgreement: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    
    public function details_modal(Request $request, $id)
    {
        try {

            $orderIds = Order::where('agreement_id', $id)->pluck('id');
            $orderDetailIds = OrderDetail::whereIn('order_id', $orderIds)->pluck('id');

            $saleDetails = SaleDetail::with([
                'sale',
                'order_detail.order.agreement.location',
                'order_detail.product'
            ])->whereIn('order_detail_id', $orderDetailIds)
              ->get();

            $html = view('contracts.detail_modal', compact('saleDetails'))->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        } catch (\Exception $e) {
            Log::error('Error loading contract modal: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function generate_closing(Request $request)
    {
        $validated = $request->validate([
            'agreement_id' => 'required|integer|exists:agreements,id',
            'details' => 'required|array|min:1',
            'details.*.sale_id' => 'required|integer|exists:sales,id',
            'details.*.product_id' => 'required|integer|exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $details = collect($validated['details']);
            $groups = $details->groupBy('sale_id');
            $agreement_id = $validated['agreement_id'];
            $result = [];

            foreach ($groups as $saleId => $items) {
                $saleId = (int) $saleId;
                // calcular nuevo closing_number = max(existing) + 1
                $max = SaleDetail::whereHas('order_detail.order', function ($q) use ($agreement_id) {
                    $q->where('agreement_id', $agreement_id);
                })->max('closing_number');
                $newClosing = ((int) ($max ?? 0)) + 1;

                // obtener product_ids únicos enviados para esta venta
                $productIds = $items->pluck('product_id')->unique()->values()->all();

                // actualizar todos los sale_details que coincidan sale_id + product_id
                $updated = SaleDetail::where('sale_id', $saleId)
                    ->whereIn('product_id', $productIds)
                    ->update([
                        'closing_number' => $newClosing,
                    ]);

                $result[] = [
                    'sale_id' => $saleId,
                    'closing_number_assigned' => $newClosing,
                    'updated_rows' => $updated,
                    'product_ids' => $productIds,
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cierre generado.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en generate_closing_from_details: '.$e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al generar cierre.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export_closing_excel(Request $request)
    {
        $validated = $request->validate([
            'closing_number' => 'required|integer',
            'agreement_id'   => 'required|integer|exists:agreements,id',
        ]);

        try {
            $closingNumber = $validated['closing_number'];
            $agreementId   = $validated['agreement_id'];
            $export = new ClosingExport($closingNumber, $agreementId);
            $filename = sprintf('cierre_%d_%d_%s.xlsx', $agreementId, $closingNumber, date('Ymd_His'));

            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            Log::error('Error exporting closing to Excel: '.$e->getMessage(), ['request' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Error al exportar a Excel.'], 500);
        }
    }
}
