<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * 
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $document
 * @property Carbon $birth_date
 * @property string $phone
 * @property string $address
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $fillable = [
		'name',
		'last_name',
		'document',
		'birth_date',
		'phone',
		'location_id',
		'address',
		'pin',
		'deleted'
	];

	protected $dates = [
		'birth_date',
	];

	public function location()
	{
		return $this->belongsTo(Location::class);
	}
}
