<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    // Send Password Reset Link to the User
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response(['message' => 'Password reset link sent!']);
        } else {
            return response(['message' => 'Unable to send password reset link.']);
        }
    }

    // Reset the User's Password
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
            'token' => 'required',
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return response(['message' => 'Password has been reset.'], 200);
        } else {
            return response(['message' => 'Password reset failed.'], 400);
        }
    }
}
