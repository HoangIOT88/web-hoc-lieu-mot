<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReport extends Model
{
    use HasFactory;
    
    const TYPE_USER_STATS = 'USER_STATS';
    const TYPE_COURSE_STATS = 'COURSE_STATS';
    const TYPE_ACTIVITY_STATS = 'ACTIVITY_STATS';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'generated_by_admin_id',
        'report_type',
        'file_url',
        'generated_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
    ];
    
    /**
     * Get the admin who generated this report.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_admin_id');
    }

    /**
     * Get the report type name in a human-readable format.
     *
     * @return string
     */
    public function getReportTypeName(): string
    {
        switch ($this->report_type) {
            case self::TYPE_USER_STATS:
                return 'User Statistics';
            case self::TYPE_COURSE_STATS:
                return 'Course Statistics';
            case self::TYPE_ACTIVITY_STATS:
                return 'Activity Statistics';
            default:
                return 'Unknown';
        }
    }
}
