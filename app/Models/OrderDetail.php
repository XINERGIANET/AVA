<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderDetail
 * 
 * @property int $id
 * @property int $order_id
 * @property string|null $area
 * @property int $product_id
 * @property float $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Order $order
 * @property Product $product
 * @property Collection|Sale[] $sales
 *
 * @package App\Models
 */
class OrderDetail extends Model
{

	protected $fillable = [
		'order_id',
		'area',
		'product_id',
		'quantity',
		'remaining'
	];

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function sale_details()
	{
		return $this->hasMany(SaleDetail::class);
	}
}
