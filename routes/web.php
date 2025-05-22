<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PluginController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/plugin', function () {

    $items = Item::all();
    return view('plugin.view',compact('items'));
})->middleware(['auth', 'verified'])->name('plugin');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/upload-plugin', [PluginController::class, 'upload'])->name('plugin.upload');
Route::post('/upload/delete', [PluginController::class, 'delete'])->name('plugin.delete');
Route::post('/check-plugin-update', [PluginController::class, 'checkUpdate']);
Route::post('/plugin-update', [PluginController::class, 'update'])->name('plugin.update');

Route::post('/subscribe/{id}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::get('/items', [ItemController::class, 'index'])->name('item');
Route::get('/checkout/success', function (Request $request) {
    $subscriptionName = $request->subscriptionName;
    return view('checkout.success', compact('subscriptionName'));
})->name('checkout.success');

Route::get('/checkout/cancel', function (Request $request) {
    
    $subscriptionName = $request->subscriptionName;
    return view('checkout.cancel', compact('subscriptionName'));
})->name('checkout.cancel');

require __DIR__ . '/auth.php';
