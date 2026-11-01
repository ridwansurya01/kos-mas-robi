@extends('layouts.app')

@section('title', ($setting->kos_name ?? 'Kos Mas Robi') . ' - Smart Kost Management')

@section('content')

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
        <h2 class="brand-title">{{ $setting->owner_name ? 'Kos ' . $setting->owner_name : 'Kos Mas Robi' }}</h2>
        <div class="brand-subtitle-wrap">
          <span class="brand-status-dot"></span>
          <span class="brand-badge modern-badge">Smart Kost Pro</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav" aria-label="Main Navigation">
      <div class="nav-section-title">Ringkasan</div>

      <div class="nav-item active" data-tab="tab-dashboard" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
        <span>Dashboard</span>
      </div>

      <div class="nav-section-title" style="margin-top: 14px;">Kamar & Pelanggan</div>

      <div class="nav-item" data-tab="tab-rooms" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
        <span>Daftar Kamar</span>
        <span class="nav-badge-count" id="badgeRoomCount">{{ $totalRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-tenants" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Data Penghuni</span>
        <span class="nav-badge-count" id="badgeTenantCount">{{ $occupiedRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-bills" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        <span>Tagihan Sewa</span>
        <span class="nav-badge-count" id="badgeDueBills" style="{{ $dueRooms > 0 ? 'background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-weight:800;' : 'display:none;' }}">{{ $dueRooms }}</span>
      </div>

      <div class="nav-item" data-tab="tab-transfers" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
        <span>Pindah Kamar</span>
        <span class="nav-badge-count" id="badgePendingTransfers" style="{{ ($pendingTransfersCount ?? 0) > 0 ? 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;' : 'display:none;' }}">{{ $pendingTransfersCount ?? 0 }}</span>
      </div>

      <div class="nav-item" data-tab="tab-maintenance" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        <span>Keluhan & Servis</span>
        <span class="nav-badge-count" id="badgePendingRepairs">{{ $activeTickets }}</span>
      </div>

      <div class="nav-section-title" style="margin-top: 14px;">Manajemen Admin</div>

      <div class="nav-item" data-tab="tab-finances" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <span>Laporan Keuangan</span>
      </div>

      <div class="nav-item" data-tab="tab-settings" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Pengaturan Kos</span>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="host-profile modern-host-card">
        <div class="host-avatar-wrap">
          <div class="host-avatar">MR</div>
          <span class="host-online-pulse" title="Online Aktif"></span>
        </div>
        <div class="host-info">
          <div class="host-name-row">
            <span class="host-name" id="profileOwnerName">{{ $setting->owner_name ?? 'Mas Robi' }}</span>
            <span class="host-badge-admin">Owner</span>
          </div>
          <div class="host-role">Pengelola Properti Kos</div>
        </div>
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
        <button type="button" class="mobile-menu-toggle" id="btnToggleMobileSidebar" aria-label="Buka Menu Navigasi">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="page-title-box modern-title-box">
          <div class="header-breadcrumb">
            <span class="bc-home">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</span>
            <span class="bc-sep">/</span>
            <span class="bc-curr" id="breadcrumbCurrent">Panel Kelola</span>
          </div>
          <h1 id="pageTitle" class="header-main-title">Dashboard Ikhtisar</h1>
          <p id="pageSubtitle" class="header-sub-title">Monitoring real-time hunian, status kamar, dan operasional</p>
        </div>
      </div>

      <div class="header-right modern-header-right">
        <div class="header-search modern-search-box">
          <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="globalSearchInput" placeholder="Cari kamar, anak kos, tagihan..." aria-label="Cari data kos">
          <kbd class="search-kbd">Ctrl K</kbd>
        </div>

        <!-- Theme Switcher Button (Sliding Pill Switch di Header Pojok Kanan) -->
        <button type="button" class="kmr-theme-switch" id="btnToggleTheme" aria-label="Ganti Tema Terang / Gelap" title="Ganti Mode Gelap / Terang">
          <span class="kmr-switch-bg"></span>
          <span class="kmr-switch-label day-lbl">DAY MODE</span>
          <span class="kmr-switch-label night-lbl">NIGHT MODE</span>
          <span class="kmr-switch-knob">
            <span class="kmr-knob-sun">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24" stroke="#f59e0b" stroke-width="1.5">
                <circle cx="12" cy="12" r="5" fill="#f59e0b"/>
                <line x1="12" y1="2" x2="12" y2="4"/>
                <line x1="12" y1="20" x2="12" y2="22"/>
                <line x1="4.93" y1="4.93" x2="6.34" y2="6.34"/>
                <line x1="17.66" y1="17.66" x2="19.07" y2="19.07"/>
                <line x1="2" y1="12" x2="4" y2="12"/>
                <line x1="20" y1="12" x2="22" y2="12"/>
                <line x1="4.93" y1="19.07" x2="6.34" y2="17.66"/>
                <line x1="17.66" y1="6.34" x2="19.07" y2="4.93"/>
              </svg>
            </span>
            <span class="kmr-knob-moon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="#93c5fd" stroke="#60a5fa" stroke-width="1.5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
            </span>
          </span>
        </button>

        <!-- Logout Button (Memicu Pop-up Konfirmasi Modern) -->
        <form action="{{ route('logout') }}" method="POST" id="formLogoutAdmin" style="display:inline; margin:0;">
          @csrf
          <button type="button" class="header-action-btn modern-logout-btn" id="btnTriggerLogoutAdmin" title="Keluar / Logout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </button>
        </form>

        <!-- Quick Add Button -->
        <button type="button" class="btn-primary-action modern-quick-add-btn" id="btnQuickAdd">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>+ Penghuni Baru</span>
        </button>
      </div>
    </header>

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

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Pemasukan Bulan Ini</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="statMonthlyIncome">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">▲ Terverifikasi</span>
              <span>Buku Kas Aktif</span>
            </div>
          </div>

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
            <button type="button" class="filter-pill" data-filter="occupied">🟢 Terisi</button>
            <button type="button" class="filter-pill" data-filter="available">🔵 Kosong</button>
            <button type="button" class="filter-pill" data-filter="due">🟡 Jatuh Tempo</button>
            <button type="button" class="filter-pill" data-filter="repair">🔴 Perbaikan</button>
          </div>
        </div>

        <!-- Floor 1 -->
        <div class="floor-section">
          <div class="floor-badge">🏢 Lantai 1 (Lantai Bawah)</div>
          <div class="rooms-grid" id="floor1Grid">
            <!-- Dynamically populated by JS & Laravel API -->
          </div>
        </div>

        <!-- Floor 2 -->
        <div class="floor-section">
          <div class="floor-badge">🏢 Lantai 2 (Lantai Atas)</div>
          <div class="rooms-grid" id="floor2Grid">
            <!-- Dynamically populated by JS & Laravel API -->
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 2: TAB DAFTAR KAMAR
           ===================================================================== -->
      <div class="tab-pane" id="tab-rooms">
        <div class="section-header">
          <div class="section-title">
            <h2>Kelola Master Kamar</h2>
          </div>
          <button type="button" class="btn-primary-action" id="btnAddNewRoom">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>+ Tambah Kamar Baru</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="roomSearchInput" placeholder="Cari nomor kamar atau tipe...">
            </div>
            <div class="filter-pills" id="roomTabFilters">
              <button type="button" class="filter-pill active" data-floor="all">Semua Lantai</button>
              <button type="button" class="filter-pill" data-floor="1">Lantai 1</button>
              <button type="button" class="filter-pill" data-floor="2">Lantai 2</button>
            </div>
          </div>

          <!-- Desktop Table View -->
          <table class="custom-table" id="roomsMasterTable">
            <thead>
              <tr>
                <th>No Kamar</th>
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
           PANE 3: TAB DATA PENGHUNI
           ===================================================================== -->
      <div class="tab-pane" id="tab-tenants">
        <div class="section-header">
          <div class="section-title">
            <h2>Data & Kontak Penghuni Kos</h2>
          </div>
          <button type="button" class="btn-primary-action" id="btnAddNewTenant">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>+ Daftarkan Anak Kos</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="tenantSearchInput" placeholder="Cari nama atau nomor WhatsApp...">
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
           PANE 3: TAB TAGIHAN & INVOICE SEWA (TERPISAH DARI KEUANGAN)
           ===================================================================== -->
      <div class="tab-pane" id="tab-bills">
        <div class="section-header">
          <div class="section-title">
            <h2>Manajemen Tagihan & Invoice Sewa</h2>
            <p style="font-size: 0.84rem; color: var(--text-secondary); margin-top: 4px;">Pantau jatuh tempo sewa, kirim pengingat tagihan via WhatsApp, dan konfirmasi pelunasan.</p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-primary-action" onclick="exportBillingReport()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Export Rekap Tagihan</span>
            </button>
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
            <div class="stat-card-value" style="color:#10b981;">{{ $occupiedRooms - $dueRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">● Terverifikasi</span>
              <span>Anak kos tertib bayar</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Tagihan Jatuh Tempo / Belum Lunas</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              </div>
            </div>
            <div class="stat-card-value" style="color:#f59e0b;">{{ $dueRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span style="color:#d97706;font-weight:700;">Rp {{ number_format($dueAmount, 0, ',', '.') }}</span>
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
            <div class="stat-card-value">{{ $occupiedRooms > 0 ? round((($occupiedRooms - $dueRooms) / $occupiedRooms) * 100, 1) : 100 }}%</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info">Collection Rate</span>
              <span>Kepatuhan bayar sewa</span>
            </div>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="billingSearchInput" placeholder="Cari kamar, nama anak kos, status..." onkeyup="filterBillingTable()">
            </div>
            <div class="filter-pills" id="billingStatusFilter">
              <button type="button" class="filter-pill active" onclick="filterBillingStatus('all', this)">Semua Unit ({{ $totalRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('due', this)" style="color:#d97706;">🟡 Jatuh Tempo ({{ $dueRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('paid', this)" style="color:#059669;">🟢 Lunas ({{ $occupiedRooms - $dueRooms }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('available', this)">🔵 Kosong ({{ $availableRooms }})</button>
            </div>
          </div>

          <table class="custom-table" id="billingMasterTable">
            <thead>
              <tr>
                <th>Unit Kamar</th>
                <th>Nama Penghuni</th>
                <th>Nominal Sewa / Bln</th>
                <th>Jatuh Tempo</th>
                <th>Status Pembayaran</th>
                <th>Aksi Penagihan & Invoice</th>
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
              @endphp
              <tr data-status="{{ $statusType }}" data-search="{{ strtolower($r->number . ' ' . $r->type . ' ' . $tenantName . ' ' . ($isDue ? 'jatuh tempo belum lunas' : ($isOccupied ? 'lunas' : 'kosong'))) }}">
                <td>
                  <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary);">Kamar #{{ $r->number }}</div>
                  <div style="font-size: 0.74rem; color: var(--text-secondary);">Lt. {{ $r->floor }} • {{ $r->type }}</div>
                </td>
                <td>
                  @if($t)
                    <div style="font-weight: 700; color: var(--text-primary);">{{ $t->name }}</div>
                    <div style="font-size: 0.76rem; color: #2563eb;">{{ $t->phone }}</div>
                  @else
                    <span style="color: var(--text-muted); font-style: italic;">(Unit Kosong / Siap Huni)</span>
                  @endif
                </td>
                <td style="font-weight: 800; color: #2563eb;">
                  Rp {{ number_format($r->price, 0, ',', '.') }}
                </td>
                <td>
                  @if($t)
                    <div style="font-weight: 700; color: {{ $isDue ? '#dc2626' : 'var(--text-primary)' }};">
                      Tiap Tgl {{ $dueDay }}
                    </div>
                    <div style="font-size: 0.74rem; color: {{ $isDue ? '#ef4444' : 'var(--text-secondary)' }};">
                      {{ $isDue ? '⚠️ Jatuh tempo bulan ini' : '✓ Pembayaran terjadwal' }}
                    </div>
                  @else
                    <span style="color: var(--text-muted);">-</span>
                  @endif
                </td>
                <td>
                  @if($isDue)
                    <span class="status-badge due" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      ⚠️ Belum Lunas (Jatuh Tempo)
                    </span>
                  @elseif($isOccupied)
                    <span class="status-badge occupied" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      ✓ Lunas Bulan Ini
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
                        $waText = rawurlencode("Halo Kak {$t->name}, kami dari Pengelola Kos Mas Robi ingin menginformasikan terkait tagihan sewa Kamar #{$r->number} sebesar Rp " . number_format($r->price, 0, ',', '.') . ". Silakan melakukan pembayaran ke Rekening BCA: 8830192831 (a.n. Robi Suryanto) atau Mandiri: 1370019283100. Jika sudah transfer, mohon kirimkan bukti transfer. Terima kasih banyak Kak!");
                      @endphp
                      <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn-secondary" style="background:#059669;color:#ffffff;border:none;padding:5px 10px;font-size:0.76rem;font-weight:700;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" title="Kirim Pesan WhatsApp Penagihan">
                        💬 WA Tagihan
                      </a>
                      <button type="button" class="btn-secondary" style="padding:5px 10px;font-size:0.76rem;font-weight:700;border-radius:8px;" onclick="quickMarkPaid('{{ $r->number }}', '{{ $t->name }}', {{ (int)$r->price }})">
                        ✓ Konfirmasi Lunas
                      </button>
                    @else
                      <button type="button" class="btn-secondary" style="padding:5px 10px;font-size:0.76rem;" onclick="document.getElementById('btnQuickAdd')?.click()">
                        + Tambah Penghuni
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">Tidak ada data unit kamar.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- =====================================================================
           PANE 4: TAB LAPORAN KEUANGAN & KURVA ARUS KAS (KOMPLEKS & LENGKAP)
           ===================================================================== -->
      <div class="tab-pane" id="tab-finances">
        <div class="section-header">
          <div class="section-title">
            <h2>Laporan Keuangan & Analisis Arus Kas</h2>
            <p style="font-size: 0.84rem; color: var(--text-secondary); margin-top: 4px;">Analisis kurva pendapatan bulanan s/d 1 tahun penuh, rasio pengeluaran operasional, dan buku kas transaksi resmi.</p>
          </div>
          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div class="finance-year-picker-wrap">
              <span class="fyp-label">📅 Periode Tahun:</span>
              <select id="selectFinanceYear" class="fyp-select" onchange="changeFinanceYear(this.value)">
                @foreach(($availableYears ?? [2027, 2026, 2025, 2024]) as $yr)
                  <option value="{{ $yr }}" {{ ($selectedYear ?? 2026) == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                @endforeach
              </select>
            </div>
            <button type="button" class="btn-secondary" onclick="exportFinancesCSV()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>Export Buku Kas</span>
            </button>
            <button type="button" class="btn-primary-action" id="btnRecordTransaction">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>+ Catat Transaksi Baru</span>
            </button>
          </div>
        </div>

        <!-- 4 KPI Cards Keuangan Kompleks (Dinamis Berdasarkan Filter Tahun) -->
        <div class="stats-grid financial-kpi-grid" style="padding: 0; margin-bottom: 24px;">
          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Total Pemasukan (<span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span>)</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiIncomeVal" style="color:#10b981;">Rp {{ number_format($yearlyIncome ?? ($monthlyIncome * 12), 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">▲ 100% Terverifikasi</span>
              <span>Arus kas masuk setahun</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Biaya Operasional (<span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span>)</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiExpenseVal" style="color:#f87171;">Rp {{ number_format($yearlyExpense ?? (($totalExpense ?: 3500000) * 12), 0, ',', '.') }}</div>
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
            <div class="stat-card-value" id="finKpiNetVal" style="color:#3b82f6;">Rp {{ number_format($yearlyNet ?? (($netCashflow ?: 14700000) * 12), 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">Margin Bersih Sehat</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <span class="stat-card-title">Rata-rata Bersih / Bulan</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="finKpiAvgVal">Rp {{ number_format(($yearlyNet ?? 176400000) / 12, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info">Tahun <span class="fin-selected-year-badge">{{ $selectedYear ?? 2026 }}</span></span>
              <span>16 Unit Kamar</span>
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
                <span style="font-size: 1.15rem;">📈</span>
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
                  <span style="width: 12px; height: 12px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
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
              <div id="ttIncome" style="color: #34d399;">Pemasukan: Rp 18.200.000</div>
              <div id="ttExpense" style="color: #f87171;">Pengeluaran: Rp 3.500.000</div>
              <div id="ttNet" style="color: #60a5fa; border-top: 1px dashed rgba(255,255,255,0.2); margin-top: 4px; padding-top: 3px;">Net: Rp 14.700.000</div>
            </div>
          </div>

          <!-- Quick Insight Bar -->
          <div style="margin-top: 16px; padding: 12px 18px; border-radius: 14px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-primary);">
              <span>💡</span>
              <span><strong>Insight Keuangan:</strong> Rata-rata pemasukan per unit kamar mencapai <strong>Rp 1.512.000/bln</strong> dengan tingkat perputaran kas sehat.</span>
            </div>
            <div style="font-size: 0.78rem; font-weight: 700; color: #2563eb;">
              Status Kas: Surplus Finansial ✓
            </div>
          </div>
        </div>

        <!-- Tabel Buku Kas & Riwayat Transaksi -->
        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="financeSearchInput" placeholder="Cari nomor bukti, keterangan, kategori kas...">
            </div>
            <div class="filter-pills" id="financeTypeFilter">
              <button type="button" class="filter-pill active" data-ftype="all">Semua Transaksi</button>
              <button type="button" class="filter-pill" data-ftype="income">🟢 Pemasukan (Sewa)</button>
              <button type="button" class="filter-pill" data-ftype="expense">🔴 Pengeluaran Kos</button>
            </div>
          </div>

          <table class="custom-table" id="financesTable">
            <thead>
              <tr>
                <th>No. Bukti</th>
                <th>Tanggal</th>
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
           PANE 5: TAB KELUHAN & PERAWATAN
           ===================================================================== -->
      <div class="tab-pane" id="tab-maintenance">
        <div class="section-header">
          <div class="section-title">
            <h2>Laporan Keluhan & Servis Fasilitas</h2>
          </div>
          <button type="button" class="btn-primary-action" id="btnNewRepairTicket">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>+ Buat Tiket Komplain</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="maintenanceSearchInput" placeholder="Cari keluhan atau kamar...">
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
           PANE 5B: TAB PERSETUJUAN PINDAH KAMAR (FITUR ADMIN BARU)
           ===================================================================== -->
      <div class="tab-pane" id="tab-transfers">
        <div class="section-header">
          <div class="section-title">
            <h2>Pengajuan Pindah Kamar Penghuni</h2>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">Tinjau dan setujui permohonan perpindahan unit kamar anak kos</p>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="transferSearchInput" placeholder="Cari penghuni atau kamar..." onkeyup="filterTransferTable()">
            </div>
            <div class="filter-pills" id="transferStatusFilter">
              <button type="button" class="filter-pill active" onclick="filterTransferStatus('all', this)">Semua</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('pending', this)">Menunggu ({{ $pendingTransfersCount ?? 0 }})</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('approved', this)">Disetujui</button>
              <button type="button" class="filter-pill" onclick="filterTransferStatus('rejected', this)">Ditolak</button>
            </div>
          </div>

          <table class="custom-table" id="transfersTable">
            <thead>
              <tr>
                <th>Tgl Pengajuan</th>
                <th>Penghuni & Kontak</th>
                <th>Kamar Asal ➔ Tujuan</th>
                <th>Alasan & Rencana</th>
                <th>Status</th>
                <th>Aksi Pengelola</th>
              </tr>
            </thead>
            <tbody id="transfersTableBody">
              @forelse($roomTransfers ?? [] as $tr)
                <tr data-status="{{ $tr->status }}" data-search="{{ strtolower(($tr->user->name ?? $tr->tenant->name ?? '') . ' ' . ($tr->currentRoom->number ?? '') . ' ' . ($tr->targetRoom->number ?? '') . ' ' . $tr->reason) }}">
                  <td>
                    <div style="font-weight: 700;">{{ $tr->created_at->format('d/m/Y') }}</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $tr->created_at->format('H:i') }} WIB</div>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--brand-primary);">{{ $tr->user->name ?? $tr->tenant->name ?? 'Anak Kos' }}</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">📞 {{ $tr->user->phone ?? $tr->tenant->phone ?? '-' }}</div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span class="badge" style="background: var(--bg-surface); color: var(--text-primary); font-weight: 700; padding: 4px 8px; border-radius: 6px;">#{{ $tr->currentRoom->number ?? '-' }}</span>
                      <span style="color: var(--brand-primary); font-weight: 800;">➔</span>
                      <span class="badge" style="background: var(--brand-soft-bg); color: var(--brand-primary); font-weight: 800; padding: 4px 8px; border-radius: 6px;">#{{ $tr->targetRoom->number ?? '-' }}</span>
                    </div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 3px;">
                      {{ $tr->targetRoom->type ?? '' }} (Lt {{ $tr->targetRoom->floor ?? '1' }})
                    </div>
                  </td>
                  <td style="max-width: 240px;">
                    <div style="font-size: 0.82rem; line-height: 1.4;">{{ $tr->reason }}</div>
                    @if($tr->preferred_date)
                      <div style="font-size: 0.72rem; color: #2563eb; font-weight: 600; margin-top: 3px;">📅 Rencana: {{ \Carbon\Carbon::parse($tr->preferred_date)->format('d M Y') }}</div>
                    @endif
                    @if($tr->admin_note)
                      <div style="font-size: 0.72rem; color: #059669; font-weight: 600; margin-top: 3px;">💬 Catatan: {{ $tr->admin_note }}</div>
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
                          ✓ Izinkan
                        </button>
                        <button type="button" class="btn-sm" style="background: #ef4444; color: #fff; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem;" onclick="handleTransferAction({{ $tr->id }}, 'rejected')">
                          ✕ Tolak
                        </button>
                      </div>
                    @else
                      <span style="font-size: 0.75rem; color: var(--text-muted);">Selesai Diproses</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">
                    Belum ada pengajuan pindah kamar dari penghuni kos.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="tab-pane" id="tab-settings">
        <!-- Modern Header -->
        <div class="settings-modern-header">
          <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0;">Pusat Pengaturan & Kelola Properti</h2>
            <p style="font-size: 0.84rem; color: var(--text-secondary); margin-top: 4px; margin-bottom: 0;">Kelola konten Landing Page, foto galeri, titik peta Google Maps, dan profil rekening pembayaran.</p>
          </div>
          <a href="{{ route('home') }}" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 10px 18px; border-radius: 9999px;">
            <span style="font-size: 1rem;">🌐</span>
            <span>Buka Website Landing Page ↗</span>
          </a>
        </div>

        <!-- Segmented Sub-Tab Navigation -->
        <div style="margin-bottom: 26px;">
          <div class="settings-segmented-nav" id="settingsSubTabNav">
            <button type="button" class="settings-nav-btn btn-settings-subtab active" data-subtab="subtab-landing">
              <span>🎨</span>
              <span>Tampilan & Foto Landing Page</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-location">
              <span>📍</span>
              <span>Titik Lokasi Google Maps & POI</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-profile">
              <span>⚙️</span>
              <span>Profil Properti & Rekening</span>
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
                  <div class="settings-card-icon-badge coral">✨</div>
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
                    <span style="font-weight: 800; font-size: 0.88rem; color: var(--text-primary);">📸 Foto Utama Gedung / Eksterior</span>
                    <span style="font-size: 0.72rem; color: var(--text-secondary);">Format: JPG, PNG, WEBP</span>
                  </div>
                  <div class="settings-preview-img-wrap">
                    <img id="previewHeroImage" src="{{ asset($lc['hero_image'] ?? 'assets/images/kos_exterior.jpg') }}" alt="Preview Foto Hero">
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 8px;">
                    <input type="file" id="lp_hero_image_file" name="hero_image_file" accept="image/*" class="form-control" style="font-size: 0.82rem; padding: 8px;">
                    <div style="font-size: 0.76rem; color: var(--text-secondary); text-align: left; line-height: 1.4;">
                      💡 <em>Tips:</em> Bagian tengah foto akan otomatis disatukan secara mulus dengan gradasi warna latar belakang Landing Page.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. BENTO & FASILITAS SECTION CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge teal">🍱</div>
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
                  <span>🖼️ 4 Foto Galeri Kamar & Fasilitas Kost</span>
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
                  <div class="settings-card-icon-badge">💬</div>
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
                <span>💾 Simpan Seluruh Tampilan Landing Page</span>
              </button>
              <a href="{{ route('home') }}" target="_blank" class="btn-secondary" style="padding: 13px 22px; font-weight: 700; border-radius: 14px;">
                <span>Lihat Hasil Langsung ↗</span>
              </a>
            </div>

          </form>
        </div>

        <!-- =========================================================================
             SUBTAB 2: TITIK LOKASI GOOGLE MAPS & 4 POI
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-location" style="display: none;">
          <form id="settingsProfileForm" style="display: flex; flex-direction: column; gap: 24px;">

            <!-- MAPS CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge coral">📍</div>
                  <div>
                    <h3 class="settings-card-title">Titik Lokasi Google Maps & Embed Interaktif</h3>
                    <div class="settings-card-subtitle">Pengaturan alamat dan peta interaktif yang dapat diakses langsung oleh calon penghuni</div>
                  </div>
                </div>
                <a href="{{ $setting->gmaps_url ?: ('https://maps.google.com/?q=' . urlencode($setting->kos_address ?? 'Kos Mas Robi')) }}" id="btnTestGmapsUrl" target="_blank" rel="noopener noreferrer" class="btn-secondary" style="font-size: 0.78rem; font-weight: 800; padding: 6px 14px; border-radius: 9999px;">
                  <span>Uji Buka Peta ↗</span>
                </a>
              </div>

              <div class="form-group" style="margin-bottom: 18px;">
                <label for="setKosAddress" style="font-weight: 700;">Alamat Lengkap Properti Kos</label>
                <textarea id="setKosAddress" class="form-control" rows="2" placeholder="Contoh: Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta">{{ $setting->kos_address }}</textarea>
              </div>

              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                  <label for="setGmapsUrl" style="font-weight: 700;">Link Buka Google Maps (Tombol Navigasi)</label>
                  <input type="text" id="setGmapsUrl" class="form-control" placeholder="https://maps.app.goo.gl/... atau https://maps.google.com/?q=..." value="{{ $setting->gmaps_url }}">
                  <div style="font-size: 0.74rem; color: var(--text-secondary); margin-top: 4px;">Link ini dibuka saat calon penghuni klik tombol "Buka di Google Maps".</div>
                </div>

                <div class="form-group">
                  <label for="setGmapsEmbed" style="font-weight: 700;">Sematkan Peta (Iframe, Link Maps, atau Koordinat)</label>
                  <input type="text" id="setGmapsEmbed" class="form-control" placeholder="Contoh: https://maps.app.goo.gl/... atau kode <iframe>..." value="{{ $setting->gmaps_embed }}">
                  <div style="font-size: 0.74rem; color: #10b981; margin-top: 4px; font-weight: 700;">
                    ✅ <em>Anti-Gagal:</em> Otomatis dikonversi aman tanpa pesan tanda larang / forbidden.
                  </div>
                </div>
              </div>

              <!-- Live Preview Peta di Admin -->
              <div class="form-group">
                <label style="font-weight: 800; color: var(--text-primary); font-size: 0.85rem; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                  <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#10b981;"></span>
                  Pratinjau Langsung Peta Google Maps (Live Preview):
                </label>
                <div id="gmapsLivePreviewWrap" style="width: 100%; height: 280px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface); position: relative; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                  <iframe id="gmapsPreviewIframe" src="{{ $setting->final_embed_url }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Pratinjau Lokasi Kos Mas Robi"></iframe>
                </div>
              </div>
            </div>

            <!-- 4 POI CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge">📌</div>
                  <div>
                    <h3 class="settings-card-title">4 Poin Akses & Keunggulan Lokasi</h3>
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
                      'icon' => ['🎓', '☕', '🏥', '🛡️'][$i] ?? '📍',
                      'title' => '',
                      'desc' => ''
                    ];
                  @endphp
                  <div class="highlight-edit-item" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                      <div style="width: 58px; flex-shrink: 0;">
                        <label style="font-size: 0.7rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 2px;">Ikon</label>
                        <input type="text" class="form-control hl-icon" value="{{ $hl['icon'] ?? '📍' }}" style="text-align: center; font-size: 1.15rem; padding: 4px;" maxlength="4" placeholder="🎓">
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

              <button type="submit" class="btn-primary" style="align-self: flex-start; margin-top: 18px; padding: 11px 26px; font-weight: 800; border-radius: 12px;">Simpan Perubahan Lokasi & Peta</button>
            </div>

          </form>
        </div>

        <!-- =========================================================================
             SUBTAB 3: PROFIL KOS & REKENING PEMBAYARAN
             ========================================================================= -->
        <div class="settings-subtab-pane" id="subtab-profile" style="display: none;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">

            <!-- Profile Card -->
            <div class="settings-modern-card" style="margin-bottom: 0;">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge">⚙️</div>
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
                <button type="submit" class="btn-primary" style="align-self: flex-start; margin-top: 6px; padding: 10px 22px; border-radius: 12px; font-weight: 800;">Simpan Profil Kos</button>
              </form>
            </div>

            <!-- Payment Account Card -->
            <div class="settings-modern-card" style="margin-bottom: 0;">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge teal">💳</div>
                  <div>
                    <h3 class="settings-card-title">Rekening Pembayaran & Cadangan Data</h3>
                    <div class="settings-card-subtitle">Tujuan transfer sewa resmi serta utilitas backup JSON</div>
                  </div>
                </div>
              </div>
              <form id="settingsBankForm" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                <div class="form-group">
                  <label for="setBankName" style="font-weight: 700;">Nama Bank / QRIS</label>
                  <input type="text" id="setBankName" class="form-control" value="{{ $setting->bank_name }}">
                </div>
                <div class="form-group">
                  <label for="setBankAccount" style="font-weight: 700;">Nomor Rekening</label>
                  <input type="text" id="setBankAccount" class="form-control" value="{{ $setting->bank_account }}">
                </div>
                <div class="form-group">
                  <label for="setBankHolder" style="font-weight: 700;">Atas Nama Pemilik Rekening</label>
                  <input type="text" id="setBankHolder" class="form-control" value="{{ $setting->bank_holder }}">
                </div>
                <button type="submit" class="btn-primary" style="align-self: flex-start; padding: 10px 22px; border-radius: 12px; font-weight: 800;">Simpan Rekening</button>
              </form>

              <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">

              <div>
                <h4 style="margin-bottom: 6px; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Manajemen Data & Cadangan</h4>
                <p style="font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 14px;">Data tersimpan aman di database SQLite Laravel. Anda dapat mengekspor cadangan JSON sewaktu-waktu.</p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                  <button type="button" class="btn-secondary" id="btnExportData" style="border-radius: 10px; font-weight: 700;">📥 Unduh Cadangan JSON</button>
                  <button type="button" class="btn-secondary" id="btnResetData" style="color: #f87171; border-radius: 10px; font-weight: 700;">🔄 Reset Data Sampel</button>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>

    </div><!-- /content-body -->

  </main>

  <!-- =========================================================================
       MOBILE BOTTOM NAVIGATION (Automatic Smartphone View)
       ========================================================================= -->
  <nav class="mobile-bottom-nav" aria-label="Navigasi Bawah Mobile">
    <div class="bottom-nav-item active" data-tab="tab-dashboard">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
      <span>Dashboard</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-rooms">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
      <span>Kamar</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-tenants">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <span>Penghuni</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-finances">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      <span>Keuangan</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-settings">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      <span>Pengaturan</span>
    </div>
  </nav>

  <!-- =========================================================================
       MODAL: DETAIL KAMAR
       ========================================================================= -->
  <div class="modal-backdrop" id="modalRoomDetail">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3 id="mRoomDetailTitle">Detail Kamar #101</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalRoomDetail">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" id="mRoomDetailBody">
        <!-- Rendered by JS -->
      </div>
      <div class="modal-footer" id="mRoomDetailFooter">
        <button type="button" class="btn-secondary" data-close-modal="modalRoomDetail">Tutup</button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM TAMBAH PENGHUNI
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAddTenant">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3>Daftarkan Penghuni Baru</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalAddTenant">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formAddTenant">
        <div class="modal-body">
          <div class="form-group">
            <label for="tenantFullName">Nama Lengkap Anak Kos *</label>
            <input type="text" id="tenantFullName" class="form-control" placeholder="Contoh: Muhammad Dimas Satria" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="tenantPhone">No. WhatsApp *</label>
              <input type="tel" id="tenantPhone" class="form-control" placeholder="0812xxxxxxxx" required>
            </div>
            <div class="form-group">
              <label for="tenantIdNumber">No. KTP / NIK</label>
              <input type="text" id="tenantIdNumber" class="form-control" placeholder="33xxxxxxxxxxxxxx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="tenantSelectRoom">Pilih Kamar *</label>
              <select id="tenantSelectRoom" class="form-control" required>
                <!-- Populated dynamically -->
              </select>
            </div>
            <div class="form-group">
              <label for="tenantStartDate">Tanggal Masuk *</label>
              <input type="date" id="tenantStartDate" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="tenantDueDay">Tgl Jatuh Tempo Tiap Bulan</label>
              <input type="number" id="tenantDueDay" class="form-control" min="1" max="31" value="1" required>
            </div>
            <div class="form-group">
              <label for="tenantRentAmount">Tarif Sewa (Rp / Bulan)</label>
              <input type="number" id="tenantRentAmount" class="form-control" placeholder="1500000" required>
            </div>
          </div>
          <div class="form-group">
            <label for="tenantEmergencyContact">Kontak Darurat (Ortu / Saudara)</label>
            <input type="text" id="tenantEmergencyContact" class="form-control" placeholder="Nama & No HP Orang Tua">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalAddTenant">Batal</button>
          <button type="submit" class="btn-primary">Simpan Penghuni</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM TAMBAH / EDIT KAMAR
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAddRoom">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3 id="mRoomFormTitle">Tambah Master Kamar</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalAddRoom">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formAddRoom">
        <div class="modal-body">
          <input type="hidden" id="roomEditId" value="">
          <div class="form-row">
            <div class="form-group">
              <label for="roomNumberInput">Nomor Kamar *</label>
              <input type="text" id="roomNumberInput" class="form-control" placeholder="Contoh: 105 atau B02" required>
            </div>
            <div class="form-group">
              <label for="roomFloorInput">Lantai *</label>
              <select id="roomFloorInput" class="form-control" required>
                <option value="1">Lantai 1</option>
                <option value="2">Lantai 2</option>
                <option value="3">Lantai 3</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="roomTypeInput">Tipe Kamar *</label>
              <select id="roomTypeInput" class="form-control" required>
                <option value="VIP AC (KM Dalam)">VIP AC (KM Dalam)</option>
                <option value="Deluxe AC">Deluxe AC</option>
                <option value="Standard Fan (KM Luar)">Standard Fan (KM Luar)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="roomPriceInput">Harga Sewa / Bulan (Rp) *</label>
              <input type="number" id="roomPriceInput" class="form-control" placeholder="1500000" required>
            </div>
          </div>
          <div class="form-group">
            <label for="roomFacilitiesInput">Fasilitas Kamar (pisahkan dengan koma)</label>
            <input type="text" id="roomFacilitiesInput" class="form-control" value="AC, Kasur Springbed, Lemari 2 Pintu, Meja Belajar, KM Dalam, Water Heater">
          </div>
          <div class="form-group">
            <label for="roomStatusInput">Status Kamar</label>
            <select id="roomStatusInput" class="form-control">
              <option value="available">🔵 Kosong (Tersedia)</option>
              <option value="occupied">🟢 Terisi</option>
              <option value="repair">🔴 Dalam Perbaikan</option>
            </select>
          </div>

          <!-- Section Custom Interior & Anggaran Khusus (Dikelola & Diubah Dinamis oleh Admin) -->
          <div class="form-group" style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border-color); background: rgba(37,99,235,0.03); padding: 14px; border-radius: 12px; border: 1px solid rgba(37,99,235,0.15);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <label style="font-weight: 700; color: #1e40af; font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                <span>🎨</span>
                <span>Custom Interior & Anggaran Pelanggan</span>
              </label>
              <span style="font-size: 0.72rem; background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Admin Control</span>
            </div>
            <p style="font-size: 0.76rem; color: var(--text-muted); margin-bottom: 12px;">
              Kelola permintaan penataan interior dari pelanggan dan tentukan/perbarui nominal anggaran (budget) secara dinamis.
            </p>

            <div class="form-row">
              <div class="form-group">
                <label for="roomCustomInteriorStatusInput">Status Custom Interior</label>
                <select id="roomCustomInteriorStatusInput" class="form-control">
                  <option value="none">⚪ Tidak Ada Permintaan</option>
                  <option value="pending">⏳ Menunggu Review / Konfirmasi</option>
                  <option value="approved">✅ Disetujui (Approved)</option>
                  <option value="in_progress">🛠️ Sedang Dikerjakan (In Progress)</option>
                  <option value="completed">🎉 Selesai Dikerjakan</option>
                  <option value="rejected">❌ Ditolak / Tidak Memungkinkan</option>
                </select>
              </div>
              <div class="form-group">
                <label for="roomCustomInteriorBudgetInput">Anggaran Interior Disetujui (Rp) *</label>
                <input type="number" id="roomCustomInteriorBudgetInput" class="form-control" placeholder="Contoh: 750000" min="0" step="10000" value="0">
              </div>
            </div>

            <div class="form-group">
              <label for="roomCustomInteriorRequestInput">Permintaan Desain Pelanggan</label>
              <textarea id="roomCustomInteriorRequestInput" class="form-control" rows="2" placeholder="Catatan permintaan dekorasi atau furnitur tambahan dari calon/penghuni kamar..."></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label for="roomCustomInteriorNotesInput">Catatan Mas Robi untuk Pelanggan</label>
              <input type="text" id="roomCustomInteriorNotesInput" class="form-control" placeholder="Contoh: Ambalan kayu jati siap dipasang tgl 5 November. Budget disetujui Rp 500.000.">
            </div>
          </div>
          <div class="form-group" style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
              <label style="font-weight: 700; color: var(--text-primary); font-size: 0.88rem;">
                📸 3 Foto Slide Kamar (Hanya Diinput & Diatur Oleh Admin)
              </label>
              <button type="button" class="btn-secondary" style="font-size: 0.72rem; padding: 2px 8px;" onclick="window.kosApp.fillDefaultRoomImages()">
                ⚡ 3 Foto Standar
              </button>
            </div>
            <p style="font-size: 0.74rem; color: var(--text-muted); margin-bottom: 10px;">
              Ketiga foto ini akan menjadi latar belakang slide interaktif kamar di Portal Pelanggan dan Landing Page.
            </p>

            <!-- Foto 1 (Utama) -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 10px; margin-bottom: 8px;">
              <span style="font-size: 0.74rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 4px;">1. Foto Kamar Tidur Utama *</span>
              <div style="display: flex; gap: 8px; align-items: center;">
                <img id="roomImgPreview1" src="/assets/images/rooms/room_1.jpg" alt="Slide 1"
                     style="width: 65px; height: 46px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); flex-shrink: 0;">
                <input type="text" id="roomImageInput1" class="form-control" value="assets/images/rooms/room_1.jpg" placeholder="assets/images/rooms/room_1.jpg"
                       oninput="document.getElementById('roomImgPreview1').src = this.value.startsWith('http') || this.value.startsWith('/') ? this.value : '/' + this.value">
              </div>
            </div>

            <!-- Foto 2 -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 10px; margin-bottom: 8px;">
              <span style="font-size: 0.74rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 4px;">2. Foto Meja Kerja / Area Belajar & AC</span>
              <div style="display: flex; gap: 8px; align-items: center;">
                <img id="roomImgPreview2" src="/assets/images/rooms/room_2.jpg" alt="Slide 2"
                     style="width: 65px; height: 46px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); flex-shrink: 0;">
                <input type="text" id="roomImageInput2" class="form-control" value="assets/images/rooms/room_2.jpg" placeholder="assets/images/rooms/room_2.jpg"
                       oninput="document.getElementById('roomImgPreview2').src = this.value.startsWith('http') || this.value.startsWith('/') ? this.value : '/' + this.value">
              </div>
            </div>

            <!-- Foto 3 -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 10px;">
              <span style="font-size: 0.74rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 4px;">3. Foto Kamar Mandi Dalam / Fasilitas Tambahan</span>
              <div style="display: flex; gap: 8px; align-items: center;">
                <img id="roomImgPreview3" src="/assets/images/rooms/room_3.jpg" alt="Slide 3"
                     style="width: 65px; height: 46px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); flex-shrink: 0;">
                <input type="text" id="roomImageInput3" class="form-control" value="assets/images/rooms/room_3.jpg" placeholder="assets/images/rooms/room_3.jpg"
                       oninput="document.getElementById('roomImgPreview3').src = this.value.startsWith('http') || this.value.startsWith('/') ? this.value : '/' + this.value">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalAddRoom">Batal</button>
          <button type="submit" class="btn-primary">Simpan Kamar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM CATAT TRANSAKSI KEUANGAN
       ========================================================================= -->
  <div class="modal-backdrop" id="modalTransaction">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3>Catat Pembayaran / Pengeluaran</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalTransaction">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formTransaction">
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="transType">Jenis Transaksi *</label>
              <select id="transType" class="form-control" required>
                <option value="income">🟢 Pemasukan (Sewa / Token / Laundry)</option>
                <option value="expense">🔴 Pengeluaran (WiFi, Listrik, Kebersihan)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="transDate">Tanggal Transaksi *</label>
              <input type="date" id="transDate" class="form-control" required>
            </div>
          </div>
          <div class="form-group" id="transRoomSelectGroup">
            <label for="transRoomId">Kaitkan dengan Kamar / Penghuni</label>
            <select id="transRoomId" class="form-control">
              <!-- Populated by JS -->
            </select>
          </div>
          <div class="form-group">
            <label for="transDescription">Keterangan Transaksi *</label>
            <input type="text" id="transDescription" class="form-control" placeholder="Contoh: Pembayaran Sewa Kamar 102 Bulan November 2026" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="transAmount">Nominal (Rp) *</label>
              <input type="number" id="transAmount" class="form-control" placeholder="1500000" required>
            </div>
            <div class="form-group">
              <label for="transMethod">Metode Pembayaran</label>
              <select id="transMethod" class="form-control">
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
          <button type="submit" class="btn-primary">Simpan Transaksi</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: KUITANSI DIGITAL (SIAP CETAK / PDF)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalReceipt">
    <div class="modal-dialog" style="max-width: 480px;">
      <div class="modal-header">
        <h3>Kuitansi Pembayaran Digital</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalReceipt">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" style="background: #f8fafc; padding: 18px;">
        <div class="receipt-paper" id="receiptPrintArea">
          <div class="receipt-header">
            <h4>{{ $setting->kos_name }}</h4>
            <p>Smart Living & Kost Eksklusif</p>
            <p>{{ $setting->kos_address }}</p>
            <p style="font-weight: 700; margin-top: 6px; letter-spacing: 0.05em;" id="recNumber">NO: KMR-2026-10-001</p>
          </div>
          <div class="receipt-row">
            <span style="color: #64748b;">Tanggal & Waktu Bayar:</span>
            <span id="recDate" style="font-weight: 700; color: #1e40af;">28 Oktober 2026, 12:00:00 WIB</span>
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
        <button type="button" class="btn-primary" id="btnPrintReceipt">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Cetak / Download PDF
        </button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: FORM BUAT TIKET KELUHAN
       ========================================================================= -->
  <div class="modal-backdrop" id="modalRepairTicket">
    <div class="modal-dialog">
      <div class="modal-header">
        <h3>Laporan Keluhan / Servis Fasilitas</h3>
        <button type="button" class="modal-close-btn" data-close-modal="modalRepairTicket">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formRepairTicket">
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="repairRoomSelect">Pilih Kamar Terkait *</label>
              <select id="repairRoomSelect" class="form-control" required>
                <!-- Populated by JS -->
              </select>
            </div>
            <div class="form-group">
              <label for="repairPriority">Tingkat Urgensi</label>
              <select id="repairPriority" class="form-control">
                <option value="Normal">Normal</option>
                <option value="Mendesak">Mendesak (Penting)</option>
                <option value="Rendah">Rendah</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="repairDescription">Rincian Keluhan / Kerusakan *</label>
            <textarea id="repairDescription" class="form-control" rows="3" placeholder="Contoh: Keran air wastafel di kamar mandi menetes terus menerus atau AC kurang dingin..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalRepairTicket">Batal</button>
          <button type="submit" class="btn-primary">Kirim Laporan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: POP-UP KONFIRMASI LOGOUT ADMIN (MODERN ANIMATION)
       ========================================================================= -->
  <div class="kmr-logout-overlay" id="modalAdminLogoutConfirm">
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
      <p class="kmr-logout-subtitle">Sesi Anda sebagai Pengelola di <strong>{{ $setting->kos_name }}</strong> akan diakhiri. Anda perlu login kembali untuk mengakses panel admin.</p>
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
      modalAdminLogout?.classList.add('open');
    });
    document.getElementById('btnCancelAdminLogout')?.addEventListener('click', function() {
      modalAdminLogout?.classList.remove('open');
    });
    document.getElementById('btnConfirmAdminLogout')?.addEventListener('click', function() {
      formLogoutAdmin?.submit();
    });
    modalAdminLogout?.addEventListener('click', function(e) {
      if (e.target === modalAdminLogout) modalAdminLogout.classList.remove('open');
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

    // Filter Table Pindah Kamar
    function filterTransferTable() {
      var input = document.getElementById('transferSearchInput').value.toLowerCase();
      var rows = document.querySelectorAll('#transfersTableBody tr');
      rows.forEach(function(r) {
        var searchData = r.getAttribute('data-search') || '';
        if (searchData.includes(input)) {
          r.style.display = '';
        } else {
          r.style.display = 'none';
        }
      });
    }

    function filterTransferStatus(status, btn) {
      document.querySelectorAll('#transferStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      btn.classList.add('active');
      var rows = document.querySelectorAll('#transfersTableBody tr');
      rows.forEach(function(r) {
        var rStatus = r.getAttribute('data-status');
        if (status === 'all' || rStatus === status) {
          r.style.display = '';
        } else {
          r.style.display = 'none';
        }
      });
    }

    // =========================================================================
    // KURVA PERTUMBUHAN PENDAPATAN & ARUS KAS (CANVAS SPLINE CURVE INTERACTIVE)
    // =========================================================================
    var chartMonths = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    var chartIncomeFull = {!! json_encode($chartIncome ?? [15650000,16300000,16950000,17600000,18250000,18900000,19550000,20200000,20850000,21500000,19500000,19500000]) !!};
    var chartExpenseFull = {!! json_encode($chartExpense ?? [3350000,3500000,3650000,3800000,3950000,4100000,4250000,4400000,4550000,4700000,3800000,3800000]) !!};
    var chartNetFull = {!! json_encode($chartNet ?? [12300000,12800000,13300000,13800000,14300000,14800000,15300000,15800000,16300000,16800000,15700000,15700000]) !!};

    var currentCurvePeriod = "1y";

    async function changeFinanceYear(year) {
      try {
        var res = await fetch('/finances/stats?year=' + encodeURIComponent(year));
        var data = await res.json();
        if (data.success) {
          if (document.getElementById('finKpiIncomeVal')) {
            document.getElementById('finKpiIncomeVal').textContent = 'Rp ' + Number(data.income).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiExpenseVal')) {
            document.getElementById('finKpiExpenseVal').textContent = 'Rp ' + Number(data.expense).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiNetVal')) {
            document.getElementById('finKpiNetVal').textContent = 'Rp ' + Number(data.net).toLocaleString('id-ID');
          }
          if (document.getElementById('finKpiAvgVal')) {
            document.getElementById('finKpiAvgVal').textContent = 'Rp ' + Number(data.avgMonthly).toLocaleString('id-ID');
          }

          document.querySelectorAll('.fin-selected-year-badge').forEach(function(el) {
            el.textContent = year;
          });
          var curveYearEl = document.getElementById('curveYearLabel');
          if (curveYearEl) curveYearEl.textContent = '(Tahun ' + year + ')';

          chartIncomeFull = data.chartIncome;
          chartExpenseFull = data.chartExpense;
          chartNetFull = data.chartNet;
          drawRevenueCurve();

          if (typeof showToast === 'function') {
            showToast('Laporan keuangan tahun ' + year + ' berhasil dimuat!', 'info');
          }
        }
      } catch (err) {
        console.error('Error changing finance year:', err);
      }
    }

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

      var labels, income, expense, net;
      if (currentCurvePeriod === "30d") {
        labels = ["Mg 1", "Mg 2", "Mg 3", "Mg 4"];
        income = [4850000, 5200000, 4900000, 6550000];
        expense = [900000, 850000, 1200000, 750000];
        net = [3950000, 4350000, 3700000, 5800000];
      } else if (currentCurvePeriod === "6m") {
        labels = chartMonths.slice(4, 10);
        income = chartIncomeFull.slice(4, 10);
        expense = chartExpenseFull.slice(4, 10);
        net = chartNetFull.slice(4, 10);
      } else {
        labels = chartMonths;
        income = chartIncomeFull;
        expense = chartExpenseFull;
        net = chartNetFull;
      }

      ctx.clearRect(0, 0, width, height);

      var padLeft = 60;
      var padRight = 24;
      var padTop = 24;
      var padBottom = 34;

      var plotW = width - padLeft - padRight;
      var plotH = height - padTop - padBottom;

      var maxVal = Math.max(...income, ...expense, ...net, 1000000);
      maxVal = Math.ceil((maxVal * 1.15) / 5000000) * 5000000;

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

        ctx.strokeStyle = gridColor;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padLeft, yPos);
        ctx.lineTo(width - padRight, yPos);
        ctx.stroke();

        var labelText = yVal >= 1000000 ? (yVal / 1000000).toFixed(0) + " Jt" : (yVal / 1000).toFixed(0) + " Rb";
        ctx.fillText(labelText, padLeft - 10, yPos);
      }

      // X Points
      var n = labels.length;
      var stepX = n > 1 ? plotW / (n - 1) : plotW;
      var points = [];

      for (var j = 0; j < n; j++) {
        var px = padLeft + j * stepX;
        var yInc = padTop + plotH - (income[j] / maxVal) * plotH;
        var yExp = padTop + plotH - (expense[j] / maxVal) * plotH;
        var yNet = padTop + plotH - (net[j] / maxVal) * plotH;
        points.push({ x: px, inc: yInc, exp: yExp, net: yNet, label: labels[j], rawInc: income[j], rawExp: expense[j], rawNet: net[j] });

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

        if (fill) {
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
      drawSpline(points, "net", "#3b82f6", "rgba(59, 130, 246, 0.15)", "rgba(59, 130, 246, 0.0)", false);
      drawSpline(points, "exp", "#ef4444", "rgba(239, 68, 68, 0.15)", "rgba(239, 68, 68, 0.0)", false);
    }

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
    // TAGIHAN SEWA (TAB-BILLS) FILTER & AKSI CEPAT
    // =========================================================================
    function filterBillingTable() {
      var input = document.getElementById("billingSearchInput").value.toLowerCase();
      var rows = document.querySelectorAll("#billingTableBody tr");
      rows.forEach(function(r) {
        var s = r.getAttribute("data-search") || "";
        r.style.display = s.includes(input) ? "" : "none";
      });
    }

    function filterBillingStatus(status, btn) {
      document.querySelectorAll("#billingStatusFilter .filter-pill").forEach(function(p) { p.classList.remove("active"); });
      if (btn) btn.classList.add("active");
      var rows = document.querySelectorAll("#billingTableBody tr");
      rows.forEach(function(r) {
        var rStatus = r.getAttribute("data-status");
        if (status === "all" || rStatus === status) {
          r.style.display = "";
        } else {
          r.style.display = "none";
        }
      });
    }

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

    function exportFinancesCSV() {
      var csv = "No Bukti,Tanggal,Keterangan,Kategori,Tipe,Nominal,Metode\n";
      var rows = document.querySelectorAll("#financesTableBody tr");
      rows.forEach(function(r) {
        var cols = r.querySelectorAll("td");
        if (cols.length >= 6) {
          var line = [
            cols[0].textContent.trim(),
            cols[1].textContent.trim(),
            "\"" + cols[2].textContent.trim().replace(/"/g, '""') + "\"",
            cols[3].textContent.trim(),
            cols[4].textContent.includes("-") ? "Pengeluaran" : "Pemasukan",
            "\"" + cols[4].textContent.trim() + "\"",
            cols[5].textContent.trim()
          ].join(",");
          csv += line + "\n";
        }
      });

      var blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
      var url = URL.createObjectURL(blob);
      var link = document.createElement("a");
      link.setAttribute("href", url);
      link.setAttribute("download", "buku_kas_kos_mas_robi_" + new Date().toISOString().slice(0,10) + ".csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

  </script>

@endsection
