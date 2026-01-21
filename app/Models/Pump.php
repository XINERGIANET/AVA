<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Pump extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'isle_id',
        'product_id',
        'side',
        'deleted',
    ];

    public function isle()
    {
        return $this->belongsTo(Isle::class, 'isle_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class);
    }
}
