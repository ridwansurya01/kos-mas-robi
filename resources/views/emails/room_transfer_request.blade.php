<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Permohonan Pindah Kamar Baru</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $tenantName = $transfer->user ? $transfer->user->name : ($transfer->tenant ? $transfer->tenant->name : 'Penghuni');
      $tenantPhone = $transfer->user ? ($transfer->user->phone ?? '-') : ($transfer->tenant ? ($transfer->tenant->phone ?? '-') : '-');
      $curRoomNum = $transfer->currentRoom ? $transfer->currentRoom->number : '-';
      $curRoomLoc = $transfer->currentRoom ? $transfer->currentRoom->location : '-';
      $targetRoomNum = $transfer->targetRoom ? $transfer->targetRoom->number : '-';
      $targetRoomLoc = $transfer->targetRoom ? $transfer->targetRoom->location : '-';
      $targetRoomPrice = $transfer->targetRoom ? number_format($transfer->targetRoom->price, 0, ',', '.') : '-';
    @endphp

    <tr>
      <td style="background-color: #0284c7; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Notifikasi Pengelola: Permohonan Pindah Kamar
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Permohonan Mutasi Kamar Masuk dari Penghuni</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $recipient->name }} ({{ strtoupper($recipient->role) }})</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Terdapat permohonan kepindahan kamar baru yang diajukan oleh penghuni melalui Tenant Portal Mandiri:
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
                  <td style="font-size: 13px; color: #64748b;">WhatsApp Penghuni</td>
                  <td style="font-size: 14px; font-weight: 700; color: #0284c7; text-align: right;">{{ $tenantPhone }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kamar Asal Saat Ini</td>
                  <td style="font-size: 14px; font-weight: 700; color: #64748b; text-align: right;">Kamar #{{ $curRoomNum }} ({{ $curRoomLoc }})</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kamar Tujuan yang Diminta</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">Kamar #{{ $targetRoomNum }} ({{ $targetRoomLoc }}) - Rp {{ $targetRoomPrice }}/bln</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Rencana Tanggal Pindah</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">
                    {{ $transfer->preferred_date ? $transfer->preferred_date->format('d F Y') : 'Fleksibel / Secepatnya' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; background-color: #ffffff;">
              <div style="font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Alasan Permohonan:</div>
              <div style="font-size: 13px; color: #1e293b; line-height: 1.5; font-style: italic; background: #f1f5f9; padding: 10px 14px; border-radius: 8px;">
                "{{ $transfer->reason }}"
              </div>
            </td>
          </tr>
        </table>

        <p style="font-size: 13px; color: #64748b; line-height: 1.5;">
          Silakan buka Dashboard Pengelola pada tab Mutasi Kamar untuk memberikan keputusan (Setujui atau Tolak) serta memberikan catatan tindak lanjut.
        </p>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);">
            Buka Dashboard Manajemen Kos
          </a>
        </div>
      </td>
    </tr>

    <tr>
      <td style="background-color: #f8fafc; padding: 16px 28px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
        Email otomatis sistem manajemen {{ $setting->kos_name ?? 'Kos Mas Robi' }}.
      </td>
    </tr>
  </table>
</body>
</html>
