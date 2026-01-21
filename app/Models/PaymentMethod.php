<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethod
 * 
 * @property int $id
 * @property string $name
 * @property bool $deleted
 * 
 * @property Collection|Payment[] $payments
 * @property Collection|Purchase[] $purchases
 *
 * @package App\Models
 */
class PaymentMethod extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'name',
		'deleted'
	];

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function purchases()
	{
		return $this->hasMany(Purchase::class);
	}
}
