<?php

namespace App\Http\Controllers;

use App\Models\Discharge;
use App\Models\DischargeDetail;
use App\Models\Location;
use App\Models\Product;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DischargeController extends Controller
{
    // app/Http/Controllers/DischargeController.php

    // DischargeController.php
    public function index()
    {
        $locations = Location::where('deleted', '0')->get();
        $products = Product::where('deleted', '0')->get();
        $discharges = Discharge::with(['location', 'first_detail.product', 'first_detail.tank'])->get();

        // Obtener todos los tanques agrupados por sede
        $tanks = Tank::where('deleted', '0')
            ->get()
            ->groupBy('location_id');

        return view('discharges.index', compact(
            'locations',
            'products',
            'discharges',
            'tanks'
        ));
    }
    public function create()
    {
        $sedes = Sede::where('estado', '0')->get();
        $products = Product::where('estado', '0')->get();

        return view('discharges.create', compact('sedes', 'products'));
    }

    public function store(Request $request)
    {
        $validated =$request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'location_id' => 'required|exists:locations,id',
            'product_id' => 'required|exists:products,id',
            'tank_id' => 'required|exists:tanks,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Registrar la distribución
            $discharge = Discharge::create([
                'purchase_id' => $request->purchase_id,
                'location_id' => $request->location_id,
                'date' => now(),
                'total_quantity' => $request->quantity,
                'deleted' => 0
            ]);

            $detail = DischargeDetail::create([
                'discharge_id' => $discharge->id,
                'tank_id' => $request->tank_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);

            // Actualizar el tanque (sumar la cantidad)
            $tank = $detail->tank;
            $tank->stored_quantity += $request->quantity;
            $tank->save();
        });

        return redirect()->route('discharges.index')
            ->with('success', 'Distribución registrada y almacenamiento actualizado');
    }
}
