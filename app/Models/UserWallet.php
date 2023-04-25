<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'coinType',
    ];

    protected $appends = [
        'balance_in_usd'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'balance',
        'user_id',
        'is_primary',
        'coin_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coinType()
    {
        return $this->belongsTo(CoinType::class);
    }

    public function getBalanceAttribute($balance)
    {
        return BigDecimal::of($balance);
    }

    public function setBalanceAttribute($balance)
    {
        $this->attributes['balance'] = (string) BigDecimal::of($balance);
    }

    public function getBalanceInUsdAttribute()
    {
        return $this->balance->multipliedBy($this->coinType->price_usd);
    }
}
