<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserApproval extends Model
{
    use HasFactory;
    
    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content_user_id',
        'status',
        'comment',
        'reviewed_at',
        'course_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];
    
    /**
     * Get the user that needs approval
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the content user who reviewed the approval
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'content_user_id');
    }
    
    /**
     * Get the course associated with this approval
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    /**
     * Check if the approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    /**
     * Check if the approval is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    
    /**
     * Check if the approval is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
