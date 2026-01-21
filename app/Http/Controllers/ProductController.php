<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Location;
use App\Models\LocationPrice;
use App\Models\Tank;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\AgreementDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('location_prices')->where('deleted', 0)->paginate(15);
        $locations = Location::where('deleted', 0) ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
            $query->where('id', auth()->user()->location_id);
        })->get();
        return view('products.index', compact('products', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateProduct($request);

        try {

            $prices = $validatedData['unit_price'];
            unset($validatedData['unit_price']);
            $product = Product::create(array_merge($validatedData, ['deleted' => 0]));

            foreach ($prices as $locationId => $price) {
                if ($price !== null && $price !== '') {
                    LocationPrice::create([
                        'product_id' => $product->id,
                        'location_id' => $locationId,
                        'unit_price' => $price,
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Producto Guardado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al guardar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *  
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $this->validateProduct($request);

        $prices = $validatedData['unit_price'];
        unset($validatedData['unit_price']);

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        foreach ($prices as $locationId => $price) {
            if ($price !== null && $price !== '') {
                LocationPrice::upsert(
                    [
                        'product_id' => $id,
                        'location_id' => $locationId,
                        'unit_price' => $price,
                        'updated_at' => now(),
                    ],
                    ['product_id', 'location_id'],
                    ['unit_price', 'updated_at']
                );
            }
        }

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['deleted' => 1]); // Cambiar estado a 1 (eliminado)

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    protected function validateProduct(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'measurement_unit' => 'nullable|string|max:50',
            'unit_price' => 'required|array',
            'unit_price.*' => 'nullable|numeric|min:0.01', // o 'required|numeric|min:0.01' si todos son obligatorios
        ]);
    }

    public function getProductsBySede(Request $request)
    {
        Log::info('=== INICIO getProductsBySede ===');
        
        try {
            // Obtener la ubicación del usuario autenticado directamente
            $userLocation = auth()->user()->location;
            Log::info('Location del usuario: ' . $userLocation);

            if (!$userLocation) {
                Log::info('Usuario sin ubicación asignada');
                return response()->json([]);
            }

            // Decodificar el JSON y obtener el ID
            $locationData = json_decode($userLocation, true);
            $locationId = $locationData['id'] ?? null;
            
            Log::info('ID de la ubicación extraído: ' . $locationId);

            if (!$locationId) {
                Log::info('No se pudo extraer el ID de la ubicación');
                return response()->json([]);
            }

            // Obtener todos los tanques de la ubicación con producto relacionado
            $tanks = Tank::where('location_id', $locationId)
                ->where('deleted', '0')
                ->where('stored_quantity', '>', 0)
                ->whereNotNull('product_id')
                ->with('product')
                ->get();

            Log::info('Tanques encontrados: ' . $tanks->count());

            if ($tanks->isEmpty()) {
                Log::info('No hay tanques con stock disponible');
                return response()->json([]);
            }

            $result = [];

            foreach ($tanks as $tank) {
                Log::info('Procesando tanque ID: ' . $tank->id . ' con producto ID: ' . $tank->product_id);

                $product = $tank->product;
                if (!$product || $product->deleted == 1) {
                    Log::info('Producto no encontrado o eliminado para ID: ' . $tank->product_id);
                    continue;
                }

                // Precio por ubicación (fallback 0)
                $locationPrice = LocationPrice::where('location_id', $locationId)
                    ->where('product_id', $product->id)
                    ->first(['unit_price']);

                $price = $locationPrice ? $locationPrice->unit_price : 0;

                // Construir la estructura de producto que espera la vista
                $prodItem = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $price,
                    'stock' => $tank->stored_quantity ?? 0,
                    'measurement_unit' => $product->measurement_unit ?? '',
                    'observations' => $product->observations ?? ''
                ];

                $result[] = [
                    'id' => $tank->id,
                    'name' => $tank->name,
                    'capacity' => $tank->capacity,
                    'stored_quantity' => $tank->stored_quantity,
                    'products' => [$prodItem]
                ];
            }

            Log::info('Total tanques para retornar: ' . count($result));
            Log::info('=== FIN getProductsBySede ===');

            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('ERROR en getProductsBySede: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function getProductsByOrder($orderId)
    {
        Log::info('=== INICIO getProductsByOrder para order ID: ' . $orderId . ' ===');
        
        try {
            // Obtener la orden
            $order = Order::where('id', $orderId)
                ->first(['id', 'number', 'date', 'agreement_id']);
                
            if (!$order) {
                Log::info('Orden no encontrada');
                return response()->json(['error' => 'Orden no encontrada'], 404);
            }

            // Obtener la ubicación del usuario autenticado
            $userLocation = auth()->user()->location;
            
            if (!$userLocation) {
                Log::info('Usuario sin ubicación asignada');
                return response()->json([
                    'order' => $order,
                    'tanks' => []
                ]);
            }

            // Decodificar el JSON y obtener el ID
            $locationData = json_decode($userLocation, true);
            $locationId = $locationData['id'] ?? null;
            
            if (!$locationId) {
                Log::info('No se pudo extraer el ID de la ubicación');
                return response()->json([
                    'order' => $order,
                    'tanks' => []
                ]);
            }

            // Obtener productos de los detalles de la orden
            $orderDetails = OrderDetail::where('order_id', $orderId)
                ->with('product')
                ->get();

            Log::info('Detalles de orden encontrados: ' . $orderDetails->count());

            if ($orderDetails->isEmpty()) {
                Log::info('No hay productos en esta orden');
                return response()->json([
                    'order' => $order,
                    'tanks' => []
                ]);
            }

            $tanksResult = [];
            
            foreach ($orderDetails as $detail) {
                if (!$detail->product || $detail->product->deleted == 1) {
                    continue;
                }

                // Para contratos/órdenes, el stock disponible en el contrato es el remaining
                $contractStock = $detail->remaining ?? 0;
                
                // Solo incluir si hay stock disponible en el contrato
                if ($contractStock <= 0) {
                    Log::info('Sin stock disponible en contrato para producto: ' . $detail->product->name);
                    continue;
                }

                // Obtener el precio del contrato (agreement_detail)
                $agreementDetail = null;
                $price = 0;
                
                if ($order->agreement_id) {
                    $agreementDetail = AgreementDetail::where('agreement_id', $order->agreement_id)
                        ->where('product_id', $detail->product_id)
                        ->first(['unit_price']);
                    
                    $price = $agreementDetail ? $agreementDetail->unit_price : 0;
                }
                
                // Si no encontramos precio del contrato, usar precio por ubicación como fallback
                if ($price == 0) {
                    $locationPrice = LocationPrice::where('location_id', $locationId)
                        ->where('product_id', $detail->product_id)
                        ->first(['unit_price']);
                    
                    $price = $locationPrice ? $locationPrice->unit_price : 0;
                }

                // Buscar tanques con este producto en la ubicación actual
                $tanks = Tank::where('location_id', $locationId)
                    ->where('product_id', $detail->product_id)
                    ->where('deleted', '0')
                    ->where('stored_quantity', '>', 0)
                    ->get();

                Log::info('Tanques encontrados para producto ' . $detail->product->name . ': ' . $tanks->count());

                foreach ($tanks as $tank) {
                    // El stock real del tanque
                    $tankStock = $tank->stored_quantity ?? 0;
                    
                    // El stock que se puede vender es el menor entre lo que hay en el tanque y lo que resta en el contrato
                    $availableStock = min($tankStock, $contractStock);
                    
                    if ($availableStock <= 0) {
                        continue;
                    }

                    $productData = [
                        'id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'price' => $price,
                        'stock' => $availableStock, // Stock disponible para vender
                        'contract_stock' => $contractStock, // Stock en el contrato
                        'tank_stock' => $tankStock, // Stock en el tanque
                        'measurement_unit' => $detail->product->measurement_unit ?? 'Galones',
                        'area' => $detail->area ?? '',
                        'observations' => '',
                        'order_detail_id' => $detail->id
                    ];

                    $tanksResult[] = [
                        'id' => $tank->id,
                        'name' => $tank->name,
                        'capacity' => $tank->capacity,
                        'stored_quantity' => $tankStock,
                        'product' => $productData
                    ];
                }
            }

            Log::info('Total tanques para retornar: ' . count($tanksResult));
            Log::info('=== FIN getProductsByOrder ===');

            return response()->json([
                'order' => $order,
                'tanks' => $tanksResult
            ]);
            
        } catch (\Exception $e) {
            Log::error('ERROR en getProductsByOrder: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
}
