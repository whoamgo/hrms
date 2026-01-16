<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogoutController extends Controller
{
    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Log logout activity
            if ($user) {
                \App\Helpers\ActivityLogHelper::log('logout', null, "User {$user->name} logged out");
                
                // Clear session_id from user record
                $user->session_id = null;
                $user->save();
                
                // Clear user cache
                Cache::forget('user_menu_' . $user->id);
            }
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                'redirect' => route('login')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout.'
            ], 500);
        }
    }
}
