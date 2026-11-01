<?php

use App\Models\Merchant;
use App\Models\Wallet;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Merchant::where('status', Merchant::STATUS_APPROVED)
            ->pluck('user_id')
            ->each(fn ($userId) => Wallet::firstOrCreate(['user_id' => $userId], ['balance' => 0]));
    }

    public function down(): void
    {
    }
};