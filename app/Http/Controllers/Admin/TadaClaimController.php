<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TadaClaim;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Helpers\NotificationHelper;

class TadaClaimController extends Controller
{
    public function index()
    {
        try {
            return view('admin.tada-claims.index');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading TA/DA claims: ' . $e->getMessage());
        }
    }

    public function getTadaClaims(Request $request)
    {
        try {
            $query = TadaClaim::with('employee');

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
                    'purpose' => $claim->purpose,
                    'distance' => $claim->distance ?? 'N/A',
                    'amount_claimed' => '₹' . number_format($claim->amount_claimed, 2),
                    'bill_file' => $claim->bill_file ? basename($claim->bill_file) : 'N/A',
                    'status' => $claim->status,
                    'actions' => view('admin.tada-claims.partials.actions', ['claim' => $claim])->render(),
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
        try {
            $tadaClaim = TadaClaim::with('employee.user')->find($id);

            if (!$tadaClaim) {
                return response()->json([
                    'success' => false,
                    'message' => 'TA/DA claim not found.'
                ], 404);
            }

            $tadaClaim->update([
                'status'       => 'approved',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
            ]);

            // Notify employee
            if ($tadaClaim->employee && $tadaClaim->employee->user) {
                NotificationHelper::notify(
                    $tadaClaim->employee->user_id,
                    'TA/DA Claim Approved',
                    "Your TA/DA claim for {$tadaClaim->purpose} has been approved by Admin.",
                    'success',
                    route('employee.tada-claims.index')
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'TA/DA claim approved successfully.'
            ]);

        } catch (\Exception $e) {

            Log::error('Error approving TADA claim', [
                'claim_id' => $id,
                'message'  => $e->getMessage()
            ]);

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

          $tadaClaim = TadaClaim::with('employee.user')->find($id);

          if (!$tadaClaim) {
              return response()->json([
                  'success' => false,
                  'message' => 'TA/DA claim not found.'
              ], 404);
          }

          $tadaClaim->update([
              'status'           => 'rejected',
              'approved_by'      => auth()->id(),
              'approved_at'      => now(),
              'rejection_reason' => $request->rejection_reason,
          ]);

          // Notify employee
          if ($tadaClaim->employee && $tadaClaim->employee->user) {
              NotificationHelper::notify(
                  $tadaClaim->employee->user_id,
                  'TA/DA Claim Rejected',
                  "Your TA/DA claim for {$tadaClaim->purpose} has been rejected by Admin. Reason: {$request->rejection_reason}",
                  'error',
                  route('employee.tada-claims.index')
              );
          }

          return response()->json([
              'success' => true,
              'message' => 'TA/DA claim rejected successfully.'
          ]);

      } catch (\Exception $e) {

          Log::error('Error rejecting TADA claim', [
              'claim_id' => $id,
              'message'  => $e->getMessage()
          ]);

          return response()->json([
              'success' => false,
              'message' => 'Error rejecting claim.'
          ], 500);
      }
  }


    
    public function show(Request $request, $id)
    {
        try {
            $tadaClaim = TadaClaim::with(['employee', 'approver'])->find($id);

            if (!$tadaClaim) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'TADA claim not found.'
                    ], 404);
                }

                return redirect()
                    ->route('admin.tada-claims.index')
                    ->with('error', 'TADA claim not found.');
            }

            // AJAX request → modal view
            if ($request->ajax()) {
                return view(
                    'admin.tada-claims.partials.view-modal',
                    compact('tadaClaim')
                );
            }

            // Normal page view
            return view('admin.tada-claims.show', compact('tadaClaim'));

        } catch (\Exception $e) {

            Log::error('Error loading TADA claim', [
                'claim_id' => $id,
                'message'  => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error loading claim.'
                ], 500);
            }

            return redirect()
                ->route('admin.tada-claims.index')
                ->with('error', 'Error loading claim.');
        }
    }








}
