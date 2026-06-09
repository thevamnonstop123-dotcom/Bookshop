<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle staff login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('staff')->attempt($credentials, $request->filled('remember'))) {

            // Check if staff account is active
            if (Auth::guard('staff')->user()->status !== 'active') {
                Auth::guard('staff')->logout();

                return back()
                    ->withErrors(['email' => 'Your account has been deactivated. Contact administrator.'])
                    ->withInput($request->except('password'));
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome to Admin Dashboard.');
        }

        return back()
            ->withErrors(['email' => 'Invalid staff credentials.'])
            ->withInput($request->except('password'));
    }

    /**
     * Handle logout.
     */
    public function logout()
    {
        Auth::guard('staff')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Logged out successfully.');
    }
}