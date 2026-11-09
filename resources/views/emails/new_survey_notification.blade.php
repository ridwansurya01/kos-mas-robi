<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Permohonan Survey Kamar Baru</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    <tr>
      <td style="background-color: #0d9488; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Permohonan Survey Lokasi Baru Masuk
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Notifikasi Calon Penghuni Kost</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $recipient->name ?? 'Pengelola' }} ({{ strtoupper($recipient->role ?? 'Pengelola') }})</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Terdapat permohonan survey lokasi / kamar baru yang dikirimkan oleh calon penghuni melalui website kos:
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Nama Calon</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">{{ $survey->name }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Nomor WhatsApp / HP</td>
                  <td style="font-size: 14px; font-weight: 700; color: #1e293b; text-align: right;">{{ $survey->phone ?? '-' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Pekerjaan / Status</td>
                  <td style="font-size: 14px; color: #1e293b; text-align: right;">{{ $survey->job ?? '-' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Cabang / Lokasi Kost</td>
                  <td style="font-size: 14px; font-weight: 700; color: #0d9488; text-align: right;">{{ $survey->branch ?? 'Lokasi 1' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          @if (!empty($survey->room))
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Pilihan Kamar / Tipe</td>
                  <td style="font-size: 14px; font-weight: 600; color: #1e293b; text-align: right;">{{ $survey->room }}</td>
                </tr>
              </table>
            </td>
          </tr>
          @endif
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Jadwal Survey</td>
                  <td style="font-size: 14px; font-weight: 700; color: #1e293b; text-align: right;">{{ !empty($survey->survey_date) ? \Carbon\Carbon::parse($survey->survey_date)->format('d F Y') : 'Menyesuaikan' }}, {{ $survey->survey_time ?? '-' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Metode Kunjungan</td>
                  <td style="font-size: 14px; font-weight: 700; color: #1e293b; text-align: right;">{{ $survey->survey_method ?? 'Datang Langsung' }}</td>
                </tr>
              </table>
            </td>
          </tr>
          @if (!empty($survey->message))
          <tr>
            <td style="padding: 14px 20px;">
              <div style="font-size: 13px; color: #64748b; margin-bottom: 6px;">Catatan Tambahan:</div>
              <div style="font-size: 13.5px; color: #1e293b; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; line-height: 1.5;">
                {{ $survey->message }}
              </div>
            </td>
          </tr>
          @endif
        </table>

        <div style="text-align: center; margin: 28px 0 10px;">
          <a href="{{ route('admin.dashboard') }}" style="display: inline-block; background-color: #0a5661; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 8px; box-shadow: 0 2px 8px rgba(10,86,97,0.3);">
            Buka Dashboard Pengelola
          </a>
        </div>
      </td>
    </tr>

    <tr>
      <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; font-size: 12px; color: #94a3b8;">
        Email ini dikirim secara otomatis oleh Sistem Notifikasi {{ $setting->kos_name ?? 'Kos Mas Robi' }}.
      </td>
    </tr>
  </table>
</body>
</html>

