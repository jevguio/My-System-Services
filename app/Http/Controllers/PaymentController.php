<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Cashier\Payment;

class PaymentController extends Controller
{
    //

    public function checkout(Request $request)
    {
        $intent = auth()->user()->createSetupIntent();

        return view('checkout', ['intent' => $intent]);
    }
    public function donate(Request $request)
    {
        $user = auth()->user();

        return $user->checkout([
            [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $request->amount * 100,
                    'product_data' => [
                        'name' => 'Donation',
                    ],
                ],
                'quantity' => 1,
            ]
        ], [
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
            'mode' => 'payment',
        ]);
    }


    public function purchase(Request $request)
    {
        $user = auth()->user();

        return $user->checkout([$request->price_id => 1], [
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
            'mode' => 'payment',
        ]);
    }

}
