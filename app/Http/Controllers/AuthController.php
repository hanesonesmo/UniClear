<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;   


class AuthController extends Controller
{
    //show forms 

public function showLogin()
{
    if (Auth::check()){
        return $this->redirectToDashboard();
    }
    return view('auth.login');

}

//SHOWING STUDENT REGISTRATION FORM
public function showRegister()
{
    if (Auth::check()){
        return $this->redirectToDashboard();
    }
$departments = Department::all();

    return view('auth.register', compact('departments'));
}

//Process the actions
//validates credentials, authenticates, the user,then redirect into their dashboard

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {

        $request->session()->regenerate();

        return $this->redirectToDashboard();
    }

    return back()
    ->withInput($request->only('email'))
    ->withErrors(['email' => 'The provided credentials do not match our records.']);
}

//Process registration form, create user, then log them in and redirect to dashboard    
public function register(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        'department_id' => ['required', 'exists:departments,id'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'department_id' => $request->department_id,
        'role' => 'student',
    ]);

    Auth::login($user);

    return redirect()->route('student.dashboard')
        ->with('success', 'Registration successful. Welcome to UniClear system!');
}

//Logout the user and invalidate the session
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'You have been logged out successfully.');
}

//Helper function to redirect users to their respective dashboards based on role
private function redirectToDashboard()
{
    return match(Auth::user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        'staff' => redirect()->route('department.dashboard'),
        default => redirect()->route('login'),
    };

}

}
