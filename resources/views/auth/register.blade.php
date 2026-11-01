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
    padding: 36px 16px;
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
    opacity: 0.65;
    animation-timing-function: ease-in-out;
    animation-iteration-count: infinite;
    animation-direction: alternate;
  }
  .orb-teal-1 {
    width: 500px;
    height: 500px;
    top: -140px;
    right: -100px;
    background: radial-gradient(circle, rgba(14, 116, 130, 0.28) 0%, rgba(10, 86, 97, 0.08) 70%, transparent 100%);
    animation: orbFloat1 15s infinite;
  }
  .orb-teal-2 {
    width: 440px;
    height: 440px;
    bottom: -120px;
    left: -80px;
    background: radial-gradient(circle, rgba(10, 86, 97, 0.22) 0%, rgba(20, 184, 166, 0.12) 70%, transparent 100%);
    animation: orbFloat2 17s infinite;
  }
  .orb-coral {
    width: 340px;
    height: 340px;
    top: 30%;
    left: 10%;
    background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, transparent 70%);
    animation: orbFloat3 13s infinite;
  }

  @keyframes orbFloat1 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-60px, 40px) scale(1.08); }
    100% { transform: translate(30px, 70px) scale(0.95); }
  }
  @keyframes orbFloat2 {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(50px, -40px) scale(1.1); }
    100% { transform: translate(-40px, -60px) scale(0.92); }
  }
  @keyframes orbFloat3 {
    0% { transform: translate(0, 0) scale(0.9); opacity: 0.4; }
    50% { transform: translate(30px, 30px) scale(1.15); opacity: 0.7; }
    100% { transform: translate(-20px, -20px) scale(1); opacity: 0.5; }
  }

  /* Frosted Glass Card */
  .auth-card {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 520px;
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

  /* Logo Badge */
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
    transform: translateY(-3px) rotate(-4deg) scale(1.05);
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

  /* Modern Animated Input Groups */
  .auth-field-group {
    position: relative;
    margin-bottom: 16px;
  }
  .auth-field-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
  }
  .auth-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
  }
  .auth-input-icon {
    position: absolute;
    left: 14px;
    width: 19px;
    height: 19px;
    color: #94a3b8;
    pointer-events: none;
    transition: color 0.25s ease;
  }
  .auth-input-control {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 16px 12px 44px;
    font-size: 0.90rem;
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
  .auth-input-wrapper:focus-within .auth-input-icon {
    color: #0e7482;
  }

  /* Two Column Grid */
  .auth-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  @media (max-width: 500px) {
    .auth-grid-2 {
      grid-template-columns: 1fr;
      gap: 16px;
    }
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
    transition: color 0.2s;
  }
  .auth-pwd-toggle:hover {
    color: #0e7482;
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
</style>

<div class="auth-viewport">
  {{-- Atmospheric Gradient Blobs --}}
  <div class="auth-ambient-orb orb-teal-1"></div>
  <div class="auth-ambient-orb orb-teal-2"></div>
  <div class="auth-ambient-orb orb-coral"></div>

  {{-- Main Glass Card --}}
  <div class="auth-card">

    {{-- Brand Logo & Header --}}
    <div style="text-align: center; margin-bottom: 22px;">
      <a href="{{ route('home') }}" class="auth-logo-badge" title="Ke Beranda Kos Mas Robi">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <line x1="19" y1="8" x2="19" y2="14"/>
          <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
      </a>
      <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.02em;">Daftar Akun Baru</h2>
      <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Pilih peran pendaftaran Anda di Kos Mas Robi</p>

      <div id="registerRoleIndicator" class="auth-role-indicator">
        <span>✨ Pendaftaran Akun Pelanggan (Anak Kos) &rarr; Masuk ke Portal Pelanggan</span>
      </div>
    </div>

    {{-- Role Switcher Tab --}}
    <div class="auth-role-tabs">
      <button type="button" class="auth-role-tab-btn active" id="tabRegPelanggan" onclick="switchRegisterRole('tenant')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Pelanggan (Anak Kos)</span>
      </button>
      <button type="button" class="auth-role-tab-btn" id="tabRegAdmin" onclick="switchRegisterRole('owner')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Admin (Pengelola)</span>
      </button>
    </div>

    {{-- Validation Error Alerts --}}
    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; margin-bottom: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.3s ease;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    {{-- Register Form --}}
    <form action="{{ route('register.post') }}" method="POST" id="formRegister">
      @csrf

      {{-- Hidden Role Input (tenant / owner) --}}
      <input type="hidden" name="role" id="regRole" value="{{ old('role', 'tenant') }}">

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

      {{-- Pilih Kamar (Hanya jika role == tenant) --}}
      <div class="auth-field-group" id="regRoomGroup">
        <label for="regRoom" class="auth-field-label">Pilih Kamar yang Dihuni (Opsional)</label>
        <div class="auth-input-wrapper">
          <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <select id="regRoom" name="room_id" class="auth-input-control" style="cursor: pointer;">
            <option value="">-- Belum Memilih Kamar (Bisa Pilih Nanti) --</option>
            @foreach($availableRooms as $ar)
              <option value="{{ $ar->id }}" {{ old('room_id') == $ar->id ? 'selected' : '' }}>
                Kamar #{{ $ar->number }} - {{ $ar->type }} (Lantai {{ $ar->floor }})
              </option>
            @endforeach
          </select>
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
        <span id="btnSubmitRegText">Daftar & Masuk ke Portal Pelanggan</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>
    </form>

    {{-- Switch to Login --}}
    <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; font-size: 0.86rem; color: #64748b;">
      Sudah memiliki akun? 
      <a href="{{ route('login') }}" style="color: #0a5661; font-weight: 700; text-decoration: none; margin-left: 4px; border-bottom: 1.5px solid rgba(10, 86, 97, 0.3); transition: all 0.2s;">
        Masuk di sini &rarr;
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
  function switchRegisterRole(role) {
    const tabPelanggan = document.getElementById('tabRegPelanggan');
    const tabAdmin = document.getElementById('tabRegAdmin');
    const roleInput = document.getElementById('regRole');
    const indicator = document.getElementById('registerRoleIndicator');
    const roomGroup = document.getElementById('regRoomGroup');
    const btnSubmitRegText = document.getElementById('btnSubmitRegText');

    roleInput.value = role;

    if (role === 'owner') {
      tabAdmin.classList.add('active');
      tabPelanggan.classList.remove('active');
      indicator.innerHTML = '⚙️ Pendaftaran <strong>Admin / Pengelola Kos</strong> &rarr; Langsung masuk ke <strong>Dashboard Admin</strong>';
      indicator.style.background = 'rgba(10, 86, 97, 0.12)';
      indicator.style.color = '#0a5661';
      roomGroup.style.display = 'none';
      btnSubmitRegText.textContent = 'Daftar & Masuk ke Dashboard Admin';
    } else {
      tabPelanggan.classList.add('active');
      tabAdmin.classList.remove('active');
      indicator.innerHTML = '📱 Pendaftaran <strong>Pelanggan (Anak Kos)</strong> &rarr; Langsung masuk ke <strong>Portal Pelanggan</strong>';
      indicator.style.background = 'rgba(14, 116, 130, 0.1)';
      indicator.style.color = '#0e7482';
      roomGroup.style.display = 'block';
      btnSubmitRegText.textContent = 'Daftar & Masuk ke Portal Pelanggan';
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

  // Set initial state from old input if validation failed
  document.addEventListener('DOMContentLoaded', () => {
    const initialRole = document.getElementById('regRole').value;
    if (initialRole === 'owner') {
      switchRegisterRole('owner');
    }
  });
</script>
@endsection
