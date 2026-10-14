<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

/**
 * Controller managing Web CRUD views and operations for MedicalCase records.
 */
class CaseController extends Controller
{
    /**
     * Display a paginated listing of cases with search and filtering.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MedicalCase::class);

        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');
        $priority = $request->input('priority', '');
        $sort = $request->input('sort', 'id');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = MedicalCase::with('creator');

        if (!empty($search)) {
            $query->search($search);
        }

        if (!empty($status)) {
            $query->filterStatus($status);
        }

        if (!empty($priority)) {
            $query->where('priority', $priority);
        }

        // Whitelist allowed sort columns to prevent SQL injection
        $allowedSorts = ['id', 'case_number', 'surgeon_name', 'status', 'priority', 'surgery_date', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('id');
        }

        // 10 cases per page, preserving query parameters across pagination links
        $cases = $query->paginate(10)->appends($request->query());

        return view('cases.index', compact('cases', 'search', 'status', 'priority', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new case (Admin only).
     */
    public function create(): View
    {
        $this->authorize('create', MedicalCase::class);

        $case = new MedicalCase();
        // Prefill default values for convenience
        $case->status = 'Draft';
        $case->priority = 'Medium';

        return view('cases.create', compact('case'));
    }

    /**
     * Store a newly created case in the database (Admin only).
     */
    public function store(StoreCaseRequest $request): RedirectResponse
    {
        $this->authorize('create', MedicalCase::class);

        $validated = $request->validated();
        // Automatically assign created_by to logged-in user to prevent spoofing
        $validated['created_by'] = Auth::id();

        $case = MedicalCase::create($validated);

        return redirect()->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} created successfully.");
    }

    /**
     * Display the specified case details.
     */
    public function show(MedicalCase $case): View
    {
        $this->authorize('view', $case);

        $case->load('creator');

        // Optional bonus feature: Nager.Date Public Holiday API near surgery date
        $holidays = [];
        if ($case->surgery_date) {
            $holidays = $this->fetchNearbyHolidays($case->surgery_date);
        }

        return view('cases.show', compact('case', 'holidays'));
    }

    /**
     * Show the form for editing the specified case (Admin only).
     */
    public function edit(MedicalCase $case): View
    {
        $this->authorize('update', $case);

        return view('cases.edit', compact('case'));
    }

    /**
     * Update the specified case in the database (Admin only).
     */
    public function update(UpdateCaseRequest $request, MedicalCase $case): RedirectResponse
    {
        $this->authorize('update', $case);

        $validated = $request->validated();
        $case->update($validated);

        return redirect()->route('cases.show', $case)
            ->with('success', "Case {$case->case_number} updated successfully.");
    }

    /**
     * Remove the specified case from the database (Admin only).
     */
    public function destroy(MedicalCase $case): RedirectResponse
    {
        $this->authorize('delete', $case);

        $caseNumber = $case->case_number;
        $case->delete();

        return redirect()->route('cases.index')
            ->with('success', "Case {$caseNumber} deleted successfully.");
    }

    /**
     * Helper to fetch holidays near surgery date with timeout and graceful fallback (Bonus feature).
     */
    private function fetchNearbyHolidays(string $surgeryDate): array
    {
        try {
            $year = date('Y', strtotime($surgeryDate));
            $baseUrl = config('services.holidays.url', env('HOLIDAY_API_URL', 'https://date.nager.at/api/v3'));
            $timeout = (int) config('services.holidays.timeout', env('HOLIDAY_API_TIMEOUT', 3));

            $response = Http::timeout($timeout)->get("{$baseUrl}/PublicHolidays/{$year}/US");

            if ($response->successful()) {
                $allHolidays = $response->json();
                $targetTime = strtotime($surgeryDate);

                // Filter holidays within +/- 14 days of surgery
                return array_filter($allHolidays, function ($h) use ($targetTime) {
                    $holidayTime = strtotime($h['date']);
                    $diffDays = abs(($holidayTime - $targetTime) / 86400);
                    return $diffDays <= 14;
                });
            }
        } catch (\Throwable $e) {
            // Gracefully ignore API failures so the medical case view never breaks
        }

        return [];
    }
}
