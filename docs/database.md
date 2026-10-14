# Database Architecture & Entity Relationship Documentation

## Jajal Medical Case Management System

This document outlines the database design, tables, columns, indexes, foreign keys, relationships, and an Entity Relationship (ER) diagram for the Medical Case Management System.

---

## 1. Entity Relationship (ER) Diagram

```
+-------------------------------------------------------------+
|                            USERS                            |
+-------------------------------------------------------------+
| PK  id                 BIGINT UNSIGNED       AUTO_INCREMENT |
|     name               VARCHAR(255)          NOT NULL       |
| UQ  email              VARCHAR(255)          NOT NULL       |
|     password           VARCHAR(255)          NOT NULL       |
|     role               ENUM('admin','viewer')NOT NULL       |
|     created_at         TIMESTAMP             NULL           |
|     updated_at         TIMESTAMP             NULL           |
+-------------------------------------------------------------+
                               |
                               | 1
                               |
                               | Has Many (created_by)
                               |
                               | N
                               v
+-------------------------------------------------------------+
|                            CASES                            |
+-------------------------------------------------------------+
| PK  id                 BIGINT UNSIGNED       AUTO_INCREMENT |
| UQ  case_number        VARCHAR(50)           NOT NULL       |
|     patient_reference  VARCHAR(100)          NOT NULL       |
|     surgeon_name       VARCHAR(255)          NOT NULL       |
|     implant_type       VARCHAR(255)          NOT NULL       |
| IX  status             VARCHAR(50)           NOT NULL       |
|     priority           VARCHAR(50)           NOT NULL       |
|     surgery_date       DATE                  NULL           |
| FK  created_by         BIGINT UNSIGNED       NOT NULL       |
|     created_at         TIMESTAMP             NULL           |
|     updated_at         TIMESTAMP             NULL           |
+-------------------------------------------------------------+
                               |
                               | (created_by references users.id)
                               | ON DELETE RESTRICT
                               v
+-------------------------------------------------------------+
|                   PERSONAL_ACCESS_TOKENS                    |
|                      (Laravel Sanctum)                      |
+-------------------------------------------------------------+
| PK  id                 BIGINT UNSIGNED       AUTO_INCREMENT |
|     tokenable_type     VARCHAR(255)          NOT NULL       |
|     tokenable_id       BIGINT UNSIGNED       NOT NULL       |
|     name               VARCHAR(255)          NOT NULL       |
| UQ  token              VARCHAR(64)           NOT NULL       |
|     abilities          TEXT                  NULL           |
|     last_used_at       TIMESTAMP             NULL           |
|     expires_at         TIMESTAMP             NULL           |
|     created_at         TIMESTAMP             NULL           |
|     updated_at         TIMESTAMP             NULL           |
+-------------------------------------------------------------+
```

---

## 2. Table Specifications

### A. `users` Table
Stores authentication credentials and role assignments for system operators.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|-------------|-------------|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Unique user identifier |
| `name` | `VARCHAR(255)` | Not Null | Full legal name of user |
| `email` | `VARCHAR(255)` | Not Null, Unique Index | Login email address |
| `password` | `VARCHAR(255)` | Not Null | Bcrypt hashed password |
| `role` | `ENUM('admin', 'viewer')` | Not Null, Default 'viewer' | User authorization role |
| `created_at` | `TIMESTAMP` | Nullable | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Record update timestamp |

### B. `cases` Table
Stores implant tracking information from draft through surgery completion. Real patient identities are never stored.

| Column Name | Data Type | Constraints | Description |
|-------------|-----------|-------------|-------------|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Primary key |
| `case_number` | `VARCHAR(50)` | Not Null, Unique Index | Unique identifier (e.g., `CASE-0001`) |
| `patient_reference` | `VARCHAR(100)` | Not Null | Anonymized dummy patient identifier (e.g., `REF-84920`) |
| `surgeon_name` | `VARCHAR(255)` | Not Null | Attending surgeon name |
| `implant_type` | `VARCHAR(255)` | Not Null | Type of implant (e.g., Titanium Hip Stem, PEEK Spinal Cage) |
| `status` | `VARCHAR(50)` | Not Null, Index | Case status: `Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled` |
| `priority` | `VARCHAR(50)` | Not Null | Priority flag: `Low`, `Medium`, `High` |
| `surgery_date` | `DATE` | Nullable | Scheduled or planned procedure date |
| `created_by` | `BIGINT UNSIGNED` | Not Null, Foreign Key | References `users.id` |
| `created_at` | `TIMESTAMP` | Nullable | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Record update timestamp |

---

## 3. Database Indexes

1. **`cases_case_number_unique`**:
   - Type: `UNIQUE`
   - Columns: `case_number`
   - Purpose: Enforces system-wide uniqueness on case references at the database engine level, preventing duplicate case numbers during concurrent submissions.
2. **`cases_status_index`**:
   - Type: `INDEX`
   - Columns: `status`
   - Purpose: Optimizes dashboard count queries and status-filtering queries on the case list.
3. **`cases_created_by_foreign`**:
   - Type: `FOREIGN KEY`
   - Columns: `created_by` -> references `users.id`
   - Purpose: Enforces referential integrity. Ensures no case can reference a non-existent user.

---

## 4. Eloquent Model Relationships

### `User` Model (`app/Models/User.php`)
```php
/**
 * A user can create many medical cases.
 */
public function cases(): HasMany
{
    return $this->hasMany(MedicalCase::class, 'created_by');
}
```

### `MedicalCase` Model (`app/Models/MedicalCase.php`)
```php
/**
 * A medical case belongs to the creator user.
 */
public function creator(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}
```
