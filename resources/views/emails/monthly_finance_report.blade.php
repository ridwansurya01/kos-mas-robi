<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan Bulanan Konsolidasi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #334155;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
    @php
      $income = (float) ($reportData['total_income'] ?? 0);
      $expense = (float) ($reportData['total_expense'] ?? 0);
      $netProfit = (float) ($reportData['net_profit'] ?? ($income - $expense));
      $periodLabel = $reportData['period_label'] ?? date('F Y');
      $isSurplus = ($netProfit >= 0);
      $netColor = $isSurplus ? '#059669' : '#dc2626';
      $totalTransactions = $reportData['total_transactions'] ?? 0;
      $occupancyRate = $reportData['occupancy_rate'] ?? '100%';
    @endphp

    <tr>
      <td style="background-color: #042f2e; padding: 12px 24px; text-align: center; color: #5eead4; font-weight: 800; font-size: 13px; letter-spacing: 0.04em; text-transform: uppercase;">
        Laporan Keuangan Eksekutif (Khusus Role Owner)
      </td>
    </tr>

    <tr>
      <td style="background: linear-gradient(135deg, #071f24 0%, #0a5661 100%); padding: 26px 28px; text-align: center; color: #ffffff;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 800;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h1>
        <p style="margin: 4px 0 0; font-size: 13px; color: #99f6e4;">Rekapitulasi Arus Kas Bulanan Periode {{ $periodLabel }}</p>
      </td>
    </tr>

    <tr>
      <td style="padding: 30px 28px;">
        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin-top: 0;">Yth. {{ $owner->name }} (Pemilik Properti)</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
          Sistem otomatis telah merekapitulasi seluruh transaksi pendapatan sewa hunian dan pengeluaran operasional kos pada periode <strong>{{ $periodLabel }}</strong>. Berikut ringkasan performa finansial properti Anda:
        </p>

        <!-- Kartu KPI Finansial -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
          <tr>
            <td width="33%" style="padding: 4px;">
              <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 14px; text-align: center;">
                <div style="font-size: 11px; font-weight: 800; color: #166534; text-transform: uppercase;">Pemasukan Kas</div>
                <div style="font-size: 15px; font-weight: 900; color: #059669; margin-top: 4px;">Rp {{ number_format($income, 0, ',', '.') }}</div>
              </div>
            </td>
            <td width="33%" style="padding: 4px;">
              <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px; text-align: center;">
                <div style="font-size: 11px; font-weight: 800; color: #991b1b; text-transform: uppercase;">Pengeluaran</div>
                <div style="font-size: 15px; font-weight: 900; color: #dc2626; margin-top: 4px;">Rp {{ number_format($expense, 0, ',', '.') }}</div>
              </div>
            </td>
            <td width="33%" style="padding: 4px;">
              <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 12px; padding: 14px; text-align: center;">
                <div style="font-size: 11px; font-weight: 800; color: #0a5661; text-transform: uppercase;">Laba Bersih</div>
                <div style="font-size: 15px; font-weight: 900; color: {{ $netColor }}; margin-top: 4px;">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
              </div>
            </td>
          </tr>
        </table>

        <!-- Detail Breakdown Tabel -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; margin: 18px 0;">
          <tr>
            <td style="padding: 12px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Total Catatan Transaksi</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0f172a; text-align: right;">{{ $totalTransactions }} Transaksi</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 12px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Tingkat Okupansi Kamar</td>
                  <td style="font-size: 14px; font-weight: 800; color: #0a5661; text-align: right;">{{ $occupancyRate }}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 12px 20px; border-bottom: 1px solid #e2e8f0;">
              <table width="100%">
                <tr>
                  <td style="font-size: 13px; color: #64748b;">Status Saldo Bersih</td>
                  <td style="font-size: 14px; font-weight: 800; color: {{ $netColor }}; text-align: right;">
                    {{ $isSurplus ? 'SURPLUS BERSIH (POSITIF)' : 'DEFISIT ARUS KAS' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding: 14px 20px; background-color: #f0fdfa;">
              <div style="font-size: 12px; color: #0a5661; font-weight: 800; text-transform: uppercase; margin-bottom: 4px;">Akses Laporan Lengkap:</div>
              <div style="font-size: 13px; color: #334155; line-height: 1.5;">
                Anda dapat melihat kurva visualisasi analitika interaktif, buku kas kasir per tanggal, serta mengunduh berkas Laporan Keuangan format resmi PDF langsung dari panel Dashboard Owner.
              </div>
            </td>
          </tr>
        </table>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ url('/dashboard?tab=finances') }}" style="display: inline-block; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; text-decoration: none; padding: 12px 26px; border-radius: 10px; font-weight: 800; font-size: 14px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);">
            Buka Laporan Keuangan & Unduh PDF
          </a>
        </div>
      </td>
    </tr>

    <tr>
      <td style="background-color: #f8fafc; padding: 16px 28px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
        Email otomatis sistem manajemen {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Khusus otoritas Pemilik Kos (Owner).
      </td>
    </tr>
  </table>
</body>
</html>
