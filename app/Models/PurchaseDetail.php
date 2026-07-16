<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseDetail
 * 
 * @property int $purchase_id
 * @property int $product_id
 * @property float $quantity
 * @property float $unit_price
 * @property float $subtotal
 * 
 * @property Product $product
 * @property Purchase $purchase
 *
 * @package App\Models
 */
class PurchaseDetail extends Model
{
	public $timestamps = false;

	protected $guarded = [
	];

	protected $appends = ['display_name'];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function tank()
	{
		return $this->belongsTo(Tank::class);
	}

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
	}

	// Nombre a mostrar: el del producto de catálogo, o la descripción libre
	// cuando la línea no está atada a ningún producto (ej. Otros Gastos).
	public function getDisplayNameAttribute()
	{
		return $this->product->name ?? ($this->description ?: 'Ítem sin descripción');
	}
}
