@extends('layouts.app')

@section('title', 'Login Akun - Kos Mas Robi')

@section('content')
<style>
  /* =========================================================================
     MODERN ANIMATED LOGIN EXPERIENCE
     ========================================================================= */
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

  /* Frosted Glass Card */
  .auth-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 460px;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-radius: 32px;
    padding: 38px 34px;
    box-shadow: 
      0 24px 60px -15px rgba(10, 86, 97, 0.18),
      0 8px 24px -6px rgba(15, 23, 42, 0.06),
      0 0 0 1px rgba(14, 116, 130, 0.08);
    animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes cardEntrance {
    0% {
      opacity: 0;
      transform: translateY(28px) scale(0.97);
    }
    100% {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Logo Badge with micro hover */
  .auth-logo-badge {
    width: 60px;
    height: 60px;
    margin: 0 auto 14px;
    border-radius: 20px;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 10px 25px rgba(14, 116, 130, 0.38);
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.35s ease;
  }
  .auth-logo-badge:hover {
    transform: translateY(-3px) rotate(4deg) scale(1.05);
    box-shadow: 0 14px 32px rgba(14, 116, 130, 0.48);
  }

  /* Role Switcher Tabs */
  .auth-role-tabs {
    display: flex;
    background: #f1f5f9;
    padding: 5px;
    border-radius: 16px;
    margin-bottom: 22px;
    position: relative;
    border: 1px solid #e2e8f0;
  }
  .auth-role-tab-btn {
    flex: 1;
    padding: 10px 12px;
    font-size: 0.82rem;
    font-weight: 700;
    border: none;
    background: transparent;
    border-radius: 12px;
    cursor: pointer;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .auth-role-tab-btn.active {
    background: #ffffff;
    color: #0a5661;
    box-shadow: 0 4px 12px rgba(10, 86, 97, 0.12), 0 1px 3px rgba(0, 0, 0, 0.05);
  }
  .auth-role-tab-btn:hover:not(.active) {
    color: #1e293b;
  }

  /* Quick Demo Helper Box */
  .auth-demo-box {
    background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
    border: 1px solid #ccfbf1;
    border-radius: 18px;
    padding: 13px 15px;
    margin-bottom: 20px;
    animation: fadeIn 0.4s ease;
  }
  .auth-demo-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 8px;
  }
  .auth-demo-pill {
    padding: 9px 12px;
    border-radius: 12px;
    border: 1px solid transparent;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s ease;
    font-family: inherit;
  }
  .auth-demo-pill.admin-pill {
    background: #0a5661;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25);
  }
  .auth-demo-pill.admin-pill:hover {
    background: #08434c;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(10, 86, 97, 0.35);
  }
  .auth-demo-pill.tenant-pill {
    background: #ffffff;
    color: #0e7482;
    border-color: #99f6e4;
    box-shadow: 0 2px 8px rgba(14, 116, 130, 0.08);
  }
  .auth-demo-pill.tenant-pill:hover {
    border-color: #0e7482;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 116, 130, 0.16);
  }

  /* Modern Animated Input Groups */
  .auth-field-group {
    position: relative;
    margin-bottom: 18px;
  }
  .auth-field-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 7px;
  }
  .auth-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }
  .auth-input-icon {
    position: absolute;
    left: 14px;
    width: 20px;
    height: 20px;
    color: #94a3b8;
    pointer-events: none;
    transition: color 0.25s ease;
  }
  .auth-input-control {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 13px 16px 13px 44px;
    font-size: 0.92rem;
    color: #0f172a;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
    font-family: inherit;
  }
  .auth-input-control:focus {
    background: #ffffff;
    border-color: #0e7482;
    box-shadow: 0 0 0 4px rgba(14, 116, 130, 0.14);
    outline: none;
  }
  .auth-input-control:focus + .auth-input-icon,
  .auth-input-wrapper:focus-within .auth-input-icon {
    color: #0e7482;
  }

  .auth-pwd-toggle {
    position: absolute;
    right: 12px;
    background: transparent;
    border: none;
    padding: 6px;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s, background 0.2s;
  }
  .auth-pwd-toggle:hover {
    color: #0e7482;
    background: rgba(14, 116, 130, 0.08);
  }

  /* Submit Button with Gradient & Shimmer */
  .auth-btn-primary {
    position: relative;
    width: 100%;
    padding: 14px 20px;
    font-size: 0.96rem;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    border: none;
    border-radius: 16px;
    cursor: pointer;
    box-shadow: 0 8px 22px rgba(14, 116, 130, 0.35);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    margin-top: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .auth-btn-primary:hover {
    background: linear-gradient(135deg, #08434c 0%, #0c616d 100%);
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(14, 116, 130, 0.45);
  }
  .auth-btn-primary:active {
    transform: translateY(0) scale(0.98);
  }
  .auth-btn-primary::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: skewX(-20deg);
    animation: btnShimmer 5s infinite;
  }
  @keyframes btnShimmer {
    0%, 75% { left: -100%; }
    100% { left: 160%; }
  }

  .auth-btn-google {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 13px 20px;
    font-size: 0.92rem;
    font-weight: 700;
    color: #1e293b;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
    cursor: pointer;
  }
  .auth-btn-google:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  }
  .auth-btn-google:active {
    transform: translateY(0) scale(0.98);
  }

  /* Badge Helper */
  .auth-role-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.74rem;
    font-weight: 700;
    background: rgba(14, 116, 130, 0.1);
    color: #0a5661;
    margin-top: 4px;
    animation: fadeIn 0.3s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 480px) {
    .auth-viewport {
      padding: 16px 12px;
    }
    .auth-card {
      padding: 26px 18px;
      border-radius: 24px;
    }
    .auth-input-control {
      font-size: 16px;
    }
  }
</style>

<div class="auth-viewport">
  {{-- Atmospheric Gradient Blobs --}}
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-teal-2"></div>
  <div class="auth-ambient-orb orb-coral"></div>

  {{-- Main Glass Card --}}
  <div class="auth-card">

    {{-- Banner: Already Logged In --}}
    @auth
    <div style="background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%); border: 1.5px solid #99f6e4; border-radius: 18px; padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
      <div>
        <div style="font-size: 0.78rem; font-weight: 700; color: #0a5661; margin-bottom: 3px;">
          ✅ Anda sudah masuk sebagai: <strong>{{ Auth::user()->name }}</strong>
          ({{ Auth::user()->isOwner() ? 'Admin' : 'Pelanggan' }})
        </div>
        <div style="font-size: 0.74rem; color: #0e7482;">Untuk ganti akun, logout terlebih dahulu atau langsung masuk dengan akun lain di bawah.</div>
      </div>
      <div style="display: flex; gap: 8px; flex-shrink: 0;">
        <a href="{{ Auth::user()->isOwner() ? route('admin.dashboard') : route('tenant.portal') }}"
           style="padding: 7px 14px; background: #0a5661; color: #fff; border-radius: 10px; font-size: 0.78rem; font-weight: 700; text-decoration: none; white-space: nowrap;">
          Ke Dashboard →
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
          @csrf
          <button type="submit" style="padding: 7px 14px; background: transparent; border: 1.5px solid #0e7482; color: #0e7482; border-radius: 10px; font-size: 0.78rem; font-weight: 700; cursor: pointer; white-space: nowrap;">
            Logout
          </button>
        </form>
      </div>
    </div>
    @endauth

    {{-- Brand Logo & Title --}}
    <div style="text-align: center; margin-bottom: 22px;">
      <a href="{{ route('home') }}" class="auth-logo-badge" title="Ke Beranda Kos Mas Robi">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
          <path d="M9 21V12h6v9"/>
        </svg>
      </a>
      <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.02em;">Masuk ke Akun</h2>
      <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Sistem Manajemen Hunian Kos Mas Robi</p>
      
    </div>

    {{-- Validation Error Alerts --}}
    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; margin-bottom: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.3s ease;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    @if (session('info'))
      <div style="background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; margin-bottom: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span>{{ session('info') }}</span>
      </div>
    @endif

    {{-- Login Form --}}
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
                 placeholder="admin@gmail.com atau dimas@gmail.com" 
                 value="{{ old('email') }}" 
                 required 
                 autofocus>
        </div>
      </div>

      {{-- Password Field --}}
      <div class="auth-field-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
          <label for="loginPassword" class="auth-field-label" style="margin-bottom: 0;">Kata Sandi</label>
          <a href="{{ route('password.request') }}" style="font-size: 0.78rem; color: #0e7482; font-weight: 700; text-decoration: none; transition: color 0.2s;">Lupa Password?</a>
        </div>
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
                 style="padding-right: 46px;">
          
          <button type="button" class="auth-pwd-toggle" id="btnTogglePassword" onclick="togglePasswordVisibility()" title="Lihat Kata Sandi">
            <svg id="iconEye" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="3" r="3"></circle>
            </svg>
            <svg id="iconEyeOff" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>
      </div>

      {{-- Remember Me --}}
      <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.83rem; margin-bottom: 18px;">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #475569; font-weight: 500;">
          <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #0e7482; cursor: pointer;">
          <span>Ingat saya di perangkat ini</span>
        </label>
      </div>

      {{-- Submit Button --}}
      <button type="submit" class="auth-btn-primary" id="btnSubmitLogin">
        <span id="btnSubmitText">Masuk Sekarang</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>

      {{-- Divider --}}
      <div style="display: flex; align-items: center; margin: 16px 0 14px; color: #94a3b8; font-size: 0.76rem;">
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        <span style="padding: 0 12px; font-weight: 700; letter-spacing: 0.05em;">ATAU</span>
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
      </div>

      {{-- Google Login Button --}}
      <a href="{{ route('auth.google') }}" class="auth-btn-google">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
          <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
          <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
        </svg>
        <span>Masuk dengan Akun Google</span>
      </a>
    </form>

    {{-- Switch to Register --}}
    <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; font-size: 0.86rem; color: #64748b;">
      Belum memiliki akun? 
      <a href="{{ route('register') }}" style="color: #0a5661; font-weight: 700; text-decoration: none; margin-left: 4px; border-bottom: 1.5px solid rgba(10, 86, 97, 0.3); transition: all 0.2s;">
        Daftar Akun Baru &rarr;
      </a>
    </div>

    {{-- Return to Landing Page --}}
    <div style="text-align: center; margin-top: 14px;">
      <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #94a3b8; text-decoration: none; font-weight: 600; transition: color 0.2s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        <span>Kembali ke Halaman Utama</span>
      </a>
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
