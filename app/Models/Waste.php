<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{

	protected $fillable = [
		'id',
		'amount',
		'type',
		'location_id',
		'product_id'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}
}
