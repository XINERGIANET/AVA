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
        $locations = Location::all();
        $currentLocationId = $request->input('location_id', auth()->user()->location_id);

        $islas = Isle::where('location_id', $currentLocationId)
            ->with(['sides' => function($query) {
                $query->orderByRaw('CAST(side AS UNSIGNED) ASC')
                    ->orderBy('name', 'asc')
                    ->with('product'); 
            }])->get();

        foreach ($islas as $isla) {
            foreach ($isla->sides as $lado) {
                
                $lastMeasurement = Measurement::where('pump_id', $lado->id)
                    ->where('location_id', $currentLocationId)
                    ->where('deleted', 0)
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

                $totalSold = SaleDetail::whereHas('sale', function ($q) use ($currentLocationId) {
                    $q->where('location_id', $currentLocationId)
                    ->where('deleted', 0)
                    ->whereDate('date', now()); 
                })
                ->where('pump_id', $lado->id)
                ->sum('quantity');

                $lado->venta_sistema_actual = $totalSold;
            }
        }

        return view('flowmeter.create', compact('islas', 'locations', 'currentLocationId'));
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
            'location_id' => 'required'
        ]);

        $userId = auth()->user()->id;
        $locationId = $request->input('location_id'); 
        $date = now()->format('Y-m-d');

        try {
            DB::beginTransaction();

            $lecturas = $request->input('lecturas');
            $savedCount = 0;

            foreach ($lecturas as $sideId => $data) {
                if (isset($data['final']) && $data['final'] !== null && $data['final'] !== '') {

                    $initial   = floatval($data['inicial'] ?? 0);
                    $final     = floatval($data['final']);
                    $theorical = floatval($data['teorico'] ?? 0);
                    $physicalSale = $final - $initial;
                    $difference = $physicalSale - $theorical;

                    Measurement::create([
                        'location_id'       => $locationId,
                        'user_id'           => $userId,
                        'pump_id'           => $sideId, 
                        'amount_initial'    => $initial,
                        'amount_final'      => $final,
                        'amount_theorical'  => $theorical,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
