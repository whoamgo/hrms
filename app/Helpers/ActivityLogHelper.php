<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogHelper
{
    /**
     * Log an activity
     */
    public static function log($action, $model = null, $description = null, $oldValues = null, $newValues = null)
    {
        try {
            $user = Auth::user();
            
            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model ? $model->id : null,
                'description' => $description ?? self::generateDescription($action, $model),
                'old_values' => $oldValues ? (is_array($oldValues) ? $oldValues : $oldValues->toArray()) : null,
                'new_values' => $newValues ? (is_array($newValues) ? $newValues : $newValues->toArray()) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'route' => Request::route() ? Request::route()->getName() : null,
                'method' => Request::method(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if logging fails
            \Log::error('Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate description from action and model
     */
    private static function generateDescription($action, $model = null)
    {
        if (!$model) {
            return ucfirst($action);
        }

        $modelName = class_basename($model);
        
        $descriptions = [
            'created' => "Created {$modelName} #{$model->id}",
            'updated' => "Updated {$modelName} #{$model->id}",
            'deleted' => "Deleted {$modelName} #{$model->id}",
            'viewed' => "Viewed {$modelName} #{$model->id}",
            'approved' => "Approved {$modelName} #{$model->id}",
            'rejected' => "Rejected {$modelName} #{$model->id}",
        ];

        return $descriptions[$action] ?? ucfirst($action) . " {$modelName} #{$model->id}";
    }
}


