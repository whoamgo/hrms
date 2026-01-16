<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TadaClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Crypt;
use Log;
class TadaLedgerController extends Controller
{
    public function index()
    {
        try {
            return view('accounts.tada-ledger.index');
        } catch (\Exception $e) {
            return redirect()->route('accounts.dashboard')->with('error', 'Error loading TA/DA ledger: ' . $e->getMessage());
        }
    }

    public function getTadaClaims(Request $request)
    {
        try {
            $query = TadaClaim::with('employee', 'approver');

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('purpose', 'like', "%{$search}%")
                      ->orWhereHas('employee', function($q2) use ($search) {
                          $q2->where('full_name', 'like', "%{$search}%")
                             ->orWhere('employee_id', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('travel_date')) {
                $query->where('travel_date', $request->travel_date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalRecords = TadaClaim::count();
            $filteredRecords = $query->count();

            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id', 'travel_date', 'purpose', 'amount_claimed', 'status', 'created_at'];
            $orderBy = $columns[$orderColumn] ?? 'id';
            $query->orderBy($orderBy, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $claims = $query->skip($start)->take($length)->get();

            $data = [];
            foreach ($claims as $claim) {
                $data[] = [
                    'id' => $claim->id,
                    'travel_date' => $claim->travel_date->format('d-m-Y'),
                    'employee_name' => $claim->employee->full_name ?? 'N/A',
                    'purpose' => $claim->purpose,
                    'distance' => $claim->distance ?? 'N/A',
                    'amount_claimed' => '₹' . number_format($claim->amount_claimed, 2),
                    'bill_file' => $claim->bill_files ? count($claim->bill_files) . ' file(s)' : ($claim->bill_file ? basename($claim->bill_file) : 'N/A'),
                    'status' => $claim->status,
                    'actions' => view('accounts.tada-ledger.partials.actions', ['claim' => $claim])->render(),
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading TA/DA claims: ' . $e->getMessage()], 500);
        }
    }

     
     public function approve(Request $request, $id)
     {
        //echo $id; die();
         try {
             $tadaClaim = TadaClaim::with('employee.user')->find(Crypt::decryptString($id));

             if (!$tadaClaim) {
                 return response()->json([
                     'success' => false,
                     'message' => 'TA/DA claim not found.'
                 ], 404);
             }

             $tadaClaim->update([
                 'status'      => 'approved',
                 'approved_by' => Auth::id(),
                 'approved_at' => now(),
             ]);

             // Notify employee
             if ($tadaClaim->employee && $tadaClaim->employee->user) {
                 NotificationHelper::notify(
                     $tadaClaim->employee->user_id,
                     'TA/DA Claim Approved',
                     "Your TA/DA claim for {$tadaClaim->purpose} has been approved.",
                     'success',
                     route('employee.tada-claims.index')
                 );
             }

             // Notify admin
             NotificationHelper::notifyByRole(
                 'admin',
                 'TA/DA Claim Approved',
                 "Accounts approved TA/DA claim for {$tadaClaim->employee->full_name} - {$tadaClaim->purpose} (₹" .
                     number_format($tadaClaim->amount_claimed, 2) . ").",
                 'info',
                 route('admin.tada-claims.index')
             );

             \App\Helpers\ActivityLogHelper::log(
                 'updated',
                 $tadaClaim,
                 "Accounts - Approved TA/DA claim for employee: {$tadaClaim->employee->full_name}"
             );

             return response()->json([
                 'success' => true,
                 'message' => 'TA/DA claim approved successfully.'
             ]);

         } catch (\Exception $e) {
             Log::error('Approve TADA claim failed', ['id' => $id, 'error' => $e->getMessage()]);

             return response()->json([
                 'success' => false,
                 'message' => 'Error approving claim.'
             ], 500);
         }
     }




     public function reject(Request $request, $id)
     {

         try {
             $validator = Validator::make($request->all(), [
                 'rejection_reason' => 'required|string'
             ]);

             if ($validator->fails()) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Validation failed.',
                     'errors'  => $validator->errors()
                 ], 422);
             }

             $tadaClaim = TadaClaim::with('employee.user')->find(Crypt::decryptString($id));

             if (!$tadaClaim) {
                 return response()->json([
                     'success' => false,
                     'message' => 'TA/DA claim not found.'
                 ], 404);
             }

             $tadaClaim->update([
                 'status'           => 'rejected',
                 'approved_by'      => Auth::id(),
                 'approved_at'      => now(),
                 'rejection_reason' => $request->rejection_reason,
             ]);

             // Notify employee
             if ($tadaClaim->employee && $tadaClaim->employee->user) {
                 NotificationHelper::notify(
                     $tadaClaim->employee->user_id,
                     'TA/DA Claim Rejected',
                     "Your TA/DA claim for {$tadaClaim->purpose} has been rejected. Reason: {$request->rejection_reason}",
                     'error',
                     route('employee.tada-claims.index')
                 );
             }

             // Notify admin
             NotificationHelper::notifyByRole(
                 'admin',
                 'TA/DA Claim Rejected',
                 "Accounts rejected TA/DA claim for {$tadaClaim->employee->full_name} - {$tadaClaim->purpose} (₹" .
                     number_format($tadaClaim->amount_claimed, 2) . ").",
                 'warning',
                 route('admin.tada-claims.index')
             );

             \App\Helpers\ActivityLogHelper::log(
                 'updated',
                 $tadaClaim,
                 "Accounts - Rejected TA/DA claim for employee: {$tadaClaim->employee->full_name}"
             );

             return response()->json([
                 'success' => true,
                 'message' => 'TA/DA claim rejected successfully.'
             ]);

         } catch (\Exception $e) {
             Log::error('Reject TADA claim failed', ['id' => $id, 'error' => $e->getMessage()]);

             return response()->json([
                 'success' => false,
                 'message' => 'Error rejecting claim.'.$e->getMessage()
             ], 500);
         }
     }





     public function show(Request $request, $id)
     {
       // echo $id; die();
         try {
             $tadaClaim = TadaClaim::with(['employee', 'approver'])->find($id);

             if (!$tadaClaim) {
                 if ($request->ajax()) {
                     return response()->json([
                         'error' => 'TA/DA claim not found.'
                     ], 404);
                 }

                 return redirect()->route('accounts.tada.index')
                     ->with('error', 'TA/DA claim not found.');
             }

             if ($request->ajax()) {
                 return view(
                     'accounts.tada-ledger.partials.view-modal',
                     compact('tadaClaim')
                 );
             }

             return redirect()->route('accounts.tada.index');

         } catch (\Exception $e) {
             Log::error('View TADA claim failed', ['id' => $id, 'error' => $e->getMessage()]);

             if ($request->ajax()) {
                 return response()->json([
                     'error' => 'Error loading claim.'
                 ], 500);
             }

             return redirect()->route('accounts.tada.index')
                 ->with('error', 'Error loading claim.');
         }
     }
















}
