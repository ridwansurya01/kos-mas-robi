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
     * Send new password directly to user's registered Gmail (Legacy compatibility).
     */
    public function sendResetLink(Request $request)
    {
        return $this->sendForgotOtp($request);
    }

    /**
     * Send 6-digit OTP to user's registered Gmail for Forgot Password.
     */
    public function sendForgotOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat Gmail tidak terdaftar dalam sistem kami.',
            ], 404);
        }

        // Jeda waktu pengiriman ulang OTP: minimal 60 detik
        if ($user->password_otp_sent_at) {
            $secondsSinceLast = abs(now()->diffInSeconds($user->password_otp_sent_at));
            if ($secondsSinceLast < 60) {
                $cooldown = (int) max(1, 60 - $secondsSinceLast);
                return response()->json([
                    'success' => false,
                    'message' => "Mohon tunggu {$cooldown} detik sebelum meminta pengiriman ulang OTP.",
                    'cooldown' => $cooldown,
                ], 429);
            }
        }

        // Generate 6 digit OTP acak
        $otpCode = sprintf('%06d', random_int(100000, 999999));

        // Simpan OTP ke database: berlaku 5 menit, reset percobaan ke 0
        $user->password_otp = $otpCode;
        $user->password_otp_expires_at = now()->addMinutes(5);
        $user->password_otp_attempts = 0;
        $user->password_otp_sent_at = now();
        $user->password_otp_verified_at = null;
        $user->save();

        // Kirim email OTP ke Gmail terdaftar
        try {
            Mail::to($user->email)->send(new \App\Mail\OtpPasswordMail($user, $otpCode));
        } catch (\Exception $e) {
            Log::warning('Send OTP for forgot password failed: ' . $e->getMessage());
        }

        Log::info("Kode OTP Lupa Password untuk {$user->email}: {$otpCode}");

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6 digit telah dikirim ke Gmail Anda (' . $user->email . '). Silakan cek kotak masuk atau spam.',
            'email' => $user->email,
            'cooldown' => 60,
            'otp_debug' => config('mail.default') === 'log' ? $otpCode : null,
        ]);
    }

    /**
     * Verify 6-digit OTP for Forgot Password.
     */
    public function verifyForgotOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Alamat email tidak ditemukan.'], 404);
        }

        if (empty($user->password_otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada permintaan OTP aktif. Silakan minta kode OTP terlebih dahulu.',
            ], 400);
        }

        // Cek kedaluwarsa (berlaku 5 menit)
        if (!$user->password_otp_expires_at || now()->isAfter($user->password_otp_expires_at)) {
            $user->password_otp = null;
            $user->save();
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa (melebihi 5 menit). Silakan minta kode OTP baru.',
            ], 422);
        }

        // Cek maksimal 5 percobaan
        if ($user->password_otp_attempts >= 5) {
            $user->password_otp = null;
            $user->save();
            return response()->json([
                'success' => false,
                'message' => 'Batas percobaan OTP telah terlampaui (maksimal 5 kali salah). Silakan minta kode OTP baru.',
            ], 422);
        }

        // Tambah counter percobaan
        $user->password_otp_attempts += 1;
        $user->save();

        // Validasi kecocokan kode OTP
        if (trim($request->otp) !== trim($user->password_otp)) {
            $remaining = max(0, 5 - $user->password_otp_attempts);
            if ($remaining === 0) {
                $user->password_otp = null;
                $user->save();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP salah. Batas maksimal 5 percobaan telah habis. Silakan minta OTP baru.',
                    'remaining' => 0,
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali percobaan.",
                'remaining' => $remaining,
            ], 422);
        }

        // OTP valid: hanguskan OTP (hanya 1 kali pakai) dan tandai terverifikasi
        $user->password_otp = null;
        $user->password_otp_verified_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil diverifikasi! Silakan buat password baru Anda.',
        ]);
    }

    /**
     * Set new password after OTP verification for Forgot Password.
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok. Pastikan kedua kolom sama.',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Alamat email tidak ditemukan.'], 404);
        }

        // Ketentuan keamanan: OTP wajib diverifikasi sebelum formulir password baru dapat diakses / disimpan
        if (!$user->password_otp_verified_at || now()->diffInMinutes($user->password_otp_verified_at) > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi OTP telah kedaluwarsa atau belum diverifikasi. Silakan ulangi proses lupa password.',
            ], 403);
        }

        // Simpan password baru dengan hashing
        $user->password = Hash::make($request->password);
        $user->password_otp_verified_at = null; // reset sesi verifikasi
        $user->save();

        Log::info("Password akun {$user->email} berhasil diubah melalui OTP Lupa Password.");

        return response()->json([
            'success' => true,
            'message' => 'Password Berhasil Diubah! Silakan login sekarang dengan password baru Anda.',
        ]);
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
