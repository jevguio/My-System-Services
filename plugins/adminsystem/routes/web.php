<?php
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
Route::middleware(['web'])->group(function () {
    Route::get('/hello-plugin', function () {
        if (Auth::check()) {
            $user = Auth::user();
            // Do something with $user if needed
        }

        return view('helloworld::helloworld');
    })->name('test.plugin'); 

});

