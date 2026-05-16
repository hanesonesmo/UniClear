{{-- Admin: Manage Staff --}}
@extends('layouts.app')

@section('title', 'Manage Staff')

@section('content')
<div class="container">

    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1"><i class="bi bi-people me-2"></i>Manage Department Staff</h2>
        <p class="text-muted">Create and manage staff accounts for each department.</p>
    </div>

    <div class="row g-4">

        {{-- ADD STAFF FORM --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-person-plus me-2 text-navy"></i>Add Staff Account
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger small">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.staff.create') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name') }}" required placeholder="Staff full name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email') }}" required placeholder="staff@university.ac.tz">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Department</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->department_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Default Password</label>
                            <input type="password" name="password" class="form-control"
                                required placeholder="Min. 8 characters">
                            <div class="form-text">Staff should change this password on first login.</div>
                        </div>

                        <button type="submit" class="btn btn-navy w-100">
                            <i class="bi bi-person-plus me-1"></i>Create Staff Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- STAFF TABLE --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-list-ul me-2 text-navy"></i>
                    All Staff
                    <span class="badge bg-light text-muted ms-2">{{ $staffMembers->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Joined</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffMembers as $index => $staff)
                                <tr>
                                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $staff->name }}</td>
                                    <td class="text-muted small">{{ $staff->email }}</td>
                                    <td class="text-muted small">{{ $staff->department?->department_name ?? '—' }}</td>
                                    <td class="text-muted small">{{ $staff->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.staff.delete', $staff) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete {{ addslashes($staff->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No staff accounts yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection