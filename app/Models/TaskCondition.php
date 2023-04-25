<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCondition extends Model
{
    use HasFactory;

    const TYPE_DISCORD_ROLE = 'discord_role';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'value',
        'operator',
        'task_id',
    ];

    protected $appends = [
        'is_opened',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getIsOpenedAttribute()
    {
        return app($this->type, ['taskCondition' => $this])->check(auth()->user(), $this->task);
    }
}
