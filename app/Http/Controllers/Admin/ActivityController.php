<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use DB;
class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     try {
    //         $activities = DB::table('activity_logs')
    //                 ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
    //                 ->select(
    //                     'activity_logs.*',
    //                     'users.name as user_name'
    //                 )
    //                 ->orderByDesc('activity_logs.created_at')
    //                 ->get();
    //         return view('admin.activity-logs.index', compact('activities'));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error loading ActivityLogs: ' . $e->getMessage());
    //     }
    // }


    public function index(Request $request)
    {
        $query = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->select(
                'activity_logs.*',
                'users.name as user_name'
            );

        // FILTER: USER
        if ($request->filled('user_id')) {
            $query->where('activity_logs.user_id', $request->user_id);
        }

        // FILTER: ACTION
        if ($request->filled('action')) {
            $query->where('activity_logs.action', $request->action);
        }

        // FILTER: DATE RANGE
        if ($request->filled('from_date')) {
            $query->whereDate('activity_logs.created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('activity_logs.created_at', '<=', $request->to_date);
        }

        $activities = $query
            ->orderByDesc('activity_logs.created_at')
            ->paginate(50)
            ->withQueryString(); // IMPORTANT

        $users = DB::table('users')->select('id', 'name')->get();

        return view('admin.activity-logs.index', compact('activities','users'));
    }

 
 
}
