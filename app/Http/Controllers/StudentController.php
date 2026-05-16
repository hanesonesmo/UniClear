<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use Illuminate\Support\Facades\Auth;

///StudentController handles the student dashboard and clearance request submission.
//It allows students to view their clearance progress, submit new clearance requests, and track the status

class StudentController extends Controller
{
    //load the clearance status for each department and overall progress

    public function dashboard()
    {
        $student = Auth::user();

        $clearances = ClearanceRequest::where('user_id', $student->id)
            ->with('department')
            ->get();

        $hashApplied = $clearances->isNotEmpty();
        //count how many departments have approved clearance

        $approvedCount = $clearances->where('status', 'approved')->count();
        $totalDepartments = Department::count();

        $isFullyCleared = $hashApplied && ($approvedCount === $totalDepartments);

        return view('student.dashboard', compact(
            'clearances',
            'student',
            'isFullyCleared',
            'totalDepartments',
            'hashApplied',
            'approvedCount'
        ));
    }

    //Submit clearance request - creates or updates 7 records for each department
    public function submitClearance(Request $request)
    {
        $student = Auth::user();

        $alreadyApplied = ClearanceRequest::where('user_id', $student->id)->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already submitted a clearance request. Please wait for it to be processed before submitting again.');

        }

        //get all departments and create a clearance request for each
        $departments = Department::all();
        foreach ($departments as $department) {
            ClearanceRequest::create([
                'user_id' => $student->id,
                'department_id' => $department->id,
                'status' => 'pending',
            ]);
        }

        return back()->with('success', 'Clearance request submitted successfully. Please check your dashboard for updates.');
    }

    //show student Profile
    public function profile()
    {
        $student = Auth::user();
        $departments = Department::all();

        return view('student.profile', compact('student', 'departments'));
    }

    //update student profile
    public function updateProfile(Request $request)
    {
        /** @var User $student */
        $student = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
        ]);

        $student->name = $request->name;
        $student->email = $request->email;
        $student->department_id = $request->department_id;
        $student->save();

        return back()->with('success', 'Profile updated successfully.');
    }


//DOWNLOAD FINAL CLEARANCE REPORT
//ONLY AVAILABLE IF ALL DEPARTMENTS HAVE APPROVED CLEARANCE
    public function downloadReport()
    {
        $student = Auth::user();

        $approvedCount = ClearanceRequest::where('user_id', $student->id)
            ->where('status', 'approved')
            ->count();
        $totalDepartments = Department::count();

        if ($approvedCount !== $totalDepartments) {
            return back()->with('error', 'Your clearance is not fully approved yet. Please wait until all departments have approved your clearance before downloading the report.');
        }

        $clearances = ClearanceRequest::where('user_id', $student->id)
            ->with('department', 'processedBy')
            ->get();

            return view('student.report', compact('clearances', 'student'));

    }
}