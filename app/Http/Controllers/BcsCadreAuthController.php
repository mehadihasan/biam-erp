<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SmsOtpGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BcsCadreAuthController extends Controller
{
    public const DEMO_CADRE_REFERENCE = '4532';

    public const DEMO_OTP = '12342';

    public function __construct(private readonly SmsOtpGateway $otpGateway) {}

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
        $request->session()->put('cadre_reference', $validated['cadre_reference']);

        $this->otpGateway->send($validated['cadre_reference'], __('Your BIAM hostel login OTP is ready.'));

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
        $cadreName = User::query()
            ->where('cadre_number', $request->session()->get('cadre_reference'))
            ->value('name');

        if (! $cadreName && $request->session()->get('cadre_reference') === self::DEMO_CADRE_REFERENCE) {
            $cadreName = User::query()
                ->where('email', 'test@example.com')
                ->oldest()
                ->value('name');
        }

        $request->session()->put('cadre_name', $cadreName);
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
        $request->session()->forget(['cadre_step1', 'cadre_reference']);

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['cadre_auth', 'cadre_step1', 'cadre_reference', 'cadre_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
