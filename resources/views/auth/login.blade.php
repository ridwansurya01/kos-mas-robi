@extends('layouts.app')

@section('title', 'Log in — ' . (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING'))

@section('content')
@php
  $setting = rescue(fn() => \App\Models\Setting::first(), null);
  $loginContent = $setting ? $setting->getLoginPageContent() : [];
  $hasLoginErrors = (isset($errors) && $errors->any()) || !empty(old('email'));
@endphp

<script>
  (function() {
    try {
      var fromLanding = sessionStorage.getItem('f3d_from_landing') === '1';
      var hasError = {{ $hasLoginErrors ? 'true' : 'false' }};
      if (fromLanding && !hasError) {
        document.documentElement.classList.add('f3d-login-intro-active');
      }
    } catch(e) {}
  })();
</script>

<style>
  html.f3d-login-intro-active #f3dLoginPortalOverlay {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 999999999 !important;
  }
  html.f3d-login-intro-active body {
    background: #ffffff !important;
  }

  :root {
    --login-primary: #033f3c;
    --login-primary-hover: #022f2d;
    --login-text-dark: #111827;
    --login-text-muted: #6b7280;
    --login-border: #e5e7eb;
    --login-border-focus: #033f3c;
  }

  * {
    box-sizing: border-box;
  }

  html, body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #ffffff !important;
    color: #111827;
  }

  /* Main Viewport Container */
  .login-viewport-wrapper {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
    background: #ffffff !important;
  }

  /* Two-Column Split Container */
  .login-split-card {
    width: 100%;
    max-width: 1040px;
    min-height: 620px;
    background: transparent;
    border-radius: 0;
    padding: 0;
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 36px;
    box-shadow: none !important;
    border: none !important;
    position: relative;
    z-index: 10;
  }

  /* Promotional Left Section */
  .login-promo-panel {
    background-color: #033f3c;
    background-image: url('{{ asset("assets/images/landing/login_left_card_clean.webp") }}?v={{ file_exists(public_path("assets/images/landing/login_left_card_clean.webp")) ? filemtime(public_path("assets/images/landing/login_left_card_clean.webp")) : time() }}');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center bottom;
    border-radius: 28px;
    padding: 48px 42px 260px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 580px;
  }

  .login-promo-headline {
    font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(28px, 2.7vw, 36px);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.18;
    letter-spacing: -0.02em;
    margin: 0;
  }

  .login-promo-curve {
    display: block;
    margin: 6px 0 14px;
  }

  .login-promo-desc {
    color: rgba(255, 255, 255, 0.85);
    font-size: 13.5px;
    line-height: 1.55;
    margin: 0;
    max-width: 320px;
    font-weight: 400;
  }

  /* Form Right Section */
  .login-form-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px 28px;
    background: transparent;
    border-radius: 0;
    box-shadow: none !important;
    border: none !important;
  }

  .login-form-content {
    width: 100%;
    max-width: 370px;
  }

  .login-brand-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 26px;
  }

  .login-logo-sq {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: #033f3c;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(3, 63, 60, 0.18);
    transition: transform 0.2s ease;
  }

  .login-logo-sq:hover {
    transform: scale(1.04);
  }

  .login-brand-title {
    font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
    font-size: 19px;
    font-weight: 800;
    color: #111827;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin: 14px 0 4px;
    text-align: center;
  }

  .login-form-subtitle {
    font-size: 13px;
    color: var(--login-text-muted);
    margin: 0;
    text-align: center;
    font-weight: 500;
  }

  /* Alert Messages */
  .login-alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 12.5px;
    font-weight: 600;
    margin-bottom: 16px;
    text-align: left;
  }

  .login-alert-info {
    background: #f0fdfa;
    color: #0d9488;
    border: 1px solid #99f6e4;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 12.5px;
    font-weight: 600;
    margin-bottom: 16px;
    text-align: left;
  }

  .login-auth-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 12.5px;
    color: #166534;
    margin-bottom: 16px;
    width: 100%;
  }

  /* Input Fields */
  .login-input-wrap {
    position: relative;
    margin-bottom: 14px;
    width: 100%;
  }

  .login-input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-text-input {
    width: 100%;
    height: 48px;
    border-radius: 14px;
    border: 1.5px solid var(--login-border);
    background: #ffffff;
    padding: 0 16px 0 46px;
    font-size: 13.5px;
    color: var(--login-text-dark);
    outline: none;
    transition: all 0.2s ease;
    font-family: inherit;
    -webkit-appearance: none;
  }

  .login-text-input.has-pwd-toggle {
    padding-right: 46px;
  }

  .login-text-input:focus {
    border-color: var(--login-border-focus);
    box-shadow: 0 0 0 3px rgba(3, 63, 60, 0.12);
  }

  /* Suppress Chrome autofill background */
  .login-text-input:-webkit-autofill,
  .login-text-input:-webkit-autofill:hover, 
  .login-text-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
    -webkit-text-fill-color: var(--login-text-dark) !important;
    transition: background-color 5000s ease-in-out 0s;
  }

  .login-pwd-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    border-radius: 50%;
    transition: color 0.15s ease;
  }

  .login-pwd-toggle:hover {
    color: #111827;
  }

  /* Forgot Password Link */
  .login-forgot-wrap {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
    margin-bottom: 20px;
    width: 100%;
  }

  .login-forgot-link {
    font-size: 12.5px;
    font-weight: 600;
    color: #033f3c;
    text-decoration: none;
    transition: opacity 0.15s ease;
  }

  .login-forgot-link:hover {
    text-decoration: underline;
    opacity: 0.85;
  }

  /* Login Submit Button */
  .login-btn-primary {
    width: 100%;
    height: 48px;
    border-radius: 14px;
    background: #033f3c;
    color: #ffffff;
    font-size: 14.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-family: inherit;
    box-shadow: 0 4px 14px rgba(3, 63, 60, 0.18);
  }

  .login-btn-primary:hover {
    background: #022f2d;
    box-shadow: 0 6px 18px rgba(3, 63, 60, 0.25);
    transform: translateY(-1px);
  }

  .login-btn-primary:active {
    transform: translateY(1px);
  }

  /* Divider */
  .login-divider-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 18px 0 16px;
    width: 100%;
  }

  .login-divider-line {
    flex: 1;
    height: 1px;
    background: var(--login-border);
  }

  .login-divider-label {
    font-size: 12px;
    color: var(--login-text-muted);
    font-weight: 500;
  }

  /* Google Button */
  .login-btn-google {
    width: 100%;
    height: 48px;
    border-radius: 14px;
    background: #ffffff;
    border: 1.5px solid var(--login-border);
    color: #1f2937;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    font-family: inherit;
  }

  .login-btn-google:hover {
    background: #fafafa;
    border-color: #d1d5db;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
  }

  /* Back to Home Link */
  .login-back-home-wrap {
    margin-top: 22px;
    text-align: center;
    width: 100%;
  }

  .login-back-home-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #033f3c;
    text-decoration: none;
    transition: all 0.15s ease;
  }

  .login-back-home-link:hover {
    text-decoration: underline;
    opacity: 0.85;
  }

  /* Responsive Design - Mobile Optimized (<900px) */
  @media (max-width: 900px) {
    .login-viewport-wrapper {
      padding: 32px 20px 48px;
      background: #ffffff !important;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .login-split-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      max-width: 390px;
      width: 100%;
      margin: 0 auto;
      padding: 0;
      gap: 0;
      box-shadow: none !important;
      border: none !important;
      background: transparent !important;
    }
    /* Sembunyikan panel promosi di mobile agar tampilan bersih, fokus, dan tidak bertumpuk */
    .login-promo-panel {
      display: none !important;
    }
    .login-form-panel {
      width: 100%;
      max-width: 100%;
      padding: 0;
      margin: 0;
      border-radius: 0;
      background: transparent !important;
      box-shadow: none !important;
      border: none !important;
    }
    .login-form-content {
      width: 100%;
      max-width: 100%;
    }
    .login-brand-badge {
      margin-bottom: 26px;
    }
    .login-logo-sq {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(3, 63, 60, 0.16);
    }
    .login-brand-title {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 0.04em;
      margin: 14px 0 4px;
    }
    .login-form-subtitle {
      font-size: 13.5px;
      color: var(--login-text-muted);
    }
    .login-input-wrap {
      margin-bottom: 14px;
    }
    .login-text-input {
      height: 48px;
      font-size: 14px;
      border-radius: 14px;
    }
    .login-btn-primary {
      height: 48px;
      font-size: 14.5px;
      border-radius: 14px;
      font-weight: 700;
    }
    .login-divider-row {
      margin: 20px 0 16px;
    }
    .login-btn-google {
      height: 48px;
      font-size: 14px;
      border-radius: 14px;
      font-weight: 700;
    }
    .login-back-home-wrap {
      margin-top: 24px;
    }
    .login-back-home-link {
      font-size: 13.5px;
      font-weight: 600;
    }
  }

  /* Modal Reset Password Styling */
  .fp-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .fp-modal-card {
    background: #ffffff;
    border-radius: 24px;
    width: 100%;
    max-width: 420px;
    padding: 28px 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: fpPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  @keyframes fpPopIn {
    0% { opacity: 0; transform: scale(0.92) translateY(12px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
  }

  /* Flutter 3D Intro Overlay */
  .f3d-dash-portal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
  }
  .f3d-dash-loader-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
  }
  .f3d-dash-svg-box {
    width: 140px;
    height: 140px;
  }
  .f3d-dash-svg-canvas {
    width: 100%;
    height: 100%;
  }
  .f3d-dash-faint-track {
    stroke: rgba(16, 185, 129, 0.15);
    fill: none;
  }
  .f3d-dash-drawn-green {
    stroke: #0a5661;
    fill: none;
    stroke-dasharray: 0 1000;
  }
  .f3d-dash-snake-tracer {
    fill: none;
    stroke-dasharray: 0 1000;
  }
  .f3d-dash-loading-track {
    width: 160px;
    height: 5px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
  }
  .f3d-dash-loading-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #10b981, #0a5661);
    border-radius: 999px;
  }
  .f3d-dash-loading-label {
    font-size: 11px;
    letter-spacing: 0.12em;
    font-weight: 700;
    color: #64748b;
  }
</style>

{{-- =========================================================================
     LOADING INTRO OVERLAY: MENUJU HALAMAN LOGIN
     ========================================================================= --}}
<div id="f3dLoginPortalOverlay" class="f3d-dash-portal-overlay">
  <div class="f3d-dash-loader-wrap" id="f3dLoginLoaderWrap">
    <div class="f3d-dash-svg-box">
      <svg class="f3d-dash-svg-canvas" viewBox="0 0 224 232" fill="none">
        <defs>
          <linearGradient id="loginSnakeGreenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#10b981" />
            <stop offset="50%" stop-color="#34d399" />
            <stop offset="100%" stop-color="#0a5661" />
          </linearGradient>
        </defs>

        <path class="f3d-dash-faint-track" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path class="f3d-dash-faint-track" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />

        <path id="f3dLoginGreenHouse" class="f3d-dash-drawn-green" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dLoginSnakeHouse" class="f3d-dash-snake-tracer" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="url(#loginSnakeGreenGrad)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />

        <path id="f3dLoginGreenDoor" class="f3d-dash-drawn-green" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dLoginSnakeDoor" class="f3d-dash-snake-tracer" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="url(#loginSnakeGreenGrad)" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>

    <div class="f3d-dash-loading-track">
      <div class="f3d-dash-loading-bar" id="f3dLoginProgressBar"></div>
    </div>
    <div class="f3d-dash-loading-label">MEMUAT HALAMAN LOGIN...</div>
  </div>
</div>

<script>
  if (document.documentElement.classList.contains('f3d-login-intro-active')) {
    var overlay = document.getElementById('f3dLoginPortalOverlay');
    if (overlay) {
      overlay.style.display = 'flex';
      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';
    }
  }
</script>

{{-- =========================================================================
     HALAMAN LOGIN UTAMA: SPLIT SCREEN MODERN SESUAI DESAIN REFERENSI
     ========================================================================= --}}
<div class="login-viewport-wrapper">
  <div class="login-split-card">

    {{-- BAGIAN KIRI: PROMOTIONAL SECTION --}}
    <div class="login-promo-panel">
      <div style="position: relative; z-index: 2;">
        <h2 class="login-promo-headline">
          {!! nl2br(e($loginContent['promo_headline'] ?? "Simplify\nmanagement with\nour dashboard.")) !!}
        </h2>
        
        {{-- Garis Lengkung Hijau Dekoratif --}}
        <svg class="login-promo-curve" width="118" height="12" viewBox="0 0 118 12" fill="none">
          <path d="M3 4C35 12 83 12 115 4" stroke="#22c55e" stroke-width="3" stroke-linecap="round"/>
        </svg>

        <p class="login-promo-desc">
          {{ $loginContent['promo_desc'] ?? 'Simplify your e-commerce management with our user-friendly admin dashboard.' }}
        </p>
      </div>
    </div>

    {{-- BAGIAN KANAN: FORM LOGIN --}}
    <div class="login-form-panel">
      <div class="login-form-content">

        @auth
        <div class="login-auth-banner">
          <span>Sudah masuk: <strong>{{ Auth::user()->name }}</strong></span>
          <a href="{{ Auth::user()->isStaffOrOwner() ? route('admin.dashboard') : route('tenant.portal') }}" style="color: #033f3c; font-weight: 700; text-decoration: underline;">Dashboard &rarr;</a>
        </div>
        @endauth

        {{-- 1 & 2 & 3: Logo, Nama Brand, Supporting Text --}}
        <div class="login-brand-badge">
          <a href="{{ route('home') }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center;" title="Kembali ke Beranda">
            @if (!empty($loginContent['brand_logo_url']))
              <img src="{{ asset($loginContent['brand_logo_url']) }}" 
                   alt="{{ $loginContent['brand_title'] ?? 'SMART LIVING' }}" 
                   class="login-logo-sq" 
                   style="width: {{ $loginContent['brand_logo_size'] ?? 48 }}px; height: {{ $loginContent['brand_logo_size'] ?? 48 }}px; object-fit: contain; background: transparent; box-shadow: none;">
            @else
              <div class="login-logo-sq" style="width: {{ $loginContent['brand_logo_size'] ?? 48 }}px; height: {{ $loginContent['brand_logo_size'] ?? 48 }}px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
                  <path d="M9 21V12h6v9"/>
                </svg>
              </div>
            @endif
            <h1 class="login-brand-title">{{ $loginContent['brand_title'] ?? 'SMART LIVING' }}</h1>
          </a>
          <p class="login-form-subtitle">{{ $loginContent['form_subtitle'] ?? 'Please login to your account' }}</p>
        </div>

        @if (isset($errors) && $errors->any())
          <div class="login-alert-error">{{ $errors->first() }}</div>
        @endif

        @if (session('info'))
          <div class="login-alert-info">{{ session('info') }}</div>
        @endif

        {{-- Form Login Utama --}}
        <form action="{{ route('login.post') }}" method="POST" onsubmit="onLoginSubmit()">
          @csrf

          {{-- 4: Input Email Address dengan Ikon Email --}}
          <div class="login-input-wrap">
            <span class="login-input-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
            </span>
            <input type="text" 
                   id="loginEmailInput" 
                   name="email" 
                   class="login-text-input" 
                   placeholder="{{ $loginContent['placeholder_email'] ?? 'Email address' }}" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus>
          </div>

          {{-- 5: Input Password dengan Ikon Gembok & Show/Hide Password --}}
          <div class="login-input-wrap">
            <span class="login-input-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
            <input type="password" 
                   id="loginPasswordInput" 
                   name="password" 
                   class="login-text-input has-pwd-toggle" 
                   placeholder="{{ $loginContent['placeholder_password'] ?? 'Password' }}" 
                   required>
            <button type="button" class="login-pwd-toggle" onclick="togglePasswordVisibility('loginPasswordInput', this)" title="Lihat Kata Sandi" aria-label="Lihat Kata Sandi">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>

          {{-- 6: Link Forgot password? --}}
          <div class="login-forgot-wrap">
            <a href="{{ route('password.request') }}" onclick="openForgotPwdModal(event)" class="login-forgot-link">
              {{ $loginContent['forgot_password_text'] ?? 'Forgot password?' }}
            </a>
          </div>

          {{-- 7: Tombol Login Hijau Tua --}}
          <button type="submit" class="login-btn-primary">
            {{ $loginContent['btn_login_text'] ?? 'Login' }}
          </button>

          {{-- 8: Tulisan Pemisah Or Login with --}}
          <div class="login-divider-row">
            <div class="login-divider-line"></div>
            <span class="login-divider-label">{{ $loginContent['divider_text'] ?? 'Or Login with' }}</span>
            <div class="login-divider-line"></div>
          </div>

          {{-- 9: Tombol Login Menggunakan Google Saja --}}
          <a href="{{ route('auth.google') }}" class="login-btn-google" title="Masuk dengan Google" aria-label="Masuk dengan Google">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Google</span>
          </a>

          {{-- 10: Link Kembali Ke Beranda di Paling Bawah --}}
          <div class="login-back-home-wrap">
            <a href="{{ route('home') }}" class="login-back-home-link" title="Kembali Ke Beranda">
              <span>{{ $loginContent['back_home_text'] ?? 'Kembali Ke Beranda' }}</span>
            </a>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>

{{-- =========================================================================
     MODAL POPUP: DITOLAK KARENA TIDAK TERDAFTAR SEBAGAI ANAK KOS
     ========================================================================= --}}
@if(session('google_not_registered_popup'))
<div id="modalGoogleNotRegistered" class="fp-modal-backdrop" style="display: flex;" onclick="if(event.target===this) closeGoogleNotRegisteredModal()">
  <div class="fp-modal-card" style="max-width: 440px; text-align: center; padding: 32px 24px;">
    {{-- Warning / Denied Icon --}}
    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; border: 2px solid #fecaca; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 18px; color: #dc2626; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.12);">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>

    {{-- Title & Message --}}
    <h3 style="margin: 0 0 12px; font-size: 1.25rem; font-weight: 800; color: #111827; font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;">
      Akses Masuk Ditolak
    </h3>

    <div style="background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px;">
      <p style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #be123c; line-height: 1.45;">
        {{ session('google_rejected_message') ?? 'Anda tidak dapat login karena tidak terdaftar sebagai anak kos.' }}
      </p>
    </div>

    <p style="font-size: 0.82rem; color: #4b5563; line-height: 1.55; margin-bottom: 22px; text-align: left;">
      Akun Google <strong style="color: #111827;">{{ session('google_rejected_email') }}</strong> belum terdaftar atau belum ditautkan sebagai anak kos di SMART LIVING.
      <br><br>
      <strong>Alur pendaftaran anak kos:</strong>
      <br>
      1. Akun didaftarkan langsung oleh Admin / Pengelola kos saat penempatan kamar.
      <br>
      2. Masuk pertama kali menggunakan email & kata sandi yang telah didaftarkan Admin.
      <br>
      3. Lengkapi formulir data diri di portal dan tekan <strong>"Simpan dengan Google"</strong> agar akun Google Anda terhubung untuk login selanjutnya.
    </p>

    <button type="button" onclick="closeGoogleNotRegisteredModal()" class="login-btn-primary" style="width: 100%; height: 46px; font-size: 14px; font-weight: 700; cursor: pointer;">
      Mengerti & Tutup
    </button>
  </div>
</div>

<script>
  function closeGoogleNotRegisteredModal() {
    var m = document.getElementById('modalGoogleNotRegistered');
    if (m) m.style.display = 'none';
  }
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeGoogleNotRegisteredModal();
  });
</script>
@endif

{{-- =========================================================================
     MODAL POPUP: RESET PASSWORD VIA OTP GMAIL
     ========================================================================= --}}
<div id="modalForgotPasswordLogin" class="fp-modal-backdrop" onclick="if(event.target===this) closeForgotPwdModal()">
  <div class="fp-modal-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #111827; font-family: 'Outfit', sans-serif;">Reset Password</h3>
      <button type="button" onclick="closeForgotPwdModal()" style="background: none; border: none; font-size: 20px; color: #64748b; cursor: pointer;">&times;</button>
    </div>

    <div id="modalFpAlert" style="display: none; padding: 10px 14px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-bottom: 14px; text-align: left;"></div>

    {{-- Step 1: Input Email --}}
    <div id="modalFpStepEmail">
      <p style="font-size: 12.5px; color: #4b5563; margin-bottom: 16px; line-height: 1.5; text-align: left;">
        Masukkan alamat Gmail Anda yang terdaftar. Kami akan mengirimkan 6 digit kode verifikasi OTP ke Gmail Anda.
      </p>
      <div style="margin-bottom: 16px; text-align: left;">
        <label for="modalFpEmailInput" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Alamat Gmail Terdaftar</label>
        <input type="email" id="modalFpEmailInput" class="login-text-input" placeholder="contoh: nama@gmail.com" required style="padding-left: 16px;">
      </div>
      <button type="button" id="btnModalFpSendOtp" onclick="modalFpSendOtp()" class="login-btn-primary">
        Kirim Kode OTP ke Gmail
      </button>
      <div style="margin-top: 14px; text-align: center;">
        <a href="{{ route('password.request') }}" style="font-size: 12px; color: #033f3c; text-decoration: underline; font-weight: 600;">Buka Halaman Reset Lengkap &rarr;</a>
      </div>
    </div>

    {{-- Step 2: Input OTP & New Password --}}
    <div id="modalFpStepOtp" style="display: none;">
      <p style="font-size: 12.5px; color: #4b5563; margin-bottom: 14px; text-align: left;">
        Kode OTP telah dikirim ke <strong id="modalFpTargetEmail"></strong>. Silakan periksa inbox atau spam.
      </p>
      <div style="margin-bottom: 12px; text-align: left;">
        <label for="modalFpOtpInput" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Kode OTP (6 Digit)</label>
        <input type="text" id="modalFpOtpInput" class="login-text-input" maxlength="6" placeholder="123456" style="text-align: center; font-size: 18px; font-weight: 700; letter-spacing: 4px; padding-left: 16px;">
      </div>
      <div style="margin-bottom: 12px; text-align: left;">
        <label for="modalFpNewPwd" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Password Baru</label>
        <input type="password" id="modalFpNewPwd" class="login-text-input" placeholder="Minimal 6 karakter" style="padding-left: 16px;">
      </div>
      <div style="margin-bottom: 16px; text-align: left;">
        <label for="modalFpConfirmPwd" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Konfirmasi Password Baru</label>
        <input type="password" id="modalFpConfirmPwd" class="login-text-input" placeholder="Ketik ulang password baru" style="padding-left: 16px;">
      </div>
      <button type="button" id="btnModalFpSave" onclick="modalFpResetPassword()" class="login-btn-primary">
        Simpan Password & Masuk
      </button>
      <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
        <button type="button" onclick="modalFpBackToEmail()" style="background: none; border: none; color: #64748b; cursor: pointer; text-decoration: underline;">Ubah Email</button>
        <button type="button" id="btnModalFpResend" onclick="modalFpResendOtp()" style="background: none; border: none; color: #033f3c; cursor: pointer; font-weight: 600;">Kirim Ulang OTP</button>
      </div>
    </div>
  </div>
</div>

<script>
  function togglePasswordVisibility(inputId, toggleBtn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var isPwd = input.type === 'password';
    input.type = isPwd ? 'text' : 'password';
    
    var icon = toggleBtn.querySelector('svg');
    if (icon) {
      if (isPwd) {
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      } else {
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      }
    }
  }

  function onLoginSubmit() {
    try {
      sessionStorage.setItem('f3d_from_login', '1');
    } catch(e) {}
  }

  // INTRO OVERLAY DARI LANDING PAGE
  (function() {
    var overlay = document.getElementById('f3dLoginPortalOverlay');
    var bar = document.getElementById('f3dLoginProgressBar');
    var gHouse = document.getElementById('f3dLoginGreenHouse');
    var sHouse = document.getElementById('f3dLoginSnakeHouse');
    var gDoor = document.getElementById('f3dLoginGreenDoor');
    var sDoor = document.getElementById('f3dLoginSnakeDoor');
    var animRaf = null;

    function setSnakePath(baseEl, snakeEl, progress, snakeLen) {
      if (!baseEl || !snakeEl) return;
      var total = 1000;
      snakeLen = snakeLen || 160;
      var head = progress * total;
      var tail = Math.max(0, head - snakeLen);

      if (progress <= 0) {
        baseEl.style.strokeDasharray = '0 1000';
        baseEl.style.strokeDashoffset = '0';
        snakeEl.style.strokeDasharray = '0 1000';
        snakeEl.style.strokeDashoffset = '0';
        snakeEl.style.opacity = '0';
        return;
      }

      baseEl.style.strokeDasharray = head.toFixed(2) + ' ' + total;
      baseEl.style.strokeDashoffset = '0';

      if (progress >= 1) {
        snakeEl.style.opacity = '0';
      } else {
        snakeEl.style.opacity = '1';
        var bodyLen = head - tail;
        snakeEl.style.strokeDasharray = '0 ' + tail.toFixed(2) + ' ' + bodyLen.toFixed(2) + ' ' + total;
        snakeEl.style.strokeDashoffset = '0';
      }
    }

    var fromLanding = false;
    try {
      fromLanding = sessionStorage.getItem('f3d_from_landing') === '1';
    } catch(e) {}

    var hasLoginError = {{ $hasLoginErrors ? 'true' : 'false' }};

    if (fromLanding && !hasLoginError && overlay) {
      overlay.style.display = 'flex';
      overlay.style.opacity = '1';
      overlay.style.visibility = 'visible';

      var startTime = null;
      var animDuration = 1800; // durasi smooth intro

      function stepAnim(timestamp) {
        if (!startTime) startTime = timestamp;
        var elapsed = timestamp - startTime;
        var p = Math.min(1, elapsed / animDuration);
        var ease = p < 0.5 ? 2 * p * p : -1 + (4 - 2 * p) * p;

        if (bar) bar.style.width = Math.round(p * 100) + '%';
        setSnakePath(gHouse, sHouse, ease, 180);
        setSnakePath(gDoor, sDoor, ease, 140);

        if (p < 1) {
          animRaf = requestAnimationFrame(stepAnim);
        } else {
          try {
            sessionStorage.removeItem('f3d_from_landing');
          } catch(e) {}
          overlay.style.opacity = '0';
          setTimeout(function() {
            overlay.style.display = 'none';
            overlay.style.visibility = 'hidden';
            document.documentElement.classList.remove('f3d-login-intro-active');
          }, 350);
        }
      }

      animRaf = requestAnimationFrame(stepAnim);
    } else if (overlay) {
      overlay.style.display = 'none';
      overlay.style.visibility = 'hidden';
      document.documentElement.classList.remove('f3d-login-intro-active');
    }
  })();

  // MODAL FORGOT PASSWORD LOGIC
  var _fpSavedEmail = '';

  function openForgotPwdModal(e) {
    if (e) e.preventDefault();
    var modal = document.getElementById('modalForgotPasswordLogin');
    if (!modal) return;
    var loginEmailVal = document.getElementById('loginEmailInput')?.value.trim();
    if (loginEmailVal && loginEmailVal.includes('@')) {
      document.getElementById('modalFpEmailInput').value = loginEmailVal;
    }
    document.getElementById('modalFpStepEmail').style.display = 'block';
    document.getElementById('modalFpStepOtp').style.display = 'none';
    document.getElementById('modalFpAlert').style.display = 'none';
    modal.style.display = 'flex';
  }

  function closeForgotPwdModal() {
    var modal = document.getElementById('modalForgotPasswordLogin');
    if (modal) modal.style.display = 'none';
  }

  function showFpAlert(msg, isSuccess) {
    var el = document.getElementById('modalFpAlert');
    if (!el) return;
    el.textContent = msg;
    el.style.display = 'block';
    if (isSuccess) {
      el.style.background = '#f0fdfa';
      el.style.color = '#0f766e';
      el.style.border = '1px solid #99f6e4';
    } else {
      el.style.background = '#fef2f2';
      el.style.color = '#dc2626';
      el.style.border = '1px solid #fecaca';
    }
  }

  function modalFpSendOtp() {
    var email = document.getElementById('modalFpEmailInput')?.value.trim();
    if (!email) {
      showFpAlert('Silakan masukkan alamat Gmail Anda.', false);
      return;
    }
    _fpSavedEmail = email;
    var btn = document.getElementById('btnModalFpSendOtp');
    btn.disabled = true;
    btn.textContent = 'Mengirim OTP...';

    fetch('{{ route("password.send_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email: email })
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Kirim Kode OTP ke Gmail';
      if (data.success) {
        showFpAlert(data.message, true);
        document.getElementById('modalFpTargetEmail').textContent = email;
        document.getElementById('modalFpStepEmail').style.display = 'none';
        document.getElementById('modalFpStepOtp').style.display = 'block';
      } else {
        showFpAlert(data.message || 'Gagal mengirim OTP.', false);
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.textContent = 'Kirim Kode OTP ke Gmail';
      showFpAlert('Terjadi kesalahan jaringan. Silakan coba lagi.', false);
    });
  }

  function modalFpBackToEmail() {
    document.getElementById('modalFpStepOtp').style.display = 'none';
    document.getElementById('modalFpStepEmail').style.display = 'block';
    document.getElementById('modalFpAlert').style.display = 'none';
  }

  function modalFpResendOtp() {
    modalFpSendOtp();
  }

  function modalFpResetPassword() {
    var otp = document.getElementById('modalFpOtpInput')?.value.trim();
    var pwd = document.getElementById('modalFpNewPwd')?.value;
    var pwdConf = document.getElementById('modalFpConfirmPwd')?.value;

    if (!otp || otp.length < 6) {
      showFpAlert('Masukkan 6 digit kode OTP yang valid.', false);
      return;
    }
    if (!pwd || pwd.length < 6) {
      showFpAlert('Password baru minimal 6 karakter.', false);
      return;
    }
    if (pwd !== pwdConf) {
      showFpAlert('Konfirmasi password tidak sesuai.', false);
      return;
    }

    var btn = document.getElementById('btnModalFpSave');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    fetch('{{ route("password.update_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: _fpSavedEmail,
        otp: otp,
        password: pwd,
        password_confirmation: pwdConf
      })
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'Simpan Password & Masuk';
      if (data.success) {
        showFpAlert(data.message || 'Password berhasil diperbarui!', true);
        setTimeout(function() {
          closeForgotPwdModal();
          var emailEl = document.getElementById('loginEmailInput');
          var pwdEl = document.getElementById('loginPasswordInput');
          if (emailEl) emailEl.value = _fpSavedEmail;
          if (pwdEl) pwdEl.value = pwd;
        }, 1200);
      } else {
        showFpAlert(data.message || 'Gagal mereset password.', false);
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.textContent = 'Simpan Password & Masuk';
      showFpAlert('Terjadi kesalahan jaringan.', false);
    });
  }
</script>
@endsection
