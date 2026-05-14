<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestBookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_scan' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:10240'],
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

        $request->session()->put('guest_pending_otp', true);
        $request->session()->put('guest_name', (string) $request->input('guest_full_name', __('Guest')));

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
        $request->session()->flash('guest_application_success', true);

        return redirect()->route('home', ['view' => 'guest']);
    }

    public function cancelOtp(Request $request): RedirectResponse
    {
        $request->session()->forget(['guest_pending_otp', 'guest_name']);

        return redirect()->route('home', ['view' => 'guest']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['guest_pending_otp', 'guest_verified', 'guest_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
