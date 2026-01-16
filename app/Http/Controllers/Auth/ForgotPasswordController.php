<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;
class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.exists' => 'We could not find a user with that email address.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with this email address.'
                ], 404);
            }

            // Generate reset token
            $token = Str::random(64);
            
            // Delete old tokens
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // Insert new token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);

            // Send email (you can implement email sending here)
            // For now, we'll return the token in response (remove in production)
             try {
                 Mail::to($user->email)->send(new ResetPasswordMail($token));

                 return response()->json([
                     'success' => true,
                     'message' => 'Password reset email sent successfully.'
                 ]);

             } catch (\Throwable $e) {

                 \Log::error('Mail sending failed', [
                     'email' => $user->email,
                     'error' => $e->getMessage()
                 ]);

                 return response()->json([
                     'success' => false,
                     'message' => 'Failed to send email. Please try again later.'
                 ], 500);
             }


            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email address.',
                'token' => $token // Remove this in production
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>'/////An error occurred. Please try again.' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show reset password form.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Reset password.
     */
    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'token.required' => 'Reset token is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.exists' => 'User not found with this email address.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if token exists and is valid
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token.'
                ], 400);
            }

            // Check if token matches
            if (!Hash::check($request->token, $passwordReset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reset token.'
                ], 400);
            }

            // Check if token is expired (24 hours)
            if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Reset token has expired. Please request a new one.'
                ], 400);
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete used token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            \App\Helpers\ActivityLogHelper::log('updated', $user, "Password reset via forgot password");

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '/////An error occurred. Please try again.' . $e->getMessage()
            ], 500);
        }
    }
}

