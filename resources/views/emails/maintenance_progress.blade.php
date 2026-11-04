<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Update Status Keluhan Servis</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $isDone = ($ticket->status === 'done');
      $headerBg = $isDone ? '#059669' : '#0284c7';
      $badgeText = $isDone ? 'Status: Telah Selesai Dikerjakan' : 'Status: Telah Dilaksanakan / Sedang Dikerjakan';
      $headerSub = $isDone ? 'Perbaikan Fasilitas Telah Rampung Ditangani' : 'Teknisi Sedang Menangani Laporan Anda';
      $roomNum = $ticket->room ? $ticket->room->number : '-';
    @endphp

    <tr>
      <td style="background-color: {{ $headerBg }}; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        {{ $badgeText }}
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">{{ $headerSub }}</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $tenantUser->name }}</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          @if($isDone)
            Kabar gembira! Laporan perbaikan fasilitas kamar Anda dengan kode tiket <strong>{{ $ticket->ticket_code }}</strong> telah <strong>SELESAI DITANGANI</strong> oleh tim teknisi pengelola kos.
          @else
            Laporan perbaikan fasilitas kamar Anda dengan kode tiket <strong>{{ $ticket->ticket_code }}</strong> saat ini <strong>TELAH DILAKSANAKAN / SEDANG DALAM PROSES PENANGANAN</strong> oleh tim pemeliharaan kos.
          @endif
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
                  <td style="font-size: 13px; color: #64748b;">Lokasi Kamar</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">Kamar #{{ $roomNum }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Status Terkini</td>
                  <td style="font-size: 14px; font-weight: 800; color: {{ $headerBg }}; text-align: right;">
                    {{ $isDone ? 'TELAH SELESAI' : 'SEDANG DIKERJAKAN' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; background-color: #ffffff;">
              <div style="font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Uraian Keluhan:</div>
              <div style="font-size: 13px; color: #1e293b; line-height: 1.5; font-style: italic; background: #f8fafc; padding: 10px 14px; border-radius: 8px;">
                "{{ $ticket->issue_description }}"
              </div>
            </td>
          </tr>
        </table>

        @if($isDone)
          <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 18px; margin-top: 16px;">
            <p style="margin: 0; font-size: 13px; color: #065f46; line-height: 1.5;">
              Mohon periksa fasilitas terkait di kamar Anda. Apabila masih terdapat kendala lanjutan, Anda dapat menghubungi staf pengelola melalui WhatsApp atau membuat tiket baru melalui portal.
            </p>
          </div>
        @else
          <div style="background-color: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 14px 18px; margin-top: 16px;">
            <p style="margin: 0; font-size: 13px; color: #0f766e; line-height: 1.5;">
              Teknisi kami sedang berusaha menyelesaikan perbaikan ini dengan cepat demi kenyamanan istirahat Anda di hunian kos.
            </p>
          </div>
        @endif

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/portal') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);">
            Lihat Tiket di Portal Mandiri
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
