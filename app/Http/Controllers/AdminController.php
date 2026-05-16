<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students'   => User::where('role', 'student')->count(),
            'total_staff'      => User::where('role', 'staff')->count(),
            'total_depts'      => Department::count(),
            'pending_count'    => ClearanceRequest::where('status', 'pending')->count(),
            'approved_count'   => ClearanceRequest::where('status', 'approved')->count(),
            'rejected_count'   => ClearanceRequest::where('status', 'rejected')->count(),
            'cleared_students' => $this->countFullyClearedStudents(),
        ];

        $recentActivity = ClearanceRequest::with(['student', 'department'])
                            ->whereIn('status', ['approved', 'rejected'])
                            ->latest('processed_at')
                            ->take(10)
                            ->get();

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }

    public function students(Request $request)
    {
        $search = $request->query('search');
        $students = User::where('role', 'student')
                        ->when($search, function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('registration_number', 'like', "%{$search}%");
                        })
                        ->with('department')
                        ->withCount([
                            'clearanceRequests as approved_count' => fn($q) => $q->where('status', 'approved'),
                            'clearanceRequests as total_count',
                        ])
                        ->paginate(20);
        $totalDepts = Department::count();
        return view('admin.students', compact('students', 'totalDepts', 'search'));
    }

    public function deleteStudent(User $user)
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Only student accounts can be deleted from this section.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Student account for '{$name}' has been deleted.");
    }

    public function staff()
    {
        $staffMembers = User::where('role', 'staff')->with('department')->get();
        $departments  = Department::all();
        return view('admin.staff', compact('staffMembers', 'departments'));
    }

    public function createStaff(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'password'      => ['required', Password::min(8)],
        ]);
        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'department_id' => $request->department_id,
            'password'      => Hash::make($request->password),
            'role'          => 'staff',
        ]);
        return back()->with('success', 'Staff account created successfully.');
    }

    public function deleteStaff(User $user)
    {
        if ($user->role !== 'staff') {
            return back()->with('error', 'Only staff accounts can be deleted here.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Staff account for '{$name}' has been removed.");
    }

    public function departments()
    {
        $departments = Department::withCount([
            'clearanceRequests as pending_count' => fn($q) => $q->where('status', 'pending'),
            'clearanceRequests as total_count',
        ])->get();
        return view('admin.departments', compact('departments'));
    }

    public function createDepartment(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|unique:departments,department_name',
            'description'     => 'nullable|string|max:255',
        ]);
        Department::create($request->only('department_name', 'description'));
        return back()->with('success', 'Department added successfully.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => "required|string|unique:departments,department_name,{$department->id}",
            'description'     => 'nullable|string|max:255',
        ]);
        $department->update($request->only('department_name', 'description'));
        return back()->with('success', 'Department updated successfully.');
    }

    public function deleteDepartment(Department $department)
    {
        $name = $department->department_name;
        $department->delete();
        return back()->with('success', "Department '{$name}' has been deleted.");
    }

    public function clearanceRequests(Request $request)
    {
        $deptFilter   = $request->query('department');
        $statusFilter = $request->query('status');
        $requests = ClearanceRequest::with(['student', 'department'])
                        ->when($deptFilter,   fn($q) => $q->where('department_id', $deptFilter))
                        ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
                        ->latest()
                        ->paginate(25);
        $departments = Department::all();
        return view('admin.clearances', compact('requests', 'departments', 'deptFilter', 'statusFilter'));
    }

    private function countFullyClearedStudents(): int
    {
        $totalDepts = Department::count();
        if ($totalDepts === 0) return 0;

        $studentIds   = User::where('role', 'student')->pluck('id');
        $clearedCount = 0;

        foreach ($studentIds as $studentId) {
            $approvedCount = ClearanceRequest::where('user_id', $studentId)
                                ->where('status', 'approved')
                                ->count();
            if ($approvedCount >= $totalDepts) {
                $clearedCount++;
            }
        }
        return $clearedCount;
    }
}
