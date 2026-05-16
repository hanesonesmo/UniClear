{{-- Clearance Certificate / Report (Printable Page) --}}
{{-- This is a standalone print-ready page, not extending the app layout --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Certificate – {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }

        /* Print-specific styles */
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .certificate { box-shadow: none !important; }
        }

        .certificate {
            max-width: 750px;
            margin: 40px auto;
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-top: 8px solid #1a3a6b;
        }

        .seal {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #1a3a6b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
    </style>
</head>
<body>

<div class="certificate">

    {{-- Header --}}
    <div class="text-center mb-4">
        <div class="seal">🎓</div>
        <h1 class="h3 fw-bold" style="color: #1a3a6b;">CLEARANCE CERTIFICATE</h1>
        <p class="text-muted mb-0">SmartClear – University Student Clearance Management System</p>
        <hr class="my-3">
    </div>

    {{-- Student Info --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tr>
                    <td class="text-muted fw-medium">Student Name:</td>
                    <td class="fw-bold">{{ $student->name }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-medium">Registration No.:</td>
                    <td class="fw-bold">{{ $student->registration_number ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tr>
                    <td class="text-muted fw-medium">Department:</td>
                    <td class="fw-bold">{{ $student->department?->department_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted fw-medium">Date Issued:</td>
                    <td class="fw-bold">{{ now()->format('d F Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Clearance Statement --}}
    <div class="alert alert-success border-0 text-center mb-4" style="background: #d1fae5;">
        <strong>✓ This student has been fully cleared by all university departments.</strong>
    </div>

    {{-- Department Approvals Table --}}
    <table class="table table-bordered align-middle">
        <thead style="background: #1a3a6b; color: white;">
            <tr>
                <th>#</th>
                <th>Department</th>
                <th>Status</th>
                <th>Comment</th>
                <th>Date Cleared</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clearances as $index => $clearance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-medium">{{ $clearance->department->department_name }}</td>
                <td>
                    <span class="badge bg-success">✓ Approved</span>
                </td>
                <td class="text-muted small">{{ $clearance->comment ?? 'Cleared' }}</td>
                <td class="text-muted small">
                    {{ $clearance->processed_at?->format('d M Y') ?? 'N/A' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signature area --}}
    <div class="row mt-5">
        <div class="col-md-6 text-center">
            <div style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 40px;">
                <p class="mb-0 fw-medium">Student Signature</p>
                <p class="text-muted small">{{ $student->name }}</p>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 40px;">
                <p class="mb-0 fw-medium">Registrar's Signature</p>
                <p class="text-muted small">University Registrar</p>
            </div>
        </div>
    </div>

    {{-- Print / Back Buttons --}}
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-navy px-5 me-2">
            <i class="bi bi-printer me-2"></i>Print Certificate
        </button>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary px-4">
            Back to Dashboard
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>