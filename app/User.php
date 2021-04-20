<?php

namespace App;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function stores()
    {
        return $this->hasMany(ShopifyStore::class)->where('isDeleted', false);
    }
    public function getEnabledShopifyStores()
    {
        return $this->stores()->where('enabled_on_dashboard', true)->get()->pluck('id');
    }
    public function getAdAccounts()
    {
        return $this->hasMany(FacebookAd::class, 'user_id')->get();
    }

    public function getPaypalAccounts()
    {
        return $this->hasMany(Paypal::class, 'user_id')->where('isDeleted', false)->select('id', 'enabled_on_dashboard', 'name')->get();
    }

    public function getStripeAccounts()
    {
        return $this->hasMany(StripeAccount::class, 'user_id')->where('isDeleted', false)->select('id', 'enabled_on_dashboard', 'name')->get();
    }

    public function getTiktokAccounts()
    {
        return $this->hasMany(TiktokAd::class, 'user_id')->where('isDeleted', false)->get();
    }

    public function getFacebookAccounts()
    {
        return $this->hasMany(FacebookAd::class, 'user_id')->where('isDeleted', false)->select('id', 'ad_account_id', 'ad_account_name', 'enabled_on_dashboard', 'access_token')->get();
    }
    public function getSnapchatAccounts()
    {
        return $this->hasMany(SnapchatAdAccount::class, 'user_id')->where('isDeleted', false)->get();
    }
    public function getGoogleAccounts()
    {
        return $this->hasMany(GoogleAd::class, 'user_id')->where('isDeleted', false)->get();
    }
    public function getStripeAccountConnectIds()
    {
        return $this->hasMany(StripeAccount::class, 'user_id')->where('isDeleted', false)->select('id', 'user_id', 'stripe_user_id', 'access_token', 'refresh_token', 'enabled_on_dashboard')->get();
    }
    public function getPaypalAccountConnectIds()
    {
        return $this->hasMany(Paypal::class, 'user_id')->where('isDeleted', false)->select('id', 'access_token', 'refresh_token', 'enabled_on_dashboard', 'expires_at')->get();
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
