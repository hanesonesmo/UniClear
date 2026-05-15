<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AdminController;

/*|--------------------------------------------------------------------------
| Web Routes foru uniclear

//ROOT REDIRECT
//Redirect the homepage to login or dashboard bsed on auth status
//
*/

Route::get('/', function () {
    if (auth()-> guard()->check()) {
        return match(auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),

            'student' => redirect()->route('student.dashboard'),

            'staff' => redirect()->route('department.dashboard'),

            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

//AUTHENTICATION ROUTES (guest only)
//these routes are only accessible for users who are not logged in

Route::middleware('guest')->group(function () {
    //show login form
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    //process login form
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    //show registration form
    Route::get('/register', [AuthController::class, 'register'])->name('register');

    //process registration form
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

//logout route (auth only)

Route::post('/logout', [AuthController::class, 'logout'])
->name('logout')
->middleware('auth');

//STUDENT ROUTES
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function (){

//student dashboard showing clearance progess
Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

//submit clearance request creating 7 records one per department
Route::post('/clearance/submit', [StudentController::class, 'submitClearance'])->name('clearance.submit');

//student profile
Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('profile.update');

//download final clearance report
Route::get('/report/download', [StudentController::class, 'downloadReport'])->name('report.download');
});


//DEPARTMENT STAFF ROUTES
Route::middleware(['auth', 'role:staff'])->prefix('department')->name('department.')->group(function () {

//Department dashboard, showing all clearance request for the department
Route::get('/dashboard', [DepartmentController::class, 'dashboard'])->name('dashboard');

//view a single learance request details
Route::get('/dashboard/{clearance}', [DepartmentController::class, 'show'])->name('clearance.show');

//Approve a clearance request
Route::post('/dashboard/{clearance}/approve', [DepartmentController::class, 'approve'])->name('clearance.approve');

//Reject a clearance request
Route::post('/dashboard/{clearance}/reject', [DepartmentController::class, 'reject'])->name('clearance.reject');
});

//ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

//Admin dashboard overview
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

//STUDENT MANAGEMENT
Route::get('/students', [AdminController::class, 'students'])->name('students');
Route::delete('students/{user}', [AdminController::class, 'deleteStudent'])->name('students.delete');

//STAFF MANAGEMENT
Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
Route::post('/staff', [AdminController::class, 'createStaff'])->name('staff.create');
Route::delete('/staff/{user}', [AdminController::class, 'deleteStaff'])->name('staff.delete');

//DEPARTMENT MANAGEMENT
Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
Route::post('/department', [AdminController::class, 'createDepartment'])->name('departments.create');
Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('departments.update');  
Route::delete('/department/{department}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');

//CLEARANCE REQUEST OVERVIEW FOR ALL USERS
Route::get('/clearances', [AdminController::class, 'clearanceRequests'])->name('clearances');
});