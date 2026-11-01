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
  .auth-alert-success { background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; padding: 14px 16px; border-radius: 14px; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
  .auth-alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; font-weight: 600; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
</style>

<div class="auth-viewport">
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-teal-2"></div>

  <div class="auth-card">
    <div style="text-align: center; margin-bottom: 26px;">
      <a href="{{ route('home') }}" class="auth-logo-badge">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg>
      </a>
      <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; letter-spacing: -0.02em;">Lupa Password?</h2>
      <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.5;">Masukkan email terdaftar Anda. Sistem akan membuat dan mengirimkan password baru langsung ke Gmail Anda.</p>
    </div>

    @if (session('status'))
      <div class="auth-alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if ($errors->any())
      <div class="auth-alert-error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <div style="margin-bottom: 20px;">
        <label for="fpEmail" class="auth-field-label">Alamat Email Terdaftar</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <input type="email" id="fpEmail" name="email" class="auth-input-control" placeholder="email@gmail.com" value="{{ old('email') }}" required autofocus>
        </div>
      </div>

      <button type="submit" class="auth-btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        <span>Kirim Password Baru ke Gmail</span>
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
@endsection

