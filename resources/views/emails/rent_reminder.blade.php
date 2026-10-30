<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $reminderTitle }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    
    <!-- Top Alert Banner based on urgency -->
    @php
      $badgeBg = '#2563eb';
      $badgeLabel = 'Pengingat Pembayaran Sewa';
      if ($reminderType === 'h-14') {
        $badgeBg = '#0284c7';
        $badgeLabel = '🗓️ H-2 Minggu Menuju Jatuh Tempo';
      } elseif ($reminderType === 'h-7') {
        $badgeBg = '#2563eb';
        $badgeLabel = '📅 H-1 Minggu Menuju Jatuh Tempo';
      } elseif ($reminderType === 'h-3') {
        $badgeBg = '#d97706';
        $badgeLabel = '⚠️ H-3 Hari Menuju Jatuh Tempo';
      } elseif ($reminderType === 'h-1') {
        $badgeBg = '#ea580c';
        $badgeLabel = '🚨 H-1 Hari (Besok Jatuh Tempo)';
      } elseif ($reminderType === 'h-0') {
        $badgeBg = '#dc2626';
        $badgeLabel = '🔴 HARI H: Batas Waktu Pembayaran Sewa';
      }
    @endphp

    <tr>
      <td style="background-color: {{ $badgeBg }}; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        {{ $badgeLabel }}
      </td>
    </tr>

    <!-- Brand Header -->
    <tr>
      <td style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <div style="font-size: 28px; margin-bottom: 6px;">🏠</div>
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Pengingat Tagihan Sewa Hunian</p>
      </td>
    </tr>

    <!-- Body -->
    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $user->name }} 👋</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Kami ingin mengingatkan tagihan sewa kamar Anda di <strong>{{ $setting->kos_name ?? 'Kos Mas Robi' }}</strong>:
        </p>

        <!-- Invoice Card -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Unit Kamar</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">Kamar #{{ $room->number }} ({{ $room->type }})</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Tanggal Jatuh Tempo</td>
                  <td style="font-size: 14px; font-weight: 800; color: #2563eb; text-align: right;">{{ $dueDateFormatted }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 18px 20px; background-color: #eff6ff;">
              <table width="100%">
                <tr>
                  <td style="font-size: 14px; font-weight: 700; color: #1e40af;">Total Tagihan Sewa</td>
                  <td style="font-size: 20px; font-weight: 900; color: #1e40af; text-align: right;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <!-- Payment Instructions -->
        <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px; margin: 20px 0;">
          <h4 style="margin: 0 0 10px; font-size: 13px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.04em;">
            💳 Pilihan Metode Pembayaran Resmi:
          </h4>
          <ul style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.8; color: #334155;">
            <li><strong>Bank Central Asia (BCA):</strong> <code>8830192831</code> a.n. Robi Suryanto</li>
            <li><strong>Bank Mandiri:</strong> <code>1370019283100</code> a.n. Robi Suryanto</li>
            <li><strong>Bank BNI:</strong> <code>0928318830</code> a.n. Robi Suryanto</li>
            <li><strong>Bank BRI:</strong> <code>012301092831502</code> a.n. Robi Suryanto</li>
            <li><strong>QRIS Instant:</strong> Scan QRIS Kos Mas Robi lewat aplikasi m-banking / e-wallet.</li>
            <li><strong>Tunai (Cash):</strong> Bayar langsung tunai kepada Mas Robi di lantai 1.</li>
          </ul>
        </div>

        <p style="font-size: 13px; line-height: 1.6; color: #475569;">
          Setelah melakukan pembayaran, mohon unggah <strong>Foto Bukti Transfer</strong> Anda di portal agar pembayaran dapat langsung dikonfirmasi dan kuitansi digital diterbitkan.
        </p>

        <!-- CTA Button -->
        <div style="text-align: center; margin: 26px 0;">
          <a href="{{ url('/portal') }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 13px 32px; border-radius: 12px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);">
            💳 Buka Portal & Bayar Sekarang →
          </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

        <p style="font-size: 12px; color: #94a3b8; line-height: 1.5; margin: 0;">
          Jika Anda telah melunasi tagihan ini, abaikan pesan pengingat ini. Untuk konfirmasi manual atau pertanyaan, silakan chat Mas Robi di WhatsApp: <strong>{{ $setting->owner_phone ?? '081234567890' }}</strong>.
        </p>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background-color: #f8fafc; padding: 16px 28px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
        Pesan pengingat otomatis ini dikirim ke alamat Gmail Anda ({{ $user->email }}) dari sistem {{ $setting->kos_name ?? 'Kos Mas Robi' }}.
      </td>
    </tr>
  </table>
</body>
</html>

