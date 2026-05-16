{{-- Admin: Manage Students --}}
@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
<div class="container">

    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1"><i class="bi bi-person-lines-fill me-2"></i>Manage Students</h2>
        <p class="text-muted">View all registered students and their clearance progress.</p>
    </div>

    {{-- Search Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students') }}" class="d-flex gap-2">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by name, email, or registration number..."
                    value="{{ $search ?? '' }}"
                >
                <button type="submit" class="btn btn-navy px-4">
                    <i class="bi bi-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-3">
            <h5 class="mb-0 fw-semibold text-navy">
                All Students
                <span class="badge bg-light text-muted ms-2">{{ $students->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($students->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-3">No students found{{ $search ? " for '$search'" : '' }}.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Student</th>
                                <th>Reg. Number</th>
                                <th>Department</th>
                                <th>Clearance Progress</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="px-4 text-muted">{{ $students->firstItem() + $index }}</td>

                                <td>
                                    <div class="fw-medium">{{ $student->name }}</div>
                                    <div class="text-muted small">{{ $student->email }}</div>
                                </td>

                                <td class="text-muted">{{ $student->registration_number ?? '—' }}</td>

                                <td class="text-muted small">{{ $student->department?->department_name ?? '—' }}</td>

                                {{-- Progress bar showing approved / total departments --}}
                                <td style="min-width: 140px;">
                                    @php
                                        $approved = $student->approved_count ?? 0;
                                        $percent  = $totalDepts > 0 ? round($approved / $totalDepts * 100) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                      <div class="progress-bar {{ $percent === 100 ? 'bg-success' : 'bg-navy' }}"
     style="width: {{ $percent ?? 0 }}%;">
</div>
                                    </div>
                                        <span class="text-muted small">{{ $approved }}/{{ $totalDepts }}</span>
                                    </div>
                                </td>

                                {{-- Full cleared badge --}}
                                <td>
                                    @if($approved === $totalDepts && $student->total_count > 0)
                                        <span class="badge bg-success">Fully Cleared</span>
                                    @elseif($student->total_count > 0)
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">Not Applied</span>
                                    @endif
                                </td>

                                {{-- Delete student button --}}
                                <td class="text-center">
                                    <form action="{{ route('admin.students.delete', $student) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete student {{ addslashes($student->name) }}? This will also delete all their clearance records.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-3">
                    {{ $students->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection