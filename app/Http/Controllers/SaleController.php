<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Tank;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\LocationPrice;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Exports\SalesExport;
use App\Exports\SalesByIsleExport;
use App\Models\Agreement;
use App\Models\Isle;
use App\Models\Pump;
use App\Models\Measurement;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        //
        $user = auth()->user();

        // Filtrar clientes que tengan contratos o créditos en la sede del usuario
        $clients = Client::whereHas('agreements', function ($query) use ($user) {
            $query->where('location_id', $user->location_id)
                ->where('deleted', 0)
                ->whereIn('type', ['contract', 'credit']); // 1=contrato, 2=crédito
        })->get();

        $product_categories = Product::all();
        $payment_methods = PaymentMethod::where('deleted', 0)->get();

        // Obtener la sede del usuario autenticado
        $isWorker = ($user->role->nombre === 'worker');
        $hasAssignedIsle = !empty($user->isle_id);

        if ($isWorker && $hasAssignedIsle) {
            // Worker con isla asignada: solo mostrar su isla
            $isles = Isle::where('id', $user->isle_id)
                ->where('deleted', 0)
                ->get();

            // Solo bombas de la isla asignada
            $pumps = Pump::with('product')
                ->where('isle_id', $user->isle_id)
                ->where('deleted', 0)
                ->get();
        } else {
            // Admin/Master: mostrar todas las islas de la sede
            $isles = Isle::where('location_id', $user->location_id)
                ->where('deleted', 0)
                ->get();

            // Todas las bombas de todas las islas de la sede
            $isleIds = $isles->pluck('id')->toArray();
            $pumps = Pump::with('product')
                ->whereIn('isle_id', $isleIds)
                ->where('deleted', 0)
                ->get();
        }

        // Si el usuario tiene asignada una isla, obtenerla
        $assignedIsle = $user->isle_id ?? null;
        try {
            $assignedIsle = auth()->user()->isle_id ?? null;
        } catch (\Throwable $e) {
            $assignedIsle = null;
        }

        return view('sales.index', compact('product_categories', 'clients', 'payment_methods', 'isles', 'pumps', 'assignedIsle'));
    }

    public function historico(Request $request)
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
            ->where(function ($q) {
                $q->where('vehicle_plate', '!=', '0-0') 
                ->orWhereNull('vehicle_plate');       
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

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

        return view('sales.historico', compact('sales', 'paymentMethods', 'locations', 'users', 'total', 'isMaster'));
    }

    public function updateDate(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $sale = Sale::findOrFail($id);
        $user = auth()->user();

        if ($user->role->nombre !== 'master' && $user->location_id && $sale->location_id != $user->location_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar esta venta.',
            ], 403);
        }

        $timePart = $sale->date ? $sale->date->format('H:i:s') : '00:00:00';
        $sale->date = Carbon::parse($validated['date'] . ' ' . $timePart);
        $sale->save();

        return response()->json([
            'success' => true,
            'message' => 'Fecha actualizada correctamente.',
            'date' => $sale->date->format('Y-m-d'),
        ]);
    }

    /**
     * Export sales to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function excel(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $number = $request->number;
        $client = $request->client;
        $type_sale = $request->type_sale;
        $type_voucher = $request->type_voucher;
        $payment_method_id = $request->payment_method_id;
        $user_id = $request->user_id;

        // Crear nombre de archivo con fechas si están presentes
        $filename = 'ventas_historico';
        if ($start_date || $end_date) {
            $filename .= '_' . ($start_date ?: 'inicio') . '_a_' . ($end_date ?: 'fin');
        }
        $filename .= '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new SalesExport(
                $start_date,
                $end_date,
                $location_id,
                $number,
                $client,
                $type_sale,
                $type_voucher,
                $payment_method_id,
                $user_id
            ),
            $filename
        );
    }

    public function pdf(Request $request)
    {
        try {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;
            $number = $request->number;
            $client = $request->client_id;
            $type_sale = $request->type_sale;
            $type_voucher = $request->voucher_type;
            $payment_method_id = $request->payment_method_id;
            $user_id = $request->user_id;
            $total = 0;

            $query = Sale::with(['payments', 'sale_details', 'location'])
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('date', '>=', $start_date);
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
                ->when($client, function ($query) use ($client) {
                    $query->where('client_id', 'like', '%' . $client . '%');
                })
                ->when(!is_null($type_sale) && $type_sale !== '' && $type_sale !== 'all', function ($query) use ($type_sale) {
                    $query->where('type_sale', (int)$type_sale);
                })
                ->when($type_voucher, function ($query) use ($type_voucher) {
                    $query->whereHas('payments', function ($q) use ($type_voucher) {
                        $q->where('voucher_type', $type_voucher);
                    });
                })
                ->when($payment_method_id, function ($query) use ($payment_method_id) {
                    $query->whereHas('payments', function ($q) use ($payment_method_id) {
                        $q->where('payment_method_id', $payment_method_id);
                    });
                })
                ->when($user_id, function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })
                ->where('deleted', 0);

            $sales = $query->get();
            $total = $query->sum('total');

            $client_name = '';
            $location_name = '';
            $payment_method_name = '';
            $type_sale_name = '';

            switch ($type_sale) {
                case 0:
                    $type_sale_name = 'Venta Directa';
                    break;
                case 1:
                    $type_sale_name = 'Contrato';
                    break;
                case 2:
                    $type_sale_name = 'Crédito';
                    break;
                default:
                    $type_sale_name = 'Desconocido';
                    break;
            }


            if ($request->location_id) {
                $locationObj = Location::find($request->location_id);
                if ($locationObj) {
                    $location_name = $locationObj->name;
                }
            }
            if ($request->client_id) {
                $clientObj = Client::find($request->client_id);
                if ($clientObj) {
                    $client_name = $clientObj->business_name ?: $clientObj->contact_name;
                }
            }
            if ($request->payment_method_id) {
                $paymentMethodObj = PaymentMethod::find($request->payment_method_id);
                if ($paymentMethodObj) {
                    $payment_method_name = $paymentMethodObj->name;
                }
            }
            $data = [
                'title' => 'REPORTE DE VENTAS',
                'subtitle' => 'LISTADO DE VENTAS REGISTRADAS',
                'sales' => $sales,
                'total' => $total,
                'filters' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'location_id' => $location_id,
                    'location_name' => $location_name,
                    'number' => $number,
                    'client' => $client,
                    'client_name' => $client_name,
                    'type_sale' => $type_sale,
                    'type_sale_name' => $type_sale_name,
                    'type_voucher' => $type_voucher,
                    'payment_method_id' => $payment_method_id,
                    'payment_method_name' => $payment_method_name,
                    'user_id' => $user_id,
                ]
            ];
            $pdf = Pdf::loadView('sales.pdf.pdf_sales', $data)->setPaper('A4', 'portrait');
            $filename = 'reporte_ventas' . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'order_detail_id' => 'nullable|exists:order_details,id',
            'vehicle_plate' => 'nullable|string|max:20',
            'pump_id' => 'nullable|exists:pumps,id',
            'type_sale' => 'nullable|integer|in:0,1,2',
            'adicional' => 'nullable|numeric|min:0',
            'credit_number' => 'nullable|integer|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discounted_price' => 'nullable|numeric|min:0',
            'products.*.tank_id' => 'nullable|exists:tanks,id',
            'products.*.order_detail_id' => 'nullable|exists:order_details,id',
            'products.*.truck_id' => 'nullable|exists:trucks,id',
            'products.*.pump_id' => 'nullable|exists:pumps,id',
            'products.*.vehicle_plate' => 'nullable|string|max:20',
            'isle_id' => 'required|exists:isles,id', 
        ];

        $sede = auth()->user()->location_id;

        if ($request->has('credit_number') && $request->credit_number === '') {
            $request->merge(['credit_number' => null]);
        }

        $typeSale = $request->type_sale ?? 0;
        if ($typeSale == 0) {
            $validationRules = array_merge($validationRules, [
                'payment_methods' => 'required|array|min:1',
                'payment_methods.*.payment_method_id' => 'required|exists:payment_methods,id',
                'payment_methods.*.amount' => 'required|numeric|min:0.01',
                'payment_methods.*.voucher_type' => 'nullable|string',
                'payment_methods.*.voucher_id' => 'nullable|string',
                'payment_methods.*.number' => 'nullable|string',
            ]);
        } else {
            $validationRules = array_merge($validationRules, [
                'payment_methods' => 'nullable|array',
            ]);
        }

        $request->validate($validationRules);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->products as $index => $product) {
                if (isset($product['subtotal']) && $product['subtotal'] > 0) {
                    $subtotalRedondeado = round($product['subtotal'], 2);
                } else {
                    $precio = $product['discounted_price'] ?? $product['unit_price'];
                    $cantidad = $product['quantity'];
                    $subtotal = $cantidad * $precio;
                    $subtotalRedondeado = round($subtotal, 2);
                }
                $total += $subtotalRedondeado;
            }

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'location_id' => $sede,
                'isle_id' => $request->isle_id, 
                'client_id' => $request->client_id,
                'client_name' => $request->client_name,
                'phone' => $request->phone,
                'type_sale' => $request->type_sale ?? 0,
                'total' => $total,
                'adicional' => ($request->has('adicional') && $request->adicional > 0) ? $request->adicional : 0,
                'vehicle_plate' => $request->vehicle_plate,
                'date' => now(),
                'deleted' => false
            ]);

            $documento = $request->document ?? null;
            $cliente_id = $request->client_id ?? null;
            if ($documento && !$cliente_id) {
                $clienteEncontrado = Client::where('document', $documento)->first();
                if (!$clienteEncontrado) {
                    $cliente_nombre = $request->client ?? null;
                    $cliente_razon_social = (strlen($documento) === 11) ? $cliente_nombre : null;
                    if (strlen($documento) === 11) $cliente_nombre = null;

                    Client::create([
                        'document' => $documento,
                        'contact_name' => $cliente_nombre,
                        'business_name' => $cliente_razon_social,
                        'deleted' => 0
                    ]);
                }
            }

            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                if (!$product) throw new \Exception("Producto {$productData['product_id']} no encontrado");

                $unitPriceFromRequest = floatval($productData['unit_price'] ?? 0);
                $discountedPriceFromRequest = floatval($productData['discounted_price'] ?? 0);
                
                $locationPrice = LocationPrice::where('product_id', $productData['product_id'])
                    ->where('location_id', $sede)->first();
                $precioSede = $locationPrice ? $locationPrice->unit_price : $product->unit_price;

                $unitPrice = $precioSede;
                $discountedPrice = null;
                if (isset($productData['is_wholesale']) && $productData['is_wholesale']) {
                    $discountedPrice = $discountedPriceFromRequest > 0 ? $discountedPriceFromRequest : $unitPriceFromRequest;
                } elseif ($request->order_detail_id) {
                    $discountedPrice = $discountedPriceFromRequest > 0 ? $discountedPriceFromRequest : $unitPriceFromRequest;
                }
                
                if ($unitPrice <= 0) $unitPrice = ($product->unit_price > 0) ? $product->unit_price : 0.01;

                $truck_id = null;
                if (!empty($productData['vehicle_plate'])) {
                    $plate = trim($productData['vehicle_plate']);
                    $truck = Truck::whereRaw('UPPER(plate) = ?', [strtoupper($plate)])->where('deleted', 0)->first();
                    if (!$truck) {
                        $truck = Truck::create(['plate' => $plate, 'name' => $plate, 'deleted' => false]);
                    }
                    $truck_id = $truck->id;
                }

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'order_detail_id' => $productData['order_detail_id'] ?? null,
                    'pump_id' => $productData['pump_id'] ?? null,
                    'truck_id' => $truck_id,
                    'unit_price' => $unitPrice,
                    'discounted_price' => $discountedPrice,
                    'subtotal' => isset($productData['subtotal']) && $productData['subtotal'] > 0 ? round($productData['subtotal'], 2) : round($productData['quantity'] * ($discountedPrice ?? $unitPrice), 2),
                    'deleted' => false
                ]);

                if (isset($productData['tank_id']) && $productData['tank_id']) {
                    $tank = Tank::find($productData['tank_id']);
                    if ($tank) {
                        $current = floatval($tank->stored_quantity ?? 0);
                        $restar = floatval($productData['quantity']);
                        if ($current < $restar) throw new \Exception('Stock insuficiente en tanque ' . $tank->name);
                        $tank->stored_quantity = max(0, $current - $restar);
                        $tank->save();
                    }
                }
                
                if (isset($productData['order_detail_id']) && $productData['order_detail_id']) {
                    OrderDetail::where('id', $productData['order_detail_id'])->decrement('remaining', $productData['quantity']);
                }
            }

            // --- DATOS GENÉRICOS DE PAGO ---
            $numeroTicket = null;
            if (($request->type_sale ?? 0) == 0 || ($request->type_sale ?? 0) == 2) {
                $numeroTicket = $this->generarNumeroTicket();
            }
            $clientName = $request->client_name ?? $request->client ?? null;
            if (!$clientName && $request->client_id) {
                $client = Client::find($request->client_id);
                if ($client) $clientName = $client->business_name ?: $client->contact_name;
            }

            if (($request->type_sale ?? 0) == 0) { 
                foreach ($request->payment_methods as $paymentData) {
                    Payment::create([
                        'sale_id' => $sale->id,
                        'user_id' => Auth::id(),
                        'client_id' => $request->client_id,
                        'client_name' => $clientName ?? null,
                        'amount' => $paymentData['amount'],
                        'payment_method_id' => $paymentData['payment_method_id'],
                        'voucher_type' => $paymentData['voucher_type'] ?? null,
                        'voucher_id' => $paymentData['voucher_id'] ?? null,
                        'number' => $numeroTicket,
                        'status' => 'paid',
                        'date' => now(),
                        'deleted' => false
                    ]);

                    if (intval($paymentData['payment_method_id']) === 1) {
                        $isleIdSeleccionada = $request->isle_id;
                        $affected = DB::table('isles')
                            ->where('id', $isleIdSeleccionada)
                            ->increment('cash_amount', $paymentData['amount']);

                        if ($affected === 0) {
                            throw new \Exception("Error crítico: La isla seleccionada (ID: {$isleIdSeleccionada}) no existe o no se pudo actualizar.");
                        }
                        
                        Log::info("✅ SALDO ACTUALIZADO: Isla {$isleIdSeleccionada} + {$paymentData['amount']}");
                    }
                }
            } 
            
            // Venta Crédito
            elseif (($request->type_sale ?? 0) == 2) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'user_id' => Auth::id(),
                    'client_id' => $request->client_id,
                    'client_name' => $clientName,
                    'amount' => $total,
                    'payment_method_id' => null,
                    'voucher_type' => 'Ticket',
                    'number' => $request->credit_number ?? $numeroTicket,
                    'status' => 'pending',
                    'date' => now(),
                    'deleted' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Venta registrada correctamente.',
                'data' => ['sale_id' => $sale->id, 'total' => $sale->total]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Store Sale: ' . $e->getMessage());
            return response()->json(['status' => false, 'error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function generarNumeroTicket()
    {
        // Usa transacción para evitar conflictos en concurrencia
        return DB::transaction(function () {
            // Bloquea la fila para actualizar el número
            $registro = DB::table('config')->lockForUpdate()->first();

            if (!$registro) {
                // Si no existe registro, crea uno
                DB::table('config')->insert([
                    'number' => 1
                ]);
                return 'TICKET-00000001';
            }

            $nuevoNumero = $registro->number + 1;

            DB::table('config')
                ->update(['number' => $nuevoNumero]);

            // Formatea el número con ceros a la izquierda y prefijo
            return 'TICKET-' . str_pad($nuevoNumero, 8, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Registrar pago(s) parcial(es) para una venta a crédito
     * Soporta múltiples métodos de pago para un mismo pago
     */
    public function registerCreditPayment(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payment_methods.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($request->sale_id);

            // Verificar que es una venta a crédito
            if ($sale->type_sale != 2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Esta venta no es a crédito'
                ], 400);
            }

            // Obtener el pago pendiente principal
            $pendingPayment = Payment::where('sale_id', $sale->id)
                ->where('status', 'pending')
                ->first();

            if (!$pendingPayment) {
                return response()->json([
                    'status' => false,
                    'message' => 'No hay pagos pendientes para esta venta'
                ], 400);
            }

            // Calcular el total pagado anteriormente (pagos con estado 'paid')
            $totalPagado = Payment::where('sale_id', $sale->id)
                ->where('status', 'paid')
                ->sum('amount');

            // Calcular cuánto se está pagando ahora
            $totalNuevoPago = collect($request->payment_methods)->sum('amount');

            // Verificar que no se exceda el total pendiente
            $saldoPendiente = $sale->total - $totalPagado;
            if ($totalNuevoPago > $saldoPendiente) {
                return response()->json([
                    'status' => false,
                    'message' => "El monto excede el saldo pendiente (S/ " . number_format($saldoPendiente, 2) . ")"
                ], 400);
            }

            $sede = auth()->user()->location_id;

            // Usar el mismo número de ticket que el pago pendiente
            $numeroTicket = $pendingPayment->number;

            // Crear un pago por cada método de pago
            foreach ($request->payment_methods as $paymentData) {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'user_id' => Auth::id(),
                    'client_id' => $sale->client_id,
                    'client' => $sale->client,
                    'amount' => $paymentData['amount'],
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'voucher_type' => 'Ticket',
                    'voucher_id' => null,
                    'number' => $numeroTicket, // Mismo ticket que el pago pendiente
                    'status' => 'paid',
                    'date' => now(),
                    'deleted' => false
                ]);

                // Si es efectivo, actualizar cash_amount
                try {
                    $method = PaymentMethod::find($payment->payment_method_id);
                    if ($method && strtolower(trim($method->name)) === 'efectivo') {
                        $location = Location::find($sede);
                        if ($location) {
                            $location->cash_amount = ($location->cash_amount ?? 0) + floatval($payment->amount);
                            $location->save();
                        }
                    }
                } catch (\Throwable $ex) {
                    Log::error('Error actualizando cash_amount: ' . $ex->getMessage());
                }
            }

            // Verificar si se completó el pago total
            $nuevoTotalPagado = Payment::where('sale_id', $sale->id)
                ->where('status', 'paid')
                ->sum('amount');

            // Si se completó el pago, actualizar el estado del pago pendiente
            if ($nuevoTotalPagado >= $sale->total) {
                $pendingPayment->update([
                    'status' => 'completed',
                    'deleted' => true // Marcar como eliminado lógicamente
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pago registrado correctamente',
                'data' => [
                    'total_pagado' => $nuevoTotalPagado,
                    'saldo_restante' => max(0, $sale->total - $nuevoTotalPagado)
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $sale_id = $request->sale_id;
            $adicional = 0;
            $sale = Sale::with(['sale_details.product'])->findOrFail($sale_id);
            if ($sale) {
                $adicional = $sale->adicional;
            }
            if ($sale->type_sale === 0) {
                // Venta directa: mostrar unit_price
                $productos = $sale->sale_details->map(function ($detail) {
                    return [
                        'name' => $detail->product->name ?? 'Producto',
                        'quantity' => round($detail->quantity, 2),
                        'unit_price' => round($detail->unit_price, 2),
                        'subtotal' => round($detail->subtotal, 2),
                    ];
                });
                $adicional = $sale->adicional;
            } else {
                // Contrato/Crédito: mostrar discounted_price
                $productos = $sale->sale_details->map(function ($detail) {
                    return [
                        'name' => $detail->product->name ?? 'Producto',
                        'quantity' => round($detail->quantity, 2),
                        'unit_price' => round($detail->discounted_price ?? $detail->unit_price, 2),
                        'subtotal' => round($detail->subtotal, 2),
                    ];
                });
                
            }

            return response()->json([
                'status' => true,
                'productos' => $productos,
                'adicional' => number_format($adicional, 2),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al obtener productos de la venta: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $sale = Sale::findOrFail($id);
        $sale->update(['deleted' => true]);

        return response()->json(['success' => true, 'message' => 'Venta eliminada lógicamente.']);
    }

    public function consultarSunat(Request $request)
    {
        $doc = $request->query('doc');

        if (!$doc || (strlen($doc) !== 8 && strlen($doc) !== 11)) {
            return response()->json([
                'success' => false,
                'message' => 'Documento inválido'
            ], 422);
        }

        $urlBase = config('apisunat.url');
        $personaId = config('apisunat.id');
        $personaToken = config('apisunat.token.prod');

        try {
            if (strlen($doc) === 8) {
                $url = "$urlBase/personas/$personaId/getDNI?dni=$doc&personaToken=$personaToken";
            } else {
                $url = "$urlBase/personas/$personaId/getRUC?ruc=$doc&personaToken=$personaToken";
            }

            $response = Http::get($url);

            // ✅ LOG TEMPORAL
            \Log::info('Consulta a API Sunat/Reniec', [
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json('data')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener información de SUNAT/RENIEC'
                ], $response->status());
            }
        } catch (\Exception $e) {
            // ✅ LOG ERROR
            \Log::error('Error al consultar Sunat', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener islas de la sede del usuario autenticado
     */
    public function getIslesByLocation()
    {
        try {
            $user = auth()->user();
            $locationId = $user->location_id;

            $isles = Isle::where('location_id', $locationId)
                ->where('deleted', 0)
                ->get();

            return response()->json([
                'success' => true,
                'isles' => $isles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener islas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener surtidores de una isla específica
     */
    public function getPumpsByIsle(Request $request)
    {
        try {
            $isleId = $request->isle_id;

            $pumps = Pump::with('product')
                ->where('isle_id', $isleId)
                ->where('deleted', 0)
                ->get();

            return response()->json([
                'success' => true,
                'pumps' => $pumps
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener surtidores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener la última medición de un surtidor
     */
    public function getLastMeasurement(Request $request)
    {
        try {
            $pumpId = $request->pump_id;

            // Buscar medición del día actual primero
            $todayMeasurement = Measurement::where('pump_id', $pumpId)
                ->where('deleted', 0)
                ->whereDate('date', today())
                ->first();

            // Obtener la última medición (de otro día)
            $lastMeasurement = Measurement::where('pump_id', $pumpId)
                ->where('deleted', 0)
                ->orderBy('date', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'measurement' => $lastMeasurement,
                'has_today_measurement' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener última medición: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular el valor teórico sumando las ventas del surtidor del día actual
     */
    public function getTheoreticalValue(Request $request)
    {
        try {
            $pumpId = $request->pump_id;
            $user = auth()->user();
            $locationId = $user->location_id;

            // Obtener la última medición para el valor inicial
            $lastMeasurement = Measurement::where('pump_id', $pumpId)
                ->where('location_id', $locationId)
                ->where('deleted', 0)
                ->orderBy('date', 'desc')
                ->first();

            $initialValue = $lastMeasurement ? $lastMeasurement->amount_final : 0;

            // Sumar SOLO las cantidades vendidas HOY para este surtidor en esta sede
            $totalSold = SaleDetail::whereHas('sale', function ($query) use ($locationId) {
                $query->where('location_id', $locationId)
                    ->where('deleted', 0)
                    ->whereDate('date', today());
            })
                ->where('pump_id', $pumpId)
                ->sum('quantity');

            return response()->json([
                'success' => true,
                'initial_value' => $initialValue,
                'total_sold' => $totalSold,
                'theoretical_value' => $initialValue + $totalSold
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular valor teórico: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveMeasurement(Request $request)
    {
        try {
            $validated = $request->validate([
                'pump_id' => 'required|exists:pumps,id',
                'initial_value' => 'required|numeric|min:0',
                'final_value' => 'required|numeric|min:0',
                'theoretical_value' => 'nullable|numeric|min:0'
            ]);

            $user = auth()->user();
            $pumpId = $validated['pump_id'];
            $locationId = $user->location_id;

            // Validar que no exista ya una medición hoy para este surtidor
            $existingMeasurement = Measurement::where('pump_id', $pumpId)
                ->where('location_id', $locationId)
                ->whereDate('date', today())
                ->where('deleted', 0)
                ->first();


            // Calcular la diferencia: (Valor Final - Valor Inicial) - Valor Teórico
            $initialValue = $validated['initial_value'];
            $finalValue = $validated['final_value'];
            $theoreticalValue = $validated['theoretical_value'] ?? 0;
            $difference = ($initialValue - $finalValue) - $theoreticalValue;

            // Crear la medición
            $measurement = Measurement::create([
                'pump_id' => $pumpId,
                'location_id' => $locationId,
                'user_id' => $user->id,
                'amount_initial' => $initialValue,
                'amount_final' => $finalValue,
                'amount_theorical' => $theoreticalValue,
                'amount_difference' => $difference,
                'date' => now(),
                'description' => 'Medición de contómetro',
                'deleted' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medición guardada correctamente',
                'measurement' => $measurement
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al guardar medición: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar medición: ' . $e->getMessage()
            ], 500);
        }
    }

    public function excelByIsle(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        // Crear nombre de archivo con fechas si están presentes
        $filename = 'ventas_diarias';
        $filename .= '_' . date('d-m-Y') . '.xlsx';

        $location_id = $request->location_id ?? auth()->user()->location_id;

        return Excel::download(
            new SalesByIsleExport(
                $date,
                $location_id,
            ),
            $filename
        );
    }
}
