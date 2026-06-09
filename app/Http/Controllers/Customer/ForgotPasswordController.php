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
        return view('customer.auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:customers,email']);

        $customer = Customer::where('email', $request->email)->first();
        $token = Str::random(64);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Send email using the same method that works in tinker
        Mail::raw(
            "Hello {$customer->name},\n\n".
            "Click this link to reset your Bookshop password:\n\n".
            "{$resetLink}\n\n".
            "This link expires in 60 minutes.\n\n".
            "If you didn't request this, ignore this email.\n\n".
            "— Bookshop Team",
            function ($message) use ($request, $customer) {
                $message->to($request->email, $customer->name)
                        ->subject('Reset Your Bookshop Password');
            }
        );

        return back()->with('success', 'Password reset link sent! Check your inbox.');
    }

    public function showResetForm(Request $request)
    {
        return view('customer.auth.passwords.reset', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        $customer = Customer::where('email', $request->email)->first();
        $customer->update(['password' => bcrypt($request->password)]);
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset! Please login.');
    }
}
