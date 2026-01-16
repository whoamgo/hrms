<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request.
     */

    public function login(Request $request)
    {
        try {
            // 1️⃣ Validate input
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'Username or Email is required.',
                'password.required' => 'Password is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 2️⃣ Detect username OR email
            $loginValue = strtolower(trim($request->username));

            $credentials = [
                'password' => $request->password,
                'status'   => 'active',
            ];

            if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $loginValue;
            } else {
                $credentials['username'] = $loginValue;
            }

            // 3️⃣ Attempt login
            if (Auth::attempt($credentials, $request->filled('remember'))) {

                $user = Auth::user();

                // 4️⃣ Contract expiration check
                if ($user->employee) {
                    $employee = $user->employee;

                    if (
                        $employee->employee_type === 'Contract' &&
                        $employee->contract_end_date &&
                        \Carbon\Carbon::parse($employee->contract_end_date)->isPast()
                    ) {
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return response()->json([
                            'success' => false,
                            'message' => 'Your contract has expired. Please contact HR for contract renewal.'
                        ], 403);
                    }
                }

                // 5️⃣ Single session enforcement
                if ($user->session_id) {
                    \DB::table('sessions')->where('id', $user->session_id)->delete();
                }

                // 6️⃣ Regenerate session
                $request->session()->regenerate();
                $sessionId = $request->session()->getId();

                // 7️⃣ Update login timestamps & session
                $user->session_id = $sessionId;
                $user->last_login_at = $user->current_login_at;
                $user->current_login_at = now();
                $user->save();

                // 8️⃣ Log activity
                \App\Helpers\ActivityLogHelper::log(
                    'login',
                    null,
                    "User {$user->name} logged in"
                );

                // 9️⃣ Clear cached menu
                Cache::forget('user_menu_' . $user->id);

                //Redirect based on role
                $redirectRoute = $this->getRedirectRoute($user);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Login successful.',
                    'redirect' => route($redirectRoute),
                ]);
            }

            // ❌ Invalid credentials
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or account is inactive.'
            ], 401);

        } catch (\Exception $e) {

            // ❌ Server error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500);
        }
    }



    public function login_only_username(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'Username is required.',
                'password.required' => 'Password is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
                'status' => 'active'
            ];

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $user = Auth::user();
                
                // Check if employee has expired contract
                if ($user->employee) {
                    $employee = $user->employee;
                    if ($employee->employee_type === 'Contract' && $employee->contract_end_date) {
                        $contractEndDate = \Carbon\Carbon::parse($employee->contract_end_date);
                        if ($contractEndDate->isPast()) {
                            Auth::logout();
                            $request->session()->invalidate();
                            $request->session()->regenerateToken();
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'Your contract has expired. Please contact HR for contract renewal.'
                            ], 403);
                        }
                    }
                }
                
                // Single session enforcement: If user has an active session, invalidate it
                if ($user->session_id) {
                    // Delete the old session from sessions table
                    \DB::table('sessions')->where('id', $user->session_id)->delete();
                }
                
                // Regenerate session and store session ID
                $request->session()->regenerate();
                $sessionId = $request->session()->getId();
                
                // Update user with new session ID and login times
                $user->session_id = $sessionId;
                $user->last_login_at = $user->current_login_at;
                $user->current_login_at = now();
                $user->save();
                
                // Log login activity
                \App\Helpers\ActivityLogHelper::log('login', null, "User {$user->name} logged in");
                
                // Clear cache for user menu
                Cache::forget('user_menu_' . $user->id);
                
                // Redirect based on role
                $redirectRoute = $this->getRedirectRoute($user);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'redirect' => route($redirectRoute)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or account is inactive.'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500);
        }
    }

    /**
     * Get redirect route based on user role
     */
    private function getRedirectRoute($user)
    {
        if (!$user->role) {
            return 'login';
        }

        $roleSlug = $user->role->slug;
        
        $routes = [
            'admin' => 'admin.dashboard',
            'hr' => 'hr.dashboard',
            'accounts' => 'accounts.dashboard',
            'employee' => 'employee.dashboard',
        ];

        return $routes[$roleSlug] ?? 'login';
    }
}
