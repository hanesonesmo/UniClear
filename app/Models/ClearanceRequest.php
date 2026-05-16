<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ClearanceRequest Model
 *
 * IMPORTANT: The foreign key column is 'user_id' (not student_id).
 * This matches the actual database column created during migration.
 */
class ClearanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // FK to users table — the student who applied
        'department_id',  // FK to departments table
        'status',         // pending | approved | rejected
        'comment',        // Department's comment/reason
        'processed_by',   // FK to users table — staff who processed it
        'processed_at',   // When it was processed
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // The student who submitted this request (foreign key: user_id)
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias — same as student() but named 'user' for flexibility
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // The department responsible for this clearance
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // The staff member who approved/rejected this request
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}