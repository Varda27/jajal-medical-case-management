<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST API Controller for Medical Cases protected by Laravel Sanctum.
 */
class CaseApiController extends Controller
{
    /**
     * Retrieve paginated and filtered list of medical cases.
     * Accessible to both Admin and Viewer roles.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MedicalCase::class);

        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');
        $priority = $request->input('priority', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = MedicalCase::with('creator:id,name,email,role');

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($status)) {
            $query->filterStatus($status);
        }

        if (!empty($priority)) {
            $query->where('priority', $priority);
        }

        $cases = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $cases,
            'message' => 'Cases retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created case (Admin only).
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        // Enforce policy authorization (returns 403 if forbidden)
        $this->authorize('create', MedicalCase::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $case = MedicalCase::create($data);
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Medical case created successfully.',
        ], 201);
    }

    /**
     * Retrieve details for a single case.
     * Accessible to both Admin and Viewer.
     */
    public function show(int $id): JsonResponse
    {
        $case = MedicalCase::with('creator:id,name,email,role')->find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Medical case with ID {$id} not found.",
            ], 404);
        }

        $this->authorize('view', $case);

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Case details retrieved successfully.',
        ], 200);
    }

    /**
     * Update an existing case (Admin only).
     */
    public function update(UpdateCaseRequest $request, int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Medical case with ID {$id} not found.",
            ], 404);
        }

        $this->authorize('update', $case);

        $case->update($request->validated());
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'data' => $case,
            'message' => 'Medical case updated successfully.',
        ], 200);
    }

    /**
     * Delete an existing case (Admin only).
     * Returns 403 when Viewer attempts this action.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Medical case with ID {$id} not found.",
            ], 404);
        }

        $this->authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return response()->json([
            'success' => true,
            'data' => [
                'deleted_id' => $id,
                'case_number' => $caseNumber,
            ],
            'message' => "Case {$caseNumber} deleted successfully.",
        ], 200);
    }
}
