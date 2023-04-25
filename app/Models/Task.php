<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Task extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'project',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'text',
        'priority',
        'manager_id',
        'project_id',
        'activity_id',
        'coin_type_id',
        'verifier_id',
        'verifier_driver',
        'min_level',
        'max_level',
        'started_at',
        'ended_at',
        'number_of_winners',
        'number_of_invites',
        'level_coefficient',
        'number_of_participants',
        'invite_contest_completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level_coefficient' => 'boolean',
        'invite_contest_completed' => 'boolean',
    ];

    protected $appends = [
        'autovalidate',
        'status_by_dates',
        'is_invite_friends',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            $user = auth()->user();
            if ($user) {
                $query->whereRelation('project', 'public', true)
                    ->orWhereIn('project_id', $user->projectMembers->pluck('project_id')->toArray());
            } else {
                $query->whereRelation('project', 'public', true);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(UserTask::class);
    }

    public function rewards()
    {
        return $this->hasMany(TaskReward::class);
    }

    public function conditions()
    {
        return $this->hasMany(TaskCondition::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function coinType()
    {
        return $this->belongsTo(CoinType::class);
    }

    public function verifier()
    {
        return $this->hasOne(TaskVerifier::class, 'task_id', 'id');
    }

    public function userTasks()
    {
        return $this->hasMany(UserTask::class);
    }

    public function inWorkByUser()
    {
        return $this->hasOne(UserTask::class)->where('user_id', auth()->id());
    }

    public function userAssignments()
    {
        return $this->hasMany(UserTaskAssignment::class, 'task_id', 'id');
    }

    public function userTasksInWork()
    {
        return $this->hasMany(UserTask::class)->inWork();
    }

    public function getCoinsAttribute($coins)
    {
        return BigDecimal::of($coins);
    }

    public function setCoinsAttribute($coins)
    {
        $this->attributes['coins'] = (string) BigDecimal::of($coins);
    }

    public function getAutovalidateAttribute()
    {
        return !empty($this->verifier_driver);
    }

    public function getIsInviteFriendsAttribute()
    {
        return $this->number_of_winners > 0 || $this->number_of_invites > 0;
    }

    public function getStatusByDatesAttribute()
    {
        $now = now();
        $status = 'finished';

        if ($this->started_at > $now) {
            $status = 'upcoming';
        } else if ($this->started_at < $now && $this->ended_at > $now->subDays(1)) {
            $status = 'available';
        }

        return $status;
    }

    public function scopeForUser($query, $ignoreUserTasks = false)
    {
        $user = auth()->user();
        if (!$user) {
            return $query;
        }

        $user->load([
            'activities' => fn ($query) => $query->active(),
        ]);

        $level = $user->level;
        $userId = $user->id;
        $activityIds = $user->activities->pluck('activity_id')->toArray();

        return $query->where(function ($query) use ($level, $userId, $activityIds) {
            $query->where(function ($query) use ($level, $activityIds) {
                $query->where(function ($q) use ($activityIds) {
                    $q->whereNull('activity_id')
                        ->orWhereIn('activity_id', $activityIds);
                })->where(function ($query) use ($level) {
                    $query->where(function ($q) {
                        $q->whereNull('min_level')
                            ->whereNull('max_level');
                    })->orWhere(function ($q) use ($level) {
                        $q->where('min_level', '<=', $level)
                            ->where('max_level', '>=', $level);
                    });
                });
            })
            ->orWhereRelation('userAssignments', 'user_id', $userId);
        })->when(!$ignoreUserTasks, function ($query) use ($userId) {
            $query->whereDoesntHave('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        });
    }
}
