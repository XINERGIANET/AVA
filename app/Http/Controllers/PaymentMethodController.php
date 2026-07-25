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

        $query = PaymentMethod::with('location')->where('deleted', 0);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($isMaster) {
            if ($request->filled('location_id')) {
                if ($request->location_id === 'global') {
                    $query->whereNull('location_id');
                } else {
                    $query->where('location_id', $request->location_id);
                }
            }
            $locations = Location::where('deleted', 0)->orderBy('name')->get();
        } else {
            // Usuario no-master: sólo ve métodos globales y de su propia sede
            $userLocationId = $user->location_id;
            $query->where(function ($q) use ($userLocationId) {
                $q->whereNull('location_id')
                  ->orWhere('location_id', $userLocationId);
            });
            $locations = Location::where('deleted', 0)->where('id', $userLocationId)->get();
        }

        $paymentMethods = $query->orderBy('id', 'asc')->paginate(15);

        return view('payment_methods.index', compact('paymentMethods', 'locations', 'isMaster', 'user'));
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
            'location_id' => 'nullable|exists:locations,id',
        ], [
            'name.required' => 'El nombre del método de pago es obligatorio.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'location_id.exists' => 'La sede seleccionada no es válida.',
        ]);

        if ($isMaster) {
            $locationId = $request->filled('location_id') ? $request->location_id : null;
        } else {
            // Un usuario de sede específica sólo puede crear métodos para su propia sede
            $locationId = $user->location_id;
            if (!$locationId) {
                return redirect()->back()->with('error', 'No tienes una sede asignada para registrar métodos de pago.');
            }
        }

        PaymentMethod::create([
            'name' => trim($request->name),
            'location_id' => $locationId,
            'deleted' => 0,
        ]);

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago registrado correctamente.');
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $isMaster = ($user->role->nombre ?? '') === 'master';
        $paymentMethod = PaymentMethod::findOrFail($id);

        // Si no es master y el método no pertenece a su sede, denegar
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

        if ($isMaster) {
            $locationId = $request->filled('location_id') ? $request->location_id : null;
        } else {
            $locationId = $user->location_id;
        }

        $paymentMethod->update([
            'name' => trim($request->name),
            'location_id' => $locationId,
        ]);

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

        if (!$isMaster && $paymentMethod->location_id != $user->location_id) {
            return redirect()->back()->with('error', 'No tienes permisos para eliminar métodos de pago globales o de otra sede.');
        }

        $paymentMethod->deleted = 1;
        $paymentMethod->save();

        return redirect()->route('payment-methods.index')->with('success', 'Método de pago eliminado correctamente.');
    }
}
