<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use Illuminate\Support\Facades\Auth;

/**
 * DepartmentController handles the staff dashboard and clearance management for each department.
 * It allows staff to view pending clearance requests, approve or reject them, and track the clearance history for students.
 * Each department has its own dashboard where staff can manage their specific clearance requests.
 * Staff can also view the clearance history of students who have interacted with their department.
 */

class DepartmentController extends Controller
{
    /**
     * Display the department dashboard with pending clearance requests and statistics.
     * This method retrieves the pending clearance requests for the logged-in staff's department and calculates statistics such as total requests, approved, rejected, and pending counts.
     */
    public function dashboard(Request $request)
    {
        $staff = auth::user();
        $departmentId = $staff->department_id;
        //staff only see their department's clearance requests

        $statusFilter = $request->query('status', 'all');
        // Get the status filter from the query parameters

        $query = ClearanceRequest::where('department_id', $departmentId)
            ->with('student');

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $clearances = $query->latest()->paginate(10);


        $pendingCount = ClearanceRequest::where('department_id', $departmentId)
            ->where('status', 'pending')
            ->count();

            $approvedCount = ClearanceRequest::where('department_id', $departmentId)
            ->where('status', 'approved')
            ->count();

            $rejectedCount = ClearanceRequest::where('department_id', $departmentId)
            ->where('status', 'rejected')
            ->count();

        return view('department.dashboard', compact(
            'clearances', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'statusFilter'
            ));
    }


    /**
     * Process a clearance request by approving or rejecting it.
     * This method updates the status of a clearance request based on the staff's decision. It
     */

    public function approve(Request $request, ClearanceRequest $clearance)
    {
        $staff = auth::user();

        if ($clearance->department_id !== $staff->department_id) {
            return back()->with('error', 'You are not the owner of this clearance request.');

            }

            $request->validate([
                'comment' => 'nullable|string|max:255',
            ]);

        $clearance->update([
            'status' => 'approved',
            'comment' => $request->comment?? 'cleared',
            'processed_by' => $staff->id, //record who approved it
            'processed_at' => now(), //record when it was approved
        ]);

        return back()->with('success', 'Clearance request approved for ' . $clearance->student->name);

    }

    //Reject a clearance request
    public function reject(Request $request, ClearanceRequest $clearance)
    {
        $staff = auth::user();

        if ($clearance->department_id !== $staff->department_id) {
            return back()->with('error', 'You are not the owner of this clearance request.');

            }


            $request->validate([
                'comment' => 'nullable|string|max:255',
            ]);

        $clearance->update([
            'status' => 'rejected',
            'comment' => $request->comment,
            'processed_by' => $staff->id, //record who rejected it
            'processed_at' => now(), //record when it was rejected
        ]);

        return back()->with('success', 'Clearance request rejected for ' . $clearance->student->name);
    }

    //View details of a single clearance request
    public function show(ClearanceRequest $clearance)
    {
        $staff = auth::user();

        if ($clearance->department_id !== $staff->department_id) {
            abort(403, 'Unauthorized access to this clearance request.');

            }

            $clearance->load('student.department');

            return view('department.clearance_show', compact('clearance'));
    }
}
