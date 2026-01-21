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
        'initial_cash_amount',
        'real_cash_amount',
        'final_cash_amount',
        'date'
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
}
