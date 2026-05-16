{{-- Admin Dashboard --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold text-navy mb-1"><i class="bi bi-grid me-2"></i>Admin Dashboard</h2>
        <p class="text-muted mb-0">System overview and recent activity.</p>
    </div>

    {{-- STATS CARDS ROW --}}
    <div class="row g-3 mb-4">

    {{-- Total Students --}}
    <div class="col-md-3 col-sm-6">
      <div class="card border-0 shadow-sm h-100">
         <div class="card-body d-flex align-items-center gap-3">
         <div class="stat-icon bg-navy-light rounded-3 p-3">
       <i class="bi bi-people-fill text-navy" style="font-size: 1.5rem;"></i>
 </div>

     <div>
       <div class="h3 fw-bold text-navy mb-0">{{ $stats['total_students'] }}</div>
       <div class="text-muted small">Students</div>
         </div>
      </div>
  </div>
</div>

 {{-- Fully Cleared Students --}}
 <div class="col-md-3 col-sm-6">
 <div class="card border-0 shadow-sm h-100">
 <div class="card-body d-flex align-items-center gap-3">
     <div class="stat-icon bg-success-light rounded-3 p-3">
     <i class="bi bi-patch-check-fill text-success" style="font-size: 1.5rem;"></i>
</div>

<div>
 <div class="h3 fw-bold text-navy mb-0">{{ $stats['cleared_students'] }}</div>
   <div class="text-muted small">Fully Cleared</div>
  </div>
    </div>
      </div>
 </div>

{{-- Pending Requests --}}
<div class="col-md-3 col-sm-6">
   <div class="card border-0 shadow-sm h-100">
     <div class="card-body d-flex align-items-center gap-3">
    <div class="stat-icon bg-warning-light rounded-3 p-3">
        <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
 </div>

 <div>
 <div class="h3 fw-bold text-navy mb-0">{{ $stats['pending_count'] }}</div>
 <div class="text-muted small">Pending Requests</div>
  </div>
    </div>
    </div>
 </div>

{{-- Departments --}}
<div class="col-md-3 col-sm-6">
 <div class="card border-0 shadow-sm h-100">
 <div class="card-body d-flex align-items-center gap-3">
 <div class="stat-icon bg-info-light rounded-3 p-3">
   <i class="bi bi-building text-info" style="font-size: 1.5rem;"></i>
</div>

<div>
<div class="h3 fw-bold text-navy mb-0">{{ $stats['total_depts'] }}</div>
<div class="text-muted small">Departments</div>
</div>
 </div>
  </div>
    </div>
    </div>


{{-- CLEARANCE REQUESTS BREAKDOWN --}}
 <div class="row g-3 mb-4">

{{-- Clearance status breakdown --}}
<div class="col-md-4">
<div class="card border-0 shadow-sm h-100">
<div class="card-header bg-white fw-semibold border-bottom">
   <i class="bi bi-pie-chart me-2 text-navy"></i>Clearance Breakdown
</div>

<div class="card-body">
@php
$total = $stats['pending_count'] + $stats['approved_count'] + $stats['rejected_count'];
@endphp

{{-- Pending --}}
<div class="d-flex justify-content-between align-items-center mb-2">
  <span class="text-muted small">Pending</span>
     <strong class="text-warning">{{ $stats['pending_count'] }}</strong>
 </div>

 <div class="progress mb-3" style="height: 6px;">
 <div class="progress-bar bg-warning"
   style="width: {{ $total > 0 ? round($stats['pending_count'] / $total * 100, 2) : 0 }}%;"></div>
 </div>

 {{-- Approved --}}
 <div class="d-flex justify-content-between align-items-center mb-2">
    <span class="text-muted small">Approved</span>
      <strong class="text-success">{{ $stats['approved_count'] }}</strong>
  </div>

<div class="progress mb-3" style="height: 6px;">
<div class="progress-bar bg-success"
  style="width: {{ $total > 0 ? round($stats['approved_count'] / $total * 100, 2) : 0 }}%;"></div>
</div>

{{-- Rejected --}}
<div class="d-flex justify-content-between align-items-center mb-2">
  <span class="text-muted small">Rejected</span>
   <strong class="text-danger">{{ $stats['rejected_count'] }}</strong>
     </div>

     <div class="progress mb-3" style="height: 6px;">
     <div class="progress-bar bg-danger" style="width: {{ $total > 0 ? round($stats['rejected_count'] / $total * 100, 2) : 0 }}%;"></div>
     </div>
   <a href="{{ route('admin.clearances') }}" class="btn btn-sm btn-outline-navy w-100 mt-2">
           View All Requests
         </a>
            </div>
         </div>
        </div>

{{-- Quick Links --}}
<div class="col-md-4">
<div class="card border-0 shadow-sm h-100">
<div class="card-header bg-white fw-semibold border-bottom">
    <i class="bi bi-lightning me-2 text-navy"></i>Quick Actions
 </div>

 <div class="card-body d-flex flex-column gap-2">
  <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary text-start">
     <i class="bi bi-person-lines-fill me-2"></i>Manage Students
       </a>
 <a href="{{ route('admin.staff') }}" class="btn btn-outline-secondary text-start">
     <i class="bi bi-people me-2"></i>Manage Staff
       </a>
 <a href="{{ route('admin.departments') }}" class="btn btn-outline-secondary text-start">
     <i class="bi bi-building me-2"></i>Manage Departments
      </a>
 <a href="{{ route('admin.clearances') }}" class="btn btn-outline-secondary text-start">
     <i class="bi bi-list-check me-2"></i>View All Clearances
        </a>
  </div>
    </div>
    </div>

 {{-- System summary --}}
 <div class="col-md-4">
 <div class="card border-0 shadow-sm h-100">
 <div class="card-header bg-white fw-semibold border-bottom">
     <i class="bi bi-info-circle me-2 text-navy"></i>System Summary
     </div>

 <div class="card-body">
    <ul class="list-group list-group-flush">
       <li class="list-group-item px-0 d-flex justify-content-between">
          <span class="text-muted">Total Students</span>
          <strong>{{ $stats['total_students'] }}</strong>
        </li>

        <li class="list-group-item px-0 d-flex justify-content-between">
            <span class="text-muted">Total Staff</span>
            <strong>{{ $stats['total_staff'] }}</strong>
        </li>
        <li class="list-group-item px-0 d-flex justify-content-between">
            <span class="text-muted">Departments</span>
            <strong>{{ $stats['total_depts'] }}</strong>
             </li>

        <li class="list-group-item px-0 d-flex justify-content-between">
          <span class="text-muted">Fully Cleared</span>
           <strong class="text-success">{{ $stats['cleared_students'] }}</strong>
            </li>
            </ul>
             </div>
            </div>
        </div>
    </div>

    {{--RECENT ACTIVITY--}}
 <div class="card border-0 shadow-sm">
 <div class="card-header bg-white fw-semibold border-bottom">
    <i class="bi bi-activity me-2 text-navy"></i>Recent Activity
 </div>

 <div class="card-body p-0">
   @if($recentActivity->isEmpty())
  <div class="text-center text-muted py-4">No activity yet.</div>
  @else

 <div class="table-responsive">
   <table class="table table-hover mb-0 align-middle">
    <thead class="table-light">
     <tr>
    <th class="px-4">Student</th>
     <th>Department</th>
     <th>Status</th>
     <th>Comment</th>
    <th>Processed At</th>
     </tr>
   </thead>

<tbody>
 @foreach($recentActivity as $activity)
 <tr>
  <td class="px-4">
    <div class="fw-medium">{{ $activity->student->name }}</div>
    <div class="text-muted small">{{ $activity->student->registration_number }}</div>
    </td>

<td>{{ $activity->department->department_name }}</td>
 <td>
@if($activity->status === 'approved')
  <span class="badge bg-success">Approved</span>
 @else

  <span class="badge bg-danger">Rejected</span>
  @endif
    </td>
    
 <td class="text-muted small">{{ $activity->comment ?? '—' }}</td>
 <td class="text-muted small">{{ $activity->processed_at?->diffForHumans() }}</td>
   </tr>
  @endforeach
  </tbody>
   </table>
    </div>
      @endif
      </div>
    </div>
</div>
@endsection