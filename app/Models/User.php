<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'kyc_status',
        'verification_level', 'avatar', 'business_name', 'address',
        'is_active', 'email_verified_at', 'phone_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function kycDocuments()
    {
        return $this->hasMany(KycDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isVerified(): bool
    {
        return $this->kyc_status === 'approved';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }
        return null;
    }
}
