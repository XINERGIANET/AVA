<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Transaction extends Model
{

    protected $fillable = [
        'user_id',
        'location_id',
        'isle_id',
        'type',
        'description',
        'amount',
        'date',
        'status',
    ];

	protected $dates = [
		'date',
	];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function isle()
    {
        return $this->belongsTo(Isle::class, 'isle_id');
    }
}
