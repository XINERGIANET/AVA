<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
        'isle_id',
        'name',
        'description',
        'loan_date',
        'loan_amount',
        'send_method',
        'due_date',
        'recovered_amount',
        'collection_method',
        'collection_date',
        'status',
        'deleted',
    ];

    protected $dates = [
        'loan_date',
        'due_date',
        'collection_date',
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

    public function getBalanceAttribute()
    {
        return max(0, (float) $this->loan_amount - (float) $this->recovered_amount);
    }
}
