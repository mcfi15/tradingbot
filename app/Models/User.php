<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'provider_id',
        'provider_name',
        'social_token',
        'password',
        'referral_code',
        'referrer_id',
        'balance',
        'status',
        'photo',
        'lang',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        $decimal_places = getSetting('decimal_places') ?: 2;
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:' . $decimal_places,
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            app(\App\Services\UserWalletProvisionerService::class)->provisionWallets($user);
        });
    }

    public function blockchainWallets()
    {
        return $this->hasMany(UserBlockchainWallet::class);
    }


    /**
     * Get the user who referred this user.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the users referred by this user.
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }


    // relationship
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // define relationship to notification messages
    public function notificationMessages()
    {
        return $this->hasMany(NotificationMessage::class);
    }


    // Define relationship with onboarding
    public function onboarding()
    {
        return $this->hasOne(Onboarding::class);
    }

    // define kyc relationship
    public function kyc()
    {
        return $this->hasMany(Kyc::class);
    }

    // define relationship with the deposits
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    // relationship with trading bot activations
    public function tradingBotActivations()
    {
        return $this->hasMany(TradingBotActivation::class);
    }

    // relationship with withdrawals
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // relationship with copy trading history
    public function copyTradingHistories()
    {
        return $this->hasMany(CopyTradingHistory::class);
    }

    // relationship with exchange API connections
    public function exchangeConnections()
    {
        return $this->hasMany(ExchangeConnection::class);
    }

    // relationship with live exchange orders
    public function tradeOrders()
    {
        return $this->hasMany(TradeOrder::class);
    }

    // relationship with the trade audit log
    public function tradeLogs()
    {
        return $this->hasMany(TradeLog::class);
    }

    // relationship with auto-trading risk rules
    public function tradingPreference()
    {
        return $this->hasOne(UserTradingPreference::class);
    }

    // relationship with followed signals
    public function signalFollows()
    {
        return $this->hasMany(SignalFollow::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include users with unverified emails.
     */
    public function scopeEmailUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Get the user's username.
     */
    public function getUsernameAttribute()
    {
        if (config('app.env') === 'sandbox' && session()->has('sandbox_user')) {
            return session()->get('sandbox_user')->first_name ?? 'User';
        }
        return $this->attributes['username'] ?? $this->attributes['first_name'] ?? 'User';
    }

    /**
     * Get the user's email.
     */
    public function getEmailAttribute()
    {
        if (config('app.env') === 'sandbox' && session()->has('sandbox_user')) {
            return session()->get('sandbox_user')->email;
        }
        return $this->attributes['email'];
    }

    /**
     * Get the user's full name.
     */
    public function getFullnameAttribute()
    {
        if (config('app.env') === 'sandbox' && session()->has('sandbox_user')) {
            return session()->get('sandbox_user')->name;
        }
        return ($this->attributes['first_name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? '');
    }

    /**
     * Get the user's first name.
     */
    public function getFirstnameAttribute()
    {
        if (config('app.env') === 'sandbox' && session()->has('sandbox_user')) {
            return session()->get('sandbox_user')->first_name;
        }
        return $this->attributes['first_name'] ?? '';
    }

    /**
     * Get the user's last name.
     */
    public function getLastnameAttribute()
    {
        if (config('app.env') === 'sandbox' && session()->has('sandbox_user')) {
            return session()->get('sandbox_user')->last_name;
        }
        return $this->attributes['last_name'] ?? '';
    }
}
