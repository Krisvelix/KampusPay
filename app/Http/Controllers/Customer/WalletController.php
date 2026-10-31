<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\Wallet;
use App\Models\WalletMutation;
use App\Services\WalletMutationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $wallet = $user->wallet()->firstOrCreate([], ['balance' => 0]);
        $recentTopups = $user->topups()->latest()->limit(5)->get();

        return view('customer.dashboard', compact('wallet', 'recentTopups'));
    }

    public function topupCreate(Request $request): View
    {
        $wallet = $request->user()->wallet()->firstOrCreate([], ['balance' => 0]);

        return view('customer.topup.create', compact('wallet'));
    }

    public function topupStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:5000000'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($user, $data) {
            $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

            Topup::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'status' => Topup::STATUS_SUCCESS,
            ]);

            app(WalletMutationService::class)->credit(
                $wallet,
                $data['amount'],
                WalletMutation::TYPE_TOPUP,
                'Top up saldo'
            );
        });

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Top up berhasil.');
    }

    public function topupIndex(Request $request): View
    {
        $topups = $request->user()->topups()->latest()->paginate(10);

        return view('customer.topup.index', compact('topups'));
    }
}