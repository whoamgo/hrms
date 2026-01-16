<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class NotificationHelper
{
    /**
     * Send notification to a user
     */
    public static function notify($user, $title, $message, $type = 'info', $url = null)
    {
        try {
            if ($user instanceof User) {
                $user->notify(new GeneralNotification($title, $message, $type, $url));
            } elseif (is_numeric($user)) {
                $user = User::find($user);
                if ($user) {
                    $user->notify(new GeneralNotification($title, $message, $type, $url));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to multiple users
     */
    public static function notifyMany($users, $title, $message, $type = 'info', $url = null)
    {
        try {
            if (is_array($users) || $users instanceof \Illuminate\Support\Collection) {
                Notification::send($users, new GeneralNotification($title, $message, $type, $url));
            }
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to users by role
     */
    public static function notifyByRole($roleSlug, $title, $message, $type = 'info', $url = null)
    {
        try {
            $users = User::whereHas('role', function($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            })->where('status', 'active')->get();

            if ($users->count() > 0) {
                Notification::send($users, new GeneralNotification($title, $message, $type, $url));
            }
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
        }
    }
}

