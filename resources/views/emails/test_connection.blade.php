<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Uji Coba Koneksi Gmail</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    <tr>
      <td style="background-color: #10b981; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Koneksi Gmail SMTP Berhasil Terhubung
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Integrasi Layanan Notifikasi Email</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo Pengelola Kos,</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Pesan ini adalah email konfirmasi uji coba otomatis. Akun Gmail Anda telah berhasil dikonfigurasi dan siap mengirimkan notifikasi aktivitas ke seluruh pengguna terdaftar di website.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Pengirim (Gmail)</td>
                  <td style="font-size: 14px; font-weight: 700; color: #0a5661; text-align: right;">{{ $senderEmail ?? '-' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Penerima Uji Coba</td>
                  <td style="font-size: 14px; font-weight: 700; color: #1e293b; text-align: right;">{{ $targetEmail ?? '-' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Waktu Pengujian</td>
                  <td style="font-size: 14px; color: #1e293b; text-align: right;">{{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <p style="font-size: 13.5px; line-height: 1.6; color: #475569;">
          Mulai saat ini, seluruh aktivitas sistem seperti permintaan survey kamar, pengingat jatuh tempo sewa, pengajuan keluhan servis kamar, pengajuan pindah kamar, dan kode OTP lupa password akan terkirim secara otomatis.
        </p>
      </td>
    </tr>

    <tr>
      <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; font-size: 12px; color: #94a3b8;">
        Email uji coba dari sistem konfigurasi {{ $setting->kos_name ?? 'Kos Mas Robi' }}.
      </td>
    </tr>
  </table>
</body>
</html>

