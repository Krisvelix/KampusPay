<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Merchant extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = ['user_id', 'store_name', 'status', 'rejection_reason'];

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
}