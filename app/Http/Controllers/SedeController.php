<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class SedeController extends Controller
{
    /**
     * La gestión de sedes es de alcance global (crear/renombrar/eliminar una
     * sede afecta a toda la empresa), así que queda reservada a master —
     * igual que ya lo oculta el sidebar, pero ahora también en el backend.
     */
    private function authorizeMaster()
    {
        if ((auth()->user()->role->nombre ?? '') !== 'master') {
            abort(403, 'Solo master puede gestionar sedes.');
        }
    }

    public function index()
    {
        $this->authorizeMaster();

        // Obtener solo las sedes activas (deleted = 0)
        $sedes = Location::where('deleted', 0)->paginate(15);
        return view('sedes.index', compact('sedes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeMaster();

        return view('sedes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorizeMaster();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Location::create([
            'name' => $request->name,
            'deleted' => 0, // Por defecto, la sede está activa
        ]);

        return redirect()->route('sedes.index')->with('success', 'Sede registrada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorizeMaster();

        $sede = Location::findOrFail($id);
        return response()->json($sede);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorizeMaster();

        $sede = Location::findOrFail($id);
        return view('sedes.edit', compact('sede'));
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
        $this->authorizeMaster();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $sede = Location::findOrFail($id);
        $sede->update($request->all());

        return redirect()->route('sedes.index')
            ->with('success', 'Sede actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorizeMaster();

        $sede = Location::findOrFail($id);
        $sede->update(['deleted' => 1]); // Cambiar estado a 1 (eliminado)
        return redirect()->route('sedes.index')
            ->with('success', 'Sede eliminada correctamente.');
    }
}
