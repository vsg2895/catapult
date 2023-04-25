<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWalletWithdrawalRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_EXECUTED = 'executed';
    const STATUS_ACCEPTED = 'accepted';

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
        'status',
        'tx_hash',
        'user_id',
        'user_wallet_id',
    ];

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
