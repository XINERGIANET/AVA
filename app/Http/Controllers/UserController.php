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
     * Solo master y admin gestionan usuarios; admin queda acotado a su propia
     * sede y nunca puede ver/asignar el rol master (evita que un admin se
     * autoeleve o administre cuentas de otras sedes).
     */
    private function authorizeManageUsers()
    {
        $role = auth()->user()->role->nombre ?? '';
        if (!in_array($role, ['master', 'admin'], true)) {
            abort(403, 'No tienes permiso para acceder a la gestión de usuarios.');
        }
    }

    private function rolesForCurrentUser()
    {
        $isMaster = auth()->user()->role->nombre === 'master';
        return $isMaster ? Role::get() : Role::where('nombre', '!=', 'master')->get();
    }

    private function locationsForCurrentUser()
    {
        $isMaster = auth()->user()->role->nombre === 'master';
        return $isMaster
            ? Location::get()
            : Location::where('id', auth()->user()->location_id)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorizeManageUsers();

        $search = $request->input('search');

        $users = User::with('role')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function($query){
                $query->where('location_id', auth()->user()->location_id);
            })
            ->where('deleted', 0)
            ->paginate(10);

        $roles = $this->rolesForCurrentUser();
        $locations = $this->locationsForCurrentUser();
        $isles = Isle::where('location_id', auth()->user()->location_id)
            ->where('deleted', 0)
            ->get();

        return view('users.index', compact('users', 'roles', 'locations', 'isles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorizeManageUsers();

        $roles = $this->rolesForCurrentUser();
        $locations = $this->locationsForCurrentUser();
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
        $this->authorizeManageUsers();

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

        $currentUser = auth()->user();
        if ($currentUser->role->nombre !== 'master') {
            if ((int) $validated['location_id'] !== (int) $currentUser->location_id) {
                return back()->withInput()->withErrors([
                    'location_id' => 'Solo puedes crear usuarios para tu propia sede.'
                ]);
            }
            if ($role && $role->nombre === 'master') {
                return back()->withInput()->withErrors([
                    'rol_id' => 'No tienes permiso para asignar el rol master.'
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
            $currentUser = auth()->user();
            if (!in_array($currentUser->role->nombre ?? '', ['master', 'admin'], true)) {
                return response()->json([
                    'status' => false,
                    'error' => 'No tienes permiso para acceder a la gestión de usuarios.'
                ], 403);
            }

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

            if ($currentUser->role->nombre !== 'master') {
                $isOtherLocation = (int) $user->location_id !== (int) $currentUser->location_id;
                $isMasterTarget = ($user->role->nombre ?? '') === 'master';
                if ($isOtherLocation || $isMasterTarget) {
                    return response()->json([
                        'status' => false,
                        'error' => 'No tienes permiso para editar este usuario.'
                    ], 403);
                }
            }

            $roles = $this->rolesForCurrentUser();

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
            $currentUser = auth()->user();
            if (!in_array($currentUser->role->nombre ?? '', ['master', 'admin'], true)) {
                return response()->json([
                    'status' => false,
                    'error' => 'No tienes permiso para acceder a la gestión de usuarios.'
                ], 403);
            }

            $user = User::findOrFail($id);

            if ($currentUser->role->nombre !== 'master') {
                $isOtherLocation = (int) $user->location_id !== (int) $currentUser->location_id;
                $isMasterTarget = ($user->role->nombre ?? '') === 'master';
                if ($isOtherLocation || $isMasterTarget) {
                    return response()->json([
                        'status' => false,
                        'error' => 'No tienes permiso para editar este usuario.'
                    ], 403);
                }
            }

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

            if ($currentUser->role->nombre !== 'master') {
                if ((int) $validated['location_id'] !== (int) $currentUser->location_id) {
                    return response()->json([
                        'status' => false,
                        'error' => 'Solo puedes asignar tu propia sede.'
                    ], 403);
                }
                $targetRole = Role::find($validated['rol_id']);
                if ($targetRole && $targetRole->nombre === 'master') {
                    return response()->json([
                        'status' => false,
                        'error' => 'No tienes permiso para asignar el rol master.'
                    ], 403);
                }
            }

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
            $currentUser = auth()->user();
            if (!in_array($currentUser->role->nombre ?? '', ['master', 'admin'], true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No tienes permiso para acceder a la gestión de usuarios.'
                ], 403);
            }

            $user = User::where('id', $id)
                ->where('deleted', 0)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            if ($currentUser->role->nombre !== 'master') {
                $isOtherLocation = (int) $user->location_id !== (int) $currentUser->location_id;
                $isMasterTarget = ($user->role->nombre ?? '') === 'master';
                if ($isOtherLocation || $isMasterTarget) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No tienes permiso para eliminar este usuario.'
                    ], 403);
                }
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
