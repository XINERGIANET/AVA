<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockBalance
 * 
 * @property int $id
 * @property int $tank_id
 * @property int $product_id
 * @property float $initial_measurement
 * @property float $final_measurement
 * @property float $sold_quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property Tank $tank
 *
 * @package App\Models
 */
class StockBalance extends Model
{

	protected $fillable = [
		'tank_id',
		'product_id',
		'date',
		'initial_measurement',
		'final_measurement',
		'purchased_quantity',
		'sold_quantity',
		'deleted'
	];

	protected $dates = [
		'date'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function tank()
	{
		return $this->belongsTo(Tank::class);
	}
}
