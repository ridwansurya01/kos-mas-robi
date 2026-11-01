<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;

class AuthController extends Controller
{
    /**
     * Show Login Page.
     * Accessible by all — controller shows the form even if already logged in.
     */
    public function showLogin()
    {
        $tenants = User::where('role', 'tenant')->with('room')->get();
        return view('auth.login', compact('tenants'));
    }

    /**
     * Handle Login Submission.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            if (Auth::user()->isOwner()) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '! Masuk sebagai Admin.');
            }

            return redirect()->route('tenant.portal')->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '! Masuk sebagai Pelanggan.');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * 1-Click Quick Demo Login for instant testing.
     */
    public function quickLogin($role = 'owner')
    {
        if ($role === 'owner') {
            $user = User::where('role', 'owner')->first();
        } elseif ($role === 'sarah') {
            $user = User::where('email', 'sarah@gmail.com')->first();
        } else {
            // default tenant: Dimas Satria
            $user = User::where('email', 'dimas@gmail.com')->first() 
                 ?? User::where('role', 'tenant')->first();
        }

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            return $user->isOwner() ? redirect()->route('admin.dashboard') : redirect()->route('tenant.portal');
        }

        return redirect()->route('login');
    }

    /**
     * Show Register Page.
     * Accessible by all — controller shows the form even if already logged in.
     */
    public function showRegister()
    {
        $availableRooms = Room::where('status', 'available')->get();
        return view('auth.register', compact('availableRooms'));
    }

    /**
     * Handle Registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string',
            'role' => 'nullable|string|in:tenant,owner',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $role = $request->input('role', 'tenant');
        if (!in_array($role, ['tenant', 'owner'])) {
            $role = 'tenant';
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'phone' => $validated['phone'],
            'room_id' => ($role === 'tenant') ? ($validated['room_id'] ?? null) : null,
            'avatar' => $role === 'owner' ? 'assets/images/owner_avatar.jpg' : 'assets/images/tenant_avatar.jpg',
        ]);

        // Send Welcome email to tenant if registered as tenant
        if ($role === 'tenant') {
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeTenantMail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Welcome email failed: ' . $e->getMessage());
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isOwner()) {
            return redirect()->route('admin.dashboard')->with('success', 'Akun Admin berhasil dibuat! Selamat datang di Dashboard Kos Mas Robi.');
        }

        return redirect()->route('tenant.portal')->with('success', 'Akun Pelanggan berhasil dibuat! Selamat datang di Kos Mas Robi.');
    }

    /**
     * Handle Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout.');
    }
}
