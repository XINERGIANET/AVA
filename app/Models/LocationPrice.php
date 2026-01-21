<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Purchase
 * 
 * @property int $location_id
 * @property int $product_id
 * @property float $price
 * 
 * @property Location $location
 * @property Product $product
 *
 * @package App\Models
 */
class LocationPrice extends Model
{

	protected $fillable = [
		'location_id',
		'product_id',
		'unit_price',
	];


	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

}
