<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements JWTSubject, HasMedia, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;

    const SET_UP_PROFILE_DATA = [
        'name',
        'languages_count',
        'country',
        'email',
        'discord_social_provider',
        'telegram_social_provider',
        'twitter_social_provider',
        'wallet',
        'activities_count',
        'skills_count',
    ];

    // protected $with = ['media'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nonce',
        'email',
        'phone',
        'level',
        'wallet',
        'points',
        'password',
        'completed_profile_reward',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_profile_reward' => 'boolean',
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    protected $appends = [
        'set_up_profile',
    ];

    public function projectReports(): HasMany
    {
        return $this->hasMany(UserProjectReport::class, 'user_id', 'id');
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(UserWallet::class, 'user_id', 'id');
    }

    public function discordProvider(): ?SocialProvider
    {
        return $this->socialProviders->where('provider_name', 'discord')->first();
    }

    public function socialProviders(): MorphMany
    {
        return $this->morphMany(SocialProvider::class, 'model');
    }

    public function historyWallets(): HasMany
    {
        return $this->hasMany(UserWalletHistory::class, 'user_id', 'id');
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(UserWalletWithdrawalRequest::class, 'user_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(UserTask::class, 'user_id', 'id');
    }

    public function tasksInWork(): HasMany
    {
        return $this->hasMany(UserTask::class, 'user_id', 'id')->inWork();
    }

    public function tasksIsDone(): HasMany
    {
        return $this->hasMany(UserTask::class, 'user_id', 'id')->where('status', UserTask::STATUS_DONE);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(UserSkill::class, 'user_id', 'id');
    }

    public function country(): HasOne
    {
        return $this->hasOne(UserCountry::class, 'user_id', 'id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class, 'user_id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class, 'user_id', 'id');
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(UserSocialLink::class, 'user_id', 'id');
    }

    public function levelPoints(): HasMany
    {
        return $this->hasMany(UserLevelPoint::class, 'user_id', 'id');
    }

    public function activityLinks(): HasMany
    {
        return $this->hasMany(UserActivityLink::class, 'user_id', 'id');
    }

    public function projectMembers(): MorphMany
    {
        return $this->morphMany(ProjectMember::class, 'userable');
    }

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? 'Talent'.$this->id;
    }

    public function getSetUpProfileAttribute(): array
    {
        $result = [];
        $percentage = 0;

        foreach (self::SET_UP_PROFILE_DATA as $attribute) {
            $condition = false;
            $fmtAttribute = str_replace(['_count', '_social_provider'], '', $attribute);

            if (str_ends_with($attribute, 'social_provider')) {
                $condition = $this->socialProviders->contains(function ($socialProvider) use ($fmtAttribute) {
                    return $socialProvider->provider_name === $fmtAttribute;
                });
            } else if (!empty($this[$fmtAttribute])) {
                $condition = !str_ends_with($attribute, 'count') || $this[$fmtAttribute]->count() > 0;
            }

            if ($condition) {
                $percentage += 10;
            }

            $result[$attribute] = $condition;
        }

        return $result + [
            'percentage' => $percentage,
            'completed_profile_reward' => $this->completed_profile_reward,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'ambassador.'.$this->id;
    }
}
