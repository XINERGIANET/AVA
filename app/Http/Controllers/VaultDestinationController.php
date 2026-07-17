<?php

namespace App\Http\Controllers;

use App\Models\VaultDestination;
use Illuminate\Http\Request;

class VaultDestinationController extends Controller
{
    public function index()
    {
        return response()->json(
            VaultDestination::where('deleted', 0)->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vault_destinations,name',
        ]);

        $destination = VaultDestination::create([
            'name' => $validated['name'],
            'deleted' => 0,
        ]);

        return response()->json([
            'success' => true,
            'destination' => $destination,
            'message' => 'Destino agregado correctamente.',
        ]);
    }

    public function destroy($id)
    {
        $destination = VaultDestination::findOrFail($id);
        $destination->update(['deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Destino eliminado correctamente.',
        ]);
    }
}
