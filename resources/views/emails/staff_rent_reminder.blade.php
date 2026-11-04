<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $reminderTitle }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $badgeBg = '#0d9488';
      $badgeLabel = 'Pemberitahuan Staf Pengelola Kos';
      if ($reminderType === 'h-3') {
        $badgeBg = '#d97706';
        $badgeLabel = 'Jadwal H-3 Hari Menuju Jatuh Tempo';
      } elseif ($reminderType === 'h-1') {
        $badgeBg = '#ea580c';
        $badgeLabel = 'Jadwal H-1 Hari (Besok Jatuh Tempo)';
      } elseif ($reminderType === 'h-0') {
        $badgeBg = '#dc2626';
        $badgeLabel = 'Jadwal Hari H Batas Pembayaran Sewa';
      }
      $tenantName = $tenantUser ? $tenantUser->name : 'Penghuni #' . $room->number;
      $tenantPhone = $tenantUser ? ($tenantUser->phone ?? '-') : '-';
      $branchLabel = ($room->location === 'Lokasi 2') ? 'Kos Mas Robi 2 (Ringroad Exclusive)' : 'Kos Mas Robi 1 (Kampus Utama)';
    @endphp

    <tr>
      <td style="background-color: {{ $badgeBg }}; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        {{ $badgeLabel }}
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Monitoring Jadwal Pembayaran Sewa Penghuni</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $staff->name }} ({{ strtoupper($staff->role) }})</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Sistem menginformasikan jadwal jatuh tempo sewa kamar penghuni berikut yang membutuhkan pemantauan operasional kas masuk:
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Nama Penghuni</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">{{ $tenantName }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Nomor Kamar & Cabang</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">Kamar #{{ $room->number }} ({{ $branchLabel }})</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kontak WhatsApp Penghuni</td>
                  <td style="font-size: 14px; font-weight: 700; color: #0284c7; text-align: right;">{{ $tenantPhone }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Tanggal Jatuh Tempo</td>
                  <td style="font-size: 14px; font-weight: 800; color: #dc2626; text-align: right;">{{ $dueDateFormatted }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 16px 20px; background-color: #f0fdfa;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; font-weight: 700; color: #0a5661;">Nominal Tagihan Sewa</td>
                  <td style="font-size: 18px; font-weight: 900; color: #059669; text-align: right;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <div style="text-align: center; margin-top: 26px;">
          <a href="{{ url('/dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; font-weight: 800; font-size: 14px; padding: 12px 28px; border-radius: 12px; box-shadow: 0 4px 14px rgba(10, 86, 97, 0.28);">
            Buka Tab Tagihan Sewa di Dashboard
          </a>
        </div>
      </td>
    </tr>

    <tr>
      <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; font-size: 12px; color: #94a3b8;">
        Email otomatis sistem manajemen operasional {{ $setting->kos_name ?? 'Kos Mas Robi' }}.
      </td>
    </tr>
  </table>
</body>
</html>
