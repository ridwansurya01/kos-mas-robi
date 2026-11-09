@extends('layouts.app')

@section('title', 'Log in — ' . (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING'))

@section('content')
@php
  $setting = rescue(fn() => \App\Models\Setting::first(), null);
  $landing = $setting ? ($setting->landing_content ?? []) : [];
  $loginContent = $setting ? $setting->getLoginPageContent() : [];

  // Sinkronisasi dinamis gambar latar dengan Gambar Utama Landing Page
  $rawHeroImg = $landing['hero_image'] ?? 'assets/images/landing/hero_corridor_teal.webp';
  if (!empty($rawHeroImg) && (str_starts_with($rawHeroImg, 'http://') || str_starts_with($rawHeroImg, 'https://'))) {
      $loginHeroImageUrl = $rawHeroImg;
  } elseif (!empty($rawHeroImg)) {
      $loginHeroImageUrl = asset(ltrim($rawHeroImg, '/'));
  } else {
      $loginHeroImageUrl = asset('assets/images/landing/hero_corridor_teal.webp');
  }

  // Jika menggunakan gambar koridor bawaan desain, gunakan backdrop master bersih 100%
  $isCustomHero = !empty($rawHeroImg) && !str_contains($rawHeroImg, 'hero_corridor_teal') && !str_contains($rawHeroImg, 'kos_exterior');
  $desktopLoginBgUrl = $isCustomHero ? $loginHeroImageUrl : asset('assets/images/landing/login_desktop_master_clean.webp');

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
    --login-primary: #235352;
    --login-primary-hover: #1b4241;
    --login-accent: #0d9488;
    --login-text-dark: #111827;
    --login-text-label: #374151;
    --login-text-muted: #4b5563;
    --login-border: #9ca3af;
    --login-border-focus: #235352;
  }

  /* Reset Viewport */
  html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #ffffff;
    overflow: hidden;
  }

  /* Shared Form Styles */
  .login-back-btn {
    position: absolute;
    top: 26px;
    left: 28px;
    z-index: 50;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1.5px solid rgba(209, 213, 219, 0.85);
    border-radius: 9999px;
    color: #374151;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .login-back-btn:hover {
    background: #ffffff;
    color: var(--login-primary);
    border-color: var(--login-primary);
    transform: translateX(-2px);
    box-shadow: 0 6px 18px rgba(35, 83, 82, 0.15);
  }

  .login-back-btn svg {
    transition: transform 0.2s ease;
  }

  .login-back-btn:hover svg {
    transform: translateX(-3px);
  }

  .login-brand-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 22px;
  }

  .login-brand-link {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: transform 0.2s ease;
  }

  .login-brand-link:hover {
    transform: translateY(-1px);
  }

  .login-brand-logo-img {
    width: 48px;
    height: 48px;
    object-fit: contain;
    border-radius: 13px;
    box-shadow: 0 6px 16px rgba(35, 83, 82, 0.22);
    margin-bottom: 8px;
    transition: transform 0.2s ease;
  }

  .login-brand-link:hover .login-brand-logo-img {
    transform: scale(1.05);
  }

  .login-brand-title {
    font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
    font-size: 21px;
    font-weight: 800;
    color: var(--login-text-dark);
    letter-spacing: -0.015em;
    margin: 0;
    line-height: 1.2;
    text-align: center;
  }

  .login-brand-subtitle {
    font-size: 11px;
    font-weight: 700;
    color: #0d9488;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-top: 3px;
  }

  .login-form-title {
    font-size: 27px;
    font-weight: 800;
    color: var(--login-text-dark);
    margin: 0 0 24px;
    letter-spacing: -0.01em;
    font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
    text-align: center;
  }

  .login-field-group {
    margin-bottom: 14px;
    text-align: left;
  }

  .login-field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--login-text-label);
    margin-bottom: 6px;
    padding-left: 20px;
  }

  .login-input-pill {
    width: 100%;
    height: 44px;
    border-radius: 9999px;
    border: 1.5px solid var(--login-border);
    background: #ffffff;
    padding: 0 20px;
    font-size: 14px;
    color: var(--login-text-dark);
    outline: none;
    box-sizing: border-box;
    transition: all 0.2s ease;
    font-family: inherit;
    -webkit-appearance: none;
  }

  /* Suppress Chrome autofill blue styling */
  input:-webkit-autofill,
  input:-webkit-autofill:hover, 
  input:-webkit-autofill:focus, 
  input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
    -webkit-text-fill-color: var(--login-text-dark) !important;
    transition: background-color 5000s ease-in-out 0s;
  }

  .login-input-pill:focus {
    border-color: var(--login-border-focus);
    box-shadow: 0 0 0 3px rgba(35, 83, 82, 0.16);
  }

  .login-pwd-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }

  .login-pwd-wrapper .login-input-pill {
    padding-right: 46px;
  }

  .login-pwd-toggle {
    position: absolute;
    right: 14px;
    background: none;
    border: none;
    color: #6b7280;
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

  .login-submit-btn {
    width: 100%;
    height: 44px;
    border-radius: 9999px;
    background: var(--login-primary);
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
    margin-top: 6px;
    box-shadow: 0 4px 12px rgba(35, 83, 82, 0.22);
  }

  .login-submit-btn:hover {
    background: var(--login-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(35, 83, 82, 0.3);
  }

  .login-submit-btn:active {
    transform: translateY(1px);
  }

  .login-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 18px 0 16px;
  }

  .login-divider-line {
    flex: 1;
    height: 1px;
    background: var(--login-border);
  }

  .login-divider-text {
    font-size: 12px;
    color: var(--login-text-muted);
    font-weight: 500;
  }

  .login-google-btn {
    width: 100%;
    height: 44px;
    border-radius: 9999px;
    border: 1.5px solid #d1d5db;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 13.5px;
    font-weight: 700;
    color: #1f2937;
    text-decoration: none;
    box-sizing: border-box;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    margin-bottom: 18px;
    cursor: pointer;
  }

  .login-google-btn:hover {
    background: #f8fafc;
    border-color: var(--login-primary);
    color: #0a5661;
    transform: translateY(-1.5px);
    box-shadow: 0 6px 16px rgba(35, 83, 82, 0.12);
  }

  .login-google-btn:active {
    transform: translateY(1px);
  }

  .login-google-btn .login-google-icon {
    flex-shrink: 0;
    transition: transform 0.2s ease;
  }

  .login-google-btn:hover .login-google-icon {
    transform: scale(1.08);
  }

  .login-forgot-link {
    display: block;
    text-align: center;
    color: var(--login-accent);
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
    cursor: pointer;
  }

  .login-forgot-link:hover {
    text-decoration: underline;
  }

  .login-back-home-wrap {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .login-back-home-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 16px;
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #0a5661;
    background: rgba(10, 86, 97, 0.05);
    border: 1px solid rgba(10, 86, 97, 0.12);
    border-radius: 9999px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .login-back-home-link:hover {
    background: rgba(10, 86, 97, 0.1);
    color: #063c44;
    border-color: rgba(10, 86, 97, 0.24);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(10, 86, 97, 0.08);
  }

  .login-back-home-link svg {
    transition: transform 0.2s ease;
  }

  .login-back-home-link:hover svg {
    transform: translateX(-3px);
  }

  .login-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 12px;
    margin-bottom: 14px;
    font-weight: 600;
    text-align: left;
  }

  .login-alert-info {
    background: #f0fdfa;
    border: 1px solid #99f6e4;
    color: #0f766e;
    padding: 8px 14px;
    border-radius: 12px;
    font-size: 12px;
    margin-bottom: 14px;
    font-weight: 600;
    text-align: left;
  }

  .login-auth-banner {
    background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
    border: 1.5px solid #99f6e4;
    border-radius: 12px;
    padding: 8px 12px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 0.74rem;
    font-weight: 700;
    color: #0a5661;
  }

  /* =========================================================================
     LOADING INTRO OVERLAY (SNAKE TRACER LOGO ON PURE WHITE)
     ========================================================================= */
  .f3d-dash-portal-overlay {
    position: fixed;
    inset: 0;
    z-index: 999999999;
    background: #ffffff;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 1;
    visibility: visible;
    overflow: hidden;
    cursor: default;
    transition: 
      opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1),
      transform 0.7s cubic-bezier(0.16, 1, 0.3, 1),
      filter 0.7s ease,
      visibility 0.7s ease;
  }
  html.f3d-login-intro-active .f3d-dash-portal-overlay {
    display: flex !important;
  }

  .f3d-dash-loader-wrap {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 24px;
    transition: transform 0.55s ease, opacity 0.55s ease;
  }

  .f3d-dash-svg-box {
    width: 165px;
    height: 165px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
  }

  .f3d-dash-svg-canvas {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .f3d-dash-faint-track {
    stroke: rgba(10, 86, 97, 0.08);
  }

  .f3d-dash-drawn-green {
    stroke: #0a5661;
  }

  .f3d-dash-snake-tracer {
    filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.75));
  }

  .f3d-dash-loading-track {
    width: 190px;
    height: 3.5px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
    margin: 0 auto 12px;
  }

  .f3d-dash-loading-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #0a5661 0%, #10b981 100%);
    border-radius: 999px;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    transition: width 0.08s linear;
  }

  .f3d-dash-loading-label {
    font-family: 'Outfit', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    color: #0a5661;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0;
  }

  .f3d-dash-portal-overlay.portal-exit {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: scale(1.04);
    filter: blur(8px);
  }

  .f3d-dash-portal-overlay.portal-exit .f3d-dash-loader-wrap {
    transform: scale(1.08);
    opacity: 0;
  }

  /* =========================================================================
     TAMPILAN 1: LAPTOP, KOMPUTER, TABLET (100% IDENTIK MASTER REFERENCE)
     ========================================================================= */
  @media (min-width: 768px) {
    .view-mobile {
      display: none !important;
    }

    .view-desktop {
      display: flex !important;
      position: relative;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
      background-color: #ffffff;
      background-image: url('{{ $desktopLoginBgUrl }}');
      background-size: 100% 100%;
      background-position: center;
      background-repeat: no-repeat;
      align-items: center;
    }

    .desktop-form-box {
      position: absolute;
      left: calc((91 / 941) * 100vw);
      top: 50%;
      transform: translateY(-50%);
      width: min(290px, 30vw);
      box-sizing: border-box;
      z-index: 10;
    }
  }

  /* =========================================================================
     TAMPILAN 2: HP / SMARTPHONE / IPHONE (100% SESUAI CONTOH KEDUA)
     ========================================================================= */
  @media (max-width: 767.98px) {
    .view-desktop {
      display: none !important;
    }

    .login-back-btn {
      position: fixed;
      top: 16px;
      left: 16px;
      padding: 7px 14px;
      font-size: 12px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }

    .view-mobile {
      display: flex !important;
      position: relative;
      min-height: 100vh;
      min-height: 100dvh;
      width: 100%;
      overflow-y: auto;
      overflow-x: hidden;
      background-image: url('{{ $desktopLoginBgUrl }}');
      background-size: cover;
      background-position: 68% top;
      background-repeat: no-repeat;
      align-items: center;
      justify-content: center;
      padding: 68px 16px 36px;
      box-sizing: border-box;
    }

    /* Mobile Floating Frosted Glass Card */
    .mobile-floating-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 340px;
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-radius: 36px;
      border: 1.5px solid rgba(255, 255, 255, 0.85);
      padding: 34px 22px 28px;
      box-sizing: border-box;
      box-shadow: 
        0 24px 60px -10px rgba(0, 0, 0, 0.35),
        inset 0 1px 2px rgba(255, 255, 255, 0.95);
      text-align: center;
    }

    .mobile-floating-card .login-form-title {
      font-size: 26px;
      font-weight: 800;
      color: #111827;
      margin-bottom: 22px;
      letter-spacing: -0.01em;
      font-family: 'Outfit', sans-serif;
      text-align: center;
    }

    .mobile-floating-card .login-field-group {
      margin-bottom: 14px;
      text-align: left;
    }

    .mobile-floating-card .login-field-label {
      display: block;
      font-size: 11.5px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 5px;
      padding-left: 20px;
    }

    .mobile-floating-card .login-input-pill {
      background: rgba(255, 255, 255, 0.95);
    }
  }

  /* Modal Lupa Password Pop-up */
  .fp-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: rgba(3, 35, 40, 0.78);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }
  .fp-modal-card {
    position: relative;
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border-radius: 28px;
    padding: 32px 26px;
    box-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.35);
    border: 1.5px solid rgba(13, 148, 136, 0.25);
    text-align: center;
    box-sizing: border-box;
    animation: fpPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes fpPopIn {
    0% { opacity: 0; transform: scale(0.92) translateY(12px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
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

        <!-- Garis Faint Panduan -->
        <path class="f3d-dash-faint-track" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path class="f3d-dash-faint-track" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Garis Hijau Rumah & Ular -->
        <path id="f3dLoginGreenHouse" class="f3d-dash-drawn-green" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dLoginSnakeHouse" class="f3d-dash-snake-tracer" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="url(#loginSnakeGreenGrad)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Garis Hijau Pintu Dalam & Ular -->
        <path id="f3dLoginGreenDoor" class="f3d-dash-drawn-green" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dLoginSnakeDoor" class="f3d-dash-snake-tracer" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="url(#loginSnakeGreenGrad)" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>

    <!-- Tampilan Loading Saja -->
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
     TAMPILAN 1: LAPTOP, KOMPUTER, TABLET (100% PERSIS CONTOH MASTER REFERENCE)
     ========================================================================= --}}
<div class="view-desktop">
  <div class="desktop-form-box">

      @auth
      <div class="login-auth-banner">
        <span>Sudah masuk: <strong>{{ Auth::user()->name }}</strong></span>
        <a href="{{ Auth::user()->isStaffOrOwner() ? route('admin.dashboard') : route('tenant.portal') }}" style="color: #0a5661; text-decoration: underline;">Dashboard &rarr;</a>
      </div>
      @endauth

      <div class="login-brand-header">
        <a href="{{ route('home') }}" class="login-brand-link" title="Kembali ke Beranda">
          <img src="{{ asset('assets/images/logo_kos_jember.png') }}" 
               alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}" 
               class="login-brand-logo-img" 
               width="48" 
               height="48">
          <h1 class="login-brand-title">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</h1>
        </a>
      </div>

      @if ($errors->any())
        <div class="login-alert-error">{{ $errors->first() }}</div>
      @endif

      @if (session('info'))
        <div class="login-alert-info">{{ session('info') }}</div>
      @endif

      <form action="{{ route('login.post') }}" method="POST" onsubmit="onLoginSubmit()">
        @csrf

        {{-- Email --}}
        <div class="login-field-group">
          <label for="desktopLoginEmail" class="login-field-label">Login email</label>
          <input type="text" 
                 id="desktopLoginEmail" 
                 name="email" 
                 class="login-input-pill" 
                 value="{{ old('email') }}" 
                 required 
                 autofocus>
        </div>

        {{-- Password --}}
        <div class="login-field-group">
          <label for="desktopLoginPwd" class="login-field-label">Password</label>
          <div class="login-pwd-wrapper">
            <input type="password" 
                   id="desktopLoginPwd" 
                   name="password" 
                   class="login-input-pill" 
                   required>
            <button type="button" class="login-pwd-toggle" onclick="togglePasswordVisibility('desktopLoginPwd', this)" title="Lihat Kata Sandi">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="login-submit-btn">Log in</button>

        <div class="login-divider">
          <div class="login-divider-line"></div>
          <span class="login-divider-text">or log in with</span>
          <div class="login-divider-line"></div>
        </div>

        <a href="{{ route('auth.google') }}" class="login-google-btn" title="Masuk dengan Google" aria-label="Masuk dengan Google">
          <svg class="login-google-icon" width="20" height="20" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
          </svg>
          <span>Masuk dengan Google</span>
        </a>

        <a href="{{ route('password.request') }}" onclick="openForgotPwdModal(event)" class="login-forgot-link">Forgot login or password?</a>

        <div class="login-back-home-wrap">
          <a href="{{ route('home') }}" class="login-back-home-link" title="Kembali ke Beranda">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Beranda</span>
          </a>
        </div>
      </form>
  </div>
</div>

{{-- =========================================================================
     TAMPILAN 2: HP / SMARTPHONE / IPHONE (100% SESUAI CONTOH KEDUA)
     ========================================================================= --}}
<div class="view-mobile">
  <div class="mobile-floating-card">
    <div class="login-brand-header">
      <a href="{{ route('home') }}" class="login-brand-link" title="Kembali ke Beranda">
        <img src="{{ asset('assets/images/logo_kos_jember.png') }}" 
             alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}" 
             class="login-brand-logo-img" 
             width="48" 
             height="48">
        <h1 class="login-brand-title">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</h1>
      </a>
    </div>

    @if ($errors->any())
      <div class="login-alert-error">{{ $errors->first() }}</div>
    @endif

    @if (session('info'))
      <div class="login-alert-info">{{ session('info') }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" onsubmit="onLoginSubmit()">
      @csrf

      {{-- Email --}}
      <div class="login-field-group">
        <label for="mobileLoginEmail" class="login-field-label">Login email</label>
        <input type="text" 
               id="mobileLoginEmail" 
               name="email" 
               class="login-input-pill" 
               value="{{ old('email') }}" 
               required>
      </div>

      {{-- Password --}}
      <div class="login-field-group">
        <label for="mobileLoginPwd" class="login-field-label">Password</label>
        <div class="login-pwd-wrapper">
          <input type="password" 
                 id="mobileLoginPwd" 
                 name="password" 
                 class="login-input-pill" 
                 required>
          <button type="button" class="login-pwd-toggle" onclick="togglePasswordVisibility('mobileLoginPwd', this)" title="Lihat Kata Sandi">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="login-submit-btn">Log in</button>

      <div class="login-divider">
        <div class="login-divider-line"></div>
        <span class="login-divider-text">or log in with</span>
        <div class="login-divider-line"></div>
      </div>

      <a href="{{ route('auth.google') }}" class="login-google-btn" title="Masuk dengan Google" aria-label="Masuk dengan Google">
        <svg class="login-google-icon" width="20" height="20" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
          <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
          <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
        </svg>
        <span>Masuk dengan Google</span>
      </a>

      <a href="{{ route('password.request') }}" onclick="openForgotPwdModal(event)" class="login-forgot-link">Forgot login or password?</a>

      <div class="login-back-home-wrap">
        <a href="{{ route('home') }}" class="login-back-home-link" title="Kembali ke Beranda">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Kembali ke Beranda</span>
        </a>
      </div>
    </form>
  </div>
</div>

{{-- =========================================================================
     MODAL POPUP: LUPA PASSWORD / RESET VIA OTP GMAIL
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
        <input type="email" id="modalFpEmailInput" class="login-input-pill" placeholder="contoh: nama@gmail.com" required style="border-radius: 14px; font-size: 13.5px;">
      </div>
      <button type="button" id="btnModalFpSendOtp" onclick="modalFpSendOtp()" class="login-submit-btn" style="border-radius: 14px; font-size: 13.5px;">
        Kirim Kode OTP ke Gmail
      </button>
      <div style="margin-top: 14px;">
        <a href="{{ route('password.request') }}" style="font-size: 12px; color: #0d9488; text-decoration: underline;">Buka Halaman Reset Lengkap &rarr;</a>
      </div>
    </div>

    {{-- Step 2: Input OTP & New Password --}}
    <div id="modalFpStepOtp" style="display: none;">
      <p style="font-size: 12.5px; color: #4b5563; margin-bottom: 14px; text-align: left;">
        Kode OTP telah dikirim ke <strong id="modalFpTargetEmail"></strong>. Silakan periksa inbox atau spam.
      </p>
      <div style="margin-bottom: 12px; text-align: left;">
        <label for="modalFpOtpInput" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Kode OTP (6 Digit)</label>
        <input type="text" id="modalFpOtpInput" class="login-input-pill" maxlength="6" placeholder="123456" style="border-radius: 14px; text-align: center; font-size: 18px; font-weight: 700; letter-spacing: 4px;">
      </div>
      <div style="margin-bottom: 12px; text-align: left;">
        <label for="modalFpNewPwd" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Password Baru</label>
        <input type="password" id="modalFpNewPwd" class="login-input-pill" placeholder="Minimal 6 karakter" style="border-radius: 14px; font-size: 13.5px;">
      </div>
      <div style="margin-bottom: 16px; text-align: left;">
        <label for="modalFpConfirmPwd" style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;">Konfirmasi Password Baru</label>
        <input type="password" id="modalFpConfirmPwd" class="login-input-pill" placeholder="Ketik ulang password baru" style="border-radius: 14px; font-size: 13.5px;">
      </div>
      <button type="button" id="btnModalFpSave" onclick="modalFpResetPassword()" class="login-submit-btn" style="border-radius: 14px; font-size: 13.5px;">
        Simpan Password & Masuk
      </button>
      <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
        <button type="button" onclick="modalFpBackToEmail()" style="background: none; border: none; color: #64748b; cursor: pointer; text-decoration: underline;">Ubah Email</button>
        <button type="button" id="btnModalFpResend" onclick="modalFpResendOtp()" style="background: none; border: none; color: #0d9488; cursor: pointer; font-weight: 600;">Kirim Ulang OTP</button>
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
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      } else {
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
    }
  }

  // 1. TRIGGER ROLE INTRO LOADING PADA SUBMIT FORM
  function onLoginSubmit() {
    try {
      sessionStorage.setItem('f3d_from_login', '1');
    } catch(e) {}
  }

  // 2. LOADING INTRO DARI LANDING PAGE KE LOGIN (SNAKE TRACER LOGO)
  (function() {
    var overlay = document.getElementById('f3dLoginPortalOverlay');
    var bar = document.getElementById('f3dLoginProgressBar');
    var gHouse = document.getElementById('f3dLoginGreenHouse');
    var sHouse = document.getElementById('f3dLoginSnakeHouse');
    var gDoor = document.getElementById('f3dLoginGreenDoor');
    var sDoor = document.getElementById('f3dLoginSnakeDoor');
    var portalTimer = null;
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

    function dismissLoginPortal() {
      if (portalTimer) clearTimeout(portalTimer);
      if (animRaf) cancelAnimationFrame(animRaf);
      if (!overlay) return;

      setSnakePath(gHouse, sHouse, 1);
      setSnakePath(gDoor, sDoor, 1);
      if (bar) bar.style.width = '100%';

      overlay.classList.add('portal-exit');
      setTimeout(function() {
        overlay.style.display = 'none';
        document.documentElement.classList.remove('f3d-login-intro-active');
      }, 700);

      try {
        sessionStorage.removeItem('f3d_from_landing');
      } catch(e) {}
    }

    function startLoginSnakeAnimation() {
      setSnakePath(gHouse, sHouse, 0);
      setSnakePath(gDoor, sDoor, 0);
      if (bar) bar.style.width = '0%';

      var startTime = performance.now();
      var houseDuration = 1800;
      var doorDelay = 600;
      var doorDuration = 1200;
      var totalDuration = 2300;

      function step(now) {
        var elapsed = now - startTime;

        var pHouse = Math.min(1, elapsed / houseDuration);
        setSnakePath(gHouse, sHouse, pHouse, 150);

        if (elapsed < doorDelay) {
          setSnakePath(gDoor, sDoor, 0);
        } else {
          var pDoor = Math.min(1, (elapsed - doorDelay) / doorDuration);
          setSnakePath(gDoor, sDoor, pDoor, 130);
        }

        var pBar = Math.min(1, elapsed / totalDuration);
        if (bar) bar.style.width = (pBar * 100).toFixed(1) + '%';

        if (elapsed < totalDuration) {
          animRaf = requestAnimationFrame(step);
        } else {
          setSnakePath(gHouse, sHouse, 1);
          setSnakePath(gDoor, sDoor, 1);
          if (bar) bar.style.width = '100%';

          portalTimer = setTimeout(function() {
            dismissLoginPortal();
          }, 350);
        }
      }

      animRaf = requestAnimationFrame(step);
    }

    function initIntro() {
      var fromLanding = false;
      try {
        fromLanding = sessionStorage.getItem('f3d_from_landing') === '1';
      } catch(e) {}

      var hasError = {{ $hasLoginErrors ? 'true' : 'false' }};
      if (fromLanding && !hasError && overlay) {
        overlay.style.display = 'flex';
        overlay.style.opacity = '1';
        overlay.style.visibility = 'visible';
        startLoginSnakeAnimation();
      } else if (overlay) {
        overlay.style.display = 'none';
        document.documentElement.classList.remove('f3d-login-intro-active');
      }
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initIntro);
    } else {
      initIntro();
    }
  })();

  // 3. MODAL LUPA PASSWORD (AJAX OTP FLOW)
  function openForgotPwdModal(e) {
    if (e && e.preventDefault) e.preventDefault();
    var modal = document.getElementById('modalForgotPasswordLogin');
    if (modal) {
      modal.style.display = 'flex';
      var emailVal = document.getElementById('desktopLoginEmail')?.value || document.getElementById('mobileLoginEmail')?.value || '';
      if (emailVal && emailVal.includes('@')) {
        document.getElementById('modalFpEmailInput').value = emailVal;
      }
    }
  }

  function closeForgotPwdModal() {
    var modal = document.getElementById('modalForgotPasswordLogin');
    if (modal) modal.style.display = 'none';
  }

  var _fpSavedEmail = '';

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
          // Masukkan email dan password ke form
          var emailEl = document.getElementById('desktopLoginEmail') || document.getElementById('mobileLoginEmail');
          var pwdEl = document.getElementById('desktopLoginPwd') || document.getElementById('mobileLoginPwd');
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
