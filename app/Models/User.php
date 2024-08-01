<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'email_verified_at' => 'datetime',
    ];

    // Method to check if the user has no subscription
    public function hasNoSubscription()
    {
        return $this->subscriptions->isEmpty();
    }

    // Method to assign a free subscription
    public function assignFreeSubscription()
    {
        if ($this->hasNoSubscription()) {
            $this->newSubscription('free_plan', 'price_1PiiQPEb2UyIF2sh3pqjBR75')->create(); // 'free-plan' is the ID of your free plan in Stripe
        }
    }

}
