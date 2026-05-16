{{-- Admin: All Clearance Requests --}}
@extends('layouts.app')

@section('title', 'All Clearances')

@section('content')
<div class="container">

    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1"><i class="bi bi-list-check me-2"></i>All Clearance Requests</h2>
        <p class="text-muted">View every clearance request across all departments.</p>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.clearances') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Filter by Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $deptFilter == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"  {{ $statusFilter === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.clearances') }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold border-bottom">
            Showing {{ $requests->total() }} result(s)
        </div>
        <div class="card-body p-0">
            @if($requests->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2.5rem;"></i>
                    <p class="mt-3">No clearance requests found with the selected filters.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Student</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Comment</th>
                                <th>Submitted</th>
                                <th>Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $index => $req)
                            <tr>
                                <td class="px-4 text-muted">{{ $requests->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ $req->student->name }}</div>
                                    <div class="text-muted small">{{ $req->student->registration_number }}</div>
                                </td>
                                <td class="text-muted small">{{ $req->department->department_name }}</td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-muted small" style="max-width: 180px;">
                                    {{ $req->comment ?? '—' }}
                                </td>
                                <td class="text-muted small">{{ $req->created_at->format('d M Y') }}</td>
                                <td class="text-muted small">
                                    {{ $req->processed_at ? $req->processed_at->format('d M Y') : '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $requests->appends(['department' => $deptFilter, 'status' => $statusFilter])->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection