<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'site_name',
        'support_email',
        'contact_phone',
        'timezone',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_encryption',
        'notification_email',
        'payment_gateway',
        'currency',
        'tax_rate',
        'stripe_publishable_key',
    ];

    protected $casts = [
        'smtp_port' => 'integer',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * This table only ever has one row (global settings).
     * This helper always returns that single row, creating it with
     * defaults if it doesn't exist yet.
     */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }
}