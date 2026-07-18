<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{

	protected $fillable = [
		'location_id',
		'date',
		'description',
		'deleted',
	];

	protected $dates =[
		'date'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

}
