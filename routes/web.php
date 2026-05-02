<?php

use App\Http\Controllers\LandingLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }

    return view('welcome');
});

Route::post('/login', [LandingLoginController::class, 'store'])->name('site.login');
