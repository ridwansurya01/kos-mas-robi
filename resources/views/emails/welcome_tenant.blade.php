<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Selamat Datang di {{ $setting->kos_name ?? 'Kos Mas Robi' }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    <!-- Header -->
    <tr>
      <td style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 28px; text-align: center; color: #ffffff;">
        <div style="font-size: 32px; margin-bottom: 8px;">🏠</div>
        <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.02em;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 6px 0 0; font-size: 13px; color: #94a3b8;">Sistem Manajemen & Portal Layanan Anak Kos</p>
      </td>
    </tr>

    <!-- Body -->
    <tr>
      <td style="padding: 32px 28px;">
        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $user->name }}! 👋</h2>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Selamat datang dan terima kasih telah mendaftarkan akun Anda di <strong>{{ $setting->kos_name ?? 'Kos Mas Robi' }}</strong>. Akun Gmail Anda (<strong>{{ $user->email }}</strong>) telah berhasil tersambung ke sistem kami.
        </p>

        <!-- Information Box -->
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin: 20px 0;">
          <h3 style="margin: 0 0 10px; font-size: 14px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.05em;">
            ✨ Fitur & Pengingat Otomatis Aktif
          </h3>
          <ul style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.7; color: #334155;">
            <li><strong>Pengingat Pembayaran Sewa ke Gmail:</strong> Anda akan otomatis menerima notifikasi pengingat pembayaran pada <strong>H-2 Minggu, H-1 Minggu, H-3 Hari, H-1 Hari</strong>, dan <strong>Hari H</strong> tanggal jatuh tempo.</li>
            <li><strong>Pilihan Pembayaran Lengkap:</strong> Transfer BCA, Mandiri, BNI, BRI, QRIS Instant, maupun Tunai (Cash).</li>
            <li><strong>Unggah Bukti Transfer:</strong> Konfirmasi pembayaran dan unduh kuitansi digital langsung dari portal.</li>
            <li><strong>Custom Interior:</strong> Ajukan penataan furnitur kamar impian dengan budget fleksibel.</li>
          </ul>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Untuk masuk ke akun dan mengelola kamar Anda, silakan klik tombol di bawah ini:
        </p>

        <div style="text-align: center; margin: 26px 0;">
          <a href="{{ url('/portal') }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
            Buka Portal Anak Kos →
          </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 26px 0;">

        <p style="font-size: 12px; color: #94a3b8; line-height: 1.5; margin: 0;">
          Butuh bantuan pengelola? Hubungi Mas Robi via WhatsApp di <strong>{{ $setting->owner_phone ?? '081234567890' }}</strong>.
        </p>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background-color: #f8fafc; padding: 18px 28px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
        &copy; {{ date('Y') }} {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Seluruh hak cipta dilindungi.
      </td>
    </tr>
  </table>
</body>
</html>

