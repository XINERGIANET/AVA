<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Truck
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $plate
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|DischargeDetail[] $discharge_details
 *
 * @package App\Models
 */
class Truck extends Model
{

	protected $fillable = [
		'name',
		'description',
		'plate',
		'deleted'
	];

	public function discharge_details()
	{
		return $this->hasMany(DischargeDetail::class);
	}
}
