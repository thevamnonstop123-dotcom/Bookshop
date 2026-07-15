<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('password.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email'
        ], [
            'email.required' => 'Email address is required.',
            'email.email'    => 'Please enter a valid email address (e.g., user@domain.com).',
            'email.exists'   => 'This email is not registered in our system.',
        ]);

        $customer = Customer::where('email', $request->email)->first();
        $token = Str::random(64);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        Mail::send('mail.password-reset', [
            'name' => $customer->name,
            'resetLink' => $resetLink
        ], function ($message) use ($request, $customer) {
            $message->to($request->email, $customer->name)
                    ->subject('Reset Your Bookshop Password');
        });

        return back()->with('status', 'Password reset link sent! Check your inbox.');
    }

    public function showResetForm(Request $request)
    {
        return view('password.reset', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:customers,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.exists'       => 'This email is not registered.',
            'password.required'  => 'New password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $reset = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        $customer = Customer::where('email', $request->email)->first();
        $customer->password = $request->password;
        $customer->save();
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully! Please login.');
    }
}