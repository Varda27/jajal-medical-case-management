<?php

namespace App\Http\Controllers;

use App\Models\MedicalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller providing dynamic analytical counts and recent case feeds for the dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Display the application dashboard with dynamic counts.
     */
    public function index(): View
    {
        // Dynamic counts aggregated directly from the database
        $stats = [
            'total' => MedicalCase::count(),
            'planning' => MedicalCase::where('status', 'Planning')->count(),
            'in_review' => MedicalCase::where('status', 'In Review')->count(),
            'approved' => MedicalCase::where('status', 'Approved')->count(),
            'completed' => MedicalCase::where('status', 'Completed')->count(),
            'draft' => MedicalCase::where('status', 'Draft')->count(),
            'cancelled' => MedicalCase::where('status', 'Cancelled')->count(),
        ];

        // Retrieve latest 6 cases with creator relationship preloaded
        $recentCases = MedicalCase::with('creator')
            ->latest('id')
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'recentCases'));
    }
}
