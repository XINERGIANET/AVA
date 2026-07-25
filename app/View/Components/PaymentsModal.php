<?php

namespace App\View\Components;

use App\Models\PaymentMethod;
use Illuminate\View\Component;

class PaymentsModal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $paymentMethods;

    public function __construct()
    {
        $user = auth()->user();
        $locationId = $user ? $user->location_id : null;
        $this->paymentMethods = PaymentMethod::where('deleted', 0)
            ->where(function ($q) use ($locationId) {
                $q->whereNull('location_id');
                if ($locationId) {
                    $q->orWhere('location_id', $locationId);
                }
            })
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.payments-modal');
    }
}
