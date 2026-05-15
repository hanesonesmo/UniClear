<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AdminController handles administrative functions such as managing users and departments.
 * It also handles managing students.
 *managing departments
 *generating reports, and overseeing the clearance process.
 *viewing all students and their clearance status.
    */

class AdminController extends Controller {

    //Admin dashboard showing overview of clearance status and pending requests
    public function dashboard()

{
    //Agregate statistics for the dashboard cards
    $stats = [
        'total_students' =>User::where('role', 'student')->count(),

        'total_staff' =>User::where('role', 'staff')->count(),

        'total_departments' =>Department::count(),
        'dending_count' => ClearanceRequest::where('status', 'pending')->count(),
        'approved_count' => ClearanceRequest::where('status', 'approved')->count(),
        'rejected_count' => ClearanceRequest::where('status', 'rejected')->count(),

        'cleared_students' => $this->countFullyClearedStudents(),
    ];

    //Recent clearance Activity (last 10 requests)
    $recentActivity = ClearanceRequest::with(['student', 'department'])
       ->whereIn('status', ['approved', 'rejected'])    
       ->latest('processed_at')
       ->take(10)
       ->get();

       return view('admin.dashboard', compact('stats', 'recentActivity'));

}

    private function countFullyClearedStudents()
    {
        return User::where('role', 'student')
            ->whereHas('clearanceRequests', fn($q) => $q->where('status', 'approved'))
            ->whereDoesntHave('clearanceRequests', fn($q) => $q->whereIn('status', ['pending', 'rejected']))
            ->count();
    }

//STUDENT MANAGEMENT
//View all students with their clearance status

public function students(Request $request)
{
    $search = $request->query('search');

    $students = User::where('role', 'student')
         ->when($search, function ($query, $search) {

         //allowing search by name or email or registration number
         $query->where('name', 'like', "%$search%")
               ->orWhere('email', 'like', "%$search%")
               ->orWhere('registration_number', 'like', "%$search%");
         })
         ->with('department')
         ->withCount([
             'clearanceRequests as approved_count' => fn($q) => $q->where('status', 'approved'),
             'clearanceRequests as total_count',
         ])
         ->paginate(20);

             $total = Department::count();

             return view('admin.students', compact('students', 'totalDepartments', 'search'));
}

//Delete a student record
public function deleteStudent(User $student)
{
    if ($student->role !== 'student') {
        return back()->with('error', 'Only student accounts can be deleted from this section.');
    }

    $name = $student->name;
    $student->delete();

    return back()->with('success', "Student $name has been deleted successfully.");
}

//STAFF MANAGEMENT
//View all staff members with their department
public function staff()
{
    $staffMembers = User::where('role', 'staff')
        ->with('department')
        ->get();
    $departments = Department::all();
    
    return view('admin.staff', compact('staffMembers', 'departments'));
}

//Create a new staff member account
public function createStaff(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
        'department_id' => 'required|exists:departments,id',
    ]);

    $staff = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'staff',
        'department_id' => $request->department_id,
    ]);

    return back()->with('success', "Staff member {$staff->name} has been created successfully.");
}

//delete a staff member account
public function deleteStaff(User $user)
{
    if ($user->role !== 'staff') {
        return back()->with('error', 'Only staff accounts can be deleted from this section.');
    }

    $name = $user->name;
    $user->delete();

    return back()->with('success', "Staff member $name has been deleted successfully.");
}

//DEPARTMENT MANAGEMENT
//View all departments with staff count and pending clearance requests

public function departments()
{
    $departments = Department::withCount(['clearanceRequests as pending_count' => fn($q) => $q->where('status', 'pending'),
    'clearanceRequests as pending_count',
    ])->get();

    return view('admin.departments', compact('departments'));
}

//Add a new department
public function createDepartment(Request $request)
{
    $request->validate([
        'department_name' => 'required|string|max:255|unique:departments,department_name',
        'description' => 'nullable|string|max:255',
    ]);

    Department::create($request->only('department_name', 'description'));


    return back()->with('success', "Department {$request->department_name} has been created successfully.");
}

//update an existing department
public function updateDepartment(Request $request, Department $department)
{
    $request->validate([
        'department_name' => "required|string|max:255|unique:departments,department_name,{$department->id}",
        'description' => 'nullable|string|max:255',
    ]);

    $department->update($request->only('department_name', 'description'));

    return back()->with('success', "Department {$department->department_name} has been updated successfully.");
}

//delete a department
//this will also delete all associated staff and clearance requests
public function deleteDepartment(Department $department)
{
    $name = $department->department_name;
    $department->delete();

    return back()->with('success', "Department $name and all associated staff and clearance requests have been deleted successfully.");
}

//CLEARANCE REQUEST OVERVIEW FOR ALL USERS
public function clearanceRequests(Request $request)
{
    $departmentFilter = $request->query('department');
    $statusFilter = $request->query('status');

    $requests = ClearanceRequest::with(['student', 'department'])
        ->when($departmentFilter, fn($q) => $q->where('department_id', $departmentFilter))
        ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
        ->latest()
        ->paginate(20);

        $departments = Department::all();

        return view('admin.clearances', compact('requests', 'departments', 'departmentFilter', 'statusFilter'));
}


}