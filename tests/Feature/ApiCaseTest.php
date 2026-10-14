<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Test Suite for REST API Endpoints with Laravel Sanctum Authentication.
 */
class ApiCaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated request to cases API is rejected with 401 Unauthorized.
     */
    public function test_unauthenticated_request_to_cases_api_is_rejected_with_401(): void
    {
        $response = $this->getJson('/api/cases');
        $response->assertStatus(401);
    }

    /**
     * Authenticated Admin can create a new case via REST API (201 Created).
     */
    public function test_api_admin_can_create_case(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin-test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', [
                'case_number' => 'CASE-API-101',
                'patient_reference' => 'REF-API-99',
                'surgeon_name' => 'Dr. Lisa Cuddy, MD',
                'implant_type' => 'Custom Titanium Hip Cup',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => '2026-11-30',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Medical case created successfully.',
            ])
            ->assertJsonPath('data.case_number', 'CASE-API-101')
            ->assertJsonPath('data.created_by', $admin->id);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-API-101',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Test 5: Creating a case with duplicate case number fails with HTTP 422 Unprocessable Content.
     */
    public function test_creating_case_with_duplicate_case_number_fails_with_422(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin-test-token')->plainTextToken;

        MedicalCase::factory()->create([
            'case_number' => 'CASE-DUPLICATE-API',
            'created_by' => $admin->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', [
                'case_number' => 'CASE-DUPLICATE-API',
                'patient_reference' => 'REF-API-12',
                'surgeon_name' => 'Dr. Eric Foreman',
                'implant_type' => 'Spinal Cage',
                'status' => 'Draft',
                'priority' => 'Low',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['case_number']);
    }

    /**
     * Test: Viewer cannot delete a case via API and receives HTTP 403 Forbidden.
     */
    public function test_api_viewer_cannot_delete_case(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create(['created_by' => $admin->id]);
        $token = $viewer->createToken('viewer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Test: Viewer cannot create a case via API and receives HTTP 403 Forbidden.
     */
    public function test_api_viewer_cannot_create_case(): void
    {
        $viewer = User::factory()->viewer()->create();
        $token = $viewer->createToken('viewer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', [
                'case_number' => 'CASE-FORBIDDEN-01',
                'patient_reference' => 'REF-001',
                'surgeon_name' => 'Dr. Unauthorized',
                'implant_type' => 'Plate',
                'status' => 'Draft',
                'priority' => 'Low',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test: Authenticated user can view single case details via API (200 OK).
     */
    public function test_api_authenticated_user_can_view_case_details(): void
    {
        $viewer = User::factory()->viewer()->create();
        $case = MedicalCase::factory()->create();
        $token = $viewer->createToken('viewer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/cases/{$case->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.id', $case->id)
            ->assertJsonPath('data.case_number', $case->case_number);
    }

    /**
     * Test: Requesting non-existent case returns 404 Not Found.
     */
    public function test_api_non_existent_case_returns_404(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/cases/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
