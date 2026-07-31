<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Isle;
use App\Models\Pump;
use App\Models\Product;
use App\Models\Location;

class PumpController extends Controller
{
    private function islesForCurrentUser()
    {
        $user = auth()->user();
        return Isle::where('deleted', 0)
            ->when($user->role->nombre !== 'master' && $user->location_id, function ($query) use ($user) {
                $query->where('location_id', $user->location_id);
            })
            ->get();
    }

    public function index(Request $request)
    {
        $fuelpumps = Pump::with('isle','product')  // Obtener la relación con Isla
            ->where('deleted', 0)
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('isle', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->when($request->filled('isle_id'), function ($query) use ($request) {
                $query->where('isle_id', $request->isle_id);
            })
            ->when($request->filled('product_id'), function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
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
        $isles = $this->islesForCurrentUser();

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

        $user = auth()->user();
        if ($user->role->nombre !== 'master') {
            $isle = Isle::find($request->isle_id);
            if (!$isle || (int) $isle->location_id !== (int) $user->location_id) {
                return back()->withInput()->withErrors([
                    'isle_id' => 'Solo puedes crear bombas para islas de tu propia sede.'
                ]);
            }
        }

        Pump::create([
            'name'    => $request->name,
            'isle_id' => $request->isle_id, 
            'product_id' => $request->product_id, 
            'side' => $request->side, 
            'status' => $request->status ?? 'activo',
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
        $fuelpumps = Pump::with('isle')->findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) ($fuelpumps->isle->location_id ?? 0) !== (int) $user->location_id) {
            abort(403, 'No tienes permiso para editar esta bomba.');
        }

        $isles = $this->islesForCurrentUser();

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
            'status' => 'nullable|string|in:activo,inoperativo',
        ]);

        $fuelpumps = Pump::with('isle')->findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master') {
            if ((int) ($fuelpumps->isle->location_id ?? 0) !== (int) $user->location_id) {
                abort(403, 'No tienes permiso para editar esta bomba.');
            }
            $newIsle = Isle::find($request->isle_id);
            if (!$newIsle || (int) $newIsle->location_id !== (int) $user->location_id) {
                return back()->withInput()->withErrors([
                    'isle_id' => 'Solo puedes asignar islas de tu propia sede.'
                ]);
            }
        }

        $fuelpumps->update([
            'name'    => $request->name,
            'isle_id' => $request->isle_id,
            'product_id' => $request->product_id,
            'side' => $request->side, 
            'status' => $request->status ?? $fuelpumps->status ?? 'activo',
        ]);

        return redirect()->route('fuelpumps.index')
            ->with('success', 'Bomba de combustible actualizada exitosamente.');
    }

    public function toggleStatus($id)
    {
        $fuelpump = Pump::with('isle')->findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) ($fuelpump->isle->location_id ?? 0) !== (int) $user->location_id) {
            abort(403, 'No tienes permiso para modificar esta bomba.');
        }

        $newStatus = ($fuelpump->status === 'inoperativo') ? 'activo' : 'inoperativo';
        $fuelpump->update(['status' => $newStatus]);

        $msg = $newStatus === 'inoperativo' ? 'Surtidor marcado como inoperativo.' : 'Surtidor activado correctamente.';
        return redirect()->route('fuelpumps.index')->with('success', $msg);
    }

    /**
     * Remove the specified fuel pump from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fuelpumps = Pump::with('isle')->findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) ($fuelpumps->isle->location_id ?? 0) !== (int) $user->location_id) {
            abort(403, 'No tienes permiso para eliminar esta bomba.');
        }

        $fuelpumps->update(['deleted' => 1]);

        return redirect()->route('fuelpumps.index')
            ->with('success', 'Bomba de combustible eliminada correctamente.');
    }
}