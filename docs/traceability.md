# Requirement Traceability Matrix (RTM)

## Jajal Medical Case Management System

This document maps requirements to their specific implementation files/methods and their corresponding automated verification tests.

---

## 1. Traceability Matrix Table

| Requirement ID | Requirement Description | Implementation Location(s) | Automated Test File & Method | Verification Status |
|----------------|-------------------------|----------------------------|------------------------------|---------------------|
| **REQ-001** | Admin can create a medical case. Creator is automatically set to authenticated user. | • `app/Http/Controllers/CaseController.php` (`store`)<br>• `app/Http/Controllers/Api/CaseApiController.php` (`store`)<br>• `app/Http/Requests/StoreCaseRequest.php`<br>• `app/Policies/MedicalCasePolicy.php` (`create`) | • `tests/Feature/CaseManagementTest.php` (`test_admin_can_create_a_case`)<br>• `tests/Feature/ApiCaseTest.php` (`test_api_admin_can_create_case`) | **PASS** |
| **REQ-002** | Case Number must be required and unique across all cases (enforced in validation and database). | • `database/migrations/2026_01_01_000001_create_cases_table.php` (`$table->unique('case_number')`)<br>• `app/Http/Requests/StoreCaseRequest.php` (`'unique:cases,case_number'`)<br>• `app/Http/Requests/UpdateCaseRequest.php` (`Rule::unique('cases')->ignore($caseId)`) | • `tests/Feature/CaseManagementTest.php` (`test_case_number_must_be_unique`)<br>• `tests/Feature/ApiCaseTest.php` (`test_creating_case_with_duplicate_case_number_fails_with_422`) | **PASS** |
| **REQ-003** | Only Admin users can delete cases; Viewers receive HTTP 403 Forbidden. | • `app/Policies/MedicalCasePolicy.php` (`delete`: checks `$user->isAdmin()`)<br>• `app/Http/Controllers/CaseController.php` (`destroy`: `$this->authorize('delete', $case)`)<br>• `app/Http/Controllers/Api/CaseApiController.php` (`destroy`) | • `tests/Feature/CaseManagementTest.php` (`test_viewer_cannot_delete_case_and_receives_403`)<br>• `tests/Feature/ApiCaseTest.php` (`test_api_viewer_cannot_delete_case`) | **PASS** |
| **REQ-004** | Valid login succeeds and authenticates the user into the system. | • `app/Http/Controllers/AuthController.php` (`login`)<br>• `app/Http/Controllers/Api/AuthController.php` (`login`)<br>• `app/Http/Requests/LoginRequest.php` | • `tests/Feature/AuthenticationTest.php` (`test_valid_login_succeeds`)<br>• `tests/Feature/AuthenticationTest.php` (`test_api_login_returns_sanctum_token`) | **PASS** |
| **REQ-005** | Invalid login is rejected with clear error messages and 401 status. | • `app/Http/Controllers/AuthController.php` (`login`)<br>• `app/Http/Controllers/Api/AuthController.php` (`login`) | • `tests/Feature/AuthenticationTest.php` (`test_invalid_login_is_rejected`)<br>• `tests/Feature/AuthenticationTest.php` (`test_api_login_with_invalid_credentials_returns_401`) | **PASS** |
| **REQ-006** | Unauthenticated guests cannot access case pages or protected APIs. | • `routes/web.php` (wrapped in `auth` middleware)<br>• `routes/api.php` (wrapped in `auth:sanctum` middleware) | • `tests/Feature/CaseManagementTest.php` (`test_unauthenticated_guest_cannot_access_cases`)<br>• `tests/Feature/ApiCaseTest.php` (`test_unauthenticated_request_to_cases_api_is_rejected_with_401`) | **PASS** |

---

## 2. Detailed Verification Evidence

### REQ-001: Admin Can Create a Case
- **Controller Logic**:
  ```php
  // app/Http/Controllers/CaseController.php
  public function store(StoreCaseRequest $request)
  {
      $this->authorize('create', MedicalCase::class);
      $data = $request->validated();
      $data['created_by'] = auth()->id();
      $case = MedicalCase::create($data);
      return redirect()->route('cases.show', $case)->with('success', 'Case created successfully.');
  }
  ```
- **Automated Test**:
  ```php
  // tests/Feature/CaseManagementTest.php
  public function test_admin_can_create_a_case()
  {
      $admin = User::factory()->admin()->create();
      $response = $this->actingAs($admin)->post('/cases', [
          'case_number' => 'CASE-9999',
          'patient_reference' => 'REF-99999',
          'surgeon_name' => 'Dr. Eleanor Vance',
          'implant_type' => 'Cervical Disc Prosthesis',
          'status' => 'Planning',
          'priority' => 'High',
          'surgery_date' => '2026-11-20',
      ]);
      $response->assertRedirect();
      $this->assertDatabaseHas('cases', [
          'case_number' => 'CASE-9999',
          'created_by' => $admin->id,
      ]);
  }
  ```

### REQ-002: Case Number Must Be Unique
- **Rule Enforcement**:
  ```php
  // app/Http/Requests/StoreCaseRequest.php
  'case_number' => ['required', 'string', 'max:50', 'unique:cases,case_number'],
  ```
- **Automated Test**:
  ```php
  // tests/Feature/ApiCaseTest.php
  public function test_creating_case_with_duplicate_case_number_fails_with_422()
  {
      $admin = User::factory()->admin()->create();
      $existing = MedicalCase::factory()->create(['case_number' => 'CASE-DUPLICATE']);
      $token = $admin->createToken('test')->plainTextToken;

      $response = $this->withHeader('Authorization', "Bearer {$token}")
          ->postJson('/api/cases', [
              'case_number' => 'CASE-DUPLICATE',
              'patient_reference' => 'REF-12345',
              'surgeon_name' => 'Dr. House',
              'implant_type' => 'Knee Implant',
              'status' => 'Planning',
              'priority' => 'Medium',
          ]);

      $response->assertStatus(422);
      $response->assertJsonValidationErrors(['case_number']);
  }
  ```

### REQ-003: Only Admin Can Delete Cases (Viewer Receives 403)
- **Policy Enforcement**:
  ```php
  // app/Policies/MedicalCasePolicy.php
  public function delete(User $user, MedicalCase $case): bool
  {
      return $user->role === 'admin';
  }
  ```
- **Automated Test**:
  ```php
  // tests/Feature/CaseManagementTest.php
  public function test_viewer_cannot_delete_case_and_receives_403()
  {
      $viewer = User::factory()->viewer()->create();
      $case = MedicalCase::factory()->create();

      $response = $this->actingAs($viewer)->delete("/cases/{$case->id}");
      $response->assertStatus(403);
      $this->assertDatabaseHas('cases', ['id' => $case->id]);
  }
  ```
