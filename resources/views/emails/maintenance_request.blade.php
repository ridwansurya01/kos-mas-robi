<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keluhan Servis Baru</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $roomNum = $ticket->room ? $ticket->room->number : '-';
      $roomLoc = $ticket->room ? $ticket->room->location : '-';
      $prio = strtolower($ticket->priority ?? 'normal');
      $prioBg = '#0d9488';
      $prioLabel = 'Normal';
      if ($prio === 'high' || $prio === 'tinggi' || $prio === 'darurat') {
        $prioBg = '#dc2626';
        $prioLabel = 'Tinggi / Mendesak';
      } elseif ($prio === 'medium' || $prio === 'sedang') {
        $prioBg = '#ea580c';
        $prioLabel = 'Sedang';
      }
    @endphp

    <tr>
      <td style="background-color: {{ $prioBg }}; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Tiket Servis Baru: Prioritas {{ $prioLabel }}
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Laporan Keluhan Perbaikan Masuk</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $recipient->name }} ({{ strtoupper($recipient->role) }})</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Terdapat laporan keluhan servis fasilitas kamar yang diajukan oleh penghuni:
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kode Tiket</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">{{ $ticket->ticket_code }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Nama Pelapor</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">{{ $ticket->reporter_name }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kamar & Lokasi</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">Kamar #{{ $roomNum }} ({{ $roomLoc }})</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Tanggal Pengajuan</td>
                  <td style="font-size: 14px; font-weight: 700; color: #0f172a; text-align: right;">{{ $ticket->report_date ? $ticket->report_date->format('d F Y') : date('d F Y') }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; background-color: #ffffff;">
              <div style="font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Deskripsi Kerusakan / Kendala:</div>
              <div style="font-size: 13px; color: #1e293b; line-height: 1.5; background: #fef2f2; border-left: 3px solid #ef4444; padding: 10px 14px; border-radius: 4px;">
                "{{ $ticket->issue_description }}"
              </div>
            </td>
          </tr>
        </table>

        <p style="font-size: 13px; color: #64748b; line-height: 1.5;">
          Mohon segera lakukan peninjauan dan perbarui status pengerjaan tiket di Dashboard manajemen operasional kos.
        </p>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);">
            Buka Tab Keluhan & Servis
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
