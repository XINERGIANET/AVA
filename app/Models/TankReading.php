<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TankReading
 *
 * Toma de inventario física (varillaje) de un tanque, usada para reconciliar
 * el stock teórico del sistema contra la lectura real y calcular la merma.
 *
 * @property int $id
 * @property int $tank_id
 * @property int $user_id
 * @property Carbon $date
 * @property float $physical_quantity
 * @property float $previous_stock
 * @property float $theoretical_stock
 * @property float $difference
 * @property string $status
 * @property string|null $notes
 * @property bool $deleted
 *
 * @property Tank $tank
 * @property User $user
 *
 * @package App\Models
 */
class TankReading extends Model
{
	protected $fillable = [
		'tank_id',
		'user_id',
		'date',
		'physical_quantity',
		'previous_stock',
		'theoretical_stock',
		'difference',
		'status',
		'notes',
		'deleted'
	];

	protected $dates = [
		'date'
	];

	public function tank()
	{
		return $this->belongsTo(Tank::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
