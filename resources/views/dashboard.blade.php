@extends('layouts.app')

@section('title', 'Clinical Operations Dashboard')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">Clinical Operations Dashboard</h3>
        <p class="text-muted mb-0">Implant procedure pipeline and status metrics</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        @can('create', App\Models\MedicalCase::class)
            <a href="{{ route('cases.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-plus-circle"></i> Create New Case
            </a>
        @endcan
        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-list-columns"></i> View All Cases
        </a>
    </div>
</div>

<!-- Dynamic Analytic Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Cases</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded">
                    <i class="bi bi-stack fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index') }}" class="stretched-link small text-primary text-decoration-none mt-2 d-inline-block">All records &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Planning</div>
                    <div class="fs-3 fw-bold text-info mt-1">{{ number_format($stats['planning']) }}</div>
                </div>
                <div class="p-2 bg-info bg-opacity-10 text-info rounded">
                    <i class="bi bi-clipboard2-pulse fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Planning']) }}" class="stretched-link small text-info text-decoration-none mt-2 d-inline-block">Filter planning &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">In Review</div>
                    <div class="fs-3 fw-bold text-warning mt-1">{{ number_format($stats['in_review']) }}</div>
                </div>
                <div class="p-2 bg-warning bg-opacity-10 text-warning rounded">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'In Review']) }}" class="stretched-link small text-warning text-decoration-none mt-2 d-inline-block">Filter review &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Approved</div>
                    <div class="fs-3 fw-bold text-success mt-1">{{ number_format($stats['approved']) }}</div>
                </div>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded">
                    <i class="bi bi-patch-check fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Approved']) }}" class="stretched-link small text-success text-decoration-none mt-2 d-inline-block">Filter approved &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-emerald h-100" style="border-left-color: #059669 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Completed</div>
                    <div class="fs-3 fw-bold text-emerald mt-1" style="color: #059669;">{{ number_format($stats['completed']) }}</div>
                </div>
                <div class="p-2 bg-success bg-opacity-10 text-success rounded">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Completed']) }}" class="stretched-link small text-success text-decoration-none mt-2 d-inline-block">Filter completed &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-2 col-lg-4">
        <div class="card card-custom p-3 border-start border-4 border-secondary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Draft / Other</div>
                    <div class="fs-3 fw-bold text-secondary mt-1">{{ number_format($stats['draft'] + $stats['cancelled']) }}</div>
                </div>
                <div class="p-2 bg-secondary bg-opacity-10 text-secondary rounded">
                    <i class="bi bi-archive fs-4"></i>
                </div>
            </div>
            <a href="{{ route('cases.index', ['status' => 'Draft']) }}" class="stretched-link small text-secondary text-decoration-none mt-2 d-inline-block">Filter drafts &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Cases Pipeline Table -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Case Activity</h5>
            <small class="text-muted">Most recently registered implant cases across all surgeons</small>
        </div>
        <a href="{{ route('cases.index') }}" class="btn btn-sm btn-outline-primary">
            View All Cases <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Case #</th>
                    <th>Patient Ref</th>
                    <th>Surgeon Name</th>
                    <th>Implant Type</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Surgery Date</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCases as $case)
                    <tr>
                        <td class="ps-4 fw-semibold text-primary">
                            <a href="{{ route('cases.show', $case) }}" class="text-decoration-none fw-bold">{{ $case->case_number }}</a>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $case->patient_reference }}</span></td>
                        <td class="fw-medium text-dark">{{ $case->surgeon_name }}</td>
                        <td class="text-truncate" style="max-width: 200px;">{{ $case->implant_type }}</td>
                        <td>
                            @php
                                $statusSlug = strtolower(str_replace(' ', '-', $case->status));
                            @endphp
                            <span class="badge badge-status-{{ $statusSlug }} px-2 py-1 rounded-pill">
                                {{ $case->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $prioritySlug = strtolower($case->priority);
                            @endphp
                            <span class="badge badge-priority-{{ $prioritySlug }} px-2 py-1">
                                {{ $case->priority }}
                            </span>
                        </td>
                        <td>
                            {{ $case->surgery_date ? date('M d, Y', strtotime($case->surgery_date)) : '—' }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('cases.show', $case) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="View Details">
                                <i class="bi bi-eye"></i> Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                            No medical cases registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
