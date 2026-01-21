<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * 
 * @property int $id
 * @property int $agreement_id
 * @property string $number
 * @property Carbon $date
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Agreement $agreement
 * @property Collection|OrderDetail[] $order_details
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $fillable = [
		'agreement_id',
		'number',
		'date',
		'deleted'
	];

	protected $dates = [
		'date'
	];

	public function agreement()
	{
		return $this->belongsTo(Agreement::class);
	}

	public function order_details()
	{
		return $this->hasMany(OrderDetail::class);
	}
}
