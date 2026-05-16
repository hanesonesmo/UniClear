{{-- Student Dashboard --}}
@extends('layouts.app')

@section('title', 'My Clearance Dashboard')

@section('content')
<div class="container">

    {{-- =========================================
         PAGE HEADER
         ========================================= --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold text-navy mb-1">
                <i class="bi bi-speedometer2 me-2"></i>My Clearance Dashboard
            </h2>
            <p class="text-muted mb-0">
                Welcome, <strong>{{ $student->name }}</strong>
                ({{ $student->registration_number ?? 'N/A' }})
            </p>
        </div>

        {{-- Submit clearance button — only shown if not yet applied --}}
        @if(!$hasApplied)
            <form action="{{ route('student.clearance.submit') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-navy"
                    onclick="return confirm('Submit clearance request to all departments? This cannot be undone.')">
                    <i class="bi bi-send me-2"></i>Apply for Clearance
                </button>
            </form>
        @endif
    </div>

    {{-- =========================================
         STATUS SUMMARY CARDS
         ========================================= --}}
    @if($hasApplied)
    <div class="row g-3 mb-4">

        {{-- Overall clearance status --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100
                {{ $isFullyCleared ? 'bg-success text-white' : 'bg-white' }}">
                <div class="card-body text-center py-4">
                    @if($isFullyCleared)
                        <i class="bi bi-patch-check-fill" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-2 mb-0 fw-bold">Fully Cleared!</h5>
                        <p class="small mb-0 opacity-75">All departments approved</p>
                    @else
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-2 mb-0 fw-bold text-navy">In Progress</h5>
                        <p class="text-muted small mb-0">Awaiting department approvals</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Approved count --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                    <h2 class="fw-bold text-navy mt-2 mb-0">{{ $approvedCount }} / {{ $totalDepts }}</h2>
                    <p class="text-muted small mb-0">Departments Approved</p>
                </div>
            </div>
        </div>

        {{-- Rejected / Pending count --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    @php
                        $rejectedCount = $clearances->where('status', 'rejected')->count();
                        $pendingCount  = $clearances->where('status', 'pending')->count();
                    @endphp
                    <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                    <h2 class="fw-bold text-navy mt-2 mb-0">{{ $pendingCount }}</h2>
                    <p class="text-muted small mb-0">
                        Pending
                        @if($rejectedCount > 0)
                            <span class="text-danger">• {{ $rejectedCount }} Rejected</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- =========================================
         CLEARANCE STATUS TABLE
         ========================================= --}}
    @if($hasApplied)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold text-navy">
                <i class="bi bi-list-check me-2"></i>Department Clearance Status
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Comment / Reason</th>
                            <th>Processed On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clearances as $index => $clearance)
                        <tr>
                            <td class="px-4 text-muted">{{ $index + 1 }}</td>

                            {{-- Department name --}}
                            <td>
                                <div class="fw-medium">{{ $clearance->department->department_name }}</div>
                            </td>

                            {{-- Status badge --}}
                            <td>
                                @if($clearance->status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Approved
                                    </span>
                                @elseif($clearance->status === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Department comment (reason for rejection, etc.) --}}
                            <td class="text-muted small">
                                {{ $clearance->comment ?? '—' }}
                            </td>

                            {{-- When was this processed? --}}
                            <td class="text-muted small">
                                {{ $clearance->processed_at ? $clearance->processed_at->format('d M Y, H:i') : 'Not yet processed' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Download report button — only visible when fully cleared --}}
    @if($isFullyCleared)
        <div class="text-center mt-4">
            <a href="{{ route('student.report.download') }}" class="btn btn-success btn-lg px-5">
                <i class="bi bi-download me-2"></i>Download Clearance Certificate
            </a>
        </div>
    @endif

    @else
    {{-- No clearance submitted yet — show call to action --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-navy fw-semibold">No Clearance Request Yet</h5>
            <p class="text-muted mb-4">
                You haven't submitted a clearance request yet.<br>
                Click the button above to apply — your request will be sent to all 7 departments.
            </p>
            <form action="{{ route('student.clearance.submit') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-navy btn-lg px-5"
                    onclick="return confirm('Submit clearance request to all departments?')">
                    <i class="bi bi-send me-2"></i>Apply for Clearance Now
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection