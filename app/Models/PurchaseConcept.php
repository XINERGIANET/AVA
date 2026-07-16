<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseConcept
 *
 * Catálogo abierto de "Concepto de la compra" (Combustible, Otros Gastos, ...).
 *
 * @property int $id
 * @property string $name
 * @property bool $is_fuel
 * @property int $deleted
 *
 * @package App\Models
 */
class PurchaseConcept extends Model
{
    protected $fillable = [
        'name',
        'is_fuel',
        'deleted',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
