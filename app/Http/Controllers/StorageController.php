<?php

// app/Http/Controllers/StorageController.php
namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tank;
use Illuminate\Http\Request;
use App\Models\Product;

class StorageController extends Controller
{
    public function index(Request $request)
    {
        $location_id = $request->location_id;
        $user = auth()->user();

        $tanks = Tank::where('location_id', $user->location_id)
            ->with(['location', 'product'])
            ->get();

        $locations = Location::all(); // Obtener todas las sedes para el filtro
        $products = Product::all();
        return view('storages.index', compact('tanks', 'locations', 'products'));
    }

    public function update(Request $request, $id)
    {
        try {
            $tank = Tank::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'capacity' => 'required|numeric|min:0',
                'stored_quantity' => 'required|numeric|min:0|lte:capacity',
                'location_id' => 'required|integer|exists:locations,id',
                'product_id' => 'required|integer|exists:products,id',
            ], [
                'stored_quantity.lte' => 'El stock disponible no puede superar la capacidad máxima del tanque.'
            ]);

            $tank->update([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'stored_quantity' => $request->stored_quantity,
                'location_id' => $request->location_id,
                'product_id' => $request->product_id,
            ]);

            return response()->json(['success' => true, 'message' => 'Tanque actualizado correctamente.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }
    public function getStorages()
    {
        $storages = Storage::with(['sede', 'product'])->get();

        return response()->json($storages);
    }

    public function getStoragesBySede($sedeId)
    {
        $storages = Storage::with(['sede', 'product'])
            ->where('sede_id', $sedeId)
            ->get();

        return response()->json($storages);
    }
}
