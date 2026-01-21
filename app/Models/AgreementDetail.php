<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementDetail extends Model
{
    use HasFactory;

    protected $fillable = [
		'agreement_id',
		'product_id',
		'unit_price',
		'quantity',
        'subtotal'
	];

    public function agreement()
	{
		return $this->belongsTo(Agreement::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
