@extends('layouts.app')
@section('title', 'Lupa Password - Kos Mas Robi')
@section('content')
<style>
  .auth-viewport {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 16px;
    background: #f4fbfb;
    overflow: hidden;
    font-family: inherit;
  }
  .auth-ambient-orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
  .orb-teal-1 { width: 480px; height: 480px; top: -120px; left: -100px; background: radial-gradient(circle, rgba(14,116,130,0.28) 0%, transparent 70%); animation: orbFloat1 14s infinite ease-in-out alternate; }
  .orb-teal-2 { width: 420px; height: 420px; bottom: -100px; right: -80px; background: radial-gradient(circle, rgba(10,86,97,0.22) 0%, transparent 70%); animation: orbFloat2 16s infinite ease-in-out alternate; }
  @keyframes orbFloat1 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(60px,70px) scale(1.08); } }
  @keyframes orbFloat2 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-50px,-60px) scale(1.1); } }
  .auth-card {
    position: relative; z-index: 10; width: 100%; max-width: 440px;
    background: rgba(255,255,255,0.94); backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.85); border-radius: 32px; padding: 38px 34px;
    box-shadow: 0 24px 60px -15px rgba(10,86,97,0.18), 0 8px 24px -6px rgba(15,23,42,0.06);
    animation: cardEntrance 0.7s cubic-bezier(0.16,1,0.3,1) both;
  }
  @keyframes cardEntrance { from { opacity: 0; transform: translateY(28px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .auth-logo-badge {
    width: 60px; height: 60px; margin: 0 auto 14px; border-radius: 20px;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    display: flex; align-items: center; justify-content: center; color: #fff;
    box-shadow: 0 10px 25px rgba(14,116,130,0.38);
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1);
    text-decoration: none;
  }
  .auth-logo-badge:hover { transform: translateY(-3px) rotate(4deg) scale(1.05); }
  .auth-field-label { display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 7px; }
  .auth-input-wrapper { position: relative; display: flex; align-items: center; }
  .auth-input-icon { position: absolute; left: 14px; width: 20px; height: 20px; color: #94a3b8; pointer-events: none; transition: color 0.25s; }
  .auth-input-control {
    width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
    padding: 13px 16px 13px 44px; font-size: 0.92rem; color: #0f172a;
    transition: all 0.25s; box-sizing: border-box; font-family: inherit;
  }
  .auth-input-control:focus { background: #fff; border-color: #0e7482; box-shadow: 0 0 0 4px rgba(14,116,130,0.14); outline: none; }
  .auth-btn-primary {
    position: relative; width: 100%; padding: 14px 20px; font-size: 0.96rem; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #0a5661, #0e7482); border: none; border-radius: 16px;
    cursor: pointer; box-shadow: 0 8px 22px rgba(14,116,130,0.35); transition: all 0.25s; overflow: hidden;
    margin-top: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  }
  .auth-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(14,116,130,0.45); }
  .auth-btn-primary::after { content: ''; position: absolute; top: 0; left: -100%; width: 60%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transform: skewX(-20deg); animation: btnShimmer 5s infinite; }
  @keyframes btnShimmer { 0%,75% { left: -100%; } 100% { left: 160%; } }
  .auth-alert-error { background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
  .auth-alert-success { background: #f0fdfa; border: 1.5px solid #99f6e4; color: #0f766e; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }

  @media (max-width: 480px) {
    .auth-viewport { padding: 16px 12px; }
    .auth-card { padding: 26px 18px; border-radius: 24px; }
    .auth-input-control { font-size: 16px; }
  }
</style>

<div class="auth-viewport">
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-teal-2"></div>

  <div class="auth-card">
    <div style="text-align: center; margin-bottom: 24px;">
      <a href="{{ route('home') }}" class="auth-logo-badge" title="Kembali ke Beranda">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg>
      </a>
      <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; letter-spacing: -0.02em;">Lupa Password?</h2>
      <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.5;" id="fpCardSubtitle">
        Reset password Anda melalui kode verifikasi OTP 6 digit yang dikirim ke Gmail terdaftar.
      </p>
    </div>

    {{-- Pesan Notifikasi Error / Sukses Dinamis --}}
    <div id="fpAlertError" class="auth-alert-error" style="display: none;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span id="fpAlertErrorText"></span>
    </div>

    <div id="fpAlertSuccess" class="auth-alert-success" style="display: none;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <span id="fpAlertSuccessText"></span>
    </div>

    {{-- =====================================================================
         LANGKAH 1: MASUKKAN EMAIL TERDAFTAR
         ===================================================================== --}}
    <div id="fpStepEmail">
      <div style="margin-bottom: 20px;">
        <label for="fpEmail" class="auth-field-label">Alamat Gmail Terdaftar</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="fpEmail" name="email" class="auth-input-control" placeholder="nama@gmail.com" required autofocus>
        </div>
      </div>

      <button type="button" id="btnSendOtp" class="auth-btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        <span>Kirim Kode OTP ke Gmail</span>
      </button>
    </div>

    {{-- =====================================================================
         LANGKAH 2: MASUKKAN & VERIFIKASI KODE OTP
         ===================================================================== --}}
    <div id="fpStepOtp" style="display: none;">
      <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 16px; padding: 14px 16px; margin-bottom: 18px; text-align: center;">
        <span style="font-size: 0.74rem; font-weight: 800; color: #0a5661; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Kode OTP Terkirim!</span>
        <p style="margin: 0; font-size: 0.8rem; color: #0e7482; line-height: 1.45;">
          Kode OTP 6 digit telah dikirim ke <strong id="fpDisplayEmail">-</strong>. Berlaku selama <strong>5 menit</strong>.
        </p>
      </div>

      <div style="margin-bottom: 18px;">
        <label for="fpOtpCode" class="auth-field-label" style="text-align: center; display: block;">
          Masukkan 6 Digit Kode OTP:
        </label>
        <input type="text" id="fpOtpCode" maxlength="6" inputmode="numeric" pattern="[0-9]*" class="auth-input-control" placeholder="000000"
               style="font-family: monospace; font-size: 1.6rem; font-weight: 900; letter-spacing: 8px; text-align: center; height: 52px; padding: 0 10px; background: #ffffff; border: 2px solid #0e7482;" autocomplete="one-time-code">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 0.74rem; color: #64748b;">
          <span>Sisa kesempatan: <strong id="fpRemainingAttempts" style="color: #0a5661;">5</strong> kali</span>
          <span id="fpTimerBadge" style="font-weight: 700; color: #0e7482;">⏱️ Kirim Ulang: <span id="fpTimerSeconds">60</span>s</span>
        </div>
      </div>

      <button type="button" id="btnVerifyOtp" class="auth-btn-primary" style="margin-bottom: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Verifikasi Kode OTP</span>
      </button>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
        <button type="button" id="btnChangeEmail" style="background: none; border: none; color: #64748b; font-size: 0.78rem; font-weight: 700; cursor: pointer; padding: 0;">
          &larr; Ganti Email
        </button>
        <button type="button" id="btnResendOtp" disabled style="background: none; border: none; color: #94a3b8; font-size: 0.78rem; font-weight: 700; cursor: not-allowed; padding: 0;">
          Kirim Ulang OTP
        </button>
      </div>
    </div>

    {{-- =====================================================================
         LANGKAH 3: FORMULIR PASSWORD BARU (DIVERIFIKASI SENDIRI OLEH PENGGUNA)
         ===================================================================== --}}
    <div id="fpStepNewPassword" style="display: none;">
      <div style="background: #f0fdfa; border: 1.5px solid #a7f3d0; border-radius: 14px; padding: 12px 14px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #10b981; color: #fff; font-size: 0.75rem;">✓</span>
        <span style="font-size: 0.78rem; font-weight: 800; color: #065f46;">OTP Berhasil Diverifikasi! Silakan buat password baru:</span>
      </div>

      <div style="margin-bottom: 16px;">
        <label for="fpNewPassword" class="auth-field-label">Password Baru (Minimal 8 Karakter)</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="fpNewPassword" class="auth-input-control" placeholder="Minimal 8 karakter" minlength="8" required>
        </div>
      </div>

      <div style="margin-bottom: 20px;">
        <label for="fpNewPasswordConfirm" class="auth-field-label">Konfirmasi Password Baru</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="fpNewPasswordConfirm" class="auth-input-control" placeholder="Ulangi password baru" minlength="8" required>
        </div>
      </div>

      <button type="button" id="btnSubmitNewPassword" class="auth-btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Simpan & Reset Password</span>
      </button>
    </div>

    {{-- =====================================================================
         LANGKAH 4: STATUS BERHASIL
         ===================================================================== --}}
    <div id="fpStepSuccess" style="display: none; text-align: center;">
      <div style="width: 58px; height: 58px; border-radius: 50%; background: #0e7482; color: #ffffff; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(14, 116, 130, 0.35);">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
      </div>

      <h3 style="font-size: 1.35rem; font-weight: 800; color: #0a5661; margin: 0 0 8px;">Password Berhasil Diubah!</h3>
      <p style="font-size: 0.86rem; color: #64748b; line-height: 1.5; margin: 0 0 24px;">
        Kata sandi baru akun Anda telah berhasil diperbarui dan disimpan dengan enkripsi aman. Silakan masuk menggunakan kata sandi baru Anda.
      </p>

      <a href="{{ route('login') }}" class="auth-btn-primary" style="text-decoration: none;">
        <span>Masuk ke Akun Sekarang &rarr;</span>
      </a>
    </div>

    <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9;">
      <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.84rem; color: #0e7482; font-weight: 700; text-decoration: none;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Login
      </a>
    </div>
  </div>
</div>

<script>
  let currentTargetEmail = '';
  let otpTimerInterval = null;
  let otpCooldown = 0;

  function showError(msg) {
    const errBox = document.getElementById('fpAlertError');
    const errText = document.getElementById('fpAlertErrorText');
    if (errBox && errText) {
      errText.textContent = msg;
      errBox.style.display = 'flex';
    }
  }

  function clearAlerts() {
    const errBox = document.getElementById('fpAlertError');
    const succBox = document.getElementById('fpAlertSuccess');
    if (errBox) errBox.style.display = 'none';
    if (succBox) succBox.style.display = 'none';
  }

  function showSuccess(msg) {
    const succBox = document.getElementById('fpAlertSuccess');
    const succText = document.getElementById('fpAlertSuccessText');
    if (succBox && succText) {
      succText.textContent = msg;
      succBox.style.display = 'flex';
    }
  }

  function startCooldown(sec) {
    otpCooldown = sec || 60;
    const badge = document.getElementById('fpTimerBadge');
    const secEl = document.getElementById('fpTimerSeconds');
    const resendBtn = document.getElementById('btnResendOtp');

    if (badge) badge.style.display = 'inline';
    if (resendBtn) {
      resendBtn.disabled = true;
      resendBtn.style.color = '#94a3b8';
      resendBtn.style.cursor = 'not-allowed';
    }

    clearInterval(otpTimerInterval);
    otpTimerInterval = setInterval(() => {
      otpCooldown--;
      if (secEl) secEl.textContent = otpCooldown;
      if (otpCooldown <= 0) {
        clearInterval(otpTimerInterval);
        if (badge) badge.style.display = 'none';
        if (resendBtn) {
          resendBtn.disabled = false;
          resendBtn.style.color = '#0e7482';
          resendBtn.style.cursor = 'pointer';
        }
      }
    }, 1000);
  }

  // 1. KIRIM OTP
  function triggerSendOtp() {
    clearAlerts();
    const email = document.getElementById('fpEmail')?.value.trim();
    if (!email) {
      showError('Harap masukkan alamat Gmail Anda.');
      document.getElementById('fpEmail')?.focus();
      return;
    }

    currentTargetEmail = email;
    const btn = document.getElementById('btnSendOtp');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>Mengirim Kode OTP...</span>';

    fetch('{{ route("password.send_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email: email })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showSuccess(data.message);
        document.getElementById('fpDisplayEmail').textContent = email;
        document.getElementById('fpStepEmail').style.display = 'none';
        document.getElementById('fpStepOtp').style.display = 'block';
        document.getElementById('fpCardSubtitle').textContent = 'Masukkan kode OTP 6 digit yang dikirim ke Gmail Anda.';
        document.getElementById('fpOtpCode').value = '';
        document.getElementById('fpOtpCode').focus();
        document.getElementById('fpRemainingAttempts').textContent = '5';
        startCooldown(data.cooldown || 60);
      } else {
        showError(data.message || 'Gagal mengirim kode OTP.');
        if (data.cooldown) startCooldown(data.cooldown);
      }
    })
    .catch(() => {
      showError('Terjadi kesalahan jaringan, silakan coba lagi.');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = origHtml;
    });
  }

  document.getElementById('btnSendOtp')?.addEventListener('click', triggerSendOtp);
  document.getElementById('btnResendOtp')?.addEventListener('click', () => {
    if (otpCooldown <= 0) triggerSendOtp();
  });

  // Ganti Email
  document.getElementById('btnChangeEmail')?.addEventListener('click', () => {
    clearAlerts();
    clearInterval(otpTimerInterval);
    document.getElementById('fpStepOtp').style.display = 'none';
    document.getElementById('fpStepEmail').style.display = 'block';
    document.getElementById('fpCardSubtitle').textContent = 'Reset password Anda melalui kode verifikasi OTP 6 digit yang dikirim ke Gmail terdaftar.';
    document.getElementById('fpEmail')?.focus();
  });

  // 2. VERIFIKASI OTP
  document.getElementById('btnVerifyOtp')?.addEventListener('click', () => {
    clearAlerts();
    const otp = document.getElementById('fpOtpCode')?.value.trim();
    if (!otp || otp.length !== 6) {
      showError('Harap masukkan 6 digit angka kode OTP yang diterima.');
      document.getElementById('fpOtpCode')?.focus();
      return;
    }

    const btn = document.getElementById('btnVerifyOtp');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>Memverifikasi...</span>';

    fetch('{{ route("password.verify_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: currentTargetEmail,
        otp: otp
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showSuccess(data.message);
        clearInterval(otpTimerInterval);
        document.getElementById('fpStepOtp').style.display = 'none';
        document.getElementById('fpStepNewPassword').style.display = 'block';
        document.getElementById('fpCardSubtitle').textContent = 'Tentukan kata sandi baru yang kuat untuk akun Anda.';
        document.getElementById('fpNewPassword').value = '';
        document.getElementById('fpNewPasswordConfirm').value = '';
        document.getElementById('fpNewPassword').focus();
      } else {
        showError(data.message || 'Kode OTP tidak valid.');
        if (typeof data.remaining !== 'undefined') {
          document.getElementById('fpRemainingAttempts').textContent = data.remaining;
        }
        document.getElementById('fpOtpCode')?.focus();
      }
    })
    .catch(() => {
      showError('Terjadi kesalahan jaringan saat memvalidasi OTP.');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = origHtml;
    });
  });

  // 3. SIMPAN PASSWORD BARU
  document.getElementById('btnSubmitNewPassword')?.addEventListener('click', () => {
    clearAlerts();
    const p1 = document.getElementById('fpNewPassword')?.value || '';
    const p2 = document.getElementById('fpNewPasswordConfirm')?.value || '';

    if (p1.length < 8) {
      showError('Password baru minimal harus 8 karakter.');
      document.getElementById('fpNewPassword')?.focus();
      return;
    }

    if (p1 !== p2) {
      showError('Konfirmasi password tidak cocok. Pastikan kedua kolom sama.');
      document.getElementById('fpNewPasswordConfirm')?.focus();
      return;
    }

    const btn = document.getElementById('btnSubmitNewPassword');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>Menyimpan Password...</span>';

    fetch('{{ route("password.update_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: currentTargetEmail,
        password: p1,
        password_confirmation: p2
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        clearAlerts();
        document.getElementById('fpStepNewPassword').style.display = 'none';
        document.getElementById('fpStepSuccess').style.display = 'block';
        document.getElementById('fpCardSubtitle').style.display = 'none';
      } else {
        showError(data.message || 'Gagal mereset password.');
      }
    })
    .catch(() => {
      showError('Terjadi kesalahan jaringan saat menyimpan password.');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = origHtml;
    });
  });
</script>
@endsection
