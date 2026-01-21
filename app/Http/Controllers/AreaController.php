<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener solo las áreas activas (estado = 0)
        $areas = Area::select('id', 'area', 'order_id', 'prod1', 'cantidad1', 'prod2', 'cantidad2', 'prod3', 'cantidad3', 'estado', 'created_at', 'updated_at')
            ->where('estado', 0)
            ->paginate(5);
        return response()->json($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:255',
            'order_id' => 'nullable|integer',
            'prod1' => 'nullable|numeric',
            'cantidad1' => 'nullable|numeric',
            'prod2' => 'nullable|numeric',
            'cantidad2' => 'nullable|numeric',
            'prod3' => 'nullable|numeric',
            'cantidad3' => 'nullable|numeric',
        ]);

        $area = Area::create([
            'area' => $request->area,
            'order_id' => $request->order_id,
            'prod1' => $request->prod1,
            'cantidad1' => $request->cantidad1,
            'prod2' => $request->prod2,
            'cantidad2' => $request->cantidad2,
            'prod3' => $request->prod3,
            'cantidad3' => $request->cantidad3,
            'estado' => 0,
        ]);

        return redirect()->route('oacs.index')->with('success', 'Área registrada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $area = Area::select('id', 'area', 'order_id', 'prod1', 'cantidad1', 'prod2', 'cantidad2', 'prod3', 'cantidad3', 'estado', 'created_at', 'updated_at')
            ->where('estado', 0)
            ->where('order_id', $id)
            ->get();

        return response()->json($area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'area' => 'required|string|max:255',
            'order_id' => 'nullable|integer',
            'prod1' => 'nullable|integer',
            'cantidad1' => 'nullable|numeric',
            'prod2' => 'nullable|integer',
            'cantidad2' => 'nullable|numeric',
            'prod3' => 'nullable|integer',
            'cantidad3' => 'nullable|numeric',
        ]);

        $area = Area::findOrFail($id);
        $area->update([
            'area' => $request->area,
            'order_id' => $request->order_id,
            'prod1' => $request->prod1,
            'cantidad1' => $request->cantidad1,
            'prod2' => $request->prod2,
            'cantidad2' => $request->cantidad2,
            'prod3' => $request->prod3,
            'cantidad3' => $request->cantidad3,
        ]);

        return response()->json(['success' => true, 'message' => 'Área actualizada exitosamente.', 'data' => $area]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->update(['estado' => 1]); // Cambiar estado a 1 (eliminado)
        return response()->json(['success' => true, 'message' => 'Área eliminada correctamente.']);
    }
}
