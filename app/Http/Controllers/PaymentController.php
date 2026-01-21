<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $number = $request->input('number');
        $client_id = $request->input('client_id');
        $voucher_type = $request->input('voucher_type');
        $payment_method_id = $request->input('payment_method_id');

        $client = Client::find($client_id);
        if ($client) {
            // Agrega el nombre al request usando merge
            $request->merge(['client_name' => $client->business_name ? $client->business_name : $client->contact_name]);
        }

        $payments = Payment::with('payment_method', 'client')
            ->when($start_date, fn($query) => $query->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($query) => $query->whereDate('date', '<=', $end_date))
            ->when($number, fn($query) => $query->where('number', 'like', "%$number%"))
            ->when($client_id, fn($query) => $query->where('client_id', $client_id))
            ->when($voucher_type, fn($query) => $query->where('voucher_type', $voucher_type))
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('sale.location', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->when($payment_method_id, fn($query) => $query->where('payment_method_id', $payment_method_id))
            ->where('status', '!=', 'paid')
            ->paginate(20);

        $payment_methods = PaymentMethod::where('deleted', 0)
            ->get();

        return view('payments.index', compact('payments', 'payment_methods'));
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
     * Obtener pagos por agreement_id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayments(Request $request)
    {
        // Manejar tanto agreement_id como payment_id
        if ($request->has('payment_id')) {
            // Para ventas directas a crédito
            $payment = Payment::with('sale.client')->find($request->payment_id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            $total = $payment->amount;
            
            // Calcular el total pagado (todos los payments con status paid para esta venta)
            $totalPagado = Payment::where('sale_id', $payment->sale_id)
                ->where('status', 'paid')
                ->where('deleted', 0)
                ->sum('amount');
                

            $saldo = $total - $totalPagado;
            
            // Obtener todos los pagos realizados para esta venta
            $paymentsPaid = Payment::with('payment_method')
                ->where('sale_id', $payment->sale_id)
                ->where('status', 'paid')
                ->where('deleted', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            // Obtener el nombre del cliente
            $clientName = $payment->client_name;
            if (!$clientName && $payment->sale && $payment->sale->client) {
                $clientName = $payment->sale->client->business_name ?? $payment->sale->client->contact_name;
            }

            return response()->json([
                'success' => true,
                'payments' => $paymentsPaid,
                'total' => number_format($total, 2),
                'total_pagado' => number_format($totalPagado, 2),
                'saldo' => number_format($saldo, 2),
                'payment' => $payment,
                'client_name' => $clientName,
            ]);
        } else {
            // Para créditos de contratos (lógica original)
            $request->validate([
                'agreement_id' => 'required|integer|exists:agreements,id',
            ]);

            $agreement = Agreement::with('client')->find($request->agreement_id);
            $total = $agreement->total;

            $payments = Payment::with('payment_method')
                ->where('agreement_id', $request->agreement_id)
                ->where('deleted', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalPagado = $payments->sum('amount');
            $saldo = $total - $totalPagado;

            // Obtener el nombre del cliente
            $clientName = null;
            if ($agreement->client) {
                $clientName = $agreement->client->business_name ?? $agreement->client->contact_name;
            }

            return response()->json([
                'success' => true,
                'payments' => $payments,
                'total' => number_format($total, 2),
                'total_pagado' => number_format($totalPagado, 2),
                'saldo' => number_format($saldo, 2),
                'agreement' => $agreement,
                'client_name' => $clientName, // Agregar esto
            ]);
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
        // Validación flexible para soportar tanto agreement_id como payment_id
        // Validar que venga payment_methods (array) o payment_method_id (legacy)
        $validationRules = [
            'agreement_id' => 'nullable|integer|exists:agreements,id',
            'payment_id' => 'nullable|integer|exists:payments,id',
            'voucher_type' => 'nullable|string',
            'voucher_number' => 'nullable|string',
            'operation_name' => 'nullable|string',
            'client_document' => 'nullable|string',
            'client_name' => 'nullable|string',
            'foto' => 'nullable|image'
        ];

        // Si viene payment_methods (array), validarlo; si no, validar payment_method_id y amount (legacy)
        if ($request->has('payment_methods') && is_array($request->payment_methods)) {
            $validationRules['payment_methods'] = 'required|array|min:1';
            $validationRules['payment_methods.*.payment_method_id'] = 'required|exists:payment_methods,id';
            $validationRules['payment_methods.*.amount'] = 'required|numeric|min:0.01';
        } else {
            $validationRules['amount'] = 'required|numeric|min:0.01';
            $validationRules['payment_method_id'] = 'required|integer|exists:payment_methods,id';
        }

        $validated = $request->validate($validationRules);

        // Validar que venga al menos uno
        if (!$request->agreement_id && !$request->payment_id) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar agreement_id o payment_id',
            ], 422);
        }

        // Manejo para ventas directas a crédito (payment_id)
        if ($request->payment_id) {
            $payment = Payment::with('sale')->find($request->payment_id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado',
                ], 404);
            }

            // Calcular el total pagado anteriormente (pagos con estado 'paid')
            $totalPagado = Payment::where('sale_id', $payment->sale_id)
                ->where('status', 'paid')
                ->where('deleted', 0)
                ->sum('amount');

            $saldoPendiente = $payment->amount - $totalPagado;

            // Obtener los métodos de pago (puede ser array o single)
            $paymentMethods = $request->payment_methods ?? [];
            if (empty($paymentMethods) && $request->payment_method_id) {
                // Legacy: convertir single a array
                $paymentMethods = [[
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => $request->amount
                ]];
            }

            // Calcular el total del nuevo pago
            $totalNuevoPago = collect($paymentMethods)->sum('amount');

            if ($totalNuevoPago > $saldoPendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto total (S/ ' . number_format($totalNuevoPago, 2) . ') no puede ser mayor al saldo pendiente (S/ ' . number_format($saldoPendiente, 2) . ')',
                ], 422);
            }

            if ($totalNuevoPago <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe ingresar al menos un método de pago con monto mayor a 0',
                ], 422);
            }

            try {
                $photoPath = null;
                if ($request->hasFile('foto')) {
                    $photoPath = $request->file('foto')->store('payments', 'public');
                }

                DB::beginTransaction();

                // Obtener el número de ticket del pago pendiente
                $numeroTicket = $payment->number;
                $sede = auth()->user()->location_id;

                // Crear un pago por cada método de pago
                foreach ($paymentMethods as $paymentData) {
                    Payment::create([
                        'sale_id' => $payment->sale_id,
                        'user_id' => Auth::id(),
                        'client_id' => $payment->client_id,
                        'client_name' => $payment->client_name,
                        'amount' => $paymentData['amount'],
                        'payment_method_id' => $paymentData['payment_method_id'],
                        'voucher_type' => $request->voucher_type ?? $payment->voucher_type ?? 'Ticket',
                        'voucher_id' => $request->voucher_number ?? null,
                        'number' => $numeroTicket,
                        'observation' => $request->operation_name,
                        'photo_url' => $photoPath,
                        'status' => 'paid',
                        'date' => now(),
                        'deleted' => false
                    ]);

                    // Si es efectivo, actualizar cash_amount
                    try {
                        $method = PaymentMethod::find($paymentData['payment_method_id']);
                        if ($method && strtolower(trim($method->name)) === 'efectivo') {
                            $location = \App\Models\Location::find($sede);
                            if ($location) {
                                $location->cash_amount = ($location->cash_amount ?? 0) + floatval($paymentData['amount']);
                                $location->save();
                            }
                        }
                    } catch (\Throwable $ex) {
                        Log::error('Error actualizando cash_amount: ' . $ex->getMessage());
                    }
                }

                // Verificar si se completó el pago
                $nuevoTotalPagado = Payment::where('sale_id', $payment->sale_id)
                    ->where('status', 'paid')
                    ->where('deleted', 0)
                    ->sum('amount');

                // Si se completó el pago, actualizar el estado del pago pendiente
                if ($nuevoTotalPagado >= $payment->amount) {
                    $payment->update([
                        'status' => 'completed',
                        'deleted' => true // Marcar como eliminado lógicamente
                    ]);
                }

                DB::commit();

                $message = $nuevoTotalPagado >= $payment->amount 
                    ? 'Pago registrado correctamente. ¡Crédito pagado totalmente!'
                    : 'Pago registrado correctamente';

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el pago: ' . $e->getMessage()
                ], 500);
            }
        }

        // Manejo original para contratos (agreement_id)
        if ($request->agreement_id) {
            // Validar que el monto no exceda el saldo pendiente
            $agreement = Agreement::find($request->agreement_id);

            if (!$agreement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acuerdo no encontrado',
                ], 404);
            }

            $totalPagado = Payment::where('agreement_id', $request->agreement_id)
                ->where('deleted', 0)
                ->sum('amount');

            $saldoPendiente = $agreement->total - $totalPagado;

            // Obtener los métodos de pago (puede ser array o single)
            $paymentMethods = $request->payment_methods ?? [];
            if (empty($paymentMethods) && $request->payment_method_id) {
                // Legacy: convertir single a array
                $paymentMethods = [[
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => $request->amount
                ]];
            }

            // Calcular el total del nuevo pago
            $totalNuevoPago = collect($paymentMethods)->sum('amount');

            if ($totalNuevoPago > $saldoPendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto total (S/ ' . number_format($totalNuevoPago, 2) . ') no puede ser mayor al saldo pendiente (S/ ' . number_format($saldoPendiente, 2) . ')',
                ], 422);
            }

            if ($totalNuevoPago <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe ingresar al menos un método de pago con monto mayor a 0',
                ], 422);
            }

            try {
                $photoPath = null;
                if ($request->hasFile('foto')) {
                    $photoPath = $request->file('foto')->store('payments', 'public');
                }

                // Obtener el nombre del cliente - priorizar el del request, sino obtenerlo del agreement
                $clientName = $request->client_name;
                if (!$clientName && $agreement->client_id) {
                    $client = Client::find($agreement->client_id);
                    if ($client) {
                        $clientName = $client->business_name ?: $client->contact_name;
                    }
                }
                // Si aún no hay nombre, usar el del payment pendiente original si existe
                if (!$clientName) {
                    $originalPayment = Payment::where('agreement_id', $request->agreement_id)
                        ->where('status', 'pending')
                        ->where('deleted', 0)
                        ->first();
                    if ($originalPayment && $originalPayment->client_name) {
                        $clientName = $originalPayment->client_name;
                    }
                }

                DB::beginTransaction();

                // Crear un pago por cada método de pago
                foreach ($paymentMethods as $paymentData) {
                    Payment::create([
                        'agreement_id' => $request->agreement_id,
                        'user_id' => Auth::id(),
                        'client_id' => $agreement->client_id,
                        'client_name' => $clientName,
                        'amount' => $paymentData['amount'],
                        'payment_method_id' => $paymentData['payment_method_id'],
                        'voucher_type' => $request->voucher_type,
                        'voucher_id' => $request->voucher_number,
                        'number' => $request->voucher_number,
                        'observation' => $request->operation_name,
                        'photo_url' => $photoPath,
                        'status' => 'paid',
                        'date' => now(),
                        'deleted' => false
                    ]);
                }

                DB::commit();
                // Verificar si el contrato está completamente pagado
                $nuevoTotalPagado = Payment::where('agreement_id', $request->agreement_id)
                    ->where('deleted', 0)
                    ->sum('amount');

                $mensaje = 'Pago registrado correctamente.';
                if ($nuevoTotalPagado >= $agreement->total) {
                    $agreement->update(['paid' => 1]);
                    $mensaje = 'Pago registrado correctamente. ¡Contrato pagado totalmente!';
                }

                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el pago: ' . $e->getMessage()
                ], 500);
            }
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
        //
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
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('Entró al método update de PaymentController', ['id' => $id]);

        $payment = Payment::find($id);
        $payment->deleted = 1;
        $payment->save();

        return response()->json(['status' => true, 'message' => 'Pago anulado correctamente']);
    }

    public function excel(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $number = $request->input('number');
        $client_name = $request->input('client_name');
        $voucher_type = $request->input('voucher_type');
        $payment_method_id = $request->input('payment_method_id');



        try {
            return Excel::download(new PaymentsExport($start_date, $end_date, $number, $client_name, $voucher_type, $payment_method_id), 'Pagos.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al generar Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $number = $request->number;
            $client_id = $request->client_id;
            $voucher_type = $request->voucher_type;
            $payment_method_id = $request->payment_method_id;

            $query = Payment::with('payment_method', 'client')
                ->when($start_date, fn($query) => $query->whereDate('date', '>=', $start_date))
                ->when($end_date, fn($query) => $query->whereDate('date', '<=', $end_date))
                ->when($number, fn($query) => $query->where('number', 'like', "%$number%"))
                ->when($client_id, fn($query) => $query->where('client_id', $client_id))
                ->when($voucher_type, fn($query) => $query->where('voucher_type', $voucher_type))
                ->when($payment_method_id, fn($query) => $query->where('payment_method_id', $payment_method_id));
            $payments = $query->get();

            $client_name = "";
            $payment_method_name = '';
            if ($payment_method_id) {
                $paymentMethod = PaymentMethod::find($payment_method_id);
                if ($paymentMethod) {
                    $payment_method_name = $paymentMethod->name;
                }
            }

            if ($request->client_id) {
                $clientObj = Client::find($request->client_id);
                if ($clientObj) {
                    $client_name = $clientObj->business_name ?: $clientObj->contact_name;
                }
            }

            $data = [
                "title" => "REPORTE DE PAGOS",
                "subtitle" => "LISTADO DE PAGOS REGISTRADOS",
                "payments" => $payments,
                "filters" => [
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "number" => $number,
                    "client_name" => $client_name,
                    "voucher_type" => $voucher_type,
                    "payment_method_name" => $payment_method_name,
                ]
            ];

            $pdf = Pdf::loadView('payments.pdf.pdf_payments', $data);
            $filename = 'reporte_pagos' . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar PDF de pagos: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'error' => 'Error al generar PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
