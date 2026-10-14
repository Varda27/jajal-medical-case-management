<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MedicalCasePolicy
 *
 * Enforces role-based authorization rules for MedicalCase resources:
 * - Admin: Full permissions (view, create, update, delete, status change)
 * - Viewer: Read-only access (viewAny, view). Explicitly forbidden from mutating data.
 */
class MedicalCasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any medical cases.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'viewer']);
    }

    /**
     * Determine whether the user can view the specific medical case.
     */
    public function view(User $user, MedicalCase $case): bool
    {
        return in_array($user->role, ['admin', 'viewer']);
    }

    /**
     * Determine whether the user can create medical cases.
     * Only Admin can create cases.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the medical case.
     * Only Admin can edit or update cases.
     */
    public function update(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can change case status.
     * Only Admin can change status.
     */
    public function changeStatus(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the medical case.
     * Only Admin can delete cases. Viewers are forbidden (HTTP 403).
     */
    public function delete(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin();
    }
}
