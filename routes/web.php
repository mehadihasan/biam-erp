<?php

use App\Http\Controllers\BcsCadreAuthController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\LandingLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (auth()->check()) {
        return redirect('/admin');
    }

    $panelView = match ($request->query('view')) {
        'staff' => 'staff',
        'guest' => 'guest',
        default => 'cadre',
    };

    $showCadreOtpModal = $request->session()->get('cadre_step1') === true
        && $request->session()->get('cadre_auth') !== true;

    $showGuestOtpModal = $request->session()->get('guest_pending_otp') === true;

    return view('welcome', [
        'demoCadreReference' => BcsCadreAuthController::DEMO_CADRE_REFERENCE,
        'demoOtp' => BcsCadreAuthController::DEMO_OTP,
        'panelView' => $panelView,
        'showCadreOtpModal' => $showCadreOtpModal,
        'showGuestOtpModal' => $showGuestOtpModal,
    ]);
})->name('home');

Route::post('/login', [LandingLoginController::class, 'store'])->name('site.login');

Route::post('/guest/application', [GuestBookingController::class, 'store'])->name('guest.application.store');
Route::post('/guest/otp', [GuestBookingController::class, 'verifyOtp'])->name('guest.otp.verify');
Route::get('/guest/otp/cancel', [GuestBookingController::class, 'cancelOtp'])->name('guest.otp.cancel');

Route::prefix('cadre')->name('cadre.')->group(function () {
    Route::get('/login', [BcsCadreAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [BcsCadreAuthController::class, 'submitCadre'])->name('login.store');
    Route::get('/otp', [BcsCadreAuthController::class, 'showOtp'])->name('otp');
    Route::post('/otp', [BcsCadreAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/otp/cancel', [BcsCadreAuthController::class, 'cancelOtp'])->name('otp.cancel');
    Route::get('/dashboard', [BcsCadreAuthController::class, 'dashboard'])->name('dashboard');
});
