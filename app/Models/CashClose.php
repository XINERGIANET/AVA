<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashClose extends Model
{
    use HasFactory;

    protected $table = 'cash_closes';

    protected $fillable = [
        'user_id',
        'location_id',
        'isle_id',
        'cash_type',
        'initial_cash_amount',
        'real_cash_amount',
        'final_cash_amount',
        'date',
        'theoretical_sale_amount',
        'credits_amount',
        'transfers_amount',
        'expenses_amount',
        'discounts_amount',
        'sale_variance_amount',
        'meter_breakdown',
    ];

    protected $casts = [
        'meter_breakdown' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function isle()
    {
        return $this->belongsTo(Isle::class, 'isle_id');
    }

    public function getRegisteredSumAmountAttribute()
    {
        if (!in_array($this->cash_type, ['isle', 'general'], true) || is_null($this->theoretical_sale_amount)) {
            return null;
        }

        return round(
            (float) $this->credits_amount + (float) $this->transfers_amount
            + (float) $this->expenses_amount + (float) $this->discounts_amount
            + (float) $this->final_cash_amount,
            2
        );
    }
}
