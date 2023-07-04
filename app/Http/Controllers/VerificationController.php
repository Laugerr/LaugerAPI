<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /**
     * Verify the user's email address.
     *
     * @param  Request  $request
     * @param  string  $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response(['message' => 'User not found.'], 404);
        }

        if ($user->email_verified) {
            return response(['message' => 'Email already verified.'], 200);
        }

        // Update the user's email_verified and email_verified_at fields
        $user->email_verified = true;
        $user->email_verified_at = now();
        $user->save();

        event(new Verified($user));

        return response(['message' => 'Email successfully verified!'], 200);
    }

    /**
     * Resend the email verification link.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response(['message' => 'Email not found.'], 404);
        }

        if ($user->email_verified) {
            return response(['message' => 'Email already verified.']);
        }

        // Generate the verification URL
        $verificationUrl = route('verification.verify', ['email' => $user->email]);

        // Send the verification email
        Mail::raw('Please click the following link to verify your email: ' . $verificationUrl, function ($message) use ($user) {
            $message->to($user->email)->subject('Email Verification');
        });

        return response(['message' => 'Verification email sent!'], 200);
    }
}
