<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        try {
            \App\Helpers\ActivityLogHelper::log('viewed', null, 'Settings Management');
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            return view('admin.settings.index', compact('settings'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'nullable|string|max:255',
                'company_address' => 'nullable|string',
                'company_email' => 'nullable|email|max:255',
                'company_phone' => 'nullable|string|max:20',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tax_id' => 'nullable|string|max:255',
                'currency' => 'nullable|string|max:10',
                'date_format' => 'nullable|string|max:20',
                'timezone' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Save settings to database
            $settingsToSave = [
                'company_name', 'company_address', 'company_email', 'company_phone',
                'tax_id', 'currency', 'date_format', 'timezone'
            ];

            foreach ($settingsToSave as $key) {
                if ($request->has($key)) {
                    \App\Models\Setting::set($key, $request->input($key));
                }
            }

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                $logoPath = $request->file('company_logo')->store('settings', 'public');
                \App\Models\Setting::set('company_logo', $logoPath);
            }

            \App\Helpers\ActivityLogHelper::log('updated', null, 'Updated system settings');

            Cache::forget('app_settings');

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings: ' . $e->getMessage()
            ], 500);
        }
    }
}

