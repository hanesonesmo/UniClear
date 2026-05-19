<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'registration_number',
    'phone',
    'department_id',
    ];

protected $hidden = [
    'password',
    'remember_token',
   ];

protected $casts = [
    'email_verified_at' => 'datetime',
    'password'=> 'hashed',
    ];

// RELATIONSHIPS
// A student's clearance requests — foreign key is 'user_id'
public function clearanceRequests()
 {
    return $this->hasMany(ClearanceRequest::class, 'user_id');
  }

// The department this user belongs to
public function department()
   {
    return $this->belongsTo(Department::class);
   }

// Requests processed by this staff member
public function processedRequests()
  {
   return $this->hasMany(ClearanceRequest::class, 'processed_by');
   }

    // ROLE HELPERS
    public function isStudent(): bool {
        return $this->role === 'student';
        }
    public function isStaff(): bool{
         return $this->role === 'staff';
         }
    public function isAdmin(): bool   {
        return $this->role === 'admin';
        }

// Returns true if all departments have approved this student
public function isFullyCleared(): bool
    {
   $totalDepartments = Department::count();
   $approvedCount = $this->clearanceRequests()
                   ->where('status', 'approved')
                    ->count();
        return $approvedCount === $totalDepartments;
    }
}
