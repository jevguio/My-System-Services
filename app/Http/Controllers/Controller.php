<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Cashier\Payment;

abstract class Controller
{
    //

    public function checkout(Request $request)
    {
        $intent = auth()->user()->createSetupIntent();

        return view('checkout', ['intent' => $intent]);
    }

    public function processCheckout(Request $request)
    {
        $paymentMethod = $request->input('payment_method');

        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($paymentMethod);
        $user->charge(1000, $paymentMethod); // 1000 = $10.00

        return back()->with('success', 'Payment successful!');
    }
}
