<?php

namespace App\Http\Requests;

use App\Models\MedicalCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request validating updates to an existing medical case.
 * Enforces case_number uniqueness while ignoring the current case record ID.
 */
class UpdateCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Resolve case ID from route parameter ('case' or 'id')
        $caseParam = $this->route('case') ?? $this->route('id');
        $caseId = is_object($caseParam) ? $caseParam->id : $caseParam;

        return [
            'case_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cases', 'case_number')->ignore($caseId),
            ],
            'patient_reference' => [
                'required',
                'string',
                'max:100',
            ],
            'surgeon_name' => [
                'required',
                'string',
                'max:255',
            ],
            'implant_type' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(MedicalCase::STATUSES),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(MedicalCase::PRIORITIES),
            ],
            'surgery_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'case_number.required' => 'Case Number is required.',
            'case_number.unique' => 'Another medical case already uses this Case Number.',
            'patient_reference.required' => 'Patient Reference dummy code is required.',
            'surgeon_name.required' => 'Surgeon Name is required.',
            'implant_type.required' => 'Implant Type is required.',
            'status.required' => 'Status selection is required.',
            'status.in' => 'Invalid status selected.',
            'priority.required' => 'Priority selection is required.',
            'priority.in' => 'Invalid priority selected.',
            'surgery_date.date_format' => 'Surgery date must be a valid date in YYYY-MM-DD format.',
        ];
    }
}
