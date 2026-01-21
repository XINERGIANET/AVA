<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DischargeDetail
 * 
 * @property int $discharge_id
 * @property int $tank_id
 * @property int $product_id
 * @property int $truck_id
 * @property float $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Discharge $discharge
 * @property Product $product
 * @property Tank $tank
 * @property Truck $truck
 *
 * @package App\Models
 */
class DischargeDetail extends Model
{

	protected $fillable = [
		'discharge_id',
		'tank_id',
		'product_id',
		'truck_id',
		'quantity'
	];

	public function discharge()
	{
		return $this->belongsTo(Discharge::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function tank()
	{
		return $this->belongsTo(Tank::class);
	}

	public function truck()
	{
		return $this->belongsTo(Truck::class);
	}
}
