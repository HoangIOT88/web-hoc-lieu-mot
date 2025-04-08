<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'joined_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
    ];
    
    /**
     * Get the chat group that this membership belongs to
     */
    public function chatGroup()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }
    
    /**
     * Get the user for this membership
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
