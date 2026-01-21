<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truck;

class PlaqueController extends Controller
{
    public function index()
    {
        $plaques = Truck::where('deleted', 0)->paginate(15);
        return view('plaques.index', compact('plaques'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('plaques.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'plate' => 'required|string|max:255',
        ]);
        Truck::create([
            'name' => $request->name,
            'description' => $request->description,
            'plate' => $request->plate,
            'deleted' => 0, // Por defecto, la placa está activa
        ]);
        return redirect()->route('plaques.index')->with('success', 'Placa registrada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plaque = Truck::findOrFail($id);
        return view('plaques.show', compact('plaque'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plaque = Truck::findOrFail($id);
        return view('plaques.edit', compact('plaque'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'plate' => 'required|string|max:255',
        ]);

        $plaque = Truck::findOrFail($id);

        $plaque->update([
            'name' => $request->name,
            'description' => $request->description,
            'plate' => $request->plate,
        ]);

        return redirect()->route('plaques.index')->with('success', 'Placa actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plaque = Truck::findOrFail($id);

        $plaque->update(['deleted' => 1]);

        return redirect()->route('plaques.index')->with('success', 'Placa eliminada correctamente.');
    }
}
