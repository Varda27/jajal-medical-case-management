@extends('layouts.app')

@section('title', 'Case Details - ' . $case->case_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Top Navigation & Action Buttons -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.index') }}" class="text-decoration-none">Cases</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $case->case_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold text-dark mb-0">{{ $case->case_number }}</h3>
                    @php
                        $statusSlug = strtolower(str_replace(' ', '-', $case->status));
                    @endphp
                    <span class="badge badge-status-{{ $statusSlug }} px-3 py-1 fs-6 rounded-pill">
                        {{ $case->status }}
                    </span>
                    @php
                        $prioritySlug = strtolower($case->priority);
                    @endphp
                    <span class="badge badge-priority-{{ $prioritySlug }} px-2 py-1">
                        {{ $case->priority }} Priority
                    </span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Case List
                </a>

                @can('update', $case)
                    <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Case
                    </a>
                @endcan

                @can('delete', $case)
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Case Deletion
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start py-3">
                                    <p>Are you sure you want to permanently delete case <strong>{{ $case->case_number }}</strong>?</p>
                                    <p class="text-danger small mb-0">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('cases.destroy', $case) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-1"></i> Delete Case
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Case Overview Cards -->
        <div class="row g-4">
            <!-- Main Clinical Data -->
            <div class="col-md-8">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">
                        <i class="bi bi-file-earmark-medical me-2 text-primary"></i>Surgical & Device Specifications
                    </h5>

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Internal ID</dt>
                        <dd class="col-sm-8 font-monospace fw-bold">#{{ $case->id }}</dd>

                        <dt class="col-sm-4 text-muted">Case Number</dt>
                        <dd class="col-sm-8 fw-bold text-primary">{{ $case->case_number }}</dd>

                        <dt class="col-sm-4 text-muted">Patient Reference</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                {{ $case->patient_reference }}
                            </span>
                            <small class="text-muted d-block mt-1">Anonymized dummy patient token</small>
                        </dd>

                        <dt class="col-sm-4 text-muted">Attending Surgeon</dt>
                        <dd class="col-sm-8 fw-semibold text-dark">{{ $case->surgeon_name }}</dd>

                        <dt class="col-sm-4 text-muted">Implant Device</dt>
                        <dd class="col-sm-8 text-dark">{{ $case->implant_type }}</dd>

                        <dt class="col-sm-4 text-muted">Current Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-status-{{ $statusSlug }} px-2 py-1 rounded-pill">
                                {{ $case->status }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Priority Level</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-priority-{{ $prioritySlug }} px-2 py-1">
                                {{ $case->priority }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Surgery Date</dt>
                        <dd class="col-sm-8">
                            @if($case->surgery_date)
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-calendar-check me-1 text-primary"></i>
                                    {{ date('F j, Y', strtotime($case->surgery_date)) }}
                                </span>
                            @else
                                <span class="text-muted fst-italic">Pending scheduling</span>
                            @endif
                        </dd>
                    </dl>
                </div>

                <!-- Nearby Public Holidays (Bonus Feature 3) -->
                @if(!empty($holidays))
                    <div class="card card-custom p-4 border-start border-4 border-info">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-calendar4-week text-info me-2"></i>Nearby Public Holidays (Nager.Date API)
                        </h6>
                        <p class="text-muted small mb-3">Notice of regional holidays close to the scheduled surgery date:</p>
                        <ul class="list-group list-group-flush small">
                            @foreach($holidays as $h)
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <span><strong>{{ $h['name'] }}</strong> ({{ $h['localName'] }})</span>
                                    <span class="badge bg-light text-dark border">{{ date('M d, Y', strtotime($h['date'])) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Audit Trail & Creator Metadata Sidebar -->
            <div class="col-md-4">
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-clock-history me-2 text-secondary"></i>Audit & Compliance
                    </h6>

                    <div class="mb-3">
                        <div class="text-muted small">Registered By</div>
                        <div class="fw-semibold text-dark mt-1">{{ $case->creator->name ?? 'System' }}</div>
                        <div class="text-muted small font-monospace">{{ $case->creator->email ?? 'N/A' }}</div>
                        <span class="badge bg-light text-secondary border mt-1 text-uppercase" style="font-size: 0.65rem;">
                            Role: {{ $case->creator->role ?? 'admin' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Created Timestamp</div>
                        <div class="text-dark small mt-1 font-monospace">
                            {{ $case->created_at ? $case->created_at->format('Y-m-d H:i:s T') : '—' }}
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted small">Last Modified Timestamp</div>
                        <div class="text-dark small mt-1 font-monospace">
                            {{ $case->updated_at ? $case->updated_at->format('Y-m-d H:i:s T') : '—' }}
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-3 bg-light">
                    <div class="d-flex align-items-center gap-2 text-secondary small">
                        <i class="bi bi-shield-check fs-4 text-success"></i>
                        <div>
                            <strong>HIPAA / Anonymization</strong><br>
                            Patient name is decoupled via token <code>{{ $case->patient_reference }}</code>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
