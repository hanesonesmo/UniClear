<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 * Restricts routes by user role.
 * Registered in bootstrap/app.php as 'role' alias.
 * Usage: ->middleware('role:admin') or ->middleware('role:student,staff')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            // Redirect to correct dashboard if wrong role
            return match($user->role) {
                'admin'   => redirect()->route('admin.dashboard')->with('error', 'Access denied.'),
                'staff'   => redirect()->route('department.dashboard')->with('error', 'Access denied.'),
                'student' => redirect()->route('student.dashboard')->with('error', 'Access denied.'),
                default   => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}