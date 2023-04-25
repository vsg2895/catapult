<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWalletHistory extends Model
{
    use HasFactory;

    protected $table = 'user_wallet_history';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'wallet',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value',
        'points',
        'user_id',
        'task_id',
        'user_wallet_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id', 'id');
    }

    public function getValueAttribute($value)
    {
        return BigDecimal::of($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = (string) BigDecimal::of($value);
    }
}
