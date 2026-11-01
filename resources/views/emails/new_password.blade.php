<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Password Baru Akun Kos Mas Robi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px;">
  <div style="max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    
    {{-- Header --}}
    <div style="background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); padding: 32px 28px; text-align: center; color: #ffffff;">
      <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.02em;">Kos Mas Robi</h1>
      <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.9;">Smart Kost Management System</p>
    </div>

    {{-- Content --}}
    <div style="padding: 32px 28px; color: #334155;">
      <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0;">Halo, {{ $user->name }}!</h2>
      
      @if($source === 'profile')
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Anda telah meminta pembuatan password baru melalui <strong>Profil Pelanggan</strong>. Berikut adalah password baru untuk akun Kos Mas Robi Anda:
        </p>
      @else
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Kami menerima permintaan reset password untuk akun Anda. Berikut adalah password baru yang telah dibuat secara otomatis:
        </p>
      @endif

      {{-- Password Box --}}
      <div style="background: #f0fdfa; border: 1.5px dashed #0e7482; border-radius: 14px; padding: 18px; text-align: center; margin: 24px 0;">
        <span style="display: block; font-size: 12px; font-weight: 700; color: #0a5661; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Password Baru Anda</span>
        <code style="font-size: 24px; font-weight: 800; color: #0a5661; letter-spacing: 2px; font-family: monospace;">{{ $newPassword }}</code>
      </div>

      <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 12px 16px; font-size: 13px; color: #92400e; line-height: 1.5; margin-bottom: 24px;">
        ⚠️ <strong>Tips Keamanan:</strong> Demi keamanan akun Anda, segera login dan ubah kata sandi ini melalui menu Profil di portal penghuni atau panel admin.
      </div>

      <div style="text-align: center;">
        <a href="{{ route('login') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 12px; box-shadow: 0 4px 12px rgba(14,116,130,0.3);">
          Login ke Akun Anda &rarr;
        </a>
      </div>
    </div>

    {{-- Footer --}}
    <div style="background: #f8fafc; padding: 18px 28px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
      Email ini dikirim secara otomatis oleh sistem Kos Mas Robi. Jangan berikan password ini kepada siapa pun.
    </div>
  </div>
</body>
</html>
