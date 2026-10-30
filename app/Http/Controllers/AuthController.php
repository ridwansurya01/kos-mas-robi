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
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isOwner() ? redirect()->route('admin.dashboard') : redirect()->route('tenant.portal');
        }

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
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('tenant.portal');
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
     * Show Tenant Register Page.
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
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'tenant',
            'phone' => $validated['phone'],
            'room_id' => $validated['room_id'] ?? null,
            'avatar' => 'assets/images/tenant_avatar.jpg',
        ]);

        // Send Welcome email to customer's Gmail
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeTenantMail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('tenant.portal')->with('success', 'Akun berhasil dibuat! Selamat datang di Kos Mas Robi.');
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
