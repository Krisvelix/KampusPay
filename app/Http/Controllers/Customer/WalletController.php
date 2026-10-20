<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    private function wallet(Request $request): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['balance' => 0]
        );
    }

    public function dashboard(Request $request)
    {
        $wallet = $this->wallet($request);
        $recentTopups = $request->user()->topups()->latest()->take(5)->get();

        return view('customer.dashboard', compact('wallet', 'recentTopups'));
    }

    public function topupCreate(Request $request)
    {
        $wallet = $this->wallet($request);

        return view('customer.topup.create', compact('wallet'));
    }

    public function topupStore(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:5000000'],
        ], [
            'amount.required' => 'Nominal top up wajib diisi.',
            'amount.integer' => 'Nominal harus berupa angka bulat.',
            'amount.min' => 'Top up minimal Rp 10.000.',
            'amount.max' => 'Top up maksimal Rp 5.000.000 per transaksi.',
        ]);

        $wallet = $this->wallet($request);

        DB::transaction(function () use ($request, $wallet, $data) {
            $locked = Wallet::whereKey($wallet->id)->lockForUpdate()->first();

            Topup::create([
                'user_id' => $request->user()->id,
                'amount' => $data['amount'],
                'status' => Topup::STATUS_SUCCESS,
            ]);

            $locked->increment('balance', $data['amount']);
        });

        return redirect()->route('customer.dashboard')
            ->with('success', 'Top up Rp '.number_format($data['amount'], 0, ',', '.').' berhasil.');
    }

    public function topupIndex(Request $request)
    {
        $topups = $request->user()->topups()->latest()->paginate(10);

        return view('customer.topup.index', compact('topups'));
    }
}