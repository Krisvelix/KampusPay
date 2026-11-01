<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletMutation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WalletMutationService
{
    public function credit(
        Wallet $wallet,
        string|int|float $amount,
        string $type,
        ?string $description = null,
        ?string $reference = null,
        ?Wallet $counterparty = null,
    ): WalletMutation {
        return $this->apply($wallet, $amount, $type, WalletMutation::DIRECTION_CREDIT, $description, $reference, $counterparty);
    }

    public function debit(
        Wallet $wallet,
        string|int|float $amount,
        string $type,
        ?string $description = null,
        ?string $reference = null,
        ?Wallet $counterparty = null,
    ): WalletMutation {
        return $this->apply($wallet, $amount, $type, WalletMutation::DIRECTION_DEBIT, $description, $reference, $counterparty);
    }

    public function makeReference(): string
    {
        return 'TRX-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }

    private function apply(
        Wallet $wallet,
        string|int|float $amount,
        string $type,
        string $direction,
        ?string $description,
        ?string $reference,
        ?Wallet $counterparty,
    ): WalletMutation {
        $amount = number_format((float) $amount, 2, '.', '');

        if (bccomp($amount, '0', 2) <= 0) {
            throw new RuntimeException('Nominal harus lebih dari 0.');
        }

        return DB::transaction(function () use ($wallet, $amount, $type, $direction, $description, $reference, $counterparty) {
            $locked = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $before = number_format((float) $locked->balance, 2, '.', '');

            if ($direction === WalletMutation::DIRECTION_DEBIT) {
                if (bccomp($before, $amount, 2) < 0) {
                    throw new RuntimeException('Saldo tidak cukup.');
                }
                $after = bcsub($before, $amount, 2);
            } else {
                $after = bcadd($before, $amount, 2);
            }

            $locked->balance = $after;
            $locked->save();

            $mutation = WalletMutation::create([
                'wallet_id' => $locked->id,
                'counterparty_wallet_id' => $counterparty?->id,
                'type' => $type,
                'direction' => $direction,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference' => $reference ?? $this->makeReference(),
                'description' => $description,
            ]);

            $wallet->refresh();

            return $mutation;
        });
    }
}