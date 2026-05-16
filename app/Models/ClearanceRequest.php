<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // The actual DB column name (not student_id)
        'department_id',
        'status',
        'comment',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    /**
     * The student who submitted this request.
     * Foreign key is 'user_id' (matches actual DB column).
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Same relationship aliased as 'user' for flexibility.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The department handling this request.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The staff member who processed this request.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // -------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
