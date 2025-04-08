<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'deadline',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'datetime',
    ];
    
    /**
     * Get the course that the exercise belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    /**
     * Get all submissions for this exercise
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(ExerciseSubmission::class);
    }
    
    /**
     * Get all user answers for this exercise
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserExerciseAnswer::class);
    }
}
