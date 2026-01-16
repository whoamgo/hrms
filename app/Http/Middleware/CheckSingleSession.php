<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = $request->session()->getId();
            
            // If user has a session_id and it doesn't match current session, logout
            if ($user->session_id && $user->session_id !== $currentSessionId) {
                // Check if the stored session still exists
                $sessionExists = \DB::table('sessions')
                    ->where('id', $user->session_id)
                    ->exists();
                
                // If old session exists, it means user logged in from another location
                if ($sessionExists) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Your account has been logged in from another location. Please login again.'
                        ], 401);
                    }
                    
                    return redirect()->route('login')
                        ->with('error', 'Your account has been logged in from another location. Please login again.');
                } else {
                    // Old session doesn't exist, update to current session
                    $user->session_id = $currentSessionId;
                    $user->save();
                }
            } elseif (!$user->session_id) {
                // No session_id stored, set it to current session
                $user->session_id = $currentSessionId;
                $user->save();
            }
        }
        
        return $next($request);
    }
}
