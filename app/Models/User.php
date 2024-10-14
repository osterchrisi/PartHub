<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Billable, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'selected_plan',
        'price_id',
        'last_login_at'
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

    //* Relationships

    public function parts()
    {
        return $this->hasMany(Part::class, 'part_owner_u_fk');
    }

    public function boms()
    {
        return $this->hasMany(Bom::class, 'bom_owner_u_fk');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'location_owner_u_fk');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'supplier_owner_u_fk');
    }

    public function supplierData()
    {
        return $this->hasMany(SupplierData::class, 'supplier_data_owner_u_fk');
    }

    public function footprints()
    {
        return $this->hasMany(Footprint::class, 'footprint_owner_u_fk');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'part_category_owner_u_fk');
    }

    // Method to check if the user has no subscription
    public function hasNoSubscription()
    {
        return $this->subscriptions->isEmpty();
    }

    /**
     * Send customized email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification($plan = 'free', $priceId = '')
    {
        // If no plan or priceId is set, send the default verification email
        if (is_null($this->selected_plan) || is_null($this->price_id)) {
            $this->notify(new CustomVerifyEmail(null, null));
        } else {
            // Otherwise, send the verification with plan and priceId
            $this->notify(new CustomVerifyEmail($this->selected_plan, $this->price_id));
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    //* Limit Counters
    public function getPartCount()
    {
        return $this->parts()->count();
    }

    public function getBomCount()
    {
        return $this->boms()->count();
    }

    public function getLocationCount()
    {
        return $this->locations()->count();
    }

    public function getSupplierCount()
    {
        return $this->suppliers()->count();
    }

    public function getSupplierDataCount($part_id)
    {
        return $this->supplierData()
            ->where('part_id_fk', $part_id)
            ->count();
    }

    public function getFootprintCount()
    {
        return $this->footprints()->count();
    }
    public function getCategoryCount() {
        return $this->categories()->count();
    }
}
