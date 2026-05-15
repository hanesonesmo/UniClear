<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
       'department_name',
       'description',
    ];

    //RELATIONSHIPS
    //A department has many staff users
    public function staff()
    {
        return $this->hasMany(User::class)->where('role', 'staff');
    }

    //A department has many claerance requests
    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class);
    }

    //HELPER METHODS
    public function pendingCount(): int
    {
        return $this->clearanceRequests()->where('status', 'pending')->count();
    }
}
