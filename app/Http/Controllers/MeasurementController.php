<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tank;
use App\Models\Measurement;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumperInterface;
use Illuminate\Support\Facades\DB;

class MeasurementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $locations = Location::with(['tanks' => function($q){
        //         return $q->where('deleted',0);
        //     }, 'tanks.product'])
        //     ->where('deleted',0)
        //     ->get();

        // return view('measurements.index',compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = Location::where('deleted', 0)
            ->get();

        $measurements = Measurement::where('deleted', 0)
            ->orderByDesc('id')->when(auth()->user()->role->nombre != 'master' && auth()->user()->location_id, function ($query) {
                $query->whereHas('location', function ($q) {
                    $q->where('location_id', auth()->user()->location_id);
                });
            })
            ->paginate(15);

        return view('measurements.create', compact('locations', 'measurements'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'amount'      => 'required|numeric',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        // valores por defecto/normalización
        $validated['amount'] = round(floatval($validated['amount']), 2);
        $validated['deleted'] = 0;

        DB::beginTransaction();
        try {
            Measurement::create($validated);
            DB::commit();

            return redirect()->route('measurements.create')
                ->with('success', 'Medición registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al guardar medición: ' . $e->getMessage()]);
        }
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
