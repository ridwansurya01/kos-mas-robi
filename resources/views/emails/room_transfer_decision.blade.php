<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Status Permohonan Pindah Kamar</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $isApproved = ($transfer->status === 'approved');
      $headerBg = $isApproved ? '#059669' : '#dc2626';
      $headerTitle = $isApproved ? 'Permohonan Pindah Kamar Disetujui' : 'Permohonan Pindah Kamar Belum Disetujui';
      $badgeText = $isApproved ? 'Status: Disetujui' : 'Status: Ditolak';
      $targetRoomNum = $transfer->targetRoom ? $transfer->targetRoom->number : '-';
      $targetRoomLoc = $transfer->targetRoom ? $transfer->targetRoom->location : '-';
      $targetRoomPrice = $transfer->targetRoom ? number_format($transfer->targetRoom->price, 0, ',', '.') : '-';
    @endphp

    <tr>
      <td style="background-color: {{ $headerBg }}; padding: 12px 24px; text-align: center; color: #ffffff; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        {{ $badgeText }}
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">{{ $headerTitle }}</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Halo, {{ $tenantUser->name }}</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          @if($isApproved)
            Kabar baik! Pengajuan kepindahan kamar Anda ke <strong>Kamar #{{ $targetRoomNum }}</strong> telah resmi <strong>DISETUJUI</strong> oleh manajemen pengelola. Data kamar di profil portal mandiri Anda telah diperbarui.
          @else
            Mohon maaf, pengajuan kepindahan kamar Anda ke <strong>Kamar #{{ $targetRoomNum }}</strong> saat ini <strong>BELUM DAPAT DISETUJUI</strong> oleh pengelola.
          @endif
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Kamar Tujuan</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">Kamar #{{ $targetRoomNum }} ({{ $targetRoomLoc }})</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Biaya Sewa Kamar Baru</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">Rp {{ $targetRoomPrice }} / bulan</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Status Keputusan</td>
                  <td style="font-size: 14px; font-weight: 800; color: {{ $headerBg }}; text-align: right;">
                    {{ $isApproved ? 'DISETUJUI' : 'DITOLAK' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          @if(!empty($transfer->admin_note))
          <tr>
            <td style="padding: 14px 20px; background-color: #ffffff;">
              <div style="font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Catatan Pengelola:</div>
              <div style="font-size: 13px; color: #1e293b; line-height: 1.5; background: #f0fdfa; border-left: 3px solid #0d9488; padding: 10px 14px; border-radius: 4px;">
                "{{ $transfer->admin_note }}"
              </div>
            </td>
          </tr>
          @endif
        </table>

        @if($isApproved)
          <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 18px; margin-top: 16px;">
            <p style="margin: 0; font-size: 13px; color: #065f46; line-height: 1.5;">
              Silakan berkoordinasi langsung dengan staf Admin kos mengenai jadwal serah terima kunci dan pemindahan barang-barang pribadi Anda.
            </p>
          </div>
        @else
          <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px 18px; margin-top: 16px;">
            <p style="margin: 0; font-size: 13px; color: #991b1b; line-height: 1.5;">
              Apabila terdapat pertanyaan lebih lanjut, Anda dapat menghubungi staf Admin kos melalui WhatsApp atau mengajukan kembali pilihan kamar lainnya melalui portal.
            </p>
          </div>
        @endif

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
