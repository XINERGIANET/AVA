<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Transaction;
use App\Models\Pump;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Isle;
use App\Models\Measurement;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlowMeterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Asegúrate de importar el modelo Location arriba
    public function create(Request $request)
    {
        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $locations = $isMaster
            ? Location::where('deleted', 0)->get()
            : Location::where('deleted', 0)->where('id', $currentUser->location_id)->get();

        $currentLocationId = $isMaster
            ? $request->input('location_id', $currentUser->location_id)
            : $currentUser->location_id;

        $selectedDate = $request->input('date', date('Y-m-d'));

        $islas = Isle::where('location_id', $currentLocationId)
            ->where('deleted', 0)
            ->with(['sides' => function($query) {
                $query->where('deleted', 0)
                    ->with('product'); 
            }])->get();

        foreach ($islas as $isla) {
            $sortedSides = $isla->sides->sort(function($a, $b) {
                // 1. Nombre de la máquina
                $c = strcmp($a->name ?? '', $b->name ?? '');
                if ($c !== 0) return $c;

                // 2. Nombre del producto
                $pNameA = $a->product->name ?? '';
                $pNameB = $b->product->name ?? '';
                $c = strcmp($pNameA, $pNameB);
                if ($c !== 0) return $c;

                // 3. Número de lado
                return ((int)$a->side) <=> ((int)$b->side);
            })->values();
            $isla->setRelation('sides', $sortedSides);

            foreach ($isla->sides as $lado) {
                
                $lastMeasurement = Measurement::where('pump_id', $lado->id)
                    ->where('location_id', $currentLocationId)
                    ->where('deleted', 0)
                    ->whereDate('date', '<=', $selectedDate)
                    ->orderBy('date', 'desc') 
                    ->orderBy('id', 'desc')   
                    ->first();

                if ($lastMeasurement) {
                    if ($lastMeasurement->amount_final > 0) {
                        $lado->ultima_lectura = $lastMeasurement->amount_final;
                    } else {
                        $lado->ultima_lectura = $lastMeasurement->amount_initial;
                    }
                } else {
                    $lado->ultima_lectura = 0;
                }
            }
        }

        return view('flowmeter.create', compact('islas', 'locations', 'currentLocationId', 'selectedDate'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'lecturas' => 'required|array',
            'location_id' => 'required|integer|exists:locations,id',
            'date' => 'nullable|date'
        ]);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';
        $userId = $currentUser->id;
        $locationId = $request->input('location_id');

        if (!$isMaster && (int) $locationId !== (int) $currentUser->location_id) {
            return back()->with('error', 'Solo puedes registrar contómetros de tu propia sede.');
        }

        $date = $request->input('date') ?: now()->format('Y-m-d');

        try {
            DB::beginTransaction();

            $lecturas = $request->input('lecturas');
            $savedCount = 0;

            foreach ($lecturas as $sideId => $data) {
                if (isset($data['final']) && $data['final'] !== null && $data['final'] !== '') {

                    $initial   = floatval($data['inicial'] ?? 0);
                    $final     = floatval($data['final']);
                    $difference = $final - $initial;

                    Measurement::create([
                        'location_id'       => $locationId,
                        'user_id'           => $userId,
                        'pump_id'           => $sideId, 
                        'amount_initial'    => $initial,
                        'amount_final'      => $final,
                        'amount_theorical'  => 0,
                        'amount_difference' => $difference,
                        'date'              => $date,
                        'deleted'           => 0
                    ]);
                    
                    $savedCount++;
                }
            }

            DB::commit();

            if ($savedCount > 0) {
                return redirect()->route('flowmeters.create', ['location_id' => $locationId])
                    ->with('success', "Se registraron correctamente $savedCount lecturas.");
            } else {
                return redirect()->back()
                    ->with('warning', 'No se ingresó ningún valor final, no se guardaron datos.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function historico(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $location_id = $request->location_id;
        $isle_id = $request->isle_id;
        $pump_id = $request->pump_id;
        $user_id = $request->user_id;

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        $locations = Location::where('deleted', 0)
            ->when(!$isMaster && $currentUser->location_id, function ($q) use ($currentUser) {
                $q->where('id', $currentUser->location_id);
            })
            ->get();

        $effectiveLocationId = $location_id ?: (!$isMaster ? $currentUser->location_id : null);

        $isles = Isle::where('deleted', 0)
            ->when($effectiveLocationId, function ($q) use ($effectiveLocationId) {
                $q->where('location_id', $effectiveLocationId);
            })
            ->get();

        $pumpsQuery = Pump::with(['product', 'isle'])
            ->where('deleted', 0);

        if ($isle_id) {
            $pumpsQuery->where('isle_id', $isle_id);
        } elseif ($effectiveLocationId) {
            $pumpsQuery->whereIn('isle_id', $isles->pluck('id'));
        }

        $pumps = $pumpsQuery->get();

        if ($isMaster) {
            $users = User::where('deleted', false)->get();
        } else {
            $users = User::whereHas('role', function ($q) {
                $q->where('nombre', 'worker');
            })
                ->where('location_id', $currentUser->location_id)
                ->where('deleted', false)
                ->get();
        }

        $query = Measurement::with(['user', 'location', 'pump.isle', 'pump.product'])
            ->when($start_date, function ($q) use ($start_date) {
                $q->whereDate('date', '>=', $start_date);
            })
            ->when($end_date, function ($q) use ($end_date) {
                $q->whereDate('date', '<=', $end_date);
            })
            ->when($location_id, function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            })
            ->when($user_id, function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->when($isle_id, function ($q) use ($isle_id) {
                $q->whereHas('pump', function ($p) use ($isle_id) {
                    $p->where('isle_id', $isle_id);
                });
            })
            ->when($pump_id, function ($q) use ($pump_id) {
                $q->where('pump_id', $pump_id);
            })
            ->where('deleted', 0)
            ->orderBy('date', 'desc')
            ->orderByDesc('id');

        if (!$isMaster && $currentUser->location_id) {
            $query->where('location_id', $currentUser->location_id);
        }

        $measurements = $query->paginate(20)->withQueryString();

        return view('flowmeter.historico', compact('measurements', 'locations', 'isles', 'pumps', 'users', 'isMaster'));
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $request->validate([
            'date' => 'required|date',
            'amount_initial' => 'required|numeric',
            'amount_final' => 'required|numeric',
        ]);

        $measurement = Measurement::findOrFail($id);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        if (!$isMaster && (int) $measurement->location_id !== (int) $currentUser->location_id) {
            return back()->with('error', 'No tienes permiso para editar este registro.');
        }

        $initial = floatval($request->input('amount_initial'));
        $final = floatval($request->input('amount_final'));
        $difference = $final - $initial;

        $measurement->update([
            'date' => $request->input('date'),
            'amount_initial' => $initial,
            'amount_final' => $final,
            'amount_difference' => $difference,
        ]);

        return redirect()->back()->with('success', 'Registro de contómetro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $measurement = Measurement::findOrFail($id);

        $currentUser = auth()->user();
        $isMaster = $currentUser->role->nombre === 'master';

        if (!$isMaster && (int) $measurement->location_id !== (int) $currentUser->location_id) {
            return back()->with('error', 'No tienes permiso para eliminar este registro.');
        }

        $measurement->update(['deleted' => 1]);

        return redirect()->back()->with('success', 'Registro de contómetro eliminado correctamente.');
    }
}
