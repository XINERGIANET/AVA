<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * 
 * @property int $id
 * @property int $sale_id
 * @property string|null $voucher_type
 * @property string|null $voucher_id
 * @property string|null $voucher_file
 * @property string|null $number
 * @property int|null $client_id
 * @property string|null $client
 * @property float $amount
 * @property int $payment_method_id
 * @property int $deleted
 * @property Carbon|null $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PaymentMethod $payment_method
 * @property Sale $sale
 *
 * @package App\Models
 */
class Payment extends Model
{

	protected $fillable = [
		'sale_id',
		'user_id',
		'agreement_id',
		'voucher_type',
		'voucher_id',
		'voucher_file',
		'number',
		'client_id',
		'client_name',
		'amount',
		'status',
		'payment_method_id',
		'observation',
		'photo_url',
		'deleted',
		'date'
	];

	protected $dates = [
		'date'
	];

	public function payment_method()
	{
		return $this->belongsTo(PaymentMethod::class);
	}

	public function sale()
	{
		return $this->belongsTo(Sale::class);
	}

	public function agreement()
	{
		return $this->belongsTo(Agreement::class);
	}

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
