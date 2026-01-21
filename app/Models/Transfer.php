<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transfer
 * 
 * @property int $id
 * @property int $from_tank_id
 * @property int $to_tank_id
 * @property int $product_id
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Tank $tank
 * @property Product $product
 *
 * @package App\Models
 */
class Transfer extends Model
{

	protected $fillable = [
		'from_tank_id',
		'to_tank_id',
		'product_id',
		'date',
		'quantity',
		'deleted',
		'recieved',
		'recieved_at'
	];

	protected $dates = [
		'date',
		'recieved_at'
	];

	public function from_tank()
	{
		return $this->belongsTo(Tank::class, 'from_tank_id');
	}

	public function to_tank()
	{
		return $this->belongsTo(Tank::class, 'to_tank_id');
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
