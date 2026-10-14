# Jajal Medical Case Management Web Application

> **Software Developer Intern Practical Assignment — Jajal Medical**  
> An enterprise medical case management system for tracking orthopedic implant procedures from initial draft to surgical completion, featuring role-based authorization, Laravel Sanctum REST APIs, automated PHPUnit feature testing, and an interactive browser dashboard.

---

## 1. Project Overview & Features

This application tracks patient implant cases through clinical and regulatory lifecycles for a medical device manufacturer. The system enforces strict role-based access control (RBAC), prevents unauthorized mutation of surgical records, adheres to data privacy standards (using dummy patient reference codes, never real patient names), and exposes authenticated REST endpoints.

### Key Features
- **Role-Based Access Control (RBAC)**:
  - **Admin**: Full permissions (create, view, edit, update case status, delete cases with confirmation dialogs, view dashboard metrics).
  - **Viewer**: Read-only permissions (dashboard access, search, filter, view case details; mutative actions are strictly rejected with HTTP 403 Forbidden).
- **Interactive Operations Dashboard**:
  - Dynamically calculated metric cards directly from MySQL: Total Cases, Planning, In Review, Approved, Completed, and Draft.
  - Recent case activity table with one-click navigation to details.
- **Comprehensive Case Management (CRUD)**:
  - Paginated table (10 cases/page) preserving filter queries across navigation.
  - Search by Case Number or Attending Surgeon Name.
  - Status and Priority filtering with clean badges.
  - Reusable form partial with field-level validation and old-input retention.
  - Uniqueness enforcement for `case_number` with update-rule exclusions.
  - Creator user ID automatically attributed from authenticated session.
- **RESTful API via Laravel Sanctum**:
  - Token issuance via `/api/login` and revocation via `/api/logout`.
  - Protected endpoints for case listing, filtering, creation, retrieval, updating, and deletion.
  - Consistent JSON envelope with standard HTTP status codes (`200`, `201`, `401`, `403`, `404`, `422`).
- **External Public Holiday Grounding (Bonus Feature)**:
  - Integration with the Nager.Date Public Holiday API near scheduled surgery dates with configurable timeouts and graceful degradation.
- **Automated Test Coverage**:
  - 6+ PHPUnit feature tests covering authentication, guest restrictions, authorization policies, and duplicate validation.

---

## 2. Technology Stack & Prerequisites

| Component | Technology | Compatible Version |
|-----------|------------|--------------------|
| **Backend Framework** | PHP / Laravel | PHP >= 8.2, Laravel 11.x (or 10.x) |
| **Package Manager** | Composer | Composer >= 2.6 |
| **Relational Database** | MySQL (or MariaDB) | MySQL >= 8.0 (SQLite in-memory for testing) |
| **Frontend UI** | Blade, HTML5, CSS3, JavaScript, Bootstrap 5 | Bootstrap 5.3+ |
| **API Token Security** | Laravel Sanctum | Sanctum 4.x |
| **Automated Testing** | PHPUnit | PHPUnit >= 10.x / 11.x |
| **Development Editor** | Visual Studio Code | Recommended with PHP Intelephense |

---

## 3. Dummy Login Credentials

> **Important**: In accordance with security requirements, credentials are never hardcoded in production source files. They are seeded via `database/seeders/UserSeeder.php` using secure Bcrypt hashing.

| Role | Email Address | Password | Permissions |
|------|---------------|----------|-------------|
| **Admin** | `admin@jajalmedical.com` | `Admin@12345` | Full CRUD, Status Changes, Deletion, Dashboard |
| **Viewer** | `viewer@jajalmedical.com` | `Viewer@12345` | Read-only viewing, Search, Filter, Details |

---

## 4. Step-by-Step Local Setup Guide (Windows & VS Code)

### Step 1: Install & Verify Prerequisites
1. **PHP 8.2+**:
   - Download PHP for Windows from [windows.php.net](https://windows.php.net/download/).
   - Add the PHP directory to your Windows System `PATH` environment variable.
   - In `php.ini`, ensure the following extensions are enabled:
     ```ini
     extension=curl
     extension=fileinfo
     extension=mbstring
     extension=openssl
     extension=pdo_mysql
     extension=pdo_sqlite
     extension=sqlite3
     ```
   - Verify in PowerShell or Command Prompt:
     ```powershell
     php -v
     ```
2. **Composer**:
   - Download and run the Windows installer from [getcomposer.org](https://getcomposer.org/Composer-Setup.exe).
   - Verify:
     ```powershell
     composer -v
     ```
3. **MySQL**:
   - Install MySQL Server directly or use **XAMPP** / **Laragon**.
   - Start the MySQL service on port `3306`.

---

### Step 2: Clone or Open the Project in VS Code
1. Open VS Code.
2. Select **File > Open Folder...** and select this project folder.
3. Open the integrated terminal (`Ctrl + ~` or **Terminal > New Terminal**).

---

### Step 3: Configure Database in MySQL
Open your MySQL CLI (or phpMyAdmin / MySQL Workbench):
```sql
CREATE DATABASE IF NOT EXISTS jajal_medical_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### Step 4: Configure Environment File (`.env`)
1. Duplicate `.env.example` to create `.env`:
   ```powershell
   copy .env.example .env
   ```
2. Open `.env` and verify your MySQL database credentials:
   ```env
   APP_NAME="Jajal Medical Case Management"
   APP_ENV="local"
   APP_KEY=
   APP_DEBUG=true
   APP_URL="http://127.0.0.1:8000"

   DB_CONNECTION="mysql"
   DB_HOST="127.0.0.1"
   DB_PORT="3306"
   DB_DATABASE="jajal_medical_db"
   DB_USERNAME="root"
   DB_PASSWORD=""

   HOLIDAY_API_URL="https://date.nager.at/api/v3"
   HOLIDAY_API_TIMEOUT=3
   ```

---

### Step 5: Install Dependencies & Initialize Database
Run the following commands in the VS Code terminal:
```powershell
# 1. Install PHP Composer dependencies
composer install

# 2. Generate unique application encryption key
php artisan key:generate

# 3. Run database migrations and seed default Admin, Viewer, and 22 dummy cases
php artisan migrate:fresh --seed
```

---

### Step 6: Start the Application
Launch the local PHP development server:
```powershell
php artisan serve
```
Open your browser and navigate to:
```
http://127.0.0.1:8000
```
- Sign in with **`admin@jajalmedical.com`** / **`Admin@12345`** to test full CRUD.
- Log out and sign in with **`viewer@jajalmedical.com`** / **`Viewer@12345`** to verify restricted access.

---

## 5. Running Automated Feature Tests

The test suite runs against an isolated, in-memory SQLite database (`:memory:`) configured in `phpunit.xml` to protect real application and development data.

Run the full automated test suite:
```powershell
php artisan test
```
Or directly via PHPUnit:
```powershell
./vendor/bin/phpunit
```

### Verified Test Cases
1. `AuthenticationTest::test_valid_login_succeeds` — Valid credentials authenticate into session.
2. `AuthenticationTest::test_invalid_login_is_rejected` — Invalid credentials rejected with validation errors.
3. `CaseManagementTest::test_unauthenticated_guest_cannot_access_cases` — Guests redirected to login.
4. `CaseManagementTest::test_admin_can_create_a_case` — Admin creates case; `created_by` set automatically.
5. `ApiCaseTest::test_creating_case_with_duplicate_case_number_fails_with_422` — Duplicate case number rejected with HTTP 422.
6. `CaseManagementTest::test_viewer_cannot_delete_case_and_receives_403` — Viewer deletion rejected with HTTP 403.
7. `ApiCaseTest::test_api_login_returns_sanctum_token` — Sanctum token issued upon authentication.
8. `ApiCaseTest::test_api_viewer_cannot_create_case` — Viewer blocked from API case mutations.

---

## 6. REST API Endpoints & Testing

All private endpoints require the `Authorization: Bearer <TOKEN>` header.

| Method | Endpoint | Access Level | Description | Status Codes |
|--------|----------|--------------|-------------|--------------|
| `POST` | `/api/login` | Public | Authenticates and returns Sanctum Bearer token | `200`, `401`, `422` |
| `POST` | `/api/logout` | Authenticated | Revokes current access token | `200`, `401` |
| `GET` | `/api/cases` | Admin & Viewer | Paginated case list with search & status filters | `200`, `401` |
| `POST` | `/api/cases` | Admin Only | Creates a new implant case | `201`, `401`, `403`, `422` |
| `GET` | `/api/cases/{id}`| Admin & Viewer | Retrieves full details of a specific case | `200`, `401`, `404` |
| `PUT` | `/api/cases/{id}`| Admin Only | Updates existing case | `200`, `401`, `403`, `404`, `422`|
| `DELETE`| `/api/cases/{id}`| Admin Only | Deletes case (Viewer receives 403) | `200`, `401`, `403`, `404` |

### Sample JSON Envelope
```json
{
  "success": true,
  "data": {
    "id": 1,
    "case_number": "CASE-0001",
    "patient_reference": "REF-92810",
    "surgeon_name": "Dr. Marcus Vance, MD",
    "implant_type": "Ceramic Total Hip Arthroplasty",
    "status": "Planning",
    "priority": "High",
    "surgery_date": "2026-10-28",
    "created_by": 1
  },
  "message": "Medical case created successfully."
}
```

### Testing with Postman or Thunder Client
A pre-configured collection is included at:
`docs/postman_collection.json`

1. Open Postman or VS Code Thunder Client extension.
2. Click **Import** > select `docs/postman_collection.json`.
3. Run `1. Authentication > Login as Admin`. Copy the returned `token`.
4. Set the `token` collection variable and test all CRUD endpoints.

---

## 7. Security & Compliance Notes

1. **Authentication Middleware**: All web routes and API routes are strictly protected by `auth` and `auth:sanctum` middleware.
2. **Server-Side Authorization**: Enforced via `App\Policies\MedicalCasePolicy`. The system never relies merely on hiding UI buttons; direct API calls or manual URL navigation are intercepted with HTTP 403.
3. **Mass Assignment Protection**: Handled via `$fillable` on Eloquent models. The `created_by` attribute is populated strictly from `auth()->id()` on the backend, making user ID spoofing impossible.
4. **Patient Privacy (HIPAA Consideration)**: Real patient names, SSNs, or identifiers are never stored. The schema enforces anonymous token codes (e.g., `REF-84920`).
5. **SQL Injection Prevention**: Eloquent ORM parameterized queries are used throughout; raw concatenated SQL is prohibited.
6. **CSRF Protection**: Standard Laravel `@csrf` tokens protect all browser form submissions.

---

## 8. Git & GitHub Submission Guide

Follow these steps to push your project to a public GitHub repository:

```bash
# 1. Initialize git repository if not already initialized
git init

# 2. Verify git status and ensure .env is ignored
git status

# 3. Add all files
git add .

# 4. Commit using meaningful convention
git commit -m "Complete Jajal Medical Case Management System implementation"

# 5. Rename branch to main
git branch -M main

# 6. Add your GitHub remote repository (replace with your repo URL)
git remote add origin https://github.com/your-username/jajal-medical-case-management.git

# 7. Push code to GitHub
git push -u origin main
```

---

## 9. Architectural Documentation

- **Database Architecture & ER Diagram**: See [docs/database.md](docs/database.md)
- **Requirement Traceability Matrix**: See [docs/traceability.md](docs/traceability.md)
- **AWS Production Deployment Architecture**: See [docs/deployment.md](docs/deployment.md)
- **Postman API Collection**: See [docs/postman_collection.json](docs/postman_collection.json)
