<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sale
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int $location_id
 * @property int|null $order_detail_id
 * @property int|null $client_id
 * @property string|null $client
 * @property string|null $phone
 * @property float $total
 * @property Carbon $date
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Location $location
 * @property OrderDetail|null $order_detail
 * @property User|null $user
 * @property Collection|Payment[] $payments
 * @property Collection|SaleDetail[] $sale_details
 *
 * @package App\Models
 */
class Sale extends Model
{

	protected $fillable = [
		'user_id',
		'location_id',
		'client_id',
		'client_name',
		'order_detail_id',
		'phone',
		'type_sale',
		'total',
		//Como un vuelto adicional que pide el cliente
		'adicional',
		'vehicle_plate',
		'date',
		'deleted'

	];

	protected $dates = [
		'date'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function sale_details()
	{
		return $this->hasMany(SaleDetail::class);
	}
	
	public function saleDetails()
	{
		return $this->hasMany(SaleDetail::class, 'sale_id');
	}
	
	public function totalProductos() {
		$products = [];
		
		// Recorrer todos los detalles de la venta
		foreach ($this->sale_details as $detail) {
			$productId = $detail->product_id;
			
			// Si el producto ya existe en el array, sumar la cantidad
			if (isset($products[$productId])) {
				$products[$productId]['total_quantity'] += $detail->quantity;
			} else {
				// Si es la primera vez que aparece el producto, inicializarlo
				$products[$productId] = [
					'product_id' => $productId,
					'product_name' => $detail->product->name ?? 'Producto desconocido',
					'total_quantity' => $detail->quantity
				];
			}
		}
		
		return $products;
	}
}
