@extends('layouts.app')

@section('title', 'Edit Case ' . $case->case_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cases.index') }}" class="text-decoration-none">Cases</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit {{ $case->case_number }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-dark mb-0">Edit Medical Case</h3>
                <small class="text-muted">Updating record ID #{{ $case->id }} ({{ $case->case_number }})</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-eye me-1"></i> View Details
                </a>
                <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Cases
                </a>
            </div>
        </div>

        <div class="card card-custom p-4">
            <form action="{{ route('cases.update', $case) }}" method="POST">
                @csrf
                @method('PUT')

                @include('cases._form', ['case' => $case])

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Update Case Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
