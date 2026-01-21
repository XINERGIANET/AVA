<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Isle;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::with('role')->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function($query){
            $query->whereHas('location', function ($q) {
                $q->where('location_id', auth()->user()->location_id);
            });
        }) ->where('deleted', 0)->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $roles = Role::get();
        $locations = Location::get();
        $isles = Isle::where('location_id', auth()->user()->location_id)
            ->where('deleted', 0)
            ->get();
        $users = User::with('role')->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function($query){
            $query->whereHas('location', function ($q) {
                $q->where('location_id', auth()->user()->location_id);
            });
        }) ->where('deleted', 0)->paginate(10);
        return view('users.create', compact('roles', 'users', 'locations', 'isles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar el campo 'name' requerido y único
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'rol_id' => 'required|integer|exists:roles,id',
            'location_id' => 'required|integer|exists:locations,id',
            'isle_id' => 'nullable|integer|exists:isles,id',
            'shift' => 'nullable|integer'
        ]);
        $role = Role::find($validated['rol_id']);
        if ($role && $role->nombre === 'worker') {
            if (empty($validated['isle_id'])) {
                return back()->withInput()->withErrors([
                    'isle_id' => 'Los trabajadores (workers) deben tener una isla asignada.'
                ]);
            }
        }
        // Crear el registro
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Encriptar la contraseña
            'role_id' => $validated['rol_id'],
            'location_id' => $validated['location_id'],
            'isle_id' => $validated['isle_id'],
            'deleted' => 0,
        ]);

        return redirect()->route('users.create')
            ->with('success', 'Usuario creado exitosamente.');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        try {
            $user = User::with('role')
                ->where('id', $id)
                ->where('deleted', 0)
                ->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'error' => 'Usuario no encontrado'
                ], 404);
            }

            $roles = Role::all();

            return response()->json([
                'status' => true,
                'message' => 'Datos de usuario para edicion',
                'data' => [
                    'user' => $user,
                    'roles' => $roles
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener datos para edicion' . $e->getMessage()
            ], 500);
        }
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
        try {
            // Reglas de validación
            $rules = [
                'name' => 'required|string|max:255',
                'email' => "required|string|max:255", // único excepto el actual
                'rol_id' => 'required|exists:roles,id',
                'location_id' => 'required|integer|exists:locations,id',
                'isle_id' => 'nullable|integer|exists:isles,id',
            ];

            // Si quiere cambiar contraseña, la validamos
            if ($request->filled('new_pass')) {
                $rules['new_pass'] = 'required|min:4';
            }

            $validated = $request->validate($rules);

            $user = User::findOrFail($id);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['rol_id'];
            $user->location_id = $validated['location_id'];
            $user->isle_id = $validated['isle_id'];
            // Solo actualiza contraseña si fue enviada
            if (!empty($validated['new_pass'])) {
                $user->password =  Hash::make($validated['new_pass']);
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)
                ->where('deleted', 0)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Soft delete - marcar como eliminado
            $user->update(['deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setEmployee(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|max:4',
        ]);

        $user = Auth::user();

        $employee = Employee::where('pin', $request->pin)->where('deleted', 0)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado'], 401);
        }

        $user->employee_id = $employee->id;
        $user->save();

        // Elimina la variable de sesión para que el modal no vuelva a aparecer
        $request->session()->forget('show_pin_modal');

        return response()->json(['success' => true]);
    }
}
