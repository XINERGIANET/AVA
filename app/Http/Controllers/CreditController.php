<?php

namespace App\Http\Controllers;

use App\Exports\CreditsExport;
use App\Models\Agreement;
use App\Models\AgreementDetail;
use App\Models\Client;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;
        $location_id = $request->location_id;

        $client = Client::find($client_id);
        if ($client) {
            // Agrega el nombre al request usando merge
            $request->merge(['client_name' => $client->business_name ? $client->business_name : $client->contact_name]);
        }

        // Cargar todas las relaciones necesarias
        $credits = Payment::with([
            'sale.sale_details.product',
            'sale.location',
            'agreement.agreement_details.product',
            'agreement.location',
            'client'
        ])
            ->whereIn('status', ['paid', 'pending'])
            ->where('deleted', 0)
            ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
            ->when($client_id, fn($q) => $q->where('client_id', $client_id))
            ->when($location_id, function($q) use ($location_id) {
                $q->where(function($query) use ($location_id) {
                    $query->whereHas('sale.location', fn($q) => $q->where('id', $location_id))
                        ->orWhereHas('agreement.location', fn($q) => $q->where('id', $location_id));
                });
            })
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        // Calcular total de créditos pagados con los mismos filtros
        $totalQuery = Payment::whereIn('status', ['paid', 'pending'])
            ->where('deleted', 0)
            ->where('status', 'paid')
            ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
            ->when($client_id, fn($q) => $q->where('client_id', $client_id))
            ->when($location_id, function($q) use ($location_id) {
                $q->where(function($query) use ($location_id) {
                    $query->whereHas('sale.location', fn($q) => $q->where('id', $location_id))
                          ->orWhereHas('agreement.location', fn($q) => $q->where('id', $location_id));
                });
            })
            ->orderBy('date', 'desc');
        
        $totalCreditosPagados = $totalQuery->sum('amount');
        
        // Obtener todas las sedes disponibles para el filtro
        $locations = Location::where('deleted', 0)->orderBy('name')->get();
        
        return view('credits.index', compact('credits', 'client', 'totalCreditosPagados', 'locations'));
    }

    /**
     * Export sales to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
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
        $filename = 'creditos_historico.xlsx';

        Log::info('export a excel');

        return Excel::download(
            new CreditsExport(
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
    
            // Cargar todas las relaciones necesarias
            $credits = Payment::with([
                'agreement.agreement_details.product',
                'agreement.location',
                'sale.sale_details.product',
                'sale.location',
                'client',
            ])
                ->whereIn('status', ['paid', 'pending'])
                ->where('deleted', 0)
                ->when($start_date, fn($q) => $q->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('date', '<=', $end_date))
                ->when($client_id, fn($q) => $q->where('client_id', $client_id))
                ->when($location_id, function($q) use ($location_id) {
                    $q->where(function($query) use ($location_id) {
                        $query->whereHas('sale.location', fn($q) => $q->where('id', $location_id))
                              ->orWhereHas('agreement.location', fn($q) => $q->where('id', $location_id));
                    });
                })
                ->get();
    
            $data = [
                "title" => "REPORTE DE CREDITOS",
                "subtitle" => "LISTADO DE CREDITOS",
                "credits" => $credits,
                "filters" => [
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "client" => $client ? ($client->business_name ?: $client->contact_name) : null,
                    "location" => $location ? $location->name : null,
                ]
            ];
    
            $pdf = Pdf::loadView('credits.pdf.pdf_credits', $data)->setPaper('A4', 'portrait');
            $filename = 'reporte_creditos' . '_' . date('Y-m-d_H-i-s') . '.pdf';
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

    public function create(Request $request)
    {
        $client_id = $request->client_id;
        $location_id = $request->location_id;
        $payment_date = $request->payment_date;
        $number = $request->number;
        $credits = Payment::with([
            'agreement.agreement_details.product',
            'agreement.location',
            'client',
            'sale.sale_details.product',
            'sale.location'
        ])
            ->where('deleted', 0)
            ->where('status', '!=', 'paid')
            ->when($client_id, fn($q) => $q->where('client_id', $client_id))
            ->when($number, fn($q) => $q->where('number', 'like', "%$number%"))
            ->when($location_id, function($q) use ($location_id) {
                $q->where(function($query) use ($location_id) {
                    $query->whereHas('sale.location', fn($q) => $q->where('id', $location_id))
                          ->orWhereHas('agreement.location', fn($q) => $q->where('id', $location_id));
                });
            })
            ->when($payment_date, fn($q) => $q->whereDate('date', $payment_date))
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->appends($request->query());
        
        $areas = Location::where('deleted', 0)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('deleted', 0)->orderBy('name')->get();
        
        // Obtener el cliente seleccionado para mantener el nombre en el input
        $client = $client_id ? Client::find($client_id) : null;
        
        return view('credits.create', compact('credits', 'areas', 'paymentMethods', 'client'));
    }

    public function store(Request $request)
    {
        // Debug: Log de todos los datos recibidos
        Log::info('Datos recibidos en CreditController@store:', $request->all());

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'location_id' => 'required|exists:locations,id',
            'total' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0',
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|numeric|min:0',
            'subtotals' => 'required|array',
            'subtotals.*' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear el agreement (crédito)
            $agreement = Agreement::create([
                'client_id' => $validated['client_id'],
                'location_id' => $validated['location_id'],
                'total' => $validated['total'],
                'payment_date' => $validated['payment_date'],
                'type' => 'credit',
                'status' => 0, // 0 = pendiente, 1 = pagado
                'paid' => 0,   // 0 = no pagado, 1 = pagado
                'date' => now(),
                'deleted' => false
            ]);

            // 2. Crear los detalles del agreement (agreement_details)
            for ($i = 0; $i < count($validated['product_ids']); $i++) {
                if ($validated['quantities'][$i]) {

                    AgreementDetail::create([
                        'agreement_id' => $agreement->id,
                        'product_id' => $validated['product_ids'][$i],
                        'unit_price' => $validated['prices'][$i],
                        'quantity' => $validated['quantities'][$i],
                        'subtotal' => $validated['subtotals'][$i]
                    ]);
                }
            }

            // 3. Crear la orden única para este crédito
            $order = Order::create([
                'agreement_id' => $agreement->id,
                'date' => now(),
                'deleted' => false
            ]);

            // 4. Crear los detalles de la orden (order_details)
            for ($i = 0; $i < count($validated['product_ids']); $i++) {
                if (
                    !isset($validated['quantities'][$i]) ||
                    $validated['quantities'][$i] === null ||
                    $validated['quantities'][$i] <= 0
                ) {
                    continue;
                }

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $validated['product_ids'][$i],
                    'quantity'  => $validated['quantities'][$i],
                    'remaining' => $validated['quantities'][$i],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Credito creado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error
            Log::error('Error al crear crédito:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar credito.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Agreement $credit)
    {
        return view('credits.show', compact('credit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Agreement  $agreement
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Agreement $credit)
    {
        return view('credits.edit', compact('credit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Agreement $agreement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Agreement $credit)
    {
        $validated = $request->validate([
            // Mismas reglas que en store
        ]);

        $credit->update($validated);

        return redirect()->route('credits.index')
            ->with('success', 'Acuerdo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *t
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Buscar el Payment por ID
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'Crédito no encontrado'
            ], 404);
        }

        // Marcar el Payment como eliminado
        $payment->deleted = true;
        $payment->save();

        // Si tiene una Sale relacionada, también marcarla como eliminada
        if ($payment->sale_id) {
            $sale = Sale::find($payment->sale_id);
            if ($sale && $sale->type_sale == 2) { // Solo si es un crédito
                $sale->deleted = 1;
                $sale->save();
            }
        }

        // Si tiene un Agreement relacionado, también marcarlo como eliminado
        if ($payment->agreement_id) {
            $agreement = Agreement::find($payment->agreement_id);
            if ($agreement) {
                $agreement->deleted = true;
                $agreement->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Crédito anulado correctamente.'
        ]);
    }
}
