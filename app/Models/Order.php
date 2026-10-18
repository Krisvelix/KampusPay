<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_number', 'merchant_id', 'customer_id', 'total_amount', 'status', 'payment_status', 'notes'])]
class Order extends Model
{
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'bg-amber-100 text-amber-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_COMPLETED  => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED  => 'bg-red-100 text-red-800',
            default                 => 'bg-gray-100 text-gray-800',
        };
    }
}
