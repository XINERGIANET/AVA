<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Isle extends Model
{
    public $timestamps = false;

    // mass assignable
    protected $fillable = [
        'name',
        'location_id',
        'cash_amount',
        'voult',
        'deleted',
    ];

    /**
     * Relación hacia la sede (location)
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
