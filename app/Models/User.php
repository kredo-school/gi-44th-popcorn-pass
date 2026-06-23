<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


#[Fillable(['username', 'email', 'password_hash', 'phone', 'date_of_birth', 'role', 'is_active'])]
#[Hidden(['password_hash', 'remember_token'])]
class User extends Authenticatable
{
     // uuid自動生成
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    // idをuuidとして扱う
    public $incrementing = false;
    protected $keyType = 'string';


    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Get the name of the password attribute for this model.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}