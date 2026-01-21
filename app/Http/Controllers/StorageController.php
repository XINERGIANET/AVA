<?php

// app/Http/Controllers/StorageController.php
namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tank;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index( Request $request)
    {
        $location_id = $request->location_id;
        $user = auth()->user();

        $tanks = Tank::where('location_id', $user->location_id)
            ->with(['location','product'])
            ->get();

        $locations = Location::all(); // Obtener todas las sedes para el filtro
        
        return view('storages.index', compact('tanks', 'locations'));
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