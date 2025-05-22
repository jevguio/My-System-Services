<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ItemController extends Controller
{
    //
    public function index()
    {

        $user_data = Auth::user();
        if ($user_data) {
            $user_id = $user_data->id;
        } else {
            // handle the null case, e.g., return error response
            return redirect()->route('dashboard')->with('error', 'User not found.');
        }

        $user = User::with('subscriptions')->findOrFail($user_id);
        $items = Item::all();
        foreach ($user->subscriptions() as $item) {

            Log::info($user->subscriptions());
            $items->each(function ($item) use ($user) {
                $item->isSubscribed = $user->subscriptions()->where('item_id', $item->id)->exists();
            });
        }

        return view('items.index', compact('items', 'user'));
    }

}
