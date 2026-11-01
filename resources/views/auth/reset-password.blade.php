@extends('layouts.app')
@section('title', 'Reset Password - Kos Mas Robi')
@section('content')
<style>
  .auth-viewport { position: relative; min-height: 100vh; min-height: 100dvh; width: 100%; display: flex; align-items: center; justify-content: center; padding: 32px 16px; background: #f4fbfb; overflow: hidden; font-family: inherit; }
  .auth-ambient-orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
  .orb-teal-1 { width: 480px; height: 480px; top: -120px; left: -100px; background: radial-gradient(circle, rgba(14,116,130,0.28) 0%, transparent 70%); animation: orbFloat1 14s infinite ease-in-out alternate; }
  .orb-coral { width: 360px; height: 360px; bottom: -80px; right: -60px; background: radial-gradient(circle, rgba(244,63,94,0.12) 0%, transparent 70%); animation: orbFloat2 12s infinite ease-in-out alternate; }
  @keyframes orbFloat1 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(50px,60px) scale(1.08); } }
  @keyframes orbFloat2 { 0% { transform: translate(0,0) scale(0.9); } 100% { transform: translate(-40px,-40px) scale(1.12); } }
  .auth-card { position: relative; z-index: 10; width: 100%; max-width: 460px; background: rgba(255,255,255,0.94); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.85); border-radius: 32px; padding: 38px 34px; box-shadow: 0 24px 60px -15px rgba(10,86,97,0.18); animation: cardEntrance 0.7s cubic-bezier(0.16,1,0.3,1) both; }
  @keyframes cardEntrance { from { opacity: 0; transform: translateY(28px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .auth-logo-badge { width: 60px; height: 60px; margin: 0 auto 14px; border-radius: 20px; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 10px 25px rgba(14,116,130,0.38); text-decoration: none; transition: transform 0.35s; }
  .auth-logo-badge:hover { transform: translateY(-3px) scale(1.05); }
  .auth-field-label { display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 7px; }
  .auth-input-wrapper { position: relative; display: flex; align-items: center; margin-bottom: 16px; }
  .auth-input-icon { position: absolute; left: 14px; width: 20px; height: 20px; color: #94a3b8; pointer-events: none; }
  .auth-input-control { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 13px 16px 13px 44px; font-size: 0.92rem; color: #0f172a; transition: all 0.25s; box-sizing: border-box; font-family: inherit; }
  .auth-input-control:focus { background: #fff; border-color: #0e7482; box-shadow: 0 0 0 4px rgba(14,116,130,0.14); outline: none; }
  .auth-pwd-toggle { position: absolute; right: 12px; background: transparent; border: none; padding: 6px; color: #94a3b8; cursor: pointer; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: color 0.2s; }
  .auth-pwd-toggle:hover { color: #0e7482; }
  .auth-btn-primary { position: relative; width: 100%; padding: 14px 20px; font-size: 0.96rem; font-weight: 700; color: #fff; background: linear-gradient(135deg, #0a5661, #0e7482); border: none; border-radius: 16px; cursor: pointer; box-shadow: 0 8px 22px rgba(14,116,130,0.35); transition: all 0.25s; overflow: hidden; margin-top: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
  .auth-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(14,116,130,0.45); }
  .auth-btn-primary::after { content: ''; position: absolute; top: 0; left: -100%; width: 60%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transform: skewX(-20deg); animation: btnShimmer 5s infinite; }
  @keyframes btnShimmer { 0%,75% { left: -100%; } 100% { left: 160%; } }
  .auth-alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
  .strength-bar { height: 4px; border-radius: 4px; background: #e2e8f0; margin-top: 8px; overflow: hidden; }
  .strength-fill { height: 100%; border-radius: 4px; transition: width 0.3s, background 0.3s; width: 0; }
</style>

<div class="auth-viewport">
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-coral"></div>

  <div class="auth-card">
    <div style="text-align: center; margin-bottom: 26px;">
      <a href="{{ route('home') }}" class="auth-logo-badge">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg>
      </a>
      <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; letter-spacing: -0.02em;">Buat Password Baru</h2>
      <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Password baru Anda harus minimal 6 karakter.</p>
    </div>

    @if ($errors->any())
      <div class="auth-alert-error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      {{-- Email --}}
      <div>
        <label for="rpEmail" class="auth-field-label">Email</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="rpEmail" name="email" class="auth-input-control" value="{{ $email ?? old('email') }}" required readonly style="color: #64748b;">
        </div>
      </div>

      {{-- New Password --}}
      <div>
        <label for="rpPwd" class="auth-field-label">Password Baru</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="rpPwd" name="password" class="auth-input-control" placeholder="Min. 6 karakter" required style="padding-right: 46px;" oninput="checkStrength(this.value)">
          <button type="button" class="auth-pwd-toggle" onclick="togglePwd('rpPwd', this)">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
        <div id="strengthLabel" style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px;"></div>
      </div>

      {{-- Confirm Password --}}
      <div style="margin-bottom: 6px;">
        <label for="rpPwdConf" class="auth-field-label">Ulangi Password Baru</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" id="rpPwdConf" name="password_confirmation" class="auth-input-control" placeholder="Ketik ulang password" required style="padding-right: 46px;">
          <button type="button" class="auth-pwd-toggle" onclick="togglePwd('rpPwdConf', this)">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="auth-btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Simpan Password Baru</span>
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9;">
      <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.84rem; color: #0e7482; font-weight: 700; text-decoration: none;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Login
      </a>
    </div>
  </div>
</div>

<script>
  function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.style.color = inp.type === 'text' ? '#0e7482' : '#94a3b8';
  }

  function checkStrength(val) {
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 6) score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
      { pct: '0%',   color: '#e2e8f0', text: '' },
      { pct: '25%',  color: '#ef4444', text: 'Sangat Lemah' },
      { pct: '50%',  color: '#f97316', text: 'Lemah' },
      { pct: '70%',  color: '#eab308', text: 'Cukup' },
      { pct: '85%',  color: '#22c55e', text: 'Kuat' },
      { pct: '100%', color: '#0e7482', text: 'Sangat Kuat' },
    ];
    const lvl = levels[Math.min(score, 5)];
    fill.style.width = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
  }
</script>
@endsection

