<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClearanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'status', // pending, approved, rejected
        'processed_by', 
        'processed_at', 
        'comment',// staff user id who processed the request
     ];

     protected $casts = [
        'processed_at' => 'datetime',
     ];

     //RELATIONSHIPS
//The student who submitted this clearance request
public function students()
{
    return $this->belongsTo(User::class, 'user_id');
}

//The department responsible for approving/Rejecting this request
public function department()
{
    return $this->belongsTo(Department::class);
}

//The staff member who processed proved/rejected this request
public function processedBy()
{
    return $this->belongsTo(User::class, 'processed_by');
}

//HELPER METHODS
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

public function statusBadgeClass(): string
{
    return match ($this->status) {
        'approved' => 'badge-success',
        'rejected' => 'badge-danger',
        default => 'badge-secondary',
    };
}
}

