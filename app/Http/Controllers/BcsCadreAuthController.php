<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BcsCadreAuthController extends Controller
{
    public const DEMO_CADRE_REFERENCE = '4532';

    public const DEMO_OTP = '12342';

    public function showLogin(Request $request): RedirectResponse
    {
        if ($request->session()->get('cadre_auth') === true) {
            return redirect()->route('cadre.dashboard');
        }

        return redirect()->route('home');
    }

    public function submitCadre(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cadre_reference' => ['required', 'string'],
        ]);

        if ($validated['cadre_reference'] !== self::DEMO_CADRE_REFERENCE) {
            return back()->withErrors([
                'cadre_reference' => __('The cadre reference is not recognised.'),
            ])->withInput();
        }

        $request->session()->put('cadre_step1', true);

        return redirect()->route('home');
    }

    public function showOtp(Request $request): RedirectResponse
    {
        if ($request->session()->get('cadre_auth') === true) {
            return redirect()->route('cadre.dashboard');
        }

        if (! $request->session()->get('cadre_step1')) {
            return redirect()->route('home');
        }

        return redirect()->route('home');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        if (! $request->session()->get('cadre_step1')) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'otp' => ['required', 'string'],
        ]);

        if ($validated['otp'] !== self::DEMO_OTP) {
            return redirect()
                ->route('home')
                ->withErrors([
                    'otp' => __('The OTP you entered is incorrect.'),
                ])
                ->withInput();
        }

        $request->session()->put('cadre_auth', true);
        $request->session()->forget('cadre_step1');

        return redirect()->route('cadre.dashboard');
    }

    public function dashboard(Request $request): RedirectResponse|View
    {
        if ($request->session()->get('cadre_auth') !== true) {
            return redirect()->route('home');
        }

        return redirect()->route('cadre.booking');
    }

    public function cancelOtp(Request $request): RedirectResponse
    {
        $request->session()->forget('cadre_step1');

        return redirect()->route('home');
    }
}
