<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController
 * Handles login, register, logout.
 */
class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    // Show register form
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        $departments = Department::all();
        return view('auth.register', compact('departments'));
    }

    // Process login
    public function login(Request $request)
    {
        // Named error bag 'login' so errors don't bleed into register page
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($validator, 'login');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectToDashboard();
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.'], 'login');
    }

    // Process registration (students only)
    public function register(Request $request)
    {
        // Named error bag 'register' so errors don't bleed into login page
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'registration_number' => [
                'required',
                'unique:users,registration_number',
                
                  
                 // Format: BCS-01-0001-2023 (Course-Campus-Number-Year)
'regex:/^[A-Z]{2,4}-\d{2}-\d{4}-\d{4}$/',
            ],
            'phone'               => 'required|string|max:20',
            'department_id'       => 'required|exists:departments,id',
            'password'            => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator, 'register');
        }

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'registration_number' => $request->registration_number,
            'phone'               => $request->phone,
            'department_id'       => $request->department_id,
            'password'            => Hash::make($request->password),
            'role'                => 'student',
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard')
                         ->with('success', 'Welcome to SmartClear! Your account has been created.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    // Redirect user to correct dashboard based on role
    private function redirectToDashboard()
    {
        return match(Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'staff'   => redirect()->route('department.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect()->route('login'),
        };
    }
}
