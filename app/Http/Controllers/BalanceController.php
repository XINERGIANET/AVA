<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Tank;
use App\Models\Measurement;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumperInterface;

// POR REFACTORIZAR
class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::with(['tanks' => function($q){
                return $q->where('deleted',0);
            }, 'tanks.product'])
            ->where('deleted',0)
            ->get();

        return view('measurements.index',compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'measurements' => 'required|array',
            'measurements.*.tank_id' => 'required|exists:tanks,id',
            'measurements.*.initial_measurement' => 'nullable|numeric',
            'measurements.*.final_measurement' => 'nullable|numeric',
            'measurements.*.purchased_quantity' => 'nullable|numeric',
            'measurements.*.sold_quantity' => 'nullable|numeric'
        ]);

        $date = $request->date;
        

        foreach($validated['measurements'] as $measurement){
            $product_id = Tank::findOrFail($measurement['tank_id'])->product_id;
            Measurement::updateOrCreate(
                [
                    'date' => $date,
                    'tank_id' => $measurement['tank_id'],
                ],[
                    'product_id' => $product_id,
                    'initial_measurement' => $measurement['initial_measurement'] ?? null,
                    'final_measurement' => $measurement['final_measurement'] ?? null,
                    'purchased_quantity' => $measurement['purchased_quantity'] ?? null,
                    'sold_quantity' => $measurement['sold_quantity'] ?? null,
                    'deleted' => 0,
                ]
            );
        }

        return response()->json(['status' => true, 'message' => 'Mediciones guardadas correctamente']);

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

    public function getMeasurements(Request $request){
        $date = $request->date;
        $location_id = $request->location_id;

        $measurements = Measurement::where('deleted',0)
            ->whereDate('date',$date)
            ->whereHas('tank', function($q) use ($location_id){
                $q->where('location_id',$location_id);
            })
            ->get();

        return response()->json($measurements);
    }
}
