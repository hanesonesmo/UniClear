<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RoleMiddleware
{

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
                }

             $user = $request->user();
             if (!$user || !in_array($user->role, $roles)) {
                return match($user?->role) {
                     'admin' => redirect()->route('admin.dashboard')
                     ->with('error', 'Access denied.'),

                     'staff' =>redirect()->route('department.dashboard')
                     ->with('error', 'Access denied.'),

                     'student' => redirect()->route('student.dashboard')
                     ->with('error', 'Access denied.'),

                     default => redirect()->route('login'),
                };
                }
                

        return $next($request);
    }
}
