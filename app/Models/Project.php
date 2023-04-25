<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'public',
        'description',
        'pool_amount',
        'coin_type_id',
        'blockchain_id',
        'medium_username',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            $user = auth()->user();
            if ($user) {
                $query->where('public', true)
                    ->orWhereIn('id', $user->projectMembers->pluck('project_id')->toArray());
            } else {
                $query->where('public', true);
            }
        });
    }

    public function tags(): HasMany
    {
        return $this->hasMany(ProjectTag::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'owner_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->without('project');
    }

    public function coinType(): BelongsTO
    {
        return $this->belongsTo(CoinType::class);
    }

    public function blockchain(): BelongsTo
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(ProjectSocialLink::class);
    }

    public function tasksForUser(): HasMany
    {
        return $this->hasMany(Task::class)->forUser()->withoutGlobalScopes();
    }

    public function showcaseTasks(): HasMany
    {
        return $this->hasMany(Task::class)->forUser()->orderByDesc('created_at')->limit(6);
    }

    public function discordProvider(): ?SocialProvider
    {
        return $this->socialProviders->where('provider_name', 'discord_bot')->first();
    }

    public function socialProviders(): MorphMany
    {
        return $this->morphMany(SocialProvider::class, 'model');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo');
        $this->addMediaCollection('banner');
    }

    public function getPoolAmountAttribute($poolAmount)
    {
        return BigDecimal::of($poolAmount);
    }

    public function setPoolAmountAttribute($poolAmount)
    {
        $this->attributes['pool_amount'] = (string) BigDecimal::of($poolAmount);
    }

    public function routeNotificationForDiscord()
    {
        $socialProvider = $this->discordProvider();
        return $socialProvider?->provider_id;
    }
}
