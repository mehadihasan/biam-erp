<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestBookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_scan' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:10240'],
            'guest_full_name' => ['required', 'string', 'max:255'],
            'guest_mobile_no' => ['nullable', 'string', 'max:30'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_cadre_reference' => ['required', 'string'],
        ], [
            'application_scan.mimes' => __('The file must be a PDF, JPG, or PNG.'),
        ]);

        if ($validated['guest_cadre_reference'] !== BcsCadreAuthController::DEMO_CADRE_REFERENCE) {
            return redirect()
                ->route('home', ['view' => 'guest'])
                ->withErrors([
                    'guest_cadre_reference' => __('The reference ID is not recognised for this demo.'),
                ])
                ->withInput();
        }

        if ($request->hasFile('application_scan')) {
            $request->file('application_scan')->store('guest-applications', 'local');
        }

        $email = $validated['guest_email'] ?: 'guest-'.Str::lower(Str::random(16)).'@example.invalid';
        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser && $existingUser->role !== 'guest') {
            return redirect()
                ->route('home', ['view' => 'guest'])
                ->withErrors(['guest_email' => __('This email is already used by another account type.')])
                ->withInput();
        }

        $guest = $existingUser ?: User::query()->create([
            'name' => $validated['guest_full_name'],
            'email' => $email,
            'phone' => $validated['guest_mobile_no'] ?? null,
            'role' => 'guest',
            'password' => Str::random(32),
            'is_verified' => false,
        ]);

        $request->session()->put('guest_pending_otp', true);
        $request->session()->put('guest_user_id', $guest->id);
        $request->session()->put('guest_name', $guest->name);
        $request->session()->put('guest_cadre_reference', $validated['guest_cadre_reference']);

        return redirect()->route('home', ['view' => 'guest']);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        if (! $request->session()->get('guest_pending_otp')) {
            return redirect()->route('home', ['view' => 'guest']);
        }

        $validated = $request->validate([
            'guest_otp' => ['required', 'string'],
        ]);

        if ($validated['guest_otp'] !== BcsCadreAuthController::DEMO_OTP) {
            return redirect()
                ->route('home', ['view' => 'guest'])
                ->withErrors([
                    'guest_otp' => __('The OTP you entered is incorrect.'),
                ])
                ->withInput();
        }

        $request->session()->forget('guest_pending_otp');
        $request->session()->put('guest_verified', true);

        User::query()
            ->whereKey($request->session()->get('guest_user_id'))
            ->update(['is_verified' => true]);

        return redirect()->route('guest.booking');
    }

    public function dashboard(Request $request): RedirectResponse
    {
        if ($request->session()->get('guest_verified') !== true) {
            return redirect()->route('home', ['view' => 'guest']);
        }

        return redirect()->route('guest.booking');
    }

    public function cancelOtp(Request $request): RedirectResponse
    {
        $request->session()->forget(['guest_pending_otp', 'guest_user_id', 'guest_name', 'guest_cadre_reference']);

        return redirect()->route('home', ['view' => 'guest']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['guest_pending_otp', 'guest_verified', 'guest_user_id', 'guest_name', 'guest_cadre_reference']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
