<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $customer = Customer::where('email', $googleUser->getEmail())->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'phone'    => '09' . rand(100000000, 999999999),
                    'password' => bcrypt(uniqid()),
                    'image'    => 'default.png',
                    'status'   => 'active',
                    'gender'   => 'male',
                    'dob'      => now()->subYears(18),
                ]);
            }

            Auth::guard('customer')->login($customer, true);
            return redirect()->route('customer.home');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login failed.');
        }
    }

    /**
     * Redirect to Facebook.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback.
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $customer = Customer::where('email', $facebookUser->getEmail())->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name'     => $facebookUser->getName(),
                    'email'    => $facebookUser->getEmail(),
                    'phone'    => '09' . rand(100000000, 999999999),
                    'password' => bcrypt(uniqid()),
                    'image'    => $facebookUser->getAvatar() ?? 'default.png',
                    'status'   => 'active',
                    'gender'   => 'male',
                    'dob'      => now()->subYears(18),
                ]);
            }

            Auth::guard('customer')->login($customer, true);
            return redirect()->route('customer.home');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Facebook login failed.');
        }
    }
}