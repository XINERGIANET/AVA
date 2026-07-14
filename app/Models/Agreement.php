<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class Agreement
 * 
 * @property int $id
 * @property int $client_id
 * @property string $type
 * @property Carbon $date
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 * @property Collection|Order[] $orders
 *
 * @package App\Models
 */
class Agreement extends Model
{

	protected $fillable = [
		'number',
		'client_id',
		'location_id',
		'type',
		'date',
		'payment_date',
		'total',
		'status',
		'paid',
		'deleted'
	];

	protected $dates = [
		'date',
		'payment_date'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function agreement_details()
	{
		return $this->hasMany(AgreementDetail::class);
	}

	public function totalProductos() {
		$products = [];
		
		// Recorrer todos los detalles del contrato
		foreach ($this->agreement_details as $detail) {
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
