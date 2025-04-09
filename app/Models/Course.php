<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
        'content_user_id',
    ];
    
    /**
     * Get the content user (instructor) who manages this course
     */
    public function contentUser()
    {
        return $this->belongsTo(User::class, 'content_user_id');
    }
    
    /**
     * Get users registered for this course
     */
    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'course_registrations')
            ->withPivot('registered_at')
            ->withTimestamps();
    }
    
    /**
     * Get lectures for this course
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }
    
    /**
     * Get exercises for this course
     */
    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
    
    /**
     * Get approval requests for this course
     */
    public function userApprovals()
    {
        return $this->hasMany(UserApproval::class);
    }
}
