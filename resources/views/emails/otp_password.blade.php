<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kode OTP Ganti Password — Kos Mas Robi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px;">
  <div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    
    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); padding: 32px 28px; text-align: center; color: #ffffff;">
      <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.02em;">Kos Mas Robi</h1>
      <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.9;">Smart Kost Management System</p>
    </div>

    {{-- Content --}}
    <div style="padding: 32px 28px; color: #334155;">
      <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0;">Halo, {{ $user->name }}!</h2>
      
      <p style="font-size: 14px; line-height: 1.6; color: #475569;">
        Kami menerima permintaan untuk mengganti kata sandi akun pelanggan Anda di <strong>Kos Mas Robi</strong>. Gunakan kode verifikasi OTP (One-Time Password) berikut untuk melanjutkan:
      </p>

      {{-- OTP Code Box --}}
      <div style="background: #f0fdfa; border: 2px dashed #0e7482; border-radius: 16px; padding: 22px; text-align: center; margin: 24px 0;">
        <span style="display: block; font-size: 12px; font-weight: 700; color: #0a5661; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">KODE VERIFIKASI OTP</span>
        <div style="font-size: 36px; font-weight: 900; color: #0a5661; letter-spacing: 8px; font-family: monospace; line-height: 1.2;">{{ $otpCode }}</div>
        <span style="display: block; font-size: 12px; color: #0e7482; font-weight: 600; margin-top: 8px;">⏱️ Berlaku selama 5 menit</span>
      </div>

      <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 12px 16px; font-size: 13px; color: #92400e; line-height: 1.5; margin-bottom: 20px;">
        🔒 <strong>Ketentuan Keamanan:</strong>
        <ul style="margin: 6px 0 0; padding-left: 18px; font-size: 12px;">
          <li>Kode OTP hanya berlaku selama <strong>5 menit</strong> dan hanya dapat digunakan <strong>1 kali</strong>.</li>
          <li>Batas maksimal percobaan verifikasi adalah <strong>5 kali</strong>.</li>
          <li>Jangan pernah membagikan kode OTP ini kepada siapa pun, termasuk pihak yang mengaku pengelola kos.</li>
        </ul>
      </div>

      <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0;">
        Jika Anda tidak merasa melakukan permintaan ganti password ini, abaikan email ini. Akun Anda tetap aman.
      </p>
    </div>

    {{-- Footer --}}
    <div style="background: #f8fafc; padding: 16px 28px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
      Email otomatis dikirim oleh sistem Kos Mas Robi. Harap tidak membalas email ini.
    </div>
  </div>
</body>
</html>
