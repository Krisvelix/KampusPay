<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletMutation extends Model
{
    public const TYPE_TOPUP = 'topup';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_WITHDRAWAL = 'withdrawal';

    public const DIRECTION_CREDIT = 'credit';
    public const DIRECTION_DEBIT = 'debit';

    protected $fillable = [
        'wallet_id',
        'counterparty_wallet_id',
        'type',
        'direction',
        'amount',
        'balance_before',
        'balance_after',
        'reference',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'counterparty_wallet_id');
    }
}