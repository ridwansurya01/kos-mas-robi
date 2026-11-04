@extends('layouts.app')

@section('title', 'Login Akun - Kos Mas Robi')

@section('content')
<style>
  /* =========================================================================
     MODERN RESPONSIVE BENTO LOGIN EXPERIENCE
     Laptop / Desktop / Tablet: 2-Column Balanced Compact Layout
     Mobile: Streamlined Single Column
     ========================================================================= */
  .auth-viewport {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    background: #f0fdfa;
    overflow: hidden;
    font-family: inherit;
    box-sizing: border-box;
  }

  /* Animated Ambient Mesh Orbs */
  .auth-ambient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
    opacity: 0.65;
    animation-timing-function: ease-in-out;
    animation-iteration-count: infinite;
    animation-direction: alternate;
  }
  .orb-teal-1 {
    width: 480px;
    height: 480px;
    top: -120px;
    left: -100px;
    background: radial-gradient(circle, rgba(14, 116, 130, 0.28) 0%, rgba(10, 86, 97, 0.08) 70%, transparent 100%);
    animation: orbFloat1 14s infinite;
  }
  .orb-teal-2 {
    width: 420px;
    height: 420px;
    bottom: -100px;
    right: -80px;
    background: radial-gradient(circle, rgba(10, 86, 97, 0.22) 0%, rgba(20, 184, 166, 0.12) 70%, transparent 100%);
    animation: orbFloat2 16s infinite;
  }
  .orb-coral {
    width: 320px;
    height: 320px;
    top: 40%;
    right: 15%;
    background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, transparent 70%);
    animation: orbFloat3 12s infinite;
  }

  @keyframes orbFloat1 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(60px, 40px) scale(1.08); }
    100% { transform: translate(-30px, 70px) scale(0.95); }
  }
  @keyframes orbFloat2 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-50px, -40px) scale(1.1); }
    100% { transform: translate(40px, -60px) scale(0.92); }
  }
  @keyframes orbFloat3 {
    0% { transform: translate(0, 0) scale(0.9); opacity: 0.4; }
    50% { transform: translate(-30px, 30px) scale(1.15); opacity: 0.7; }
    100% { transform: translate(20px, -20px) scale(1); opacity: 0.5; }
  }

  /* Main Responsive Bento Card */
  .auth-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 920px;
    background: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 28px;
    box-shadow: 
      0 24px 60px -15px rgba(10, 86, 97, 0.18),
      0 8px 24px -6px rgba(15, 23, 42, 0.06),
      0 0 0 1px rgba(14, 116, 130, 0.08);
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    overflow: hidden;
    animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes cardEntrance {
    0% {
      opacity: 0;
      transform: translateY(20px) scale(0.98);
    }
    100% {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Left Panel: Luxury Real Estate Hero */
  .auth-panel-hero {
    background: linear-gradient(145deg, #032328 0%, #063d45 42%, #0a5661 100%);
    color: #ffffff;
    padding: 34px 30px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }
  .auth-panel-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(20, 184, 166, 0.25) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }
  .auth-panel-hero::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -40px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(244, 63, 94, 0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .auth-hero-top {
    position: relative;
    z-index: 2;
  }
  .auth-hero-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #ffffff;
    margin-bottom: 20px;
  }
  .auth-hero-badge {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.14);
    border: 1.5px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
  }
  .auth-hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.2px;
    line-height: 1.2;
  }
  .auth-hero-headline {
    font-family: 'Outfit', sans-serif;
    font-size: 1.45rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.3;
    margin: 0 0 8px;
    letter-spacing: -0.3px;
  }
  .auth-hero-desc {
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.75);
    line-height: 1.5;
    margin: 0 0 20px;
  }

  .auth-hero-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 24px;
  }
  .auth-hero-feat-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #ccfbf1;
  }
  .auth-hero-feat-icon {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    background: rgba(204, 251, 241, 0.15);
    color: #5eead4;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  /* Frosted Notice Box on Left Panel */
  .auth-hero-notice {
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  }
  .auth-hero-notice-title {
    font-size: 0.82rem;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
  }
  .auth-hero-notice-text {
    font-size: 0.74rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.45;
    margin: 0 0 10px;
  }
  .auth-hero-notice-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    background: #10b981;
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.76rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    box-sizing: border-box;
  }
  .auth-hero-notice-btn:hover {
    background: #059669;
    transform: translateY(-1px);
    color: #ffffff;
  }

  /* Right Panel: Compact Login Form */
  .auth-panel-form {
    padding: 34px 36px 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #ffffff;
  }

  .auth-form-header {
    margin-bottom: 20px;
  }
  .auth-mobile-brand {
    display: none;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
  }
  .auth-form-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
    letter-spacing: -0.02em;
  }
  .auth-form-desc {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0;
  }

  /* Form Fields */
  .auth-field-group {
    position: relative;
    margin-bottom: 14px;
  }
  .auth-field-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 5px;
  }
  .auth-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }
  .auth-input-icon {
    position: absolute;
    left: 14px;
    width: 18px;
    height: 18px;
    color: #94a3b8;
    pointer-events: none;
    transition: color 0.2s ease;
  }
  .auth-input-control {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 11px 14px 11px 42px;
    font-size: 0.88rem;
    color: #0f172a;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: inherit;
  }
  .auth-input-control:focus {
    background: #ffffff;
    border-color: #0a5661;
    box-shadow: 0 0 0 3px rgba(10, 86, 97, 0.12);
    outline: none;
  }
  .auth-input-control:focus + .auth-input-icon,
  .auth-input-wrapper:focus-within .auth-input-icon {
    color: #0a5661;
  }

  .auth-pwd-toggle {
    position: absolute;
    right: 10px;
    background: transparent;
    border: none;
    padding: 6px;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
  }
  .auth-pwd-toggle:hover {
    color: #0a5661;
  }

  /* Row Remember Me & Forgot Password */
  .auth-row-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.78rem;
    margin-bottom: 16px;
  }
  .auth-row-options label {
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    color: #475569;
    font-weight: 500;
  }
  .auth-row-options a {
    color: #0a5661;
    font-weight: 700;
    text-decoration: none;
    transition: color 0.2s;
  }
  .auth-row-options a:hover {
    text-decoration: underline;
  }

  /* Primary Button */
  .auth-btn-primary {
    position: relative;
    width: 100%;
    padding: 12px 18px;
    font-size: 0.92rem;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    border: none;
    border-radius: 14px;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(10, 86, 97, 0.3);
    transition: all 0.2s;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .auth-btn-primary:hover {
    background: linear-gradient(135deg, #08434c 0%, #0c616d 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(10, 86, 97, 0.4);
  }
  .auth-btn-primary:active {
    transform: translateY(0);
  }

  /* Divider */
  .auth-divider {
    display: flex;
    align-items: center;
    margin: 14px 0 12px;
    color: #94a3b8;
    font-size: 0.72rem;
  }
  .auth-divider::before,
  .auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
  }
  .auth-divider span {
    padding: 0 10px;
    font-weight: 700;
    letter-spacing: 0.05em;
  }

  /* Google Button */
  .auth-btn-google {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 11px 16px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    transition: all 0.2s;
    box-sizing: border-box;
    cursor: pointer;
  }
  .auth-btn-google:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  /* Mobile Only Notice (hidden on desktop/laptop/tablet) */
  .auth-mobile-notice {
    display: none;
    margin-top: 16px;
    padding: 12px 14px;
    background: #f0fdfa;
    border: 1px solid #ccfbf1;
    border-radius: 14px;
    text-align: center;
  }

  .auth-back-link-wrap {
    text-align: center;
    margin-top: 14px;
  }
  .auth-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    color: #94a3b8;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
  }
  .auth-back-link:hover {
    color: #0a5661;
  }

  /* =========================================================================
     RESPONSIVE BREAKPOINT
     ========================================================================= */
  @media (max-width: 819px) {
    .auth-viewport {
      padding: 16px 12px;
    }
    .auth-card {
      max-width: 440px;
      grid-template-columns: 1fr;
      border-radius: 24px;
    }
    .auth-panel-hero {
      display: none;
    }
    .auth-mobile-brand {
      display: flex;
    }
    .auth-panel-form {
      padding: 26px 20px 22px;
    }
    .auth-mobile-notice {
      display: block;
    }
  }
</style>

@php
  $setting = \App\Models\Setting::first();
  $rawPhone = $setting->owner_phone ?? '6281234567890';
  $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
  if (str_starts_with($cleanPhone, '0')) {
      $cleanPhone = '62' . substr($cleanPhone, 1);
  }
  $waLoginHelp = urlencode("Halo Admin " . ($setting->kos_name ?? 'Kos Mas Robi') . ", saya penghuni baru dan ingin dibuatkan akun login portal anak kos.");
@endphp

<div class="auth-viewport">
  {{-- Atmospheric Gradient Blobs --}}
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-teal-2"></div>
  <div class="auth-ambient-orb orb-coral"></div>

  {{-- Main Responsive Bento Card --}}
  <div class="auth-card">

    {{-- =========================================================================
         LEFT COLUMN (DESKTOP, LAPTOP & TABLET VIEW)
         ========================================================================= --}}
    <div class="auth-panel-hero">
      <div class="auth-hero-top">
        <a href="{{ route('home') }}" class="auth-hero-brand" title="Ke Beranda {{ $setting->kos_name ?? 'Kos Mas Robi' }}">
          <div class="auth-hero-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
              <path d="M9 21V12h6v9"/>
            </svg>
          </div>
          <div class="auth-hero-title">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
        </a>

        <h1 class="auth-hero-headline">Smart Living &<br>Kost Eksklusif</h1>
        <p class="auth-hero-desc">Sistem manajemen hunian modern, nyaman, full furnished, dan aman 24 jam di Sleman, DI Yogyakarta.</p>

        <div class="auth-hero-features">
          <div class="auth-hero-feat-item">
            <div class="auth-hero-feat-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <span>Akses Portal Layanan Penghuni 24 Jam</span>
          </div>
          <div class="auth-hero-feat-item">
            <div class="auth-hero-feat-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <span>Konfirmasi Pembayaran & Kuitansi Digital</span>
          </div>
          <div class="auth-hero-feat-item">
            <div class="auth-hero-feat-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <span>Smart Key Mandiri & CCTV Seluruh Lorong</span>
          </div>
        </div>
      </div>

      {{-- Informasi Belum Punya Akun (Tampil Elegan di Sisi Kiri) --}}
      <div class="auth-hero-notice">
        <div class="auth-hero-notice-title">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span>Belum Memiliki Akun?</span>
        </div>
        <p class="auth-hero-notice-text">
          Pembuatan akun baru hanya dilakukan langsung oleh Admin saat penempatan kamar.
        </p>
        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waLoginHelp }}" target="_blank" class="auth-hero-notice-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
          <span>Hubungi Admin via WhatsApp</span>
        </a>
      </div>
    </div>

    {{-- =========================================================================
         RIGHT COLUMN: COMPACT MODERN LOGIN FORM
         ========================================================================= --}}
    <div class="auth-panel-form">

      {{-- Banner: Already Logged In --}}
      @auth
      <div style="background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%); border: 1.5px solid #99f6e4; border-radius: 14px; padding: 10px 12px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
        <div>
          <div style="font-size: 0.74rem; font-weight: 700; color: #0a5661;">
            Sudah masuk sebagai: <strong>{{ Auth::user()->name }}</strong>
            ({{ Auth::user()->isOwner() ? 'Owner' : (Auth::user()->isAdmin() ? 'Admin' : 'Pelanggan') }})
          </div>
        </div>
        <div style="display: flex; gap: 6px; flex-shrink: 0;">
          <a href="{{ Auth::user()->isStaffOrOwner() ? route('admin.dashboard') : route('tenant.portal') }}"
             style="padding: 5px 10px; background: #0a5661; color: #fff; border-radius: 8px; font-size: 0.72rem; font-weight: 700; text-decoration: none;">
            Ke Dashboard →
          </a>
          <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" style="padding: 5px 10px; background: transparent; border: 1px solid #0e7482; color: #0e7482; border-radius: 8px; font-size: 0.72rem; font-weight: 700; cursor: pointer;">
              Logout
            </button>
          </form>
        </div>
      </div>
      @endauth

      {{-- Form Header --}}
      <div class="auth-form-header">
        <div class="auth-mobile-brand">
          <div style="width: 32px; height: 32px; border-radius: 10px; background: #0a5661; display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg>
          </div>
          <span style="font-family: 'Outfit', sans-serif; font-size: 0.95rem; font-weight: 800; color: #0a5661;">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</span>
        </div>
        <h2 class="auth-form-title">Masuk ke Akun</h2>
        <p class="auth-form-desc">Masukkan email dan kata sandi yang telah didaftarkan pengelola</p>
      </div>

      {{-- Validation Error Alerts --}}
      @if ($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 12px; border-radius: 12px; font-size: 0.78rem; margin-bottom: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif

      @if (session('info'))
        <div style="background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; padding: 10px 12px; border-radius: 12px; font-size: 0.78rem; margin-bottom: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span>{{ session('info') }}</span>
        </div>
      @endif

      {{-- Form Fields --}}
      <form action="{{ route('login.post') }}" method="POST" id="formLogin">
        @csrf

        {{-- Email Field --}}
        <div class="auth-field-group">
          <label for="loginEmail" class="auth-field-label">Alamat Email</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
            <input type="email" 
                   id="loginEmail" 
                   name="email" 
                   class="auth-input-control" 
                   placeholder="Masukkan alamat email Anda" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus>
          </div>
        </div>

        {{-- Password Field --}}
        <div class="auth-field-group">
          <label for="loginPassword" class="auth-field-label">Kata Sandi</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input type="password" 
                   id="loginPassword" 
                   name="password" 
                   class="auth-input-control" 
                   placeholder="Masukkan kata sandi..." 
                   required 
                   style="padding-right: 44px;">
            
            <button type="button" class="auth-pwd-toggle" id="btnTogglePassword" onclick="togglePasswordVisibility()" title="Lihat Kata Sandi">
              <svg id="iconEye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="3" r="3"></circle>
              </svg>
              <svg id="iconEyeOff" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="auth-row-options">
          <label>
            <input type="checkbox" name="remember" style="width: 15px; height: 15px; accent-color: #0a5661; cursor: pointer;">
            <span>Ingat saya</span>
          </label>
          <a href="{{ route('password.request') }}">Lupa Password?</a>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="auth-btn-primary" id="btnSubmitLogin">
          <span id="btnSubmitText">Masuk Sekarang</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>

        {{-- Divider --}}
        <div class="auth-divider">
          <span>ATAU</span>
        </div>

        {{-- Google Login Button --}}
        <a href="{{ route('auth.google') }}" class="auth-btn-google">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
          </svg>
          <span>Masuk dengan Akun Google</span>
        </a>
      </form>

      {{-- Mobile-Only Notice (Muncul khusus di smartphone agar ringkas) --}}
      <div class="auth-mobile-notice">
        <div style="font-size: 0.78rem; font-weight: 700; color: #0a5661; margin-bottom: 2px;">
          Belum Memiliki Akun Penghuni?
        </div>
        <p style="font-size: 0.72rem; color: #64748b; margin: 0 0 8px;">
          Pembuatan akun dilakukan langsung oleh Admin kos saat penempatan kamar.
        </p>
        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waLoginHelp }}" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #059669; color: #ffffff; padding: 6px 14px; border-radius: 999px; font-size: 0.74rem; font-weight: 700; text-decoration: none;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
          <span>Hubungi Admin WhatsApp</span>
        </a>
      </div>

      {{-- Return to Landing Page --}}
      <div class="auth-back-link-wrap">
        <a href="{{ route('home') }}" class="auth-back-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          <span>Kembali ke Halaman Utama</span>
        </a>
      </div>

    </div>
  </div>
</div>

<script>
  // Toggle password visibility
  function togglePasswordVisibility() {
    const pwdInput = document.getElementById('loginPassword');
    const iconEye = document.getElementById('iconEye');
    const iconEyeOff = document.getElementById('iconEyeOff');
    
    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      iconEye.style.display = 'none';
      iconEyeOff.style.display = 'block';
    } else {
      pwdInput.type = 'password';
      iconEye.style.display = 'block';
      iconEyeOff.style.display = 'none';
    }
  }
</script>
@endsection
