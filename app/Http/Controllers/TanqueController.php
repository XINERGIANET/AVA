<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tank;
use App\Models\Location;
use App\Models\Product;

class TanqueController extends Controller
{
    public function index()
    {
        // Obtener tanques activos con su sede y producto
        $tanques = Tank::where('tanks.deleted', 0)
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->where('tanks.location_id', auth()->user()->location_id);
            })
            ->join('locations', 'tanks.location_id', '=', 'locations.id')
            ->leftJoin('products', 'tanks.product_id', '=', 'products.id')
            ->select(
                'tanks.*',
                'locations.name as sede_nombre',
                'products.name as producto_nombre'
            )
            ->paginate(10);

        $sedes = Location::where('deleted', 0)->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
            $query->where('id', auth()->user()->location_id);
        })->get();
        
        $products = Product::where('deleted', 0)->get();

        return view('tanques.index', compact('tanques', 'sedes', 'products'));
    }

    public function create()
    {
        return view('tanques.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'product_id' => 'required|integer|exists:products,id',
            'is_reserve' => 'required|boolean',
        ]);

        // 1. Verificar duplicado exacto (todos los campos)
        $duplicadoExacto = Tank::where('location_id', $request->location_id)
            ->where('name', $request->name)
            ->where('capacity', $request->capacity)
            ->where('product_id', $request->product_id)
            ->where('deleted', 0)
            ->exists();

        if ($duplicadoExacto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['duplicate' => 'Ya existe un tanque con los mismos datos.']);
        }

        $conflictoProducto = Tank::where('location_id', $request->location_id)
            ->where('name', $request->name)
            ->where('product_id', '!=', $request->product_id)
            ->where('deleted', 0)
            ->exists();

        if ($conflictoProducto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['producto_conflict' => 'Este nombre de tanque ya está asociado a otro producto.']);
        }

        $is_reserve = $request->boolean('is_reserve') ? 1 : 0;

        // Crear el tanque
        Tank::create([
            'location_id' => $request->location_id,
            'name' => $request->name,
            'capacity' => $request->capacity,
            'product_id' => $request->product_id,
            'is_reserve' => $is_reserve,
            'deleted' => 0,
        ]);

        return redirect()->route('tanques.index')->with('success', 'Tanque registrado correctamente.');
    }


    public function show($id)
    {
        $tanque = Tank::findOrFail($id);
        return response()->json($tanque);
    }

    public function edit($id)
    {
        $tanque = Tank::findOrFail($id);
        $sedes = Location::where('deleted', 0)->get();
        $products = Product::where('deleted', 0)->get();
    
        return view('tanques.edit', compact('tanque', 'sedes', 'products'));
    }
 
    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'name'        => 'required|string|max:255',
            'capacity'    => 'required|numeric|min:0',
            'product_id'  => 'required|integer|exists:products,id', 
            'is_reserve' => 'required|boolean',
        ]);

        $tanque = Tank::findOrFail($id);

        $duplicado = Tank::where('id', '!=', $id)
            ->where('location_id', $request->location_id)
            ->where('name', $request->name)
            ->where('capacity', $request->capacity)
            ->where('product_id', $request->product_id) 
            ->where('deleted', 0)
            ->exists();

        if ($duplicado) {
            return back()->withInput()->withErrors(['duplicate' => 'Ya existe otro tanque con los mismos datos.']);
        }

        $conflicto = Tank::where('id', '!=', $id)
            ->where('location_id', $request->location_id)
            ->where('name', $request->name)
            ->where('product_id', '!=', $request->product_id) 
            ->where('deleted', 0)
            ->exists();

        if ($conflicto) {
            return back()->withInput()->withErrors(['producto_conflict' => 'Este nombre de tanque ya está asociado a otro producto.']);
        }

        $tanque->update($request->only(['location_id','name','capacity','product_id','is_reserve']));

        return redirect()->route('tanques.index')->with('success', 'Tanque actualizado exitosamente.');
    }



    public function destroy($id)
    {
        $tanque = Tank::findOrFail($id);
        $tanque->update(['deleted' => 1]); // Cambiar estado a 1 (eliminado)
        return redirect()->route('tanques.index')
            ->with('success', 'Tanque eliminado correctamente.');
    }
}
