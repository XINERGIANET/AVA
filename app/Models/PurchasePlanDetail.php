<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchasePlanDetail
 * 
 * @property int $id
 * @property int $purchase_plan_id
 * @property int $product_id
 * @property int|null $tank_id
 * @property float $current_stock
 * @property float $requested_quantity
 * @property float|null $approved_quantity
 * @property float $purchased_quantity
 * @property float|null $unit_price_estimate
 * @property float|null $estimated_total
 * 
 * @property PurchasePlan $purchase_plan
 * @property Product $product
 * @property Tank|null $tank
 */
class PurchasePlanDetail extends Model
{
    protected $table = 'purchase_plan_details';

    protected $guarded = [];

    protected $appends = [
        'compliance_rate'
    ];

    public function purchase_plan()
    {
        return $this->belongsTo(PurchasePlan::class, 'purchase_plan_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tank_id');
    }

    public function getComplianceRateAttribute()
    {
        $target = $this->approved_quantity !== null ? (float)$this->approved_quantity : (float)$this->requested_quantity;
        if ($target <= 0) {
            return 0;
        }
        return round(((float)$this->purchased_quantity / $target) * 100, 2);
    }
}
