<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: User.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\UserEnum;
use App\Observers\UserObserver;
use Awobaz\Compoships\Compoships;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Traits\HasWallets;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Overtrue\LaravelFavorite\Traits\Favoriter;
use Overtrue\LaravelLike\Traits\Liker;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements Customer, FilamentUser, HasAvatar, WalletFloat
{
    use AuthenticationLoggable, CanPay, Compoships, Favoriter, HasApiTokens, HasFactory, HasRoles, HasWalletFloat, HasWallets, Liker, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'username',
        'name',
        'email',
        'password',
        'department',
        'designation',
        'email_verified_at',
        'remember_token',
        'status',
        'invitation_token',
        'added_by',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserEnum::class,
        ];
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function departments(): HasOne
    {
        return $this->hasOne(Departments::class, 'id', 'department');
    }

    public function designations(): HasOne
    {
        return $this->hasOne(Designations::class, 'id', 'designation');
    }

    public function receivedGifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'to', 'id');
    }

    public function sentGifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'by', 'id');
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class, ['to', 'by'], ['id', 'id']);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInvitedStatus(Builder $query): Builder
    {
        return $query->where('status', UserEnum::Invited);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('access panel', 'web') && $this->status !== UserEnum::Deactivate;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ?? 'https://ui-avatars.com/api/?name='.$this->name.'&rounded=true&color=FFFFFF&background=0D8ABC';
    }
}
