<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Notifications\ResetPasswordNotification;
use App\Observers\UserObserver;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWallets;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Overtrue\LaravelLike\Traits\Liker;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements Customer
{
    use CanPay, HasApiTokens, HasFactory, HasRoles,  HasWallets, Liker, Notifiable, SoftDeletes;

    protected string $guard_name = 'sanctum';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
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
            'status' => UserEnum::class
        ];
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'user_id', 'id');
    }

    public function departments(): HasOne
    {
        return $this->hasOne(Departments::class, 'id', 'department');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(User::class, 'added_by', 'id');
    }

    public function scopeInvitedStatus($query)
    {
        return $query->where('status', UserEnum::Invited);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('frontend.reset_password').'?token='.$token;

        $this->notify(new ResetPasswordNotification($url));
    }
}
