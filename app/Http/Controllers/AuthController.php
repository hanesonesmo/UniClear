<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController
 *
 * Handles all authentication actions:
 * - Show login/register forms
 * - Process login attempts
 * - Register new student accounts
 * - Log users out
 */
class AuthController extends Controller
{
    // -------------------------------------------------------
    // SHOW FORMS
    // -------------------------------------------------------

    /**
     * Show the login form.
     * Redirect already-authenticated users to their dashboard.
     */
    public function showLogin()
    {
        // If user is already logged in, send them to the right place
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    /**
     * Show the student registration form.
     * Passes the list of departments so students can select their faculty.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        $departments = Department::all(); // Used in the registration dropdown
        return view('auth.register', compact('departments'));
    }

    // -------------------------------------------------------
    // PROCESS ACTIONS
    // -------------------------------------------------------

    /**
     * Process the login form submission.
     * Validates credentials, authenticates the user, then redirects to their dashboard.
     */
    public function login(Request $request)
    {
        // Validate the submitted form data.
        // Using 'login' as a named error bag so these errors ONLY show on the login form
        // and never bleed into the register page (which uses the 'register' bag).
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($validator, 'login'); // 'login' = named error bag
        }

        // Attempt to log in with the given credentials
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return $this->redirectToDashboard();
        }

        // Login failed — use named bag so this only appears on the login page
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.'], 'login');
    }

    /**
     * Process the student registration form.
     * Creates a new student account and logs them in immediately.
     */
    public function register(Request $request)
    {
        // Validate all required registration fields.
        // Using 'register' as named error bag — keeps these errors away from the login page.
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'registration_number' => 'required|string|unique:users,registration_number',
            'phone'               => 'required|string|max:20',
            'department_id'       => 'required|exists:departments,id',
            'password'            => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator, 'register'); // 'register' = named error bag
        }

        // Create the new student user
        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'registration_number' => $request->registration_number,
            'phone'               => $request->phone,
            'department_id'       => $request->department_id,
            'password'            => Hash::make($request->password), // Always hash passwords!
            'role'                => 'student',                       // New registrations are always students
        ]);

        // Log the user in right after registration
        Auth::login($user);

        return redirect()->route('student.dashboard')
                         ->with('success', 'Welcome to SmartClear! Your account has been created.');
    }

    /**
     * Log the user out and destroy their session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate and regenerate the session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    // -------------------------------------------------------
    // PRIVATE HELPER
    // -------------------------------------------------------

    /**
     * Redirect the user to the correct dashboard based on their role.
     * Called after successful login or registration.
     */
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