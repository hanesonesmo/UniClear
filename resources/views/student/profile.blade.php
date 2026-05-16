{{-- Student Profile Page --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="mb-4">
                <h2 class="fw-bold text-navy mb-1"><i class="bi bi-person me-2"></i>My Profile</h2>
                <p class="text-muted">Update your personal information.</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger small">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('student.profile.update') }}" method="POST">
                        @csrf

                        {{-- Read-only: email and registration number can't be changed --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Email Address (cannot be changed)</label>
                            <input type="email" class="form-control bg-light" value="{{ $student->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Registration Number (cannot be changed)</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $student->registration_number ?? 'Not set' }}" disabled>
                        </div>

                        {{-- Editable fields --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Full Name</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $student->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-medium">Phone Number</label>
                            <input type="text" name="phone" id="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $student->phone) }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department_id" class="form-label fw-medium">Department / Faculty</label>
                            <select name="department_id" id="department_id"
                                class="form-select @error('department_id') is-invalid @enderror" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('department_id', $student->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->department_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-navy px-5">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection