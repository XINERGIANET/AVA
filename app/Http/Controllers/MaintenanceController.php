<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::where('deleted', 0)->paginate(15);
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        return view('maintenances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Maintenance::create([
            'date' => $request->date,
            'description' => $request->description,
            'deleted' => 0,
        ]);

        return redirect()->route('maintenances.index')->with('success', 'Mantenimiento registrada correctamente.');
    }

    public function show($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        return view('maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $maintenance = Maintenance::findOrFail($id);

        $maintenance->update([
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('maintenances.index')->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update(['deleted' => 1]);

        return redirect()->route('maintenances.index')->with('success', 'Mantenimiento eliminado correctamente.');
    }
}
