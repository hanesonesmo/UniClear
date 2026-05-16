{{-- Department Staff Dashboard --}}
@extends('layouts.app')

@section('title', 'Department Dashboard')

@section('content')
<div class="container">

    {{-- =========================================
         HEADER
         ========================================= --}}
    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1">
            <i class="bi bi-inbox me-2"></i>{{ $staff->department->department_name ?? 'Department' }} – Clearance Requests
        </h2>
        <p class="text-muted mb-0">Review and process student clearance requests for your department.</p>
    </div>

    {{-- =========================================
         STATS CARDS
         ========================================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <i class="bi bi-clock-history text-warning" style="font-size: 1.8rem;"></i>
                    <h3 class="fw-bold text-navy mt-2 mb-0">{{ $pendingCount }}</h3>
                    <p class="text-muted small mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.8rem;"></i>
                    <h3 class="fw-bold text-navy mt-2 mb-0">{{ $approvedCount }}</h3>
                    <p class="text-muted small mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.8rem;"></i>
                    <h3 class="fw-bold text-navy mt-2 mb-0">{{ $rejectedCount }}</h3>
                    <p class="text-muted small mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================
         FILTER TABS
         ========================================= --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-pills gap-2">
                {{-- Filter by status — passes 'status' query param in URL --}}
                @foreach(['all' => 'All Requests', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $statusFilter === $value ? 'active bg-navy' : 'text-muted' }}"
                           href="{{ route('department.dashboard', ['status' => $value]) }}">
                            {{ $label }}
                            @if($value === 'pending' && $pendingCount > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- =========================================
             REQUESTS TABLE
             ========================================= --}}
        <div class="card-body p-0">
            @if($clearances->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-0">No {{ $statusFilter !== 'all' ? $statusFilter : '' }} requests found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Student</th>
                                <th>Registration No.</th>
                                <th>Status</th>
                                <th>Comment</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clearances as $index => $clearance)
                            <tr>
                                <td class="px-4 text-muted">{{ $clearances->firstItem() + $index }}</td>

                                {{-- Student info --}}
                                <td>
                                    <div class="fw-medium">{{ $clearance->student->name }}</div>
                                    <div class="text-muted small">{{ $clearance->student->email }}</div>
                                </td>

                                <td class="text-muted">{{ $clearance->student->registration_number ?? '—' }}</td>

                                {{-- Status badge --}}
                                <td>
                                    @if($clearance->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($clearance->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>

                                <td class="text-muted small" style="max-width: 200px;">
                                    {{ $clearance->comment ?? '—' }}
                                </td>

                                <td class="text-muted small">
                                    {{ $clearance->created_at->format('d M Y') }}
                                </td>

                                {{-- ACTION BUTTONS --}}
                                <td class="text-center">
                                    @if($clearance->isPending())
                                        {{-- APPROVE BUTTON --}}
                                        <button class="btn btn-sm btn-success me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal"
                                            data-id="{{ $clearance->id }}"
                                            data-name="{{ $clearance->student->name }}"
                                            data-url="{{ route('department.clearance.approve', $clearance) }}">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>

                                        {{-- REJECT BUTTON --}}
                                        <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal"
                                            data-id="{{ $clearance->id }}"
                                            data-name="{{ $clearance->student->name }}"
                                            data-url="{{ route('department.clearance.reject', $clearance) }}">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    @else
                                        {{-- Already processed — show who handled it --}}
                                        <span class="text-muted small">
                                            Processed {{ $clearance->processed_at?->diffForHumans() }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination links --}}
                <div class="p-3">
                    {{ $clearances->appends(['status' => $statusFilter])->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- =====================================================
     APPROVE MODAL
     Pops up when staff clicks "Approve"
     ===================================================== --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-success">
                    <i class="bi bi-check-circle me-2"></i>Approve Clearance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Approving clearance for: <strong id="approveStudentName"></strong>
                    </p>
                    {{-- Optional approval comment --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Comment (Optional)</label>
                        <textarea name="comment" class="form-control" rows="2"
                            placeholder="e.g., All books returned and account settled"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =====================================================
     REJECT MODAL
     Pops up when staff clicks "Reject" — comment required
     ===================================================== --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-x-circle me-2"></i>Reject Clearance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Rejecting clearance for: <strong id="rejectStudentName"></strong>
                    </p>
                    {{-- Rejection reason is REQUIRED so student knows what to fix --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea name="comment" class="form-control" rows="3" required
                            placeholder="e.g., You have 3 unreturned books. Please return them and reapply."></textarea>
                        <div class="form-text text-danger">Required — the student will see this message.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // -------------------------------------------------------
    // Approve Modal — populate form action URL and student name
    // -------------------------------------------------------
    const approveModal = document.getElementById('approveModal');
    approveModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        document.getElementById('approveStudentName').textContent = button.dataset.name;
        document.getElementById('approveForm').action = button.dataset.url;
    });

    // -------------------------------------------------------
    // Reject Modal — populate form action URL and student name
    // -------------------------------------------------------
    const rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('rejectStudentName').textContent = button.dataset.name;
        document.getElementById('rejectForm').action = button.dataset.url;
    });
</script>
@endpush

@endsection