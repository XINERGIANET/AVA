<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 * 
 * @property int $id
 * @property string $business_name
 * @property string $contact_name
 * @property string $commercial_name
 * @property string $document
 * @property string $phone
 * @property string $address
 * @property string $department
 * @property string $province
 * @property string $disctrict
 * @property bool $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Agreement[] $agreements
 * @property Collection|Sale[] $sales
 *
 * @package App\Models
 */
class Client extends Model
{
	protected $fillable = [
		'business_name',
		'contact_name',
		'commercial_name',
		'document',
		'phone',
		'address',
		'department',
		'province',
		'district',
		'deleted'
	];

	public function agreements()
	{
		return $this->hasMany(Agreement::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}
}
