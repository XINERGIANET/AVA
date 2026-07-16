<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SupplierPayment
 *
 * Pago (parcial o total) registrado contra una compra, dentro de Cuentas por Pagar.
 *
 * @property int $id
 * @property int $purchase_id
 * @property int|null $supplier_id
 * @property int|null $user_id
 * @property int|null $payment_method_id
 * @property float $amount
 * @property Carbon $date
 * @property string|null $observation
 * @property int $deleted
 *
 * @property Purchase $purchase
 * @property Supplier|null $supplier
 * @property User|null $user
 * @property PaymentMethod|null $payment_method
 *
 * @package App\Models
 */
class SupplierPayment extends Model
{
    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'user_id',
        'payment_method_id',
        'amount',
        'date',
        'observation',
        'deleted',
    ];

    protected $dates = [
        'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
