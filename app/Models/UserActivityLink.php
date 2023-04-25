<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'activity_link_id',
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(ActivityLink::class, 'activity_link_id', 'id');
    }
}
