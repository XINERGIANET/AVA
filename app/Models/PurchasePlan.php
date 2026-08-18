<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchasePlan
 * 
 * @property int $id
 * @property int $location_id
 * @property int $user_id
 * @property Carbon $scheduled_date
 * @property float $available_money
 * @property string $status
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property string|null $notes
 * @property string|null $manager_notes
 * @property float|null $compliance_percentage
 * @property string|null $justification_notes
 * @property int $deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Location $location
 * @property User $user
 * @property User|null $reviewer
 * @property Collection|PurchasePlanDetail[] $details
 */
class PurchasePlan extends Model
{
    protected $table = 'purchase_plans';

    protected $guarded = [];

    protected $dates = [
        'scheduled_date',
        'reviewed_at'
    ];

    protected $appends = [
        'total_requested_gallons',
        'total_approved_gallons',
        'total_purchased_gallons',
        'effective_compliance'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function details()
    {
        return $this->hasMany(PurchasePlanDetail::class, 'purchase_plan_id');
    }

    public function getTotalRequestedGallonsAttribute()
    {
        return (float) $this->details->sum('requested_quantity');
    }

    public function getTotalApprovedGallonsAttribute()
    {
        return (float) $this->details->sum(function ($detail) {
            return $detail->approved_quantity !== null ? $detail->approved_quantity : $detail->requested_quantity;
        });
    }

    public function getTotalPurchasedGallonsAttribute()
    {
        return (float) $this->details->sum('purchased_quantity');
    }

    public function getEffectiveComplianceAttribute()
    {
        if ($this->compliance_percentage !== null) {
            return (float) $this->compliance_percentage;
        }
        
        $baseGallons = $this->total_approved_gallons > 0 ? $this->total_approved_gallons : $this->total_requested_gallons;
        if ($baseGallons <= 0) {
            return 0;
        }

        $purchased = $this->total_purchased_gallons;
        $calculated = round(($purchased / $baseGallons) * 100, 2);
        return min($calculated, 100);
    }
}
