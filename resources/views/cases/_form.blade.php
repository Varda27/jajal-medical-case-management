<div class="row g-3">
    <!-- Case Number -->
    <div class="col-md-6">
        <label for="case_number" class="form-label fw-semibold text-secondary">
            Case Number <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
            <input type="text"
                   class="form-control @error('case_number') is-invalid @enderror"
                   id="case_number"
                   name="case_number"
                   value="{{ old('case_number', $case->case_number ?? '') }}"
                   placeholder="e.g. CASE-0042"
                   required>
        </div>
        <small class="form-text text-muted">Must be globally unique across all recorded cases.</small>
        @error('case_number')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Patient Reference (Dummy Code Only) -->
    <div class="col-md-6">
        <label for="patient_reference" class="form-label fw-semibold text-secondary">
            Patient Reference Code <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
            <input type="text"
                   class="form-control @error('patient_reference') is-invalid @enderror"
                   id="patient_reference"
                   name="patient_reference"
                   value="{{ old('patient_reference', $case->patient_reference ?? '') }}"
                   placeholder="e.g. REF-89214"
                   required>
        </div>
        <small class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Privacy constraint: Never enter real patient names.</small>
        @error('patient_reference')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Surgeon Name -->
    <div class="col-md-6">
        <label for="surgeon_name" class="form-label fw-semibold text-secondary">
            Attending Surgeon Name <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
            <input type="text"
                   class="form-control @error('surgeon_name') is-invalid @enderror"
                   id="surgeon_name"
                   name="surgeon_name"
                   value="{{ old('surgeon_name', $case->surgeon_name ?? '') }}"
                   placeholder="e.g. Dr. Robert Chen, MD"
                   required>
        </div>
        @error('surgeon_name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Implant Type -->
    <div class="col-md-6">
        <label for="implant_type" class="form-label fw-semibold text-secondary">
            Implant Device Specification <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-tools"></i></span>
            <input type="text"
                   class="form-control @error('implant_type') is-invalid @enderror"
                   id="implant_type"
                   name="implant_type"
                   value="{{ old('implant_type', $case->implant_type ?? '') }}"
                   placeholder="e.g. Total Hip Arthroplasty (Ceramic-on-Polyethylene)"
                   required>
        </div>
        @error('implant_type')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Case Status -->
    <div class="col-md-4">
        <label for="status" class="form-label fw-semibold text-secondary">
            Case Status <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="" disabled>-- Select Case Status --</option>
            @foreach(\App\Models\MedicalCase::STATUSES as $statusOption)
                <option value="{{ $statusOption }}" {{ old('status', $case->status ?? 'Draft') === $statusOption ? 'selected' : '' }}>
                    {{ $statusOption }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Priority -->
    <div class="col-md-4">
        <label for="priority" class="form-label fw-semibold text-secondary">
            Clinical Priority <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
            <option value="" disabled>-- Select Priority --</option>
            @foreach(\App\Models\MedicalCase::PRIORITIES as $priorityOption)
                <option value="{{ $priorityOption }}" {{ old('priority', $case->priority ?? 'Medium') === $priorityOption ? 'selected' : '' }}>
                    {{ $priorityOption }} Priority
                </option>
            @endforeach
        </select>
        @error('priority')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Surgery Date (Optional) -->
    <div class="col-md-4">
        <label for="surgery_date" class="form-label fw-semibold text-secondary">
            Planned Surgery Date <span class="text-muted fw-normal">(Optional)</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
            <input type="date"
                   class="form-control @error('surgery_date') is-invalid @enderror"
                   id="surgery_date"
                   name="surgery_date"
                   value="{{ old('surgery_date', isset($case->surgery_date) ? (is_string($case->surgery_date) ? substr($case->surgery_date, 0, 10) : $case->surgery_date->format('Y-m-d')) : '') }}">
        </div>
        <small class="form-text text-muted">Leave empty if date is unconfirmed.</small>
        @error('surgery_date')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>
