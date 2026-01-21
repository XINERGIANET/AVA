<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SaleDetail
 * 
 * @property int $product_id
 * @property int $sale_id
 * @property float $quantity
 * @property float $unit_price
 * @property float $subtotal
 * @property int $deleted
 * 
 * @property Product $product
 * @property Sale $sale
 *
 * @package App\Models
 */
class SaleDetail extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'product_id',
		'sale_id',
		'order_detail_id',
		'truck_id',
		'closing_number',
		'pump_id',
		'quantity',
		'unit_price',
		'discounted_price',
		'subtotal',
		'deleted'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function sale()
	{
		return $this->belongsTo(Sale::class);
	}

	public function order_detail()
	{
		return $this->belongsTo(OrderDetail::class);
	}

	public function truck()
	{
		return $this->belongsTo(Truck::class);
	}

	public function pump()
	{
		return $this->belongsTo(Pump::class, 'pump_id');
	}
}
