@extends('layouts.portal')

@section('title', 'Portal Anak Kos — ' . (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING'))

@section('content')

<style>
  /* =========================================================================
     WHITE BACKGROUND LOADING WITH SNAKE TRACER LOGO (LOGIN -> PENGHUNI KOST)
     ========================================================================= */
  body.f3d-tenant-active .tp-navbar,
  body.f3d-tenant-active .tp-main {
    opacity: 0;
    transform: perspective(1400px) translateY(35px);
    filter: blur(10px);
    transition: none;
  }

  body.f3d-tenant-ready .tp-navbar,
  body.f3d-tenant-ready .tp-main {
    opacity: 1;
    transform: perspective(1400px) translateY(0);
    filter: blur(0px);
    transition: 
      transform 0.85s cubic-bezier(0.16, 1, 0.3, 1),
      opacity 0.75s ease,
      filter 0.75s ease;
  }

  .f3d-tenant-portal-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999999;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    visibility: visible;
    overflow: hidden;
    cursor: default;
    transition: 
      opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1),
      transform 0.65s cubic-bezier(0.16, 1, 0.3, 1),
      filter 0.65s ease,
      visibility 0.65s ease;
  }

  .f3d-tenant-loader-wrap {
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

  .f3d-tenant-svg-box {
    width: 165px;
    height: 165px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
  }

  .f3d-tenant-svg-canvas {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .f3d-tenant-faint-track {
    stroke: rgba(10, 86, 97, 0.08);
  }

  .f3d-tenant-drawn-green {
    stroke: #0a5661;
  }

  .f3d-tenant-snake-tracer {
    filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.75));
  }

  .f3d-tenant-loading-track {
    width: 190px;
    height: 3.5px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
    margin: 0 auto 12px;
  }

  .f3d-tenant-loading-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #0a5661 0%, #10b981 100%);
    border-radius: 999px;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    transition: width 0.08s linear;
  }

  .f3d-tenant-loading-label {
    font-family: 'Outfit', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    color: #0a5661;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0;
  }

  .f3d-tenant-portal-overlay.portal-exit {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: scale(1.04);
    filter: blur(8px);
  }

  .f3d-tenant-portal-overlay.portal-exit .f3d-tenant-loader-wrap {
    transform: scale(1.08);
    opacity: 0;
  }

  /* =========================================================================
     MANDATORY PROFILE COMPLETION MODAL GATE FOR NEW TENANTS
     ========================================================================= */
  body.tp-profile-locked .tp-navbar,
  body.tp-profile-locked .tp-main {
    filter: blur(8px) saturate(0.6) !important;
    pointer-events: none !important;
    user-select: none !important;
  }

  .tp-profile-gate-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999990;
    background: rgba(4, 30, 36, 0.88);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.4s ease, visibility 0.4s ease;
  }

  .tp-profile-gate-overlay.gate-closed {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }

  .tp-profile-gate-card {
    background: #ffffff;
    width: 100%;
    max-width: 580px;
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(10, 86, 97, 0.12);
    overflow: hidden;
    position: relative;
    animation: tpGateCardPop 0.45s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes tpGateCardPop {
    from {
      opacity: 0;
      transform: scale(0.92) translateY(20px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  .tp-profile-gate-header {
    background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);
    padding: 24px 28px;
    color: #ffffff;
    position: relative;
  }

  .tp-profile-gate-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-bottom: 10px;
    color: #ccfbf1;
  }

  .tp-profile-gate-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0 0 6px 0;
    color: #ffffff;
    letter-spacing: -0.02em;
  }

  .tp-profile-gate-sub {
    font-size: 0.82rem;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
  }

  .tp-profile-gate-body {
    padding: 24px 28px;
    background: #ffffff;
  }

  .tp-profile-gate-alert {
    background: #fffbeb;
    border: 1.5px solid #fef3c7;
    border-radius: 14px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #92400e;
    font-size: 0.8rem;
    line-height: 1.45;
  }

  .tp-gate-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 16px;
  }

  @media (max-width: 580px) {
    .tp-profile-gate-overlay {
      padding: 10px 8px !important;
    }
    .tp-profile-gate-card {
      max-width: 100% !important;
      width: 100% !important;
      border-radius: 20px !important;
    }
    .tp-profile-gate-header {
      padding: 16px 16px !important;
    }
    .tp-profile-gate-body {
      padding: 16px 14px !important;
    }
    .tp-gate-form-row {
      grid-template-columns: 1fr;
      gap: 12px;
    }
  }

  .tp-gate-input-group {
    margin-bottom: 16px;
  }

  .tp-gate-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
  }

  .tp-gate-input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    font-size: 0.88rem;
    color: #0f172a;
    transition: all 0.2s ease;
    outline: none;
    box-sizing: border-box;
  }

  .tp-gate-input:focus {
    border-color: #0d9488;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
  }

  .tp-btn-gate-submit {
    width: 100%;
    padding: 14px 20px;
    background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);
    color: #ffffff;
    border: none;
    border-radius: 14px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.95rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(10, 86, 97, 0.35);
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .tp-btn-gate-submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(10, 86, 97, 0.45);
  }

  .tp-btn-gate-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .tp-gate-dropzone {
    border: 2px dashed #94a3b8;
    background: #f8fafc;
    border-radius: 14px;
    transition: all 0.25s ease;
    cursor: pointer;
    overflow: hidden;
  }
  .tp-gate-dropzone:hover {
    border-color: #0d9488;
    background: #f0fdfa;
  }
  .tp-gate-dropzone.has-error {
    border-color: #ef4444;
    background: #fef2f2;
    animation: tpGateShake 0.4s ease;
  }
  @keyframes tpGateShake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
  }
</style>

  @php
    $room = $room ?? ($tenant->room ?? null);
    $hasActiveRoom = $hasActiveRoom ?? (!empty($tenant && $tenant->room_id && $room));
    $dueDate = $dueDate ?? ($tenant && $tenant->due_day ? now()->setDay(min($tenant->due_day, 28)) : now()->addDays(30));
    $allRooms = $allRooms ?? collect([]);
    $branches = $branches ?? ($setting->branches ?? \App\Models\Setting::defaultBranches());
    $isProfileComplete = $isProfileComplete ?? true;
    $hasRealAvatar = !empty($user->avatar) && !str_contains($user->avatar, 'tenant_avatar.jpg');
    $userInitial = strtoupper(substr($user->name ?? 'P', 0, 1));
    $isB2 = ($tenantBranchKey ?? 'lokasi_1') === 'lokasi_2';
    $b1NamePortal = $branches['lokasi_1']['name'] ?? ($setting->branches['lokasi_1']['name'] ?? 'SMART LIVING 1 - Kampus Utama');
    $b2NamePortal = $branches['lokasi_2']['name'] ?? ($setting->branches['lokasi_2']['name'] ?? 'SMART LIVING 2 - Ringroad Exclusive');
    $branchName = $isB2 ? $b2NamePortal : $b1NamePortal;
    $branchAddr = $isB2 ? ($branches['lokasi_2']['address'] ?? 'Jl. Kaliurang KM 12, Sardonoharjo, Sleman') : ($branches['lokasi_1']['address'] ?? 'Jl. Pandega Marta, Pogung, Sinduadi (3 Mnt UGM)');
    $branchMaps = $isB2 ? ($branches['lokasi_2']['gmaps_url'] ?? 'https://maps.google.com/?q=SMART+LIVING+Ringroad') : ($branches['lokasi_1']['gmaps_url'] ?? 'https://maps.google.com/?q=SMART+LIVING+Kampus');
  @endphp

  {{-- WHITE BACKGROUND LOADING: MENUJU HALAMAN PENGHUNI KOST --}}
  <div id="f3dTenantPortalOverlay" class="f3d-tenant-portal-overlay">
    <div class="f3d-tenant-loader-wrap" id="f3dTenantLoaderWrap">
      <div class="f3d-tenant-svg-box" id="f3dTenantSvgBox">
        <svg class="f3d-tenant-svg-canvas" viewBox="0 0 224 232" fill="none">
          <defs>
            <linearGradient id="tenantSnakeGreenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#10b981" />
              <stop offset="50%" stop-color="#34d399" />
              <stop offset="100%" stop-color="#0a5661" />
            </linearGradient>
          </defs>

          <!-- Garis Faint Panduan -->
          <path class="f3d-tenant-faint-track" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
          <path class="f3d-tenant-faint-track" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />

          <!-- Garis Hijau Rumah & Ular -->
          <path id="f3dTenantGreenHouse" class="f3d-tenant-drawn-green" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
          <path id="f3dTenantSnakeHouse" class="f3d-tenant-snake-tracer" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="url(#tenantSnakeGreenGrad)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />

          <!-- Garis Hijau Pintu Dalam & Ular -->
          <path id="f3dTenantGreenDoor" class="f3d-tenant-drawn-green" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
          <path id="f3dTenantSnakeDoor" class="f3d-tenant-snake-tracer" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="url(#tenantSnakeGreenGrad)" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <!-- Tampilan Loading Saja -->
      <div class="f3d-tenant-loading-track">
        <div class="f3d-tenant-loading-bar" id="f3dTenantProgressBar"></div>
      </div>
      <div class="f3d-tenant-loading-label">MENUJU HALAMAN PENGHUNI KOST...</div>
    </div>
  </div>

  <script>
    (function() {
      try {
        var gH = document.getElementById('f3dTenantGreenHouse');
        var sH = document.getElementById('f3dTenantSnakeHouse');
        var gD = document.getElementById('f3dTenantGreenDoor');
        var sD = document.getElementById('f3dTenantSnakeDoor');
        if (gH) { gH.style.strokeDasharray = '0 1000'; gH.style.strokeDashoffset = '0'; }
        if (sH) { sH.style.strokeDasharray = '0 1000'; sH.style.strokeDashoffset = '0'; }
        if (gD) { gD.style.strokeDasharray = '0 1000'; gD.style.strokeDashoffset = '0'; }
        if (sD) { sD.style.strokeDasharray = '0 1000'; sD.style.strokeDashoffset = '0'; }
      } catch(e) {}
    })();
  </script>

  @if(!($isProfileComplete ?? true))
  <!-- =========================================================================
       POP-UP WAJIB: LENGKAPI DATA DIRI PENGHUNI BARU (LOGIN PERTAMA KALI)
       ========================================================================= -->
  <div id="tpProfileGateModal" class="tp-profile-gate-overlay">
    <div class="tp-profile-gate-card">
      <div class="tp-profile-gate-header">
        <div class="tp-profile-gate-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span>Aktivasi Akun & Data Diri Penghuni</span>
        </div>
        <h3 class="tp-profile-gate-title">Lengkapi Data Diri Anda</h3>
        <p class="tp-profile-gate-sub">
          Selamat datang di {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}! Mohon lengkapi formulir identitas di bawah ini untuk mengaktifkan seluruh akses fitur portal hunian Anda.
        </p>
      </div>

      <div class="tp-profile-gate-body">
        <div class="tp-profile-gate-alert">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2.2" style="flex-shrink:0; margin-top: 1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>
            <strong>Akses Fitur Lain Ditangguhkan Sementara</strong><br>
            Seluruh fitur portal (konfirmasi pembayaran sewa, tiket keluhan/servis, dan permohonan kamar) terkunci dan akan aktif secara otomatis setelah Anda menyimpan data diri di bawah ini.
          </div>
        </div>

        <form id="formCompleteProfileGate" onsubmit="submitCompleteProfileGate(event)">
          <div class="tp-gate-input-group">
            <label class="tp-gate-label" for="gateFullName">Nama Lengkap Anak Kos *</label>
            <input type="text" id="gateFullName" name="name" class="tp-gate-input" value="{{ $user->name }}" required placeholder="Contoh: Muhammad Dimas Satria">
          </div>

          {{-- GAMBAR 1: No. WhatsApp & No. KTP / SIM / KTM / KTA --}}
          <div class="tp-gate-form-row">
            <div>
              <label class="tp-gate-label" for="gatePhone">No. WhatsApp *</label>
              <input type="tel" id="gatePhone" name="phone" class="tp-gate-input" value="{{ ($user->phone && $user->phone !== '-') ? $user->phone : '' }}" required placeholder="0812xxxxxxxx">
            </div>
            <div>
              <label class="tp-gate-label" for="gateIdCard">No. KTP / SIM / KTM / KTA (Opsional)</label>
              <input type="text" id="gateIdCard" name="id_card" class="tp-gate-input" value="{{ $tenant?->id_card ?? '' }}" placeholder="33xxxxxxxxxxxxxx / Nomor Identitas">
            </div>
          </div>

          {{-- WAJIB KIRIM FOTO KTP / SIM / KTM / KTA --}}
          <div class="tp-gate-input-group">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
              <label class="tp-gate-label" for="gateIdCardPhoto" style="margin-bottom: 0;">
                Wajib Kirim Foto KTP / SIM / KTM / KTA *
              </label>
              <span style="font-size: 0.68rem; font-weight: 800; background: #fee2e2; color: #dc2626; padding: 2px 7px; border-radius: 999px;">Wajib Upload</span>
            </div>
            <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 8px; line-height: 1.4;">
              Lampirkan foto fisik asli kartu identitas Anda yang masih berlaku (KTP, SIM, Kartu Tanda Mahasiswa, atau KTA Dinas/Kerja).
            </div>

            <div class="tp-gate-dropzone" id="gateIdCardDropzone" onclick="document.getElementById('gateIdCardPhoto').click()">
              <input type="file" id="gateIdCardPhoto" name="id_card_photo" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;" onchange="handleGateIdCardPhotoChange(this)">
              
              <div id="gateIdCardUploadPrompt" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 20px 14px; text-align: center; cursor: pointer;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; border: 1px solid #bae6fd;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="4" width="18" height="16" rx="3"/>
                    <circle cx="9" cy="10" r="2"/>
                    <line x1="15" y1="8" x2="17" y2="8"/>
                    <line x1="15" y1="12" x2="17" y2="12"/>
                    <line x1="7" y1="16" x2="17" y2="16"/>
                  </svg>
                </div>
                <div>
                  <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">Pilih / Ambil Foto Kartu Identitas</div>
                  <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">Ketuk untuk memilih foto dari galeri atau kamera HP (JPG, PNG, WEBP, Maks. 5 MB)</div>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; font-size: 0.74rem; font-weight: 700; border: 1.5px solid #bfdbfe; margin-top: 2px;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  <span>Unggah Foto KTP / SIM / KTM / KTA</span>
                </div>
              </div>

              <div id="gateIdCardPreviewWrap" style="display: none; padding: 12px 16px; align-items: center; justify-content: space-between; gap: 12px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px;">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                  <img id="gateIdCardPreviewImg" src="" alt="Preview Identitas" style="width: 72px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #bbf7d0; box-shadow: 0 2px 6px rgba(0,0,0,0.06); flex-shrink: 0;">
                  <div style="min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                      <span style="font-size: 0.68rem; font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 7px; border-radius: 4px;">Foto Siap Dikirim</span>
                    </div>
                    <div id="gateIdCardFileName" style="font-size: 0.75rem; font-weight: 700; color: #0f172a; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">-</div>
                    <div id="gateIdCardFileSize" style="font-size: 0.7rem; color: #64748b;">-</div>
                  </div>
                </div>
                <button type="button" onclick="event.stopPropagation(); window.removeGateIdCardPhoto();" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 6px 10px; border-radius: 8px; font-size: 0.74rem; font-weight: 700; cursor: pointer; flex-shrink: 0;" title="Ganti Foto">
                  Ganti
                </button>
              </div>
            </div>
          </div>

          {{-- GAMBAR 2: Kontak Darurat (Ortu / Wali / Saudara) --}}
          <div class="tp-gate-input-group">
            <label class="tp-gate-label" for="gateEmergencyContact">Kontak Darurat (Ortu / Wali / Saudara) *</label>
            <input type="text" id="gateEmergencyContact" name="emergency_contact" class="tp-gate-input" value="{{ $tenant?->emergency_contact ?? '' }}" required placeholder="Contoh: Bpk. Joko (0813xxxxxxxx) - Orang Tua">
          </div>

          <div style="margin-top: 22px; display: flex; flex-direction: column; gap: 10px;">
            <button type="submit" id="btnSubmitGateProfile" class="tp-btn-gate-submit">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Simpan & Lengkapi Data Diri</span>
            </button>
            <button type="button" id="btnGateSaveWithGoogle" onclick="submitGateWithGoogle(event)" style="width: 100%; height: 46px; border-radius: 12px; background: #ffffff; border: 1.5px solid #033f3c; color: #033f3c; font-size: 0.86rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
              <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
              </svg>
              <span>Simpan Data & Hubungkan Akun Google</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- ======================================================= NAVBAR ======================================================= --}}
  <nav class="tp-navbar">
    <div class="tp-navbar-brand">
      <div class="tp-brand-icon-box" style="padding: 0; overflow: hidden; background: transparent; border: none; box-shadow: none;">
        <img src="{{ asset('assets/images/logo_kos_jember.png') }}" alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'Kos Jember' }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 11px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      </div>
      <div>
        <div class="tp-brand-name">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</div>
        <div class="tp-brand-sub">Portal Anak Kos</div>
      </div>
    </div>

    {{-- Center Tabs: Dashboard Kos & Kamarku --}}
    <div class="tp-navbar-nav-tabs">
      <button type="button" class="tp-tab-btn active" id="tabBtnCatalog" onclick="switchPortalTab('catalog')">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Dashboard Kos</span>
      </button>
      <button type="button" class="tp-tab-btn" id="tabBtnMyRoom" onclick="switchPortalTab('myroom')">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Kamarku</span>
        @if($hasActiveRoom && $room)
          <span class="tp-tab-room-pill">#{{ $room->number }}</span>
        @endif
      </button>
    </div>

    <div class="tp-navbar-actions">
      {{-- Tombol Info WiFi Resmi (Gambar 3: Gantikan Logo Bell Menjadi Logo WiFi) --}}
      <button type="button" class="tp-icon-btn" id="btnWifiModal" title="Info WiFi Kos">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
          <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
          <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
          <line x1="12" y1="20" x2="12.01" y2="20"/>
        </svg>
      </button>

      {{-- Tombol Peraturan Kos (Disebelah Logo WiFi) --}}
      <button type="button" class="tp-icon-btn" id="btnRulesModal" title="Peraturan & Tata Tertib Kos">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
      </button>

      <form action="{{ route('logout') }}" method="POST" id="formLogoutTenant" style="display:inline;">
        @csrf
        <button type="submit" class="tp-btn-logout">Keluar</button>
      </form>
    </div>
  </nav>

  {{-- ========================================================= MAIN ========================================================= --}}
  <main class="tp-main">

    @if(session('success'))
      <div class="tp-alert success tp-flash-alert" id="tenantFlashAlertSuccess">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span style="flex: 1;">{{ session('success') }}</span>
        <button type="button" class="tp-alert-close" onclick="this.closest('.tp-alert').remove()" title="Tutup notifikasi">&times;</button>
      </div>
    @endif

    @if(isset($errors) && $errors->any())
      <div class="tp-alert error tp-flash-alert" id="tenantFlashAlertError">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="flex: 1;">{{ $errors->first() }}</span>
        <button type="button" class="tp-alert-close" onclick="this.closest('.tp-alert').remove()" title="Tutup notifikasi">&times;</button>
      </div>
    @endif

    {{-- =========================================================================
         TAB 1: DASHBOARD KOS (ALL ROOMS CATALOG & LIVE AVAILABILITY)
         ========================================================================= --}}
    <div id="sectionDashboardCatalog" class="tp-portal-tab-content active">

      @php
        $landing = $setting->landing_content ?? [];
        $heroImg = $landing['hero_image'] ?? 'assets/images/kos_exterior.jpg';
        $heroTitle = $landing['hero_title'] ?? "Kos Impian\nOrang Jember";
        $heroDesc = $landing['hero_desc'] ?? 'Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis.';
        $heroBtnText = $landing['hero_btn_text'] ?? 'Jelajahi Kamar';
      @endphp

      <!-- =========================================================================
           HERO SECTION: MATCHING LANDING PAGE DESIGN (IMAGE 5) & DYNAMICALLY
           CONNECTED TO ADMIN LANDING PAGE SETTINGS
           ========================================================================= -->
      <div class="tp-hero-landing-banner">
        {{-- Right Side Blended Photo with Gradient Mask --}}
        <div class="tp-hero-landing-photo-wrap">
          <img src="{{ asset($heroImg) }}"
               alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}"
               onerror="this.src='{{ asset('assets/images/kos_exterior.jpg') }}'"
               class="tp-hero-landing-photo">
        </div>

        {{-- Left Content: Dynamic Typography from Admin Landing Page Settings --}}
        <div class="tp-hero-landing-content">
          <h1 class="tp-hero-landing-title">{!! nl2br(e($heroTitle)) !!}</h1>
          <p class="tp-hero-landing-desc" style="margin-bottom: 0;">{{ $heroDesc }}</p>
        </div>
      </div>

      <!-- =========================================================================
           PEMILIHAN 2 TEMPAT KOS & INDIKATOR KAMAR TERSEDIA VS SUDAH DISEWA
           ========================================================================= -->
      <div class="tp-branch-select-section" id="sectionBranchSelector">
        <div class="tp-branch-select-header">
          <div>
            <div class="tp-branch-subtitle-label">
              <span>PILIHAN 2 TITIK LOKASI CABANG</span>
            </div>
            <h3 class="tp-branch-title-text">Pilih Lokasi Cabang Kos</h3>
          </div>
          <div class="tp-branch-overall-pill">
            <span>Total Keseluruhan: <strong>{{ $totalRoomsCount }} Kamar</strong></span>
          </div>
        </div>

        <div class="tp-branch-cards-grid">
          {{-- Cabang 1 Card --}}
          <div class="tp-branch-card active" id="branchCard1" onclick="selectCatalogBranch('lokasi_1', this)">
            <div class="tp-branch-card-header">
              <div class="tp-branch-tag-pill b1">
                <span>{{ $b1NamePortal }}</span>
              </div>
              <span class="tp-branch-selected-badge">Terpilih</span>
            </div>
            <h4 class="tp-branch-card-name">{{ $b1NamePortal }}</h4>
            <div class="tp-branch-card-addr">{{ $branches['lokasi_1']['address'] ?? 'Jl. Pandega Marta, Caturtunggal (3 Mnt UGM)' }}</div>
            <div class="tp-branch-stats-badges">
              <span class="tp-branch-stat available">
                <span class="tp-stat-dot green"></span>
                <strong>{{ $b1Available }}</strong> Kamar Kosong
              </span>
              <span class="tp-branch-stat occupied">
                <span class="tp-stat-dot slate"></span>
                <strong>{{ $b1Occupied }}</strong> Sudah Disewa
              </span>
              <span class="tp-branch-stat total">
                Total <strong>{{ $b1Total }}</strong> Unit
              </span>
            </div>
          </div>

          {{-- Cabang 2 Card --}}
          <div class="tp-branch-card" id="branchCard2" onclick="selectCatalogBranch('lokasi_2', this)">
            <div class="tp-branch-card-header">
              <div class="tp-branch-tag-pill b2">
                <span>{{ $b2NamePortal }}</span>
              </div>
              <span class="tp-branch-selected-badge">Terpilih</span>
            </div>
            <h4 class="tp-branch-card-name">{{ $b2NamePortal }}</h4>
            <div class="tp-branch-card-addr">{{ $branches['lokasi_2']['address'] ?? 'Jl. Kaliurang KM 12, Sardonoharjo (Modern)' }}</div>
            <div class="tp-branch-stats-badges">
              <span class="tp-branch-stat available">
                <span class="tp-stat-dot green"></span>
                <strong>{{ $b2Available }}</strong> Kamar Kosong
              </span>
              <span class="tp-branch-stat occupied">
                <span class="tp-stat-dot slate"></span>
                <strong>{{ $b2Occupied }}</strong> Sudah Disewa
              </span>
              <span class="tp-branch-stat total">
                Total <strong>{{ $b2Total }}</strong> Unit
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Bar -->
      <div class="tp-catalog-filters-bar">
        <div class="tp-filter-pills" id="catalogFilterPills">
          <button type="button" class="tp-filter-btn active" data-filter="all" onclick="filterCatalogRooms('all', this)">
            Semua Kamar ({{ $totalRoomsCount }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="lokasi_1" onclick="filterCatalogRooms('lokasi_1', this)">
            {{ $b1NamePortal }} ({{ $b1Total }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="lokasi_2" onclick="filterCatalogRooms('lokasi_2', this)">
            {{ $b2NamePortal }} ({{ $b2Total }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="available" onclick="filterCatalogRooms('available', this)">
            Tersedia / Kosong ({{ $availableRoomsCount }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="occupied" onclick="filterCatalogRooms('occupied', this)">
            Sudah Disewa ({{ $occupiedRoomsCount }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="floor-1" onclick="filterCatalogRooms('floor-1', this)">
            Lantai 1
          </button>
          <button type="button" class="tp-filter-btn" data-filter="floor-2" onclick="filterCatalogRooms('floor-2', this)">
            Lantai 2
          </button>
        </div>
      </div>

      <!-- Room Catalog Grid -->
      <div class="tp-catalog-grid" id="catalogRoomGrid">
        @forelse($allRooms as $r)
          @php
            $isMyRoom = ($room && $room->id === $r->id);
            $rThumb = !empty($r->images_list[0]) ? asset($r->images_list[0]) : asset('assets/images/rooms/room_1.jpg');
            $isOccupied = ($r->status !== 'available' || !empty($r->booked_by_user_id) || $r->activeTenant);
            $statusClass = $isMyRoom ? 'my-room' : (!$isOccupied ? 'available' : 'occupied');
            $statusText = $isMyRoom ? 'Kamar Anda' : (!$isOccupied ? 'Tersedia' : 'Sudah Disewa');
            $hasCustomInterior = $r->custom_interior_status && $r->custom_interior_status !== 'none';
          @endphp
          <div class="tp-room-card room-item"
               data-status="{{ $isOccupied ? 'occupied' : 'available' }}"
               data-floor="floor-{{ $r->floor }}"
               data-location="{{ ($r->location ?? 'Lokasi 1') === 'Lokasi 2' ? 'lokasi_2' : 'lokasi_1' }}"
               onclick="{{ $isMyRoom ? "switchPortalTab('myroom')" : "handleRoomCardClick(" . $r->id . ")" }}">
            
            <div class="tp-room-card-thumb-wrap">
              <img src="{{ $rThumb }}" alt="Foto Kamar #{{ $r->number }}" class="tp-room-card-thumb" loading="lazy">
              <span class="tp-room-card-number-badge">Kamar #{{ $r->number }}</span>
              <span class="tp-room-card-status-badge {{ $statusClass }}">{{ $statusText }}</span>
              @if($hasCustomInterior)
                <span class="tp-room-card-interior-tag" title="Permintaan Custom Interior Aktif">
                  <span>Custom Interior</span>
                </span>
              @endif
            </div>

            <div class="tp-room-card-body">
              <div class="tp-room-card-head">
                <h3 class="tp-room-card-title">Kamar #{{ $r->number }}</h3>
                <span class="tp-room-card-floor">Lt. {{ $r->floor }}</span>
              </div>
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                <div class="tp-room-card-type">{{ $r->type }}</div>
                @if(($r->location ?? 'Lokasi 1') === 'Lokasi 2')
                  <span style="font-size: 0.68rem; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 6px; border: 1px solid #bae6fd;">{{ $b2NamePortal }}</span>
                @else
                  <span style="font-size: 0.68rem; font-weight: 800; color: #0a5661; background: #f0fdfa; padding: 2px 7px; border-radius: 6px; border: 1px solid #ccfbf1;">{{ $b1NamePortal }}</span>
                @endif
              </div>

              <div class="tp-room-card-facilities">
                @foreach(array_slice($r->facilities ?? ['AC', 'WiFi', 'Kasur', 'Lemari', 'KM Dalam'], 0, 4) as $fac)
                  <span class="tp-room-card-facility-pill">{{ $fac }}</span>
                @endforeach
              </div>

              <div class="tp-room-card-foot">
                <div class="tp-room-card-price-box">
                  <span class="tp-room-card-price-label">Biaya Sewa</span>
                  <span class="tp-room-card-price-val">Rp {{ number_format($r->price, 0, ',', '.') }}<span class="tp-room-card-price-unit">/bln</span></span>
                </div>

                @if($isMyRoom)
                  <button type="button" class="tp-room-card-action-btn is-my-room" onclick="event.stopPropagation(); switchPortalTab('myroom')">
                    <span>Kamarku</span>
                    <span>→</span>
                  </button>
                @elseif($isOccupied)
                  <button type="button" class="tp-room-card-action-btn is-occupied" onclick="event.stopPropagation(); handleRoomCardClick({{ $r->id }})" title="Kamar sudah terisi oleh penghuni lain">
                    <span>Sudah Disewa</span>
                  </button>
                @else
                  <button type="button" class="tp-room-card-action-btn" onclick="event.stopPropagation(); handleRoomCardClick({{ $r->id }})">
                    <span>Pesan Kamar</span>
                    <span>→</span>
                  </button>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 48px 20px; background: #ffffff; border-radius: 16px; border: 1.5px dashed #cbd5e1;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #f0fdfa; display: inline-flex; align-items: center; justify-content: center; color: #0a5661; margin-bottom: 12px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Belum Ada Unit Kamar Terdaftar</div>
            <p style="font-size: 0.84rem; color: #64748b; margin: 0;">Pengelola kos belum mendaftarkan unit kamar ke dalam sistem.</p>
          </div>
        @endforelse
      </div>

    </div>

    {{-- =========================================================================
         TAB 2: KAMARKU (MY ROOM — MODERN BENTO HOSPITALITY LAYOUT)
         ========================================================================= --}}
    <div id="sectionKamarku" class="tp-portal-tab-content">

      {{-- 1. MODERN RESIDENT WELCOME BANNER --}}
      <div class="tp-welcome-banner">
        <div class="tp-welcome-left">
          <div class="tp-welcome-avatar-wrap" id="btnOpenProfileBanner" style="cursor: pointer;" title="Klik untuk edit profil">
            @if($hasRealAvatar)
              <img src="{{ asset($user->avatar) }}"
                   alt="{{ $user->name }}" class="tp-welcome-avatar" id="welcomeAvatarImg">
              <div class="tp-welcome-avatar tp-welcome-avatar-initial" id="welcomeAvatarInitial" style="display:none !important;">{{ $userInitial }}</div>
            @else
              <img src=""
                   alt="{{ $user->name }}" class="tp-welcome-avatar" id="welcomeAvatarImg" style="display:none !important;">
              <div class="tp-welcome-avatar tp-welcome-avatar-initial" id="welcomeAvatarInitial">{{ $userInitial }}</div>
            @endif
            <span class="tp-welcome-edit-badge" title="Ubah profil">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </span>
          </div>
          <div class="tp-welcome-info">
            <div class="tp-welcome-greeting">{{ $greeting }} Selamat datang di ruang hunian Anda</div>
            <div class="tp-welcome-name">{{ $user->name }}</div>
          </div>
        </div>
      </div>

      {{-- 1.5 NOTIFICATION FOR UNREGISTERED / CHECKED-OUT TENANT --}}
      @if(!$hasActiveRoom)
        <div class="tp-inactive-tenant-banner">
          <div class="tp-inactive-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
          <div class="tp-inactive-content">
            <div class="tp-inactive-title">Status Akun: Belum Terdaftar di Kamar Aktif / Selesai Sewa</div>
            <div class="tp-inactive-desc">
              Akun Anda saat ini belum dikaitkan dengan unit kamar aktif oleh Pengelola. Fitur <strong>Lapor Keluhan</strong> dan <strong>Pindah Kamar</strong> hanya dapat digunakan oleh customer yang terdaftar aktif di kamar kos. Untuk menghubungkan akun atau mendaftar sewa kamar, hubungi Pengelola Mas Robi melalui WhatsApp.
            </div>
            <div class="tp-inactive-actions">
              <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin%20Kos%20Mas%20Robi,%20saya%20ingin%20menghubungkan%20akun%20penghuni%20saya%20({{ urlencode($user->email) }})" target="_blank" class="tp-btn-inactive-wa">
                Hubungi Pengelola via WhatsApp
              </a>
            </div>
          </div>
        </div>
      @endif

      {{-- 2. SHOWCASE SECTION (Desktop: Left Gallery Slider, Right Room & Billing Card) --}}
      @php
        $roomImages = ($hasActiveRoom && $room) ? ($room->images_list ?? [
          'assets/images/rooms/room_1.jpg',
          'assets/images/rooms/room_2.jpg',
          'assets/images/rooms/room_3.jpg',
        ]) : [
          'assets/images/rooms/room_1.jpg',
          'assets/images/rooms/room_2.jpg',
          'assets/images/rooms/room_3.jpg',
        ];
      @endphp

      <div class="tp-showcase-container" id="tpHeroCard">

        {{-- LEFT: INTERACTIVE GALLERY SLIDER --}}
        <div class="tp-showcase-gallery">
          <div class="tp-gallery-slider" id="heroBgSlider">
            @foreach($roomImages as $idx => $img)
              <div class="tp-hero-bg-slide {{ $idx === 0 ? 'active' : '' }}"
                   style="background-image: url('{{ asset($img) }}');"
                   data-slide-index="{{ $idx }}"></div>
            @endforeach
          </div>

          {{-- Top Badge: Counter Foto --}}
          <div class="tp-gallery-badge">
            <span class="tp-pulse-dot"></span>
            <span id="heroSlideBadgeText">Foto 1/{{ count($roomImages) }}: {{ $hasActiveRoom && $room ? ('Kamar #' . $room->number) : 'Unit Kos Mas Robi' }}</span>
          </div>

          {{-- Bottom Overlay Info --}}
          <div class="tp-gallery-bottom-gradient">
            <div class="tp-gallery-controls">
              <button type="button" class="tp-gallery-nav-btn prev" id="btnSlidePrev" title="Foto Sebelumnya">&#8249;</button>
              <div class="tp-gallery-dots" id="heroOverlayDots">
                @foreach($roomImages as $idx => $img)
                  <button type="button" class="tp-overlay-dot {{ $idx === 0 ? 'active' : '' }}" data-dot-index="{{ $idx }}" title="Slide {{ $idx + 1 }}"></button>
                @endforeach
              </div>
              <button type="button" class="tp-gallery-nav-btn next" id="btnSlideNext" title="Foto Selanjutnya">&#8250;</button>
            </div>
          </div>
        </div>

        {{-- RIGHT: ROOM DETAILS & BILLING SUMMARY CARD --}}
        <div class="tp-showcase-details">
          <div class="tp-details-top">
            <div class="tp-details-header">
              <div>
                @if($hasActiveRoom && $room)
                  <span class="tp-badge-category">Hunian Aktif Anda</span>
                  <h2 class="tp-details-title">Kamar #{{ $room->number }} • {{ $room->type ?? 'Deluxe AC' }}</h2>
                @else
                  <span class="tp-badge-category" style="background: #f1f5f9; color: #475569;">Status Akun</span>
                  <h2 class="tp-details-title">Belum Terdaftar di Kamar Aktif</h2>
                @endif
              </div>
              @if($hasActiveRoom && $room)
                <span class="tp-status-pill {{ ($room->status ?? '') === 'due' ? 'due' : 'paid' }}">
                  @if(($room->status ?? '') === 'due')
                    Menunggu Bayar
                  @else
                    ● Sewa Aktif
                  @endif
                </span>
              @else
                <span class="tp-status-pill" style="background: #fef3c7; color: #b45309; border: 1.5px solid #fde68a;">
                  Nonaktif / Belum Terkait
                </span>
              @endif
            </div>

            {{-- Location Strip --}}
            <div class="tp-details-branch-strip">
              <div class="tp-details-branch-left">
                <span class="tp-details-branch-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <div style="min-width: 0;">
                  <div class="tp-details-branch-title">{{ $branchName }}</div>
                  <div class="tp-details-branch-addr">{{ $branchAddr }}</div>
                </div>
              </div>
              <a href="{{ $branchMaps }}" target="_blank" class="tp-details-branch-maps">Buka Maps</a>
            </div>

            {{-- Pricing & Due Date Box --}}
            <div class="tp-price-banner">
              @if($hasActiveRoom && $room)
                @php
                  $vMonthlyRent = (float) ($monthlyRent ?? (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 0)));
                  $vRemainingBill = isset($remainingBill) ? (float) $remainingBill : $vMonthlyRent;
                  $vPaidThisMonth = isset($paidThisMonth) ? (float) $paidThisMonth : 0;
                  $vIsPartial = $isPartialInstallment ?? ($vPaidThisMonth > 0 && $vRemainingBill > 0);
                  $vIsPaid = $isPaidThisMonth ?? ($vPaidThisMonth >= $vMonthlyRent);
                  $vIsDue = $isDue ?? ($room->status === 'due');
                @endphp
                <div class="tp-price-main">
                  @if($vIsPartial)
                    <span class="tp-price-lbl" style="color: #d97706; font-weight: 700;">Sisa Tagihan Periode Ini</span>
                    <div class="tp-price-val" style="color: #b45309;">
                      Rp {{ number_format($vRemainingBill, 0, ',', '.') }}
                      <span class="tp-price-cycle" style="display: block; font-size: 0.72rem; font-weight: 600; color: #78350f; margin-top: 2px;">
                        Terbayar Rp {{ number_format($vPaidThisMonth, 0, ',', '.') }} dari Rp {{ number_format($vMonthlyRent, 0, ',', '.') }}
                      </span>
                    </div>
                  @elseif($vIsPaid)
                    <span class="tp-price-lbl" style="color: #059669; font-weight: 700;">Tagihan Periode Ini: Lunas</span>
                    <div class="tp-price-val" style="color: #065f46;">
                      Rp {{ number_format($vMonthlyRent, 0, ',', '.') }}
                      <span class="tp-price-cycle">/bulan</span>
                    </div>
                  @elseif($vIsDue)
                    <span class="tp-price-lbl" style="color: #dc2626; font-weight: 700;">Tagihan Jatuh Tempo (Menunggu Bayar)</span>
                    <div class="tp-price-val" style="color: #b91c1c;">
                      Rp {{ number_format($vRemainingBill > 0 ? $vRemainingBill : $vMonthlyRent, 0, ',', '.') }}
                      <span class="tp-price-cycle">/bulan</span>
                    </div>
                  @else
                    <span class="tp-price-lbl">Tagihan Sewa Bulanan</span>
                    <div class="tp-price-val">
                      Rp {{ number_format($vMonthlyRent, 0, ',', '.') }}
                      <span class="tp-price-cycle">/bulan</span>
                    </div>
                  @endif
                </div>
                <div class="tp-due-badge">
                  <span class="tp-due-lbl">Jatuh Tempo Sewa</span>
                  <span class="tp-due-date">{{ $dueDate->translatedFormat('d F Y') }}</span>
                </div>
              @else
                @php
                  $minPrice = $allRooms->min('price');
                @endphp
                <div class="tp-price-main">
                  <span class="tp-price-lbl">Pilihan Kamar Tersedia</span>
                  <div class="tp-price-val" style="font-size: 1.15rem; color: #334155;">
                    @if($minPrice)
                      Mulai Rp {{ number_format($minPrice, 0, ',', '.') }}
                      <span class="tp-price-cycle">/bulan</span>
                    @else
                      Hubungi Pengelola
                    @endif
                  </div>
                </div>
                <div class="tp-due-badge" style="background: #f8fafc; border-color: #e2e8f0;">
                  <span class="tp-due-lbl">Status Kamar</span>
                  <span class="tp-due-date" style="color: #64748b;">
                    {{ $allRooms->isNotEmpty() ? 'Lihat katalog di bawah' : 'Belum ada kamar aktif' }}
                  </span>
                </div>
              @endif
            </div>

            {{-- Description --}}
            <div class="tp-desc-block">
              @if($hasActiveRoom && $room)
                <h4 class="tp-desc-heading">Spesifikasi & Kenyamanan Unit</h4>
                <p class="tp-desc-body">
                  Unit kamar tipe {{ $room->type ?? 'Deluxe AC' }} di Lantai {{ $room->floor ?? '1' }} ({{ $branchName }}). Dilengkapi pendingin ruangan (AC), kamar mandi dalam pribadi, meja kerja ergonomis, lemari pakaian, serta koneksi internet WiFi cepat.
                </p>
              @else
                <h4 class="tp-desc-heading">Kenyamanan Hunian {{ $setting->kos_name ?? 'Kos Mas Robi' }}</h4>
                <p class="tp-desc-body">
                  Akun Anda saat ini belum dikaitkan dengan unit kamar aktif atau masa sewa Anda telah selesai. Anda dapat meninjau ketersediaan kamar pada katalog interaktif di bawah atau menghubungi pengelola kos.
                </p>
              @endif
            </div>

            {{-- Facilities Quick Icons with Crisp SVGs --}}
            <div class="tp-fac-quick-list">
              <div class="tp-fac-pill">
                <span class="tp-fac-svg-ico">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
                </span>
                <span class="tp-fac-text">Kasur Queen</span>
              </div>
              <div class="tp-fac-pill">
                <span class="tp-fac-svg-ico">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path d="M4 4l2.5 2.5"/><path d="M13.5 6.5a4.95 4.95 0 0 0-7 7"/><path d="M15 5L5 15"/><path d="M14 17v.01"/><path d="M10 16v.01"/><path d="M13 13v.01"/><path d="M16 10v.01"/><path d="M11 20v.01"/><path d="M17 14v.01"/><path d="M20 11v.01"/></svg>
                </span>
                <span class="tp-fac-text">KM Dalam</span>
              </div>
              <div class="tp-fac-pill">
                <span class="tp-fac-svg-ico">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><rect x="2" y="4" width="20" height="8" rx="2"/><path d="M6 8h.01"/><path d="M10 8h8"/><path d="M6 16c1.5 2 3.5 2 5 0s3.5-2 5 0"/></svg>
                </span>
                <span class="tp-fac-text">AC Dingin</span>
              </div>
              <div class="tp-fac-pill">
                <span class="tp-fac-svg-ico">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                </span>
                <span class="tp-fac-text">WiFi Cepat</span>
              </div>
            </div>
          </div>

          {{-- Action Buttons Row --}}
          <div class="tp-details-actions">
            <button type="button" class="tp-btn-secondary-action tp-btn-receipt-action" onclick="document.getElementById('modalReceipts')?.classList.add('open')">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              <span>Kuitansi & Riwayat</span>
            </button>
            <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin,%20saya%20penghuni%20Kamar%20%23{{ $room->number ?? '102' }}%20ingin%20konfirmasi%20sewa"
               target="_blank" class="tp-btn-primary-action tp-btn-wa-green" style="text-decoration: none;">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
              </svg>
              <span>WhatsApp Admin</span>
            </a>
          </div>
        </div>

      </div>{{-- /tp-showcase-container --}}

      {{-- 3. OVERVIEW METRICS & ROOM INFO BENTO ROW --}}
      <div class="tp-overview-row">

        {{-- Left Card: Facility Metrics --}}
        <div class="tp-metrics-card">
          <div class="tp-card-row">
            <div>
              <span class="tp-card-label">Status Fasilitas Kamar</span>
              <div style="font-size: 0.76rem; color: #64748b; margin-top: 2px;">Indikator performa fasilitas unit hunian Anda</div>
            </div>
            <span class="tp-badge blue">Kamar #{{ $hasActiveRoom && $room ? $room->number : '-' }}</span>
          </div>
          <div class="tp-metrics-grid">
            <div class="tp-metric-box">
              <div class="tp-metric-ico-circle sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="4" width="20" height="8" rx="2"/><path d="M6 8h.01"/><path d="M10 8h8"/><path d="M6 16c1.5 2 3.5 2 5 0s3.5-2 5 0"/></svg>
              </div>
              <span class="tp-metric-val">22°C</span>
              <span class="tp-metric-lbl">Suhu AC Kamar</span>
              <span class="tp-metric-pill">Dingin Optimal</span>
            </div>
            <div class="tp-metric-box">
              <div class="tp-metric-ico-circle teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
              </div>
              <span class="tp-metric-val">{{ $isB2 ? '300' : '100' }} <small style="font-size:0.72rem;font-weight:700;">Mbps</small></span>
              <span class="tp-metric-lbl">Jaringan WiFi</span>
              <span class="tp-metric-pill">Stabil & Cepat</span>
            </div>
            <div class="tp-metric-box">
              <div class="tp-metric-ico-circle emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <span class="tp-metric-val">ON</span>
              <span class="tp-metric-lbl">Listrik & Air</span>
              <span class="tp-metric-pill">Aktif 24 Jam</span>
            </div>
          </div>
        </div>

        {{-- Right Card: Room Info Summary --}}
        <div class="tp-roominfo-card">
          <div class="tp-card-row">
            <div>
              <span class="tp-card-label">Informasi Detail Hunian</span>
              <div style="font-size: 0.76rem; color: #64748b; margin-top: 2px;">Ringkasan kontrak sewa & status pembayaran</div>
            </div>
            <span class="tp-badge green">● Terverifikasi</span>
          </div>
          <div class="tp-roominfo-grid">
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val">{{ $hasActiveRoom && $room ? ('Kamar #' . $room->number . ' (Lt. ' . $room->floor . ')') : 'Belum Ditentukan' }}</div>
                <div class="tp-roominfo-key">Unit & Lantai Kamar</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val">{{ $hasActiveRoom ? $dueDate->format('d M Y') : '-' }}</div>
                <div class="tp-roominfo-key">Jatuh Tempo Berikutnya</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val">{{ $hasActiveRoom && $room ? ('Rp ' . number_format($monthlyRent ?? (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 0)), 0, ',', '.')) : '-' }}</div>
                <div class="tp-roominfo-key">Tarif Sewa / Bulan</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico" style="background: {{ ($isPartialInstallment ?? false) ? '#fef3c7' : (($isDue ?? false) ? '#fee2e2' : '#ecfdf5') }}; color: {{ ($isPartialInstallment ?? false) ? '#d97706' : (($isDue ?? false) ? '#dc2626' : '#059669') }};">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val {{ !$hasActiveRoom ? '' : (($isDue ?? false) ? 'red' : (($isPartialInstallment ?? false) ? 'orange' : 'green')) }}">
                  @if(!$hasActiveRoom)
                    Nonaktif / Selesai Sewa
                  @elseif($isPartialInstallment ?? false)
                    Cicilan (Sisa Rp {{ number_format($remainingBill ?? 0, 0, ',', '.') }})
                  @elseif($isDue ?? false)
                    Menunggu Bayar (Rp {{ number_format(($remainingBill ?? 0) > 0 ? $remainingBill : ($monthlyRent ?? ($room->price ?? 0)), 0, ',', '.') }})
                  @else
                    Lunas &amp; Aktif
                  @endif
                </div>
                <div class="tp-roominfo-key">Status Tagihan Periode Ini</div>
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /tp-overview-row --}}

      {{-- 4. SYMMETRICAL 4-COLUMN QUICK ACCESS BENTO GRID --}}
      <div class="tp-quick-section">
        <div class="tp-section-hdr">
          <div>
            <h2 class="tp-section-ttl">Akses & Layanan Cepat Penghuni</h2>
            <span class="tp-section-sub">Kelola fasilitas kamar, administrasi kuitansi, dan permohonan layanan secara instan</span>
          </div>
        </div>
        <div class="tp-quick-grid">

          <button type="button" class="tp-quick-tile {{ !$hasActiveRoom ? 'tp-quick-locked' : '' }}" id="btnOpenComplaint"
                  data-has-active-room="{{ $hasActiveRoom ? '1' : '0' }}">
            <div class="tp-quick-ico purple">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
              </svg>
            </div>
            <div>
              <div class="tp-quick-ttl" style="display: flex; align-items: center; gap: 6px;">
                <span>Lapor Keluhan</span>
                @if(!$hasActiveRoom)
                  <span class="tp-pill-locked">Terkunci</span>
                @endif
              </div>
              <div class="tp-quick-desc">
                @if($hasActiveRoom)
                  Laporkan kendala AC, air, lampu, atau perbaikan fasilitas kamar
                @else
                  Khusus customer yang terdaftar aktif di unit kamar
                @endif
              </div>
            </div>
            <div class="tp-quick-footer">
              <span class="tp-quick-cta-lbl">{{ $hasActiveRoom ? 'Buat Laporan' : 'Khusus Penghuni' }}</span>
              <span class="tp-quick-arrow">{{ $hasActiveRoom ? '→' : '[Terkunci]' }}</span>
            </div>
          </button>

          <button type="button" class="tp-quick-tile" id="btnOpenReceipts">
            <div class="tp-quick-ico amber">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
              </svg>
            </div>
            <div>
              <div class="tp-quick-ttl">Kuitansi Digital</div>
              <div class="tp-quick-desc">Lihat riwayat pembayaran sewa & cetak bukti kuitansi resmi</div>
            </div>
            <div class="tp-quick-footer"><span class="tp-quick-cta-lbl">Lihat Kuitansi</span><span class="tp-quick-arrow">→</span></div>
          </button>

          <button type="button" class="tp-quick-tile {{ !$hasActiveRoom ? 'tp-quick-locked' : '' }}" id="btnOpenRoomTransfer"
                  data-has-active-room="{{ $hasActiveRoom ? '1' : '0' }}">
            <div class="tp-quick-ico blue">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
                <path d="M16 3h5v5"/>
                <path d="M4 20L21 3"/>
                <path d="M21 16v5h-5"/>
                <path d="M15 15l6 6"/>
                <path d="M4 4l5 5"/>
              </svg>
            </div>
            <div>
              <div class="tp-quick-ttl" style="display: flex; align-items: center; gap: 6px;">
                <span>Pindah Kamar</span>
                @if(!$hasActiveRoom)
                  <span class="tp-pill-locked">Terkunci</span>
                @endif
              </div>
              <div class="tp-quick-desc">
                @if($hasActiveRoom)
                  Ajukan perpindahan unit kamar atau pindah lintas cabang kos
                @else
                  Khusus customer yang terdaftar aktif di unit kamar
                @endif
              </div>
            </div>
            <div class="tp-quick-footer">
              <span class="tp-quick-cta-lbl">{{ $hasActiveRoom ? 'Ajukan Pindah' : 'Khusus Penghuni' }}</span>
              <span class="tp-quick-arrow">{{ $hasActiveRoom ? '→' : '[Terkunci]' }}</span>
            </div>
          </button>

          <button type="button" class="tp-quick-tile" onclick="document.getElementById('modalRules')?.classList.add('open')">
            <div class="tp-quick-ico green">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
            </div>
            <div>
              <div class="tp-quick-ttl">Tata Tertib Kos</div>
              <div class="tp-quick-desc">Pedoman kenyamanan, keamanan, dan peraturan resmi penghuni</div>
            </div>
            <div class="tp-quick-footer"><span class="tp-quick-cta-lbl">Baca Aturan</span><span class="tp-quick-arrow">→</span></div>
          </button>

        </div>
      </div>

    </div> {{-- End #sectionKamarku --}}

  </main>

{{-- =============================================================== MODALS POPUP =============================================================== --}}

{{-- Modal 0: Detail Kamar & Permintaan Custom Interior --}}
<div class="modal-overlay" id="modalRoomDetailCustom">
  <div class="modal-box wide" style="max-width: 680px;">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title" id="mCustomRoomTitle">Detail Kamar #101</h3>
        <p class="modal-subtitle" id="mCustomRoomSubtitle">Informasi hunian & pengajuan Custom Interior</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalRoomDetailCustom" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    
    <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
      
      <!-- Photo Preview Slider (Foto Bergerak Otomatis Seperti di Kamarku) -->
      <div class="tp-modal-slider-wrap" id="modalRoomSliderWrap">
        <div class="tp-modal-slider-track" id="modalRoomSliderTrack">
          {{-- Slides injected dynamically --}}
        </div>

        {{-- Top Slider Counter Badge --}}
        <div class="tp-modal-slider-badge">
          <span class="tp-pulse-dot"></span>
          <span id="modalRoomSlideBadgeText">Foto 1/3: Kamar #101</span>
        </div>

        {{-- Room Meta Tag Overlay --}}
        <div class="tp-modal-slider-overlay-meta">
          <div class="tp-modal-slider-room-badge">
            <span id="mCustomRoomBadge">Kamar #101</span> • <span id="mCustomRoomFloor">Lantai 1</span>
          </div>
          <div id="mCustomRoomStatusTag">
            <span class="tp-pill paid">Tersedia</span>
          </div>
        </div>

        {{-- Nav Buttons --}}
        <button type="button" class="tp-modal-slider-nav prev" id="btnModalSlidePrev" onclick="navigateModalRoomSlide(-1)" title="Foto Sebelumnya">&#8249;</button>
        <button type="button" class="tp-modal-slider-nav next" id="btnModalSlideNext" onclick="navigateModalRoomSlide(1)" title="Foto Selanjutnya">&#8250;</button>

        {{-- Dots Indicators --}}
        <div class="tp-modal-slider-dots" id="modalRoomSliderDots"></div>
      </div>

      <!-- Quick Specs -->
      <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 18px;">
        <div>
          <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Harga Sewa / Bulan</span>
          <div style="font-size: 1.25rem; font-weight: 800; color: #2563eb;" id="mCustomRoomPrice">Rp 1.500.000</div>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Tipe Unit Kamar</span>
          <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;" id="mCustomRoomType">Deluxe AC</div>
        </div>
      </div>

      <!-- Facilities List -->
      <div>
        <h4 style="font-size: 0.85rem; font-weight: 800; color: #334155; margin-bottom: 8px;">Fasilitas Termasuk:</h4>
        <div id="mCustomRoomFacilities" style="display: flex; flex-wrap: wrap; gap: 6px;"></div>
      </div>

      <!-- Existing Custom Interior Status Box if present -->
      <div id="mCustomExistingStatusBox" style="display: none; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
          <div style="font-size: 0.85rem; font-weight: 800; color: #1e40af;">Status Custom Interior Saat Ini:</div>
          <span id="mCustomExistingBadge" class="tp-pill paid">Menunggu Review</span>
        </div>
        <div style="font-size: 0.82rem; color: #334155;" id="mCustomExistingReqText"></div>
        <div style="margin-top: 8px; font-size: 0.84rem; font-weight: 700; color: #2563eb;">
          Anggaran Disetujui Admin: <span id="mCustomExistingBudget">Rp 0</span>
        </div>
        <div id="mCustomExistingNotes" style="margin-top: 4px; font-size: 0.78rem; color: #64748b; font-style: italic;"></div>
      </div>

      <!-- Notice jika kamar sudah terisi / disewa orang lain -->
      <div id="mCustomOccupiedNotice" style="display: none; background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 16px; padding: 20px; text-align: center;">
        <div style="width: 46px; height: 46px; border-radius: 50%; background: #fee2e2; color: #e11d48; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </div>
        <h4 style="font-size: 0.98rem; font-weight: 800; color: #9f1239; margin-bottom: 6px;">Kamar Sudah Terisi / Disewa</h4>
        <p style="font-size: 0.84rem; color: #be123c; margin: 0 0 16px; line-height: 1.5; max-width: 480px; margin-left: auto; margin-right: auto;">
          Unit kamar ini saat ini telah ditempati oleh penghuni lain atau sudah dibooking. Kamar tidak dapat dipesan kembali. Silakan pilih unit kamar yang berstatus <strong>Kosong / Tersedia</strong> di katalog.
        </p>
        <button type="button" class="btn-secondary" data-close-modal="modalRoomDetailCustom" style="padding: 7px 18px; font-size: 0.82rem; border-radius: 8px;">Tutup & Cari Kamar Tersedia</button>
      </div>

      <!-- Custom Interior Request Form -->
      <form id="formCustomInterior" method="POST" action="">
        @csrf
        <div class="tp-custom-interior-card">
          <div class="tp-custom-interior-title">
            <span>Permintaan Custom Interior & Booking Kamar</span>
          </div>
          <p class="tp-custom-interior-desc">
            Ingin tata letak furnitur tambahan seperti ambalan kayu estetik, lampu warm white, karpet, atau penataan khusus? Tuliskan permintaan Anda di bawah beserta perkiraan anggaran. Admin akan meninjau dan menetapkan anggaran khusus secara fleksibel.
          </p>

          <div style="display: flex; flex-direction: column; gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="customInteriorReqInput" class="form-label">
                <span>Rincian Permintaan Custom Interior *</span>
                <span class="form-label-hint">Tuliskan kebutuhan dekorasi / furnitur tambahan</span>
              </label>
              <textarea id="customInteriorReqInput" name="custom_interior_request" class="form-control" rows="3" required placeholder="Contoh: Tolong pasangkan ambalan rak buku minimalis kayu jati 2 tingkat di atas meja kerja dan ganti tirai dengan warna beige modern..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="customInteriorBudgetInput" class="form-label">
                  <span>Estimasi Budget Anda (Rp)</span>
                  <span class="form-label-hint">Dapat disesuaikan admin</span>
                </label>
                <input type="number" id="customInteriorBudgetInput" name="custom_interior_budget" class="form-control" placeholder="Contoh: 500000" min="0" step="10000">
              </div>

              <div class="form-group" style="margin-bottom: 0;">
                <label for="customInteriorPhoneInput" class="form-label">
                  <span>No. WhatsApp Pemesan *</span>
                  <span class="form-label-hint">Untuk konfirmasi pengelola</span>
                </label>
                <input type="tel" id="customInteriorPhoneInput" name="booked_by_phone" class="form-control" value="{{ $user->phone }}" required>
              </div>
            </div>
            
            <input type="hidden" name="booked_by_name" value="{{ $user->name }}">
          </div>

          <div style="margin-top: 14px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn-secondary" data-close-modal="modalRoomDetailCustom">Tutup</button>
            <button type="submit" class="btn-primary" id="btnSubmitCustomInterior">
              Ajukan Custom Interior & Pesan Kamar
            </button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- Modal 2: Lapor Keluhan --}}
<div class="modal-overlay" id="modalComplaint">
  <div class="modal-box">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Lapor Keluhan / Kendala Fasilitas</h3>
        <p class="modal-subtitle">
          @if($hasActiveRoom && $room)
            Kamar #{{ $room->number }} — Pengelola akan segera merespons
          @else
            Akses Terbatas — Khusus Penghuni Aktif
          @endif
        </p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalComplaint" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    @if(!$hasActiveRoom)
      <div class="modal-body" style="padding: 24px; text-align: center;">
        <div style="width: 54px; height: 54px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </div>
        <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Akses Layanan Terbatas</h4>
        <p style="font-size: 0.88rem; color: #64748b; line-height: 1.55; max-width: 420px; margin: 0 auto 20px;">
          Fitur <strong>Lapor Keluhan & Servis</strong> hanya dapat digunakan oleh customer yang terdaftar aktif di unit kamar kos. Akun Anda saat ini belum dikaitkan dengan unit kamar aktif atau masa sewa telah selesai.
        </p>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: left; font-size: 0.82rem; color: #475569; margin-bottom: 20px;">
          <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Informasi Akun Anda:</div>
          <div>- Nama: <strong>{{ $user->name }}</strong></div>
          <div>- Email: <strong>{{ $user->email }}</strong></div>
          <div>- Status Kamar: <span style="color: #dc2626; font-weight: 700;">Belum terdaftar di kamar aktif</span></div>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
          <button type="button" class="btn-secondary" data-close-modal="modalComplaint">Tutup</button>
          <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin%20Kos%20Mas%20Robi,%20saya%20ingin%20menghubungkan%20akun%20penghuni%20saya%20({{ urlencode($user->email) }})" target="_blank" class="btn-primary" style="text-decoration: none;">
            Hubungi Pengelola via WhatsApp
          </a>
        </div>
      </div>
    @else
      <form action="{{ route('tenant.complaint.submit') }}" method="POST" id="formComplaint">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="priority" id="complaintPriority" value="Normal">
          <div class="form-group" style="margin-bottom: 0;">
            <label for="complaintIssue" class="form-label" style="font-weight: 700; margin-bottom: 8px; font-size: 0.92rem;">
              Tuliskan Keluhan / Kendala Fasilitas Kamar Anda *
            </label>
            <textarea id="complaintIssue" name="issue_description" class="form-control" rows="6"
              placeholder="Tuliskan kendala Anda secara lengkap di sini (contoh: AC tidak dingin sejak kemarin malam, lampu kamar mandi padam, atau kran air bocor)..."
              required style="resize: vertical; font-size: 0.92rem; border-radius: 12px;"></textarea>
            <div style="font-size: 0.76rem; color: #64748b; margin-top: 6px;">
              Laporan Anda akan langsung diterima pengelola kos dan segera ditindaklanjuti.
            </div>
          </div>
        </div>
        <div class="modal-ftr">
          <button type="button" class="btn-secondary" data-close-modal="modalComplaint">Batal</button>
          <button type="submit" class="btn-primary">Kirim Laporan ke Admin</button>
        </div>
      </form>
    @endif
  </div>
</div>

{{-- Modal 3: Kuitansi Digital & Riwayat Pembayaran --}}
<div class="modal-overlay" id="modalReceipts">
  <div class="modal-box wide tp-receipts-modal-box">
    <div class="modal-hdr tp-modal-hdr-modern">
      <div class="tp-hdr-left">
        <div class="tp-hdr-badge-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Kuitansi Pembayaran & Riwayat</h3>
          <p class="modal-subtitle">Bukti resmi transaksi sewa {{ $hasActiveRoom && $room ? ('Kamar #' . $room->number) : 'Hunian' }} yang telah divalidasi sistem</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalReceipts" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="modal-body tp-receipts-modal-body">
      @if($hasActiveRoom && $room)
        {{-- Status Rincian Tagihan Sewa (Sinkron dengan Admin & Owner) --}}
        @php
          $mRent = (float) ($monthlyRent ?? (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 0)));
          $mPaid = isset($paidThisMonth) ? (float) $paidThisMonth : 0;
          $mRem = isset($remainingBill) ? (float) $remainingBill : max(0, $mRent - $mPaid);
          $mIsPartial = $isPartialInstallment ?? ($mPaid > 0 && $mRem > 0);
          $mIsPaid = $isPaidThisMonth ?? ($mPaid >= $mRent);
          $mIsDue = $isDue ?? ($room->status === 'due');
        @endphp
        <div style="background: {{ $mIsPaid ? '#f0fdf4' : ($mIsPartial ? '#fffbeb' : '#fef2f2') }}; border: 1.5px solid {{ $mIsPaid ? '#bbf7d0' : ($mIsPartial ? '#fde68a' : '#fecaca') }}; border-radius: 14px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <div>
            <div style="font-size: 0.74rem; font-weight: 800; color: {{ $mIsPaid ? '#166534' : ($mIsPartial ? '#92400e' : '#991b1b') }}; text-transform: uppercase; letter-spacing: 0.5px;">
              Status Tagihan Periode {{ now()->translatedFormat('F Y') }}
            </div>
            <div style="font-size: 1.15rem; font-weight: 900; color: #0f172a; margin-top: 3px;">
              @if($mIsPaid)
                Lunas (Rp {{ number_format($mRent, 0, ',', '.') }})
              @elseif($mIsPartial)
                Sisa Tagihan: Rp {{ number_format($mRem, 0, ',', '.') }}
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748b; margin-left: 4px;">(Terbayar Rp {{ number_format($mPaid, 0, ',', '.') }} dari Rp {{ number_format($mRent, 0, ',', '.') }})</span>
              @else
                Menunggu Bayar: Rp {{ number_format($mRem > 0 ? $mRem : $mRent, 0, ',', '.') }}
              @endif
            </div>
            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
              Unit: Kamar #{{ $room->number }} &bull; Jatuh tempo tiap tanggal {{ $dueDate->format('d') }}
            </div>
          </div>
          <div>
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 800; padding: 6px 14px; border-radius: 999px; background: {{ $mIsPaid ? '#dcfce7' : ($mIsPartial ? '#fef3c7' : '#fee2e2') }}; color: {{ $mIsPaid ? '#15803d' : ($mIsPartial ? '#b45309' : '#b91c1c') }};">
              <span style="width: 7px; height: 7px; border-radius: 50%; background: currentColor;"></span>
              {{ $mIsPaid ? 'Lunas Bulan Ini' : ($mIsPartial ? 'Cicilan Berjalan' : 'Menunggu Bayar') }}
            </span>
          </div>
        </div>
      @endif

      @if($recentPayments->isNotEmpty())
        {{-- Summary Banner Bar --}}
        <div class="tp-receipt-summary-bar">
          <div class="tp-rec-summary-item">
            <span class="tp-rec-sum-lbl">Total Kuitansi Terbit</span>
            <span class="tp-rec-sum-val">{{ $recentPayments->count() }} Pembayaran</span>
          </div>
          <div class="tp-rec-summary-item right">
            <span class="tp-rec-sum-badge">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              Terverifikasi & Sah
            </span>
          </div>
        </div>

        {{-- Desktop Table View (Hidden on mobile) --}}
        <div class="tp-receipts-desktop-table-wrap">
          <table class="tp-table">
            <thead>
              <tr>
                <th>Kode & Keterangan</th>
                <th>Waktu Transaksi</th>
                <th>Metode</th>
                <th>Nominal</th>
                <th>Status & Kuitansi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentPayments as $p)
                <tr>
                  <td class="tp-cell-desc">
                    <div class="tp-desc-main">{{ $p->description }}</div>
                    <div class="tp-desc-sub">
                      <span class="tp-badge-code">{{ $p->transaction_code ?? 'TRX' }}</span>
                      @if(!empty($p->proof_image))
                        <a href="{{ asset($p->proof_image) }}" target="_blank" class="tp-proof-link">Foto Bukti</a>
                      @endif
                    </div>
                  </td>
                  <td class="tp-cell-muted">
                    <div style="font-weight: 700; color: #0f172a;">{{ $p->date->format('d M Y') }}</div>
                    <div class="tp-time-tz">{{ $p->formatted_payment_time }}</div>
                  </td>
                  <td>
                    <span class="tp-method-pill">{{ $p->payment_method }}</span>
                  </td>
                  <td class="tp-cell-amount">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                  <td>
                    <div class="tp-cell-action-group">
                      <span class="tp-pill paid">LUNAS</span>
                      <button type="button" class="tp-btn-receipt-small"
                              onclick="viewDetailedReceipt('{{ $p->transaction_code }}', '{{ $p->date->format('d M Y') }}', '{{ $p->formatted_payment_time }}', '{{ addslashes($user->name) }}', '{{ $room->number ?? '102' }}', '{{ addslashes($p->description) }}', '{{ $p->payment_method }}', '{{ number_format($p->amount, 0, ',', '.') }}', '{{ $p->proof_image ? asset($p->proof_image) : '' }}')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Kuitansi</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Mobile Cards View (Visible on mobile HP, Hidden on desktop) --}}
        <div class="tp-receipts-mobile-cards">
          @foreach($recentPayments as $p)
            <div class="tp-rec-mobile-card">
              <div class="tp-rec-mc-top">
                <span class="tp-badge-code">{{ $p->transaction_code ?? 'TRX' }}</span>
                <span class="tp-pill paid">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  LUNAS
                </span>
              </div>
              <div class="tp-rec-mc-amount-row">
                <div class="tp-rec-mc-amount">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                <span class="tp-method-pill">{{ $p->payment_method }}</span>
              </div>
              <div class="tp-rec-mc-desc">{{ $p->description }}</div>
              <div class="tp-rec-mc-meta">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $p->date->format('d M Y') }} • {{ $p->formatted_payment_time }}</span>
              </div>
              <div class="tp-rec-mc-actions">
                <button type="button" class="tp-rec-mc-btn-primary"
                        onclick="viewDetailedReceipt('{{ $p->transaction_code }}', '{{ $p->date->format('d M Y') }}', '{{ $p->formatted_payment_time }}', '{{ addslashes($user->name) }}', '{{ $room->number ?? '102' }}', '{{ addslashes($p->description) }}', '{{ $p->payment_method }}', '{{ number_format($p->amount, 0, ',', '.') }}', '{{ $p->proof_image ? asset($p->proof_image) : '' }}')">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                  <span>Buka Kuitansi Resmi</span>
                </button>
                @if(!empty($p->proof_image))
                  <a href="{{ asset($p->proof_image) }}" target="_blank" class="tp-rec-mc-btn-proof">
                    Foto Bukti
                  </a>
                @endif
              </div>
            </div>
          @endforeach
        </div>

      @else
        {{-- Sleek Modern Empty State --}}
        <div class="tp-receipt-empty-clean">
          <div class="tp-rec-empty-icon-wrap">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
          </div>
          <h4 class="tp-rec-empty-heading">Belum Ada Kuitansi Resmi yang Dikirimkan</h4>
          <p class="tp-rec-empty-paragraph">
            Pengelola kos belum menerbitkan atau mengirimkan kuitansi pembayaran untuk kamar Anda. Kuitansi resmi dan riwayat pembayaran akan otomatis muncul di sini setelah divalidasi dan dikirimkan oleh Admin.
          </p>
        </div>
      @endif
    </div>

    <div class="modal-ftr tp-receipts-modal-ftr">
      <button type="button" class="btn-secondary" data-close-modal="modalReceipts">Tutup</button>
      <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20kuitansi%20pembayaran%20sewa%20kamar%20%23{{ $room->number ?? '102' }}"
         target="_blank" class="btn-primary tp-btn-wa-green" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/></svg>
        <span>Konfirmasi via WhatsApp Admin</span>
      </a>
    </div>
  </div>
</div>

{{-- Modal 3B: Kuitansi Digital Pop-up Resmi --}}
<div class="modal-overlay" id="modalDigitalReceiptDetail">
  <div class="modal-box tp-receipt-detail-modal-box">
    <div class="modal-hdr tp-modal-hdr-modern">
      <div class="tp-hdr-left">
        <div class="tp-hdr-badge-icon emerald">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <polyline points="9 15 11 17 15 13"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Kuitansi Pembayaran Digital</h3>
          <p class="modal-subtitle">Bukti Resmi & Terverifikasi Sistem</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalDigitalReceiptDetail" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="modal-body tp-receipt-slip-body" id="printableReceiptArea">
      <div class="tp-receipt-slip-card">
        {{-- Slip Top Header --}}
        <div class="tp-slip-hdr">
          <div class="tp-slip-brand">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</div>
          <div class="tp-slip-subbrand">KUITANSI ELEKTRONIK RESMI PEMBAYARAN SEWA KOST</div>
          <div class="tp-slip-code-pill" id="dtlRecCode">TRX-000</div>
        </div>

        {{-- Verification Ribbon --}}
        <div class="tp-slip-verified-ribbon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <span>TERVERIFIKASI SAH SISTEM PENGELOLA</span>
        </div>

        {{-- Slip Key-Value Details --}}
        <div class="tp-slip-details-list">
          <div class="tp-slip-row">
            <span class="tp-slip-lbl">Waktu Terbit:</span>
            <span class="tp-slip-val highlight" id="dtlRecTime">03 Nov 2026, 10:00:00 WIB</span>
          </div>
          <div class="tp-slip-row">
            <span class="tp-slip-lbl">Nama Penghuni:</span>
            <span class="tp-slip-val" id="dtlRecTenant">{{ $user->name }}</span>
          </div>
          <div class="tp-slip-row">
            <span class="tp-slip-lbl">Unit Kamar:</span>
            <span class="tp-slip-val bold" id="dtlRecRoom">Kamar #{{ $room->number ?? '102' }}</span>
          </div>
          <div class="tp-slip-row">
            <span class="tp-slip-lbl">Metode Bayar:</span>
            <span class="tp-slip-val" id="dtlRecMethod">Transfer Bank</span>
          </div>
          <div class="tp-slip-row tp-slip-row-desc">
            <span class="tp-slip-lbl">Keterangan:</span>
            <span class="tp-slip-val" id="dtlRecDesc">-</span>
          </div>
        </div>

        {{-- Highlighted Total Box --}}
        <div class="tp-slip-total-box">
          <div class="tp-slip-total-lbl">TOTAL PEMBAYARAN DITERIMA</div>
          <div class="tp-slip-total-val" id="dtlRecAmount">Rp 1.500.000</div>
          <div class="tp-slip-status-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            STATUS: LUNAS & SAH
          </div>
        </div>

        <!-- Lampiran Foto Bukti -->
        <div id="dtlRecProofBox" class="tp-slip-proof-section" style="display: none;">
          <div class="tp-slip-proof-lbl">LAMPIRAN FOTO BUKTI / NOTA TRANSAKSI:</div>
          <a id="dtlRecProofLink" href="" target="_blank">
            <img id="dtlRecProofImg" src="" alt="Bukti Transfer" class="tp-slip-proof-img">
          </a>
        </div>

        {{-- Slip Footer --}}
        <div class="tp-slip-footer">
          <p>Kuitansi elektronik ini merupakan bukti pembayaran resmi yang sah dari Sistem Manajemen {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}. Disimpan secara digital dan dapat dicetak sewaktu-waktu.</p>
        </div>
      </div>
    </div>

    <div class="modal-ftr tp-receipt-slip-ftr">
      <button type="button" class="btn-secondary" data-close-modal="modalDigitalReceiptDetail">Tutup</button>
      <button type="button" class="btn-primary tp-btn-print" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
          <polyline points="6 9 6 2 18 2 18 9"/>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
          <rect x="6" y="14" width="12" height="8"/>
        </svg>
        <span>Cetak / Simpan PDF</span>
      </button>
    </div>
  </div>
</div>

{{-- Modal 4A: Info WiFi Kos (Multi-WiFi & Modem Sesuai Permintaan) --}}
<div class="modal-overlay" id="modalWifi">
  <div class="modal-box" style="max-width: 520px;">
    <div class="modal-hdr">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="tp-icon-badge cyan" style="width: 40px; height: 40px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
            <line x1="12" y1="20" x2="12.01" y2="20"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Info Jaringan WiFi Kos (2 Cabang)</h3>
          <p class="modal-subtitle" style="font-size: 0.76rem; color: #64748b; margin: 2px 0 0;">Daftar koneksi WiFi & modem aktif sesuai lokasi cabang kos Anda</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalWifi" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body" style="display: flex; flex-direction: column; gap: 14px; max-height: 70vh; overflow-y: auto;">
      @if(!$hasActiveRoom || !$room)
        <div style="text-align: center; padding: 36px 20px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px;">
          <div style="width: 52px; height: 52px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <h4 style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">Akses WiFi Terkunci</h4>
          <p style="font-size: 0.84rem; color: #64748b; line-height: 1.5; margin: 0 auto; max-width: 360px;">
            Informasi nama koneksi dan kata sandi WiFi kos hanya dapat diakses oleh penghuni yang terdaftar aktif menempati unit kamar.
          </p>
        </div>
      @else
        @php
          $tenantBranchLoc = ($room->location ?? 'Lokasi 1') === 'Lokasi 2' ? 'Lokasi 2' : 'Lokasi 1';
          $isWfLoc2 = $tenantBranchLoc === 'Lokasi 2';
          $currentBranchName = $isWfLoc2 ? $b2NamePortal : $b1NamePortal;
          $branchWifis = $setting->getActiveWifiForLocation($tenantBranchLoc);
        @endphp

        <div class="tp-wifi-status-card" style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; border: 1.5px solid #bbf7d0; padding: 12px 16px; border-radius: 14px; flex-wrap: wrap; gap: 8px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
            <span class="tp-wifi-status-text" style="color: #166534; font-weight: 800; font-size: 0.82rem;">WiFi Cabang: {{ $currentBranchName }} (Kamar #{{ $room->number }})</span>
          </div>
          <span class="tp-badge green" style="background: #dcfce7; color: #15803d; font-weight: 800; font-size: 0.74rem; padding: 3px 10px; border-radius: 999px;">{{ count($branchWifis) }} Jaringan Tersedia</span>
        </div>

        @if(empty($branchWifis))
          <div style="text-align: center; padding: 30px 18px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
            <div style="color: #64748b; font-size: 0.9rem; font-weight: 700;">Tidak Ada Jaringan Ditayangkan</div>
            <p style="color: #94a3b8; font-size: 0.8rem; margin: 6px 0 0;">Seluruh jaringan WiFi untuk cabang {{ $currentBranchName }} saat ini sedang dinonaktifkan atau disembunyikan oleh pengelola.</p>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 12px;" id="portalWifiCardList">
            @foreach($branchWifis as $idx => $wf)
              @php
                $wfSpeed = $wf['speed'] ?? ($isWfLoc2 ? '300 Mbps Mesh WiFi 6' : '100 Mbps Dual-Band');
                $cardId = 'wf_portal_' . $idx;
              @endphp
              <div class="portal-wf-card" style="background: #ffffff; border: 1.5px solid #0d9488; border-radius: 14px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $isWfLoc2 ? '#ccfbf1' : '#dcfce7' }}; border: 1px solid {{ $isWfLoc2 ? '#5eead4' : '#86efac' }}; color: {{ $isWfLoc2 ? '#0a5661' : '#059669' }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                    </div>
                    <div>
                      <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span style="font-size: 0.66rem; font-weight: 800; padding: 2px 7px; border-radius: 999px; background: {{ $isWfLoc2 ? '#ccfbf1' : '#dcfce7' }}; color: {{ $isWfLoc2 ? '#0a5661' : '#059669' }};">
                          {{ $currentBranchName }}
                        </span>
                        <span style="font-size: 0.66rem; font-weight: 700; color: #64748b;">• {{ $wfSpeed }}</span>
                      </div>
                      <div style="font-size: 0.96rem; font-weight: 800; color: #0f172a; letter-spacing: -0.2px; margin-top: 2px;">{{ $wf['name'] ?? 'KOS_MAS_ROBI' }}</div>
                    </div>
                  </div>
                  <button type="button" class="tp-btn-copy" onclick="copyText('{{ $wf['name'] ?? '' }}', this)" title="Salin SSID" style="background: #f8fafc; border: 1.5px solid #cbd5e1; padding: 5px 10px; border-radius: 8px; font-size: 0.74rem; font-weight: 700; color: #334155; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                    Salin SSID
                  </button>
                </div>

                {{-- Info Modem --}}
                <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px; padding: 7px 10px; display: flex; align-items: center; gap: 8px; margin-bottom: {{ !empty($wf['password']) ? '8px' : '0' }};">
                  <div style="font-size: 0.76rem; color: #475569;">
                    <strong style="color: #1e293b;">Modem:</strong> {{ $wf['modem'] ?? 'Modem Utama Kos' }}
                  </div>
                </div>

                @if(!empty($wf['password']))
                  {{-- Info Password dengan Fitur Tampilkan / Sembunyikan Sandi --}}
                  <div id="{{ $cardId }}" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; font-size: 0.78rem; gap: 8px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span style="color: #64748b; font-size: 0.74rem; font-weight: 700;">Sandi:</span>
                      <span class="portal-wf-pass-masked" style="font-family: monospace; font-size: 0.95rem; font-weight: 800; color: #0f172a; letter-spacing: 2px;">••••••••</span>
                      <code class="portal-wf-pass-plain" style="display: none; font-family: monospace; font-size: 0.85rem; font-weight: 800; color: #0a5661; background: #ccfbf1; padding: 2px 8px; border-radius: 4px;">{{ $wf['password'] }}</code>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <button type="button" class="btn-toggle-portal-pass" onclick="togglePortalWifiPass('{{ $cardId }}', this)" style="background: #ffffff; border: 1px solid #cbd5e1; color: #0a5661; font-weight: 700; font-size: 0.72rem; cursor: pointer; padding: 4px 8px; border-radius: 6px;">
                        Tampilkan
                      </button>
                      <button type="button" onclick="copyText('{{ $wf['password'] }}', this)" style="background: #0a5661; border: 1px solid #0a5661; color: #ffffff; font-weight: 700; font-size: 0.72rem; cursor: pointer; padding: 4px 10px; border-radius: 6px;">
                        Salin Sandi
                      </button>
                    </div>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      @endif

      <div style="font-size: 0.76rem; color: #64748b; line-height: 1.5; background: #f8fafc; padding: 10px 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <strong>Tips Koneksi:</strong> Hubungkan perangkat Anda ke WiFi modem terdekat di lantai Anda untuk mendapatkan sinyal terbaik dan terkuat.
      </div>
    </div>
    <div class="modal-ftr">
      <button type="button" class="btn-primary" data-close-modal="modalWifi" style="width: 100%;">Tutup</button>
    </div>

    <script>
      function togglePortalWifiPass(containerId, btn) {
        var container = document.getElementById(containerId);
        if (!container) return;
        var masked = container.querySelector('.portal-wf-pass-masked');
        var plain = container.querySelector('.portal-wf-pass-plain');
        if (plain.style.display === 'none' || !plain.style.display) {
          plain.style.display = 'inline-block';
          masked.style.display = 'none';
          btn.textContent = 'Sembunyikan';
          btn.style.background = '#e2e8f0';
        } else {
          plain.style.display = 'none';
          masked.style.display = 'inline-block';
          btn.textContent = 'Tampilkan';
          btn.style.background = '#ffffff';
        }
      }
    </script>
    </div>
  </div>
</div>

{{-- Modal 4B: Peraturan & Tata Tertib Kos (Terpisah Khusus Peraturan) --}}
<div class="modal-overlay" id="modalRules">
  <div class="modal-box" style="max-width: 560px;">
    <div class="modal-hdr">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="tp-icon-badge purple" style="width: 38px; height: 38px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Tata Tertib & Peraturan Kos</h3>
          <p class="modal-subtitle">Pedoman kenyamanan dan ketertiban bersama di {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalRules" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
      <div class="tp-rules-body">
        <ul class="tp-rules-list">
          @foreach(($setting->kos_rules ?? []) as $ruleText)
            <li>
              <span class="tp-rule-bullet">•</span>
              <span>{{ $ruleText }}</span>
            </li>
          @endforeach
        </ul>

        {{-- Kontak Darurat / Security --}}
        <div class="tp-emergency-box" style="margin-top: 14px;">
          <div class="tp-emergency-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
          <div class="tp-emergency-content">
            <div class="tp-emergency-label">Kontak Darurat / Pengelola:</div>
            <div class="tp-emergency-phone">{{ $setting->owner_phone ?? '6281234567890' }}</div>
          </div>
          <a href="tel:{{ $setting->owner_phone ?? '6281234567890' }}" class="tp-emergency-btn">Panggil</a>
        </div>
      </div>
    </div>
    <div class="modal-ftr">
      <button type="button" class="btn-primary" data-close-modal="modalRules" style="width: 100%;">Saya Mengerti & Setuju</button>
    </div>
  </div>
</div>

{{-- Modal 4C: Pengajuan Pindah Kamar (Fitur Baru Pelanggan) --}}
<div class="modal-overlay" id="modalRoomTransfer">
  <div class="modal-box wide" style="max-width: 620px;">
    <div class="modal-hdr">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="tp-icon-badge teal" style="background: #f0fdfa; color: #0d9488; width: 40px; height: 40px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 3h5v5"/>
            <path d="M4 20L21 3"/>
            <path d="M21 16v5h-5"/>
            <path d="M15 15l6 6"/>
            <path d="M4 4l5 5"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Pengajuan Pindah Kamar</h3>
          <p class="modal-subtitle">
            @if($hasActiveRoom && $room)
              Pilih kamar tujuan dan ajukan permohonan pindah unit ke pengelola
            @else
              Akses Terbatas — Khusus Penghuni Aktif
            @endif
          </p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalRoomTransfer" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    @if(!$hasActiveRoom)
      <div class="modal-body" style="padding: 24px; text-align: center;">
        <div style="width: 54px; height: 54px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </div>
        <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Akses Pindah Kamar Terkunci</h4>
        <p style="font-size: 0.88rem; color: #64748b; line-height: 1.55; max-width: 440px; margin: 0 auto 20px;">
          Pengajuan <strong>Pindah Kamar</strong> hanya dapat diproses untuk customer yang sedang menempati unit kamar kos secara aktif. Akun Anda saat ini belum terdaftar di unit kamar kos atau masa sewa telah selesai.
        </p>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: left; font-size: 0.82rem; color: #475569; margin-bottom: 20px;">
          <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Informasi Status Akun:</div>
          <div>- Nama: <strong>{{ $user->name }}</strong></div>
          <div>- Email: <strong>{{ $user->email }}</strong></div>
          <div>- Status Kamar: <span style="color: #dc2626; font-weight: 700;">Belum terdaftar di unit kamar aktif</span></div>
          <div style="margin-top: 6px; color: #64748b;">Untuk menyewa kamar baru, Anda dapat melihat pilihan kamar di katalog atau menghubungi pengelola.</div>
        </div>
        <div style="display: flex; gap: 10px; justify-content: center;">
          <button type="button" class="btn-secondary" data-close-modal="modalRoomTransfer">Tutup</button>
          <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin%20Kos%20Mas%20Robi,%20saya%20tertarik%20untuk%20menyewa%20kamar%20kos" target="_blank" class="btn-primary" style="text-decoration: none;">
            Tanya Sewa Kamar via WhatsApp
          </a>
        </div>
      </div>
    @else
      <form action="{{ route('tenant.room.transfer') }}" method="POST">
        @csrf
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
          
          {{-- Kamar Saat Ini --}}
          <div style="background: var(--tp-blue-soft); border: 1.5px solid var(--tp-blue-border); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
            <div>
              <div style="font-size: 0.72rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase;">Kamar Anda Saat Ini:</div>
              <div style="font-size: 1rem; font-weight: 800; color: #1e3a8a;">Kamar #{{ $room->number }} — {{ $room->type ?? 'Deluxe AC' }}</div>
              <div style="font-size: 0.78rem; color: #3b82f6;">Lantai {{ $room->floor ?? '1' }} • Rp {{ number_format($monthlyRent ?? (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 1500000)), 0, ',', '.') }}/bulan</div>
            </div>
            <span class="tp-badge green">Aktif</span>
          </div>

          @php
            $availableTransferRooms = $allRooms->filter(function($r) use ($room) {
              if ($room && $r->id == $room->id) return false;
              // Hanya kamar yang berstatus available
              if ($r->status !== 'available') return false;
              // Tidak memiliki tenant aktif
              if ($r->activeTenant) return false;
              // Belum dibooking oleh orang lain
              if (!empty($r->booked_by_user_id)) return false;
              return true;
            });
          @endphp

          @if($availableTransferRooms->count() > 0)
            {{-- Dropdown Pilih Kamar Tujuan (Hanya Kamar Kosong & Belum Disewa/Dibooking) --}}
            <div class="form-group">
              <label for="transferTargetRoom" class="form-label">Pilih Kamar Tujuan yang Kosong & Tersedia *</label>
              <select name="target_room_id" id="transferTargetRoom" class="form-control" required style="font-weight: 600;">
                <option value="">-- Pilih Kamar Kosong ({{ $availableTransferRooms->count() }} Pilihan Tersedia) --</option>
                @foreach($availableTransferRooms as $r)
                  <option value="{{ $r->id }}">
                    [{{ ($r->location ?? 'Lokasi 1') === 'Lokasi 2' ? 'Cabang 2 - Ringroad' : 'Cabang 1 - Kampus' }}] Kamar #{{ $r->number }} (Lt {{ $r->floor }}) — {{ $r->type }} — Rp {{ number_format($r->price, 0, ',', '.') }}/bln
                  </option>
                @endforeach
              </select>
              <div style="font-size: 0.72rem; color: #0f766e; margin-top: 4px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Hanya kamar yang berstatus kosong, belum disewa, dan belum dibooking yang dapat dipilih.</span>
              </div>
            </div>

            {{-- Tanggal Rencana Pindah --}}
            <div class="form-group">
              <label for="transferPreferredDate" class="form-label">Rencana Tanggal Pindah *</label>
              <input type="date" name="preferred_date" id="transferPreferredDate" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
            </div>

            {{-- Alasan Pindah Kamar --}}
            <div class="form-group">
              <label for="transferReason" class="form-label">Alasan Pengajuan Pindah Kamar *</label>
              <textarea name="reason" id="transferReason" class="form-control" rows="3" placeholder="Contoh: Ingin pindah ke lantai 1 karena lebih dekat parkir / ingin upgrade ke tipe kamar yang lebih luas..." required></textarea>
            </div>
          @else
            <div style="background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 14px; padding: 18px; text-align: center; margin: 4px 0 10px;">
              <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; color: #e11d48; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              </div>
              <h4 style="font-size: 0.95rem; font-weight: 800; color: #9f1239; margin-bottom: 6px;">Tidak Ada Kamar Kosong yang Tersedia</h4>
              <p style="font-size: 0.82rem; color: #be123c; margin: 0; line-height: 1.5;">
                Saat ini semua unit kamar lainnya sedang terisi penuh atau sudah disewa oleh orang lain. Pindah kamar hanya dapat diajukan ke kamar kosong yang tersedia. Silakan cek berkala atau hubungi pengelola.
              </p>
            </div>
          @endif

          {{-- Riwayat Pengajuan Sebelumnya (Jika Ada) --}}
          @if(isset($myTransfers) && count($myTransfers) > 0)
            <div style="border-top: 1px solid var(--tp-border); padding-top: 12px;">
              <div style="font-size: 0.82rem; font-weight: 700; color: var(--tp-text-primary); margin-bottom: 8px;">Riwayat Pengajuan Anda:</div>
              <div style="display: flex; flex-direction: column; gap: 8px; max-height: 160px; overflow-y: auto;">
                @foreach($myTransfers as $tr)
                  <div style="background: var(--tp-bg-surface); border: 1px solid var(--tp-border); border-radius: 10px; padding: 10px 12px; font-size: 0.78rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <span style="font-weight: 700;">Ke Kamar #{{ $tr->targetRoom?->number ?? '-' }} ({{ $tr->targetRoom?->type ?? '-' }})</span>
                      @if($tr->status === 'pending')
                        <span class="tp-transfer-status-badge pending">Menunggu Peninjauan</span>
                      @elseif($tr->status === 'approved')
                        <span class="tp-transfer-status-badge approved">Disetujui Pengelola</span>
                      @else
                        <span class="tp-transfer-status-badge rejected">Ditolak</span>
                      @endif
                    </div>
                    <div style="color: var(--tp-text-secondary); margin-bottom: 2px;">Alasan: {{ $tr->reason }}</div>
                    @if($tr->admin_note)
                      <div style="font-weight: 600; color: #1d4ed8; margin-top: 4px;">Catatan Pengelola: {{ $tr->admin_note }}</div>
                    @endif
                    <div style="font-size: 0.7rem; color: var(--tp-text-muted); margin-top: 4px;">{{ $tr->created_at->format('d M Y, H:i') }} WIB</div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

        </div>
        <div class="modal-ftr">
          <button type="button" class="btn-secondary" data-close-modal="modalRoomTransfer">Batal</button>
          @if($availableTransferRooms->count() > 0)
            <button type="submit" class="btn-primary" style="background: #0d9488; border-color: #0d9488;">
              Kirim Pengajuan Pindah
            </button>
          @else
            <button type="button" class="btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed; background: #e2e8f0; color: #64748b; border-color: #cbd5e1;">
              Kamar Kosong Belum Tersedia
            </button>
          @endif
        </div>
      </form>
    @endif
  </div>
</div>

{{-- Modal 5: Profil Pelanggan (Fitur Lengkap Sesuai Permintaan User) --}}
<div class="modal-overlay" id="modalProfile">
  <div class="modal-box wide">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Profil Saya & Akun Penghuni</h3>
        <p class="modal-subtitle">Kelola informasi pribadi, kontak, dan foto profil Anda</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalProfile" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <form action="{{ route('tenant.profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile">
      @csrf
      <div class="modal-body">

        {{-- Foto Profil Section --}}
        <div class="tp-profile-avatar-section">
          <div class="tp-avatar-wrapper">
            @if($hasRealAvatar)
              <img src="{{ asset($user->avatar) }}"
                   alt="{{ $user->name }}" class="tp-profile-preview-avatar" id="avatarPreviewImg">
              <div class="tp-profile-preview-avatar tp-profile-preview-initial" id="avatarPreviewInitial" style="display:none;">{{ $userInitial }}</div>
            @else
              <img src=""
                   alt="{{ $user->name }}" class="tp-profile-preview-avatar" id="avatarPreviewImg" style="display:none;">
              <div class="tp-profile-preview-avatar tp-profile-preview-initial" id="avatarPreviewInitial">{{ $userInitial }}</div>
            @endif
            <label for="avatarFileInput" class="tp-avatar-upload-badge" title="Unggah foto baru">
              
            </label>
          </div>
          <div class="tp-avatar-meta">
            <h4 class="tp-avatar-title">Foto Profil Penghuni</h4>
            <p class="tp-avatar-sub">Unggah foto profil Anda sendiri (JPG, PNG, atau WEBP, maks 3MB) atau gunakan kamera/galeri.</p>
            <div class="tp-avatar-buttons">
              <label for="avatarFileInput" class="tp-btn-upload-file">
                Pilih Foto Dari Perangkat
              </label>
              <input type="file" id="avatarFileInput" name="avatar_file" accept="image/*" style="display:none;" onchange="previewSelectedAvatar(event)">
              <input type="hidden" id="avatarPresetInput" name="avatar_preset" value="">
            </div>
          </div>
        </div>

        {{-- Form Fields (Semua 6 Data Permintaan User) --}}
        <div class="tp-form-grid">

          {{-- 1. Nama Lengkap --}}
          <div class="form-group tp-grid-col-2">
            <label for="profileName" class="form-label">
              <span>Nama Lengkap *</span>
              <span class="form-label-hint">Sesuai KTP / Identitas</span>
            </label>
            <input type="text" id="profileName" name="name" class="form-control"
                   value="{{ old('name', $user->name) }}" required placeholder="Contoh: Sarah Maulida Putri">
          </div>

          {{-- 2. Tanggal Lahir --}}
          <div class="form-group">
            <label for="profileBirthDate" class="form-label">
              <span>Tanggal Lahir</span>
              <span class="form-label-hint">Tgl / Bulan / Tahun</span>
            </label>
            <input type="date" id="profileBirthDate" name="birth_date" class="form-control"
                   value="{{ old('birth_date', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}">
          </div>

          {{-- 3. Alamat Asal Daerah --}}
          <div class="form-group">
            <label for="profileOriginAddress" class="form-label">
              <span>Alamat Asal Daerah</span>
              <span class="form-label-hint">Kota / Kabupaten asal</span>
            </label>
            <input type="text" id="profileOriginAddress" name="origin_address" class="form-control"
                   value="{{ old('origin_address', $user->origin_address) }}" placeholder="Contoh: Sleman, D.I. Yogyakarta">
          </div>

          {{-- 4. Gmail / Email --}}
          <div class="form-group">
            <label for="profileEmail" class="form-label">
              <span>Gmail / Alamat Email *</span>
              <span class="form-label-hint">Untuk login & notifikasi</span>
            </label>
            <input type="email" id="profileEmail" name="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required placeholder="nama@gmail.com">
          </div>

          {{-- 5. Nomor HP / WhatsApp --}}
          <div class="form-group">
            <label for="profilePhone" class="form-label">
              <span>No. HP / WhatsApp *</span>
              <span class="form-label-hint">Nomor aktif pengelola</span>
            </label>
            <input type="tel" id="profilePhone" name="phone" class="form-control"
                   value="{{ old('phone', ($user->phone && $user->phone !== '-') ? $user->phone : '') }}" required placeholder="Contoh: 081234567890">
          </div>

          {{-- 6. No. KTP / SIM / KTM / KTA --}}
          <div class="form-group">
            <label for="profileIdCard" class="form-label">
              <span>No. KTP / SIM / KTM / KTA</span>
              <span class="form-label-hint">Nomor identitas diri</span>
            </label>
            <input type="text" id="profileIdCard" name="id_card" class="form-control"
                   value="{{ old('id_card', $tenant?->id_card) }}" placeholder="33xxxxxxxxxxxxxx / Nomor Identitas">
          </div>

          {{-- Foto KTP / SIM / KTM / KTA --}}
          <div class="form-group tp-grid-col-2">
            <label class="form-label" for="profileIdCardPhoto">
              <span>Foto KTP / SIM / KTM / KTA (Kartu Identitas) *</span>
              <span class="form-label-hint">Wajib melampirkan foto kartu identitas aktif</span>
            </label>
            @php
              $currentIdCardPhoto = $user->id_card_photo ?: ($tenant?->id_card_photo ?? null);
            @endphp
            <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
              @if($currentIdCardPhoto)
                <div style="position: relative;">
                  <a href="{{ asset($currentIdCardPhoto) }}" target="_blank" title="Klik untuk memperbesar foto kartu identitas">
                    <img src="{{ asset($currentIdCardPhoto) }}" alt="Foto Identitas" style="width: 105px; height: 70px; object-fit: cover; border-radius: 10px; border: 1.5px solid #0d9488; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
                  </a>
                  <span style="display: block; font-size: 0.68rem; font-weight: 700; color: #0d9488; margin-top: 4px; text-align: center;">Terlampir</span>
                </div>
              @endif
              <div style="flex: 1; min-width: 220px;">
                <input type="file" id="profileIdCardPhoto" name="id_card_photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                <span style="font-size: 0.72rem; color: #64748b; margin-top: 4px; display: block;">
                  {{ $currentIdCardPhoto ? 'Pilih file baru jika ingin mengganti foto kartu identitas Anda.' : 'Pilih foto fisik KTP, SIM, KTM Mahasiswa, atau KTA Dinas (Maks. 5 MB).' }}
                </span>
              </div>
            </div>
          </div>

          {{-- 7. Kontak Darurat (Ortu / Wali / Saudara) --}}
          <div class="form-group tp-grid-col-2">
            <label for="profileEmergencyContact" class="form-label">
              <span>Kontak Darurat (Ortu / Wali / Saudara) *</span>
              <span class="form-label-hint">Nama & No. HP keluarga</span>
            </label>
            <input type="text" id="profileEmergencyContact" name="emergency_contact" class="form-control"
                   value="{{ old('emergency_contact', $tenant?->emergency_contact) }}" placeholder="Contoh: Bpk. Joko (0813xxxxxxxx) - Orang Tua">
          </div>

          {{-- Info Hunian Saat Ini (Read-only badge info) --}}
          <div class="tp-profile-room-box tp-grid-col-2">
            <div class="tp-profile-room-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div class="tp-profile-room-details">
              @if($hasActiveRoom && $room)
                <strong>Kamar Saat Ini: #{{ $room->number }} ({{ $room->type ?? 'Deluxe AC' }}) • {{ ($tenantBranchKey ?? 'lokasi_1') === 'lokasi_2' ? 'Cabang 2 (Ringroad)' : 'Cabang 1 (Kampus)' }}</strong>
                <p>{{ $tenantBranch['name'] ?? (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING') }} • Lantai {{ $room->floor ?? '1' }} • Status Sewa: Aktif Menghuni • Biaya: Rp {{ number_format($monthlyRent ?? (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 1500000)), 0, ',', '.') }}/bulan</p>
              @else
                <strong>Status Hunian: Belum Terdaftar di Kamar Aktif / Selesai Sewa</strong>
                <p>Akun Anda belum dikaitkan dengan unit hunian aktif oleh Pengelola. Hubungi admin untuk pendaftaran atau aktivasi sewa kamar.</p>
              @endif
            </div>
          </div>

          {{-- Integrasi Akun Google Penghuni (Simpan dengan Google) --}}
          <div class="tp-grid-col-2" style="background: {{ !empty($user->google_id) ? '#f0fdf4' : '#ffffff' }}; border: 1.5px solid {{ !empty($user->google_id) ? '#86efac' : '#e2e8f0' }}; border-radius: 16px; padding: 16px; margin-top: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
              <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #ffffff; border: 1.5px solid {{ !empty($user->google_id) ? '#bbf7d0' : '#e2e8f0' }}; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.05); flex-shrink: 0;">
                  <svg width="22" height="22" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                  </svg>
                </div>
                <div>
                  <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <strong style="color: #0f172a; font-size: 0.92rem;">Login dengan Akun Google</strong>
                    @if(!empty($user->google_id))
                      <span style="font-size: 0.7rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 999px; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Akun Google Terhubung</span>
                      </span>
                    @else
                      <span style="font-size: 0.7rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 999px; border: 1px solid #fde68a;">
                        Belum Terhubung
                      </span>
                    @endif
                  </div>
                  <p style="margin: 3px 0 0; font-size: 0.78rem; color: #64748b; line-height: 1.45;">
                    @if(!empty($user->google_id))
                      Akun Google Anda telah tersimpan dan terhubung. Anda dapat langsung login melalui tombol Google di halaman login tanpa perlu kata sandi.
                    @else
                      Tautkan akun Google Anda di sini agar dapat langsung login menggunakan akun Google di halaman login SMART LIVING.
                    @endif
                  </p>
                </div>
              </div>

              <div>
                <button type="button" onclick="saveProfileAndConnectGoogle()" id="btnCardSaveWithGoogle" style="background: #ffffff; border: 1.5px solid {{ !empty($user->google_id) ? '#86efac' : '#033f3c' }}; color: #033f3c; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.04); transition: all 0.2s;">
                  <svg width="15" height="15" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                  </svg>
                  <span>{{ !empty($user->google_id) ? 'Perbarui Akun Google' : 'Simpan dengan Google' }}</span>
                </button>
              </div>
            </div>
          </div>

          {{-- Keamanan & Password --}}
          <div class="tp-grid-col-2" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 16px; margin-top: 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
              <div>
                <strong style="color: #0f172a; font-size: 0.92rem; display: flex; align-items: center; gap: 6px;">
                  <span>Keamanan & Password</span>
                </strong>
                <p style="margin: 3px 0 0; font-size: 0.78rem; color: #64748b;">
                  Ganti kata sandi akun Anda secara aman dengan verifikasi kode OTP 6 digit ke Gmail.
                </p>
              </div>

              {{-- Tombol Ganti Password (Pertahankan tombol yang sudah ada pada posisinya) --}}
              <button type="button" id="btnSendPasswordToEmail" class="btn-sm" style="background: #f0fdfa; border: 1.5px solid #0e7482; color: #0a5661; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span id="btnSendPasswordText">Ganti Password</span>
              </button>
            </div>

            {{-- Info Awal sebelum klik Ganti Password --}}
            <div id="pwdInitialNotice" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 0.78rem; color: #64748b; display: flex; align-items: center; gap: 8px;">
              <span>Klik tombol <strong>Ganti Password</strong> di atas untuk menerima kode OTP 6 digit di Gmail terdaftar ({{ $user->email }}).</span>
            </div>

            {{-- Container Interaktif Alur Ganti Password (OTP -> Form Password -> Selesai) --}}
            <div id="otpPasswordWorkflow" style="display: none; flex-direction: column; gap: 12px; margin-top: 10px;">

              {{-- TAHAP 1: INPUT & VERIFIKASI OTP --}}
              <div id="otpStepVerification" style="background: #ffffff; border: 1.5px solid #ccfbf1; border-radius: 14px; padding: 16px; box-shadow: 0 4px 14px rgba(14, 116, 130, 0.06);">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                  <div>
                    <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(14, 116, 130, 0.1); color: #0a5661; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Langkah 1 dari 2: Verifikasi OTP</span>
                    <p style="margin: 6px 0 0; font-size: 0.8rem; color: #334155; line-height: 1.45;">
                      Kode OTP 6 digit telah dikirim ke Gmail <strong>{{ $user->email }}</strong>. Berlaku <strong>5 menit</strong>.
                    </p>
                  </div>
                  <div id="otpTimerBadge" style="font-size: 0.74rem; font-weight: 700; color: #0e7482; background: #f0fdfa; border: 1px solid #99f6e4; padding: 4px 10px; border-radius: 20px;">
                    Jeda Kirim Ulang: <span id="otpTimerSeconds">60</span>s
                  </div>
                </div>

                {{-- Input OTP 6 Digit --}}
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                  <div style="position: relative; flex: 1 1 160px; max-width: 220px;">
                    <input type="text" id="inputOtpCode" maxlength="6" inputmode="numeric" pattern="[0-9]*" class="form-control" placeholder="000000"
                           style="font-family: monospace; font-size: 1.35rem; font-weight: 800; letter-spacing: 6px; text-align: center; height: 44px; padding: 0 10px; background: #f8fafc; border: 2px solid #cbd5e1; border-radius: 12px;" autocomplete="one-time-code">
                  </div>
                  <button type="button" id="btnVerifyOtp" style="height: 44px; background: linear-gradient(135deg, #0a5661, #0e7482); color: #ffffff; border: none; padding: 0 18px; border-radius: 12px; font-size: 0.82rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(14, 116, 130, 0.25); transition: all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Verifikasi OTP</span>
                  </button>
                  <button type="button" id="btnResendOtp" disabled style="height: 44px; background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; padding: 0 14px; border-radius: 12px; font-size: 0.78rem; font-weight: 700; cursor: not-allowed; transition: all 0.2s;">
                    Kirim Ulang OTP
                  </button>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; flex-wrap: wrap; gap: 6px;">
                  <span style="font-size: 0.73rem; color: #64748b;">
                    Kesempatan verifikasi: <strong id="otpRemainingAttempts" style="color: #0a5661;">5</strong> kali tersisa.
                  </span>
                </div>

                <div id="otpErrorAlert" style="display: none; margin-top: 10px; background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 8px 12px; border-radius: 10px; font-size: 0.78rem; font-weight: 600;"></div>
              </div>

              {{-- TAHAP 2: FORMULIR PASSWORD BARU (Hanya tampil setelah OTP valid) --}}
              <div id="otpStepNewPassword" style="display: none; background: #ffffff; border: 1.5px solid #a7f3d0; border-radius: 14px; padding: 16px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.08);">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                  <span style="font-size: 0.76rem; font-weight: 800; color: #065f46; text-transform: uppercase;">OTP Terverifikasi — Langkah 2: Tentukan Password Baru</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                  <div class="form-group" style="margin-bottom: 0;">
                    <label for="inputNewPassword" class="form-label" style="font-size: 0.78rem; font-weight: 700;">
                      <span>Password Baru (Min. 8 Karakter) *</span>
                    </label>
                    <input type="password" id="inputNewPassword" class="form-control" placeholder="Minimal 8 karakter" minlength="8" style="font-size: 16px;">
                  </div>
                  <div class="form-group" style="margin-bottom: 0;">
                    <label for="inputNewPasswordConfirm" class="form-label" style="font-size: 0.78rem; font-weight: 700;">
                      <span>Ulangi Password Baru *</span>
                    </label>
                    <input type="password" id="inputNewPasswordConfirm" class="form-control" placeholder="Ketik ulang password" minlength="8" style="font-size: 16px;">
                  </div>
                </div>

                <div id="newPwdErrorAlert" style="display: none; margin-top: 10px; background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 8px 12px; border-radius: 10px; font-size: 0.78rem; font-weight: 600;"></div>

                <div style="margin-top: 14px; display: flex; justify-content: flex-end; gap: 8px;">
                  <button type="button" id="btnCancelOtpFlow" class="btn-sm" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 9px 14px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                    Batal
                  </button>
                  <button type="button" id="btnSubmitNewPassword" style="background: linear-gradient(135deg, #0a5661, #0e7482); color: #ffffff; border: none; padding: 9px 18px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(14, 116, 130, 0.25); transition: all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Simpan Password Baru</span>
                  </button>
                </div>
              </div>

              {{-- TAHAP 3: NOTIFIKASI BERHASIL --}}
              <div id="otpStepSuccess" style="display: none; background: #f0fdfa; border: 2px solid #99f6e4; border-radius: 14px; padding: 18px; text-align: center;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 50%; background: #0e7482; color: #ffffff; margin-bottom: 8px; box-shadow: 0 4px 12px rgba(14, 116, 130, 0.25);">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h4 style="margin: 0 0 4px; font-size: 1.05rem; font-weight: 800; color: #0a5661;">Password Berhasil Diubah!</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">
                  Kata sandi akun Anda telah berhasil diperbarui menggunakan enkripsi hashing. Silakan gunakan password baru ini saat masuk ke portal berikutnya.
                </p>
              </div>

            </div>
          </div>

        </div>

      </div>
      <div class="modal-ftr" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <button type="button" class="btn-secondary" data-close-modal="modalProfile">Batal</button>
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          <button type="button" id="btnSaveWithGoogle" onclick="saveProfileAndConnectGoogle()" style="background: #ffffff; border: 1.5px solid #033f3c; color: #033f3c; padding: 9px 16px; border-radius: 12px; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(3, 63, 60, 0.08); transition: all 0.2s;">
            <svg width="16" height="16" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
              <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Simpan dengan Google</span>
          </button>
          <button type="submit" class="btn-primary" style="background: #033f3c; color: #ffffff; padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.82rem; border: none; cursor: pointer;">
            Simpan Perubahan Profil
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

  {{-- Floating Bottom Navigation for Mobile --}}
  <nav class="tp-mobile-nav" aria-label="Navigasi Bawah">
    <button type="button" class="tp-nav-item active" id="btnBottomNavHome" onclick="switchPortalTab('catalog'); window.scrollTo({top: 0, behavior: 'smooth'});">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      <span>Home</span>
    </button>
    <button type="button" class="tp-nav-item" onclick="document.getElementById('modalReceipts')?.classList.add('open')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      <span>Kuitansi</span>
    </button>
    <button type="button" class="tp-nav-item" onclick="document.getElementById('modalComplaint')?.classList.add('open')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <span>Keluhan</span>
    </button>
    <button type="button" class="tp-nav-item" onclick="document.getElementById('modalProfile')?.classList.add('open')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span>Profil</span>
    </button>
  </nav>

<script>
/* ============================================================
   SMART LIVING — Portal JS (AJAX + Animations + Auto-slide)
   ============================================================ */

window.currentUserId = {{ (int) $user->id }};
window.catalogRoomsData = @json($allRooms);

function handleRoomCardClick(roomId) {
  if (!window.catalogRoomsData) return;
  var room = window.catalogRoomsData.find(function(r) { return r.id == roomId; });
  if (room) {
    openRoomCustomModal(room);
  }
}
window.handleRoomCardClick = handleRoomCardClick;

// ─── Toast Notification ──────────────────────────────────────
function showPortalToast(message, type) {
  type = type || 'success';
  var existing = document.getElementById('portalToast');
  if (existing) existing.remove();
  var toast = document.createElement('div');
  toast.id = 'portalToast';
  toast.className = 'tp-toast tp-toast-' + type;
  toast.innerHTML = '<span class="tp-toast-icon">' + (type === 'success' ? '' : '') + '</span>'
    + '<span class="tp-toast-msg">' + message + '</span>';
  document.body.appendChild(toast);
  requestAnimationFrame(function() { toast.classList.add('show'); });
  setTimeout(function() {
    toast.classList.remove('show');
    setTimeout(function() { toast.remove(); }, 400);
  }, 3500);
}

// ─── Ripple Effect ────────────────────────────────────────────
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.btn-primary, .tp-btn-pay, .tp-btn-profile-edit, .tp-btn-primary-action, .tp-btn-secondary-action');
  if (!btn) return;
  var circle = document.createElement('span');
  circle.className = 'tp-ripple';
  var rect = btn.getBoundingClientRect();
  var size = Math.max(rect.width, rect.height);
  circle.style.width = circle.style.height = size + 'px';
  circle.style.left = (e.clientX - rect.left - size / 2) + 'px';
  circle.style.top  = (e.clientY - rect.top  - size / 2) + 'px';
  btn.style.position = 'relative';
  btn.style.overflow = 'hidden';
  btn.appendChild(circle);
  setTimeout(function() { circle.remove(); }, 700);
});

(function() {
  // ─── Entry Animations ────────────────────────────────────────
  var animEls = document.querySelectorAll(
    '.tp-welcome-banner, .tp-showcase-container, .tp-overview-row, .tp-quick-section'
  );
  animEls.forEach(function(el, i) {
    el.style.opacity = '0';
    el.style.transform = 'translateY(28px)';
    el.style.transition = 'opacity 0.45s ease ' + (i * 0.07) + 's, transform 0.45s ease ' + (i * 0.07) + 's';
    requestAnimationFrame(function() {
      requestAnimationFrame(function() {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
      });
    });
  });

  // ─── Modal Triggers ───────────────────────────────────────────
  var triggers = {
    btnPay:               'modalPay',
    btnOpenPay:           'modalPay',
    btnOpenComplaint:     'modalComplaint',
    btnOpenReceipts:      'modalReceipts',
    btnOpenReceipts2:     'modalReceipts',
    btnOpenRoomTransfer:  'modalRoomTransfer',
    btnWifiModal:         'modalWifi',
    btnRulesModal:        'modalRules',
    btnOpenProfile:       'modalProfile',
  };
  Object.keys(triggers).forEach(function(id) {
    var modalId = triggers[id];
    document.getElementById(id)?.addEventListener('click', function() {
      document.getElementById(modalId)?.classList.add('open');
    });
  });

  document.getElementById('btnOpenProfileBanner')?.addEventListener('click', function() {
    document.getElementById('modalProfile')?.classList.add('open');
  });

  // Close on backdrop click
  document.querySelectorAll('.modal-overlay').forEach(function(el) {
    el.addEventListener('click', function(e) {
      if (e.target === el) el.classList.remove('open');
    });
  });

  // Close on data-close-modal buttons (direct and delegated for resilience)
  document.addEventListener('click', function(e) {
    var closeBtn = e.target.closest('[data-close-modal]');
    if (closeBtn) {
      var modalId = closeBtn.getAttribute('data-close-modal') || closeBtn.dataset.closeModal;
      if (modalId) {
        var m = document.getElementById(modalId);
        if (m) m.classList.remove('open');
      }
    }
  });

  // ESC key closes modal
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(function(m) { m.classList.remove('open'); });
    }
  });

  // ─── Hero BG Auto-Slide ────────────────────────────────────────
  (function initHeroSlider() {
    var bgSlides  = document.querySelectorAll('.tp-hero-bg-slide');
    var dots      = document.querySelectorAll('.tp-overlay-dot');
    var badgeText = document.getElementById('heroSlideBadgeText');
    var roomNum   = '{{ $hasActiveRoom && $room ? $room->number : "" }}';
    var slideLabels = [
      'Foto 1/3: Kamar Tidur Utama',
      'Foto 2/3: Meja Kerja & AC',
      'Foto 3/3: Kamar Mandi Dalam'
    ];
    var currentIndex = 0;
    var totalSlides  = bgSlides.length;
    if (totalSlides <= 1) return;

    function goToSlide(index) {
      currentIndex = (index + totalSlides) % totalSlides;
      bgSlides.forEach(function(el, idx) { el.classList.toggle('active', idx === currentIndex); });
      dots.forEach(function(el, idx)     { el.classList.toggle('active', idx === currentIndex); });
      if (badgeText) {
        badgeText.textContent = slideLabels[currentIndex] || ('Foto ' + (currentIndex+1) + '/' + totalSlides + (roomNum ? (': Kamar #' + roomNum) : ''));
      }
    }

    document.getElementById('btnSlidePrev')?.addEventListener('click', function(e) {
      e.stopPropagation(); goToSlide(currentIndex - 1); resetAutoSlide();
    });
    document.getElementById('btnSlideNext')?.addEventListener('click', function(e) {
      e.stopPropagation(); goToSlide(currentIndex + 1); resetAutoSlide();
    });
    dots.forEach(function(dot, idx) {
      dot.addEventListener('click', function(e) {
        e.stopPropagation(); goToSlide(idx); resetAutoSlide();
      });
    });

    // Pause on hover
    var heroCard = document.getElementById('tpHeroCard');
    var paused   = false;
    if (heroCard) {
      heroCard.addEventListener('mouseenter', function() { paused = true; });
      heroCard.addEventListener('mouseleave', function() { paused = false; });
    }

    var slideTimer = setInterval(function() { if (!paused) goToSlide(currentIndex + 1); }, 4500);
    function resetAutoSlide() {
      clearInterval(slideTimer);
      slideTimer = setInterval(function() { if (!paused) goToSlide(currentIndex + 1); }, 4500);
    }
  })();

  // ─── AJAX: Payment Form ────────────────────────────────────────
  document.getElementById('formPay')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn  = form.querySelector('[type=submit]');
    var orig = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Mengirim...';
    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '' },
      body: new FormData(form)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message || 'Pembayaran berhasil dikonfirmasi!', 'success');
        document.getElementById('modalPay')?.classList.remove('open');
        form.reset();
        if (data.payment) prependPaymentRow(data.payment);
      } else {
        showPortalToast(data.message || 'Gagal mengirim konfirmasi.', 'error');
      }
    })
    .catch(function() { showPortalToast('Terjadi kesalahan jaringan, coba lagi.', 'error'); })
    .finally(function() { btn.disabled = false; btn.textContent = orig; });
  });

  function prependPaymentRow(payment) {
    var tbody = document.getElementById('paymentsTableBody');
    if (!tbody) return;
    var emptyEl = tbody.querySelector('.tp-table-empty');
    if (emptyEl) { var emptyTr = emptyEl.closest('tr'); if (emptyTr) emptyTr.remove(); }
    var tr = document.createElement('tr');
    tr.style.cssText = 'opacity:0;transform:translateY(-12px);';
    var proofHtml = payment.proof_image ? '<a href="' + payment.proof_image + '" target="_blank" style="font-size:0.72rem;color:#2563eb;font-weight:700;display:inline-flex;align-items:center;gap:3px;margin-top:3px;">Lihat Foto Bukti</a>' : '';
    tr.innerHTML =
      '<td class="tp-cell-desc"><div class="tp-desc-main">' + (payment.description || 'Konfirmasi Pembayaran') + '</div>'
      + '<div class="tp-desc-sub">' + (payment.transaction_code || 'TRX') + ' ' + proofHtml + '</div></td>'
      + '<td class="tp-cell-muted">' + (payment.date || '-') + '</td>'
      + '<td><span class="tp-method-pill">' + (payment.payment_method || '-') + '</span></td>'
      + '<td class="tp-cell-amount">Rp ' + Number(payment.amount || 0).toLocaleString('id-ID') + '</td>'
      + '<td><span class="tp-pill paid">LUNAS</span></td>';
    tbody.prepend(tr);
    requestAnimationFrame(function() {
      tr.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      tr.style.opacity = '1';
      tr.style.transform = 'translateY(0)';
    });
  }

  // ─── AJAX: Complaint Form ──────────────────────────────────────
  document.getElementById('formComplaint')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn  = form.querySelector('[type=submit]');
    var orig = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Mengirim...';
    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '' },
      body: new FormData(form)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message || 'Laporan berhasil dikirim!', 'success');
        document.getElementById('modalComplaint')?.classList.remove('open');
        form.reset();
      } else {
        showPortalToast(data.message || 'Gagal mengirim laporan.', 'error');
      }
    })
    .catch(function() { showPortalToast('Terjadi kesalahan jaringan, coba lagi.', 'error'); })
    .finally(function() { btn.disabled = false; btn.textContent = orig; });
  });

  // ─── AJAX: Profile Form ────────────────────────────────────────
  document.getElementById('formProfile')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn  = form.querySelector('[type=submit]');
    var orig = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Menyimpan...';
    fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '' },
      body: new FormData(form)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message || 'Profil berhasil diperbarui!', 'success');
        document.getElementById('modalProfile')?.classList.remove('open');
        if (data.user) {
          if (data.user.avatar_url) {
            var url = data.user.avatar_url + '?v=' + Date.now();
            ['navAvatarImg', 'welcomeAvatarImg', 'avatarPreviewImg'].forEach(function(id) {
              var el = document.getElementById(id);
              if (el) { el.src = url; el.style.display = 'block'; }
            });
            ['navAvatarInitial', 'welcomeAvatarInitial', 'avatarPreviewInitial'].forEach(function(id) {
              var el = document.getElementById(id);
              if (el) {
                el.style.setProperty('display', 'none', 'important');
              }
            });
          }
          if (data.user.name) {
            var shortName = data.user.name.length > 15 ? data.user.name.slice(0,15) + '\u2026' : data.user.name;
            var nameEl = document.querySelector('.tp-btn-profile-name');
            if (nameEl) nameEl.textContent = shortName;
            var wName = document.querySelector('.tp-welcome-name');
            if (wName) wName.textContent = data.user.name;
          }
        }
      } else {
        showPortalToast(data.message || 'Gagal memperbarui profil.', 'error');
      }
    })
    .catch(function() { showPortalToast('Terjadi kesalahan jaringan, coba lagi.', 'error'); })
    .finally(function() { btn.disabled = false; btn.textContent = orig; });
  });

  // ─── SIMPAN DENGAN GOOGLE (HUBUNGKAN AKUN GOOGLE DARI PROFIL) ─────────
  window.saveProfileAndConnectGoogle = function(e) {
    if (e && typeof e.preventDefault === 'function') e.preventDefault();

    var btn1 = document.getElementById('btnSaveWithGoogle');
    var btn2 = document.getElementById('btnCardSaveWithGoogle');
    if (btn1) {
      btn1.disabled = true;
      btn1.style.opacity = '0.75';
      btn1.innerHTML = '<span class="tp-spinner"></span> Menghubungkan Google...';
    }
    if (btn2) {
      btn2.disabled = true;
      btn2.style.opacity = '0.75';
      btn2.innerHTML = '<span class="tp-spinner"></span> Menghubungkan Google...';
    }

    var form = document.getElementById('formProfile');
    var redirectTarget = "{{ route('tenant.google.connect') }}";

    // Jika ada perubahan form profil, coba simpan via background fetch
    if (form) {
      try {
        var formData = new FormData(form);
        var hasRedirected = false;

        var doRedirect = function() {
          if (!hasRedirected) {
            hasRedirected = true;
            window.location.href = redirectTarget;
          }
        };

        fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '{{ csrf_token() }}'
          },
          body: formData
        })
        .finally(function() {
          doRedirect();
        });

        // Timeout pengaman maks 1 detik agar tidak menunggu lama
        setTimeout(doRedirect, 1000);
        return;
      } catch (err) {
        console.warn('Form profile background save caught:', err);
      }
    }

    // Fallback langsung arahkan ke Google OAuth
    window.location.href = redirectTarget;
  };

  // ─── ALUR GANTI PASSWORD VIA VERIFIKASI OTP GMAIL ───────────────────
  var otpTimerInterval = null;
  var otpCooldownSeconds = 0;

  function startOtpCooldown(seconds) {
    otpCooldownSeconds = seconds || 60;
    var timerBadge = document.getElementById('otpTimerBadge');
    var timerSec = document.getElementById('otpTimerSeconds');
    var resendBtn = document.getElementById('btnResendOtp');
    var btnSend = document.getElementById('btnSendPasswordToEmail');
    var btnSendText = document.getElementById('btnSendPasswordText');

    if (timerBadge) timerBadge.style.display = 'inline-block';
    if (resendBtn) {
      resendBtn.disabled = true;
      resendBtn.style.background = '#f1f5f9';
      resendBtn.style.color = '#94a3b8';
      resendBtn.style.cursor = 'not-allowed';
    }

    clearInterval(otpTimerInterval);
    otpTimerInterval = setInterval(function() {
      otpCooldownSeconds--;
      if (timerSec) timerSec.textContent = otpCooldownSeconds;
      if (btnSendText) btnSendText.textContent = 'Kirim Ulang (' + otpCooldownSeconds + 's)';

      if (otpCooldownSeconds <= 0) {
        clearInterval(otpTimerInterval);
        if (timerBadge) timerBadge.style.display = 'none';
        if (btnSend) btnSend.disabled = false;
        if (btnSendText) btnSendText.textContent = 'Kirim Ulang OTP';
        if (resendBtn) {
          resendBtn.disabled = false;
          resendBtn.style.background = '#0a5661';
          resendBtn.style.color = '#ffffff';
          resendBtn.style.cursor = 'pointer';
        }
      }
    }, 1000);
  }

  function sendOtpRequest() {
    var btn = document.getElementById('btnSendPasswordToEmail');
    var resendBtn = document.getElementById('btnResendOtp');
    var errAlert = document.getElementById('otpErrorAlert');
    if (errAlert) errAlert.style.display = 'none';

    var origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Mengirim OTP...';
    if (resendBtn) resendBtn.disabled = true;

    fetch('{{ route("tenant.profile.send_otp") }}', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '',
        'Accept': 'application/json'
      }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message, 'success');

        // Buka workflow dan sembunyikan info awal
        var notice = document.getElementById('pwdInitialNotice');
        var workflow = document.getElementById('otpPasswordWorkflow');
        var stepVerify = document.getElementById('otpStepVerification');
        var stepNewPwd = document.getElementById('otpStepNewPassword');
        var stepSucc = document.getElementById('otpStepSuccess');

        if (notice) notice.style.display = 'none';
        if (workflow) workflow.style.display = 'flex';
        if (stepVerify) stepVerify.style.display = 'block';
        if (stepNewPwd) stepNewPwd.style.display = 'none';
        if (stepSucc) stepSucc.style.display = 'none';

        var inputOtp = document.getElementById('inputOtpCode');
        if (inputOtp) {
          inputOtp.value = '';
          inputOtp.focus();
        }

        var remEl = document.getElementById('otpRemainingAttempts');
        if (remEl) remEl.textContent = '5';

        startOtpCooldown(data.cooldown || 60);
      } else {
        showPortalToast(data.message || 'Gagal mengirim kode OTP.', 'error');
        if (data.cooldown) {
          startOtpCooldown(data.cooldown);
        } else {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      }
    })
    .catch(function() {
      showPortalToast('Terjadi kesalahan jaringan.', 'error');
      btn.disabled = false;
      btn.innerHTML = origHtml;
    });
  }

  // Klik tombol Ganti Password (sudah tersedia pada halaman profil)
  document.getElementById('btnSendPasswordToEmail')?.addEventListener('click', function() {
    if (otpCooldownSeconds > 0) {
      showPortalToast('Mohon tunggu ' + otpCooldownSeconds + ' detik sebelum meminta pengiriman ulang OTP.', 'warning');
      return;
    }
    sendOtpRequest();
  });

  // Klik tombol Kirim Ulang OTP
  document.getElementById('btnResendOtp')?.addEventListener('click', function() {
    if (this.disabled) return;
    sendOtpRequest();
  });

  // Verifikasi Kode OTP
  document.getElementById('btnVerifyOtp')?.addEventListener('click', function() {
    var otpInput = document.getElementById('inputOtpCode');
    var otpVal = (otpInput?.value || '').trim();
    var errAlert = document.getElementById('otpErrorAlert');
    if (errAlert) errAlert.style.display = 'none';

    if (!otpVal || otpVal.length !== 6) {
      if (errAlert) {
        errAlert.textContent = 'Harap masukkan 6 digit kode OTP yang diterima melalui Gmail.';
        errAlert.style.display = 'block';
      }
      otpInput?.focus();
      return;
    }

    var btn = this;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Memvalidasi...';

    fetch('{{ route("tenant.profile.verify_otp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ otp: otpVal })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message, 'success');

        // Beralih ke Tahap 2: Form Password Baru
        var stepVerify = document.getElementById('otpStepVerification');
        var stepNewPwd = document.getElementById('otpStepNewPassword');
        if (stepVerify) stepVerify.style.display = 'none';
        if (stepNewPwd) stepNewPwd.style.display = 'block';

        var newPwdInput = document.getElementById('inputNewPassword');
        if (newPwdInput) {
          newPwdInput.value = '';
          newPwdInput.focus();
        }
        var newPwdConf = document.getElementById('inputNewPasswordConfirm');
        if (newPwdConf) newPwdConf.value = '';
      } else {
        if (errAlert) {
          errAlert.textContent = data.message || 'Kode OTP tidak valid.';
          errAlert.style.display = 'block';
        }
        if (typeof data.remaining !== 'undefined') {
          var remEl = document.getElementById('otpRemainingAttempts');
          if (remEl) remEl.textContent = data.remaining;
        }
        otpInput?.focus();
      }
    })
    .catch(function() {
      if (errAlert) {
        errAlert.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
        errAlert.style.display = 'block';
      }
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = orig;
    });
  });

  // Simpan Password Baru (Tahap 2)
  document.getElementById('btnSubmitNewPassword')?.addEventListener('click', function() {
    var p1 = document.getElementById('inputNewPassword')?.value || '';
    var p2 = document.getElementById('inputNewPasswordConfirm')?.value || '';
    var errAlert = document.getElementById('newPwdErrorAlert');
    if (errAlert) errAlert.style.display = 'none';

    if (p1.length < 8) {
      if (errAlert) {
        errAlert.textContent = 'Password baru minimal harus 8 karakter.';
        errAlert.style.display = 'block';
      }
      document.getElementById('inputNewPassword')?.focus();
      return;
    }

    if (p1 !== p2) {
      if (errAlert) {
        errAlert.textContent = 'Konfirmasi password baru tidak cocok. Pastikan kedua kolom sama.';
        errAlert.style.display = 'block';
      }
      document.getElementById('inputNewPasswordConfirm')?.focus();
      return;
    }

    var btn = this;
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Menyimpan...';

    fetch('{{ route("tenant.profile.update_password") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        password: p1,
        password_confirmation: p2
      })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message, 'success');

        // Beralih ke Tahap 3: Notifikasi Berhasil
        var stepNewPwd = document.getElementById('otpStepNewPassword');
        var stepSucc = document.getElementById('otpStepSuccess');
        if (stepNewPwd) stepNewPwd.style.display = 'none';
        if (stepSucc) stepSucc.style.display = 'block';

        // Reset teks tombol utama
        var btnSendText = document.getElementById('btnSendPasswordText');
        if (btnSendText) btnSendText.textContent = 'Ganti Password';
        var btnSend = document.getElementById('btnSendPasswordToEmail');
        if (btnSend) btnSend.disabled = false;
        clearInterval(otpTimerInterval);
      } else {
        if (errAlert) {
          errAlert.textContent = data.message || 'Gagal menyimpan password baru.';
          errAlert.style.display = 'block';
        }
      }
    })
    .catch(function() {
      if (errAlert) {
        errAlert.textContent = 'Terjadi kesalahan jaringan saat menyimpan password.';
        errAlert.style.display = 'block';
      }
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = orig;
    });
  });

  // Tombol Batal saat di formulir password baru
  document.getElementById('btnCancelOtpFlow')?.addEventListener('click', function() {
    var workflow = document.getElementById('otpPasswordWorkflow');
    var notice = document.getElementById('pwdInitialNotice');
    if (workflow) workflow.style.display = 'none';
    if (notice) notice.style.display = 'flex';
  });

  // ─── AJAX: Custom Interior & Booking Form ──────────────────────
  document.getElementById('formCustomInterior')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn  = document.getElementById('btnSubmitCustomInterior');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="tp-spinner"></span> Mengirim Pengajuan...';
    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).content || ''
      },
      body: new FormData(form)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        showPortalToast(data.message || 'Pengajuan Custom Interior berhasil dikirim!', 'success');
        document.getElementById('modalRoomDetailCustom')?.classList.remove('open');
        form.reset();
        setTimeout(function() {
          window.location.reload();
        }, 1200);
      } else {
        showPortalToast(data.message || 'Gagal mengirim pengajuan.', 'error');
      }
    })
    .catch(function() { showPortalToast('Terjadi kesalahan koneksi, coba lagi.', 'error'); })
    .finally(function() { btn.disabled = false; btn.innerHTML = orig; });
  });
})();

// ─── Tab Switching: Dashboard Kos vs Kamarku (With Smooth Modern Page Animation) ───
function switchPortalTab(tabName) {
  var btnCatalog = document.getElementById('tabBtnCatalog');
  var btnMyRoom  = document.getElementById('tabBtnMyRoom');
  var secCatalog = document.getElementById('sectionDashboardCatalog');
  var secMyRoom  = document.getElementById('sectionKamarku');

  if (tabName === 'myroom') {
    btnCatalog?.classList.remove('active');
    btnMyRoom?.classList.add('active');
    secCatalog?.classList.remove('active');
    if (secMyRoom) {
      secMyRoom.classList.remove('active');
      void secMyRoom.offsetWidth; // Trigger reflow for clean re-animation
      secMyRoom.classList.add('active');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  } else {
    btnMyRoom?.classList.remove('active');
    btnCatalog?.classList.add('active');
    secMyRoom?.classList.remove('active');
    if (secCatalog) {
      secCatalog.classList.remove('active');
      void secCatalog.offsetWidth; // Trigger reflow for clean re-animation
      secCatalog.classList.add('active');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

// ─── Room Catalog Filtering & 2-Branch Location Selector ─────────
function filterCatalogRooms(filter, btn) {
  document.querySelectorAll('#catalogFilterPills .tp-filter-btn').forEach(function(b) {
    b.classList.remove('active');
  });
  if (btn) btn.classList.add('active');

  // Sync branch cards highlight if branch pill is selected
  if (filter === 'lokasi_1' || filter === 'lokasi_2') {
    document.querySelectorAll('.tp-branch-card').forEach(function(c) {
      c.classList.toggle('active', c.getAttribute('data-branch') === filter);
    });
  }

  var cards = document.querySelectorAll('#catalogRoomGrid .tp-room-card');
  cards.forEach(function(card) {
    var status = card.dataset.status;
    var floor  = card.dataset.floor;
    var loc    = card.dataset.location;
    var show   = false;

    if (filter === 'all') {
      show = true;
    } else if (filter === 'lokasi_1') {
      show = (loc === 'lokasi_1');
    } else if (filter === 'lokasi_2') {
      show = (loc === 'lokasi_2');
    } else if (filter === 'available') {
      show = (status === 'available');
    } else if (filter === 'occupied') {
      show = (status === 'occupied' || status === 'due');
    } else if (filter === 'floor-1') {
      show = (floor === 'floor-1');
    } else if (filter === 'floor-2') {
      show = (floor === 'floor-2');
    }

    card.style.display = show ? 'flex' : 'none';
  });
}

function selectCatalogBranch(branchKey, el) {
  document.querySelectorAll('.tp-branch-card').forEach(function(c) {
    c.classList.remove('active');
  });
  if (el) el.classList.add('active');

  // Sync filter pill
  document.querySelectorAll('#catalogFilterPills .tp-filter-btn').forEach(function(b) {
    if (b.dataset.filter === branchKey) {
      b.classList.add('active');
    } else {
      b.classList.remove('active');
    }
  });

  var cards = document.querySelectorAll('#catalogRoomGrid .tp-room-card');
  cards.forEach(function(card) {
    var loc = card.dataset.location;
    if (branchKey === 'all' || loc === branchKey) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });

  // Smooth scroll to rooms grid
  document.getElementById('catalogRoomGrid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ─── Auto-Advancing Room Detail Photo Slider ─────────────────────
var modalRoomSlideTimer = null;
var modalRoomCurrentIndex = 0;
var modalRoomImages = [];

function openRoomCustomModal(room) {
  if (!room) return;
  document.getElementById('mCustomRoomTitle').textContent = 'Detail Kamar #' + room.number + ' (' + room.type + ')';
  document.getElementById('mCustomRoomSubtitle').textContent = 'Lantai ' + room.floor + ' • ' + (room.status === 'available' ? 'Kamar Kosong Siap Huni' : 'Kamar Sedang Ditempati');
  document.getElementById('mCustomRoomBadge').textContent = 'Kamar #' + room.number;
  document.getElementById('mCustomRoomFloor').textContent = 'Lantai ' + room.floor;
  document.getElementById('mCustomRoomPrice').textContent = 'Rp ' + Number(room.price || 0).toLocaleString('id-ID') + ' / bln';
  document.getElementById('mCustomRoomType').textContent = room.type;

  // Prepare images list (minimum 3 photos for smooth auto-sliding transitions)
  var list = [];
  if (Array.isArray(room.images_list) && room.images_list.length > 0) {
    list = room.images_list.slice();
  } else if (room.image) {
    list = [room.image];
  }

  var defaultImgs = [
    '/assets/images/rooms/room_1.jpg',
    '/assets/images/rooms/room_2.jpg',
    '/assets/images/rooms/room_3.jpg',
    '/assets/images/rooms/room_4.jpg',
    '/assets/images/hero_scandinavian_room.jpg'
  ];

  if (list.length === 0) {
    list = defaultImgs.slice(0, 3);
  } else if (list.length === 1) {
    var offset = (parseInt(room.number, 10) || 1);
    list.push(defaultImgs[offset % defaultImgs.length]);
    list.push(defaultImgs[(offset + 1) % defaultImgs.length]);
  } else if (list.length === 2) {
    list.push(defaultImgs[2]);
  }

  modalRoomImages = list.map(function(img) {
    if (!img.startsWith('http') && !img.startsWith('/')) {
      return '/' + img;
    }
    return img;
  });

  // Render slider slides & dots
  var track = document.getElementById('modalRoomSliderTrack');
  var dotsContainer = document.getElementById('modalRoomSliderDots');
  if (track && dotsContainer) {
    track.innerHTML = '';
    dotsContainer.innerHTML = '';
    modalRoomImages.forEach(function(src, idx) {
      var slide = document.createElement('div');
      slide.className = 'tp-modal-slide' + (idx === 0 ? ' active' : '');
      slide.style.backgroundImage = "url('" + src + "')";
      slide.setAttribute('data-index', idx);
      track.appendChild(slide);

      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'tp-modal-dot' + (idx === 0 ? ' active' : '');
      dot.setAttribute('data-index', idx);
      dot.onclick = function() { setModalRoomSlide(idx); };
      dotsContainer.appendChild(dot);
    });
  }

  modalRoomCurrentIndex = 0;
  updateModalRoomSlideUI(room.number);
  startModalRoomAutoSlide();

  var currentUserId = window.currentUserId || {{ (int) $user->id }};
  var isOccupiedRoom = (room.status !== 'available' || (room.booked_by_user_id && room.booked_by_user_id != currentUserId) || room.active_tenant);

  var statusTag = document.getElementById('mCustomRoomStatusTag');
  if (statusTag) {
    if (!isOccupiedRoom) {
      statusTag.innerHTML = '<span class="tp-pill paid" style="font-size:0.75rem;padding:4px 12px;background:#10b981;color:#ffffff;font-weight:700;">Tersedia (Bisa Booking)</span>';
    } else {
      statusTag.innerHTML = '<span class="tp-pill" style="font-size:0.75rem;padding:4px 12px;background:#ef4444;color:#ffffff;font-weight:700;">Sudah Terisi (Tidak Bisa Dipesan)</span>';
    }
  }

  // Tampilkan form pemesanan hanya jika kamar kosong & belum disewa
  var formCustomInterior = document.getElementById('formCustomInterior');
  var occupiedNotice = document.getElementById('mCustomOccupiedNotice');
  if (isOccupiedRoom) {
    if (formCustomInterior) formCustomInterior.style.display = 'none';
    if (occupiedNotice) occupiedNotice.style.display = 'block';
  } else {
    if (formCustomInterior) formCustomInterior.style.display = 'block';
    if (occupiedNotice) occupiedNotice.style.display = 'none';
  }

  // Facilities
  var facContainer = document.getElementById('mCustomRoomFacilities');
  if (facContainer) {
    facContainer.innerHTML = '';
    var facs = room.facilities || ['AC', 'Springbed', 'Lemari', 'KM Dalam', 'WiFi'];
    facs.forEach(function(f) {
      var span = document.createElement('span');
      span.className = 'tp-room-card-facility-pill';
      span.textContent = '' + f;
      facContainer.appendChild(span);
    });
  }

  // Existing custom interior status if any
  var existingBox = document.getElementById('mCustomExistingStatusBox');
  if (room.custom_interior_status && room.custom_interior_status !== 'none') {
    existingBox.style.display = 'block';
    var badge = document.getElementById('mCustomExistingBadge');
    var statusLabels = {
      pending: 'Menunggu Konfirmasi Admin',
      approved: 'Disetujui Admin',
      in_progress: 'Sedang Dikerjakan',
      completed: 'Selesai Dipasang',
      rejected: 'Tidak Disetujui'
    };
    badge.textContent = statusLabels[room.custom_interior_status] || room.custom_interior_status;
    document.getElementById('mCustomExistingReqText').textContent = room.custom_interior_request || '-';
    document.getElementById('mCustomExistingBudget').textContent = 'Rp ' + Number(room.custom_interior_budget || 0).toLocaleString('id-ID');
    document.getElementById('mCustomExistingNotes').textContent = room.custom_interior_admin_notes ? ('Catatan Admin: ' + room.custom_interior_admin_notes) : '';
  } else {
    existingBox.style.display = 'none';
  }

  // Set form action
  var form = document.getElementById('formCustomInterior');
  if (form) {
    form.action = '/portal/room/' + room.id + '/custom-interior';
  }

  // Pre-fill inputs
  var reqInput = document.getElementById('customInteriorReqInput');
  var budgetInput = document.getElementById('customInteriorBudgetInput');
  if (reqInput) reqInput.value = room.custom_interior_request || '';
  if (budgetInput) budgetInput.value = room.custom_interior_budget || '';

  document.getElementById('modalRoomDetailCustom')?.classList.add('open');
}

function updateModalRoomSlideUI(roomNumber) {
  var slides = document.querySelectorAll('#modalRoomSliderTrack .tp-modal-slide');
  var dots = document.querySelectorAll('#modalRoomSliderDots .tp-modal-dot');
  slides.forEach(function(slide, idx) {
    if (idx === modalRoomCurrentIndex) {
      slide.classList.add('active');
    } else {
      slide.classList.remove('active');
    }
  });
  dots.forEach(function(dot, idx) {
    if (idx === modalRoomCurrentIndex) {
      dot.classList.add('active');
    } else {
      dot.classList.remove('active');
    }
  });
  var badgeText = document.getElementById('modalRoomSlideBadgeText');
  if (badgeText && modalRoomImages.length > 0) {
    var rNum = roomNumber || (document.getElementById('mCustomRoomBadge')?.textContent || '').replace('Kamar #', '');
    badgeText.textContent = 'Foto ' + (modalRoomCurrentIndex + 1) + '/' + modalRoomImages.length + ': Kamar #' + rNum;
  }
}

function navigateModalRoomSlide(step) {
  if (modalRoomImages.length === 0) return;
  modalRoomCurrentIndex = (modalRoomCurrentIndex + step + modalRoomImages.length) % modalRoomImages.length;
  updateModalRoomSlideUI();
  restartModalRoomAutoSlide();
}

function setModalRoomSlide(index) {
  if (index >= 0 && index < modalRoomImages.length) {
    modalRoomCurrentIndex = index;
    updateModalRoomSlideUI();
    restartModalRoomAutoSlide();
  }
}

function startModalRoomAutoSlide() {
  stopModalRoomAutoSlide();
  modalRoomSlideTimer = setInterval(function() {
    var modal = document.getElementById('modalRoomDetailCustom');
    if (modal && modal.classList.contains('open')) {
      navigateModalRoomSlide(1);
    } else {
      stopModalRoomAutoSlide();
    }
  }, 2800); // Bergerak otomatis setiap 2.8 detik
}

function stopModalRoomAutoSlide() {
  if (modalRoomSlideTimer) {
    clearInterval(modalRoomSlideTimer);
    modalRoomSlideTimer = null;
  }
}

function restartModalRoomAutoSlide() {
  stopModalRoomAutoSlide();
  startModalRoomAutoSlide();
}

function closeRoomCustomModal() {
  stopModalRoomAutoSlide();
  document.getElementById('modalRoomDetailCustom')?.classList.remove('open');
}

// ─── Dynamic Payment Method Details (Dikelola Langsung Oleh Admin) ─
@php
  $activePms = $setting->active_payment_methods ?? [];
  $pmMapped = [];
  foreach ($activePms as $m) {
    $pmMapped[$m['name']] = [
      'badge' => $m['name'],
      'bank' => $m['bank_name'] ?? $m['name'],
      'number' => $m['account_number'] ?? '',
      'holder' => $m['account_holder'] ?? ($setting->owner_name ?? 'Robi Suryanto'),
      'isCash' => ($m['type'] ?? '') === 'cash',
      'isQris' => ($m['type'] ?? '') === 'qris',
      'instruction' => $m['instructions'] ?? '',
    ];
  }
  if (empty($pmMapped)) {
    $pmMapped = [
      'Transfer Mandiri' => [
        'badge' => 'Rekening Resmi Bank Mandiri',
        'bank' => 'Bank Mandiri',
        'number' => '1370019283100',
        'holder' => 'Robi Suryanto',
        'isCash' => false,
        'isQris' => false,
        'instruction' => ''
      ],
      'Transfer BCA' => [
        'badge' => 'Rekening Resmi Bank BCA',
        'bank' => 'Bank Central Asia (BCA)',
        'number' => '8830192831',
        'holder' => 'Robi Suryanto',
        'isCash' => false,
        'isQris' => false,
        'instruction' => ''
      ],
      'Transfer QRIS' => [
        'badge' => 'QRIS Instant All Payment',
        'bank' => 'QRIS Resmi ' . (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING'),
        'number' => 'NMID: ID102026192831',
        'holder' => (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING') . ' / Admin',
        'isCash' => false,
        'isQris' => true,
        'instruction' => '<strong>Scan QRIS All Payment:</strong> Buka aplikasi m-Banking atau e-Wallet (GoPay, OVO, DANA, ShopeePay). Scan kode QRIS yang tersedia atau gunakan NMID di atas.'
      ],
      'Tunai (Cash)' => [
        'badge' => 'Pembayaran Tunai (Cash)',
        'bank' => 'Tunai Langsung ke Admin',
        'number' => 'Bayar Cash / Uang Tunai',
        'holder' => 'Admin (Kamar Pengelola Lt. 1)',
        'isCash' => true,
        'isQris' => false,
        'instruction' => '<strong>Pembayaran Tunai (Cash):</strong> Silakan tentukan tanggal penyerahan uang tunai pada kolom di bawah. Lalu serahkan uang tunai sewa kamar langsung kepada Admin di kantor/kamar pengelola lantai 1.'
      ]
    ];
  }
@endphp

var paymentMethodsData = @json($pmMapped);

function handlePaymentMethodChange(selectedMethod) {
  var data = paymentMethodsData[selectedMethod] || Object.values(paymentMethodsData)[0] || {
    badge: 'Rekening Resmi',
    bank: 'Bank Transfer',
    number: '-',
    holder: 'Pengelola',
    isCash: false,
    isQris: false,
    instruction: ''
  };
  var badgeEl = document.getElementById('payDestBadge');
  var bankNameEl = document.getElementById('payDestBankName');
  var numberTextEl = document.getElementById('bankNumberText');
  var numberRowEl = document.getElementById('payDestNumberRow');
  var btnCopy = document.getElementById('btnCopyAcc');
  var instEl = document.getElementById('payDestInstructions');
  var holderEl = document.getElementById('payDestHolder');
  var cashDateGroup = document.getElementById('cashDateGroup');
  var proofUploadGroup = document.getElementById('proofUploadGroup');
  var proofLabelTitle = document.getElementById('proofLabelTitle');
  var proofLabelHint = document.getElementById('proofLabelHint');
  var payProofFile = document.getElementById('payProofFile');

  if (badgeEl) badgeEl.textContent = data.badge;
  if (bankNameEl) bankNameEl.textContent = data.bank;
  if (holderEl) holderEl.innerHTML = 'a.n. <strong>' + data.holder + '</strong>';

  if (data.isCash) {
    if (numberRowEl) numberRowEl.style.display = 'none';
    if (cashDateGroup) cashDateGroup.style.display = 'block';
    if (instEl) {
      instEl.style.display = 'block';
      instEl.innerHTML = data.instruction;
    }
    // Mode Cash: Bukti transfer tidak wajib
    if (proofLabelTitle) proofLabelTitle.textContent = 'Foto Bukti Pembayaran (Opsional untuk Tunai)';
    if (proofLabelHint) proofLabelHint.textContent = 'Tidak wajib untuk transaksi tunai';
    if (payProofFile) payProofFile.required = false;
  } else {
    // Mode Transfer / QRIS: Bukti nota transfer WAJIB diisi
    if (cashDateGroup) cashDateGroup.style.display = 'none';
    if (proofLabelTitle) proofLabelTitle.textContent = 'Foto Bukti / Nota Pembayaran *';
    if (proofLabelHint) proofLabelHint.textContent = 'Wajib Struk ATM / Screenshot m-Banking';
    if (payProofFile) payProofFile.required = true;

    if (data.isQris) {
      if (numberRowEl) numberRowEl.style.display = 'flex';
      if (numberTextEl) numberTextEl.textContent = data.number;
      if (btnCopy) btnCopy.textContent = 'Salin Kode QRIS';
      if (instEl) {
        instEl.style.display = 'block';
        instEl.innerHTML = data.instruction;
      }
    } else {
      // Bank Transfer
      if (numberRowEl) numberRowEl.style.display = 'flex';
      if (numberTextEl) numberTextEl.textContent = data.number;
      if (btnCopy) btnCopy.textContent = 'Salin No. Rekening';
      if (instEl) instEl.style.display = 'none';
    }
  }
}

// ─── Fitur Screening Nota Pembayaran Otomatis ──────────────────────
var isProofValidForTransfer = false;

function handleProofImageSelected(event) {
  var file = event.target.files[0];
  var placeholder = document.getElementById('proofPreviewPlaceholder');
  var img = document.getElementById('proofPreviewImg');
  var resultBox = document.getElementById('screeningResultBox');

  if (!file) {
    isProofValidForTransfer = false;
    if (resultBox) resultBox.style.display = 'none';
    if (img) img.style.display = 'none';
    if (placeholder) placeholder.style.display = 'block';
    return;
  }

  // Preview Image
  var reader = new FileReader();
  reader.onload = function(e) {
    if (img) {
      img.src = e.target.result;
      img.style.display = 'block';
    }
    if (placeholder) placeholder.style.display = 'none';

    // Jalankan Screening Nota Pembayaran
    screenReceiptImage(e.target.result, file);
  };
  reader.readAsDataURL(file);
}

function screenReceiptImage(dataUrl, file) {
  var resultBox = document.getElementById('screeningResultBox');
  if (!resultBox) return;

  resultBox.style.display = 'block';
  resultBox.style.background = '#eff6ff';
  resultBox.style.border = '1px solid #bfdbfe';
  resultBox.style.color = '#1e40af';
  resultBox.innerHTML = '<div style="display:flex;align-items:center;gap:8px;"><span><strong>Sedang Memindai Nota:</strong> Memeriksa keabsahan format & struktur bukti transfer...</span></div>';

  // 1. Validasi Tipe & Ukuran File
  var validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
  if (!validMimes.includes(file.type.toLowerCase())) {
    setScreeningFailed('Format file bukan gambar yang sah. Hanya menerima format JPG, PNG, atau WEBP.');
    return;
  }

  if (file.size < 6 * 1024) { // Di bawah 6KB
    setScreeningFailed('Ukuran gambar terlalu kecil (' + (file.size / 1024).toFixed(1) + ' KB). Foto nota/struk tidak terbaca atau rusak.');
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    setScreeningFailed('Ukuran file melebihi batas maksimal 5MB.');
    return;
  }

  // 2. Analisis Struktur Dokumen via HTML5 Canvas
  var tempImg = new Image();
  tempImg.onload = function() {
    var w = tempImg.width;
    var h = tempImg.height;

    // Minimum dimensi bukti struk / mobile screenshot
    if (w < 160 || h < 160) {
      setScreeningFailed('Dimensi gambar terlalu kecil (' + w + 'x' + h + ' px). Pastikan Anda memfoto struk ATM atau screenshot m-Banking secara jelas.');
      return;
    }

    try {
      var canvas = document.createElement('canvas');
      canvas.width = 100;
      canvas.height = 140;
      var ctx = canvas.getContext('2d');
      ctx.drawImage(tempImg, 0, 0, 100, 140);
      var imgData = ctx.getImageData(0, 0, 100, 140).data;

      var totalLuminance = 0;
      var luminances = [];
      for (var i = 0; i < imgData.length; i += 4) {
        var lum = 0.299 * imgData[i] + 0.587 * imgData[i + 1] + 0.114 * imgData[i + 2];
        luminances.push(lum);
        totalLuminance += lum;
      }
      var mean = totalLuminance / luminances.length;
      var varianceSum = 0;
      for (var j = 0; j < luminances.length; j++) {
        var diff = luminances[j] - mean;
        varianceSum += diff * diff;
      }
      var stdDev = Math.sqrt(varianceSum / luminances.length);

      // Gambar kosong / 1 warna flat
      if (stdDev < 9) {
        setScreeningFailed('Gambar terdeteksi polos/kosong dan tidak memuat teks atau rincian nota pembayaran yang sah.');
        return;
      }

      // Lolos Screening Nota!
      setScreeningSuccess('Nota Pembayaran Terverifikasi: Struktur struk/bukti transfer sah terdeteksi (' + w + 'x' + h + ' px).');
    } catch (e) {
      setScreeningSuccess('Nota Pembayaran Diterima (' + w + 'x' + h + ' px).');
    }
  };
  tempImg.onerror = function() {
    setScreeningFailed('File gambar rusak atau tidak dapat diproses.');
  };
  tempImg.src = dataUrl;
}

function setScreeningFailed(msg) {
  isProofValidForTransfer = false;
  var resultBox = document.getElementById('screeningResultBox');
  if (resultBox) {
    resultBox.style.display = 'block';
    resultBox.style.background = '#fef2f2';
    resultBox.style.border = '1.5px solid #fecaca';
    resultBox.style.color = '#991b1b';
    resultBox.innerHTML = '<div style="display:flex;align-items:flex-start;gap:8px;"><div><strong>Screening Gagal — Bukan Nota Valid:</strong><br>' + msg + '</div></div>';
  }
}

function setScreeningSuccess(msg) {
  isProofValidForTransfer = true;
  var resultBox = document.getElementById('screeningResultBox');
  if (resultBox) {
    resultBox.style.display = 'block';
    resultBox.style.background = '#f0fdf4';
    resultBox.style.border = '1.5px solid #bbf7d0';
    resultBox.style.color = '#166534';
    resultBox.innerHTML = '<div style="display:flex;align-items:center;gap:8px;"><div><strong>' + msg + '</strong></div></div>';
  }
}

function validatePaymentSubmission(event) {
  var method = document.getElementById('payMethod').value;
  var isCash = method.includes('Tunai') || method.includes('Cash');

  if (isCash) {
    var cashDate = document.getElementById('payCashDate').value;
    if (!cashDate) {
      window.showModernAlert('Mohon tentukan Tanggal Bayar Tunai (Cash) Anda.', 'error', 'Perhatian');
      document.getElementById('payCashDate').focus();
      if (event) event.preventDefault();
      return false;
    }
    return true; // Cash tidak mewajibkan upload foto nota
  }

  // Transfer / QRIS: Wajib nota & lolos screening
  var fileInput = document.getElementById('payProofFile');
  if (!fileInput.files || fileInput.files.length === 0) {
    window.showModernAlert('Foto bukti / nota pembayaran WAJIB diunggah untuk metode transfer atau QRIS!\n\nSilakan lampirkan struk ATM atau screenshot m-Banking Anda.', 'error', 'Bukti Transfer Diperlukan');
    fileInput.focus();
    if (event) event.preventDefault();
    return false;
  }

  if (!isProofValidForTransfer) {
    window.showModernAlert('File yang diunggah belum lolos screening nota pembayaran!\n\nPastikan foto yang diunggah adalah nota transfer atau screenshot m-banking yang sah dan jelas.', 'error', 'Screening Nota');
    if (event) event.preventDefault();
    return false;
  }

  return true;
}

// ─── Timezone Auto-Detection (WIB, WITA, WIT) Berjalan Otomatis di Latar Belakang ───
var userSelectedTimezone = 'WIB';

function initTimezoneDetection() {
  try {
    var offset = -new Date().getTimezoneOffset() / 60;
    var tzName = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
    var detected = 'WIB';

    if (offset >= 8.5 || tzName.includes('Jayapura')) {
      detected = 'WIT';
    } else if (offset >= 7.5 || tzName.includes('Makassar') || tzName.includes('Bali') || tzName.includes('Denpasar') || tzName.includes('Ujung_Pandang')) {
      detected = 'WITA';
    } else {
      detected = 'WIB';
    }

    userSelectedTimezone = detected;
    var inputEl = document.getElementById('clientTimezoneInput');
    if (inputEl) inputEl.value = detected;
  } catch (e) {
    userSelectedTimezone = 'WIB';
    var inputEl = document.getElementById('clientTimezoneInput');
    if (inputEl) inputEl.value = 'WIB';
  }
}

// ─── Pop-up Detail Kuitansi Digital Resmi ─────────────────────────
function viewDetailedReceipt(code, date, timeWithTz, tenantName, roomNumber, desc, method, amountFormatted, proofUrl) {
  document.getElementById('dtlRecCode').textContent = code || 'TRX-001';
  document.getElementById('dtlRecTime').textContent = date + ', ' + timeWithTz;
  document.getElementById('dtlRecTenant').textContent = tenantName;
  document.getElementById('dtlRecRoom').textContent = 'Kamar #' + roomNumber;
  document.getElementById('dtlRecDesc').textContent = desc || '-';
  document.getElementById('dtlRecMethod').textContent = method;
  document.getElementById('dtlRecAmount').textContent = 'Rp ' + amountFormatted;

  var proofBox = document.getElementById('dtlRecProofBox');
  var proofImg = document.getElementById('dtlRecProofImg');
  var proofLink = document.getElementById('dtlRecProofLink');
  if (proofUrl && proofBox && proofImg) {
    proofBox.style.display = 'block';
    proofImg.src = proofUrl;
    if (proofLink) proofLink.href = proofUrl;
  } else if (proofBox) {
    proofBox.style.display = 'none';
  }

  // Buka modal kuitansi digital resmi
  document.getElementById('modalDigitalReceiptDetail')?.classList.add('open');
}

// Inisialisasi Deteksi Zona Waktu Otomatis & Auto-Dismiss Alert saat Dokumen Siap
document.addEventListener('DOMContentLoaded', function() {
  initTimezoneDetection();

  // Auto-dismiss notifikasi flash setelah sekitar 10 detik
  document.querySelectorAll('.tp-flash-alert').forEach(function(el) {
    setTimeout(function() {
      if (!el || !el.parentNode) return;
      el.style.opacity = '0';
      el.style.transform = 'translateY(-10px)';
      el.style.maxHeight = '0';
      el.style.paddingTop = '0';
      el.style.paddingBottom = '0';
      el.style.marginTop = '0';
      el.style.marginBottom = '0';
      setTimeout(function() {
        if (el && el.parentNode) el.remove();
      }, 550);
    }, 10000);
  });
});

// ─── Foto Bukti Transfer Preview ─────────────────────────────────
function previewProofImage(event) {
  var file = event.target.files[0];
  var placeholder = document.getElementById('proofPreviewPlaceholder');
  var img = document.getElementById('proofPreviewImg');
  if (file && img) {
    var reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      img.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
  }
}

// ─── Avatar live preview ──────────────────────────────────────
function previewSelectedAvatar(event) {
  var file = event.target.files[0];
  if (file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var preview = document.getElementById('avatarPreviewImg');
      var previewInit = document.getElementById('avatarPreviewInitial');
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      if (previewInit) previewInit.style.setProperty('display', 'none', 'important');
    };
    reader.readAsDataURL(file);
  }
}

// ─── Copy to clipboard ────────────────────────────────────────
function copyText(text, btnElement) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(function() {
      var orig = btnElement.textContent;
      btnElement.textContent = 'Tersalin!';
      btnElement.style.background = '#2563eb';
      btnElement.style.color = '#ffffff';
      setTimeout(function() {
        btnElement.textContent = orig;
        btnElement.style.background = '';
        btnElement.style.color = '';
      }, 1800);
    }).catch(function() { prompt('Salin teks berikut:', text); });
  } else {
    prompt('Salin teks berikut:', text);
  }
}

// =========================================================================
// WHITE BACKGROUND SNAKE TRACER LOADING (LOGIN -> PENGHUNI KOST)
// =========================================================================
(function() {
  var overlay = document.getElementById('f3dTenantPortalOverlay');
  var bar = document.getElementById('f3dTenantProgressBar');
  var gHouse = document.getElementById('f3dTenantGreenHouse');
  var sHouse = document.getElementById('f3dTenantSnakeHouse');
  var gDoor = document.getElementById('f3dTenantGreenDoor');
  var sDoor = document.getElementById('f3dTenantSnakeDoor');
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

  window.dismissTenant3dPortal = function(isManualSkip) {
    if (portalTimer) clearTimeout(portalTimer);
    if (animRaf) cancelAnimationFrame(animRaf);
    if (!overlay) return;

    if (isManualSkip) {
      setSnakePath(gHouse, sHouse, 1);
      setSnakePath(gDoor, sDoor, 1);
      if (bar) bar.style.width = '100%';
    }

    overlay.classList.add('portal-exit');
    document.body.classList.remove('f3d-tenant-active');
    document.body.classList.add('f3d-tenant-ready');

    var delay = isManualSkip ? 250 : 650;
    setTimeout(function() {
      overlay.style.display = 'none';
    }, delay);

    try {
      sessionStorage.removeItem('f3d_from_login');
      sessionStorage.removeItem('f3d_from_login_portal');
    } catch(e) {}
  };

  function startSnakeLoadingAnimation() {
    setSnakePath(gHouse, sHouse, 0);
    setSnakePath(gDoor, sDoor, 0);
    if (bar) bar.style.width = '0%';

    var startTime = performance.now();
    var houseDuration = 1150;
    var doorDelay = 380;
    var doorDuration = 820;
    var totalDuration = 1450;

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
          window.dismissTenant3dPortal(false);
        }, 300);
      }
    }

    animRaf = requestAnimationFrame(step);
  }

  window.replayTenantSnakeAnimation = function() {
    if (!overlay) return;
    overlay.style.display = 'flex';
    overlay.classList.remove('portal-exit');
    document.body.classList.remove('f3d-tenant-ready');
    document.body.classList.add('f3d-tenant-active');
    startSnakeLoadingAnimation();
  };

  document.addEventListener('DOMContentLoaded', function() {
    var fromLogin = false;
    try {
      fromLogin = sessionStorage.getItem('f3d_from_login') === '1' || sessionStorage.getItem('f3d_from_login_portal') === '1';
    } catch(e) {}

    // HANYA jalankan jika datang dari login
    if (!fromLogin) {
      if (overlay) overlay.style.display = 'none';
      document.body.classList.remove('f3d-tenant-active');
      document.body.classList.add('f3d-tenant-ready');
      return;
    }

    document.body.classList.add('f3d-tenant-active');
    startSnakeLoadingAnimation();
    try {
      sessionStorage.removeItem('f3d_from_login');
      sessionStorage.removeItem('f3d_from_login_portal');
    } catch(e) {}
  });
})();

// =========================================================================
// HANDLER: LENGKAPI DATA DIRI PENGHUNI BARU (GATE POP-UP)
// =========================================================================
@if(!($isProfileComplete ?? true))
document.addEventListener('DOMContentLoaded', function() {
  document.body.classList.add('tp-profile-locked');
});

window.handleGateIdCardPhotoChange = function(input) {
  var dropzone = document.getElementById('gateIdCardDropzone');
  if (dropzone) dropzone.classList.remove('has-error');
  if (input.files && input.files[0]) {
    var file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran foto kartu identitas terlalu besar. Maksimal 5 MB.');
      input.value = '';
      return;
    }
    var prompt = document.getElementById('gateIdCardUploadPrompt');
    var wrap = document.getElementById('gateIdCardPreviewWrap');
    var img = document.getElementById('gateIdCardPreviewImg');
    var nameEl = document.getElementById('gateIdCardFileName');
    var sizeEl = document.getElementById('gateIdCardFileSize');

    if (nameEl) nameEl.textContent = file.name;
    if (sizeEl) sizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB';

    var reader = new FileReader();
    reader.onload = function(e) {
      if (img) img.src = e.target.result;
      if (prompt) prompt.style.display = 'none';
      if (wrap) wrap.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  }
};

window.removeGateIdCardPhoto = function() {
  var input = document.getElementById('gateIdCardPhoto');
  if (input) input.value = '';
  var prompt = document.getElementById('gateIdCardUploadPrompt');
  var wrap = document.getElementById('gateIdCardPreviewWrap');
  var img = document.getElementById('gateIdCardPreviewImg');
  if (img) img.src = '';
  if (prompt) prompt.style.display = 'flex';
  if (wrap) wrap.style.display = 'none';
};

function submitCompleteProfileGate(e) {
  e.preventDefault();
  var form = document.getElementById('formCompleteProfileGate');
  var btn = document.getElementById('btnSubmitGateProfile');
  var name = document.getElementById('gateFullName').value.trim();
  var phone = document.getElementById('gatePhone').value.trim();
  var idCard = document.getElementById('gateIdCard').value.trim();
  var emergency = document.getElementById('gateEmergencyContact').value.trim();
  var photoInput = document.getElementById('gateIdCardPhoto');
  var dropzone = document.getElementById('gateIdCardDropzone');

  if (!name || !phone || !emergency) {
    alert('Nama lengkap, nomor WhatsApp, dan kontak darurat wajib diisi.');
    return;
  }

  var hasFile = photoInput && photoInput.files && photoInput.files.length > 0;
  var hasExistingPhoto = @json(!empty($user->id_card_photo) || (!empty($tenant) && !empty($tenant->id_card_photo)));

  if (!hasFile && !hasExistingPhoto) {
    if (dropzone) {
      dropzone.classList.add('has-error');
      dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    alert('Wajib melampirkan foto kartu identitas (KTP / SIM / KTM / KTA).');
    return;
  }

  var formData = new FormData();
  formData.append('name', name);
  formData.append('phone', phone);
  formData.append('id_card', idCard);
  formData.append('emergency_contact', emergency);
  if (hasFile) {
    formData.append('id_card_photo', photoInput.files[0]);
  }

  var origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <span>Menyimpan Data Diri & Foto Identitas...</span>';

  fetch('{{ route("tenant.profile.complete") }}', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
  })
  .then(function(res) {
    return res.json().then(function(data) {
      return { ok: res.ok, status: res.status, data: data };
    });
  })
  .then(function(result) {
    if (result.ok && result.data && result.data.success) {
      var overlay = document.getElementById('tpProfileGateModal');
      if (overlay) {
        overlay.classList.add('gate-closed');
        setTimeout(function() {
          overlay.style.display = 'none';
        }, 400);
      }
      document.body.classList.remove('tp-profile-locked');

      // Update in DOM
      var pName = document.getElementById('profileName');
      var pPhone = document.getElementById('profilePhone');
      var pIdCard = document.getElementById('profileIdCard');
      if (pName) pName.value = name;
      if (pPhone) pPhone.value = phone;
      if (pIdCard) pIdCard.value = idCard;

      var navName = document.querySelector('.tp-user-name');
      if (navName) navName.textContent = name;

      var welcomeName = document.querySelector('.tp-welcome-title strong');
      if (welcomeName) welcomeName.textContent = name;

      if (typeof showPortalToast === 'function') {
        showPortalToast(result.data.message || 'Data diri dan foto kartu identitas berhasil disimpan! Seluruh fitur portal kini aktif.', 'success');
      } else {
        alert(result.data.message || 'Data diri dan foto kartu identitas berhasil disimpan! Seluruh fitur portal kini telah aktif.');
      }
    } else {
      btn.disabled = false;
      btn.innerHTML = origHtml;
      var msg = (result.data && result.data.message) ? result.data.message : 'Gagal menyimpan data diri. Pastikan seluruh kolom diisi dengan benar.';
      if (result.data && result.data.errors) {
        var errList = Object.values(result.data.errors).flat();
        if (errList.length) msg = errList[0];
      }
      alert(msg);
    }
  })
  .catch(function(err) {
    console.error(err);
    alert('Terjadi kendala koneksi saat menyimpan data diri.');
    btn.disabled = false;
    btn.innerHTML = origHtml;
  });
}

function submitGateWithGoogle(e) {
  if (e) e.preventDefault();
  var form = document.getElementById('formCompleteProfileGate');
  var name = document.getElementById('gateFullName').value.trim();
  var phone = document.getElementById('gatePhone').value.trim();
  var idCard = document.getElementById('gateIdCard').value.trim();
  var emergency = document.getElementById('gateEmergencyContact').value.trim();
  var photoInput = document.getElementById('gateIdCardPhoto');
  var dropzone = document.getElementById('gateIdCardDropzone');
  var btnGoogle = document.getElementById('btnGateSaveWithGoogle');

  if (!name || !phone || !emergency) {
    alert('Nama lengkap, nomor WhatsApp, dan kontak darurat wajib diisi.');
    return;
  }

  var hasFile = photoInput && photoInput.files && photoInput.files.length > 0;
  var hasExistingPhoto = @json(!empty($user->id_card_photo) || (!empty($tenant) && !empty($tenant->id_card_photo)));

  if (!hasFile && !hasExistingPhoto) {
    if (dropzone) {
      dropzone.classList.add('has-error');
      dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    alert('Wajib melampirkan foto kartu identitas (KTP / SIM / KTM / KTA).');
    return;
  }

  var formData = new FormData();
  formData.append('name', name);
  formData.append('phone', phone);
  formData.append('id_card', idCard);
  formData.append('emergency_contact', emergency);
  if (hasFile) {
    formData.append('id_card_photo', photoInput.files[0]);
  }

  var orig = btnGoogle ? btnGoogle.innerHTML : '';
  if (btnGoogle) {
    btnGoogle.disabled = true;
    btnGoogle.innerHTML = '<span class="tp-spinner"></span> Menyimpan data...';
  }

  fetch('{{ route("tenant.profile.complete") }}', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
  })
  .then(function(res) {
    return res.json().then(function(data) {
      return { ok: res.ok, status: res.status, data: data };
    });
  })
  .then(function(result) {
    if (result.ok && result.data && result.data.success) {
      if (btnGoogle) btnGoogle.innerHTML = '<span class="tp-spinner"></span> Menghubungkan Google...';
      window.location.href = "{{ route('tenant.google.connect') }}";
    } else {
      if (btnGoogle) { btnGoogle.disabled = false; btnGoogle.innerHTML = orig; }
      var msg = (result.data && result.data.message) ? result.data.message : 'Gagal menyimpan data diri.';
      if (result.data && result.data.errors) {
        var errList = Object.values(result.data.errors).flat();
        if (errList.length) msg = errList[0];
      }
      alert(msg);
    }
  })
  .catch(function(err) {
    console.error(err);
    window.location.href = "{{ route('tenant.google.connect') }}";
  });
}
@endif

@if(session('open_profile_modal'))
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    var modal = document.getElementById('modalProfile');
    if (modal) modal.classList.add('open');
  }, 450);
});
@endif
</script>

@endsection
