<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// mediciones de serafin
class Measurement extends Model
{

	protected $fillable = [
		'location_id',
		'user_id',
		'pump_id',
		'amount_initial',
		'amount_final',
		'amount_theorical',	
		'amount_difference',
		'date',
		'description',
		'deleted'
	];

	protected $dates = [
		'date'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function pump()
	{
		return $this->belongsTo(Pump::class);
	}

}
