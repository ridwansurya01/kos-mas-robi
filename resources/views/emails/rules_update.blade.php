<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembaruan Tata Tertib Kos</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    <tr>
      <td style="background-color: #0a5661; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Pengumuman Manajemen: Tata Tertib Hunian
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Pembaruan Peraturan & Kenyamanan Bersama</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $tenantUser->name }}</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Dalam rangka menjaga ketertiban, keamanan, dan kebersihan lingkungan hunian kos bersama, pihak pengelola memperbarui tata tertib hunian sebagai berikut:
        </p>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 22px; margin: 20px 0;">
          <ol style="margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.8; color: #1e293b;">
            @forelse($rules as $rule)
              <li style="margin-bottom: 8px;">{{ $rule }}</li>
            @empty
              <li>Harap mematuhi seluruh peraturan operasional kos yang berlaku.</li>
            @endforelse
          </ol>
        </div>

        <div style="background-color: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 14px 18px;">
          <p style="margin: 0; font-size: 13px; color: #0f766e; line-height: 1.5;">
            Peraturan ini berlaku efektif untuk seluruh penghuni demi menciptakan suasana tempat tinggal yang aman, nyaman, dan harmonis. Terima kasih atas kerja sama dan kepedulian Anda.
          </p>
        </div>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/portal') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);">
            Buka Portal Mandiri Penghuni
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
