@extends('layouts.app')

@section('title', 'Medical Cases Directory')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">Medical Cases Directory</h3>
        <p class="text-muted mb-0">Browse, filter, and track orthopedic and implant surgical cases</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        @can('create', App\Models\MedicalCase::class)
            <a href="{{ route('cases.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Register New Case
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filtering Toolbar -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('cases.index') }}" class="row g-2 align-items-center">
        <!-- Search input -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text"
                       class="form-control border-start-0"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search Case Number or Surgeon Name...">
            </div>
        </div>

        <!-- Status filter -->
        <div class="col-md-3">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(\App\Models\MedicalCase::STATUSES as $opt)
                    <option value="{{ $opt }}" {{ $status === $opt ? 'selected' : '' }}>
                        Status: {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Priority filter (Bonus feature 2) -->
        <div class="col-md-3">
            <select class="form-select" name="priority" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                @foreach(\App\Models\MedicalCase::PRIORITIES as $pOpt)
                    <option value="{{ $pOpt }}" {{ $priority === $pOpt ? 'selected' : '' }}>
                        Priority: {{ $pOpt }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Actions -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary w-100">
                Filter
            </button>
            @if(!empty($search) || !empty($status) || !empty($priority))
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Cases Table -->
<div class="card card-custom mb-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">
                        <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'case_number', 'direction' => ($sort === 'case_number' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                            Case Number
                            @if($sort === 'case_number')
                                <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Patient Ref</th>
                    <th>
                        <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'surgeon_name', 'direction' => ($sort === 'surgeon_name' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                            Surgeon Name
                            @if($sort === 'surgeon_name')
                                <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Implant Type</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>
                        <a href="{{ route('cases.index', array_merge(request()->query(), ['sort' => 'surgery_date', 'direction' => ($sort === 'surgery_date' && $direction === 'asc') ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                            Surgery Date
                            @if($sort === 'surgery_date')
                                <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Created By</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $item)
                    <tr>
                        <td class="ps-4 fw-semibold text-primary">
                            <a href="{{ route('cases.show', $item) }}" class="text-decoration-none fw-bold">
                                {{ $item->case_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $item->patient_reference }}</span>
                        </td>
                        <td class="fw-medium text-dark">{{ $item->surgeon_name }}</td>
                        <td class="text-truncate" style="max-width: 220px;" title="{{ $item->implant_type }}">
                            {{ $item->implant_type }}
                        </td>
                        <td>
                            @php
                                $statusSlug = strtolower(str_replace(' ', '-', $item->status));
                            @endphp
                            <span class="badge badge-status-{{ $statusSlug }} px-2 py-1 rounded-pill">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $prioritySlug = strtolower($item->priority);
                            @endphp
                            <span class="badge badge-priority-{{ $prioritySlug }} px-2 py-1">
                                {{ $item->priority }}
                            </span>
                        </td>
                        <td>
                            {{ $item->surgery_date ? date('M d, Y', strtotime($item->surgery_date)) : '—' }}
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->creator->name ?? 'System' }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('cases.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @can('update', $item)
                                    <a href="{{ route('cases.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit Case">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @can('delete', $item)
                                    <!-- Delete Modal Trigger Button -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" title="Delete Case">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Delete Confirmation Modal Dialog -->
                                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Case Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start py-3">
                                                    <p class="mb-1">Are you sure you want to permanently delete medical case <strong>{{ $item->case_number }}</strong>?</p>
                                                    <p class="text-muted small mb-0">Implant: {{ $item->implant_type }}<br>Surgeon: {{ $item->surgeon_name }}</p>
                                                    <p class="text-danger small mt-2 mb-0"><strong>Warning:</strong> This operation is permanent and cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('cases.destroy', $item) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash me-1"></i> Delete Permanently
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                                <h5 class="fw-semibold text-secondary">No Matching Medical Cases Found</h5>
                                <p class="text-muted small mb-3">We couldn't find any records matching your search or filter criteria.</p>
                                <a href="{{ route('cases.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Results Counter -->
    @if($cases->total() > 0)
        <div class="p-3 border-top bg-light d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="text-muted small">
                Showing <strong>{{ $cases->firstItem() }}</strong> to <strong>{{ $cases->lastItem() }}</strong> of <strong>{{ $cases->total() }}</strong> total medical cases
            </div>
            <div>
                {{ $cases->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
