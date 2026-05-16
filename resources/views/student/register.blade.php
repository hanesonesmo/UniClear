{{-- Student Registration Page --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – USmartClear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="auth-page bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">

                    {{-- Header --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-mortarboard-fill text-navy" style="font-size: 2.5rem;"></i>
                        <h1 class="h3 fw-bold text-navy mt-2 mb-1">Create Student Account</h1>
                        <p class="text-muted small">SmartClear – University Clearance System</p>
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger small">
                            @foreach($errors->all() as $error)
                                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- REGISTRATION FORM --}}
                    <form action="{{ route('register.submit') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            {{-- Full Name --}}
                            <div class="col-12">
                                <label for="name" class="form-label fw-medium">Full Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="e.g., John Mwangi"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Registration Number (unique per student) --}}
                            <div class="col-md-6">
                                <label for="registration_number" class="form-label fw-medium">Registration Number</label>
                                <input
                                    type="text"
                                    name="registration_number"
                                    id="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number') }}"
                                    placeholder="e.g., 2021/CS/0042"
                                    required
                                >
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">Phone Number</label>
                                <input
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="+255 7XX XXX XXX"
                                    required
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Academic Department --}}
                            <div class="col-12">
                                <label for="department_id" class="form-label fw-medium">Academic Department / Faculty</label>
                                <select
                                    name="department_id"
                                    id="department_id"
                                    class="form-select @error('department_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Select your department --</option>
                                    @foreach($departments as $dept)
                                        {{-- Keep previous selection if form fails validation --}}
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="your@university.ac.tz"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Min. 8 characters"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control"
                                    placeholder="Repeat password"
                                    required
                                >
                            </div>

                        </div>{{-- end .row --}}

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold mt-4">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>

                    {{-- Link back to login --}}
                    <div class="text-center mt-3">
                        <p class="text-muted small mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-navy fw-medium">Sign in here</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
