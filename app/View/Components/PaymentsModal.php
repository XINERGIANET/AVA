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
        $this->paymentMethods = PaymentMethod::all();
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
