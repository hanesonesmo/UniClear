<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DepartmentController
 * Handles department staff dashboard, approve, reject clearance requests.
 */
class DepartmentController extends Controller
{
    // Department dashboard — shows requests for this staff's department
    public function dashboard(Request $request)
    {
        $staff        = Auth::user();
        $departmentId = $staff->department_id;
        $statusFilter = $request->query('status', 'all');

        $query = ClearanceRequest::where('department_id', $departmentId)
                    ->with('student'); // 'student' relationship uses user_id foreign key

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $clearances    = $query->latest()->paginate(15);
        $pendingCount  = ClearanceRequest::where('department_id', $departmentId)->where('status', 'pending')->count();
        $approvedCount = ClearanceRequest::where('department_id', $departmentId)->where('status', 'approved')->count();
        $rejectedCount = ClearanceRequest::where('department_id', $departmentId)->where('status', 'rejected')->count();

        return view('department.dashboard', compact(
            'staff', 'clearances', 'statusFilter',
            'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    // Approve a clearance request
    public function approve(Request $request, ClearanceRequest $clearance)
    {
        $staff = Auth::user();

        // Security: staff can only approve requests from their own department
        if ($clearance->department_id !== $staff->department_id) {
            return back()->with('error', 'You are not authorized to process this request.');
        }

        $request->validate(['comment' => 'nullable|string|max:500']);

        $clearance->update([
            'status'       => 'approved',
            'comment'      => $request->comment ?? 'Cleared',
            'processed_by' => $staff->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', "Clearance approved for {$clearance->student->name}.");
    }

    // Reject a clearance request (comment required)
    public function reject(Request $request, ClearanceRequest $clearance)
    {
        $staff = Auth::user();

        if ($clearance->department_id !== $staff->department_id) {
            return back()->with('error', 'You are not authorized to process this request.');
        }

        // Comment is REQUIRED on rejection so student knows what to fix
        $request->validate(['comment' => 'required|string|max:500']);

        $clearance->update([
            'status'       => 'rejected',
            'comment'      => $request->comment,
            'processed_by' => $staff->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', "Clearance rejected for {$clearance->student->name}.");
    }

    // View a single clearance request detail
    public function show(ClearanceRequest $clearance)
    {
        $staff = Auth::user();

        if ($clearance->department_id !== $staff->department_id) {
            abort(403, 'Unauthorized access.');
        }

        $clearance->load('student.department');
        return view('department.show', compact('clearance'));
    }
}