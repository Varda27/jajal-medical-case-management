<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MedicalCase
 *
 * Tracks an implant procedure from Draft to Completed.
 * Dummy patient codes are strictly used to protect privacy.
 *
 * @property int $id
 * @property string $case_number
 * @property string $patient_reference
 * @property string $surgeon_name
 * @property string $implant_type
 * @property string $status ('Draft', 'Planning', 'In Review', 'Approved', 'Completed', 'Cancelled')
 * @property string $priority ('Low', 'Medium', 'High')
 * @property string|null $surgery_date
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class MedicalCase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * Allowed statuses according to business specifications.
     */
    public const STATUSES = [
        'Draft',
        'Planning',
        'In Review',
        'Approved',
        'Completed',
        'Cancelled',
    ];

    /**
     * Allowed priority levels.
     */
    public const PRIORITIES = [
        'Low',
        'Medium',
        'High',
    ];

    /**
     * Attributes that are mass assignable.
     * Note: created_by is explicitly set by the application, never raw user manipulation.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'case_number',
        'patient_reference',
        'surgeon_name',
        'implant_type',
        'status',
        'priority',
        'surgery_date',
        'created_by',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'surgery_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: The user who created the medical case.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to filter cases by status.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if (!empty($status)) {
            $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope a query to search by case number or surgeon name.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'LIKE', "%{$search}%")
                  ->orWhere('surgeon_name', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }
}
