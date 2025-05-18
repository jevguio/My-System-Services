<?php

use App\Http\Controllers\ProfileController;
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
    
    return view('plugin.view');
})->middleware(['auth', 'verified'])->name('plugin');

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/upload-plugin', [PluginController::class, 'upload'])->name('plugin.upload');
Route::post('/upload/delete', [PluginController::class, 'delete'])->name('plugin.delete');
Route::post('/check-plugin-update', [PluginController::class, 'checkUpdate']);
Route::post('/plugin-update', [PluginController::class, 'update'])->name('plugin.update');

require __DIR__.'/auth.php';
