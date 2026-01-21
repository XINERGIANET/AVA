<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property string|null $brand
 * @property string|null $type
 * @property string|null $category
 * @property string|null $measurement_unit
 * @property float $unit_price
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|DischargeDetail[] $discharge_details
 * @property Collection|OrderDetail[] $order_details
 * @property Collection|PurchaseDetail[] $purchase_details
 * @property Collection|SaleDetail[] $sale_details
 * @property Collection|Tank[] $tanks
 * @property Collection|Transfer[] $transfers
 *
 * @package App\Models
 */
class Product extends Model
{

	protected $fillable = [
		'name',
		'brand',
		'type',
		'category',
		'measurement_unit',
		'unit_price',
		'deleted'
	];

	public function discharge_details()
	{
		return $this->hasMany(DischargeDetail::class);
	}

	public function order_details()
	{
		return $this->hasMany(OrderDetail::class);
	}

	public function purchase_details()
	{
		return $this->hasMany(PurchaseDetail::class);
	}

	public function sale_details()
	{
		return $this->hasMany(SaleDetail::class);
	}

	public function tanks()
	{
		return $this->hasMany(Tank::class);
	}

	public function transfers()
	{
		return $this->hasMany(Transfer::class);
	}

	public function location_prices()
	{
		return $this->hasMany(LocationPrice::class);
	}
}
