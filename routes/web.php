<?php

use App\Http\Controllers\BcsCadreAuthController;
use App\Http\Controllers\BcsCadrePortalController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\LandingLoginController;
use App\Filament\Pages\Hostel\Bookings\AllBookings;
use App\Filament\Pages\Hostel\Bookings\Calendar;
use App\Filament\Pages\Hostel\Bookings\CheckInOut;
use App\Filament\Pages\Hostel\Bookings\NewBooking;
use App\Filament\Pages\Hostel\Rooms\Availability;
use App\Filament\Pages\Hostel\Rooms\Maintenance;
use App\Filament\Pages\Hostel\Rooms\NewRoom;
use App\Filament\Pages\Hostel\Rooms\RoomInventory;
use App\Filament\Pages\Hostel\Users\AllUsers;
use App\Filament\Pages\Hostel\Users\Eligibility;
use App\Filament\Pages\Hostel\Users\NewUser;
use App\Filament\Pages\HostelDashboard;
use App\Filament\Pages\InventoryDashboard;
use App\Filament\Pages\ModuleSelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (auth()->check()) {
        return redirect(HostelDashboard::getUrl(panel: 'admin'));
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

/*
|--------------------------------------------------------------------------
| Filament page shortcuts (optional)
|--------------------------------------------------------------------------
|
| Filament registers its own routes under /admin based on each Page $slug.
| These shortcuts give you "web.php routes" that redirect to the Filament URLs.
|
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', fn () => redirect(HostelDashboard::getUrl(panel: 'admin')))->name('dashboard');

    Route::get('/modules', fn () => redirect(ModuleSelector::getUrl(panel: 'admin')))->name('modules');
    Route::get('/inventory', fn () => redirect(InventoryDashboard::getUrl(panel: 'admin')))->name('inventory.dashboard');

    Route::prefix('hostel')->name('hostel.')->group(function () {
        Route::get('/dashboard', fn () => redirect(HostelDashboard::getUrl(panel: 'admin')))->name('dashboard');

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', fn () => redirect(AllBookings::getUrl(panel: 'admin')))->name('index');
            Route::get('/new', fn () => redirect(NewBooking::getUrl(panel: 'admin')))->name('new');
            Route::get('/check-in-out', fn () => redirect(CheckInOut::getUrl(panel: 'admin')))->name('checkinout');
            Route::get('/calendar', fn () => redirect(Calendar::getUrl(panel: 'admin')))->name('calendar');
        });

        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', fn () => redirect(RoomInventory::getUrl(panel: 'admin')))->name('index');
            Route::get('/new', fn () => redirect(NewRoom::getUrl(panel: 'admin')))->name('new');
            Route::get('/availability', fn () => redirect(Availability::getUrl(panel: 'admin')))->name('availability');
            Route::get('/maintenance', fn () => redirect(Maintenance::getUrl(panel: 'admin')))->name('maintenance');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', fn () => redirect(AllUsers::getUrl(panel: 'admin')))->name('index');
            Route::get('/new', fn () => redirect(NewUser::getUrl(panel: 'admin')))->name('new');
            Route::get('/eligibility', fn () => redirect(Eligibility::getUrl(panel: 'admin')))->name('eligibility');
        });
    });
});

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
    Route::get('/booking', [BcsCadrePortalController::class, 'booking'])->name('booking');
    Route::get('/meals', [BcsCadrePortalController::class, 'mealOrder'])->name('meals');
    Route::post('/meals', [BcsCadrePortalController::class, 'storeMealOrder'])->name('meals.store');
    Route::put('/meals/{mealOrder}', [BcsCadrePortalController::class, 'updateMealOrder'])->name('meals.update');
    Route::delete('/meals/{mealOrder}', [BcsCadrePortalController::class, 'destroyMealOrder'])->name('meals.destroy');
    Route::get('/feedback', [BcsCadrePortalController::class, 'feedback'])->name('feedback');
    Route::post('/feedback', [BcsCadrePortalController::class, 'storeFeedback'])->name('feedback.store');
    Route::put('/feedback/{feedback}', [BcsCadrePortalController::class, 'updateFeedback'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [BcsCadrePortalController::class, 'destroyFeedback'])->name('feedback.destroy');
    Route::get('/billing', [BcsCadrePortalController::class, 'billing'])->name('billing');
});
