{{-- 
    SmartClear Navigation
    This replaces Laravel's default navigation.blade.php
    which referenced route('dashboard') that doesn't exist in our system.
--}}
@auth
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #1a3a6b;">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand fw-bold" href="/">
            <i class="bi bi-mortarboard-fill me-2"></i>SmartClear
        </a>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav me-auto">

                {{-- STUDENT LINKS --}}
                @if(auth()->user()->role === 'student')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                           href="{{ route('student.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"
                           href="{{ route('student.profile') }}">
                            <i class="bi bi-person me-1"></i>My Profile
                        </a>
                    </li>
                @endif

                {{-- STAFF LINKS --}}
                @if(auth()->user()->role === 'staff')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('department.dashboard') ? 'active' : '' }}"
                           href="{{ route('department.dashboard') }}">
                            <i class="bi bi-inbox me-1"></i>Clearance Requests
                        </a>
                    </li>
                @endif

                {{-- ADMIN LINKS --}}
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-grid me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i>Manage
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.students') }}">
                                <i class="bi bi-person-lines-fill me-2"></i>Students
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.staff') }}">
                                <i class="bi bi-people-fill me-2"></i>Staff
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.departments') }}">
                                <i class="bi bi-building me-2"></i>Departments
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('admin.clearances') }}">
                                <i class="bi bi-list-check me-2"></i>All Clearances
                            </a></li>
                        </ul>
                    </li>
                @endif

            </ul>

            {{-- Right side: user + logout --}}
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.2);color:white;font-weight:700;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        {{ auth()->user()->name }}
                        <span class="badge ms-1" style="background-color:#c9991a;">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-1 text-muted small">{{ auth()->user()->email }}</li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endauth
