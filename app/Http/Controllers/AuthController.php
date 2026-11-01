<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            // admin TIDAK boleh daftar lewat form
            'role' => ['required', Rule::in([User::ROLE_CUSTOMER, User::ROLE_MERCHANT])],
            // nama toko wajib hanya kalau role = merchant
            'store_name' => ['required_if:role,'.User::ROLE_MERCHANT, 'nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'is_active' => true,
            ]);

            if ($user->isMerchant()) {
                Merchant::create([
                    'user_id' => $user->id,
                    'store_name' => $data['store_name'],
                    'status' => Merchant::STATUS_PENDING,
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // akun nonaktif tidak akan pernah berhasil login
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            $inactive = User::where('email', $credentials['email'])
                ->where('is_active', false)
                ->exists();

            return back()
                ->withErrors(['email' => $inactive
                    ? 'Akunmu dinonaktifkan. Hubungi admin.'
                    : 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route(Auth::user()->dashboardRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}