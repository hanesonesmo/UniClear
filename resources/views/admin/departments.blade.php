{{-- Admin: Manage Departments --}}
@extends('layouts.app')

@section('title', 'Manage Departments')

@section('content')
<div class="container">

    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1"><i class="bi bi-building me-2"></i>Manage Departments</h2>
        <p class="text-muted">Add, edit, or remove university clearance departments.</p>
    </div>

    <div class="row g-4">

        {{-- =========================================
             LEFT: ADD DEPARTMENT FORM
             ========================================= --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-plus-circle me-2 text-navy"></i>Add Department
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.create') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Department Name</label>
                            <input
                                type="text"
                                name="department_name"
                                class="form-control @error('department_name') is-invalid @enderror"
                                value="{{ old('department_name') }}"
                                placeholder="e.g., Sports Department"
                                required
                            >
                            @error('department_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Description (Optional)</label>
                            <textarea
                                name="description"
                                class="form-control"
                                rows="2"
                                placeholder="What does this department handle?">{{ old('description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-navy w-100">
                            <i class="bi bi-plus me-1"></i>Add Department
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- =========================================
             RIGHT: DEPARTMENTS LIST
             ========================================= --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-list-ul me-2 text-navy"></i>
                    All Departments
                    <span class="badge bg-light text-muted ms-2">{{ $departments->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">#</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Pending</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $index => $dept)
                                <tr>
                                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $dept->department_name }}</td>
                                    <td class="text-muted small">{{ $dept->description ?? '—' }}</td>
                                    <td class="text-center">
                                        @if($dept->pending_count > 0)
                                            <span class="badge bg-warning text-dark">{{ $dept->pending_count }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">{{ $dept->total_count }}</td>

                                    {{-- Edit + Delete --}}
                                    <td class="text-center">
                                        {{-- Edit button triggers modal --}}
                                        <button class="btn btn-sm btn-outline-secondary me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $dept->id }}"
                                            data-name="{{ $dept->department_name }}"
                                            data-desc="{{ $dept->description }}"
                                            data-url="{{ route('admin.departments.update', $dept) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete button --}}
                                        <form action="{{ route('admin.departments.delete', $dept) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete {{ addslashes($dept->department_name) }}? All related clearance requests will also be deleted.')">
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
                                        <td colspan="6" class="text-center text-muted py-4">No departments found.</td>
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

{{-- Edit Department Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-navy">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT') {{-- Laravel uses PUT for update routes --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Department Name</label>
                        <input type="text" name="department_name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" id="editDesc" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Populate the edit modal with existing department data when opened
    document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('editName').value  = btn.dataset.name;
        document.getElementById('editDesc').value  = btn.dataset.desc || '';
        document.getElementById('editForm').action = btn.dataset.url;
    });
</script>
@endpush

@endsection