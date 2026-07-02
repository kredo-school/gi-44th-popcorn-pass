<?php

namespace App\Models;

use App\Services\TierService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'username',
    'email',
    'password_hash',
    'phone',
    'date_of_birth',
    'role',
    'is_active',
    'first_name',
    'last_name',
    'avatar',
    'points',
    'gender',
    'occupation',
])]
#[Hidden(['password_hash', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = \Illuminate\Support\Str::uuid();
        });
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = [
        'tier',
        'tier_progress',
        'full_name',
        'age',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password_hash' => 'hashed',
            'points' => 'integer',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // -----------------------
    // Computed attributes
    // -----------------------

    public function getTierAttribute(): string
    {
        return app(TierService::class)->tierForPoints($this->points ?? 0);
    }

    public function getTierProgressAttribute(): int
    {
        return app(TierService::class)->progressPercent($this->points ?? 0);
    }

    public function getTierLabelAttribute(): string
    {
        return ucfirst($this->tier);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    // -----------------------
    // Role helpers
    // -----------------------

    public function isCustomer(): bool
    {
        return (int) $this->role === 1;
    }

    public function isAdminPanelUser(): bool
    {
        return in_array((int) $this->role, [2, 3, 4], true);
    }
}