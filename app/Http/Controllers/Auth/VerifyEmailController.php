<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // MODIFIED: Redirect to the dynamic 'home' route
            return redirect()->route('home', ['user' => $request->user()->name, 'verified' => 1]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        // MODIFIED: Redirect to the dynamic 'home' route
        return redirect()->route('home', ['user' => $request->user()->name, 'verified' => 1]);
    }
}
