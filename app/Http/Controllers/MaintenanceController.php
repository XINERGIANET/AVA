<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Maintenance;

class MaintenanceController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $locations = $isMaster
            ? Location::where('deleted', 0)->orderBy('name')->get()
            : Location::where('deleted', 0)->where('id', $currentUser->location_id)->get();

        $maintenances = Maintenance::with('location')
            ->where('deleted', 0)
            ->when(!$isMaster, fn($q) => $q->where('location_id', $currentUser->location_id))
            ->orderByDesc('id')
            ->paginate(15);

        return view('maintenances.index', compact('maintenances', 'locations', 'isMaster'));
    }

    public function create()
    {
        return view('maintenances.create');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        if (!$isMaster && $request->location_id != $currentUser->location_id) {
            return redirect()->route('maintenances.index')->with('error', 'No tiene permisos para registrar mantenimientos en otra sede.');
        }

        Maintenance::create([
            'location_id' => $request->location_id,
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
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $maintenance = Maintenance::findOrFail($id);

        if (!$isMaster && $maintenance->location_id != $currentUser->location_id) {
            abort(403, 'No tiene permisos para modificar este mantenimiento');
        }

        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $maintenance->update([
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('maintenances.index')->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $maintenance = Maintenance::findOrFail($id);

        if (!$isMaster && $maintenance->location_id != $currentUser->location_id) {
            abort(403, 'No tiene permisos para eliminar este mantenimiento');
        }

        $maintenance->update(['deleted' => 1]);

        return redirect()->route('maintenances.index')->with('success', 'Mantenimiento eliminado correctamente.');
    }
}
