<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $collaborators = Employee::where('deleted', 0)
            ->when(!$isMaster, fn($q) => $q->where('location_id', $currentUser->location_id))
            ->paginate(10);
        $locations = Location::where('deleted', 0)->when(!$isMaster, function ($query) use ($currentUser) {
            $query->where('id', $currentUser->location_id);
        })->get();
        return view('collaborators.index', compact('collaborators', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('collaborators.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document' => 'required|string|max:11',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:15',
            'location_id' => 'required|exists:locations,id',
            'address' => 'required|string|max:255',
            'pin' => 'required|string|max:4|min:4'
        ]);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';
        if (!$isMaster && $request->location_id != $currentUser->location_id) {
            return redirect()->back()->withInput()->withErrors(['location_id' => 'No tiene permisos para registrar colaboradores en otra sede.']);
        }

        // Verificar que el PIN no exista en otro colaborador activo
        $existingPin = Employee::where('pin', $request->pin)
            ->where('deleted', 0)
            ->first();

        if ($existingPin) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pin' => 'Este PIN ya está siendo utilizado por otro colaborador activo.']);
        }

        Employee::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'document' => $request->document,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'location_id' => $request->location_id,
            'address' => $request->address,
            'pin' => $request->pin,
            'deleted' => 0, // Por defecto, el colaborador está activo
        ]);

        return redirect()->route('collaborators.index')->with('success', 'Colaborador registrado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return view('collaborators.show', compact('collaborators'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return view('collaborators.edit', compact('collaborators'));
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
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document' => 'required|string|max:11',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'pin' => 'required|string|max:4|min:4'
        ]);

        // Buscar el colaborador por ID
        $collaborator = Employee::findOrFail($id);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';
        if (!$isMaster && $collaborator->location_id != $currentUser->location_id) {
            abort(403, 'No tiene permisos para modificar este colaborador');
        }

        // Verificar que el PIN no exista en otro colaborador activo (excepto el actual)
        $existingPin = Employee::where('pin', $request->pin)
            ->where('deleted', 0)
            ->where('id', '!=', $id)
            ->first();

        if ($existingPin) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pin' => 'Este PIN ya está siendo utilizado por otro colaborador activo.']);
        }

        // Actualizar los datos del colaborador
        $collaborator->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'document' => $request->document,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'address' => $request->address,
            'pin' => $request->pin,
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('collaborators.index')->with('success', 'Colaborador actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Buscar el colaborador por ID
        $collaborator = Employee::findOrFail($id);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';
        if (!$isMaster && $collaborator->location_id != $currentUser->location_id) {
            abort(403, 'No tiene permisos para eliminar este colaborador');
        }

        // Cambiar el estado del colaborador a inactivo (1)
        $collaborator->update(['deleted' => 1]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('collaborators.index')->with('success', 'Colaborador eliminado correctamente.');
    }
}
