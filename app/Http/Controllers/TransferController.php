<?php

namespace App\Http\Controllers;

use App\Exports\TransfersExport;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\Location;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    public function historico(Request $request)
    {
        $from_date = $request->from_date;
        $to_date   = $request->to_date;
        //Desde Sede y tanque
        $from_location_id = $request->from_location_id;
        $from_tank_id = $request->from_tank_id;
        //Hacia Sede y tanque
        $to_location_id = $request->to_location_id;
        $to_tank_id = $request->to_tank_id;

        $locations = Location::where('deleted', '0')->get();
        $tanksByLocation = Tank::where('deleted', '0')->get()->groupBy('location_id');
        $query = Transfer::with(['from_tank', 'to_tank', 'product'])
            ->where('deleted', 0)
            ->when($from_location_id, function ($q) use ($from_location_id) {
                $q->whereHas('from_tank', function ($subQ) use ($from_location_id) {
                    $subQ->where('location_id', $from_location_id);
                });
            })
            ->when($from_tank_id, function ($q) use ($from_tank_id) {
                $q->where('from_tank_id', $from_tank_id);
            })
            ->when($to_location_id, function ($q) use ($to_location_id) {
                $q->whereHas('to_tank', function ($subQ) use ($to_location_id) {
                    $subQ->where('location_id', $to_location_id);
                });
            })
            ->when($to_tank_id, function ($q) use ($to_tank_id) {
                $q->where('to_tank_id', $to_tank_id);
            })
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                $q->whereBetween('created_at', [
                    $from_date . ' 00:00:00',
                    $to_date   . ' 23:59:59'
                ]);
            })
            ->when($from_date && !$to_date, function ($q) use ($from_date) {
                $q->whereDate('created_at', '>=', $from_date);
            })
            ->when(!$from_date && $to_date, function ($q) use ($to_date) {
                $q->whereDate('created_at', '<=', $to_date);
            });

        $distribuciones = $query->paginate(10);

        return view('transfers.historico', compact('distribuciones', 'locations', 'tanksByLocation'));
    }
    //Excel del historico
    public function excel(Request $request)
    {
        $from_location_id = $request->from_location_id;
        $from_tank = $request->from_tank_id;
        $to_location_id = $request->to_location_id;
        $to_tank = $request->to_tank_id;
        $product = $request->product;
        $unit = $request->unit;
        $quantity = $request->quantity;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $received = $request->received;


        $filename = 'historico_distribuciones.xlsx';

        return Excel::download(
            new TransfersExport(
                $from_location_id,
                $from_tank,
                $to_location_id,
                $to_tank,
                $product,
                $unit,
                $quantity,
                $from_date,
                $to_date,
                $received
            ),
            $filename
        );
    }

    public function pdf(Request $request)
    {
        try {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $from_location_id = $request->from_location_id;
            $from_tank_id = $request->from_tank_id;
            $to_location_id = $request->to_location_id;
            $to_tank_id = $request->to_tank_id;

            $query = Transfer::with(['from_tank', 'to_tank', 'product'])
                ->where('deleted', 0)
                ->when($from_date, fn($q) => $q->whereDate('from_date', '>=', $from_date))
                ->when($to_date, fn($q) => $q->whereDate('to_date', '<=', $to_date))
                ->when($from_location_id, function ($q) use ($from_location_id) {
                    $q->whereHas('from_tank', function ($subQ) use ($from_location_id) {
                        $subQ->where('location_id', $from_location_id);
                    });
                })
                ->when($from_tank_id, fn($q) => $q->where('from_tank_id', $from_tank_id))
                ->when($to_location_id, function ($q) use ($to_location_id) {
                    $q->whereHas('to_tank', function ($subQ) use ($to_location_id) {
                        $subQ->where('location_id', $to_location_id);
                    });
                })
                ->when($to_tank_id, fn($q) => $q->where('', $to_tank_id));

            $distribucion = $query->get();
            $data = [
                "title" => "REPORTE DE TRANSFERENCIAS",
                "subtitle" => "LISTADO DE TRANSFERENCIAS",
                "distribucion" => $distribucion,
                "filters" => [
                    "from_date" => $from_date,
                    "to_date" => $to_date,
                    "from_location" => $from_location_id,
                    "from_tank" => $from_tank_id,
                    "to_location" => $to_location_id,
                    "to_tank" => $to_tank_id,
                ]
            ];


            $pdf = Pdf::loadView('transfers.pdf.pdf_transfers', $data)->setPaper('A4', 'portrait');
            $filename = 'reporte_transferencia' . '_' . date('Y-m-d_H-i-s') . '.pdf';
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating PDF in TransfersController@pdf:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => request()->all()
            ]);
            return response()->json(['status' => false, 'message' => 'Error generating PDF. Please try again later.'], 500);
        }
    }
    // app/Http/Controllers/DischargeController.php

    // DischargeController.php
    //listar
    public function index()
    {
        $user = auth()->user();
        $locations = Location::where('deleted', '0')->get();
        $tanksByLocation = Tank::where('deleted', '0')->get()->groupBy('location_id');
        $transfers = Transfer::with(['from_tank', 'to_tank', 'product'])
            ->where('deleted', 0)
            ->orderBy('id','desc')
            ->paginate(10);

        return view(
            // 'transfers.index'
            'transfers.index_semaforo'
            , compact(
            'transfers',
            'locations',
            'tanksByLocation'
        ));
    }
    public function create()
    {
        $sedes = Location::where('estado', '0')->get();
        $products = Product::where('estado', '0')->get();
        return view('transfers.create', compact('sedes', 'products'));
    }

    //crear
    public function store(Request $request)
    {
        $request->validate([
            'from_tank_id' => 'required|exists:tanks,id',
            'to_tank_id' => 'required|exists:tanks,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        $from_tank = Tank::findOrFail($request->from_tank_id);
        $to_tank = Tank::findOrFail($request->to_tank_id);

        //verifica que tanques sean del mismo producto
        if ($from_tank->product_id != $to_tank->product_id) {
            return redirect()->route('transfers.index')->with('error', 'Los productos deben coincidir para realizar el traslado');
        }


        DB::transaction(function () use ($request, $from_tank) {
            //crea transfer
            $transfer = Transfer::create([
                'from_tank_id' => $request->from_tank_id,
                'to_tank_id' => $request->to_tank_id,
                'product_id' => $from_tank->product_id,
                'quantity' => $request->quantity,
                'date' => now(),
            ]);

            //resta stock SOLO de tanque de origen, se suma al tanque de recepción solo al confirmar
            $from_tank->stored_quantity -= $request->quantity;
            $from_tank->save();

            $transfer->update([
                'recieved' => 1,
                'recieved_at' => now(),
            ]);

            //suma al tanque de recepción
            $toTank = $transfer->to_tank;
            $toTank->stored_quantity += $transfer->quantity;
            $toTank->save();

        });

        return redirect()->route('transfers.index')
            ->with('success', 'Distribución registrada y almacenamiento de origen actualizado');
    }

    public function destroy($id)
    {
        $transfer = Transfer::findOrFail($id);

        DB::transaction(function () use ($transfer) {
            //borra
            $transfer->update([
                'deleted' => 1,
            ]);

            //actualiza tanque de origen
            $fromTank = $transfer->from_tank;
            $fromTank->stored_quantity += $transfer->quantity;
            $fromTank->save();

            //si ya fue recibida, tambien actualiza tanque de recepción
            if ($transfer->recieved == 1) {
                $toTank = $transfer->to_tank;
                $toTank->stored_quantity -= $transfer->quantity;
                $toTank->save();
            }
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Distribución eliminada correctamente');
    }

    //usado para confirmar
    public function update($id)
    {
        $transfer = Transfer::findOrFail($id);

        DB::transaction(function () use ($transfer) {
            //cambia recibido a 1
            $transfer->update([
                'recieved' => 1,
                'recieved_at' => now(),
            ]);

            //suma al tanque de recepción
            $toTank = $transfer->to_tank;
            $toTank->stored_quantity += $transfer->quantity;
            $toTank->save();
        });


        return redirect()->route('transfers.index')
            ->with('success', 'Distribución actualizada exitosamente');
    }
}
