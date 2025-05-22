<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;
use App\Models\User;

class SubscriptionController extends Controller
{
    // 
    public function subscribe(Request $request)
    {
        $user = auth()->user();

        $subscriptionName = 'default'; // product name

        if ($user->subscribed($subscriptionName, $request->price_id)) {
            return response()->json(['message' => 'Already subscribed to ' . $subscriptionName]);
        }

        return $user->newSubscription($subscriptionName, $request->price_id)
            ->checkout([
                'success_url' => route('checkout.success', compact('subscriptionName')),
                'cancel_url' => route('checkout.cancel', compact('subscriptionName')),
            ]);
    }

}
