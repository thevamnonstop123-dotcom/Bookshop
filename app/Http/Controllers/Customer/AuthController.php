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
    /**
     * Show customer login form.
     */
    public function showLoginForm()
    {
        return redirect('/?login=open');
    }

    /**
     * Handle customer login.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer.home'))
                ->with('success', 'Welcome back!');
        }

        return redirect('/?login=open&error=1')
        ->withErrors(['email' => 'Invalid credentials. Please try again.'])
        ->withInput($request->except('password'));
    }

    /**
     * Show customer registration form.
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Handle customer registration.
     */
        /**
     * Handle customer registration.
     */
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

    /**
     * Handle logout.
     */
    public function logout()
    {
        Auth::guard('customer')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out.');
    }
}