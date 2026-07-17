<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Isle;

class IsleController extends Controller
{
    private function locationsForCurrentUser()
    {
        $user = auth()->user();
        if ($user->role->nombre === 'master') {
            return Location::where('deleted', 0)->get();
        }
        return Location::where('deleted', 0)->where('id', $user->location_id)->get();
    }

    public function index(Request $request)
    {
        // Obtener solo las islas activas (deleted = 0) con sus ubicaciones relacionadas
        $isles = Isle::with('location')
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('location', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->when($request->filled('location_id'), function ($query) use ($request) {
                $query->where('location_id', $request->location_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->where('deleted', 0)
            ->paginate(15);

        $locations = Location::where('deleted', 0)->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
            $query->where('id', auth()->user()->location_id);
        })->get();

        return view('isles.index', compact('isles', 'locations'));
    }

    /**
     * Show the form for creating a new isle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = $this->locationsForCurrentUser();

        return view('isles.create', compact('locations'));
    }

    /**
     * Store a newly created isle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location_id' => 'required|integer|exists:locations,id', // Validación de location_id
        ]);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) $request->location_id !== (int) $user->location_id) {
            return back()->withInput()->withErrors([
                'location_id' => 'Solo puedes crear islas para tu propia sede.'
            ]);
        }

        Isle::create([
            'name'        => $request->name,
            'location_id' => $request->location_id, // ID de la sede
            'deleted'     => 0, // Por defecto, la isla está activa
        ]);

        return redirect()->route('isles.index')->with('success', 'Isla registrada correctamente.');
    }

    /**
     * Display the specified isle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $isle = Isle::findOrFail($id);
        return response()->json($isle);
    }

    /**
     * Show the form for editing the specified isle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $isle = Isle::findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) $isle->location_id !== (int) $user->location_id) {
            abort(403, 'No tienes permiso para editar esta isla.');
        }

        $locations = $this->locationsForCurrentUser();

        return view('isles.edit', compact('isle', 'locations'));
    }

    /**
     * Update the specified isle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location_id' => 'required|integer|exists:locations,id', // Validación de location_id
        ]);

        $isle = Isle::findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master') {
            if ((int) $isle->location_id !== (int) $user->location_id) {
                abort(403, 'No tienes permiso para editar esta isla.');
            }
            if ((int) $request->location_id !== (int) $user->location_id) {
                return back()->withInput()->withErrors([
                    'location_id' => 'Solo puedes asignar tu propia sede.'
                ]);
            }
        }

        $isle->update([
            'name'        => $request->name,
            'location_id' => $request->location_id, // Actualizar el ID de la sede
        ]);

        return redirect()->route('isles.index')
            ->with('success', 'Isla actualizada exitosamente.');
    }

    /**
     * Remove the specified isle from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isle = Isle::findOrFail($id);

        $user = auth()->user();
        if ($user->role->nombre !== 'master' && (int) $isle->location_id !== (int) $user->location_id) {
            abort(403, 'No tienes permiso para eliminar esta isla.');
        }

        $isle->update(['deleted' => 1]); // Cambiar el estado de eliminado

        return redirect()->route('isles.index')
            ->with('success', 'Isla eliminada correctamente.');
    }
}
