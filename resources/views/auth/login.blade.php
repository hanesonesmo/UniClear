{{-- Login Page --}}
{{-- This is a simple standalone page — no navbar needed, so we don't extend app layout --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SmartClear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="auth-page bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">

            {{-- Login Card --}}
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">

                    {{-- Logo / Branding --}}
                    <div class="text-center mb-4">
                        <div class="brand-icon mb-3">
                            <i class="bi bi-mortarboard-fill text-navy" style="font-size: 2.5rem;"></i>
                        </div>
                        <h1 class="h3 fw-bold text-navy mb-1">SmartClear</h1>
                        <p class="text-muted small">University Clearance Management System</p>
                    </div>

                    {{-- Validation error messages --}}
                    {{-- We use $errors->login (named bag) so register errors never appear here --}}
                    @if($errors->login->any())
                        <div class="alert alert-danger small">
                            @foreach($errors->login->all() as $error)
                                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Success message (e.g., after logout) --}}
                    @if(session('success'))
                        <div class="alert alert-success small">{{ session('success') }}</div>
                    @endif

                    {{-- LOGIN FORM --}}
                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf {{-- CSRF protection token — required for all POST forms in Laravel --}}

                        {{-- Email Field --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control @if($errors->login->has('email')) is-invalid @endif"
                                    value="{{ old('email') }}" {{-- Repopulate on validation failure --}}
                                    placeholder="your@email.com"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        {{-- Password Field --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control @if($errors->login->has('password')) is-invalid @endif"
                                    placeholder="Enter your password"
                                    required
                                >
                                {{-- Toggle password visibility --}}
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me Checkbox --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-muted small" for="remember">Keep me logged in</label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </form>

                    {{-- Register Link --}}
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">
                            New student?
                            <a href="{{ route('register') }}" class="text-navy fw-medium">Create an account</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility on the login form
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon       = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
</body>
</html>