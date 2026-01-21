<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 * 
 * @property int $id
 * @property string $name
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Discharge[] $discharges
 * @property Collection|Sale[] $sales
 * @property Collection|Tank[] $tanks
 *
 * @package App\Models
 */
class Location extends Model
{
	protected $fillable = [
		'name',
		'deleted'
	];

	public function discharges()
	{
		return $this->hasMany(Discharge::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}

	public function tanks()
	{
		return $this->hasMany(Tank::class);
	}
	
	public function location_prices()
	{
		return $this->hasMany(LocationPrice::class);
	}

	public function cash_closes()
	{
		return $this->hasMany(CashClose::class);
	}
}
