<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * StudentController
 * Handles student dashboard, clearance submission, profile, report download.
 */
class StudentController extends Controller
{
    // Student dashboard — shows clearance status per department
    public function dashboard()
    {
        $student = Auth::user();

        // Load all clearance requests for this student
        // NOTE: foreign key is 'user_id' (not student_id) — matches actual DB column
        $clearances = ClearanceRequest::where('user_id', $student->id)
                        ->with('department')
                        ->get();

        $hasApplied     = $clearances->isNotEmpty();
        $approvedCount  = $clearances->where('status', 'approved')->count();
        $totalDepts     = Department::count();
        $isFullyCleared = $hasApplied && ($approvedCount === $totalDepts);

        return view('student.dashboard', compact(
            'student', 'clearances', 'hasApplied',
            'approvedCount', 'totalDepts', 'isFullyCleared'
        ));
    }

    // Submit clearance request — creates one record per department
    public function submitClearance(Request $request)
    {
        $student = Auth::user();

        // Prevent duplicate submissions
        $alreadyApplied = ClearanceRequest::where('user_id', $student->id)->exists();
        if ($alreadyApplied) {
            return back()->with('error', 'You have already submitted a clearance request.');
        }

        // Create one pending record per department
        $departments = Department::all();
        foreach ($departments as $department) {
            ClearanceRequest::create([
                'user_id'       => $student->id,
                'department_id' => $department->id,
                'status'        => 'pending',
                'comment'       => null,
            ]);
        }

        return back()->with('success', 'Clearance request submitted! Departments will now review your request.');
    }

    // Show student profile
    public function profile()
    {
        $student     = Auth::user();
        $departments = Department::all();
        return view('student.profile', compact('student', 'departments'));
    }

    // Update student profile
    public function updateProfile(Request $request)
    {
        $student = Auth::user();

        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
        ]);

        $student->update([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'department_id' => $request->department_id,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    // Download clearance certificate (only when fully cleared)
    public function downloadReport()
    {
        $student = Auth::user();

        if (!$student->isFullyCleared()) {
            return back()->with('error', 'You can only download your report when all departments have approved.');
        }

        $clearances = ClearanceRequest::where('user_id', $student->id)
                        ->with(['department', 'processedBy'])
                        ->get();

        return view('student.report', compact('student', 'clearances'));
    }
}