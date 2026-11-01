<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Room;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show Login Page.
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
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || config('services.google.client_id') === 'YOUR_GOOGLE_CLIENT_ID') {
            return redirect()->route('login')->withErrors([
                'email' => 'Fitur Google Login siap digunakan! Untuk menghubungkan ke Google Cloud Console, tambahkan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada file .env Anda.'
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth Callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google gagal. Silakan coba lagi.']);
        }

        // Find or create user
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if ($user) {
            // Update google info if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id'     => $googleUser->getId(),
                    'google_token'  => $googleUser->token,
                    'google_avatar' => $googleUser->getAvatar(),
                ]);
            }
        } else {
            // Create new tenant account from Google
            $user = User::create([
                'name'          => $googleUser->getName(),
                'email'         => $googleUser->getEmail(),
                'google_id'     => $googleUser->getId(),
                'google_token'  => $googleUser->token,
                'google_avatar' => $googleUser->getAvatar(),
                'role'          => 'tenant',
                'phone'         => '',
                'avatar'        => $googleUser->getAvatar() ?? 'assets/images/tenant_avatar.jpg',
                'password'      => null,
            ]);

            // Welcome email
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeTenantMail($user));
            } catch (\Exception $e) {
                Log::warning('Welcome email (Google) failed: ' . $e->getMessage());
            }
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if ($user->isOwner()) {
            return redirect()->route('admin.dashboard')->with('success', 'Berhasil masuk dengan akun Google sebagai Admin.');
        }

        return redirect()->route('tenant.portal')->with('success', 'Berhasil masuk dengan akun Google, Selamat datang ' . $user->name . '!');
    }

    /**
     * Show Forgot Password form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send new password directly to user's registered Gmail.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Alamat email tidak ditemukan dalam sistem kami.'])->onlyInput('email');
        }

        $newPassword = 'Kmr' . rand(1000, 9999) . '@' . Str::random(2);
        $user->password = Hash::make($newPassword);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\NewPasswordMail($user, $newPassword, 'lupa_password'));
        } catch (\Exception $e) {
            Log::warning('Send new password to email failed: ' . $e->getMessage());
        }

        return back()->with('status', 'Password baru telah berhasil dibuat dan dikirim ke alamat Gmail Anda (' . $user->email . '). Silakan cek kotak masuk/spam Anda.');
    }

    /**
     * Show Reset Password form.
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle Reset Password submission.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
        }

        return back()->withErrors(['email' => match($status) {
            Password::INVALID_TOKEN => 'Token reset tidak valid atau sudah kedaluwarsa.',
            Password::INVALID_USER  => 'Email tidak ditemukan.',
            default                 => 'Gagal mereset password.',
        }]);
    }

    /**
     * 1-Click Quick Demo Login.
     */
    public function quickLogin($role = 'owner')
    {
        if ($role === 'owner') {
            $user = User::where('role', 'owner')->first();
        } elseif ($role === 'sarah') {
            $user = User::where('email', 'sarah@gmail.com')->first();
        } else {
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'required|string',
            'role'     => 'nullable|string|in:tenant,owner',
            'room_id'  => 'nullable|exists:rooms,id',
        ]);

        $role = $request->input('role', 'tenant');
        if (!in_array($role, ['tenant', 'owner'])) {
            $role = 'tenant';
        }

        $user = User::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'    => $role,
            'phone'   => $validated['phone'],
            'room_id' => ($role === 'tenant') ? ($validated['room_id'] ?? null) : null,
            'avatar'  => $role === 'owner' ? 'assets/images/owner_avatar.jpg' : 'assets/images/tenant_avatar.jpg',
        ]);

        if ($role === 'tenant') {
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeTenantMail($user));
            } catch (\Exception $e) {
                Log::warning('Welcome email failed: ' . $e->getMessage());
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isOwner()) {
            return redirect()->route('admin.dashboard')->with('success', 'Akun Admin berhasil dibuat!');
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
