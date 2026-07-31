<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Location;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of active payment methods.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isMaster = ($user->role->nombre ?? '') === 'master';

        $locations = Location::where('deleted', 0)->orderBy('name')->get();

        if ($isMaster) {
            $query = PaymentMethod::with('location')->where('deleted', 0);

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('location_id')) {
                if ($request->location_id === 'global') {
                    $query->whereNull('location_id');
                } else {
                    $query->where('location_id', $request->location_id);
                }
            }

            $allMethods = $query->orderBy('id', 'asc')->get();

            // Group by trimmed lowercased name
            $grouped = $allMethods->groupBy(function ($item) {
                return mb_strtolower(trim($item->name));
            });

            // Transform into collection of grouped objects
            $transformed = $grouped->map(function ($items) {
                $first = $items->first();
                $isGlobal = $items->contains(fn($i) => is_null($i->location_id));
                $locationIds = $isGlobal ? [] : $items->pluck('location_id')->filter()->unique()->values()->toArray();
                $locationsList = $isGlobal ? collect([]) : $items->pluck('location')->filter()->unique('id')->values();

                return (object) [
                    'id' => $first->id,
                    'name' => $first->name,
                    'is_global' => $isGlobal,
                    'location_ids' => $locationIds,
                    'locations' => $locationsList,
                    'location_id' => $first->location_id,
                    'location' => $first->location,
                ];
            })->values();

            // Paginate manually
            $page = (int) $request->input('page', 1);
            $perPage = 15;
            $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformed->slice(($page - 1) * $perPage, $perPage)->values(),
                $transformed->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('payment_methods.index', [
                'paymentMethods' => $paginatedItems,
                'locations' => $locations,
                'isMaster' => true,
                'user' => $user
            ]);
        } else {
            $userLocationId = $user->location_id;
            $query = PaymentMethod::with('location')
                ->where('deleted', 0)
                ->where(function ($q) use ($userLocationId) {
                    $q->whereNull('location_id')
                      ->orWhere('location_id', $userLocationId);
                });

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $paymentMethods = $query->orderBy('id', 'asc')->paginate(15);
            $locations = Location::where('deleted', 0)->where('id', $userLocationId)->get();

            return view('payment_methods.index', compact('paymentMethods', 'locations', 'isMaster', 'user'));
        }
    }

    /**
     * Store a newly created payment method in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isMaster = ($user->role->nombre ?? '') === 'master';

        $request->validate([
            'name' => 'required|string|max:255',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'nullable|exists:locations,id',
            'location_id' => 'nullable|exists:locations,id',
        ], [
            'name.required' => 'El nombre del método de pago es obligatorio.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'location_ids.*.exists' => 'Una de las sedes seleccionadas no es válida.',
        ]);

        $name = trim($request->name);

        if ($isMaster) {
            if ($request->has('is_global') && $request->is_global == '1') {
                // Soft delete any previous entries with same name to avoid duplicate rows
                PaymentMethod::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->update(['deleted' => 1]);

                PaymentMethod::create([
                    'name' => $name,
                    'location_id' => null,
                    'deleted' => 0,
                ]);
            } elseif ($request->has('location_ids') && is_array($request->location_ids) && count($request->location_ids) > 0) {
                // Soft delete previous entries with same name
                PaymentMethod::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->update(['deleted' => 1]);

                foreach ($request->location_ids as $locId) {
                    PaymentMethod::create([
                        'name' => $name,
                        'location_id' => $locId,
                        'deleted' => 0,
                    ]);
                }
            } else {
                $locationId = $request->filled('location_id') ? $request->location_id : null;
                PaymentMethod::create([
                    'name' => $name,
                    'location_id' => $locationId,
                    'deleted' => 0,
                ]);
            }
        } else {
            $locationId = $user->location_id;
            if (!$locationId) {
                return redirect()->back()->with('error', 'No tienes una sede asignada para registrar métodos de pago.');
            }
            PaymentMethod::create([
                'name' => $name,
                'location_id' => $locationId,
                'deleted' => 0,
            ]);
        }

        return redirect()->route('payment-methods.index')->with('success', 'Método(s) de pago registrado(s) correctamente.');
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $isMaster = ($user->role->nombre ?? '') === 'master';
        $paymentMethod = PaymentMethod::findOrFail($id);

        if (!$isMaster && $paymentMethod->location_id != $user->location_id) {
            return redirect()->back()->with('error', 'No tienes permisos para modificar métodos de pago globales o de otra sede.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
        ], [
            'name.required' => 'El nombre del método de pago es obligatorio.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'location_id.exists' => 'La sede seleccionada no es válida.',
        ]);

        $newName = trim($request->name);
        $oldName = trim($paymentMethod->name);

        if ($isMaster) {
            // Soft-delete all existing records for the old method name
            PaymentMethod::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($oldName)])->update(['deleted' => 1]);

            if ($request->has('is_global') && $request->is_global == '1') {
                PaymentMethod::create([
                    'name' => $newName,
                    'location_id' => null,
                    'deleted' => 0,
                ]);
            } elseif ($request->has('location_ids') && is_array($request->location_ids) && count($request->location_ids) > 0) {
                foreach ($request->location_ids as $locId) {
                    PaymentMethod::create([
                        'name' => $newName,
                        'location_id' => $locId,
                        'deleted' => 0,
                    ]);
                }
            } else {
                $locationId = $request->filled('location_id') ? $request->location_id : null;
                PaymentMethod::create([
                    'name' => $newName,
                    'location_id' => $locationId,
                    'deleted' => 0,
                ]);
            }
        } else {
            $paymentMethod->update([
                'name' => $newName,
            ]);
        }

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago actualizado correctamente.');
    }

    /**
     * Soft delete the specified payment method.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $isMaster = ($user->role->nombre ?? '') === 'master';
        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($isMaster) {
            PaymentMethod::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($paymentMethod->name))])->update(['deleted' => 1]);
        } else {
            if ($paymentMethod->location_id != $user->location_id) {
                return redirect()->back()->with('error', 'No tienes permisos para eliminar métodos de pago globales o de otra sede.');
            }
            $paymentMethod->deleted = 1;
            $paymentMethod->save();
        }

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago eliminado correctamente.');
    }
}
