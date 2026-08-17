<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->user()->role === 'Admin Master') {
            return redirect()->intended(route('dashboard.admin-master'));
        }

        if ($request->user()->role === 'Admin') {
            return redirect()->intended(route('dashboard.admin'));
        }

        if ($request->user()->role === 'Client') {
            return redirect()->intended(route('dashboard.client'));
        }

        Auth::logout();

        return redirect()->route('login')
            ->withErrors([
                'username' => 'Role pengguna tidak dikenali.',
            ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
