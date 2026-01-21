<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tank
 * 
 * @property int $id
 * @property int $location_id
 * @property string $name
 * @property int $capacity
 * @property string $deleted
 * @property int|null $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Location $location
 * @property Product|null $product
 * @property Collection|DischargeDetail[] $discharge_details
 * @property Collection|Transfer[] $transfers
 *
 * @package App\Models
 */
class Tank extends Model
{
	protected $fillable = [
		'location_id',
		'name',
		'capacity',
		'deleted',
		'stored_quantity',
		'product_id',
		'is_reserve',
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}


	public function product()
	{
		return $this->belongsTo(Product::class, 'product_id');
	}

	public function discharge_details()
	{
		return $this->hasMany(DischargeDetail::class);
	}

	public function transfers()
	{
		return $this->hasMany(Transfer::class, 'to_tank_id');
	}
}
