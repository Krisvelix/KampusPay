<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[Fillable(['user_id', 'store_name', 'description', 'store_address', 'phone_number', 'status', 'rejection_reason'])]
class Merchant extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected static function booted(): void
    {
        static::saved(function (Merchant $merchant) {
            $becameApproved = $merchant->status === self::STATUS_APPROVED
                && ($merchant->wasRecentlyCreated || $merchant->wasChanged('status'));

            if ($becameApproved) {
                $merchant->user->ensureWallet();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): HasOneThrough
    {
        return $this->hasOneThrough(Wallet::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}