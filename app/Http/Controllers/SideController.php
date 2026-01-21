<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Side;
use App\Models\FuelPump;

class SideController extends Controller
{
    public function index()
    {
        $sides = Side::with('fuelpump')
            ->where('deleted', 0)
            ->paginate(15);

        $fuelpumps = FuelPump::where('deleted', 0)->get();

        return view('sides.index', compact('sides', 'fuelpumps'));
    }

    /**
     * Show the form for creating a new side.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fuelpumps = FuelPump::where('deleted', 0)->get();

        return view('sides.create', compact('fuelpumps'));
    }

    /**
     * Store a newly created side in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'fuel_pump_id' => 'required|integer|exists:fuel_pumps,id',
        ]);

        Side::create([
            'name'         => $request->name,
            'fuel_pump_id' => $request->fuel_pump_id,
            'deleted'      => 0,
        ]);

        return redirect()->route('sides.index')->with('success', 'Lado registrado correctamente.');
    }

    /**
     * Display the specified side.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $side = Side::findOrFail($id);
        return response()->json($side);
    }

    /**
     * Show the form for editing the specified side.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $side = Side::findOrFail($id);
        $fuelpumps = FuelPump::where('deleted', 0)->get();

        return view('sides.edit', compact('side', 'fuelpumps'));
    }

    /**
     * Update the specified side in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'fuel_pump_id' => 'required|integer|exists:fuel_pumps,id',
        ]);

        $side = Side::findOrFail($id);
        $side->update([
            'name'         => $request->name,
            'fuel_pump_id' => $request->fuel_pump_id,
        ]);

        return redirect()->route('sides.index')
            ->with('success', 'Lado actualizado exitosamente.');
    }

    /**
     * Remove the specified side from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $side = Side::findOrFail($id);
        $side->update(['deleted' => 1]);

        return redirect()->route('sides.index')
            ->with('success', 'Lado eliminado correctamente.');
    }
}