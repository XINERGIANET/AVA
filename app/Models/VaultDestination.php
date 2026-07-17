<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaultDestination extends Model
{
    protected $fillable = [
        'name',
        'deleted',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
