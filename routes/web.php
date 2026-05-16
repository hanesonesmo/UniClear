<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'staff'   => redirect()->route('department.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::post('/clearance/submit', [StudentController::class, 'submitClearance'])->name('clearance.submit');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('profile.update');
    Route::get('/report/download', [StudentController::class, 'downloadReport'])->name('report.download');
});

Route::middleware(['auth', 'role:staff'])->prefix('department')->name('department.')->group(function () {
    Route::get('/dashboard', [DepartmentController::class, 'dashboard'])->name('dashboard');
    Route::get('/clearance/{clearance}', [DepartmentController::class, 'show'])->name('clearance.show');
    Route::post('/clearance/{clearance}/approve', [DepartmentController::class, 'approve'])->name('clearance.approve');
    Route::post('/clearance/{clearance}/reject', [DepartmentController::class, 'reject'])->name('clearance.reject');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::delete('/students/{user}', [AdminController::class, 'deleteStudent'])->name('students.delete');
    Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
    Route::post('/staff', [AdminController::class, 'createStaff'])->name('staff.create');
    Route::delete('/staff/{user}', [AdminController::class, 'deleteStaff'])->name('staff.delete');
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'createDepartment'])->name('departments.create');
    Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');
    Route::get('/clearances', [AdminController::class, 'clearanceRequests'])->name('clearances');
});
