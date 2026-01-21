<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Isle;
use App\Models\Pump;
use App\Models\Product;
use App\Models\Location;

class PumpController extends Controller
{
    public function index()
    {
        $fuelpumps = Pump::with('isle','product')  // Obtener la relación con Isla
            ->where('deleted', 0)
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('isle', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->paginate(15);


        $isles = Isle::with('location')->where('deleted', 0)->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
            $query->whereHas('location', function ($q) {
                $q->where('location_id', auth()->user()->location_id);
            });
        })->get();
        $products = Product::where('deleted', 0)->get();

        return view('fuelpumps.index', compact('fuelpumps', 'isles','products'));
    }

    /**
     * Show the form for creating a new fuel pump.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $isles = Isle::where('deleted', 0)->get(); 

        return view('fuelpumps.create', compact('isles'));
    }

    /**
     * Store a newly created fuel pump in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'isle_id' => 'required|integer|exists:isles,id', 
            'product_id' => 'required|integer|exists:products,id', 
            'side' => 'required|integer', 
        ]);

        Pump::create([
            'name'    => $request->name,
            'isle_id' => $request->isle_id, 
            'product_id' => $request->product_id, 
            'side' => $request->side, 
            'deleted' => 0, // Por defecto, la bomba está activa
        ]);

        return redirect()->route('fuelpumps.index')->with('success', 'Bomba de combustible registrada correctamente.');
    }

    /**
     * Display the specified fuel pump.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fuelpumps = Pump::findOrFail($id);
        return response()->json($fuelpumps);
    }

    /**
     * Show the form for editing the specified fuel pump.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fuelpumps = Pump::findOrFail($id);
        $isles = Isle::where('deleted', 0)->get();

        return view('fuelpumps.edit', compact('fuelpumps', 'isles'));
    }

    /**
     * Update the specified fuel pump in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'product_id' => 'required|integer|exists:products,id', 
            'isle_id' => 'required|integer|exists:isles,id', 
            'side' => 'required|integer', 
        ]);

        $fuelpumps = Pump::findOrFail($id);
        $fuelpumps->update([
            'name'    => $request->name,
            'isle_id' => $request->isle_id,
            'product_id' => $request->product_id,
            'side' => $request->side, 
        ]);

        return redirect()->route('fuelpumps.index')
            ->with('success', 'Bomba de combustible actualizada exitosamente.');
    }

    /**
     * Remove the specified fuel pump from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fuelpumps = Pump::findOrFail($id);
        $fuelpumps->update(['deleted' => 1]); 

        return redirect()->route('fuelpumps.index')
            ->with('success', 'Bomba de combustible eliminada correctamente.');
    }
}