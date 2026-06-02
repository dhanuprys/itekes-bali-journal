<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, SoftDeletes;

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if (!$user->isForceDeleting()) {
                $user->email = $user->email . '#deleted-' . time();
                if ($user->username) {
                    $user->username = $user->username . '#deleted-' . time();
                }
                $user->save();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'photo_path',
        'max_active_research',
        'max_active_community_service',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function researchSubmissions(): HasMany
    {
        return $this->hasMany(ResearchSubmission::class);
    }

    public function communityServiceSubmissions(): HasMany
    {
        return $this->hasMany(CommunityServiceSubmission::class);
    }

    public function ethicalClearanceSubmissions(): HasMany
    {
        return $this->hasMany(EthicalClearanceSubmission::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(UserLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }
}
