<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseSubmission extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exercise_id',
        'user_id',
        'submission_content',
        'submitted_at',
        'score',
        'graded_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'decimal:2',
    ];
    
    /**
     * Get the exercise for this submission
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
    
    /**
     * Get the user who submitted this exercise
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
