<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test Suite for Medical Case Management Web CRUD and Role Authorization.
 */
class CaseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 3: An unauthenticated guest cannot access case pages and is redirected to login.
     */
    public function test_unauthenticated_guest_cannot_access_cases(): void
    {
        $response = $this->get('/cases');
        $response->assertRedirect('/login');

        $responseDashboard = $this->get('/dashboard');
        $responseDashboard->assertRedirect('/login');
    }

    /**
     * Test 4: An Admin can create a case with automated creator attribution.
     */
    public function test_admin_can_create_a_case(): void
    {
        $admin = User::factory()->admin()->create();

        $casePayload = [
            'case_number' => 'CASE-5544',
            'patient_reference' => 'REF-99123',
            'surgeon_name' => 'Dr. Robert Oppenheimer, MD',
            'implant_type' => 'Custom 3D Titanium Reconstruction Mesh',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-20',
        ];

        $response = $this->actingAs($admin)->post('/cases', $casePayload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-5544',
            'patient_reference' => 'REF-99123',
            'surgeon_name' => 'Dr. Robert Oppenheimer, MD',
            'created_by' => $admin->id, // Creator automatically set
        ]);
    }

    /**
     * Test: Case number uniqueness is enforced on creation.
     */
    public function test_case_number_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        MedicalCase::factory()->create([
            'case_number' => 'CASE-DUPLICATE-01',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post('/cases', [
            'case_number' => 'CASE-DUPLICATE-01',
            'patient_reference' => 'REF-48201',
            'surgeon_name' => 'Dr. Elena Rostova',
            'implant_type' => 'Knee Implant',
            'status' => 'Draft',
            'priority' => 'Low',
        ]);

        $response->assertSessionHasErrors('case_number');
    }

    /**
     * Test 6: A Viewer cannot delete a case and receives HTTP 403 Forbidden.
     */
    public function test_viewer_cannot_delete_case_and_receives_403(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create([
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($viewer)->delete("/cases/{$case->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
        ]);
    }

    /**
     * Test: Admin can delete a case successfully.
     */
    public function test_admin_can_delete_a_case(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create([
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/cases/{$case->id}");

        $response->assertRedirect('/cases');
        $this->assertDatabaseMissing('cases', [
            'id' => $case->id,
        ]);
    }

    /**
     * Test: Viewer can view cases list and case details without editing rights.
     */
    public function test_viewer_can_view_cases_but_cannot_see_create_page(): void
    {
        $viewer = User::factory()->viewer()->create();
        $case = MedicalCase::factory()->create();

        // Viewer can see index
        $responseIndex = $this->actingAs($viewer)->get('/cases');
        $responseIndex->assertStatus(200);

        // Viewer can see single case details
        $responseShow = $this->actingAs($viewer)->get("/cases/{$case->id}");
        $responseShow->assertStatus(200);

        // Viewer cannot access create form (returns 403)
        $responseCreate = $this->actingAs($viewer)->get('/cases/create');
        $responseCreate->assertStatus(403);
    }
}
