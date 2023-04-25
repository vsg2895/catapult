<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserTask extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    const STATUS_DONE = 'done';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_RETURNED = 'returned';
    const STATUS_REJECTED = 'rejected';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ON_REVISION = 'on_revision';
    const STATUS_WAITING_FOR_REVIEW = 'waiting_for_review';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'rating',
        'report',
        'user_id',
        'task_id',
        'notified',
        'manager_id',
        'revised_at',
        'reported_at',
        'completed_at',
        'referral_code',
        'winner_by_invites',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notified' => 'boolean',
        'revised_at' => 'datetime',
        'reported_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function referrals()
    {
        return $this->hasMany(UserReferral::class, 'user_task_id', 'id');
    }

    public function scopeInWork($query)
    {
        return $query->whereIn('status', [
            UserTask::STATUS_RETURNED,
            UserTask::STATUS_IN_PROGRESS,
            UserTask::STATUS_ON_REVISION,
            UserTask::STATUS_WAITING_FOR_REVIEW,
        ]);
    }

    public function latestComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}
