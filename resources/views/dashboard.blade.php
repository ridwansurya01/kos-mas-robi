@extends('layouts.app')

@section('title', ($setting->kos_name ?? 'Kos Mas Robi') . ' - Smart Kost Management')

@section('content')

  <script>
    window.initialAdminData = {
      settings: @json($setting),
      rooms: @json($rooms),
      finances: @json($allFinances ?? []),
      maintenance: @json($allMaintenance ?? [])
    };

    window.isOwnerUser = {{ Auth::user()->isOwner() ? 'true' : 'false' }};
    window.switchTab = function(tabId) {
      if ((tabId === 'tab-finances' || tabId === 'tab-admins') && !window.isOwnerUser) {
        tabId = 'tab-dashboard';
      }
      var navTitles = {
        'tab-dashboard': { title: 'Dashboard', sub: 'Monitoring real-time hunian, status kamar, dan operasional' },
        'tab-rooms': { title: 'Daftar Kamar', sub: 'Daftar seluruh unit kamar, fasilitas, dan tarif sewa' },
        'tab-tenants': { title: 'Data Penghuni', sub: 'Kelola kontak anak kos, sewa aktif, dan pengingat WhatsApp' },
        'tab-bills': { title: 'Tagihan Sewa', sub: 'Monitoring jatuh tempo sewa, kirim WA pengingat tagihan, dan konfirmasi lunas' },
        'tab-transfers': { title: 'Pindah Kamar', sub: 'Daftar permohonan alih kamar dari anak kos dan persetujuan' },
        'tab-maintenance': { title: 'Keluhan & Servis', sub: 'Daftar laporan fasilitas kos dan penanganan teknisi' },
        'tab-finances': { title: 'Laporan Keuangan', sub: 'Analisis kurva pendapatan bulanan s/d 1 tahun penuh, laba bersih, dan buku kas' },
        'tab-admins': { title: 'Manajemen Staf Admin', sub: 'Pantau siapa saja yang memiliki akses Admin operasional dan kelola akun staf' },
        'tab-settings': { title: 'Pengaturan Kos', sub: 'Pengaturan profil properti, nomor rekening, dan cadangan data' }
      };
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.toggle('active', pane.id === tabId);
      });
      document.querySelectorAll('.app-sidebar .nav-item').forEach(function(item) {
        item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
      });
      document.querySelectorAll('.mobile-bottom-nav .bottom-nav-item').forEach(function(item) {
        item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
      });
      if (navTitles[tabId]) {
        var pageTitle = document.getElementById('pageTitle');
        var pageSubtitle = document.getElementById('pageSubtitle');
        var breadcrumbCurrent = document.getElementById('breadcrumbCurrent');
        var headerActiveTitle = document.getElementById('headerActiveTitle');
        if (pageTitle) pageTitle.textContent = navTitles[tabId].title;
        if (pageSubtitle) pageSubtitle.textContent = navTitles[tabId].sub;
        if (breadcrumbCurrent) breadcrumbCurrent.textContent = navTitles[tabId].title;
        if (headerActiveTitle) headerActiveTitle.textContent = navTitles[tabId].title;
        var mobileActiveTitle = document.getElementById('mobileActiveTitle');
        if (mobileActiveTitle) mobileActiveTitle.textContent = navTitles[tabId].title;
      }
      if (typeof window.updateSettingsSubChip === 'function') window.updateSettingsSubChip(tabId);
      if (tabId === 'tab-finances' && typeof window.drawRevenueCurve === 'function') {
        setTimeout(window.drawRevenueCurve, 80);
      }
      var sb = document.getElementById('appSidebar');
      if (sb) sb.classList.remove('open');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Menampilkan nama sub-fitur aktif di dalam Pengaturan Kos (judul mobile & desktop)
    window.updateSettingsSubChip = function(tabId) {
      var chip = document.getElementById('mobileActiveSubChip');
      var headerSub = document.getElementById('headerActiveSubChip');
      var activeTab = tabId;
      if (!activeTab) {
        var activePane = document.querySelector('.tab-pane.active');
        activeTab = activePane ? activePane.id : '';
      }
      var label = '';
      if (activeTab === 'tab-settings') {
        var activeBtn = document.querySelector('#settingsSubTabNav .btn-settings-subtab.active span');
        label = activeBtn ? activeBtn.textContent.trim() : '';
      }
      [chip, headerSub].forEach(function(el) {
        if (!el) return;
        el.textContent = label;
        el.style.display = label ? 'inline-flex' : 'none';
      });
    };

    document.addEventListener('click', function(e) {
      if (e.target.closest('#settingsSubTabNav .btn-settings-subtab')) {
        setTimeout(function() { window.updateSettingsSubChip('tab-settings'); }, 0);
      }
    });

    window.openModal = function(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('open');
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        document.body.style.overflow = 'hidden';
      }
    };

    window.closeModal = function(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('open');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        document.body.style.overflow = '';
      }
    };
  </script>

  @php
    $allBranchesGlobal = $setting->branches ?? [];
    $branch1Name = $allBranchesGlobal['lokasi_1']['name'] ?? 'Kos Mas Robi 1 - Kampus Utama';
    $branch2Name = $allBranchesGlobal['lokasi_2']['name'] ?? 'Kos Mas Robi 2 - Ringroad Exclusive';
    $ownerInitials = collect(preg_split('/\s+/', trim($setting->owner_name ?? 'Mas Robi')))
      ->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'MR';
  @endphp

  <!-- =========================================================================
       SIDEBAR (Desktop Fixed, Mobile Slide-out Drawer)
       ========================================================================= -->
  <aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-header">
      <div class="brand-icon-box modern-brand-box">
        <div class="brand-icon-glow"></div>
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
          <path d="M9 21V12h6v9"/>
        </svg>
      </div>
      <div class="brand-details">
        <h2 class="brand-title">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h2>
        <div class="brand-subtitle-wrap">
          <span class="brand-status-dot"></span>
          <span class="brand-badge modern-badge">{{ Auth::user()->isOwner() ? 'Manajemen Owner' : 'Manajemen Admin' }}</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav" aria-label="Main Navigation">
      <div class="nav-section-title">Ringkasan</div>

      <div class="nav-item active" data-tab="tab-dashboard" onclick="window.switchTab('tab-dashboard')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg></span>
        <span>Dashboard</span>
      </div>

      <div class="nav-section-title" style="margin-top: 12px;">Kamar & Pelanggan</div>

      <div class="nav-item" data-tab="tab-rooms" onclick="window.switchTab('tab-rooms')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><line x1="12" y1="4" x2="12" y2="20"/></svg></span>
        <span>Daftar Kamar</span>
        <span class="nav-badge-count" id="badgeRoomCount">{{ $totalRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-tenants" onclick="window.switchTab('tab-tenants')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span>Data Penghuni</span>
        <span class="nav-badge-count" id="badgeTenantCount">{{ $occupiedRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-bills" onclick="window.switchTab('tab-bills')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></span>
        <span>Tagihan Sewa</span>
        <span class="nav-badge-count" id="badgeDueBills" style="{{ $dueRooms > 0 ? 'background: rgba(244, 63, 94, 0.22); color: #fda4af; border: 1px solid rgba(244, 63, 94, 0.4); font-weight:800;' : 'display:none;' }}">{{ $dueRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-transfers" onclick="window.switchTab('tab-transfers')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg></span>
        <span>Pindah Kamar</span>
        <span class="nav-badge-count" id="badgePendingTransfers" style="{{ ($pendingTransfersCount ?? 0) > 0 ? 'background: #f0fdfa; color: #0a5661; border: 1px solid #99f6e4;' : 'display:none;' }}">{{ $pendingTransfersCount ?? 0 }}</span>
      </div>

      <div class="nav-item" data-tab="tab-maintenance" onclick="window.switchTab('tab-maintenance')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
        <span>Keluhan & Servis</span>
        <span class="nav-badge-count" id="badgePendingRepairs">{{ $activeTickets }}</span>
      </div>

      <div class="nav-section-title" style="margin-top: 14px;">{{ Auth::user()->isOwner() ? 'Manajemen Owner' : 'Manajemen Admin' }}</div>

      @if(Auth::user()->isOwner())
      <div class="nav-item" data-tab="tab-finances" onclick="window.switchTab('tab-finances')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
        <span>Laporan Keuangan</span>
      </div>

      <div class="nav-item" data-tab="tab-admins" onclick="window.switchTab('tab-admins')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span>Manajemen Admin</span>
        <span class="nav-badge-count" id="badgeTotalAdmins" style="background: rgba(14, 116, 130, 0.22); color: #0a5661; border: 1px solid rgba(14, 116, 130, 0.35); font-weight:800;">{{ $totalAdminsCount ?? count($allAdmins ?? []) }}</span>
      </div>
      @endif

      <div class="nav-item" data-tab="tab-settings" onclick="window.switchTab('tab-settings')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
        <span>Pengaturan Kos</span>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user-label">Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</div>
      <div class="host-profile modern-host-card host-profile-clickable" role="button" tabindex="0" onclick="window.openModal('modalAkunPengelola')" title="Buka Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}">
        <div class="host-avatar-wrap">
          <div class="host-avatar">{{ $ownerInitials }}</div>
          <span class="host-online-pulse" title="Online Aktif"></span>
        </div>
        <div class="host-info">
          <div class="host-name-row">
            <span class="host-name" id="profileOwnerName">{{ Auth::user()->name }}</span>
            <span class="host-badge-admin">{{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</span>
          </div>
          <div class="host-role">{{ Auth::user()->isOwner() ? 'Pengelola Properti Kos' : 'Staf Admin Operasional' }}</div>
        </div>
        <span class="host-profile-chevron" aria-hidden="true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
    </div>
  </aside>

  <!-- =========================================================================
       MAIN APPLICATION AREA
       ========================================================================= -->
  <main class="app-main">

    <!-- HEADER TOP BAR -->
    <header class="app-header modern-header">
      <div class="header-left">
        <h1 class="header-active-page-title" id="headerActiveTitle">Dashboard</h1>
        <span class="header-sub-chip" id="headerActiveSubChip" style="display: none;"></span>
        <div class="mobile-header-brand">
          <div class="brand-icon-box modern-brand-box">
            <div class="brand-icon-glow"></div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
              <path d="M9 21V12h6v9"/>
            </svg>
          </div>
          <div class="brand-details">
            <h2 class="brand-title">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h2>
            <div class="brand-subtitle-wrap">
              <span class="brand-status-dot"></span>
              <span class="brand-badge modern-badge">{{ Auth::user()->isOwner() ? 'Manajemen Owner' : 'Manajemen Admin' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="header-right modern-header-right">
        <button type="button" class="mobile-account-btn" onclick="window.openModal('modalAkunPengelola')" title="Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}" aria-label="Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}">
          <span class="mobile-account-avatar">{{ $ownerInitials }}</span>
        </button>
        <form action="{{ route('logout') }}" method="POST" id="formLogoutAdmin" style="display:inline; margin:0;">
          @csrf
          <button type="button" class="header-action-btn modern-logout-btn" id="btnTriggerLogoutAdmin" title="Keluar / Logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Keluar</span>
          </button>
        </form>
      </div>
    </header>

    <!-- MOBILE ACTIVE FEATURE TITLE BAR (Khusus HP — sama seperti judul fitur di komputer) -->
    <div class="mobile-page-titlebar" id="mobilePageTitlebar">
      <div class="mpt-label">Anda sedang berada di</div>
      <div class="mpt-row">
        <h1 class="mpt-title" id="mobileActiveTitle">Dashboard</h1>
        <span class="mpt-sub-chip" id="mobileActiveSubChip" style="display: none;"></span>
      </div>
    </div>

    <!-- =======================================================================
         CONTENT BODY & TABS
         ======================================================================= -->
    <div class="content-body">
      <!-- =======================================================================
           PANE 1: TAB DASHBOARD (DENAH INTERAKTIF & RINGKASAN STATISTIK)
           ======================================================================= -->
      <div class="tab-pane active" id="tab-dashboard">

        <!-- RINGKASAN STATISTIK KPI KOS (HANYA MUNCUL DI TAB DASHBOARD) -->
        <section class="stats-grid dashboard-stats-grid" aria-label="Ringkasan Statistik Kos" style="padding: 0; margin-bottom: 24px;">
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tingkat Okupansi</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statOccupancyRate">{{ $occupancyRate }}%</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">{{ $occupiedRooms }} Terisi</span>
              <span>dari {{ $totalRooms }} Kamar</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Kamar Tersedia</span>
              <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statAvailableRooms">{{ $availableRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info">Siap Huni</span>
              <span>Unit Bersih & Terawat</span>
            </div>
          </div>

          @if(Auth::user()->isOwner())
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Pemasukan Bulan Ini</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statMonthlyIncome">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">● Terverifikasi Owner</span>
              <span>Buku Kas Aktif</span>
            </div>
          </div>
          @else
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Total Penghuni Aktif</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
            </div>
            <div class="stat-card-value">{{ $occupiedRooms }} Penghuni</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">● Terdata Aktif</span>
              <span>Penghuni Unit Kamar</span>
            </div>
          </div>
          @endif

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tagihan Jatuh Tempo</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statDueBills">{{ $dueRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span style="color: #d97706; font-weight: 700;">Rp {{ number_format($dueAmount, 0, ',', '.') }}</span>
              <span>perlu konfirmasi</span>
            </div>
          </div>
        </section>
        <div class="section-header">
          <div class="section-title">
            <h2>Denah & Status Kamar Kos</h2>
          </div>
          <div class="filter-pills" id="dashboardFilters">
            <button type="button" class="filter-pill active" data-filter="all">Semua Kamar</button>
            <button type="button" class="filter-pill" data-filter="occupied">Terisi</button>
            <button type="button" class="filter-pill" data-filter="available">Kosong</button>
            <button type="button" class="filter-pill" data-filter="due">Jatuh Tempo</button>
            <button type="button" class="filter-pill" data-filter="repair">Perbaikan</button>
          </div>
        </div>

        <!-- Floor 1 -->
        <div class="floor-section">
          <div class="floor-badge">Lantai 1 (Lantai Bawah)</div>
          <div class="rooms-grid" id="floor1Grid">
            <!-- Dynamically populated by JS & Laravel API -->
          </div>
        </div>

        <!-- Floor 2 -->
        <div class="floor-section">
          <div class="floor-badge">Lantai 2 (Lantai Atas)</div>
          <div class="rooms-grid" id="floor2Grid">
            <!-- Dynamically populated by JS & Laravel API -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 2: TAB DAFTAR KAMAR
           ===================================================================== -->
      <div class="tab-pane" id="tab-rooms">
        <div class="section-header" style="justify-content: flex-end;">
          <button type="button" class="btn-primary-action" id="btnAddNewRoom">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Tambah Kamar Baru</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar" style="display: flex; flex-direction: column; gap: 14px; padding: 18px 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
              <div class="table-search" style="flex: 1; min-width: 260px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="roomSearchInput" placeholder="Cari nomor kamar, tipe, atau penghuni...">
              </div>
              <div class="filter-pills" id="roomTabFilters">
                <button type="button" class="filter-pill active" data-floor="all">Semua Lantai</button>
                <button type="button" class="filter-pill" data-floor="1">Lantai 1</button>
                <button type="button" class="filter-pill" data-floor="2">Lantai 2</button>
              </div>
            </div>

            <!-- 2 TITIK LOKASI TABS FILTER -->
            <div class="location-switcher-bar" id="roomLocationFilters" style="display: flex; gap: 8px; flex-wrap: wrap; background: #f0fdfa; border: 1.5px solid #ccfbf1; padding: 6px; border-radius: 14px;">
              <button type="button" class="loc-filter-btn active" data-location="all" style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; border: none; font-size: 0.82rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: #0a5661; color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);">
                Semua Cabang ({{ $totalRooms }})
              </button>
              <button type="button" class="loc-filter-btn" data-location="Lokasi 1" style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; border: 1px solid rgba(10,86,97,0.15); font-size: 0.82rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0a5661;">
                <span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $roomsLoc1->count() }})
              </button>
              <button type="button" class="loc-filter-btn" data-location="Lokasi 2" style="display: flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; border: 1px solid rgba(13,148,136,0.15); font-size: 0.82rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0d9488;">
                <span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $roomsLoc2->count() }})
              </button>
            </div>
          </div>

          <!-- Desktop Table View -->
          <table class="custom-table" id="roomsMasterTable">
            <thead>
              <tr>
                <th>No Kamar</th>
                <th>Lokasi Cabang</th>
                <th>Tipe & Fasilitas</th>
                <th>Lantai</th>
                <th>Harga Bulanan</th>
                <th>Status</th>
                <th>Penghuni Saat Ini</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="roomsMasterTableBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>

          <!-- Mobile Card View -->
          <div class="mobile-card-list" id="roomsMobileCardList">
            <!-- Dynamically populated -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 3: TAB DATA PENGHUNI (DENGAN PERBEDAAN 2 KOS MODERN)
           ===================================================================== -->
      <div class="tab-pane" id="tab-tenants">
        <div class="section-header" style="justify-content: flex-end;">
          <button type="button" class="btn-primary-action" id="btnAddNewTenant">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Daftarkan Penghuni & Buat Akun</span>
          </button>
        </div>

        @php
          $b1TenantsCount = $rooms->where('location', 'Lokasi 1')->filter(fn($r) => $r->activeTenant)->count();
          $b2TenantsCount = $rooms->where('location', 'Lokasi 2')->filter(fn($r) => $r->activeTenant)->count();
          $b1DueTenantsCount = $rooms->where('location', 'Lokasi 1')->where('status', 'due')->count();
          $b2DueTenantsCount = $rooms->where('location', 'Lokasi 2')->where('status', 'due')->count();
        @endphp

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (DATA PENGHUNI) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="tenantLocationFilters">
            <button type="button" class="filter-pill active" data-tloc="all">Semua Cabang ({{ $b1TenantsCount + $b2TenantsCount }})</button>
            <button type="button" class="filter-pill" data-tloc="Lokasi 1"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $b1TenantsCount }})</button>
            <button type="button" class="filter-pill" data-tloc="Lokasi 2"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $b2TenantsCount }})</button>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="tenantSearchInput" placeholder="Cari nama, kamar, cabang, atau nomor WhatsApp...">
            </div>
            <div class="filter-pills" id="tenantStatusFilter">
              <button type="button" class="filter-pill active" data-tstatus="all">Semua</button>
              <button type="button" class="filter-pill" data-tstatus="active">Aktif Menghuni</button>
              <button type="button" class="filter-pill" data-tstatus="due">Jatuh Tempo</button>
            </div>
          </div>

          <table class="custom-table" id="tenantsTable">
            <thead>
              <tr>
                <th>Nama Penghuni</th>
                <th>Kamar</th>
                <th>Lokasi Cabang & Akses</th>
                <th>No. WhatsApp</th>
                <th>Tgl Masuk</th>
                <th>Jatuh Tempo</th>
                <th>Status Bayar</th>
                <th>Aksi Cepat</th>
              </tr>
            </thead>
            <tbody id="tenantsTableBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>

          <div class="mobile-card-list" id="tenantsMobileCardList">
            <!-- Dynamically populated -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 3B: TAB TAGIHAN & INVOICE SEWA (DENGAN PERBEDAAN 2 KOS MODERN)
           ===================================================================== -->
      <div class="tab-pane" id="tab-bills">
        <div class="section-header" style="justify-content: flex-end;">
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn-primary-action" onclick="openManualPaymentReceiptModal()" style="background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
              <span>+ Input Pembayaran & Terbitkan Kuitansi</span>
            </button>
            <button type="button" class="btn-primary-action" onclick="exportBillingReport()" style="background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-color);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Export Rekap Tagihan</span>
            </button>
          </div>
        </div>

        @php
          $b1RoomsList = $rooms->where('location', 'Lokasi 1');
          $b2RoomsList = $rooms->where('location', 'Lokasi 2');
          $b1PaidCount = $b1RoomsList->where('status', 'occupied')->count();
          $b1DueCount = $b1RoomsList->where('status', 'due')->count();
          $b1MonthlyPot = $b1RoomsList->whereIn('status', ['occupied', 'due'])->sum('price');
          $b1DueNominal = $b1RoomsList->where('status', 'due')->sum('price');

          $b2PaidCount = $b2RoomsList->where('status', 'occupied')->count();
          $b2DueCount = $b2RoomsList->where('status', 'due')->count();
          $b2MonthlyPot = $b2RoomsList->whereIn('status', ['occupied', 'due'])->sum('price');
          $b2DueNominal = $b2RoomsList->where('status', 'due')->sum('price');
        @endphp

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (TAGIHAN SEWA) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="billingLocationFilters">
            <button type="button" class="filter-pill active" onclick="filterBillingLocation('all', this)">Semua Cabang ({{ $totalRooms }} Unit)</button>
            <button type="button" class="filter-pill" onclick="filterBillingLocation('Lokasi 1', this)"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $b1RoomsList->count() }} Unit)</button>
            <button type="button" class="filter-pill" onclick="filterBillingLocation('Lokasi 2', this)"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $b2RoomsList->count() }} Unit)</button>
          </div>
        </div>

        <!-- 3 KPI Cards Tagihan -->
        <div class="stats-grid billing-stats-grid" style="padding: 0; margin-bottom: 22px;">
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tagihan Lunas Bulan Ini</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="billKpiPaidVal" style="color:#10b981;">{{ $occupiedRooms - $dueRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">● Terverifikasi {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</span>
              <span><span data-branch-name="lokasi_1">{{ $branch1Name }}</span>: {{ $b1PaidCount }} • <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>: {{ $b2PaidCount }}</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tagihan Jatuh Tempo / Belum Lunas</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="billKpiDueVal" style="color:#f59e0b;">{{ $dueRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span id="billKpiDueNom" style="color:#d97706;font-weight:700;">Rp {{ number_format($dueAmount, 0, ',', '.') }}</span>
              <span>perlu pengingat tagihan</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tingkat Ketepatan Bayar</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="billKpiRateVal">{{ $occupiedRooms > 0 ? round((($occupiedRooms - $dueRooms) / $occupiedRooms) * 100, 1) : 100 }}%</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info">Collection Rate</span>
              <span>Kepatuhan bayar 2 cabang</span>
            </div>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="billingSearchInput" placeholder="Cari kamar, cabang, nama anak kos, status..." onkeyup="filterBillingTable()">
            </div>
            <div class="filter-pills" id="billingStatusFilter">
              <button type="button" class="filter-pill active" onclick="filterBillingStatus('all', this)">Semua Unit ({{ $totalRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('due', this)" style="color:#d97706;">Jatuh Tempo ({{ $dueRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('paid', this)" style="color:#059669;">Lunas ({{ $occupiedRooms - $dueRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('available', this)">Kosong ({{ $availableRooms }})</button>
            </div>
          </div>

          <div class="table-responsive">
          <table class="custom-table" id="billingMasterTable">
            <thead>
              <tr>
                <th>Unit Kamar</th>
                <th>Lokasi Cabang & Layanan</th>
                <th>Nama Penghuni</th>
                <th>Nominal Sewa / Bln</th>
                <th>Jatuh Tempo</th>
                <th>Status Pembayaran</th>
                <th>Aksi Penagihan & Input Manual</th>
              </tr>
            </thead>
            <tbody id="billingTableBody">
              @forelse($rooms as $r)
              @php
                $t = $r->activeTenant;
                $isDue = $r->status === 'due';
                $isOccupied = $r->status === 'occupied';
                $isAvailable = $r->status === 'available';
                $statusType = $isDue ? 'due' : ($isOccupied ? 'paid' : 'available');
                $tenantName = $t ? $t->name : '-';
                $tenantPhone = $t ? $t->phone : '';
                $dueDay = $t ? $t->due_day : 5;
                $rLoc = $r->location ?? (intval($r->number) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                $isLoc2 = $rLoc === 'Lokasi 2';
              @endphp
              <tr data-status="{{ $statusType }}" data-location="{{ $rLoc }}" data-price="{{ (int)$r->price }}" data-search="{{ strtolower($r->number . ' ' . $rLoc . ' ' . $r->type . ' ' . $tenantName . ' ' . ($isDue ? 'jatuh tempo belum lunas' : ($isOccupied ? 'lunas' : 'kosong'))) }}">
                <td>
                  <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary);">Kamar #{{ $r->number }}</div>
                  <div style="font-size: 0.74rem; color: var(--text-secondary);">Lt. {{ $r->floor }} • {{ $r->type }}</div>
                </td>
                <td>
                  @if($isLoc2)
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;">
                      <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                    </span>
                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 3px;">Incl. Co-Working & Cleaning 2x</div>
                  @else
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;">
                      <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                    </span>
                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 3px;">Incl. Air PDAM & WiFi Kampus</div>
                  @endif
                </td>
                <td>
                  @if($t)
                    <div style="font-weight: 700; color: var(--text-primary);">{{ $t->name }}</div>
                    <div style="font-size: 0.76rem; color: #0a5661;">{{ $t->phone }}</div>
                  @else
                    <span style="color: var(--text-muted); font-style: italic;">(Unit Kosong / Siap Huni)</span>
                  @endif
                </td>
                <td style="font-weight: 800; color: #0a5661;">
                  Rp {{ number_format($r->price, 0, ',', '.') }}
                </td>
                <td>
                  @if($t)
                    <div style="font-weight: 700; color: {{ $isDue ? '#dc2626' : 'var(--text-primary)' }};">
                      Tiap Tgl {{ $dueDay }}
                    </div>
                    <div style="font-size: 0.74rem; color: {{ $isDue ? '#ef4444' : 'var(--text-secondary)' }};">
                      {{ $isDue ? 'Jatuh tempo bulan ini' : 'Pembayaran terjadwal' }}
                    </div>
                  @else
                    <span style="color: var(--text-muted);">-</span>
                  @endif
                </td>
                <td>
                  @if($isDue)
                    <span class="status-badge due" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      Belum Lunas (Jatuh Tempo)
                    </span>
                  @elseif($isOccupied)
                    <span class="status-badge occupied" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      Lunas Bulan Ini
                    </span>
                  @else
                    <span class="status-badge available" style="padding: 4px 12px; border-radius: 999px;">
                      ● Kosong
                    </span>
                  @endif
                </td>
                <td>
                  <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                    @if($t)
                      @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $tenantPhone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                        $branchLabel = $isLoc2 ? 'Kos Mas Robi 2 (Ringroad Exclusive)' : 'Kos Mas Robi 1 (Kampus Utama)';
                        $waText = rawurlencode("Halo Kak {$t->name}, kami dari Pengelola {$branchLabel} ingin menginformasikan terkait tagihan sewa Kamar #{$r->number} sebesar Rp " . number_format($r->price, 0, ',', '.') . ". Silakan melakukan pembayaran ke Rekening BCA: 8830192831 (a.n. Robi Suryanto) atau Mandiri: 1370019283100. Terima kasih banyak Kak!");
                      @endphp
                      <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn-secondary" style="background:#059669;color:#ffffff;border:none;padding:5px 10px;font-size:0.76rem;font-weight:700;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" title="Kirim Pesan WhatsApp Penagihan">
                        WA Tagihan
                      </a>
                      <button type="button" class="btn-secondary" style="background:#0a5661;color:#ffffff;border:none;padding:5px 11px;font-size:0.76rem;font-weight:800;border-radius:8px;display:inline-flex;align-items:center;gap:4px;" onclick="openManualPaymentReceiptModal('{{ $r->id }}', '{{ $r->number }}', '{{ addslashes($t->name) }}', {{ (int)$r->price }}, '{{ $rLoc }}')" title="Catat pembayaran & terbitkan kuitansi digital resmi">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Input & Kirim Kuitansi</span>
                      </button>
                    @else
                      <button type="button" class="btn-secondary" style="padding:5px 10px;font-size:0.76rem;" onclick="document.getElementById('btnAddNewTenant')?.click()">
                        + Tambah Penghuni
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">Tidak ada data unit kamar.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          </div>
        </div>
      </div>

      @if(Auth::user()->isOwner())
      <!-- =====================================================================
           PANE 4: TAB LAPORAN KEUANGAN & KURVA ARUS KAS (DENGAN PERBEDAAN 2 KOS)
           ===================================================================== -->
      <div class="tab-pane" id="tab-finances">
        <div class="section-header" style="justify-content: flex-end;">
          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @php
              $currYear = (int)($selectedYear ?? 2026);
              if ($currYear < 2020) $currYear = 2026;
              $maxYearOpt = max(2050, $currYear + 5);
              $yearsList = range(2024, $maxYearOpt);
            @endphp
            <!-- Picker Tahun Laporan -->
            <div class="finance-year-picker-wrap" id="finYearPickerWrap" onwheel="handleYearWheelScroll(event)" title="Pilih atau gulir roda mouse untuk melihat laporan keuangan tahun berapa pun">
              <span class="fyp-label">Tahun Laporan:</span>
              <button type="button" class="fyp-scroll-btn" id="btnPrevYear" onclick="scrollFinanceYear(-1)" title="Tahun Sebelumnya" {{ $currYear <= 2020 ? 'disabled' : '' }}>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              </button>
              <select id="selectFinanceYear" class="fyp-select" onchange="changeFinanceYear(this.value); updateYearNavButtons();">
                @foreach($yearsList as $yr)
                  <option value="{{ $yr }}" {{ $currYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                @endforeach
              </select>
              <button type="button" class="fyp-scroll-btn" id="btnNextYear" onclick="scrollFinanceYear(1)" title="Tahun Berikutnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
              </button>
            </div>

            <!-- Picker Bulan Laporan (Rekapan Per Bulan Khusus Owner) -->
            <div class="finance-year-picker-wrap" id="finMonthPickerWrap" title="Pilih bulan rekapan laporan keuangan">
              <span class="fyp-label">Bulan:</span>
              <button type="button" class="fyp-scroll-btn" id="btnPrevMonth" onclick="scrollFinanceMonth(-1)" title="Bulan Sebelumnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              </button>
              <select id="selectFinanceMonth" class="fyp-select" onchange="changeFinanceMonth(this.value)">
                <option value="all" selected>Semua Bulan (1 Tahun)</option>
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
              </select>
              <button type="button" class="fyp-scroll-btn" id="btnNextMonth" onclick="scrollFinanceMonth(1)" title="Bulan Berikutnya">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
              </button>
            </div>
            <button type="button" class="btn-secondary" onclick="openFinancePdfModal()" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#0f2e33 0%,#0a5661 100%);color:#ffffff;border:1px solid #0d9488;font-weight:700;padding:10px 18px;border-radius:12px;box-shadow:0 6px 16px rgba(10,86,97,0.22);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
              <span>File PDF Laporan Keuangan</span>
            </button>
            <button type="button" class="btn-primary-action" id="btnRecordTransaction">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>Catat Transaksi Baru</span>
            </button>
          </div>
        </div>

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (LAPORAN KEUANGAN) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="financeLocationFilters">
            <button type="button" class="filter-pill active" data-floc="all">Gabungan 2 Kos</button>
            <button type="button" class="filter-pill" data-floc="Lokasi 1">Buku Kas <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></button>
            <button type="button" class="filter-pill" data-floc="Lokasi 2">Buku Kas <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></button>
          </div>
        </div>

        <!-- 4 KPI Cards Keuangan Kompleks (Dinamis Berdasarkan Filter Tahun & Cabang) -->
        <div class="stats-grid financial-kpi-grid" style="padding: 0; margin-bottom: 24px;">
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Total Pemasukan (<span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span>)</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiIncomeVal" style="color:#10b981;">Rp {{ number_format($yearlyIncome ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">▲ Terverifikasi Sistem</span>
              <span id="finKpiBranchBadge">Gabungan 2 Cabang</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Biaya Operasional (<span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span>)</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiExpenseVal" style="color:#f87171;">Rp {{ number_format($yearlyExpense ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span>Listrik, WiFi, Servis AC, Opex</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Laba Bersih (<span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span>)</span>
              <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiNetVal" style="color:#0d9488;">Rp {{ number_format($yearlyNet ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">Arus Kas Bersih</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Rata-rata Bersih / Bulan</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiAvgVal">Rp {{ number_format(round(($yearlyNet ?? 0) / 12), 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info">Tahun <span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span></span>
              <span id="finKpiUnitCountLabel">16 Unit Kamar</span>
            </div>
          </div>
        </div>

        <!-- ===================================================================
             KURVA PERTUMBUHAN PENDAPATAN & ARUS KAS (VISUAL CANVAS BEZIER SPLINE)
             =================================================================== -->
        <div class="financial-curve-card" style="margin-bottom: 24px; padding: 24px; border-radius: 22px; background: var(--bg-surface); border: 1px solid var(--border-color); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.06);">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin: 0;">Kurva Pertumbuhan Pendapatan <span id="curveYearLabel">(Tahun {{ $selectedYear ?? 2026 }})</span></h3>
              </div>
              <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 4px 0 0 0;">Grafik kurva dinamis pendapatan sewa dan arus kas riil Kos Mas Robi</p>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
              <!-- Legend Kurva -->
              <div style="display: flex; align-items: center; gap: 14px; font-size: 0.8rem; font-weight: 700;">
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                  <span style="color: var(--text-secondary);">Pemasukan</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                  <span style="color: var(--text-secondary);">Pengeluaran</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span style="width: 12px; height: 12px; border-radius: 50%; background: #0d9488; display: inline-block;"></span>
                  <span style="color: var(--text-secondary);">Laba Bersih</span>
                </div>
              </div>

              <!-- Filter Periode Kurva -->
              <div class="filter-pills" id="curvePeriodFilters">
                <button type="button" class="filter-pill" onclick="switchRevenueCurve('30d', this)">30 Hari</button>
                <button type="button" class="filter-pill" onclick="switchRevenueCurve('6m', this)">6 Bulan</button>
                <button type="button" class="filter-pill active" onclick="switchRevenueCurve('1y', this)">1 Tahun (12 Bln)</button>
              </div>
            </div>
          </div>

          <!-- Canvas Interactive Curve -->
          <div style="position: relative; width: 100%; height: 280px;">
            <canvas id="revenueCurveCanvas" style="width: 100%; height: 100%; display: block;"></canvas>
            <div id="curveTooltip" style="position: absolute; display: none; pointer-events: none; background: rgba(15, 23, 42, 0.95); color: #ffffff; padding: 8px 12px; border-radius: 10px; font-size: 0.76rem; font-weight: 700; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.15); z-index: 10;">
              <div id="ttPeriod" style="color: #94a3b8; font-size: 0.7rem; margin-bottom: 2px;">Oktober 2026</div>
              <div id="ttIncome" style="color: #34d399;">Pemasukan: Rp 0</div>
              <div id="ttExpense" style="color: #f87171;">Pengeluaran: Rp 0</div>
              <div id="ttNet" style="color: #2dd4bf; border-top: 1px dashed rgba(255,255,255,0.2); margin-top: 4px; padding-top: 3px;">Net: Rp 0</div>
            </div>
          </div>

          <!-- Quick Insight Bar -->
          <div style="margin-top: 16px; padding: 12px 18px; border-radius: 14px; background: rgba(13, 148, 136, 0.08); border: 1px solid rgba(13, 148, 136, 0.2); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-primary);">
              <span id="finInsightText"><strong>Insight Keuangan 2 Cabang:</strong> Kos 1 menyumbang stabilitas arus kas mahasiswa, sedangkan Kos 2 memberikan margin sewa eksklusif tertinggi.</span>
            </div>
            <div id="finStatusCashLabel" style="font-size: 0.78rem; font-weight: 700; color: #0a5661;">
              Status Kas: Surplus Finansial
            </div>
          </div>
        </div>

        <!-- Tabel Buku Kas & Riwayat Transaksi -->
        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="financeSearchInput" placeholder="Cari nomor bukti, cabang kos, keterangan, kategori kas...">
            </div>
            <div class="filter-pills" id="financeTypeFilter">
              <button type="button" class="filter-pill active" data-ftype="all">Semua Transaksi</button>
              <button type="button" class="filter-pill" data-ftype="income">Pemasukan (Sewa)</button>
              <button type="button" class="filter-pill" data-ftype="expense">Pengeluaran Kos</button>
            </div>
          </div>

          <table class="custom-table" id="financesTable">
            <thead>
              <tr>
                <th>No. Bukti</th>
                <th>Tanggal</th>
                <th>Lokasi Cabang</th>
                <th>Keterangan Transaksi</th>
                <th>Kategori</th>
                <th>Nominal</th>
                <th>Metode Bayar</th>
                <th>Bukti / Kuitansi</th>
              </tr>
            </thead>
            <tbody id="financesTableBody">
              <!-- Dynamically populated by JS -->
            </tbody>
          </table>

          <div class="mobile-card-list" id="financesMobileCardList">
            <!-- Dynamically populated by JS -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE: TAB MANAJEMEN AKUN ADMIN OPERASIONAL (KHUSUS ROLE OWNER)
           ===================================================================== -->
      <div class="tab-pane" id="tab-admins">
        <!-- Banner Header Hero -->
        <div style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%); border-radius: 24px; padding: 26px 30px; margin-bottom: 24px; position: relative; overflow: hidden; box-shadow: 0 16px 40px -10px rgba(3, 35, 40, 0.35); color: #ffffff;">
          <div style="position:absolute; top:-40px; right:-40px; width:220px; height:220px; background:radial-gradient(circle, rgba(20, 184, 166, 0.25) 0%, transparent 70%); border-radius:50%; pointer-events:none;"></div>
          <div style="position:absolute; bottom:-60px; left:40px; width:180px; height:180px; background:radial-gradient(circle, rgba(244, 63, 94, 0.15) 0%, transparent 70%); border-radius:50%; pointer-events:none;"></div>
          
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 54px; height: 54px; background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.22); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; backdrop-filter: blur(4px);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #5eead4; margin-bottom: 4px;">
                  <span>Otoritas Pemilik Kos (Owner Access Only)</span>
                </div>
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.45rem; font-weight: 800; color: #ffffff; margin: 0 0 4px; letter-spacing: -0.02em;">Manajemen Staf Admin</h2>
                <p style="font-size: 0.82rem; color: rgba(255,255,255,0.8); margin: 0; max-width: 650px; line-height: 1.5;">
                  Ketahui secara transparan siapa saja yang memiliki akses login sebagai Admin operasional, buat akun staf baru, kelola password, atau cabut izin akses kapan pun.
                </p>
              </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
              <button type="button" class="btn-primary" onclick="openAddAdminModal()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; padding: 12px 22px; border-radius: 14px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35); cursor: pointer; transition: all 0.2s;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Tambah Admin Baru</span>
              </button>
            </div>
          </div>
        </div>

        <!-- 3 Stat Cards Ringkasan Akses -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
          <!-- Card 1: Total Admin -->
          <div class="stat-card" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 18px; padding: 20px;">
            <div class="stat-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span class="stat-card-title" style="font-size: 0.82rem; font-weight: 700; color: var(--text-secondary);">Total Akun Admin Operasional</span>
              <div class="stat-icon emerald" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statCardTotalAdmins" style="font-size: 1.6rem; font-weight: 800; color: #0a5661; margin-bottom: 6px;">{{ $totalAdminsCount ?? count($allAdmins ?? []) }} Akun Aktif</div>
            <div class="stat-card-meta" style="font-size: 0.74rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
              <span style="color: #059669; font-weight: 700;">● Hak Akses Terdaftar</span>
              <span>Memegang kredensial login</span>
            </div>
          </div>

          <!-- Card 2: Akun Owner Utama -->
          <div class="stat-card" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 18px; padding: 20px;">
            <div class="stat-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span class="stat-card-title" style="font-size: 0.82rem; font-weight: 700; color: var(--text-secondary);">Pemegang Hak Owner Utama</span>
              <div class="stat-icon" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(10, 86, 97, 0.12); color: #0a5661; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
            </div>
            <div class="stat-card-value" style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
            <div class="stat-card-meta" style="font-size: 0.74rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
              <span style="color: #0a5661; font-weight: 700;">● {{ Auth::user()->email }}</span>
              <span>Super Administrator</span>
            </div>
          </div>

          <!-- Card 3: Proteksi Hak Istimewa -->
          <div class="stat-card" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 18px; padding: 20px;">
            <div class="stat-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span class="stat-card-title" style="font-size: 0.82rem; font-weight: 700; color: var(--text-secondary);">Proteksi Keamanan Finansial</span>
              <div class="stat-icon" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
            </div>
            <div class="stat-card-value" style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Buku Kas Terproteksi</div>
            <div class="stat-card-meta" style="font-size: 0.74rem; color: var(--text-muted);">
              Admin tidak dapat melihat laporan arus kas & laba
            </div>
          </div>
        </div>

        <!-- Tabel Daftar Seluruh Admin -->
        <div class="content-card modern-table-card" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm);">
          <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; background: #fafafa;">
            <div>
              <h3 style="margin: 0 0 2px; font-size: 1.05rem; font-weight: 800; color: #0f172a;">Daftar Akun yang Memiliki Akses Admin</h3>
              <p style="margin: 0; font-size: 0.76rem; color: #64748b;">Hanya pengguna di bawah ini yang dapat masuk ke dashboard operasional kos.</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
              <input type="text" id="searchAdminInput" oninput="filterAdminTable()" placeholder="Cari nama atau email admin..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 0.8rem; width: 220px;">
            </div>
          </div>

          <div class="table-responsive">
            <table class="data-table" id="tableAdminsList" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Staf Admin</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Email Login</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Kontak WhatsApp</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Cakupan Izin</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Terdaftar</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Tindakan Owner</th>
                </tr>
              </thead>
              <tbody id="tbodyAdminRows">
                @forelse($allAdmins as $admin)
                @php
                  $adminWords = explode(' ', trim($admin->name));
                  $admInitials = count($adminWords) >= 2 
                    ? strtoupper(substr($adminWords[0], 0, 1) . substr($adminWords[1], 0, 1))
                    : strtoupper(substr($admin->name, 0, 2));
                  $adminPhoneClean = preg_replace('/[^0-9]/', '', $admin->phone ?? '');
                  if ($adminPhoneClean && str_starts_with($adminPhoneClean, '0')) {
                    $adminPhoneClean = '62' . substr($adminPhoneClean, 1);
                  }
                @endphp
                <tr class="admin-data-row" id="adminRow_{{ $admin->id }}" data-name="{{ strtolower($admin->name) }}" data-email="{{ strtolower($admin->email) }}" data-phone="{{ $admin->phone }}" style="border-bottom: 1px solid #f1f5f9;">
                  <td style="padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <div style="width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0;">
                        {{ $admInitials }}
                      </div>
                      <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;" class="adm-name-text">{{ $admin->name }}</div>
                        <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; color: #059669; font-weight: 700;">
                          <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                          <span>Admin Aktif</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td style="padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <span style="font-weight: 600; color: #334155; font-size: 0.84rem;" class="adm-email-text">{{ $admin->email }}</span>
                      <button type="button" onclick="navigator.clipboard.writeText('{{ $admin->email }}'); window.showNotification('success', 'Email tersalin!');" title="Salin Email" style="border: none; background: transparent; cursor: pointer; color: #94a3b8; padding: 2px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                      </button>
                    </div>
                  </td>
                  <td style="padding: 14px 20px;">
                    @if($admin->phone)
                      <a href="https://wa.me/{{ $adminPhoneClean }}" target="_blank" class="adm-phone-link" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #0a5661; font-weight: 700; text-decoration: none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
                        <span>{{ $admin->phone }}</span>
                      </a>
                    @else
                      <span class="adm-phone-link" style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">Belum diisi</span>
                    @endif
                  </td>
                  <td style="padding: 14px 20px;">
                    <div style="display: inline-flex; flex-direction: column; gap: 4px;">
                      <span style="font-size: 0.74rem; background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 6px; font-weight: 700;">Operasional Harian</span>
                      <span style="font-size: 0.7rem; color: #64748b;">Kamar, Penghuni & Servis</span>
                    </div>
                  </td>
                  <td style="padding: 14px 20px;">
                    <span style="font-size: 0.8rem; color: #64748b;">{{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}</span>
                  </td>
                  <td style="padding: 14px 20px; text-align: right;">
                    <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: flex-end;">
                      <button type="button" class="btn-secondary" onclick="openEditAdminModal({{ json_encode($admin) }})" style="font-size: 0.76rem; padding: 6px 12px; border-radius: 8px; font-weight: 700;" title="Edit Data & Ganti Password">
                        Edit
                      </button>
                      <button type="button" class="btn-secondary" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')" style="font-size: 0.76rem; padding: 6px 10px; border-radius: 8px; font-weight: 700; color: #ef4444; border-color: #fecaca;" title="Hapus Akses Admin">
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr id="emptyAdminRow">
                  <td colspan="6" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <div style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Belum Ada Staf Admin Tambahan</div>
                    <div style="font-size: 0.8rem; color: #64748b;">Klik tombol 'Tambah Admin Baru' di atas untuk memberikan akses kepada staf operasional kos.</div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      <!-- =====================================================================
           PANE 5: TAB KELUHAN & PERAWATAN (DENGAN PERBEDAAN 2 KOS MODERN)
           ===================================================================== -->
      <div class="tab-pane" id="tab-maintenance">
        <div class="section-header" style="justify-content: flex-end;">
          <button type="button" class="btn-primary-action" id="btnNewRepairTicket">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Buat Tiket Komplain</span>
          </button>
        </div>

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (KELUHAN & SERVIS) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="maintenanceLocationFilters">
            <button type="button" class="filter-pill active" data-mloc="all">Semua Cabang</button>
            <button type="button" class="filter-pill" data-mloc="Lokasi 1">Servis <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></button>
            <button type="button" class="filter-pill" data-mloc="Lokasi 2">Servis <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></button>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="maintenanceSearchInput" placeholder="Cari tiket, keluhan, cabang, atau nomor kamar...">
            </div>
            <div class="filter-pills" id="repairStatusFilter">
              <button type="button" class="filter-pill active" data-rstatus="all">Semua</button>
              <button type="button" class="filter-pill" data-rstatus="pending">Menunggu</button>
              <button type="button" class="filter-pill" data-rstatus="progress">Dalam Proses</button>
              <button type="button" class="filter-pill" data-rstatus="done">Selesai</button>
            </div>
          </div>

          <table class="custom-table" id="maintenanceTable">
            <thead>
              <tr>
                <th>Tiket #</th>
                <th>Kamar & Pelapor</th>
                <th>Lokasi Cabang & PIC</th>
                <th>Deskripsi Keluhan</th>
                <th>Tgl Lapor</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Update Status</th>
              </tr>
            </thead>
            <tbody id="maintenanceTableBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>

          <div class="mobile-card-list" id="maintenanceMobileCardList">
            <!-- Dynamically populated -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 5B: TAB PERSETUJUAN PINDAH KAMAR (DENGAN PERBEDAAN 2 KOS MODERN)
           ===================================================================== -->
      <div class="tab-pane" id="tab-transfers">
        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (PINDAH KAMAR) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="transferLocationFilters">
            <button type="button" class="filter-pill active" onclick="filterTransferLocation('all', this)">Semua Mutasi</button>
            <button type="button" class="filter-pill" onclick="filterTransferLocation('Lokasi 1', this)">Internal <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></button>
            <button type="button" class="filter-pill" onclick="filterTransferLocation('Lokasi 2', this)">Internal <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></button>
            <button type="button" class="filter-pill" onclick="filterTransferLocation('Lintas Cabang', this)">Lintas Cabang (<span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ⇄ <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>)</button>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="transferSearchInput" placeholder="Cari penghuni, cabang, atau nomor kamar..." onkeyup="filterTransferTable()">
            </div>
            <div class="filter-pills" id="transferStatusFilter">
              <button type="button" class="filter-pill active" onclick="filterTransferStatus('all', this)">Semua</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('pending', this)">Menunggu ({{ $pendingTransfersCount ?? 0 }})</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('approved', this)">Disetujui</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('rejected', this)">Ditolak</button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="custom-table" id="transfersTable">
            <thead>
              <tr>
                <th>Tgl Pengajuan</th>
                <th>Penghuni & Kontak</th>
                <th>Jenis & Cabang Mutasi</th>
                <th>Kamar Asal → Tujuan</th>
                <th>Alasan & Rencana</th>
                <th>Status</th>
                <th>Aksi Pengelola</th>
              </tr>
            </thead>
            <tbody id="transfersTableBody">
              @forelse($roomTransfers ?? [] as $tr)
                @php
                  $fromNum = $tr->currentRoom->number ?? '101';
                  $toNum = $tr->targetRoom->number ?? '102';
                  $fromLoc = $tr->currentRoom->location ?? (intval($fromNum) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                  $toLoc = $tr->targetRoom->location ?? (intval($toNum) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                  $isCrossBranch = $fromLoc !== $toLoc;
                  $transferLocCategory = $isCrossBranch ? 'Lintas Cabang' : $fromLoc;
                  $priceDiff = (int)($tr->targetRoom->price ?? 0) - (int)($tr->currentRoom->price ?? 0);
                @endphp
                <tr data-status="{{ $tr->status }}" data-location="{{ $transferLocCategory }}" data-from-loc="{{ $fromLoc }}" data-to-loc="{{ $toLoc }}" data-search="{{ strtolower(($tr->user->name ?? $tr->tenant->name ?? '') . ' ' . $fromNum . ' ' . $toNum . ' ' . $fromLoc . ' ' . $toLoc . ' ' . $transferLocCategory . ' ' . $tr->reason) }}">
                  <td>
                    <div style="font-weight: 700;">{{ $tr->created_at->format('d/m/Y') }}</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $tr->created_at->format('H:i') }} WIB</div>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--brand-primary);">{{ $tr->user->name ?? $tr->tenant->name ?? 'Anak Kos' }}</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $tr->user->phone ?? $tr->tenant->phone ?? '-' }}</div>
                  </td>
                  <td>
                    @if($isCrossBranch)
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd;">
                        Lintas Cabang (@if($fromLoc === 'Lokasi 1')<span data-branch-name="lokasi_1">{{ $branch1Name }}</span> → <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>@else<span data-branch-name="lokasi_2">{{ $branch2Name }}</span> → <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>@endif)
                      </span>
                    @elseif($fromLoc === 'Lokasi 2')
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;">
                        Internal <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                      </span>
                    @else
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;">
                        Internal <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                      </span>
                    @endif
                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 4px;">
                      @if($priceDiff > 0)
                        <span style="color: #059669; font-weight: 700;">▲ Upgrade (+Rp {{ number_format($priceDiff, 0, ',', '.') }}/bln)</span>
                      @elseif($priceDiff < 0)
                        <span style="color: #d97706; font-weight: 700;">▼ Penyesuaian (-Rp {{ number_format(abs($priceDiff), 0, ',', '.') }}/bln)</span>
                      @else
                        <span>● Tarif Setara</span>
                      @endif
                    </div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span class="badge" style="background: var(--bg-surface); color: var(--text-primary); font-weight: 700; padding: 4px 8px; border-radius: 6px;">#{{ $fromNum }}</span>
                      <span style="color: var(--brand-primary); font-weight: 800;">→</span>
                      <span class="badge" style="background: var(--brand-soft-bg); color: var(--brand-primary); font-weight: 800; padding: 4px 8px; border-radius: 6px;">#{{ $toNum }}</span>
                    </div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 3px;">
                      {{ $tr->targetRoom->type ?? '' }} (Lt {{ $tr->targetRoom->floor ?? '1' }})
                    </div>
                  </td>
                  <td style="max-width: 240px;">
                    <div style="font-size: 0.82rem; line-height: 1.4;">{{ $tr->reason }}</div>
                    @if($tr->preferred_date)
                      <div style="font-size: 0.72rem; color: #0a5661; font-weight: 600; margin-top: 3px;">Rencana: {{ \Carbon\Carbon::parse($tr->preferred_date)->format('d M Y') }}</div>
                    @endif
                    @if($tr->admin_note)
                      <div style="font-size: 0.72rem; color: #059669; font-weight: 600; margin-top: 3px;">Catatan: {{ $tr->admin_note }}</div>
                    @endif
                  </td>
                  <td>
                    @if($tr->status === 'pending')
                      <span class="status-pill status-due">Menunggu</span>
                    @elseif($tr->status === 'approved')
                      <span class="status-pill status-occupied">Disetujui</span>
                    @else
                      <span class="status-pill status-repair">Ditolak</span>
                    @endif
                  </td>
                  <td>
                    @if($tr->status === 'pending')
                      <div style="display: flex; gap: 6px;">
                        <button type="button" class="btn-sm" style="background: #10b981; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem;" onclick="handleTransferAction({{ $tr->id }}, 'approved')">
                          Izinkan
                        </button>
                        <button type="button" class="btn-sm" style="background: #ef4444; color: #fff; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem;" onclick="handleTransferAction({{ $tr->id }}, 'rejected')">
                          Tolak
                        </button>
                      </div>
                    @else
                      <span style="font-size: 0.75rem; color: var(--text-muted);">Selesai Diproses</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted);">
                    Belum ada pengajuan pindah kamar dari penghuni kos.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="tab-settings">
        <!-- ====== SUPER PREMIUM SETTINGS HEADER ====== -->
        <div class="settings-hero-banner" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%); border-radius: 24px; padding: 24px 30px; margin-bottom: 24px; position: relative; overflow: hidden; box-shadow: 0 16px 40px -10px rgba(3, 35, 40, 0.35);">
          <!-- Decorative blobs -->
          <div style="position:absolute; top:-40px; right:-40px; width:200px; height:200px; background:rgba(255,255,255,0.05); border-radius:50%; pointer-events:none;"></div>
          <div style="position:absolute; bottom:-60px; left:60px; width:150px; height:150px; background:rgba(20, 184, 166, 0.1); border-radius:50%; pointer-events:none;"></div>
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 16px;">
              <div class="settings-hero-icon" style="width: 52px; height: 52px; background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; backdrop-filter: blur(4px);"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
              <div>
                <h2 class="settings-hero-title" style="font-size: 1.4rem; font-weight: 900; color: #fff; margin: 0; letter-spacing: -0.3px;">Pusat Pengaturan Properti</h2>
                <p class="settings-hero-desc" style="font-size: 0.82rem; color: rgba(255,255,255,0.6); margin: 4px 0 0; font-weight: 400;">Kelola konten, foto, peta, profil 2 lokasi & jaringan WiFi kost</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ====== PREMIUM SUB-TAB NAV ====== -->
        <div style="margin-bottom: 26px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 6px;">
          <div class="settings-segmented-nav" id="settingsSubTabNav" style="display: flex; gap: 4px; background: transparent; padding: 0; border-radius: 0;">
            <button type="button" class="settings-nav-btn btn-settings-subtab active" data-subtab="subtab-landing" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Landing Page</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-location" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Titik Lokasi</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-profile" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Tata Tertib</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-wifi" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Jaringan WiFi</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-survey" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Formulir Calon Penghuni</span>
            </button>
          </div>
        </div>

        @php
          $lc = $setting->landing_content ?? [];
          $bGallery = $lc['bento_gallery'] ?? [
              'assets/images/hero_scandinavian_room.jpg',
              'assets/images/rooms/room_2.jpg',
              'assets/images/rooms/room_3.jpg',
              'assets/images/kos_exterior.jpg'
          ];
        @endphp

        <!-- =========================================================================
             SUBTAB 1: KELOLA TAMPILAN & FOTO LANDING PAGE
             ========================================================================= -->
        <div class="settings-subtab-pane active" id="subtab-landing">
          <form id="landingPageEditorForm" style="display: flex; flex-direction: column; gap: 24px;" enctype="multipart/form-data">

            <!-- 1. HERO SECTION CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge coral"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">1. Hero Banner Utama (Bagian Teratas)</h3>
                    <div class="settings-card-subtitle">Atur judul headline, deskripsi pengantar, tombol CTA, dan foto utama gedung kos</div>
                  </div>
                </div>
                <span style="font-size: 0.76rem; background: var(--bg-card); padding: 5px 12px; border-radius: 9999px; color: var(--text-secondary); font-weight: 700; border: 1px solid var(--border-color);">Tampilan Pertama Pengunjung</span>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                  <div class="form-group">
                    <label for="lp_hero_title" style="font-weight: 700;">Judul Utama Hero (Gunakan Enter untuk Baris Baru)</label>
                    <textarea id="lp_hero_title" name="hero_title" class="form-control" rows="2" placeholder="Contoh: Find Your&#10;Dream Home" style="font-size: 1rem; font-weight: 700;">{{ $lc['hero_title'] ?? "Find Your\nDream Home" }}</textarea>
                  </div>
                  <div class="form-group">
                    <label for="lp_hero_desc" style="font-weight: 700;">Deskripsi Singkat Hero</label>
                    <textarea id="lp_hero_desc" name="hero_desc" class="form-control" rows="3" placeholder="Deskripsi hunian kost eksklusif...">{{ $lc['hero_desc'] ?? 'Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis di Sleman, DI Yogyakarta.' }}</textarea>
                  </div>
                  <div class="form-group">
                    <label for="lp_hero_btn_text" style="font-weight: 700;">Teks Tombol Aksi (CTA)</label>
                    <input type="text" id="lp_hero_btn_text" name="hero_btn_text" class="form-control" value="{{ $lc['hero_btn_text'] ?? 'Jelajahi Kamar' }}">
                  </div>
                </div>

                <!-- Foto Utama Hero / Eksterior -->
                <div class="settings-upload-drop">
                  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-weight: 800; font-size: 0.88rem; color: var(--text-primary);">Foto Utama Gedung / Eksterior</span>
                    <span style="font-size: 0.72rem; color: var(--text-secondary);">Format: JPG, PNG, WEBP</span>
                  </div>
                  <div class="settings-preview-img-wrap">
                    <img id="previewHeroImage" src="{{ asset($lc['hero_image'] ?? 'assets/images/kos_exterior.jpg') }}" alt="Preview Foto Hero">
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 8px;">
                    <input type="file" id="lp_hero_image_file" name="hero_image_file" accept="image/*" class="form-control" style="font-size: 0.82rem; padding: 8px;">
                    <div style="font-size: 0.76rem; color: var(--text-secondary); text-align: left; line-height: 1.4;">
                      <em>Tips:</em> Bagian tengah foto akan otomatis disatukan secara mulus dengan gradasi warna latar belakang Landing Page.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. BENTO & FASILITAS SECTION CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">2. Bento Keunggulan & 4 Galeri Foto Kost</h3>
                    <div class="settings-card-subtitle">Pengaturan teks keunggulan kos serta 4 slot foto galeri yang dapat diganti kapan saja</div>
                  </div>
                </div>
                <span style="font-size: 0.76rem; background: var(--bg-card); padding: 5px 12px; border-radius: 9999px; color: var(--text-secondary); font-weight: 700; border: 1px solid var(--border-color);">4 Slot Galeri Real-time</span>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                  <label for="lp_bento_tag" style="font-weight: 700;">Judul Tagline (Warna Coral / Merah)</label>
                  <input type="text" id="lp_bento_tag" name="bento_tag" class="form-control" value="{{ $lc['bento_tag'] ?? 'Kenyamanan Maksimal' }}">
                </div>
                <div class="form-group">
                  <label for="lp_bento_title" style="font-weight: 700;">Judul Utama Keunggulan</label>
                  <input type="text" id="lp_bento_title" name="bento_title" class="form-control" value="{{ $lc['bento_title'] ?? 'Untuk Istirahat & Produktivitas' }}">
                </div>
              </div>

              <div class="form-group" style="margin-bottom: 24px;">
                <label for="lp_bento_desc" style="font-weight: 700;">Deskripsi Paragraf Keunggulan</label>
                <textarea id="lp_bento_desc" name="bento_desc" class="form-control" rows="2">{{ $lc['bento_desc'] ?? 'Setiap jengkal hunian kami rancang dengan seksama untuk memastikan penghuni mendapatkan istirahat berkualitas dan fokus maksimal dalam menuntut ilmu maupun berkarier.' }}</textarea>
              </div>

              <!-- 4 FOTO GALERI BENTO GRID -->
              <div style="margin-top: 10px;">
                <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                  <span>4 Foto Galeri Kamar & Fasilitas Kost</span>
                </h4>

                <div class="settings-gallery-grid">
                  @for($g = 0; $g < 4; $g++)
                    @php
                      $curGalImg = $bGallery[$g] ?? 'assets/images/rooms/room_1.jpg';
                      $galLabels = ['Galeri 1 (Interior)', 'Galeri 2 (Kamar Tidur)', 'Galeri 3 (Fasilitas/KM)', 'Galeri 4 (Area Gedung)'];
                    @endphp
                    <div class="gallery-photo-card">
                      <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: var(--text-primary);">{{ $galLabels[$g] }}</span>
                        <span style="font-size: 0.7rem; color: var(--primary); font-weight: 700;">Foto #{{ $g + 1 }}</span>
                      </div>
                      <div class="settings-preview-img-wrap" style="height: 140px; margin-bottom: 8px;">
                        <img id="previewBentoGallery_{{ $g }}" src="{{ asset($curGalImg) }}" alt="Preview Galeri {{ $g + 1 }}">
                      </div>
                      <input type="file" id="lp_gallery_file_{{ $g }}" name="gallery_file_{{ $g }}" accept="image/*" class="form-control gal-file-input" data-preview-target="previewBentoGallery_{{ $g }}" style="font-size: 0.76rem; padding: 6px;">
                    </div>
                  @endfor
                </div>
              </div>
            </div>

            <!-- 3. KUTIPAN PENGELOLA & FOOTER CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">3. Kutipan Komitmen Pengelola & Footer</h3>
                    <div class="settings-card-subtitle">Profil pemilik properti serta teks komitmen layanan yang dibaca calon penghuni</div>
                  </div>
                </div>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div class="form-group">
                  <label for="lp_founder_name" style="font-weight: 700;">Nama Pengelola / Owner</label>
                  <input type="text" id="lp_founder_name" name="founder_name" class="form-control" value="{{ $lc['founder_name'] ?? ($setting->owner_name ?? 'Mas Robi') }}">
                </div>
                <div class="form-group">
                  <label for="lp_founder_role" style="font-weight: 700;">Jabatan / Peran</label>
                  <input type="text" id="lp_founder_role" name="founder_role" class="form-control" value="{{ $lc['founder_role'] ?? 'Owner & Pengelola Properti' }}">
                </div>
                <div class="form-group">
                  <label for="lp_founder_avatar" style="font-weight: 700;">Inisial Avatar Bulat</label>
                  <input type="text" id="lp_founder_avatar" name="founder_avatar" class="form-control" value="{{ $lc['founder_avatar'] ?? 'MR' }}" maxlength="4">
                </div>
              </div>

              <div class="form-group" style="margin-bottom: 18px;">
                <label for="lp_founder_quote" style="font-weight: 700;">Pernyataan Komitmen Pengelola</label>
                <textarea id="lp_founder_quote" name="founder_quote" class="form-control" rows="3">{{ $lc['founder_quote'] ?? 'Kenyamanan, kebersihan, dan keamanan penghuni adalah komitmen utama kami. Kami memastikan setiap anak kos merasakan suasana tinggal yang hangat layaknya di rumah sendiri, dengan fasilitas modern tanpa repot dan dukungan pengelola yang selalu siap membantu kapan pun dibutuhkan.' }}</textarea>
              </div>

              <div class="form-group">
                <label for="lp_footer_desc" style="font-weight: 700;">Deskripsi Singkat Footer</label>
                <textarea id="lp_footer_desc" name="footer_desc" class="form-control" rows="2">{{ $lc['footer_desc'] ?? 'Hunian kost eksklusif dengan fasilitas lengkap, kamar ber-AC, kamar mandi dalam, WiFi fiber cepat 100 Mbps, dan keamanan 24 jam di kawasan strategis.' }}</textarea>
              </div>
            </div>

            <!-- Tombol Simpan Landing Page Action Bar -->
            <div style="display: flex; gap: 14px; align-items: center; padding: 12px 0; flex-wrap: wrap;">
              <button type="submit" id="btnSaveLandingPage" class="btn-primary" style="padding: 13px 32px; font-size: 0.95rem; font-weight: 800; display: inline-flex; align-items: center; gap: 10px; border-radius: 14px; box-shadow: 0 4px 16px rgba(37,99,235,0.3);">
                <span>Simpan Seluruh Tampilan Landing Page</span>
              </button>
              <a href="{{ route('home') }}" target="_blank" class="btn-secondary" style="padding: 13px 22px; font-weight: 700; border-radius: 14px;">
                <span>Lihat Hasil Langsung ↗</span>
              </a>
            </div>

          </form>
        </div>

        <!-- =========================================================================
             SUBTAB 2: KELOLA 2 TITIK LOKASI (CABANG), GOOGLE MAPS & FASILITAS
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-location" style="display: none;">
          <form id="settingsProfileForm" style="display: flex; flex-direction: column; gap: 24px;">

            @php
              $allBranches = $setting->branches ?? [];
              $b1 = $allBranches['lokasi_1'] ?? [];
              $b2 = $allBranches['lokasi_2'] ?? [];
            @endphp

            <!-- CARD SELECTOR TITIK LOKASI (CABANG) -->
            <div style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%); border-radius: 20px; padding: 22px 26px; box-shadow: 0 14px 30px -8px rgba(3, 35, 40, 0.35); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
              <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                <div>
                  <h3 style="color: #fff; font-size: 1.15rem; font-weight: 800; margin: 0;">Sistem Manajemen 2 Titik Lokasi Kos</h3>
                  <p style="color: rgba(255,255,255,0.7); font-size: 0.78rem; margin: 3px 0 0;">Setiap lokasi memiliki fasilitas, tipe kamar, alamat & link Google Maps sendiri</p>
                </div>
              </div>
              <div style="display: flex; gap: 8px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.18);">
                <button type="button" class="branch-pill-tab active" id="btnTabBranch1" onclick="switchBranchSettings('lokasi_1')" style="display: flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; border: none; font-size: 0.84rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0a5661; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                  <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                </button>
                <button type="button" class="branch-pill-tab" id="btnTabBranch2" onclick="switchBranchSettings('lokasi_2')" style="display: flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; border: none; font-size: 0.84rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: transparent; color: rgba(255,255,255,0.8);">
                  <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                </button>
              </div>
            </div>

            <!-- ================= PANEL LOKASI 1: KAMPUS UTAMA ================= -->
            <div id="panelBranch1" class="branch-settings-panel" style="display: flex; flex-direction: column; gap: 20px;">
              <div class="settings-modern-card">
                <div class="settings-card-header">
                  <div class="settings-card-title-group">
                    <div class="settings-card-icon-badge" style="background: rgba(10,86,97,0.1); color: #0a5661;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div>
                      <h3 class="settings-card-title">Pengaturan <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></h3>
                      <div class="settings-card-subtitle">Alamat resmi, peta Google Maps, dan kontak WhatsApp untuk Cabang 1</div>
                    </div>
                  </div>
                </div>

                <div class="form-row" style="gap: 16px;">
                  <div class="form-group" style="flex: 2;">
                    <label style="font-weight: 700; font-size: 0.82rem;">Nama Resmi Cabang 1 *</label>
                    <input type="text" id="b1_name" class="form-control" value="{{ $b1['name'] ?? 'Kos Mas Robi 1 - Kampus Utama' }}" placeholder="Contoh: Kos Mas Robi 1 - Kampus Utama" style="border-radius: 12px;">
                  </div>
                  <div class="form-group" style="flex: 2;">
                    <label style="font-weight: 700; font-size: 0.82rem;">Tagline / Keunggulan Utama *</label>
                    <input type="text" id="b1_tagline" class="form-control" value="{{ $b1['tagline'] ?? 'Hunian Tenang, Nyaman & 3 Menit ke Kampus Utama' }}" placeholder="Contoh: Hunian Tenang, Nyaman..." style="border-radius: 12px;">
                  </div>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                  <label style="font-weight: 700; font-size: 0.82rem;">Alamat Lengkap Cabang 1 *</label>
                  <textarea id="b1_address" class="form-control" rows="2" placeholder="Jl. Pandega Marta No. 88, Caturtunggal, Sleman, Yogyakarta" style="border-radius: 12px;">{{ $b1['address'] ?? ($setting->kos_address ?? 'Jl. Pandega Marta No. 88, Caturtunggal, Depok, Sleman, D.I. Yogyakarta') }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                  <label style="font-weight: 700; font-size: 0.82rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>Link Google Maps Cabang 1</span>
                    <span style="font-size: 0.72rem; color: #047857; background: #dcfce7; padding: 2px 8px; border-radius: 6px; font-weight: 600;">Cukup Tempel Link Maps</span>
                  </label>
                  <input type="text" id="b1_gmaps_url" class="form-control" placeholder="https://maps.app.goo.gl/... atau link dari browser" value="{{ $b1['gmaps_url'] ?? ($setting->gmaps_url ?: $setting->gmaps_embed) }}" style="border-radius: 12px;">
                  <input type="hidden" id="b1_gmaps_embed" value="{{ $b1['gmaps_embed'] ?? $setting->gmaps_embed }}">
                </div>

                <!-- Tipe Kamar & Fasilitas Cabang 1 -->
                <div class="form-row" style="gap: 16px; margin-top: 16px;">
                  <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #0a5661;">Daftar Tipe Kamar Cabang 1 (Pisahkan koma)</label>
                    <textarea id="b1_room_types" class="form-control" rows="3" style="border-radius: 12px; font-size: 0.84rem;">{{ is_array($b1['room_types'] ?? null) ? implode(', ', $b1['room_types']) : 'VIP AC (KM Dalam), Deluxe AC, Standard Fan (KM Luar)' }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px;">Otomatis menjadi pilihan tipe saat menambah kamar di Cabang 1.</span>
                  </div>
                  <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #0a5661;">Daftar Fasilitas Khas Cabang 1 (Pisahkan koma)</label>
                    <textarea id="b1_facilities" class="form-control" rows="3" style="border-radius: 12px; font-size: 0.84rem;">{{ is_array($b1['facilities'] ?? null) ? implode(', ', $b1['facilities']) : 'AC Dingin & Hemat Listrik, Kamar Mandi Dalam & Water Heater, WiFi Fiber Ultra Cepat 100 Mbps, Dapur Bersama Komplit, Parkir Mobil & Motor Luas Beratap, Keamanan CCTV 24 Jam & One-Gate System, Rooftop Jemuran & Ruang Santai' }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px;">Tampil di detail hunian & landing page untuk Cabang 1.</span>
                  </div>
                </div>

                <!-- Live Preview Peta Cabang 1 -->
                <div class="form-group" style="margin-top: 16px;">
                  <label style="font-weight: 800; font-size: 0.84rem; display: flex; align-items: center; gap: 8px;">
                    <span style="display:inline-block; width:9px; height:9px; border-radius:50%; background:#10b981;"></span>
                    Pratinjau Peta Google Maps Cabang 1:
                  </label>
                  <div style="width: 100%; height: 260px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface); margin-top: 6px;">
                    <iframe id="iframePreviewB1" src="{{ $setting->getBranchEmbedUrl('lokasi_1') }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                  </div>
                </div>
              </div>
            </div>

            <!-- ================= PANEL LOKASI 2: RINGROAD EXCLUSIVE ================= -->
            <div id="panelBranch2" class="branch-settings-panel" style="display: none; flex-direction: column; gap: 20px;">
              <div class="settings-modern-card">
                <div class="settings-card-header">
                  <div class="settings-card-title-group">
                    <div class="settings-card-icon-badge" style="background: rgba(13,148,136,0.1); color: #0d9488;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div>
                      <h3 class="settings-card-title">Pengaturan <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></h3>
                      <div class="settings-card-subtitle">Alamat resmi, peta Google Maps, dan kontak WhatsApp untuk Cabang 2</div>
                    </div>
                  </div>
                </div>

                <div class="form-row" style="gap: 16px;">
                  <div class="form-group" style="flex: 2;">
                    <label style="font-weight: 700; font-size: 0.82rem;">Nama Resmi Cabang 2 *</label>
                    <input type="text" id="b2_name" class="form-control" value="{{ $b2['name'] ?? 'Kos Mas Robi 2 - Ringroad Exclusive' }}" placeholder="Contoh: Kos Mas Robi 2 - Ringroad Exclusive" style="border-radius: 12px;">
                  </div>
                  <div class="form-group" style="flex: 2;">
                    <label style="font-weight: 700; font-size: 0.82rem;">Tagline / Keunggulan Utama *</label>
                    <input type="text" id="b2_tagline" class="form-control" value="{{ $b2['tagline'] ?? 'Smart Living, Co-Working Space & Fasilitas Modern' }}" placeholder="Contoh: Smart Living, Co-Working Space..." style="border-radius: 12px;">
                  </div>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                  <label style="font-weight: 700; font-size: 0.82rem;">Alamat Lengkap Cabang 2 *</label>
                  <textarea id="b2_address" class="form-control" rows="2" placeholder="Jl. Kaliurang KM 12, Sardonoharjo, Ngaglik, Sleman, Yogyakarta" style="border-radius: 12px;">{{ $b2['address'] ?? 'Jl. Kaliurang KM 12, Sardonoharjo, Ngaglik, Sleman, D.I. Yogyakarta' }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                  <label style="font-weight: 700; font-size: 0.82rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>Link Google Maps Cabang 2</span>
                    <span style="font-size: 0.72rem; color: #047857; background: #dcfce7; padding: 2px 8px; border-radius: 6px; font-weight: 600;">Cukup Tempel Link Maps</span>
                  </label>
                  <input type="text" id="b2_gmaps_url" class="form-control" placeholder="https://maps.app.goo.gl/... atau link dari browser" value="{{ $b2['gmaps_url'] ?? 'https://maps.google.com/?q=Jl.+Kaliurang+KM+12+Yogyakarta' }}" style="border-radius: 12px;">
                  <input type="hidden" id="b2_gmaps_embed" value="{{ $b2['gmaps_embed'] ?? '' }}">
                </div>

                <!-- Tipe Kamar & Fasilitas Cabang 2 -->
                <div class="form-row" style="gap: 16px; margin-top: 16px;">
                  <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #0d9488;">Daftar Tipe Kamar Cabang 2 (Pisahkan koma)</label>
                    <textarea id="b2_room_types" class="form-control" rows="3" style="border-radius: 12px; font-size: 0.84rem;">{{ is_array($b2['room_types'] ?? null) ? implode(', ', $b2['room_types']) : 'Executive Suite (Balkon), Superior AC Plus, Studio Mezzanine' }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px;">Otomatis menjadi pilihan tipe saat menambah kamar di Cabang 2.</span>
                  </div>
                  <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 700; font-size: 0.82rem; color: #0d9488;">Daftar Fasilitas Khas Cabang 2 (Pisahkan koma)</label>
                    <textarea id="b2_facilities" class="form-control" rows="3" style="border-radius: 12px; font-size: 0.84rem;">{{ is_array($b2['facilities'] ?? null) ? implode(', ', $b2['facilities']) : 'Smart Digital Door Lock (Kartu & PIN), Balkon Pribadi dengan View Menarik, Smart Android TV & Meja Kerja Ergonomis, Co-Working Lounge & Area Diskusi, Mini Gym & Gazebo Santai Outdoor, Pantry Bersih & Dispenser Tiap Lantai, Free Pembersihan Kamar 1x per Minggu' }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 4px;">Tampil di detail hunian & landing page untuk Cabang 2.</span>
                  </div>
                </div>

                <!-- Live Preview Peta Cabang 2 -->
                <div class="form-group" style="margin-top: 16px;">
                  <label style="font-weight: 800; font-size: 0.84rem; display: flex; align-items: center; gap: 8px;">
                    <span style="display:inline-block; width:9px; height:9px; border-radius:50%; background:#10b981;"></span>
                    Pratinjau Peta Google Maps Cabang 2:
                  </label>
                  <div style="width: 100%; height: 260px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface); margin-top: 6px;">
                    <iframe id="iframePreviewB2" src="{{ $setting->getBranchEmbedUrl('lokasi_2') }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4 POI / AKSES STRATEGIS KOS CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">4 Poin Akses & Keunggulan Strategis</h3>
                    <div class="settings-card-subtitle">4 kartu akses strategis yang tampil mendampingi peta pada Landing Page</div>
                  </div>
                </div>
              </div>

              @php
                $highlights = $setting->location_highlights ?? [];
              @endphp

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;" id="locationHighlightsEditorList">
                @for($i = 0; $i < 4; $i++)
                  @php
                    $hl = $highlights[$i] ?? [
                      'icon' => ['', '', '', ''][$i] ?? '',
                      'title' => '',
                      'desc' => ''
                    ];
                  @endphp
                  <div class="highlight-edit-item" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                      <div style="width: 58px; flex-shrink: 0;">
                        <label style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 2px;">Ikon</label>
                        <input type="text" class="form-control hl-icon" value="{{ $hl['icon'] ?? '' }}" style="text-align: center; font-size: 1.15rem; padding: 4px;" maxlength="4" placeholder="">
                      </div>
                      <div style="flex: 1;">
                        <label style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 2px;">Judul Poin Keunggulan #{{ $i + 1 }}</label>
                        <input type="text" class="form-control hl-title" value="{{ $hl['title'] ?? '' }}" placeholder="Contoh: 3 – 5 Menit ke Kawasan Kampus">
                      </div>
                    </div>
                    <div>
                      <label style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 2px;">Deskripsi Singkat</label>
                      <textarea class="form-control hl-desc" rows="2" placeholder="Contoh: Akses sangat dekat ke kampus dan pusat kuliner.">{{ $hl['desc'] ?? '' }}</textarea>
                    </div>
                  </div>
                @endfor
              </div>

              <div style="display: flex; gap: 14px; align-items: center; margin-top: 20px;">
                <button type="submit" class="btn-primary" style="padding: 12px 28px; font-weight: 800; border-radius: 12px; display: flex; align-items: center; gap: 8px;">
                  Simpan Seluruh Pengaturan 2 Lokasi & Peta
                </button>
              </div>
            </div>

          </form>
        </div>

        <!-- =========================================================================
             SUBTAB 3: TATA TERTIB & PERATURAN KOS
             (Informasi Properti & Pengelola dipindahkan ke modal Akun Pengelola)
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-profile" style="display: none;">
          <div style="display: grid; grid-template-columns: 1fr; gap: 24px; max-width: 960px; margin: 0 auto;">

            <!-- Tata Tertib & Peraturan Kos Card -->
            <div class="settings-modern-card" style="margin-bottom: 0;">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge" style="background: rgba(13,148,136,0.12); color: #0d9488;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">Tata Tertib & Peraturan Kos</h3>
                    <div class="settings-card-subtitle">Kelola poin peraturan kos yang otomatis tampil di Portal Pelanggan</div>
                  </div>
                </div>
              </div>

              @php
                $kosRulesList = $setting->kos_rules ?? [
                  'Pembayaran sewa dilakukan maksimal pada tanggal jatuh tempo setiap bulannya.',
                  'Tamu lawan jenis dilarang menginap di dalam kamar. Jam kunjung tamu maksimal pukul 22.00 WIB.',
                  'Jaga ketenangan, kebersihan kamar, dan fasilitas bersama (dapur, lorong, area parkir).',
                  'Matikan AC, lampu, dan keran air saat meninggalkan kamar dalam waktu lama.',
                  'Dilarang keras membawa barang terlarang, senjata tajam, narkoba, atau minuman keras ke lingkungan kos.',
                ];
              @endphp

              <form id="settingsKosRulesForm" onsubmit="saveKosRulesToServer(event)" style="display: flex; flex-direction: column; gap: 12px;">
                <div id="kosRulesEditorList" style="display: flex; flex-direction: column; gap: 10px;">
                  @foreach($kosRulesList as $rIdx => $ruleText)
                    <div class="kos-rule-edit-item" style="display: flex; align-items: center; gap: 10px; background: var(--bg-surface); border: 1px solid var(--border-color); padding: 10px 12px; border-radius: 12px;">
                      <span class="kos-rule-num" style="width: 26px; height: 26px; border-radius: 50%; background: #ccfbf1; color: #0a5661; font-size: 0.78rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">{{ $rIdx + 1 }}</span>
                      <input type="text" class="form-control kos-rule-input" value="{{ $ruleText }}" placeholder="Tulis poin peraturan kos..." style="flex: 1; border-radius: 10px; font-size: 0.85rem;" required>
                      <button type="button" class="btn-secondary" onclick="removeKosRuleRow(this)" style="padding: 6px 10px; border-radius: 8px; color: #ef4444; font-size: 0.78rem; flex-shrink: 0;" title="Hapus Poin"></button>
                    </div>
                  @endforeach
                </div>

                <button type="button" class="btn-secondary" onclick="addKosRuleRow()" style="width: 100%; border-style: dashed; padding: 10px; font-weight: 700; border-radius: 10px; font-size: 0.84rem;">
                  Tambah Poin Peraturan Baru
                </button>

                <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px;">
                  <button type="submit" class="btn-primary" style="padding: 10px 22px; border-radius: 12px; font-weight: 800;">
                    Simpan Peraturan Kos
                  </button>
                  <span id="kosRulesSaveStatus" style="font-size: 0.8rem; font-weight: 700;"></span>
                </div>
              </form>
            </div>

          </div>
        </div>

        <!-- =========================================================================
             SUBTAB 4: KELOLA JARINGAN WIFI KOS & MODEM (DENGAN PERBEDAAN 2 KOS MODERN)
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-wifi" style="display: none;">
          <div class="settings-modern-card" style="max-width: 960px; margin: 0 auto;">
            @php
              $allWifis = $setting->wifi_list ?? [];
              $wfLoc1Count = collect($allWifis)->filter(fn($w) => ($w['location'] ?? 'Lokasi 1') === 'Lokasi 1')->count();
              $wfLoc2Count = collect($allWifis)->filter(fn($w) => ($w['location'] ?? 'Lokasi 1') === 'Lokasi 2')->count();
            @endphp

            <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (JARINGAN WIFI) -->
            <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
              <div class="filter-pills" id="wifiLocationFilters">
                <button type="button" class="filter-pill active" onclick="filterWifiByLocation('all', this)">Semua Cabang ({{ count($allWifis) }})</button>
                <button type="button" class="filter-pill" onclick="filterWifiByLocation('Lokasi 1', this)">WiFi <span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $wfLoc1Count }})</button>
                <button type="button" class="filter-pill" onclick="filterWifiByLocation('Lokasi 2', this)">WiFi <span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $wfLoc2Count }})</button>
              </div>
            </div>

            {{-- Daftar Jaringan WiFi Dinamis --}}
            <div id="wifiNetworksList" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
              @foreach($allWifis as $idx => $wf)
                @php
                  $wfLoc = $wf['location'] ?? 'Lokasi 1';
                  $isWfLoc2 = $wfLoc === 'Lokasi 2';
                  $wfSpeed = $wf['speed'] ?? ($isWfLoc2 ? '300 Mbps Mesh WiFi 6' : '100 Mbps Dual-Band');
                @endphp
                <div class="wf-card-item" data-id="{{ $wf['id'] ?? 'wifi_'.$idx }}" data-location="{{ $wfLoc }}" style="background: var(--bg-surface); border: 1.5px solid {{ !empty($wf['is_active']) ? '#10b981' : 'var(--border-color)' }}; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; transition: all 0.2s;">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <div style="width: 42px; height: 42px; border-radius: 12px; background: {{ $isWfLoc2 ? '#ccfbf1' : '#dcfce7' }}; border: 1px solid {{ $isWfLoc2 ? '#5eead4' : '#86efac' }}; color: {{ $isWfLoc2 ? '#0a5661' : '#059669' }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                      </div>
                      <div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                          <span style="font-weight: 800; font-size: 0.98rem; color: var(--text-primary);" class="wf-name-text">{{ $wf['name'] ?? 'KOS_MAS_ROBI' }}</span>
                          @if($isWfLoc2)
                            <span class="wf-branch-badge" style="font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span></span>
                          @else
                            <span class="wf-branch-badge" style="font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></span>
                          @endif
                          <span style="font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: #f1f5f9; color: #475569;">{{ $wfSpeed }}</span>
                        </div>
                        <div style="font-size: 0.78rem; color: #0a5661; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                          <span>Modem:</span>
                          <span class="wf-modem-text" style="color: var(--text-secondary); font-weight: 500;">{{ $wf['modem'] ?? 'Modem Utama' }}</span>
                        </div>
                      </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer; background: {{ !empty($wf['is_active']) ? '#dcfce7' : '#f1f5f9' }}; color: {{ !empty($wf['is_active']) ? '#15803d' : '#64748b' }}; padding: 4px 10px; border-radius: 999px;">
                        <input type="checkbox" class="wf-active-toggle" {{ !empty($wf['is_active']) ? 'checked' : '' }} onchange="toggleWfActive(this)">
                        <span class="wf-toggle-label">{{ !empty($wf['is_active']) ? '● Ditayangkan' : '○ Disembunyikan' }}</span>
                      </label>
                      <button type="button" class="btn-secondary" onclick="deleteWfItem(this)" style="padding: 4px 8px; border-radius: 8px; color: #ef4444; font-size: 0.75rem;" title="Hapus WiFi">
                        
                      </button>
                    </div>
                  </div>

                  @if(!empty($wf['password']))
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 12px; font-size: 0.78rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                      <div>
                        <span style="color: var(--text-muted); font-size: 0.72rem;">Sandi (Password):</span>
                        <code style="font-family: monospace; font-weight: 700; color: var(--text-primary); margin-left: 6px;" class="wf-pass-text">{{ $wf['password'] }}</code>
                      </div>
                      <span style="font-size: 0.7rem; color: var(--text-secondary);">Cabang: <strong><span data-branch-name="{{ $isWfLoc2 ? 'lokasi_2' : 'lokasi_1' }}">{{ $isWfLoc2 ? $branch2Name : $branch1Name }}</span></strong></span>
                    </div>
                  @endif

                  <input type="hidden" class="wf-raw-data" value="{{ json_encode($wf) }}">
                </div>
              @endforeach
            </div>

            {{-- Tombol & Box Tambah WiFi Baru --}}
            <div style="margin-bottom: 20px;">
              <button type="button" id="btnToggleAddWf" onclick="toggleAddWifiForm()" class="btn-secondary" style="width: 100%; border-style: dashed; padding: 12px; font-weight: 800; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span>Tambah Jaringan WiFi & Modem Baru</span>
              </button>

              <div id="addWifiFormBox" style="display: none; background: var(--bg-surface); border: 1.5px solid #0a5661; border-radius: 16px; padding: 18px; margin-top: 14px;">
                <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
                  Tambah Jaringan WiFi Baru untuk Cabang Kos
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 14px;">
                  <div class="form-group">
                    <label style="font-size: 0.78rem; font-weight: 700;">Lokasi Cabang Kos *</label>
                    <select id="newWfLocation" class="form-control" style="font-weight: 700;">
                      <option value="Lokasi 1" data-branch-option="lokasi_1">{{ $branch1Name }}</option>
                      <option value="Lokasi 2" data-branch-option="lokasi_2">{{ $branch2Name }}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label style="font-size: 0.78rem; font-weight: 700;">Nama WiFi (SSID) *</label>
                    <input type="text" id="newWfName" class="form-control" placeholder="Contoh: KOS_MAS_ROBI_2_ROOFTOP">
                  </div>
                  <div class="form-group">
                    <label style="font-size: 0.78rem; font-weight: 700;">Modem / Lokasi Modem *</label>
                    <input type="text" id="newWfModem" class="form-control" placeholder="Contoh: Modem Ruijie Mesh - Lantai 2">
                  </div>
                  <div class="form-group">
                    <label style="font-size: 0.78rem; font-weight: 700;">Kata Sandi (Password)</label>
                    <input type="text" id="newWfPassword" class="form-control" placeholder="Contoh: kostnyaman2026">
                  </div>
                  <div class="form-group">
                    <label style="font-size: 0.78rem; font-weight: 700;">Kecepatan / Spek Jaringan</label>
                    <input type="text" id="newWfSpeed" class="form-control" placeholder="Contoh: 200 Mbps Fiber 5GHz">
                  </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                  <label style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; color: var(--text-primary);">
                    <input type="checkbox" id="newWfIsActive" checked style="accent-color: #10b981; width: 16px; height: 16px;">
                    <span>Langsung Tayangkan ke Portal Pelanggan Cabang Terkait</span>
                  </label>
                  <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-secondary" onclick="toggleAddWifiForm()" style="padding: 8px 16px; border-radius: 10px; font-weight: 700;">Batal</button>
                    <button type="button" class="btn-primary" onclick="submitAddNewWifi()" style="padding: 8px 20px; border-radius: 10px; font-weight: 800;">Simpan WiFi</button>
                  </div>
                </div>
              </div>
            </div>

            {{-- Tombol Simpan Seluruh Perubahan WiFi --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-top: 16px;">
              <button type="button" class="btn-primary" onclick="saveAllWifiNetworksToServer()" style="padding: 10px 24px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
                <span>Simpan Perubahan Jaringan WiFi</span>
              </button>
              <span id="wfSaveStatus" style="font-size: 0.82rem; font-weight: 700;"></span>
            </div>
          </div>
        </div>

        <!-- =========================================================================
             SUBTAB 5: PENGATURAN FORMULIR SURVEY & HUBUNGI ADMIN (CALON PENGHUNI)
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-survey" style="display: none;">
          <div class="settings-modern-card" style="max-width: 960px; margin: 0 auto;">
            <div class="settings-card-header">
              <div class="settings-card-title-group">
                <div class="settings-card-icon-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="settings-card-title">Template Formulir Calon Penghuni (WhatsApp)</h3>
                  <div class="settings-card-subtitle">Ubah susunan kalimat atau format formulir yang dikirimkan calon penghuni saat menghubungi Admin</div>
                </div>
              </div>
              <span style="font-size: 0.76rem; background: var(--bg-card); padding: 5px 12px; border-radius: 9999px; color: #059669; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.3);">WhatsApp Otomatis</span>
            </div>

            <!-- Petunjuk Variabel Tag -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
              <div style="font-weight: 800; font-size: 0.86rem; color: var(--text-primary); margin-bottom: 6px;">Petunjuk Variabel Dinamis:</div>
              <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0 0 10px; line-height: 1.5;">
                Anda dapat menyusun dan mengganti kalimat formulir ini kapan saja. Sistem otomatis mengisi variabel dalam tanda kurung siku berikut saat calon penghuni mengisi formulir di Landing Page:
              </p>
              <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Tulis Nama Anda]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Mahasiswa / Karyawan / dll]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Pilih Lokasi 1 / Lokasi 2]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Tulis Nomor / Tipe Kamar, contoh: Kamar 104 - Lantai 2]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Tulis Tanggal, contoh: Kamis, 5 November 2026]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Tulis Jam, contoh : 14.00 WIB]</span>
                <span style="font-size: 0.74rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; color: #0a5661;">[Datang Langsung / Video Call]</span>
              </div>
            </div>

            <!-- Form Editor -->
            <form id="settingsSurveyTemplateForm" onsubmit="saveSurveyTemplateToServer(event)" style="display: flex; flex-direction: column; gap: 16px;">
              <div class="form-group">
                <label for="admin_survey_form_template" style="font-weight: 800; font-size: 0.88rem; display: flex; align-items: center; justify-content: space-between;">
                  <span>Isi Teks Formulir / Format Pesan WhatsApp:</span>
                  <button type="button" class="btn-secondary" onclick="resetSurveyTemplateToDefault()" style="font-size: 0.74rem; padding: 4px 12px; border-radius: 6px; font-weight: 700;">Kembalikan Format Awal</button>
                </label>
                <textarea id="admin_survey_form_template" class="form-control" rows="15" style="font-family: monospace, sans-serif; font-size: 0.88rem; line-height: 1.6; padding: 14px; border-radius: 12px;" placeholder="Tulis format formulir pesan WhatsApp disini...">{{ $setting->survey_form_template }}</textarea>
              </div>

              <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn-primary" style="padding: 11px 24px; border-radius: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px;">
                  <span>Simpan Perubahan Formulir</span>
                </button>
                <button type="button" class="btn-secondary" onclick="copyAdminSurveyTemplate()" style="padding: 11px 18px; border-radius: 12px; font-weight: 700;">
                  <span>Salin Teks Format</span>
                </button>
                <span id="surveyTemplateSaveStatus" style="font-size: 0.84rem; font-weight: 700;"></span>
              </div>
            </form>
          </div>
        </div>

      </div>

    </div><!-- /content-body -->

  </main>

  <!-- =========================================================================
       MOBILE BOTTOM NAVIGATION (Khusus Device HP — Seluruh Fitur Admin di Bawah)
       ========================================================================= -->
  <nav class="mobile-bottom-nav" aria-label="Navigasi Bawah Mobile">
    <div class="bottom-nav-item active" data-tab="tab-dashboard" onclick="window.switchTab('tab-dashboard')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
      <span>Dashboard</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-rooms" onclick="window.switchTab('tab-rooms')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
      <span>Kamar</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-tenants" onclick="window.switchTab('tab-tenants')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <span>Penghuni</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-bills" onclick="window.switchTab('tab-bills')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      <span>Tagihan</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-transfers" onclick="window.switchTab('tab-transfers')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
      <span>Pindah</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-maintenance" onclick="window.switchTab('tab-maintenance')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      <span>Servis</span>
    </div>
    @if(Auth::user()->isOwner())
    <div class="bottom-nav-item" data-tab="tab-finances" onclick="window.switchTab('tab-finances')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      <span>Keuangan</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-admins" onclick="window.switchTab('tab-admins')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span>Admin</span>
    </div>
    @endif
    <div class="bottom-nav-item" data-tab="tab-settings" onclick="window.switchTab('tab-settings')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      <span>Pengaturan</span>
    </div>
  </nav>

  <!-- =========================================================================
       MODAL: AKUN PENGELOLA (INFORMASI PROPERTI & PENGELOLA + MANAJEMEN DATA)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAkunPengelola" style="display: none;">
    <div class="modal-dialog" style="max-width: 560px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%);">
        <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
          <div class="akun-modal-avatar" id="akunModalAvatar">{{ $ownerInitials }}</div>
          <div style="min-width: 0;">
            <div style="font-size: 0.66rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.55);">Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</div>
            <h3 id="akunModalOwnerName" style="color: #fff; margin: 2px 0 0; font-size: 1.12rem; font-weight: 800; letter-spacing: -0.2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</h3>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalAkunPengelola" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" style="padding: 22px;">
        <div class="settings-card-header" style="margin-bottom: 18px;">
          <div class="settings-card-title-group">
            <div class="settings-card-icon-badge" style="color: #ffffff;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div>
              <h3 class="settings-card-title">Informasi Properti & Pengelola</h3>
              <div class="settings-card-subtitle">Nama kos, nama pengelola, dan kontak WhatsApp resmi</div>
            </div>
          </div>
        </div>
        <form id="settingsGeneralProfileForm" style="display: flex; flex-direction: column; gap: 16px;">
          <div class="form-group">
            <label for="setKosName" style="font-weight: 700;">Nama Resmi Properti Kos</label>
            <input type="text" id="setKosName" class="form-control" value="{{ $setting->kos_name }}">
          </div>
          <div class="form-group">
            <label for="setOwnerName" style="font-weight: 700;">Nama Pemilik / Pengelola Utama</label>
            <input type="text" id="setOwnerName" class="form-control" value="{{ $setting->owner_name }}">
          </div>
          <div class="form-group">
            <label for="setOwnerPhone" style="font-weight: 700;">No. WhatsApp Pengelola (Format 62...)</label>
            <input type="text" id="setOwnerPhone" class="form-control" value="{{ $setting->owner_phone }}">
          </div>
          <button type="submit" class="btn-primary" style="align-self: flex-start; margin-top: 6px; padding: 10px 22px; border-radius: 12px; font-weight: 800;">Simpan Akun Pengelola</button>
        </form>

        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 22px 0;">

        <div>
          <h4 style="margin-bottom: 6px; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Manajemen Data & Cadangan</h4>
          <p style="font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 14px;">Data tersimpan aman di database Laravel. Anda dapat mengekspor cadangan JSON sewaktu-waktu.</p>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn-secondary" id="btnExportData" style="border-radius: 10px; font-weight: 700;">Unduh Cadangan JSON</button>
            <button type="button" class="btn-secondary" id="btnResetData" style="color: #f87171; border-radius: 10px; font-weight: 700;">Reset Data Sampel</button>
          </div>
          @if(Auth::user()->isOwner())
          <button type="button" class="btn-primary" onclick="window.closeModal('modalAkunPengelola'); window.switchTab('tab-admins');" style="margin-top: 12px; width: 100%; border-radius: 10px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Buka Menu Manajemen Staf Admin</span>
          </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: KELOLA AKUN ADMIN OPERASIONAL (KHUSUS ROLE OWNER)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalManageAdmin" style="display: none;">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div>
            <h3 id="modalManageAdminTitle" style="color: #fff; margin: 0; font-size: 1.12rem; font-weight: 800; letter-spacing: -0.2px;">Tambah Akun Admin Baru</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Berikan hak akses login staf operasional ke portal manajemen kos</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalManageAdmin" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <form id="formManageAdmin" onsubmit="submitAdminForm(event)">
        <input type="hidden" id="manageAdminId" value="">
        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          
          <div class="form-group">
            <label for="adminNameInput" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nama Lengkap Staf Admin *</label>
            <input type="text" id="adminNameInput" class="form-control" placeholder="Contoh: Budi Santoso" required style="border-radius: 12px;">
          </div>

          <div class="form-group">
            <label for="adminEmailInput" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Alamat Email Login *</label>
            <input type="email" id="adminEmailInput" class="form-control" placeholder="Contoh: budi.admin@gmail.com" required style="border-radius: 12px;">
          </div>

          <div class="form-group">
            <label for="adminPhoneInput" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nomor WhatsApp / HP Staf</label>
            <input type="text" id="adminPhoneInput" class="form-control" placeholder="Contoh: 081234567890" style="border-radius: 12px;">
          </div>

          <div class="form-group">
            <label for="adminPasswordInput" id="adminPasswordLabel" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Kata Sandi Login *</label>
            <div style="position: relative;">
              <input type="password" id="adminPasswordInput" class="form-control" placeholder="Minimal 6 karakter" required style="border-radius: 12px; padding-right: 42px;">
              <button type="button" onclick="toggleInputPasswordVisibility('adminPasswordInput', 'iconEyeAdm')" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: #94a3b8; padding: 4px;">
                <svg id="iconEyeAdm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <span id="adminPasswordHelp" style="font-size: 0.72rem; color: #64748b; margin-top: 4px; display: none;">Kosongkan bila tidak ingin mengubah kata sandi yang sudah ada.</span>
          </div>

          <div style="background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 12px; font-size: 0.75rem; color: #0a5661; line-height: 1.5;">
            <strong>Catatan Hak Akses:</strong> Akun ini akan memiliki role <strong>Admin</strong>. Staf dapat mengelola kamar, data penghuni, servis, dan respon survey WhatsApp, namun <em>tidak memiliki akses</em> ke Buku Kas Keuangan dan Pengaturan Admin.
          </div>

        </div>
        <div class="modal-footer" style="padding: 16px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalManageAdmin">Batal</button>
          <button type="submit" class="btn-primary" id="btnSubmitManageAdmin" style="padding: 10px 20px; border-radius: 10px; font-weight: 800;">
            Simpan Akun Admin
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: DETAIL KAMAR (MODERN LUXURY TEAL)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalRoomDetail" style="display: none;">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 id="mRoomDetailTitle" style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Detail Kamar #101</h3>
            <span id="mRoomDetailLocBadge" style="display: inline-block; font-size: 0.72rem; color: rgba(255,255,255,0.75); font-weight: 600; margin-top: 2px;"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></span>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalRoomDetail">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" id="mRoomDetailBody" style="padding: 22px;">
        <!-- Rendered by JS -->
      </div>
      <div class="modal-footer" id="mRoomDetailFooter">
        <button type="button" class="btn-secondary" data-close-modal="modalRoomDetail">Tutup</button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM TAMBAH PENGHUNI (MODERN LUXURY TEAL)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAddTenant" style="display: none;">
    <div class="modal-dialog" style="max-width: 600px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Daftarkan Penghuni & Buat Akun Baru</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Registrasi data anak kos, buat akun login portal, dan penempatan kamar</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalAddTenant">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formAddTenant">
        <div class="modal-body" style="padding: 22px; gap: 16px;">
          <div class="form-group">
            <label for="tenantFullName" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nama Lengkap Anak Kos *</label>
            <input type="text" id="tenantFullName" class="form-control" placeholder="Contoh: Muhammad Dimas Satria" required style="border-radius: 12px;">
          </div>

          <!-- SECTION AKUN LOGIN PORTAL PENGHUNI -->
          <div style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border: 1.5px solid #99f6e4; border-radius: 16px; padding: 16px; margin: 4px 0 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 26px; height: 26px; background: #0a5661; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <span style="font-weight: 800; font-size: 0.88rem; color: #04272c;">Akun Login Portal Anak Kos</span>
              </div>
              <span style="font-size: 0.7rem; color: #0f766e; background: #ccfbf1; padding: 2px 8px; border-radius: 6px; font-weight: 700;">Akses Penghuni</span>
            </div>

            <div class="form-row" style="gap: 12px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="tenantEmail" style="font-weight: 700; font-size: 0.8rem; color: #0f766e;">Email Akun (Gmail) *</label>
                <input type="email" id="tenantEmail" class="form-control" placeholder="contoh: dimas@gmail.com" required style="border-radius: 10px; border-color: #99f6e4; background: #fff;">
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                  <label for="tenantPassword" style="font-weight: 700; font-size: 0.8rem; color: #0f766e; margin-bottom: 0;">Kata Sandi *</label>
                  <button type="button" onclick="generateTenantRandomPassword()" style="background: none; border: none; color: #0a5661; font-size: 0.72rem; font-weight: 700; cursor: pointer; text-decoration: underline;">Buat Acak</button>
                </div>
                <input type="text" id="tenantPassword" class="form-control" placeholder="Minimal 6 karakter..." required style="border-radius: 10px; border-color: #99f6e4; background: #fff; font-family: monospace;">
              </div>
            </div>
            <div style="font-size: 0.72rem; color: #0f766e; margin-top: 8px; line-height: 1.4;">
              Informasi email dan kata sandi ini otomatis dapat langsung dikirimkan ke nomor WhatsApp anak kos setelah disimpan.
            </div>
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantPhone" style="font-weight: 700; font-size: 0.82rem; color: #334155;">No. WhatsApp *</label>
              <input type="tel" id="tenantPhone" class="form-control" placeholder="0812xxxxxxxx" required style="border-radius: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantIdNumber" style="font-weight: 700; font-size: 0.82rem; color: #334155;">No. KTP / NIK</label>
              <input type="text" id="tenantIdNumber" class="form-control" placeholder="33xxxxxxxxxxxxxx" style="border-radius: 12px;">
            </div>
          </div>
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantSelectRoom" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Pilih Kamar & Lokasi *</label>
              <select id="tenantSelectRoom" class="form-control" required style="border-radius: 12px; font-weight: 600;">
                <!-- Populated dynamically with location groupings -->
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantStartDate" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tanggal Masuk *</label>
              <input type="date" id="tenantStartDate" class="form-control" required style="border-radius: 12px;">
            </div>
          </div>
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantDueDay" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tgl Jatuh Tempo Bulanan</label>
              <input type="number" id="tenantDueDay" class="form-control" min="1" max="31" value="1" required style="border-radius: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantRentAmount" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tarif Sewa (Rp / Bulan)</label>
              <input type="number" id="tenantRentAmount" class="form-control" placeholder="1500000" required style="border-radius: 12px;">
            </div>
          </div>
          <div class="form-group">
            <label for="tenantEmergencyContact" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Kontak Darurat (Ortu / Wali / Saudara)</label>
            <input type="text" id="tenantEmergencyContact" class="form-control" placeholder="Contoh: Bpk. Joko (0813xxxxxxxx) - Orang Tua" style="border-radius: 12px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalAddTenant">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 800;">Simpan & Buat Akun Penghuni</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM TAMBAH / EDIT KAMAR — THEME TEAL & EMPTY BY DEFAULT
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAddRoom" style="display: none;">
    <div class="modal-dialog" style="max-width: 780px; width: 95vw;">
      <!-- Premium Modal Header (Deep Teal Ocean) -->
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 40%, #0a5661 100%); border-radius: 20px 20px 0 0; padding: 20px 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 42px; height: 42px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 id="mRoomFormTitle" style="color: #fff; font-size: 1.1rem; font-weight: 800; margin: 0;">Tambah Master Kamar</h3>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.76rem; margin: 2px 0 0;">Isi data kamar & upload foto kamar asli</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalAddRoom" style="background: rgba(255,255,255,0.15); border-radius: 10px; color: #fff;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <form id="formAddRoom">
        <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 20px; max-height: 75vh; overflow-y: auto;">
          <input type="hidden" id="roomEditId" value="">

          <!-- SECTION 1: INFO DASAR KAMAR -->
          <div style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border: 1.5px solid #99f6e4; border-radius: 16px; padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 28px; height: 28px; background: #0a5661; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                <span style="font-weight: 800; font-size: 0.92rem; color: #04272c;">Informasi Dasar Kamar</span>
              </div>
              <span style="font-size: 0.72rem; color: #0f766e; background: #ccfbf1; padding: 3px 8px; border-radius: 6px; font-weight: 700;">2 Titik Lokasi Tersedia</span>
            </div>

            <!-- PILIHAN CABANG LOKASI KOS -->
            <div class="form-group" style="margin-bottom: 14px;">
              <label for="roomLocationInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e; display: flex; align-items: center; justify-content: space-between;">
                <span>Titik Lokasi / Cabang Kos *</span>
                <span style="font-weight: 500; font-size: 0.7rem; color: #0f766e;">Otomatis sinkron tipe & fasilitas</span>
              </label>
              <select id="roomLocationInput" class="form-control" required style="border-color: #99f6e4; border-radius: 10px; font-weight: 700; color: #0a5661; background: #ffffff;">
                <option value="Lokasi 1" data-branch-option="lokasi_1">{{ $branch1Name }}</option>
                <option value="Lokasi 2" data-branch-option="lokasi_2">{{ $branch2Name }}</option>
              </select>
            </div>

            <div class="form-row" style="gap: 14px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomNumberInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Nomor Kamar *</label>
                <input type="text" id="roomNumberInput" class="form-control" placeholder="Contoh: 101 atau B02" required style="border-color: #99f6e4; border-radius: 10px;">
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomFloorInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Lantai *</label>
                <select id="roomFloorInput" class="form-control" required style="border-color: #99f6e4; border-radius: 10px;">
                  <option value="1">Lantai 1</option>
                  <option value="2">Lantai 2</option>
                  <option value="3">Lantai 3</option>
                </select>
              </div>
            </div>
            <div class="form-row" style="gap: 14px; margin-top: 12px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomTypeInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Tipe Kamar *</label>
                <select id="roomTypeInput" class="form-control" required style="border-color: #99f6e4; border-radius: 10px;">
                  <option value="VIP AC (KM Dalam)">VIP AC (KM Dalam)</option>
                  <option value="Deluxe AC">Deluxe AC</option>
                  <option value="Standard Fan (KM Luar)">Standard Fan (KM Luar)</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomPriceInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Harga Sewa / Bulan (Rp) *</label>
                <input type="number" id="roomPriceInput" class="form-control" placeholder="1500000" required style="border-color: #99f6e4; border-radius: 10px;">
              </div>
            </div>
            <div class="form-row" style="gap: 14px; margin-top: 12px;">
              <div class="form-group" style="margin-bottom: 0; flex: 2;">
                <label for="roomFacilitiesInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Fasilitas (pisahkan dengan koma)</label>
                <input type="text" id="roomFacilitiesInput" class="form-control" value="AC, Kasur Springbed, Lemari 2 Pintu, Meja Belajar, KM Dalam, Water Heater" style="border-color: #99f6e4; border-radius: 10px;">
              </div>
              <div class="form-group" style="margin-bottom: 0; flex: 1;">
                <label for="roomStatusInput" style="font-size: 0.78rem; font-weight: 700; color: #0f766e;">Status Kamar</label>
                <select id="roomStatusInput" class="form-control" style="border-color: #99f6e4; border-radius: 10px;">
                  <option value="available">Kosong</option>
                  <option value="occupied">Terisi</option>
                  <option value="repair">Perbaikan</option>
                </select>
              </div>
            </div>
          </div>

          <!-- SECTION 2: UPLOAD 3–4 FOTO KAMAR (KOSONG AWAL, MUNCUL KETIKA DIUPLOAD) -->
          <div style="background: #fff; border: 1.5px solid #ccfbf1; border-radius: 16px; padding: 18px; box-shadow: 0 2px 12px rgba(10,86,97,0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #0a5661, #0d9488); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                <div>
                  <span style="font-weight: 800; font-size: 0.92rem; color: #04272c;">Foto Kamar</span>
                  <span style="font-size: 0.72rem; background: #ccfbf1; color: #0f766e; padding: 2px 8px; border-radius: 999px; font-weight: 700; margin-left: 8px;">Upload 3–4 Foto</span>
                </div>
              </div>
              <span style="font-size: 0.72rem; color: #0d9488; font-weight: 700;">Klik slot untuk memilih foto</span>
            </div>
            <p style="font-size: 0.74rem; color: var(--text-secondary); margin-bottom: 14px; line-height: 1.5;">
              Slot foto awal kosong. Silakan upload 3–4 foto terbaik kamar. Foto pertama otomatis menjadi <strong>foto utama (cover)</strong> kamar.
            </p>

            <!-- 4 Foto Grid Upload (Default Kosong) -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;" id="roomPhotosGrid">

              <!-- Slot Foto 1 (Utama) -->
              <div class="room-photo-slot" id="roomPhotoSlot1" data-slot="1" style="position: relative; overflow: hidden; cursor: pointer; min-height: 140px;"
                   onclick="document.getElementById('roomPhotoFile1').click()"
                   ondragover="event.preventDefault(); this.style.borderColor='#0d9488'; this.style.background='#ccfbf1';"
                   ondragleave="this.style.borderColor='#99f6e4'; this.style.background='#f0fdfa';"
                   ondrop="handleRoomPhotoDrop(event, 1)">
                <!-- Empty Placeholder -->
                <div id="roomPhotoPlaceholder1" class="room-photo-placeholder">
                  <div style="width: 42px; height: 42px; border-radius: 12px; background: #ccfbf1; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                  <div style="font-size: 0.82rem; font-weight: 800; color: #0a5661;">Foto 1: Utama (Cover) *</div>
                  <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">Klik / drag foto ke sini</div>
                  <div style="font-size: 0.62rem; color: #0d9488; margin-top: 4px; font-weight: 700;">Wajib Diisi</div>
                </div>
                <!-- Filled View -->
                <div id="roomPhotoFilled1" class="room-photo-filled" style="display: none;">
                  <img id="roomImgPreview1" src="" alt="Foto 1" style="width: 100%; height: 140px; object-fit: cover; display: block; border-radius: 12px;">
                  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(3,35,40,0.65) 0%, transparent 55%); border-radius: 12px; pointer-events: none;"></div>
                  <div style="position: absolute; top: 8px; left: 8px; background: #0a5661; color: #fff; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; pointer-events: none;">FOTO UTAMA </div>
                  <button type="button" onclick="event.stopPropagation(); removeRoomPhoto(1);" style="position: absolute; top: 8px; right: 8px; background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: 6px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.72rem; font-weight: 800;" title="Hapus foto"></button>
                  <div id="roomImgUploadStatus1" style="position: absolute; top: 8px; right: 36px; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: rgba(0,0,0,0.5); color: #fff; pointer-events: none;"></div>
                  <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 12px; pointer-events: none;">
                    <div style="color: #fff; font-size: 0.72rem; font-weight: 700;">Foto Kamar Tidur Utama</div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.62rem;">Klik untuk ganti foto</div>
                  </div>
                </div>
                <input type="file" id="roomPhotoFile1" accept="image/*" style="display: none;" onchange="uploadRoomSlideImage(this, 1)">
                <input type="hidden" id="roomImageInput1" value="">
              </div>

              <!-- Slot Foto 2 -->
              <div class="room-photo-slot" id="roomPhotoSlot2" data-slot="2" style="position: relative; overflow: hidden; cursor: pointer; min-height: 140px;"
                   onclick="document.getElementById('roomPhotoFile2').click()"
                   ondragover="event.preventDefault(); this.style.borderColor='#0d9488'; this.style.background='#ccfbf1';"
                   ondragleave="this.style.borderColor='#99f6e4'; this.style.background='#f0fdfa';"
                   ondrop="handleRoomPhotoDrop(event, 2)">
                <div id="roomPhotoPlaceholder2" class="room-photo-placeholder">
                  <div style="width: 42px; height: 42px; border-radius: 12px; background: #ccfbf1; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                  <div style="font-size: 0.82rem; font-weight: 800; color: #0a5661;">Foto 2: Meja Kerja / AC</div>
                  <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">Klik / drag foto ke sini</div>
                </div>
                <div id="roomPhotoFilled2" class="room-photo-filled" style="display: none;">
                  <img id="roomImgPreview2" src="" alt="Foto 2" style="width: 100%; height: 140px; object-fit: cover; display: block; border-radius: 12px;">
                  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(3,35,40,0.65) 0%, transparent 55%); border-radius: 12px; pointer-events: none;"></div>
                  <div style="position: absolute; top: 8px; left: 8px; background: rgba(3,35,40,0.65); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; pointer-events: none;">FOTO 2</div>
                  <button type="button" onclick="event.stopPropagation(); removeRoomPhoto(2);" style="position: absolute; top: 8px; right: 8px; background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: 6px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.72rem; font-weight: 800;" title="Hapus foto"></button>
                  <div id="roomImgUploadStatus2" style="position: absolute; top: 8px; right: 36px; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: rgba(0,0,0,0.5); color: #fff; pointer-events: none;"></div>
                  <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 12px; pointer-events: none;">
                    <div style="color: #fff; font-size: 0.72rem; font-weight: 700;">Meja Kerja / AC</div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.62rem;">Klik untuk ganti foto</div>
                  </div>
                </div>
                <input type="file" id="roomPhotoFile2" accept="image/*" style="display: none;" onchange="uploadRoomSlideImage(this, 2)">
                <input type="hidden" id="roomImageInput2" value="">
              </div>

              <!-- Slot Foto 3 -->
              <div class="room-photo-slot" id="roomPhotoSlot3" data-slot="3" style="position: relative; overflow: hidden; cursor: pointer; min-height: 140px;"
                   onclick="document.getElementById('roomPhotoFile3').click()"
                   ondragover="event.preventDefault(); this.style.borderColor='#0d9488'; this.style.background='#ccfbf1';"
                   ondragleave="this.style.borderColor='#99f6e4'; this.style.background='#f0fdfa';"
                   ondrop="handleRoomPhotoDrop(event, 3)">
                <div id="roomPhotoPlaceholder3" class="room-photo-placeholder">
                  <div style="width: 42px; height: 42px; border-radius: 12px; background: #ccfbf1; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                  <div style="font-size: 0.82rem; font-weight: 800; color: #0a5661;">Foto 3: Kamar Mandi / Fasilitas</div>
                  <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">Klik / drag foto ke sini</div>
                </div>
                <div id="roomPhotoFilled3" class="room-photo-filled" style="display: none;">
                  <img id="roomImgPreview3" src="" alt="Foto 3" style="width: 100%; height: 140px; object-fit: cover; display: block; border-radius: 12px;">
                  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(3,35,40,0.65) 0%, transparent 55%); border-radius: 12px; pointer-events: none;"></div>
                  <div style="position: absolute; top: 8px; left: 8px; background: rgba(3,35,40,0.65); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; pointer-events: none;">FOTO 3</div>
                  <button type="button" onclick="event.stopPropagation(); removeRoomPhoto(3);" style="position: absolute; top: 8px; right: 8px; background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: 6px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.72rem; font-weight: 800;" title="Hapus foto"></button>
                  <div id="roomImgUploadStatus3" style="position: absolute; top: 8px; right: 36px; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: rgba(0,0,0,0.5); color: #fff; pointer-events: none;"></div>
                  <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 12px; pointer-events: none;">
                    <div style="color: #fff; font-size: 0.72rem; font-weight: 700;">Kamar Mandi / Fasilitas</div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.62rem;">Klik untuk ganti foto</div>
                  </div>
                </div>
                <input type="file" id="roomPhotoFile3" accept="image/*" style="display: none;" onchange="uploadRoomSlideImage(this, 3)">
                <input type="hidden" id="roomImageInput3" value="">
              </div>

              <!-- Slot Foto 4 (Opsional) -->
              <div class="room-photo-slot" id="roomPhotoSlot4" data-slot="4" style="position: relative; overflow: hidden; cursor: pointer; min-height: 140px;"
                   onclick="document.getElementById('roomPhotoFile4').click()"
                   ondragover="event.preventDefault(); this.style.borderColor='#0d9488'; this.style.background='#ccfbf1';"
                   ondragleave="this.style.borderColor='#99f6e4'; this.style.background='#f0fdfa';"
                   ondrop="handleRoomPhotoDrop(event, 4)">
                <div id="roomPhotoPlaceholder4" class="room-photo-placeholder">
                  <div style="width: 42px; height: 42px; border-radius: 12px; background: #ccfbf1; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                  <div style="font-size: 0.82rem; font-weight: 800; color: #0a5661;">Foto 4: Tambahan (Opsional)</div>
                  <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">Klik / drag foto ke sini</div>
                </div>
                <div id="roomPhotoFilled4" class="room-photo-filled" style="display: none;">
                  <img id="roomImgPreview4" src="" alt="Foto 4" style="width: 100%; height: 140px; object-fit: cover; display: block; border-radius: 12px;">
                  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(3,35,40,0.65) 0%, transparent 55%); border-radius: 12px; pointer-events: none;"></div>
                  <div style="position: absolute; top: 8px; left: 8px; background: rgba(3,35,40,0.65); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; pointer-events: none;">OPSIONAL</div>
                  <button type="button" onclick="event.stopPropagation(); removeRoomPhoto(4);" style="position: absolute; top: 8px; right: 8px; background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: 6px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.72rem; font-weight: 800;" title="Hapus foto"></button>
                  <div id="roomImgUploadStatus4" style="position: absolute; top: 8px; right: 36px; font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: rgba(0,0,0,0.5); color: #fff; pointer-events: none;"></div>
                  <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 10px 12px; pointer-events: none;">
                    <div style="color: #fff; font-size: 0.72rem; font-weight: 700;">Foto Tambahan</div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.62rem;">Klik untuk ganti foto</div>
                  </div>
                </div>
                <input type="file" id="roomPhotoFile4" accept="image/*" style="display: none;" onchange="uploadRoomSlideImage(this, 4)">
                <input type="hidden" id="roomImageInput4" value="">
              </div>

            </div>

            <p style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 10px; text-align: center;">
              Format: JPG, PNG, WEBP • Maks 5MB per foto • Klik kotak foto untuk upload langsung
            </p>
          </div>

          <!-- SECTION 3: CUSTOM INTERIOR -->
          <div style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border: 1.5px solid #5eead4; border-radius: 16px; padding: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 28px; height: 28px; background: #0a5661; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                <span style="font-weight: 800; font-size: 0.92rem; color: #04272c;">Custom Interior & Anggaran</span>
              </div>
              <span style="font-size: 0.68rem; background: #063d45; color: #fff; padding: 3px 9px; border-radius: 999px; font-weight: 700;">{{ Auth::user()->isOwner() ? 'Owner Only' : 'Admin Only' }}</span>
            </div>
            <div class="form-row" style="gap: 14px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomCustomInteriorStatusInput" style="font-size: 0.76rem; font-weight: 700; color: #0f766e;">Status Custom Interior</label>
                <select id="roomCustomInteriorStatusInput" class="form-control" style="border-color: #99f6e4; border-radius: 10px;">
                  <option value="none">Tidak Ada Permintaan</option>
                  <option value="pending">Menunggu Review</option>
                  <option value="approved">Disetujui</option>
                  <option value="in_progress">Sedang Dikerjakan</option>
                  <option value="completed">Selesai</option>
                  <option value="rejected">Ditolak</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label for="roomCustomInteriorBudgetInput" style="font-size: 0.76rem; font-weight: 700; color: #0f766e;">Anggaran Disetujui (Rp)</label>
                <input type="number" id="roomCustomInteriorBudgetInput" class="form-control" placeholder="0" min="0" step="10000" value="0" style="border-color: #99f6e4; border-radius: 10px;">
              </div>
            </div>
            <div class="form-group" style="margin-top: 12px; margin-bottom: 10px;">
              <label for="roomCustomInteriorRequestInput" style="font-size: 0.76rem; font-weight: 700; color: #0f766e;">Permintaan Desain Pelanggan</label>
              <textarea id="roomCustomInteriorRequestInput" class="form-control" rows="2" placeholder="Catatan permintaan dari pelanggan..." style="border-color: #99f6e4; border-radius: 10px;"></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="roomCustomInteriorNotesInput" style="font-size: 0.76rem; font-weight: 700; color: #0f766e;">Catatan {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }} untuk Pelanggan</label>
              <input type="text" id="roomCustomInteriorNotesInput" class="form-control" placeholder="Contoh: Ambalan kayu jati siap dipasang tgl 5 Nov." style="border-color: #99f6e4; border-radius: 10px;">
            </div>
          </div>

        </div>

        <!-- Premium Footer -->
        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 20px 20px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">
          <p style="font-size: 0.72rem; color: var(--text-secondary); margin: 0;">* Bidang wajib diisi</p>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-secondary" data-close-modal="modalAddRoom" style="border-radius: 10px; padding: 10px 20px;">Batal</button>
            <button type="submit" class="btn-primary" style="border-radius: 10px; padding: 10px 24px; font-weight: 800; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); border: none; box-shadow: 0 4px 14px rgba(10,86,97,0.35);">
              Simpan Kamar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM CATAT TRANSAKSI KEUANGAN (MODERN LUXURY TEAL)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalTransaction" style="display: none;">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Catat Pembayaran / Transaksi</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Pemasukan sewa kamar atau pengeluaran operasional kos</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalTransaction">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formTransaction">
        <div class="modal-body" style="padding: 22px; gap: 16px;">
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="transType" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Jenis Transaksi *</label>
              <select id="transType" class="form-control" required style="border-radius: 12px; font-weight: 700;">
                <option value="income">Pemasukan (Sewa / Token / Laundry)</option>
                <option value="expense">Pengeluaran (WiFi, Listrik, Kebersihan)</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="transDate" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tanggal Transaksi *</label>
              <input type="date" id="transDate" class="form-control" required style="border-radius: 12px;">
            </div>
          </div>
          <div class="form-group" id="transRoomSelectGroup">
            <label for="transRoomId" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Kaitkan dengan Kamar / Penghuni</label>
            <select id="transRoomId" class="form-control" style="border-radius: 12px; font-weight: 600;">
              <!-- Populated by JS -->
            </select>
          </div>
          <div class="form-group">
            <label for="transDescription" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Keterangan Transaksi *</label>
            <input type="text" id="transDescription" class="form-control" placeholder="Contoh: Pembayaran Sewa Kamar 102 Bulan November 2026" required style="border-radius: 12px;">
          </div>
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="transAmount" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nominal (Rp) *</label>
              <input type="number" id="transAmount" class="form-control" placeholder="1500000" required style="border-radius: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="transMethod" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Metode Pembayaran</label>
              <select id="transMethod" class="form-control" style="border-radius: 12px; font-weight: 600;">
                <option value="Transfer BCA">Transfer BCA</option>
                <option value="Transfer QRIS">Transfer QRIS</option>
                <option value="Transfer Mandiri">Transfer Mandiri</option>
                <option value="Tunai (Cash)">Tunai (Cash)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalTransaction">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 800;">Simpan Transaksi</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: INPUT PEMBAYARAN MANUAL & TERBITKAN KUITANSI KE PELANGGAN
       ========================================================================= -->
  <div class="modal-backdrop" id="modalManualPaymentReceipt" style="display: none;">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Input Pembayaran & Terbitkan Kuitansi</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Catat pembayaran sewa kos dan langsung kirimkan kuitansi resmi ke Pelanggan</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalManualPaymentReceipt" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formManualPaymentReceipt" onsubmit="submitManualPaymentReceipt(event)">
        <div class="modal-body" style="padding: 22px; gap: 14px; max-height: calc(88vh - 120px); overflow-y: auto;">
          <div class="form-group">
            <label for="payRecRoomId" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Pilih Unit Kamar & Penghuni *</label>
            <select id="payRecRoomId" class="form-control" required style="border-radius: 12px; font-weight: 700;" onchange="onManualPayRoomChanged(this)">
              <option value="">-- Pilih Kamar Penghuni --</option>
              @foreach($rooms as $r)
                @php $t = $r->activeTenant; @endphp
                <option value="{{ $r->id }}" data-price="{{ (int)$r->price }}" data-tenant="{{ $t ? $t->name : 'Unit Kosong' }}" data-room="{{ $r->number }}">
                  Kamar #{{ $r->number }} ({{ $r->type }}) — {{ $t ? $t->name : '(Unit Kosong)' }} [Rp {{ number_format($r->price, 0, ',', '.') }}/bln]
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="payRecDate" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tanggal Pembayaran *</label>
              <input type="date" id="payRecDate" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="payRecTimezone" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Zona Waktu</label>
              <select id="payRecTimezone" class="form-control" style="border-radius: 12px; font-weight: 700;">
                <option value="WIB" selected>WIB (Waktu Indonesia Barat)</option>
                <option value="WITA">WITA (Waktu Indonesia Tengah)</option>
                <option value="WIT">WIT (Waktu Indonesia Timur)</option>
              </select>
            </div>
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="payRecAmount" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nominal Pembayaran (Rp) *</label>
              <input type="number" id="payRecAmount" class="form-control" placeholder="Contoh: 1500000" required style="border-radius: 12px; font-weight: 800; font-size: 1rem; color: #0a5661;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="payRecMethod" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Metode Pembayaran *</label>
              <select id="payRecMethod" class="form-control" required style="border-radius: 12px; font-weight: 700;">
                <option value="Transfer BCA">Transfer Bank BCA</option>
                <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
                <option value="Transfer BRI">Transfer Bank BRI</option>
                <option value="Transfer BNI">Transfer Bank BNI</option>
                <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                <option value="Tunai (Cash)">Tunai / Cash Langsung ke Pengelola</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="payRecDesc" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Keterangan / Keperluan Kuitansi *</label>
            <input type="text" id="payRecDesc" class="form-control" placeholder="Contoh: Pembayaran Sewa Kamar #102 Periode November 2026" required style="border-radius: 12px;">
          </div>

          <div class="form-group">
            <label for="payRecProofFile" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Lampiran Foto Bukti / Nota Transaksi (Opsional)</label>
            <input type="file" id="payRecProofFile" class="form-control" accept="image/*" style="border-radius: 12px; padding: 8px;">
          </div>

          <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 14px; padding: 12px 16px; display: flex; align-items: flex-start; gap: 10px;">
            <input type="checkbox" id="payRecSendToTenant" checked style="accent-color: #0a5661; width: 18px; height: 18px; margin-top: 2px;">
            <div>
              <label for="payRecSendToTenant" style="font-weight: 800; font-size: 0.86rem; color: #0a5661; cursor: pointer;">
                Langsung Kirim Kuitansi Resmi ke Portal Pelanggan
              </label>
              <div style="font-size: 0.74rem; color: #0f766e; margin-top: 2px;">
                Saat dicentang, kuitansi digital akan langsung tampil dan dapat dibuka/dicetak oleh anak kos di akun portal pelanggannya.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 14px 22px;">
          <button type="button" class="btn-secondary" data-close-modal="modalManualPaymentReceipt">Batal</button>
          <button type="submit" class="btn-primary" id="btnSubmitPayRec" style="padding: 11px 26px; border-radius: 12px; font-weight: 800; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
            Simpan & Kirim Kuitansi ke Pelanggan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: KUITANSI DIGITAL (MODERN LUXURY TEAL)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalReceipt" style="display: none;">
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Kuitansi Digital Resmi</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Bukti pembayaran sah tercatat di pembukuan kost</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalReceipt">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" style="background: #f8fafc; padding: 20px;">
        <div class="receipt-paper" id="receiptPrintArea">
          <div class="receipt-header">
            <h4>{{ $setting->kos_name }}</h4>
            <p>Smart Living & Kost Eksklusif (2 Lokasi Strategis)</p>
            <p>{{ $setting->kos_address }}</p>
            <p style="font-weight: 700; margin-top: 6px; letter-spacing: 0.05em;" id="recNumber">NO: KMR-2026-10-001</p>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Tanggal & Waktu Bayar:</span>
            <span id="recDate" style="font-weight: 700; color: #0a5661;">28 Oktober 2026, 12:00:00 WIB</span>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Telah Terima Dari:</span>
            <span style="font-weight: 700;" id="recTenant">Dimas Satria</span>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Untuk Kamar:</span>
            <span style="font-weight: 700;" id="recRoom">Kamar 102 (Lantai 1)</span>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Keterangan:</span>
            <span id="recDesc">Sewa Kamar Periode Nov 2026</span>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Metode Bayar:</span>
            <span id="recMethod">Transfer BCA</span>
          </div>
          <div class="receipt-row total">
            <span>JUMLAH DITERIMA:</span>
            <span id="recAmount">Rp 1.500.000</span>
          </div>
          <div id="recProofRow" style="display: none; margin-top: 10px; padding: 10px; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
            <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; margin-bottom: 6px;">LAMPIRAN FOTO BUKTI TRANSFER:</div>
            <img id="recProofImg" src="" alt="Bukti Transfer" style="max-width: 100%; max-height: 180px; object-fit: contain; border-radius: 6px; border: 1px solid #cbd5e1;">
          </div>
          <div class="receipt-footer">
            <p>Status: <strong style="color: #059669;">LUNAS / SAH</strong></p>
            <p style="margin-top: 4px;">Terima kasih atas pembayaran tepat waktu!</p>
            <p style="margin-top: 10px; font-size: 0.7rem; color: #94a3b8;">Diterbitkan otomatis oleh Sistem Pengelola {{ $setting->kos_name }}</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" data-close-modal="modalReceipt">Tutup</button>
        <button type="button" class="btn-primary" id="btnPrintReceipt" style="padding: 10px 22px; border-radius: 12px; font-weight: 800;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Cetak / Download PDF
        </button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM BUAT TIKET KELUHAN (MODERN LUXURY TEAL)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalRepairTicket" style="display: none;">
    <div class="modal-dialog" style="max-width: 580px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Laporan Keluhan & Servis Fasilitas</h3>
            <p style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Catat kendala teknis atau perbaikan fasilitas kamar</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalRepairTicket">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formRepairTicket">
        <div class="modal-body" style="padding: 22px; gap: 16px;">
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="repairRoomSelect" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Pilih Kamar Terkait *</label>
              <select id="repairRoomSelect" class="form-control" required style="border-radius: 12px; font-weight: 600;">
                <!-- Populated by JS -->
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="repairPriority" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tingkat Urgensi</label>
              <select id="repairPriority" class="form-control" style="border-radius: 12px; font-weight: 700;">
                <option value="Normal">Normal</option>
                <option value="Mendesak">Mendesak (Penting)</option>
                <option value="Rendah">Rendah</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="repairDescription" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Rincian Keluhan / Kerusakan *</label>
            <textarea id="repairDescription" class="form-control" rows="3" placeholder="Contoh: Keran air wastafel di kamar mandi menetes terus menerus atau AC kurang dingin..." required style="border-radius: 12px;"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalRepairTicket">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 800;">Kirim Laporan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: LAPORAN KEUANGAN PDF MODERN & EKSEKUTIF (PREVIEW & CETAK/SIMPAN PDF)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalFinancePdfPreview" onclick="if(event.target === this) closeFinancePdfModal()" style="display: none; position: fixed; inset: 0; background: rgba(3, 35, 40, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div class="modal-dialog" style="max-width: 960px; width: 100%; max-height: 94vh; margin: auto; display: flex; flex-direction: column; border-radius: 24px; overflow: hidden; background: #0f172a; border: 1.5px solid rgba(45, 212, 191, 0.35); box-shadow: 0 28px 65px rgba(0,0,0,0.6);">
      <!-- Toolbar Header PDF -->
      <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; background: linear-gradient(135deg, #071f24 0%, #0a3d46 60%, #0d5c68 100%); border-bottom: 1px solid rgba(255,255,255,0.12); flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(45, 212, 191, 0.16); border: 1px solid rgba(45, 212, 191, 0.35); display: flex; align-items: center; justify-content: center; color: #2dd4bf;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #ffffff;" id="pdfModalTitleText">Dokumen PDF Laporan Keuangan</h3>
              <span style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.68rem; font-weight: 800; padding: 2px 9px; border-radius: 999px; letter-spacing: 0.04em;">A4 EXECUTIVE PDF</span>
            </div>
            <p style="margin: 2px 0 0 0; font-size: 0.76rem; color: rgba(255,255,255,0.72);" id="pdfModalSubtitleText">Pratinjau resmi dokumen laporan keuangan siap unduh & cetak</p>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <button type="button" onclick="downloadFinancePdfDocument()" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; padding: 10px 18px; border-radius: 12px; font-size: 0.82rem; font-weight: 800; cursor: pointer; box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Download / Cetak PDF</span>
          </button>
          <button type="button" class="modal-close-btn" data-close-modal="modalFinancePdfPreview" onclick="closeFinancePdfModal()" style="background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; cursor: pointer;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </div>

      <!-- Canvas Area A4 Document Sheet -->
      <div style="padding: 24px; overflow-y: auto; background: #1e293b; flex: 1; display: flex; justify-content: center;">
        <div id="financePdfA4Sheet" style="width: 100%; max-width: 820px; background: #ffffff; color: #0f172a; border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.35); overflow: hidden; font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;">
          <!-- Injected dynamically by openFinancePdfModal() -->
        </div>
      </div>

      <!-- Footer PDF Modal -->
      <div style="padding: 14px 24px; background: #0f172a; border-top: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 0.76rem; color: #94a3b8;">Format Dokumen: Standar A4 Portrait • Otomatis menyesuaikan Periode Bulan, Tahun & Cabang Kos yang dipilih</span>
        <div style="display: flex; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalFinancePdfPreview" onclick="closeFinancePdfModal()" style="background: rgba(255,255,255,0.08); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;">Tutup Pratinjau</button>
          <button type="button" onclick="downloadFinancePdfDocument()" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #0d9488 0%, #0a5661 100%); color: #ffffff; border: 1px solid #14b8a6; padding: 9px 20px; border-radius: 11px; font-size: 0.82rem; font-weight: 800; cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            <span>Simpan sebagai PDF</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: POP-UP KONFIRMASI LOGOUT ADMIN (MODERN ANIMATION)
       ========================================================================= -->
  <div class="kmr-logout-overlay" id="modalAdminLogoutConfirm" style="display: none;">
    <div class="kmr-logout-card">
      <div class="kmr-logout-icon-wrap">
        <div class="kmr-logout-pulse-ring"></div>
        <div class="kmr-logout-icon-circle">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </div>
      </div>
      <h3 class="kmr-logout-title">Yakin Ingin Keluar?</h3>
      <p class="kmr-logout-subtitle">Sesi Anda sebagai <strong>{{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</strong> di <strong>{{ $setting->kos_name }}</strong> akan diakhiri. Anda perlu login kembali untuk mengakses panel {{ Auth::user()->isOwner() ? 'owner' : 'admin' }}.</p>
      <div class="kmr-logout-actions">
        <button type="button" class="kmr-logout-btn-cancel" id="btnCancelAdminLogout">Batal</button>
        <button type="button" class="kmr-logout-btn-confirm" id="btnConfirmAdminLogout">Ya, Keluar</button>
      </div>
    </div>
  </div>

  <script>
    // Konfirmasi Logout Admin Modern
    var modalAdminLogout = document.getElementById('modalAdminLogoutConfirm');
    var formLogoutAdmin = document.getElementById('formLogoutAdmin');
    document.getElementById('btnTriggerLogoutAdmin')?.addEventListener('click', function(e) {
      e.preventDefault();
      if (modalAdminLogout) {
        modalAdminLogout.classList.add('open');
        modalAdminLogout.style.display = 'flex';
        modalAdminLogout.style.pointerEvents = 'auto';
      }
    });
    document.getElementById('btnCancelAdminLogout')?.addEventListener('click', function() {
      if (modalAdminLogout) {
        modalAdminLogout.classList.remove('open');
        modalAdminLogout.style.display = 'none';
        modalAdminLogout.style.pointerEvents = 'none';
      }
    });
    document.getElementById('btnConfirmAdminLogout')?.addEventListener('click', function() {
      formLogoutAdmin?.submit();
    });
    modalAdminLogout?.addEventListener('click', function(e) {
      if (e.target === modalAdminLogout) {
        modalAdminLogout.classList.remove('open');
        modalAdminLogout.style.display = 'none';
        modalAdminLogout.style.pointerEvents = 'none';
      }
    });

    // Delegated dismissal for all modals in dashboard
    document.addEventListener('click', function(e) {
      var closeBtn = e.target.closest('[data-close-modal]');
      if (closeBtn) {
        var mId = closeBtn.getAttribute('data-close-modal');
        var mEl = document.getElementById(mId);
        if (mEl) {
          mEl.classList.remove('open');
          mEl.style.display = 'none';
          mEl.style.pointerEvents = 'none';
          document.body.style.overflow = '';
        }
      }
    });

    // Pindah Kamar Admin Action (Izinkan / Tolak)
    function handleTransferAction(id, status) {
      var note = prompt('Masukkan catatan/pesan pengelola untuk penghuni (opsional):', status === 'approved' ? 'Pengajuan disetujui, silakan koordinasi kunci kamar baru.' : 'Mohon maaf, kamar yang diminta belum tersedia saat ini.');
      if (note === null) return;

      fetch('/api/room-transfers/' + id + '/status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          status: status,
          admin_note: note
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          alert(data.message);
          window.location.reload();
        } else {
          alert(data.message || 'Gagal memperbarui status');
        }
      })
      .catch(function(err) {
        console.error(err);
        alert('Terjadi kesalahan saat memproses status pindah kamar.');
      });
    }

    // Filter Table Pindah Kamar (2 Cabang & Lintas Cabang)
    var currentTransferStatus = 'all';
    var currentTransferLocation = 'all';

    function applyTransferFilters() {
      var inputEl = document.getElementById('transferSearchInput');
      var input = inputEl ? inputEl.value.toLowerCase() : '';
      var rows = document.querySelectorAll('#transfersTableBody tr[data-status]');
      rows.forEach(function(r) {
        var searchData = r.getAttribute('data-search') || '';
        var rStatus = r.getAttribute('data-status') || '';
        var rLoc = r.getAttribute('data-location') || '';
        var rFrom = r.getAttribute('data-from-loc') || '';
        var rTo = r.getAttribute('data-to-loc') || '';

        var matchSearch = !input || searchData.includes(input);
        var matchStatus = (currentTransferStatus === 'all' || rStatus === currentTransferStatus);
        var matchLoc = true;
        if (currentTransferLocation === 'Lintas Cabang') {
          matchLoc = (rLoc === 'Lintas Cabang');
        } else if (currentTransferLocation !== 'all') {
          matchLoc = (rLoc === currentTransferLocation || rFrom === currentTransferLocation || rTo === currentTransferLocation);
        }

        r.style.display = (matchSearch && matchStatus && matchLoc) ? '' : 'none';
      });
    }

    function filterTransferTable() {
      applyTransferFilters();
    }

    function filterTransferStatus(status, btn) {
      currentTransferStatus = status;
      document.querySelectorAll('#transferStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyTransferFilters();
    }

    function filterTransferLocation(loc, btn) {
      currentTransferLocation = loc;
      document.querySelectorAll('#transferLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyTransferFilters();
    }
    window.filterTransferLocation = filterTransferLocation;

    // =========================================================================
    // KURVA PERTUMBUHAN PENDAPATAN & ARUS KAS (CANVAS SPLINE CURVE INTERACTIVE)
    // =========================================================================
    var chartMonths = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    window.chartIncomeFull = {!! json_encode($chartIncome ?? array_fill(0, 12, 0)) !!};
    window.chartExpenseFull = {!! json_encode($chartExpense ?? array_fill(0, 12, 0)) !!};
    window.chartNetFull = {!! json_encode($chartNet ?? array_fill(0, 12, 0)) !!};

    var currentCurvePeriod = "1y";

    function handleYearWheelScroll(e) {
      e.preventDefault();
      var delta = e.deltaY > 0 ? 1 : -1;
      scrollFinanceYear(delta);
    }
    window.handleYearWheelScroll = handleYearWheelScroll;

    function scrollFinanceYear(delta) {
      var select = document.getElementById('selectFinanceYear');
      if (!select) return;
      var currentYear = parseInt(select.value, 10) || 2026;
      var targetYear = currentYear + delta;
      if (targetYear < 2020) {
        targetYear = 2020;
        updateYearNavButtons();
        return;
      }

      var optionExists = false;
      for (var i = 0; i < select.options.length; i++) {
        if (parseInt(select.options[i].value, 10) === targetYear) {
          optionExists = true;
          break;
        }
      }
      if (!optionExists) {
        var opt = document.createElement('option');
        opt.value = targetYear;
        opt.textContent = 'Tahun ' + targetYear;
        select.appendChild(opt);
      }

      select.value = targetYear;
      updateYearNavButtons();
      changeFinanceYear(targetYear);
    }
    window.scrollFinanceYear = scrollFinanceYear;

    function updateYearNavButtons() {
      var select = document.getElementById('selectFinanceYear');
      var btnPrev = document.getElementById('btnPrevYear');
      if (select && btnPrev) {
        var currentYear = parseInt(select.value, 10) || 2026;
        btnPrev.disabled = (currentYear <= 2020);
      }
    }
    window.updateYearNavButtons = updateYearNavButtons;

    function scrollFinanceMonth(delta) {
      var select = document.getElementById('selectFinanceMonth');
      if (!select) return;
      var curVal = select.value;
      var options = ['all', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
      var curIdx = options.indexOf(curVal);
      if (curIdx === -1) curIdx = 0;
      var newIdx = curIdx + delta;
      if (newIdx < 0) newIdx = options.length - 1;
      if (newIdx >= options.length) newIdx = 0;
      select.value = options[newIdx];
      changeFinanceMonth(options[newIdx]);
    }
    window.scrollFinanceMonth = scrollFinanceMonth;

    function changeFinanceMonth(monthVal) {
      if (window.kosApp && typeof window.kosApp.renderFinancesTable === 'function') {
        var searchVal = document.getElementById('financeSearchInput')?.value || '';
        var activeTypeBtn = document.querySelector('#financeTypeFilter .filter-pill.active');
        var activeType = activeTypeBtn ? (activeTypeBtn.getAttribute('data-ftype') || 'all') : 'all';
        var activeLocBtn = document.querySelector('#financeLocationFilters .filter-pill.active');
        var activeLoc = activeLocBtn ? (activeLocBtn.getAttribute('data-floc') || 'all') : 'all';
        window.kosApp.renderFinancesTable(searchVal, activeType, activeLoc);
      }
    }
    window.changeFinanceMonth = changeFinanceMonth;

    async function changeFinanceYear(year) {
      var yr = parseInt(year, 10) || 2026;
      var select = document.getElementById('selectFinanceYear');
      if (select && parseInt(select.value, 10) !== yr) {
        select.value = yr;
      }

      document.querySelectorAll('.fin-selected-year-badge').forEach(function(el) {
        el.textContent = yr;
      });
      var curveYearEl = document.getElementById('curveYearLabel');
      if (curveYearEl) curveYearEl.textContent = '(Tahun ' + yr + ')';

      // Sinkronkan tabel transaksi, 4 KPI, dan kurva secara instan dari data lokal
      if (window.kosApp && typeof window.kosApp.renderFinancesTable === 'function') {
        var searchVal = document.getElementById('financeSearchInput')?.value || '';
        var activeTypeBtn = document.querySelector('#financeTypeFilter .filter-pill.active');
        var activeType = activeTypeBtn ? (activeTypeBtn.getAttribute('data-ftype') || 'all') : 'all';
        var activeLocBtn = document.querySelector('#financeLocationFilters .filter-pill.active');
        var activeLoc = activeLocBtn ? (activeLocBtn.getAttribute('data-floc') || 'all') : 'all';
        window.kosApp.renderFinancesTable(searchVal, activeType, activeLoc);
      }

      try {
        var activeLocEl = document.querySelector('#financeLocationFilters .filter-pill.active');
        var locParam = activeLocEl ? (activeLocEl.getAttribute('data-floc') || 'all') : 'all';
        var res = await fetch('/api/finances/stats?year=' + encodeURIComponent(yr) + '&location=' + encodeURIComponent(locParam));
        var data = await res.json();
        if (data && data.success) {
          if (document.getElementById('finKpiIncomeVal')) {
            document.getElementById('finKpiIncomeVal').textContent = 'Rp ' + Number(data.income || 0).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiExpenseVal')) {
            document.getElementById('finKpiExpenseVal').textContent = 'Rp ' + Number(data.expense || 0).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiNetVal')) {
            document.getElementById('finKpiNetVal').textContent = 'Rp ' + Number(data.net || 0).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiAvgVal')) {
            document.getElementById('finKpiAvgVal').textContent = 'Rp ' + Number(data.avgMonthly || 0).toLocaleString('id-ID');
          }

          window.chartIncomeFull = Array.isArray(data.chartIncome) ? data.chartIncome : new Array(12).fill(0);
          window.chartExpenseFull = Array.isArray(data.chartExpense) ? data.chartExpense : new Array(12).fill(0);
          window.chartNetFull = Array.isArray(data.chartNet) ? data.chartNet : new Array(12).fill(0);
          drawRevenueCurve();
        }
      } catch (err) {
        console.error('Error changing finance year:', err);
      }
    }
    window.changeFinanceYear = changeFinanceYear;

    function drawRevenueCurve() {
      var canvas = document.getElementById("revenueCurveCanvas");
      if (!canvas) return;
      var ctx = canvas.getContext("2d");
      var dpr = window.devicePixelRatio || 1;

      var rect = canvas.getBoundingClientRect();
      var width = rect.width;
      var height = rect.height;
      if (width === 0 || height === 0) return;

      canvas.width = width * dpr;
      canvas.height = height * dpr;
      ctx.scale(dpr, dpr);

      var incArr = Array.isArray(window.chartIncomeFull) ? window.chartIncomeFull : new Array(12).fill(0);
      var expArr = Array.isArray(window.chartExpenseFull) ? window.chartExpenseFull : new Array(12).fill(0);
      var netArr = Array.isArray(window.chartNetFull) ? window.chartNetFull : new Array(12).fill(0);

      var isYearEmpty = incArr.every(function(v) { return Number(v) === 0; }) && expArr.every(function(v) { return Number(v) === 0; });

      var labels, income, expense, net;
      if (currentCurvePeriod === "30d") {
        labels = ["Mg 1", "Mg 2", "Mg 3", "Mg 4"];
        if (isYearEmpty) {
          income = [0, 0, 0, 0];
          expense = [0, 0, 0, 0];
          net = [0, 0, 0, 0];
        } else {
          // Ambil bulan terakhir yang memiliki transaksi pada tahun terpilih
          var activeIdx = 9;
          for (var m = 11; m >= 0; m--) {
            if (incArr[m] > 0 || expArr[m] > 0) {
              activeIdx = m;
              break;
            }
          }
          var mI = incArr[activeIdx] || 0;
          var mE = expArr[activeIdx] || 0;
          income = [Math.round(mI * 0.25), Math.round(mI * 0.25), Math.round(mI * 0.25), mI - Math.round(mI * 0.25) * 3];
          expense = [Math.round(mE * 0.25), Math.round(mE * 0.25), Math.round(mE * 0.25), mE - Math.round(mE * 0.25) * 3];
          net = income.map(function(val, idx) { return val - expense[idx]; });
        }
      } else if (currentCurvePeriod === "6m") {
        labels = chartMonths.slice(6, 12);
        income = incArr.slice(6, 12);
        expense = expArr.slice(6, 12);
        net = netArr.slice(6, 12);
      } else {
        labels = chartMonths;
        income = incArr;
        expense = expArr;
        net = netArr;
      }

      ctx.clearRect(0, 0, width, height);

      var padLeft = 64;
      var padRight = 24;
      var padTop = 24;
      var padBottom = 34;

      var plotW = width - padLeft - padRight;
      var plotH = height - padTop - padBottom;

      var rawMax = Math.max.apply(null, income.concat(expense, net, [0]));
      var isAllZero = (rawMax === 0);
      var maxVal = isAllZero ? 10000000 : Math.ceil((rawMax * 1.15) / 1000000) * 1000000;
      if (maxVal <= 0) maxVal = 10000000;

      var isDark = document.documentElement.getAttribute("data-theme") === "dark";
      var gridColor = isDark ? "rgba(255, 255, 255, 0.07)" : "rgba(0, 0, 0, 0.06)";
      var textColor = isDark ? "#94a3b8" : "#64748b";

      // Draw Gridlines & Y-Axis labels
      ctx.textAlign = "right";
      ctx.textBaseline = "middle";
      ctx.font = "600 11px Outfit, sans-serif";
      ctx.fillStyle = textColor;

      var ySteps = 4;
      for (var i = 0; i <= ySteps; i++) {
        var yVal = (maxVal / ySteps) * i;
        var yPos = padTop + plotH - (i / ySteps) * plotH;

        ctx.strokeStyle = i === 0 ? "rgba(13, 148, 136, 0.3)" : gridColor;
        ctx.lineWidth = i === 0 ? 1.5 : 1;
        ctx.beginPath();
        ctx.moveTo(padLeft, yPos);
        ctx.lineTo(width - padRight, yPos);
        ctx.stroke();

        var labelText = yVal === 0 ? "Rp 0" : (yVal >= 1000000 ? (yVal / 1000000).toFixed(yVal % 1000000 === 0 ? 0 : 1) + " Jt" : (yVal / 1000).toFixed(0) + " Rb");
        ctx.fillText(labelText, padLeft - 10, yPos);
      }

      // X Points
      var n = labels.length;
      var stepX = n > 1 ? plotW / (n - 1) : plotW;
      var points = [];

      for (var j = 0; j < n; j++) {
        var px = padLeft + j * stepX;
        var incVal = Math.max(0, Number(income[j] || 0));
        var expVal = Math.max(0, Number(expense[j] || 0));
        var netVal = Math.max(0, Number(net[j] || 0));
        var yInc = padTop + plotH - (incVal / maxVal) * plotH;
        var yExp = padTop + plotH - (expVal / maxVal) * plotH;
        var yNet = padTop + plotH - (netVal / maxVal) * plotH;
        points.push({ x: px, inc: yInc, exp: yExp, net: yNet, label: labels[j], rawInc: Number(income[j] || 0), rawExp: Number(expense[j] || 0), rawNet: Number(net[j] || 0) });

        // Draw X Axis label
        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        ctx.fillStyle = textColor;
        ctx.fillText(labels[j], px, padTop + plotH + 10);
      }

      canvas._curvePoints = points;

      // Draw Bezier Spline
      function drawSpline(pts, prop, strokeColor, gradStart, gradEnd, fill) {
        if (pts.length < 2) return;
        ctx.save();

        if (fill && !isAllZero) {
          var grad = ctx.createLinearGradient(0, padTop, 0, padTop + plotH);
          grad.addColorStop(0, gradStart);
          grad.addColorStop(1, gradEnd);

          ctx.beginPath();
          ctx.moveTo(pts[0].x, padTop + plotH);
          ctx.lineTo(pts[0].x, pts[0][prop]);

          for (var k = 0; k < pts.length - 1; k++) {
            var cp1x = pts[k].x + (pts[k + 1].x - pts[k].x) / 2;
            var cp1y = pts[k][prop];
            var cp2x = cp1x;
            var cp2y = pts[k + 1][prop];
            ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, pts[k + 1].x, pts[k + 1][prop]);
          }

          ctx.lineTo(pts[pts.length - 1].x, padTop + plotH);
          ctx.closePath();
          ctx.fillStyle = grad;
          ctx.fill();
        }

        // Line Stroke
        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0][prop]);
        for (var l = 0; l < pts.length - 1; l++) {
          var c1x = pts[l].x + (pts[l + 1].x - pts[l].x) / 2;
          var c1y = pts[l][prop];
          var c2x = c1x;
          var c2y = pts[l + 1][prop];
          ctx.bezierCurveTo(c1x, c1y, c2x, c2y, pts[l + 1].x, pts[l + 1][prop]);
        }
        ctx.strokeStyle = strokeColor;
        ctx.lineWidth = 2.8;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.stroke();

        // Dots
        pts.forEach(function(pt) {
          ctx.beginPath();
          ctx.arc(pt.x, pt[prop], 4, 0, Math.PI * 2);
          ctx.fillStyle = strokeColor;
          ctx.fill();
          ctx.lineWidth = 2;
          ctx.strokeStyle = isDark ? "#0f172a" : "#ffffff";
          ctx.stroke();
        });

        ctx.restore();
      }

      // Draw 3 Curves: Income (Green/Cyan Glow), Net (Blue), Expense (Red)
      drawSpline(points, "inc", "#10b981", "rgba(16, 185, 129, 0.25)", "rgba(16, 185, 129, 0.0)", true);
      drawSpline(points, "net", "#0d9488", "rgba(13, 148, 136, 0.18)", "rgba(13, 148, 136, 0.0)", false);
      drawSpline(points, "exp", "#ef4444", "rgba(239, 68, 68, 0.15)", "rgba(239, 68, 68, 0.0)", false);

      // Jika tahun yang dipilih belum pernah diisi (semua 0), tampilkan indikator bahwa kurva berada di titik 0
      if (isAllZero) {
        var selYr = document.getElementById('selectFinanceYear')?.value || 'Ini';
        var zeroMsg = "Kurva di Titik 0 — Belum Ada Data Keuangan Tahun " + selYr + " (Rp 0)";
        ctx.save();
        ctx.font = "700 12px Outfit, sans-serif";
        var textW = ctx.measureText(zeroMsg).width;
        var boxW = textW + 28;
        var boxH = 30;
        var boxX = padLeft + (plotW - boxW) / 2;
        var boxY = padTop + plotH - 46;
        ctx.fillStyle = "rgba(15, 23, 42, 0.86)";
        ctx.beginPath();
        if (ctx.roundRect) {
          ctx.roundRect(boxX, boxY, boxW, boxH, 999);
        } else {
          ctx.rect(boxX, boxY, boxW, boxH);
        }
        ctx.fill();
        ctx.fillStyle = "#5eead4";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText(zeroMsg, padLeft + plotW / 2, boxY + boxH / 2);
        ctx.restore();
      }
    }
    window.drawRevenueCurve = drawRevenueCurve;

    function switchRevenueCurve(period, btn) {
      currentCurvePeriod = period;
      document.querySelectorAll("#curvePeriodFilters .filter-pill").forEach(function(b) { b.classList.remove("active"); });
      if (btn) btn.classList.add("active");
      drawRevenueCurve();
    }

    // Interactive Tooltip on Canvas
    document.addEventListener("DOMContentLoaded", function() {
      var canvas = document.getElementById("revenueCurveCanvas");
      var tt = document.getElementById("curveTooltip");
      if (canvas && tt) {
        canvas.addEventListener("mousemove", function(e) {
          var rect = canvas.getBoundingClientRect();
          var mx = e.clientX - rect.left;
          var my = e.clientY - rect.top;

          var pts = canvas._curvePoints || [];
          var closest = null;
          var minDist = 9999;

          pts.forEach(function(pt) {
            var dist = Math.abs(pt.x - mx);
            if (dist < minDist) {
              minDist = dist;
              closest = pt;
            }
          });

          if (closest && minDist < 35) {
            tt.style.display = "block";
            tt.style.left = Math.min(Math.max(closest.x - 70, 10), rect.width - 150) + "px";
            tt.style.top = Math.max(closest.inc - 80, 10) + "px";

            document.getElementById("ttPeriod").textContent = "Periode " + closest.label;
            document.getElementById("ttIncome").textContent = "Pemasukan: Rp " + closest.rawInc.toLocaleString("id-ID");
            document.getElementById("ttExpense").textContent = "Pengeluaran: Rp " + closest.rawExp.toLocaleString("id-ID");
            document.getElementById("ttNet").textContent = "Laba Bersih: Rp " + closest.rawNet.toLocaleString("id-ID");
          } else {
            tt.style.display = "none";
          }
        });

        canvas.addEventListener("mouseleave", function() {
          tt.style.display = "none";
        });

        window.addEventListener("resize", drawRevenueCurve);
        setTimeout(drawRevenueCurve, 200);
      }
    });

    // =========================================================================
    // TAGIHAN SEWA (TAB-BILLS) FILTER 2 CABANG & AKSI CEPAT
    // =========================================================================
    var currentBillingStatus = "all";
    var currentBillingLocation = "all";

    function applyBillingFilters() {
      var inputEl = document.getElementById("billingSearchInput");
      var input = inputEl ? inputEl.value.toLowerCase() : "";
      var rows = document.querySelectorAll("#billingTableBody tr[data-status]");
      var paidCount = 0;
      var dueCount = 0;
      var dueNominal = 0;

      rows.forEach(function(r) {
        var s = r.getAttribute("data-search") || "";
        var rStatus = r.getAttribute("data-status") || "";
        var rLoc = r.getAttribute("data-location") || "Lokasi 1";
        var rPrice = parseInt(r.getAttribute("data-price") || "0", 10);

        var matchLoc = (currentBillingLocation === "all" || rLoc === currentBillingLocation);
        if (matchLoc) {
          if (rStatus === "paid") paidCount++;
          if (rStatus === "due") {
            dueCount++;
            dueNominal += rPrice;
          }
        }

        var matchSearch = !input || s.includes(input);
        var matchStatus = (currentBillingStatus === "all" || rStatus === currentBillingStatus);

        r.style.display = (matchLoc && matchSearch && matchStatus) ? "" : "none";
      });

      var paidEl = document.getElementById("billKpiPaidVal");
      var dueEl = document.getElementById("billKpiDueVal");
      var dueNomEl = document.getElementById("billKpiDueNom");
      var rateEl = document.getElementById("billKpiRateVal");

      if (paidEl) paidEl.textContent = paidCount + " Kamar";
      if (dueEl) dueEl.textContent = dueCount + " Kamar";
      if (dueNomEl) dueNomEl.textContent = "Rp " + dueNominal.toLocaleString("id-ID");
      if (rateEl) {
        var totOcc = paidCount + dueCount;
        var rate = totOcc > 0 ? Math.round((paidCount / totOcc) * 1000) / 10 : 100;
        rateEl.textContent = rate + "%";
      }
    }

    function filterBillingTable() {
      applyBillingFilters();
    }

    function filterBillingStatus(status, btn) {
      currentBillingStatus = status;
      document.querySelectorAll("#billingStatusFilter .filter-pill").forEach(function(p) { p.classList.remove("active"); });
      if (btn) btn.classList.add("active");
      applyBillingFilters();
    }

    function filterBillingLocation(loc, btn) {
      currentBillingLocation = loc;
      document.querySelectorAll("#billingLocationFilters .filter-pill").forEach(function(p) { p.classList.remove("active"); });
      if (btn) btn.classList.add("active");
      applyBillingFilters();
    }
    window.filterBillingLocation = filterBillingLocation;

    window.openManualPaymentReceiptModal = function(roomId, roomNumber, tenantName, price, location) {
      if (roomId) {
        var select = document.getElementById('payRecRoomId');
        if (select) select.value = roomId;
        var amt = document.getElementById('payRecAmount');
        if (amt && price) amt.value = price;
        var desc = document.getElementById('payRecDesc');
        var nowMonth = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date());
        if (desc) desc.value = 'Pembayaran Sewa Kamar #' + (roomNumber || '') + (tenantName && tenantName !== '-' ? ' (' + tenantName + ')' : '') + ' Periode ' + nowMonth;
      }
      window.openModal('modalManualPaymentReceipt');
    };

    window.onManualPayRoomChanged = function(sel) {
      var opt = sel.options[sel.selectedIndex];
      if (!opt || !opt.value) return;
      var price = opt.getAttribute('data-price');
      var tenant = opt.getAttribute('data-tenant');
      var room = opt.getAttribute('data-room');
      if (price) {
        var amt = document.getElementById('payRecAmount');
        if (amt) amt.value = price;
      }
      var desc = document.getElementById('payRecDesc');
      var nowMonth = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date());
      if (desc) desc.value = 'Pembayaran Sewa Kamar #' + (room || '') + (tenant && tenant !== '(Unit Kosong)' ? ' (' + tenant + ')' : '') + ' Periode ' + nowMonth;
    };

    window.submitManualPaymentReceipt = function(e) {
      e.preventDefault();
      var btn = document.getElementById('btnSubmitPayRec');
      var prevTxt = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = 'Menyimpan & Mengirim...';

      var fd = new FormData();
      fd.append('type', 'income');
      fd.append('category', 'Sewa Kamar');
      fd.append('room_id', document.getElementById('payRecRoomId').value);
      fd.append('date', document.getElementById('payRecDate').value);
      fd.append('timezone', document.getElementById('payRecTimezone').value);
      fd.append('amount', document.getElementById('payRecAmount').value);
      fd.append('payment_method', document.getElementById('payRecMethod').value);
      fd.append('description', document.getElementById('payRecDesc').value);
      fd.append('send_to_tenant', document.getElementById('payRecSendToTenant').checked ? '1' : '0');

      var fileInput = document.getElementById('payRecProofFile');
      if (fileInput && fileInput.files[0]) {
        fd.append('proof_image', fileInput.files[0]);
      }

      fetch('/api/finances', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: fd
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = prevTxt;
        if (data.success) {
          alert(data.message || 'Pembayaran berhasil dicatat & kuitansi resmi langsung dikirim ke pelanggan!');
          window.closeModal('modalManualPaymentReceipt');
          window.location.reload();
        } else {
          alert(data.message || 'Gagal memproses pembayaran.');
        }
      })
      .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = prevTxt;
        alert('Terjadi kesalahan saat memproses pembayaran.');
      });
    };

    function quickMarkPaid(roomNumber, tenantName, price) {
      if (!confirm("Konfirmasi bahwa tagihan Kamar #" + roomNumber + " (" + tenantName + ") sebesar Rp " + price.toLocaleString("id-ID") + " telah lunas diterima?")) return;

      fetch("/api/finances", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}"
        },
        body: JSON.stringify({
          desc: "Pembayaran Sewa Kamar #" + roomNumber + " (" + tenantName + ") - Lunas Dikonfirmasi",
          type: "income",
          category: "Sewa Kamar",
          amount: price,
          method: "Tunai (Cash / Transfer Langsung)"
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        alert("Pembayaran berhasil dikonfirmasi dan dicatat ke Buku Kas!");
        window.location.reload();
      })
      .catch(function(err) {
        alert("Gagal mencatat pembayaran.");
      });
    }

    function exportBillingReport() {
      window.print();
    }

    function formatRpPdf(val) {
      return "Rp " + Number(val || 0).toLocaleString("id-ID");
    }

    function buildFinancePdfHtml() {
      var payload = (window.kosApp && typeof window.kosApp.getFinanceReportPayload === "function")
        ? window.kosApp.getFinanceReportPayload()
        : {
            year: parseInt(document.getElementById("selectFinanceYear")?.value || 2026, 10),
            isMonthFiltered: false,
            monthNum: null,
            monthName: "Semua Bulan (1 Tahun)",
            periodTitle: "TAHUN 2026",
            locationFilter: "all",
            branchLabel: "Gabungan 2 Cabang",
            branch1Name: "{{ $branch1Name }}",
            branch2Name: "{{ $branch2Name }}",
            kosName: "{{ $setting->kos_name ?? 'Kos Mas Robi' }}",
            ownerName: "{{ $setting->owner_name ?? 'Mas Robi' }}",
            kosAddress: "{{ $setting->kos_address ?? 'Sleman, DI Yogyakarta' }}",
            b1Inc: 0, b1Exp: 0, b1Net: 0,
            b2Inc: 0, b2Exp: 0, b2Net: 0,
            totalInc: 0, totalExp: 0, totalNet: 0, avgMonthly: 0,
            mInc: new Array(12).fill(0),
            mExp: new Array(12).fill(0),
            mNet: new Array(12).fill(0),
            transactions: []
          };

      var fullMonths = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
      var maxMonthVal = Math.max.apply(null, payload.mInc.concat(payload.mExp, [1]));
      var hasTransactions = payload.transactions && payload.transactions.length > 0;
      var printedDate = new Date().toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });
      var monthCode = payload.isMonthFiltered ? ("M" + payload.monthNum + "/") : "";
      var docCode = "FIN-PDF/" + payload.year + "/" + monthCode + (payload.locationFilter === "all" ? "ALL" : (payload.locationFilter === "Lokasi 2" ? "B2" : "B1"));

      var monthlyRowsHtml = "";
      for (var m = 0; m < 12; m++) {
        var inc = payload.mInc[m] || 0;
        var exp = payload.mExp[m] || 0;
        var net = payload.mNet[m] || 0;
        var barPct = maxMonthVal > 0 ? Math.min(100, Math.round((inc / maxMonthVal) * 100)) : 0;
        var isSelectedMonthRow = payload.isMonthFiltered && (payload.monthNum === (m + 1));
        var rowBg = isSelectedMonthRow ? 'background: #f0fdfa; border: 1.5px solid #0d9488;' : 'border-bottom: 1px solid #f1f5f9;';
        var monthLabelBadge = isSelectedMonthRow
          ? '<span style="display:inline-block;margin-left:6px;font-size:0.62rem;font-weight:800;background:#0d9488;color:#ffffff;padding:1px 6px;border-radius:999px;">Bulan Terpilih</span>'
          : '';

        monthlyRowsHtml +=
          '<tr style="' + rowBg + '">' +
            '<td style="padding:7px 10px;font-weight:700;color:#1e293b;font-size:0.76rem;">' + fullMonths[m] + ' ' + payload.year + monthLabelBadge + '</td>' +
            '<td style="padding:7px 10px;width:130px;">' +
              '<div style="width:100%;height:7px;background:#f1f5f9;border-radius:999px;overflow:hidden;">' +
                '<div style="width:' + barPct + '%;height:100%;background:linear-gradient(90deg,#0d9488,#10b981);border-radius:999px;"></div>' +
              '</div>' +
            '</td>' +
            '<td style="padding:7px 10px;text-align:right;font-weight:700;color:' + (inc > 0 ? '#059669' : '#94a3b8') + ';font-size:0.76rem;">' + formatRpPdf(inc) + '</td>' +
            '<td style="padding:7px 10px;text-align:right;font-weight:700;color:' + (exp > 0 ? '#dc2626' : '#94a3b8') + ';font-size:0.76rem;">' + formatRpPdf(exp) + '</td>' +
            '<td style="padding:7px 10px;text-align:right;font-weight:800;color:' + (net > 0 ? '#0a5661' : (net < 0 ? '#dc2626' : '#94a3b8')) + ';font-size:0.76rem;">' + formatRpPdf(net) + '</td>' +
          '</tr>';
      }

      var trxPeriodText = payload.isMonthFiltered ? (payload.monthName + ' ' + payload.year) : ('Tahun ' + payload.year);
      var trxRowsHtml = "";
      if (!hasTransactions) {
        trxRowsHtml =
          '<tr>' +
            '<td colspan="6" style="padding:26px 16px;text-align:center;background:#f8fafc;color:#64748b;font-size:0.8rem;font-weight:700;border-bottom:1px solid #e2e8f0;">' +
              'DATA NIHIL (Rp 0) — Belum ada transaksi keuangan yang dicatat pada ' + trxPeriodText + '.' +
            '</td>' +
          '</tr>';
      } else {
        payload.transactions.forEach(function(t) {
          var isInc = t.type === "income";
          trxRowsHtml +=
            '<tr style="border-bottom:1px solid #f1f5f9;">' +
              '<td style="padding:8px 10px;font-family:monospace;font-weight:700;font-size:0.74rem;color:#0f172a;">' + (t.id || '-') + '</td>' +
              '<td style="padding:8px 10px;font-size:0.75rem;color:#475569;">' + (t.date || '-') + '</td>' +
              '<td style="padding:8px 10px;font-size:0.74rem;font-weight:700;color:#0a5661;">' + (t.branchName || '-') + '</td>' +
              '<td style="padding:8px 10px;font-size:0.75rem;font-weight:600;color:#1e293b;">' + (t.desc || '-') + '</td>' +
              '<td style="padding:8px 10px;font-size:0.73rem;color:#64748b;">' + (t.category || '-') + ' • ' + (t.method || '-') + '</td>' +
              '<td style="padding:8px 10px;text-align:right;font-weight:800;font-size:0.76rem;color:' + (isInc ? '#059669' : '#dc2626') + ';">' + (isInc ? '+ ' : '- ') + formatRpPdf(t.amount) + '</td>' +
            '</tr>';
        });
      }

      var kpiPeriodSuffix = payload.isMonthFiltered ? (payload.monthName.substring(0,3) + ' ' + payload.year) : payload.year;
      var fourthKpiHtml = payload.isMonthFiltered
        ? '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-top:3.5px solid #0284c7;border-radius:12px;padding:14px;">' +
            '<div style="font-size:0.66rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">STATUS SURPLUS BULANAN</div>' +
            '<div style="font-size:1.1rem;font-weight:900;color:#0284c7;margin-top:6px;">' + (payload.totalNet >= 0 ? 'Surplus Finansial' : 'Defisit Kas') + '</div>' +
            '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Kondisi Kas ' + payload.monthName + '</div>' +
          '</div>'
        : '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-top:3.5px solid #0284c7;border-radius:12px;padding:14px;">' +
            '<div style="font-size:0.66rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">RATA-RATA BERSIH / BLN</div>' +
            '<div style="font-size:1.1rem;font-weight:900;color:#0284c7;margin-top:6px;">' + formatRpPdf(payload.avgMonthly) + '</div>' +
            '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Estimasi Rata-rata Bulanan</div>' +
          '</div>';

      return (
        '<div class="pdf-executive-sheet">' +
          /* Executive Header */
          '<div style="background:linear-gradient(135deg,#071f24 0%,#0a3d46 55%,#0a5661 100%);color:#ffffff;padding:28px 32px;display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;border-bottom:4px solid #14b8a6;">' +
            '<div style="max-width:440px;">' +
              '<div style="display:inline-flex;align-items:center;gap:8px;background:rgba(45,212,191,0.16);border:1px solid rgba(45,212,191,0.38);padding:4px 12px;border-radius:999px;font-size:0.68rem;font-weight:800;letter-spacing:0.06em;color:#5eead4;margin-bottom:10px;">' + (payload.isMonthFiltered ? 'LAPORAN KEUANGAN RESMI & BUKU KAS BULANAN' : 'LAPORAN KEUANGAN RESMI & BUKU KAS TAHUNAN') + '</div>' +
              '<h1 style="margin:0;font-size:1.5rem;font-weight:900;letter-spacing:-0.02em;color:#ffffff;">' + payload.kosName + '</h1>' +
              '<p style="margin:5px 0 0 0;font-size:0.78rem;color:rgba(255,255,255,0.78);line-height:1.45;">' + payload.kosAddress + '</p>' +
            '</div>' +
            '<div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:14px;padding:12px 18px;text-align:right;min-width:210px;">' +
              '<div style="font-size:0.66rem;text-transform:uppercase;letter-spacing:0.08em;color:#99f6e4;font-weight:800;">PERIODE ANGGARAN</div>' +
              '<div style="font-size:1.35rem;font-weight:900;color:#ffffff;margin:2px 0;">' + payload.periodTitle + '</div>' +
              '<div style="font-size:0.72rem;color:rgba(255,255,255,0.82);font-weight:600;">No. Dokumen: ' + docCode + '</div>' +
              '<div style="font-size:0.7rem;color:rgba(255,255,255,0.68);margin-top:2px;">Dicetak: ' + printedDate + '</div>' +
            '</div>' +
          '</div>' +

          /* Body Content */
          '<div style="padding:26px 32px;display:flex;flex-direction:column;gap:22px;">' +
            /* Meta Scope Bar */
            '<div style="display:flex;justify-content:space-between;align-items:center;background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;padding:10px 16px;flex-wrap:wrap;gap:8px;">' +
              '<div style="font-size:0.78rem;color:#0f172a;"><strong>Cakupan Laporan:</strong> ' + payload.branchLabel + '</div>' +
              '<div style="font-size:0.76rem;font-weight:800;color:' + (hasTransactions ? '#059669' : '#64748b') + ';">Status Data: ' + (hasTransactions ? (payload.transactions.length + ' Transaksi Terverifikasi') : 'Data Kosong / Rp 0') + '</div>' +
            '</div>' +

            /* 4 KPI Executive Cards */
            '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">' +
              '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-top:3.5px solid #10b981;border-radius:12px;padding:14px;">' +
                '<div style="font-size:0.66rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">TOTAL PEMASUKAN (' + kpiPeriodSuffix + ')</div>' +
                '<div style="font-size:1.1rem;font-weight:900;color:#059669;margin-top:6px;">' + formatRpPdf(payload.totalInc) + '</div>' +
                '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Pendapatan Sewa Kamar</div>' +
              '</div>' +
              '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-top:3.5px solid #ef4444;border-radius:12px;padding:14px;">' +
                '<div style="font-size:0.66rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">BEBAN OPERASIONAL (' + kpiPeriodSuffix + ')</div>' +
                '<div style="font-size:1.1rem;font-weight:900;color:#dc2626;margin-top:6px;">' + formatRpPdf(payload.totalExp) + '</div>' +
                '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Listrik, WiFi & Perawatan</div>' +
              '</div>' +
              '<div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-top:3.5px solid #0d9488;border-radius:12px;padding:14px;">' +
                '<div style="font-size:0.66rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">LABA BERSIH (' + kpiPeriodSuffix + ')</div>' +
                '<div style="font-size:1.1rem;font-weight:900;color:#0a5661;margin-top:6px;">' + formatRpPdf(payload.totalNet) + '</div>' +
                '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Net Cashflow Periode</div>' +
              '</div>' +
              fourthKpiHtml +
            '</div>' +

            /* Komparasi Neraca 2 Cabang Kos */
            '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' +
              '<div style="background:#ffffff;border:1.5px solid #cbd5e1;border-radius:12px;padding:14px 16px;">' +
                '<div style="font-size:0.7rem;font-weight:800;color:#059669;text-transform:uppercase;letter-spacing:0.04em;">NERACA CABANG 1 — ' + payload.branch1Name + '</div>' +
                '<div style="display:flex;justify-content:space-between;margin-top:8px;font-size:0.76rem;">' +
                  '<span>Pemasukan: <strong style="color:#059669;">' + formatRpPdf(payload.b1Inc) + '</strong></span>' +
                  '<span>Opex: <strong style="color:#dc2626;">' + formatRpPdf(payload.b1Exp) + '</strong></span>' +
                  '<span>Surplus: <strong style="color:#0f172a;">' + formatRpPdf(payload.b1Net) + '</strong></span>' +
                '</div>' +
              '</div>' +
              '<div style="background:#ffffff;border:1.5px solid #cbd5e1;border-radius:12px;padding:14px 16px;">' +
                '<div style="font-size:0.7rem;font-weight:800;color:#0a5661;text-transform:uppercase;letter-spacing:0.04em;">NERACA CABANG 2 — ' + payload.branch2Name + '</div>' +
                '<div style="display:flex;justify-content:space-between;margin-top:8px;font-size:0.76rem;">' +
                  '<span>Pemasukan: <strong style="color:#059669;">' + formatRpPdf(payload.b2Inc) + '</strong></span>' +
                  '<span>Opex: <strong style="color:#dc2626;">' + formatRpPdf(payload.b2Exp) + '</strong></span>' +
                  '<span>Surplus: <strong style="color:#0f172a;">' + formatRpPdf(payload.b2Net) + '</strong></span>' +
                '</div>' +
              '</div>' +
            '</div>' +

            /* Rekapitulasi Arus Kas 12 Bulan */
            '<div>' +
              '<div style="font-size:0.84rem;font-weight:800;color:#0f172a;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.04em;">I. Rekapitulasi Arus Kas Bulanan (Januari – Desember ' + payload.year + ')</div>' +
              '<div style="border:1px solid #cbd5e1;border-radius:12px;overflow:hidden;">' +
                '<table style="width:100%;border-collapse:collapse;">' +
                  '<thead>' +
                    '<tr style="background:#0f172a;color:#ffffff;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;">' +
                      '<th style="padding:8px 10px;text-align:left;">Bulan</th>' +
                      '<th style="padding:8px 10px;text-align:left;">Indikator Visual</th>' +
                      '<th style="padding:8px 10px;text-align:right;">Pemasukan</th>' +
                      '<th style="padding:8px 10px;text-align:right;">Pengeluaran</th>' +
                      '<th style="padding:8px 10px;text-align:right;">Laba Bersih</th>' +
                    '</tr>' +
                  '</thead>' +
                  '<tbody>' + monthlyRowsHtml + '</tbody>' +
                '</table>' +
              '</div>' +
            '</div>' +

            /* Rincian Buku Kas & Transaksi */
            '<div>' +
              '<div style="font-size:0.84rem;font-weight:800;color:#0f172a;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.04em;">II. Rincian Buku Kas & Transaksi Resmi (' + trxPeriodText + ')</div>' +
              '<div style="border:1px solid #cbd5e1;border-radius:12px;overflow:hidden;">' +
                '<table style="width:100%;border-collapse:collapse;">' +
                  '<thead>' +
                    '<tr style="background:#f1f5f9;color:#334155;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1.5px solid #cbd5e1;">' +
                      '<th style="padding:8px 10px;text-align:left;">No. Bukti</th>' +
                      '<th style="padding:8px 10px;text-align:left;">Tanggal</th>' +
                      '<th style="padding:8px 10px;text-align:left;">Cabang Kos</th>' +
                      '<th style="padding:8px 10px;text-align:left;">Keterangan Transaksi</th>' +
                      '<th style="padding:8px 10px;text-align:left;">Kategori & Metode</th>' +
                      '<th style="padding:8px 10px;text-align:right;">Nominal</th>' +
                    '</tr>' +
                  '</thead>' +
                  '<tbody>' + trxRowsHtml + '</tbody>' +
                '</table>' +
              '</div>' +
            '</div>' +

            /* Footer Signature & Verification Stamp */
            '<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-top:10px;padding-top:16px;border-top:2px dashed #cbd5e1;flex-wrap:wrap;gap:16px;">' +
              '<div style="max-width:420px;">' +
                '<div style="display:inline-block;background:#ecfdf5;border:1.5px solid #6ee7b7;color:#065f46;font-size:0.7rem;font-weight:800;padding:4px 12px;border-radius:8px;letter-spacing:0.04em;">DOKUMEN KEUANGAN TERVERIFIKASI SISTEM</div>' +
                '<p style="margin:6px 0 0 0;font-size:0.72rem;color:#64748b;line-height:1.45;">Laporan keuangan ini dihasilkan secara otomatis oleh Sistem Manajemen ' + payload.kosName + ' berdasarkan pencatatan kas riil pada periode ' + trxPeriodText + '.</p>' +
              '</div>' +
              '<div style="text-align:center;min-width:190px;">' +
                '<div style="font-size:0.74rem;color:#475569;margin-bottom:44px;">Mengetahui, Pengelola Kos</div>' +
                '<div style="font-size:0.88rem;font-weight:900;color:#0f172a;border-bottom:1.5px solid #0f172a;padding-bottom:3px;">' + payload.ownerName + '</div>' +
                '<div style="font-size:0.68rem;color:#64748b;margin-top:3px;">Owner & Pengelola Utama</div>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>'
      );
    }

    function openFinancePdfModal() {
      var sheet = document.getElementById("financePdfA4Sheet");
      var modal = document.getElementById("modalFinancePdfPreview");
      var yr = document.getElementById("selectFinanceYear")?.value || 2026;
      var mSelect = document.getElementById("selectFinanceMonth");
      var mVal = mSelect ? mSelect.value : 'all';
      var mNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      var periodText = (mVal !== 'all' && mNames[mVal]) ? (mNames[mVal] + ' ' + yr) : ('Tahun ' + yr);

      if (sheet) {
        sheet.innerHTML = buildFinancePdfHtml();
      }
      var titleEl = document.getElementById("pdfModalTitleText");
      if (titleEl) {
        titleEl.textContent = "Dokumen PDF Laporan Keuangan — " + periodText;
      }
      if (modal) {
        modal.classList.add("open");
        modal.style.display = "flex";
        modal.style.pointerEvents = "auto";
      }
    }
    window.openFinancePdfModal = openFinancePdfModal;

    function closeFinancePdfModal() {
      var modal = document.getElementById("modalFinancePdfPreview");
      if (modal) {
        modal.classList.remove("open");
        modal.style.display = "none";
      }
    }
    window.closeFinancePdfModal = closeFinancePdfModal;

    function downloadFinancePdfDocument() {
      var yr = document.getElementById("selectFinanceYear")?.value || 2026;
      var mSelect = document.getElementById("selectFinanceMonth");
      var mVal = mSelect ? mSelect.value : 'all';
      var mNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      var periodSlug = (mVal !== 'all' && mNames[mVal]) ? ('Bulan_' + mNames[mVal] + '_') : '';
      var docTitle = "Laporan_Keuangan_Kos_Mas_Robi_" + periodSlug + "Tahun_" + yr;
      var htmlContent = buildFinancePdfHtml();

      // Gunakan isolasi iframe tersembunyi agar tampilan dashboard utama tidak pernah terdistorsi
      var printFrame = document.getElementById("financePdfPrintIframe");
      if (!printFrame) {
        printFrame = document.createElement("iframe");
        printFrame.id = "financePdfPrintIframe";
        printFrame.style.position = "fixed";
        printFrame.style.right = "0";
        printFrame.style.bottom = "0";
        printFrame.style.width = "0";
        printFrame.style.height = "0";
        printFrame.style.border = "0";
        printFrame.style.visibility = "hidden";
        document.body.appendChild(printFrame);
      }

      var frameDoc = printFrame.contentWindow || printFrame.contentDocument;
      if (frameDoc.document) frameDoc = frameDoc.document;

      frameDoc.open();
      frameDoc.write(
        '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>' + docTitle + '</title>' +
        '<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">' +
        '<style>' +
          '*, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }' +
          'body { margin: 0; padding: 20px; background: #ffffff; font-family: "Outfit", -apple-system, BlinkMacSystemFont, sans-serif; color: #0f172a; }' +
          '.pdf-executive-sheet { max-width: 840px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; }' +
          '@page { size: A4 portrait; margin: 8mm; }' +
          '@media print { body { padding: 0; background: #ffffff; } .pdf-executive-sheet { max-width: 100%; border-radius: 0; box-shadow: none; } }' +
        '</style></head><body>' +
        htmlContent +
        '</body></html>'
      );
      frameDoc.close();

      setTimeout(function() {
        try {
          printFrame.contentWindow.focus();
          printFrame.contentWindow.print();
        } catch(e) {
          var pop = window.open("", "_blank", "width=960,height=800");
          if (pop) {
            pop.document.open();
            pop.document.write(
              '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>' + docTitle + '</title>' +
              '<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">' +
              '<style>*, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } body { margin: 0; padding: 20px; background: #f1f5f9; font-family: "Outfit", sans-serif; } .pdf-executive-sheet { max-width: 840px; margin: 0 auto; background: #fff; } @page { size: A4 portrait; margin: 8mm; } @media print { body { padding: 0; } }</style></head><body>' +
              htmlContent +
              '<script>window.onload=function(){setTimeout(function(){window.print();},250);};<\/script>' +
              '</body></html>'
            );
            pop.document.close();
          }
        }
      }, 350);
    }
    window.downloadFinancePdfDocument = downloadFinancePdfDocument;

    function exportFinancesCSV() {
      openFinancePdfModal();
    }
    window.exportFinancesCSV = exportFinancesCSV;

    // =========================================================================
    // UPLOAD FOTO KAMAR OLEH ADMIN (4 FOTO — TEAL THEME & EMPTY BY DEFAULT)
    // =========================================================================
    function setRoomPhotoSlot(slideIndex, url) {
      var textInput = document.getElementById('roomImageInput' + slideIndex);
      var previewImg = document.getElementById('roomImgPreview' + slideIndex);
      var placeholder = document.getElementById('roomPhotoPlaceholder' + slideIndex);
      var filled = document.getElementById('roomPhotoFilled' + slideIndex);
      var slot = document.getElementById('roomPhotoSlot' + slideIndex);

      if (textInput) textInput.value = url || '';

      if (url && url.trim() !== '') {
        var fullSrc = url.startsWith('http') || url.startsWith('/') || url.startsWith('data:') ? url : '/' + url;
        if (previewImg) previewImg.src = fullSrc;
        if (placeholder) placeholder.style.display = 'none';
        if (filled) filled.style.display = 'block';
        if (slot) {
          slot.style.borderColor = '#0d9488';
          slot.style.borderStyle = 'solid';
          slot.style.background = '#f0fdfa';
        }
      } else {
        if (previewImg) previewImg.src = '';
        if (placeholder) placeholder.style.display = 'flex';
        if (filled) filled.style.display = 'none';
        if (slot) {
          slot.style.borderColor = '#99f6e4';
          slot.style.borderStyle = 'dashed';
          slot.style.background = '#f0fdfa';
        }
      }
    }
    window.setRoomPhotoSlot = setRoomPhotoSlot;

    function removeRoomPhoto(slideIndex) {
      setRoomPhotoSlot(slideIndex, '');
      var fileInput = document.getElementById('roomPhotoFile' + slideIndex);
      if (fileInput) fileInput.value = '';
      var statusEl = document.getElementById('roomImgUploadStatus' + slideIndex);
      if (statusEl) statusEl.textContent = '';
    }
    window.removeRoomPhoto = removeRoomPhoto;

    function uploadRoomSlideImage(fileInput, slideIndex) {
      if (!fileInput.files || !fileInput.files[0]) return;
      _doRoomPhotoUpload(fileInput.files[0], slideIndex);
    }

    function handleRoomPhotoDrop(event, slideIndex) {
      event.preventDefault();
      var slot = event.currentTarget;
      slot.style.borderColor = '#99f6e4';
      slot.style.background = '#f0fdfa';
      var file = event.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        _doRoomPhotoUpload(file, slideIndex);
      }
    }

    function _doRoomPhotoUpload(file, slideIndex) {
      var statusEl = document.getElementById('roomImgUploadStatus' + slideIndex);
      var textInput = document.getElementById('roomImageInput' + slideIndex);
      var previewImg = document.getElementById('roomImgPreview' + slideIndex);

      if (statusEl) {
        statusEl.textContent = 'Uploading...';
        statusEl.style.color = '#fff';
        statusEl.style.background = 'rgba(10,86,97,0.85)';
      }

      // Local preview instantly
      var reader = new FileReader();
      reader.onload = function(e) {
        setRoomPhotoSlot(slideIndex, e.target.result);
      };
      reader.readAsDataURL(file);

      var formData = new FormData();
      formData.append('photo', file);

      fetch('/api/rooms/upload-photo', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: formData
      })
      .then(function(res) {
        if (!res.ok) throw new Error('Upload gagal');
        return res.json();
      })
      .then(function(data) {
        if (data.success) {
          var url = data.full_url || data.url || data.path;
          if (textInput) textInput.value = data.path || url;
          if (previewImg) previewImg.src = url;
          if (statusEl) {
            statusEl.textContent = 'Tersimpan';
            statusEl.style.background = 'rgba(13,148,136,0.9)';
            setTimeout(function() { statusEl.textContent = ''; }, 3000);
          }
        } else {
          throw new Error(data.message || 'Gagal');
        }
      })
      .catch(function(err) {
        if (statusEl) {
          statusEl.textContent = 'Gagal';
          statusEl.style.background = 'rgba(239,68,68,0.85)';
        }
        console.error('Upload foto ' + slideIndex + ':', err.message);
      });
    }

    function fillDefaultRoomImages4() {
      var defaults = [
        'assets/images/rooms/room_1.jpg',
        'assets/images/rooms/room_2.jpg',
        'assets/images/rooms/room_3.jpg',
        ''
      ];
      defaults.forEach(function(path, i) {
        setRoomPhotoSlot(i + 1, path);
      });
    }

    // =========================================================================
    // POINT 4: PENGELOLAAN METODE & REKENING PEMBAYARAN DINAMIS
    // =========================================================================
    function togglePmActive(checkbox) {
      var card = checkbox.closest('.pm-card-item');
      var label = card ? card.querySelector('.pm-toggle-label') : null;
      if (checkbox.checked) {
        if (card) card.style.borderColor = '#10b981';
        if (label) {
          label.textContent = '● Ditayangkan';
          label.parentElement.style.background = '#dcfce7';
          label.parentElement.style.color = '#15803d';
        }
      } else {
        if (card) card.style.borderColor = 'var(--border-color)';
        if (label) {
          label.textContent = '○ Disembunyikan';
          label.parentElement.style.background = '#f1f5f9';
          label.parentElement.style.color = '#64748b';
        }
      }
    }

    function toggleAddPmForm() {
      var box = document.getElementById('addPmFormBox');
      if (!box) return;
      if (box.style.display === 'none' || !box.style.display) {
        box.style.display = 'block';
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } else {
        box.style.display = 'none';
      }
    }

    function handleNewPmTypeChange(type) {
      var nameInput = document.getElementById('newPmName');
      var bankInput = document.getElementById('newPmBank');
      var numInput = document.getElementById('newPmNumber');
      if (type === 'qris') {
        if (nameInput) nameInput.placeholder = 'Contoh: QRIS Toko Mas Robi / ShopeePay QR';
        if (bankInput) bankInput.placeholder = 'Contoh: QRIS All Payment (NMID)';
        if (numInput) numInput.placeholder = 'Contoh: ID1029384756 (NMID) atau Link Barcode';
      } else if (type === 'ewallet') {
        if (nameInput) nameInput.placeholder = 'Contoh: GoPay / OVO / Dana / ShopeePay';
        if (bankInput) bankInput.placeholder = 'Contoh: Dana Bisnis / GoPay';
        if (numInput) numInput.placeholder = 'Contoh: 081234567890';
      } else if (type === 'cash') {
        if (nameInput) nameInput.placeholder = 'Contoh: Tunai / Cash ke Pengelola';
        if (bankInput) bankInput.placeholder = 'Contoh: Tunai Langsung';
        if (numInput) numInput.placeholder = 'Contoh: Bayar langsung di Kantor Mas Robi';
      } else {
        if (nameInput) nameInput.placeholder = 'Contoh: Transfer Bank Jago / BCA / Mandiri';
        if (bankInput) bankInput.placeholder = 'Contoh: Bank Jago / BCA / Mandiri';
        if (numInput) numInput.placeholder = 'Contoh: 1029384756';
      }
    }

    function submitAddNewPaymentMethod() {
      var type = document.getElementById('newPmType')?.value || 'bank';
      var name = document.getElementById('newPmName')?.value.trim();
      var bank = document.getElementById('newPmBank')?.value.trim() || name;
      var number = document.getElementById('newPmNumber')?.value.trim() || '-';
      var holder = document.getElementById('newPmHolder')?.value.trim() || 'Robi Suryanto';
      var instructions = document.getElementById('newPmInstructions')?.value.trim() || '';
      var isActive = document.getElementById('newPmIsActive')?.checked ?? true;

      if (!name) {
        alert('Mohon isi Nama Metode Tampilan terlebih dahulu!');
        return;
      }

      var icon = '';
      if (type === 'qris') icon = '';
      else if (type === 'cash') icon = '';
      else if (type === 'ewallet') icon = '';

      var newId = 'pm_' + Date.now();
      var card = document.createElement('div');
      card.className = 'pm-card-item';
      card.setAttribute('data-id', newId);
      card.style.cssText = 'background: var(--bg-surface); border: 1.5px solid ' + (isActive ? '#10b981' : 'var(--border-color)') + '; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; transition: all 0.2s;';

      var rawObj = {
        id: newId,
        type: type,
        name: name,
        bank_name: bank,
        account_number: number,
        account_holder: holder,
        instructions: instructions,
        is_active: isActive
      };

      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.3rem;">${icon}</span>
            <div>
              <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary);" class="pm-name-text">${name}</div>
              <div style="font-size: 0.76rem; color: var(--text-secondary);" class="pm-bank-text">${bank}</div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer; background: ${isActive ? '#dcfce7' : '#f1f5f9'}; color: ${isActive ? '#15803d' : '#64748b'}; padding: 4px 10px; border-radius: 999px;">
              <input type="checkbox" class="pm-active-toggle" ${isActive ? 'checked' : ''} onchange="togglePmActive(this)">
              <span class="pm-toggle-label">${isActive ? '● Ditayangkan' : '○ Disembunyikan'}</span>
            </label>
            <button type="button" class="btn-secondary" onclick="deletePmItem(this)" style="padding: 4px 8px; border-radius: 8px; color: #ef4444; font-size: 0.75rem;" title="Hapus Metode">
              
            </button>
          </div>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.8rem;">
          <div>
            <span style="color: var(--text-secondary); font-size: 0.7rem; display: block;">No. Rekening / NMID:</span>
            <strong class="pm-number-text" style="color: var(--text-primary); font-family: monospace; font-size: 0.85rem;">${number}</strong>
          </div>
          <div>
            <span style="color: var(--text-secondary); font-size: 0.7rem; display: block;">Atas Nama:</span>
            <strong class="pm-holder-text" style="color: var(--text-primary);">${holder}</strong>
          </div>
        </div>
        ${instructions ? `<div style="font-size: 0.74rem; color: var(--text-secondary); font-style: italic;" class="pm-inst-text">${instructions}</div>` : ''}
        <input type="hidden" class="pm-raw-data" value='${JSON.stringify(rawObj)}'>
      `;

      var list = document.getElementById('paymentMethodsList');
      if (list) list.appendChild(card);

      document.getElementById('newPmName').value = '';
      document.getElementById('newPmBank').value = '';
      document.getElementById('newPmNumber').value = '';
      document.getElementById('newPmInstructions').value = '';
      toggleAddPmForm();

      saveAllPaymentMethodsToServer();
    }

    function deletePmItem(btn) {
      if (!confirm('Hapus metode pembayaran ini?')) return;
      var card = btn.closest('.pm-card-item');
      if (card) {
        card.remove();
        saveAllPaymentMethodsToServer();
      }
    }

    function saveAllPaymentMethodsToServer() {
      var cards = document.querySelectorAll('#paymentMethodsList .pm-card-item');
      var methods = [];
      var statusEl = document.getElementById('pmSaveStatus');

      cards.forEach(function(card, index) {
        var rawInput = card.querySelector('.pm-raw-data');
        var obj = {};
        if (rawInput && rawInput.value) {
          try { obj = JSON.parse(rawInput.value); } catch(e) {}
        }
        var activeToggle = card.querySelector('.pm-active-toggle');
        obj.is_active = activeToggle ? activeToggle.checked : false;
        if (!obj.id) obj.id = card.getAttribute('data-id') || ('pm_' + index);
        methods.push(obj);
      });

      if (statusEl) {
        statusEl.textContent = 'Menyimpan...';
        statusEl.style.color = '#0d9488';
      }

      fetch('/api/settings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          payment_methods: methods
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (statusEl) {
          statusEl.textContent = 'Tersimpan di database!';
          statusEl.style.color = '#10b981';
          setTimeout(function() { statusEl.textContent = ''; }, 3000);
        }
      })
      .catch(function(err) {
        if (statusEl) {
          statusEl.textContent = 'Gagal menyimpan';
          statusEl.style.color = '#ef4444';
        }
      });
    }

    // =========================================================================
    // POINT 5: PENGELOLAAN JARINGAN WIFI KOS & MODEM (MULTI-WIFI)
    // =========================================================================
    function toggleWfActive(checkbox) {
      var card = checkbox.closest('.wf-card-item');
      var label = card ? card.querySelector('.wf-toggle-label') : null;
      if (checkbox.checked) {
        if (card) card.style.borderColor = '#10b981';
        if (label) {
          label.textContent = '● Ditayangkan';
          label.parentElement.style.background = '#dcfce7';
          label.parentElement.style.color = '#15803d';
        }
      } else {
        if (card) card.style.borderColor = 'var(--border-color)';
        if (label) {
          label.textContent = '○ Disembunyikan';
          label.parentElement.style.background = '#f1f5f9';
          label.parentElement.style.color = '#64748b';
        }
      }
    }

    function toggleAddWifiForm() {
      var box = document.getElementById('addWifiFormBox');
      if (!box) return;
      if (box.style.display === 'none' || !box.style.display) {
        box.style.display = 'block';
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } else {
        box.style.display = 'none';
      }
    }

    function filterWifiByLocation(loc, btn) {
      document.querySelectorAll('#wifiLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      var cards = document.querySelectorAll('#wifiNetworksList .wf-card-item');
      cards.forEach(function(card) {
        var cLoc = card.getAttribute('data-location') || 'Lokasi 1';
        if (loc === 'all' || cLoc === loc) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }
    window.filterWifiByLocation = filterWifiByLocation;

    function submitAddNewWifi() {
      var location = document.getElementById('newWfLocation')?.value || 'Lokasi 1';
      var name = document.getElementById('newWfName')?.value.trim();
      var modem = document.getElementById('newWfModem')?.value.trim();
      var password = document.getElementById('newWfPassword')?.value.trim() || '';
      var speed = document.getElementById('newWfSpeed')?.value.trim() || (location === 'Lokasi 2' ? '300 Mbps Mesh WiFi 6' : '100 Mbps Dual-Band');
      var isActive = document.getElementById('newWfIsActive')?.checked ?? true;

      if (!name) {
        alert('Mohon isi Nama WiFi (SSID) terlebih dahulu!');
        document.getElementById('newWfName')?.focus();
        return;
      }
      if (!modem) {
        alert('Mohon isi Modem / Lokasi Modem terlebih dahulu!');
        document.getElementById('newWfModem')?.focus();
        return;
      }

      var isWfLoc2 = (location === 'Lokasi 2');
      var newId = 'wifi_' + Date.now();
      var rawObj = {
        id: newId,
        location: location,
        name: name,
        modem: modem,
        password: password,
        speed: speed,
        is_active: isActive
      };

      var card = document.createElement('div');
      card.className = 'wf-card-item';
      card.setAttribute('data-id', newId);
      card.setAttribute('data-location', location);
      card.style.cssText = 'background: var(--bg-surface); border: 1.5px solid ' + (isActive ? '#10b981' : 'var(--border-color)') + '; border-radius: 14px; padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; transition: all 0.2s;';

      var currentB1Name = document.getElementById('b1_name')?.value?.trim() || @json($branch1Name);
      var currentB2Name = document.getElementById('b2_name')?.value?.trim() || @json($branch2Name);
      var branchDisplayName = isWfLoc2 ? currentB2Name : currentB1Name;
      var branchKeyAttr = isWfLoc2 ? 'lokasi_2' : 'lokasi_1';

      var branchBadgeHtml = isWfLoc2
        ? '<span class="wf-branch-badge" style="font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;"><span data-branch-name="lokasi_2">' + branchDisplayName + '</span></span>'
        : '<span class="wf-branch-badge" style="font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;"><span data-branch-name="lokasi_1">' + branchDisplayName + '</span></span>';

      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: ${isWfLoc2 ? '#ccfbf1' : '#dcfce7'}; border: 1px solid ${isWfLoc2 ? '#5eead4' : '#86efac'}; color: ${isWfLoc2 ? '#0a5661' : '#059669'}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-weight: 800; font-size: 0.98rem; color: var(--text-primary);" class="wf-name-text">${name}</span>
                ${branchBadgeHtml}
                <span style="font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: #f1f5f9; color: #475569;">${speed}</span>
              </div>
              <div style="font-size: 0.78rem; color: #0a5661; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                <span>Modem:</span>
                <span class="wf-modem-text" style="color: var(--text-secondary); font-weight: 500;">${modem}</span>
              </div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer; background: ${isActive ? '#dcfce7' : '#f1f5f9'}; color: ${isActive ? '#15803d' : '#64748b'}; padding: 4px 10px; border-radius: 999px;">
              <input type="checkbox" class="wf-active-toggle" ${isActive ? 'checked' : ''} onchange="toggleWfActive(this)">
              <span class="wf-toggle-label">${isActive ? '● Ditayangkan' : '○ Disembunyikan'}</span>
            </label>
            <button type="button" class="btn-secondary" onclick="deleteWfItem(this)" style="padding: 4px 8px; border-radius: 8px; color: #ef4444; font-size: 0.75rem;" title="Hapus WiFi">
              
            </button>
          </div>
        </div>
        ${password ? `
          <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 12px; font-size: 0.78rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <div>
              <span style="color: var(--text-muted); font-size: 0.72rem;">Sandi (Password):</span>
              <code style="font-family: monospace; font-weight: 700; color: var(--text-primary); margin-left: 6px;" class="wf-pass-text">${password}</code>
            </div>
            <span style="font-size: 0.7rem; color: var(--text-secondary);">Cabang: <strong><span data-branch-name="${branchKeyAttr}">${branchDisplayName}</span></strong></span>
          </div>
        ` : ''}
        <input type="hidden" class="wf-raw-data" value='${JSON.stringify(rawObj)}'>
      `;

      var list = document.getElementById('wifiNetworksList');
      if (list) list.appendChild(card);

      document.getElementById('newWfName').value = '';
      document.getElementById('newWfModem').value = '';
      document.getElementById('newWfPassword').value = '';
      if (document.getElementById('newWfSpeed')) document.getElementById('newWfSpeed').value = '';
      toggleAddWifiForm();

      saveAllWifiNetworksToServer();
    }

    function deleteWfItem(btn) {
      if (!confirm('Hapus jaringan WiFi ini?')) return;
      var card = btn.closest('.wf-card-item');
      if (card) {
        card.remove();
        saveAllWifiNetworksToServer();
      }
    }

    function saveAllWifiNetworksToServer() {
      var cards = document.querySelectorAll('#wifiNetworksList .wf-card-item');
      var wifis = [];
      var statusEl = document.getElementById('wfSaveStatus');

      cards.forEach(function(card, index) {
        var rawInput = card.querySelector('.wf-raw-data');
        var obj = {};
        if (rawInput && rawInput.value) {
          try { obj = JSON.parse(rawInput.value); } catch(e) {}
        }
        var activeToggle = card.querySelector('.wf-active-toggle');
        obj.is_active = activeToggle ? activeToggle.checked : false;
        if (!obj.id) obj.id = card.getAttribute('data-id') || ('wifi_' + index);
        wifis.push(obj);
      });

      if (statusEl) {
        statusEl.textContent = 'Menyimpan...';
        statusEl.style.color = '#0a5661';
      }

      fetch('/api/settings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          wifi_list: wifis
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (statusEl) {
          statusEl.textContent = 'Pengaturan WiFi berhasil disimpan!';
          statusEl.style.color = '#10b981';
          setTimeout(function() { statusEl.textContent = ''; }, 3000);
        }
      })
      .catch(function(err) {
        if (statusEl) {
          statusEl.textContent = 'Gagal menyimpan WiFi';
          statusEl.style.color = '#ef4444';
        }
      });
    }

    // =========================================================================
    // POINT 6: PENGELOLAAN TATA TERTIB & PERATURAN KOS (DINAMIS)
    // =========================================================================
    function renumberKosRules() {
      var items = document.querySelectorAll('#kosRulesEditorList .kos-rule-edit-item');
      items.forEach(function(item, idx) {
        var numEl = item.querySelector('.kos-rule-num');
        if (numEl) numEl.textContent = idx + 1;
      });
    }

    function addKosRuleRow() {
      var list = document.getElementById('kosRulesEditorList');
      if (!list) return;
      var count = list.querySelectorAll('.kos-rule-edit-item').length + 1;
      var div = document.createElement('div');
      div.className = 'kos-rule-edit-item';
      div.style.cssText = 'display: flex; align-items: center; gap: 10px; background: var(--bg-surface); border: 1px solid var(--border-color); padding: 10px 12px; border-radius: 12px;';
      div.innerHTML = `
        <span class="kos-rule-num" style="width: 26px; height: 26px; border-radius: 50%; background: #ccfbf1; color: #0a5661; font-size: 0.78rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">${count}</span>
        <input type="text" class="form-control kos-rule-input" value="" placeholder="Tulis poin peraturan kos baru..." style="flex: 1; border-radius: 10px; font-size: 0.85rem;" required>
        <button type="button" class="btn-secondary" onclick="removeKosRuleRow(this)" style="padding: 6px 10px; border-radius: 8px; color: #ef4444; font-size: 0.78rem; flex-shrink: 0;" title="Hapus Poin"></button>
      `;
      list.appendChild(div);
      var inp = div.querySelector('.kos-rule-input');
      if (inp) inp.focus();
    }

    function removeKosRuleRow(btn) {
      var list = document.getElementById('kosRulesEditorList');
      var items = list ? list.querySelectorAll('.kos-rule-edit-item') : [];
      if (items.length <= 1) {
        alert('Minimal harus terdapat 1 poin peraturan kos.');
        return;
      }
      var row = btn.closest('.kos-rule-edit-item');
      if (row) {
        row.remove();
        renumberKosRules();
      }
    }

    function saveKosRulesToServer(e) {
      if (e) e.preventDefault();
      var inputs = document.querySelectorAll('#kosRulesEditorList .kos-rule-input');
      var rules = [];
      inputs.forEach(function(inp) {
        var val = (inp.value || '').trim();
        if (val !== '') rules.push(val);
      });

      var statusEl = document.getElementById('kosRulesSaveStatus');
      if (statusEl) {
        statusEl.textContent = 'Menyimpan...';
        statusEl.style.color = '#0a5661';
      }

      fetch('/api/settings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          kos_rules: rules
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (statusEl) {
          statusEl.textContent = 'Peraturan kos berhasil disimpan!';
          statusEl.style.color = '#10b981';
          setTimeout(function() { statusEl.textContent = ''; }, 3000);
        }
      })
      .catch(function(err) {
        if (statusEl) {
          statusEl.textContent = 'Gagal menyimpan peraturan';
          statusEl.style.color = '#ef4444';
        }
      });
    }

    // =========================================================================
    // FITUR PENGATURAN TEMPLATE FORMULIR CALON PENGHUNI
    // =========================================================================
    window.defaultSurveyTemplateText = {!! json_encode(\App\Models\Setting::DEFAULT_SURVEY_TEMPLATE) !!};

    window.resetSurveyTemplateToDefault = function() {
      if (confirm('Kembalikan format formulir ke format standar awal?')) {
        var el = document.getElementById('admin_survey_form_template');
        if (el) el.value = window.defaultSurveyTemplateText;
      }
    };

    window.copyAdminSurveyTemplate = function() {
      var el = document.getElementById('admin_survey_form_template');
      if (!el) return;
      navigator.clipboard.writeText(el.value).then(function() {
        var statusEl = document.getElementById('surveyTemplateSaveStatus');
        if (statusEl) {
          statusEl.textContent = 'Format tersalin ke papan klip!';
          statusEl.style.color = '#10b981';
          setTimeout(function() { statusEl.textContent = ''; }, 2500);
        }
      });
    };

    window.saveSurveyTemplateToServer = function(e) {
      if (e) e.preventDefault();
      var el = document.getElementById('admin_survey_form_template');
      var statusEl = document.getElementById('surveyTemplateSaveStatus');
      if (!el) return;

      var textVal = el.value.trim();
      if (!textVal) {
        alert('Format formulir tidak boleh kosong');
        return;
      }

      if (statusEl) {
        statusEl.textContent = 'Menyimpan format formulir...';
        statusEl.style.color = '#0a5661';
      }

      fetch('/api/settings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          survey_form_template: textVal
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (statusEl) {
          statusEl.textContent = 'Format formulir calon penghuni berhasil disimpan!';
          statusEl.style.color = '#10b981';
          setTimeout(function() { statusEl.textContent = ''; }, 3500);
        }
      })
      .catch(function(err) {
        if (statusEl) {
          statusEl.textContent = 'Gagal menyimpan format formulir';
          statusEl.style.color = '#ef4444';
        }
      });
    };

    window.generateTenantRandomPassword = function() {
      var pwdInput = document.getElementById('tenantPassword');
      if (pwdInput) {
        pwdInput.value = 'kost' + Math.floor(1000 + Math.random() * 9000);
      }
    };

    // =========================================================================
    // FITUR MANAJEMEN AKUN ADMIN OPERASIONAL (KHUSUS ROLE OWNER)
    // =========================================================================
    window.openAddAdminModal = function() {
      document.getElementById('formManageAdmin').reset();
      document.getElementById('manageAdminId').value = '';
      document.getElementById('modalManageAdminTitle').textContent = 'Tambah Akun Admin Baru';
      document.getElementById('adminPasswordInput').required = true;
      document.getElementById('adminPasswordHelp').style.display = 'none';
      document.getElementById('btnSubmitManageAdmin').textContent = 'Simpan Akun Admin';
      window.openModal('modalManageAdmin');
    };

    window.openEditAdminModal = function(adminData) {
      if (!adminData) return;
      document.getElementById('manageAdminId').value = adminData.id;
      document.getElementById('adminNameInput').value = adminData.name || '';
      document.getElementById('adminEmailInput').value = adminData.email || '';
      document.getElementById('adminPhoneInput').value = adminData.phone || '';
      document.getElementById('adminPasswordInput').value = '';
      document.getElementById('adminPasswordInput').required = false;
      document.getElementById('adminPasswordHelp').style.display = 'block';
      document.getElementById('modalManageAdminTitle').textContent = 'Edit Akun Staf Admin';
      document.getElementById('btnSubmitManageAdmin').textContent = 'Perbarui Akun Admin';
      window.openModal('modalManageAdmin');
    };

    window.toggleInputPasswordVisibility = function(inputId, iconId) {
      var input = document.getElementById(inputId);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
    };

    window.filterAdminTable = function() {
      var query = (document.getElementById('searchAdminInput')?.value || '').toLowerCase().trim();
      var rows = document.querySelectorAll('#tbodyAdminRows .admin-data-row');
      rows.forEach(function(row) {
        var name = (row.getAttribute('data-name') || '').toLowerCase();
        var email = (row.getAttribute('data-email') || '').toLowerCase();
        var phone = (row.getAttribute('data-phone') || '').toLowerCase();
        if (!query || name.includes(query) || email.includes(query) || phone.includes(query)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    };

    window.submitAdminForm = function(e) {
      if (e) e.preventDefault();
      var btn = document.getElementById('btnSubmitManageAdmin');
      var adminId = document.getElementById('manageAdminId').value;
      var name = document.getElementById('adminNameInput').value.trim();
      var email = document.getElementById('adminEmailInput').value.trim();
      var phone = document.getElementById('adminPhoneInput').value.trim();
      var password = document.getElementById('adminPasswordInput').value;

      if (!name || !email) {
        alert('Nama lengkap dan email admin wajib diisi.');
        return;
      }

      if (!adminId && (!password || password.length < 6)) {
        alert('Kata sandi untuk admin baru minimal 6 karakter.');
        return;
      }

      if (password && password.length < 6) {
        alert('Kata sandi minimal 6 karakter.');
        return;
      }

      var prevBtnText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Menyimpan...';

      fetch('/api/admins', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          id: adminId || null,
          name: name,
          email: email,
          phone: phone,
          password: password || null
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        btn.disabled = false;
        btn.textContent = prevBtnText;
        if (data.success) {
          window.closeModal('modalManageAdmin');
          if (window.showNotification) {
            window.showNotification('success', data.message || 'Akun admin berhasil disimpan.');
          } else {
            alert(data.message || 'Akun admin berhasil disimpan.');
          }
          setTimeout(function() {
            window.location.reload();
          }, 800);
        } else {
          alert(data.message || 'Gagal menyimpan akun admin');
        }
      })
      .catch(function(err) {
        btn.disabled = false;
        btn.textContent = prevBtnText;
        alert('Terjadi kesalahan jaringan saat menyimpan data admin.');
      });
    };

    window.confirmDeleteAdmin = function(adminId, adminName) {
      if (!confirm('Apakah Anda yakin ingin menghapus hak akses dan akun staf Admin "' + adminName + '"? Staf tidak akan dapat login lagi.')) {
        return;
      }

      fetch('/api/admins/' + adminId + '/delete', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          if (window.showNotification) {
            window.showNotification('success', data.message || 'Akun admin berhasil dihapus.');
          } else {
            alert(data.message || 'Akun admin berhasil dihapus.');
          }
          var row = document.getElementById('adminRow_' + adminId);
          if (row) row.remove();
          var badge = document.getElementById('badgeTotalAdmins');
          if (badge) {
            var curr = parseInt(badge.textContent) || 0;
            if (curr > 0) badge.textContent = curr - 1;
          }
          var statCard = document.getElementById('statCardTotalAdmins');
          if (statCard) {
            var curr2 = parseInt(statCard.textContent) || 0;
            if (curr2 > 0) statCard.textContent = (curr2 - 1) + ' Akun Aktif';
          }
        } else {
          alert(data.message || 'Gagal menghapus akun admin.');
        }
      })
      .catch(function(err) {
        alert('Terjadi kesalahan saat menghapus data admin.');
      });
    };

  </script>

@endsection
