@extends('layouts.app')

@section('title', 'Daftar Akun Baru - Kos Mas Robi')

@section('content')
<style>
  /* =========================================================================
     MODERN ANIMATED REGISTER EXPERIENCE
     ========================================================================= */
  .auth-viewport {
    position: relative;
    min-height: 100vh;
    min-height: 100dvh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 14px;
    background: #f4fbfb;
    overflow: hidden;
    font-family: inherit;
  }

  /* Animated Ambient Mesh Orbs */
  .auth-ambient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(85px);
    pointer-events: none;
    z-index: 0;
    opacity: 0.55;
    animation-timing-function: ease-in-out;
    animation-iteration-count: infinite;
    animation-direction: alternate;
  }
  .orb-teal-1 {
    width: 420px;
    height: 420px;
    top: -120px;
    right: -80px;
    background: radial-gradient(circle, rgba(14, 116, 130, 0.24) 0%, rgba(10, 86, 97, 0.06) 70%, transparent 100%);
    animation: orbFloat1 15s infinite;
  }
  .orb-teal-2 {
    width: 380px;
    height: 380px;
    bottom: -100px;
    left: -60px;
    background: radial-gradient(circle, rgba(10, 86, 97, 0.2) 0%, rgba(20, 184, 166, 0.1) 70%, transparent 100%);
    animation: orbFloat2 17s infinite;
  }
  .orb-coral {
    width: 280px;
    height: 280px;
    top: 30%;
    left: 10%;
    background: radial-gradient(circle, rgba(244, 63, 94, 0.1) 0%, transparent 70%);
    animation: orbFloat3 13s infinite;
  }

  @keyframes orbFloat1 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-40px, 30px) scale(1.05); }
    100% { transform: translate(20px, 50px) scale(0.96); }
  }
  @keyframes orbFloat2 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -30px) scale(1.06); }
    100% { transform: translate(-30px, -40px) scale(0.94); }
  }
  @keyframes orbFloat3 {
    0% { transform: translate(0, 0) scale(0.95); opacity: 0.3; }
    50% { transform: translate(20px, 20px) scale(1.1); opacity: 0.6; }
    100% { transform: translate(-15px, -15px) scale(1); opacity: 0.4; }
  }

  /* Frosted Glass Card - Compact & Clean */
  .auth-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 480px;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 26px;
    padding: 26px 26px;
    box-shadow: 
      0 20px 48px -12px rgba(10, 86, 97, 0.16),
      0 6px 18px -4px rgba(15, 23, 42, 0.05),
      0 0 0 1px rgba(14, 116, 130, 0.06);
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

  /* Logo Badge */
  .auth-logo-badge {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(14, 116, 130, 0.32);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  }
  .auth-logo-badge:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 10px 24px rgba(14, 116, 130, 0.42);
  }

  /* Compact Input Groups */
  .auth-field-group {
    position: relative;
    margin-bottom: 11px;
  }
  .auth-field-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
  }
  .auth-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }
  .auth-input-icon {
    position: absolute;
    left: 12px;
    width: 17px;
    height: 17px;
    color: #94a3b8;
    pointer-events: none;
    transition: color 0.2s ease;
  }
  .auth-input-control {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 9px 14px 9px 38px;
    font-size: 0.86rem;
    color: #0f172a;
    transition: all 0.2s ease;
    box-sizing: border-box;
    font-family: inherit;
  }
  .auth-input-control:focus {
    background: #ffffff;
    border-color: #0e7482;
    box-shadow: 0 0 0 3px rgba(14, 116, 130, 0.12);
    outline: none;
  }
  .auth-input-wrapper:focus-within .auth-input-icon {
    color: #0e7482;
  }

  /* Two Column Grid */
  .auth-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  @media (max-width: 480px) {
    .auth-grid-2 {
      grid-template-columns: 1fr;
      gap: 10px;
    }
  }

  .auth-pwd-toggle {
    position: absolute;
    right: 10px;
    background: transparent;
    border: none;
    padding: 4px;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
  }
  .auth-pwd-toggle:hover {
    color: #0e7482;
  }

  /* Submit Button */
  .auth-btn-primary {
    position: relative;
    width: 100%;
    padding: 11px 18px;
    font-size: 0.92rem;
    font-weight: 800;
    color: #ffffff;
    background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%);
    border: none;
    border-radius: 13px;
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
    gap: 10px;
    width: 100%;
    padding: 10px 16px;
    font-size: 0.86rem;
    font-weight: 700;
    color: #1e293b;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 13px;
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

  .auth-role-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
    background: rgba(14, 116, 130, 0.1);
    color: #0a5661;
    margin-top: 4px;
    line-height: 1.4;
    text-align: left;
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
          Anda sudah masuk sebagai: <strong>{{ Auth::user()->name }}</strong>
          ({{ Auth::user()->isOwner() ? 'Admin' : 'Pelanggan' }})
        </div>
        <div style="font-size: 0.74rem; color: #0e7482;">Untuk daftar akun baru, logout terlebih dahulu atau isi form di bawah dengan data akun baru.</div>
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

    {{-- Brand Logo & Header (Compact) --}}
    <div style="text-align: center; margin-bottom: 14px;">
      <a href="{{ route('home') }}" class="auth-logo-badge" title="Ke Beranda Kos Mas Robi">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <line x1="19" y1="8" x2="19" y2="14"/>
          <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
      </a>
      <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0 0 2px; letter-spacing: -0.02em;">Daftar Akun Baru</h2>
      <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Buat akun pelanggan Kos Mas Robi</p>
    </div>

    {{-- Validation Error Alerts --}}
    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 12px; border-radius: 12px; font-size: 0.78rem; margin-bottom: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    {{-- Register Form --}}
    <form action="{{ route('register.post') }}" method="POST" id="formRegister" enctype="multipart/form-data">
      @csrf

      {{-- Role always tenant for public registration --}}
      <input type="hidden" name="role" id="regRole" value="tenant">

      {{-- Foto Profil Pelanggan (COMPACT & KOSONG SEBELUM USER UPLOAD) --}}
      <div class="auth-field-group" style="margin-bottom: 11px;">
        <div style="background: #f8fafc; border: 1.5px dashed #0a5661; border-radius: 14px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 10px; cursor: pointer; transition: all 0.2s;"
             onclick="document.getElementById('regAvatar').click()"
             id="avatarUploadBox"
             title="Klik untuk memilih foto profil Anda">
          <div style="display: flex; align-items: center; gap: 10px;">
            {{-- Lingkaran Avatar: Bersih/Kosong Tanpa Foto Dummy Sebelum User Upload --}}
            <div style="position: relative; width: 44px; height: 44px; border-radius: 50%; background: #f0fdfa; border: 2px solid #0a5661; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
              {{-- Placeholder Icon User saat belum upload foto --}}
              <div id="avatarPlaceholderSvg" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; color: #0a5661;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              {{-- Foto asli yang diupload user (sembunyi sebelum dipilih) --}}
              <img id="avatarPreviewImg" src="" alt="Foto Profil" style="display: none; width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div>
              <div style="font-size: 0.78rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 5px;">
                <span>Foto Profil Diri (Wajib) *</span>
                <span id="avatarSelectedTag" style="display: none; font-size: 0.65rem; font-weight: 700; color: #047857; background: #dcfce7; padding: 1px 5px; border-radius: 4px;">Terpilih</span>
              </div>
              <div id="avatarFileName" style="font-size: 0.70rem; color: #64748b; margin-top: 1px;">
                Klik untuk upload foto diri (Maks. 5MB)
              </div>
            </div>
          </div>

          <button type="button" style="background: #0a5661; color: #ffffff; border: none; font-size: 0.72rem; font-weight: 700; padding: 5px 12px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px; pointer-events: none;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Pilih Foto</span>
          </button>
        </div>

        <input type="file"
               id="regAvatar"
               name="avatar"
               accept="image/jpeg,image/png,image/jpg,image/webp"
               required
               style="display: none;"
               onchange="previewRegisterAvatar(this)">
      </div>

      {{-- Nama Lengkap --}}
      <div class="auth-field-group">
        <label for="regName" class="auth-field-label">Nama Lengkap *</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <input type="text" 
                 id="regName" 
                 name="name" 
                 class="auth-input-control" 
                 placeholder="Nama lengkap Anda" 
                 value="{{ old('name') }}" 
                 required 
                 autofocus>
        </div>
      </div>

      {{-- Email & WhatsApp Grid --}}
      <div class="auth-grid-2">
        <div class="auth-field-group">
          <label for="regEmail" class="auth-field-label">Alamat Email *</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" 
                   id="regEmail" 
                   name="email" 
                   class="auth-input-control" 
                   placeholder="email@gmail.com" 
                   value="{{ old('email') }}" 
                   required>
          </div>
        </div>

        <div class="auth-field-group">
          <label for="regPhone" class="auth-field-label">No. WhatsApp *</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <input type="tel" 
                   id="regPhone" 
                   name="phone" 
                   class="auth-input-control" 
                   placeholder="0812xxxxxxxx" 
                   value="{{ old('phone') }}" 
                   required>
          </div>
        </div>
      </div>

      {{-- =========================================================================
           PILIHAN CABANG KOS & TIPE KAMAR (SUPER COMPACT & SLEEK)
           ========================================================================= --}}
      <div class="auth-field-group" id="regRoomGroup" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 9px 12px; margin-bottom: 11px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px;">
          <label class="auth-field-label" style="font-size: 0.78rem; font-weight: 800; color: #0f172a; margin-bottom: 0;">
            Pilih Cabang Kos *
          </label>
          <span style="font-size: 0.68rem; color: #0a5661; font-weight: 700;">2 Cabang Tersedia</span>
        </div>

        <!-- 2 Compact Branch Buttons -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
          <label id="cardBranch1" style="display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; border: 1.5px solid {{ old('branch_preference', 'Lokasi 1') === 'Lokasi 1' ? '#0a5661' : '#e2e8f0' }}; border-radius: 9px; padding: 7px 8px; background: {{ old('branch_preference', 'Lokasi 1') === 'Lokasi 1' ? '#f0fdfa' : '#ffffff' }}; transition: all 0.2s;">
            <input type="radio" name="branch_preference" value="Lokasi 1" {{ old('branch_preference', 'Lokasi 1') === 'Lokasi 1' ? 'checked' : '' }} style="display: none;" onchange="handleBranchRegisterChange('Lokasi 1')">
            <span style="font-size: 0.76rem; font-weight: 800; color: #0a5661;">Cabang 1 (Kampus)</span>
          </label>

          <label id="cardBranch2" style="display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; border: 1.5px solid {{ old('branch_preference') === 'Lokasi 2' ? '#0284c7' : '#e2e8f0' }}; border-radius: 9px; padding: 7px 8px; background: {{ old('branch_preference') === 'Lokasi 2' ? '#f0f9ff' : '#ffffff' }}; transition: all 0.2s;">
            <input type="radio" name="branch_preference" value="Lokasi 2" {{ old('branch_preference') === 'Lokasi 2' ? 'checked' : '' }} style="display: none;" onchange="handleBranchRegisterChange('Lokasi 2')">
            <span style="font-size: 0.76rem; font-weight: 800; color: #0284c7;">Cabang 2 (Ringroad)</span>
          </label>
        </div>

        <!-- Dynamic Tipe Kamar & Unit Kamar -->
        <div class="auth-grid-2" style="margin-bottom: 0; gap: 8px;">
          <div style="margin-bottom: 0;">
            <label for="regRoomType" style="display: block; font-size: 0.70rem; font-weight: 700; color: #475569; margin-bottom: 2px;">Tipe Kamar</label>
            <select id="regRoomType" name="room_type_preference" class="auth-input-control" style="padding: 6px 10px; font-size: 0.78rem; font-weight: 600; border-radius: 8px; height: 34px; cursor: pointer;" onchange="handleTypeFilterRegister(this.value)">
              <!-- Populated dynamically -->
            </select>
          </div>

          <div style="margin-bottom: 0;">
            <label for="regRoom" style="display: block; font-size: 0.70rem; font-weight: 700; color: #475569; margin-bottom: 2px;">Pilih Unit Kamar</label>
            <select id="regRoom" name="room_id" class="auth-input-control" style="padding: 6px 10px; font-size: 0.78rem; font-weight: 600; border-radius: 8px; height: 34px; cursor: pointer;">
              <!-- Populated dynamically -->
            </select>
          </div>
        </div>
      </div>

      {{-- Kata Sandi & Konfirmasi Grid --}}
      <div class="auth-grid-2">
        <div class="auth-field-group">
          <label for="regPassword" class="auth-field-label">Kata Sandi *</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" 
                   id="regPassword" 
                   name="password" 
                   class="auth-input-control" 
                   placeholder="Min. 6 karakter" 
                   required 
                   style="padding-right: 42px;">
            <button type="button" class="auth-pwd-toggle" onclick="toggleRegPassword('regPassword', this)" title="Lihat Sandi">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="auth-field-group">
          <label for="regPasswordConf" class="auth-field-label">Ulangi Sandi *</label>
          <div class="auth-input-wrapper">
            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" 
                   id="regPasswordConf" 
                   name="password_confirmation" 
                   class="auth-input-control" 
                   placeholder="Ketik ulang sandi" 
                   required 
                   style="padding-right: 42px;">
            <button type="button" class="auth-pwd-toggle" onclick="toggleRegPassword('regPasswordConf', this)" title="Lihat Sandi">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>

      {{-- Submit Button --}}
      <button type="submit" class="auth-btn-primary" id="btnSubmitReg">
        <span>Daftar Akun Sekarang</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>

      {{-- Divider --}}
      <div style="display: flex; align-items: center; margin: 12px 0 10px; color: #94a3b8; font-size: 0.72rem;">
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        <span style="padding: 0 10px; font-weight: 700; letter-spacing: 0.05em;">ATAU</span>
        <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
      </div>

      {{-- Google Register Button --}}
      <a href="{{ route('auth.google') }}" class="auth-btn-google">
        <svg width="18" height="18" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
          <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
          <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
        </svg>
        <span>Daftar dengan Akun Google</span>
      </a>
    </form>

    {{-- Switch to Login --}}
    <div style="text-align: center; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 0.82rem; color: #64748b;">
      Sudah memiliki akun? 
      <a href="{{ route('login') }}" style="color: #0a5661; font-weight: 700; text-decoration: none; margin-left: 4px; border-bottom: 1.5px solid rgba(10, 86, 97, 0.3); transition: all 0.2s;">
        Masuk di sini &rarr;
      </a>
    </div>

    {{-- Return to Landing Page --}}
    <div style="text-align: center; margin-top: 10px;">
      <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #94a3b8; text-decoration: none; font-weight: 600; transition: color 0.2s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        <span>Kembali ke Halaman Utama</span>
      </a>
    </div>

  </div>
</div>

<script>
  function previewRegisterAvatar(input) {
    if (input.files && input.files[0]) {
      var file = input.files[0];
      var reader = new FileReader();
      reader.onload = function(e) {
        var img = document.getElementById('avatarPreviewImg');
        var placeholder = document.getElementById('avatarPlaceholderSvg');
        var tag = document.getElementById('avatarSelectedTag');
        var box = document.getElementById('avatarUploadBox');

        if (img) {
          img.src = e.target.result;
          img.style.display = 'block';
        }
        if (placeholder) {
          placeholder.style.display = 'none';
        }
        if (tag) {
          tag.style.display = 'inline-flex';
        }
        if (box) {
          box.style.borderColor = '#10b981';
          box.style.background = '#f0fdf4';
        }

        var info = document.getElementById('avatarFileName');
        if (info) {
          info.innerHTML = '<strong style="color: #047857;">' + file.name + '</strong> (' + (file.size / 1024).toFixed(0) + ' KB)';
        }
      };
      reader.readAsDataURL(file);
    }
  }

  function toggleRegPassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      btn.style.color = '#0e7482';
    } else {
      input.type = 'password';
      btn.style.color = '#94a3b8';
    }
  }

  // =========================================================================
  // DYNAMIC BRANCH & ROOM TYPE SELECTION
  // =========================================================================
  var availableRoomsData = @json($availableRooms);
  var currentSelectedBranch = "{{ old('branch_preference', 'Lokasi 1') }}";
  var oldRoomId = "{{ old('room_id', '') }}";
  var oldRoomType = "{{ old('room_type_preference', '') }}";

  function handleBranchRegisterChange(branch) {
    currentSelectedBranch = branch;
    var c1 = document.getElementById('cardBranch1');
    var c2 = document.getElementById('cardBranch2');

    if (branch === 'Lokasi 2') {
      if (c1) { 
        c1.style.borderColor = '#e2e8f0'; 
        c1.style.background = '#ffffff'; 
        var t1 = c1.querySelector('span:last-child');
        if (t1) t1.style.color = '#64748b';
      }
      if (c2) { 
        c2.style.borderColor = '#0284c7'; 
        c2.style.background = '#f0f9ff'; 
        var t2 = c2.querySelector('span:last-child');
        if (t2) t2.style.color = '#0284c7';
      }
    } else {
      if (c1) { 
        c1.style.borderColor = '#0a5661'; 
        c1.style.background = '#f0fdfa'; 
        var t1 = c1.querySelector('span:last-child');
        if (t1) t1.style.color = '#0a5661';
      }
      if (c2) { 
        c2.style.borderColor = '#e2e8f0'; 
        c2.style.background = '#ffffff'; 
        var t2 = c2.querySelector('span:last-child');
        if (t2) t2.style.color = '#64748b';
      }
    }

    populateRoomTypesAndRooms(branch);
  }

  function populateRoomTypesAndRooms(branch, filterType) {
    var typeSelect = document.getElementById('regRoomType');
    if (!typeSelect) return;

    var branchRooms = availableRoomsData.filter(function(r) {
      return (r.location || 'Lokasi 1') === branch;
    });

    var types = [];
    branchRooms.forEach(function(r) {
      if (r.type && types.indexOf(r.type) === -1) {
        types.push(r.type);
      }
    });

    typeSelect.innerHTML = '<option value="">-- Semua Tipe Kamar --</option>';
    types.forEach(function(t) {
      var opt = document.createElement('option');
      opt.value = t;
      opt.textContent = t;
      if (oldRoomType === t || filterType === t) opt.selected = true;
      typeSelect.appendChild(opt);
    });

    renderFilteredRooms(branch, filterType || typeSelect.value);
  }

  function handleTypeFilterRegister(type) {
    renderFilteredRooms(currentSelectedBranch, type);
  }

  function renderFilteredRooms(branch, typeFilter) {
    var roomSelect = document.getElementById('regRoom');
    if (!roomSelect) return;

    var branchRooms = availableRoomsData.filter(function(r) {
      var matchBranch = (r.location || 'Lokasi 1') === branch;
      var matchType = !typeFilter || r.type === typeFilter;
      return matchBranch && matchType;
    });

    roomSelect.innerHTML = '<option value="">-- Pilih Nanti / Belum Memilih --</option>';
    if (branchRooms.length === 0) {
      var opt = document.createElement('option');
      opt.value = "";
      opt.disabled = true;
      opt.textContent = "Kamar penuh pada tipe ini";
      roomSelect.appendChild(opt);
    } else {
      branchRooms.forEach(function(r) {
        var opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = 'Kamar #' + r.number + ' - ' + r.type + ' (Lt ' + r.floor + ') - Rp ' + Number(r.price).toLocaleString('id-ID') + '/bln';
        if (String(oldRoomId) === String(r.id)) opt.selected = true;
        roomSelect.appendChild(opt);
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    handleBranchRegisterChange(currentSelectedBranch);
  });
</script>
@endsection
