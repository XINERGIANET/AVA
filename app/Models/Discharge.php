<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Discharge
 * 
 * @property int $id
 * @property int|null $purchase_id
 * @property int $location_id
 * @property Carbon $date
 * @property float $total_quantity
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Location $location
 * @property Purchase|null $purchase
 * @property Collection|DischargeDetail[] $discharge_details
 *
 * @package App\Models
 */
class Discharge extends Model
{

	protected $fillable = [
		'purchase_id',
		'location_id',
		'date',
		'total_quantity',
		'deleted'
	];

	protected $dates = [
		'date'
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
	}

	public function discharge_details()
	{
		return $this->hasMany(DischargeDetail::class);
	}

	public function first_detail()
	{
		return $this->hasOne(DischargeDetail::class)->orderBy('created_at');
	}
}
