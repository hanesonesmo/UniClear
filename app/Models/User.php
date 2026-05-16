<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'staff', 'student'
        'department_id', // nullable for admin and students
        'registration_number',
     ];

     //Automatic type casting for specific fields
     protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
     ];

//a user can have many clearance requests (for students)
public function clearanceRequests()
{
   return $this->hasMany(ClearanceRequest::class, 'user_id');
}

//A staff member belongs to one department
public function department()
{
    return $this->belongsTo(Department::class);
}

//Clearance requests where this user is the processor (staff)
public function processedRequests()
{
    return $this->hasMany(ClearanceRequest::class, 'processed_by');
}

//Return true if the user is a student
public function isStudent(): bool 
{
    return $this->role === 'student';
}

//Returns true if the user is department staff
public function isStaff(): bool 
{
    return $this->role === 'staff';
}

//Return true if the user is an admin
public function isAdmin(): bool {
    return $this->role === 'admin';
}

//Checks if the student is fully cleared
public function isFullyCleared(): bool
{
$totalDepartments = Department::count();
$approvedCount = $this->clearanceRequests()
       ->where('status', 'approved')
       ->count();
       
return $approvedCount === $totalDepartments;
}
}

