<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\RegisterRequest;
use App\Http\Requests\Customer\LoginRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('customer.home');
        }
        return redirect()->route('customer.home', ['login' => 'open']);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.home'))->with('success', 'Welcome back!');
        }

        // If request expects JSON (AJAX/fetch), return JSON error so front-end can display without reload
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['message' => 'Invalid email or password. Please try again.'], 422);
        }
        // Store error in session and redirect for normal form submit
        return redirect()->route('customer.home', ['login' => 'open'])
            ->with('login_error', 'Invalid email or password. Please try again.');
    }

    public function showRegisterForm()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('customer.home');
        }
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'gender'   => $request->gender,
            'dob'      => $request->dob,
            'password' => Hash::make($request->password),
            'image'    => 'default.png',
            'status'   => 'active',
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.home')
            ->with('success', 'Welcome to Bookshop! Your account has been created.');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('customer.home', ['login' => 'open'])
            ->with('success', 'You have been logged out.');
    }
}