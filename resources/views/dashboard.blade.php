@extends('layouts.app')

@section('title', ($setting->kos_name ?? 'Kos Mas Robi') . ' - Smart Kost Management')

@section('content')

  <!-- =========================================================================
       SIDEBAR (Desktop Fixed, Mobile Slide-out Drawer)
       ========================================================================= -->
  <aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-header">
      <div class="brand-icon-box">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
          <path d="M9 21V12h6v9"/>
        </svg>
      </div>
      <div class="brand-details">
        <h2>{{ $setting->owner_name ? 'Kos ' . $setting->owner_name : 'Kos Mas Robi' }}</h2>
        <span class="brand-badge">Laravel Edition</span>
      </div>
    </div>

    <nav class="sidebar-nav" aria-label="Main Navigation">
      <div class="nav-section-title">Menu Utama</div>

      <div class="nav-item active" data-tab="tab-dashboard" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
        <span>Dashboard</span>
      </div>

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

      <div class="nav-item" data-tab="tab-finances" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span>Keuangan & Tagihan</span>
        <span class="nav-badge-count" id="badgeDueBills" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;">{{ $dueRooms }}</span>
      </div>

      <div class="nav-section-title" style="margin-top: 14px;">Operasional</div>

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

      <div class="nav-item" data-tab="tab-settings" role="button" tabindex="0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Pengaturan Kos</span>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="host-profile">
        <div class="host-avatar">MR</div>
        <div class="host-info">
          <div class="host-name" id="profileOwnerName">{{ $setting->owner_name ?? 'Mas Robi' }}</div>
          <div class="host-role">Owner & Pengelola</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- =========================================================================
       MAIN APPLICATION AREA
       ========================================================================= -->
  <main class="app-main">

    <!-- HEADER TOP BAR -->
    <header class="app-header">
      <div class="header-left">
        <button type="button" class="mobile-menu-toggle" id="btnToggleMobileSidebar" aria-label="Buka Menu Navigasi">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="page-title-box">
          <h1 id="pageTitle">Dashboard Ikhtisar</h1>
          <p id="pageSubtitle">Selamat datang kembali di panel kelola {{ $setting->kos_name }}</p>
        </div>
      </div>

      <div class="header-right">
        <div class="header-search">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="globalSearchInput" placeholder="Cari kamar, anak kos..." aria-label="Cari data kos">
        </div>

        <!-- Theme Switcher Button (Gambar 1: Sliding Pill Switch di Header Pojok Kanan) -->
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
        <form action="{{ route('logout') }}" method="POST" id="formLogoutAdmin" style="display:inline;">
          @csrf
          <button type="button" class="header-action-btn" id="btnTriggerLogoutAdmin" title="Keluar / Logout" style="color:#dc2626;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </button>
        </form>

        <!-- Quick Add Button -->
        <button type="button" class="btn-primary-action" id="btnQuickAdd">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>+ Penghuni Baru</span>
        </button>
      </div>
    </header>

    <!-- PROPERTY HERO BANNER -->
    <section class="property-banner" aria-label="Info Properti Kos">
      <img src="{{ asset('assets/images/kos_exterior.jpg') }}" alt="Eksterior Kos Mas Robi" class="property-banner-bg" id="propertyBannerImg">
      <div class="property-banner-content">
        <span class="property-tag">✨ Kos Eksklusif & Nyaman</span>
        <h2 id="bannerPropertyName">{{ $setting->kos_name }}</h2>
        <p id="bannerPropertyAddress">📍 {{ $setting->kos_address }}</p>
        <div class="banner-stats-row">
          <div class="banner-pill">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
            <span>WiFi 100 Mbps</span>
          </div>
          <div class="banner-pill">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Akses 24 Jam</span>
          </div>
          <div class="banner-pill">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
            <span>Listrik Token Mandiri</span>
          </div>
        </div>
      </div>
    </section>

    <!-- STATS KPI CARDS -->
    <section class="stats-grid" aria-label="Ringkasan Statistik Kos">
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

    <!-- =======================================================================
         PANE 1: TAB DASHBOARD (DENAH INTERAKTIF)
         ======================================================================= -->
    <div class="content-body">
      <div class="tab-pane active" id="tab-dashboard">
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
           PANE 4: TAB KEUANGAN & PEMBAYARAN
           ===================================================================== -->
      <div class="tab-pane" id="tab-finances">
        <div class="section-header">
          <div class="section-title">
            <h2>Buku Kas & Pembayaran Kos</h2>
          </div>
          <button type="button" class="btn-primary-action" id="btnRecordTransaction">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>+ Catat Transaksi Baru</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="financeSearchInput" placeholder="Cari riwayat transaksi...">
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
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Nominal</th>
                <th>Metode</th>
                <th>Kuitansi</th>
              </tr>
            </thead>
            <tbody id="financesTableBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>

          <div class="mobile-card-list" id="financesMobileCardList">
            <!-- Dynamically populated -->
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
        <div class="section-header">
          <div class="section-title">
            <h2>Profil & Pengaturan Kos Mas Robi</h2>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
          <!-- Profile Card -->
          <div class="table-card-container" style="padding: 24px;">
            <h3 style="margin-bottom: 16px;">Informasi Properti Kos</h3>
            <form id="settingsProfileForm" style="display: flex; flex-direction: column; gap: 14px;">
              <div class="form-group">
                <label for="setKosName">Nama Kos</label>
                <input type="text" id="setKosName" class="form-control" value="{{ $setting->kos_name }}">
              </div>
              <div class="form-group">
                <label for="setOwnerName">Nama Pemilik / Pengelola</label>
                <input type="text" id="setOwnerName" class="form-control" value="{{ $setting->owner_name }}">
              </div>
              <div class="form-group">
                <label for="setOwnerPhone">No. WhatsApp Pengelola (Format 62...)</label>
                <input type="text" id="setOwnerPhone" class="form-control" value="{{ $setting->owner_phone }}">
              </div>
              <div class="form-group">
                <label for="setKosAddress">Alamat Lengkap</label>
                <textarea id="setKosAddress" class="form-control" rows="3">{{ $setting->kos_address }}</textarea>
              </div>
              <button type="submit" class="btn-primary" style="align-self: flex-start;">Simpan Perubahan Profil</button>
            </form>
          </div>

          <!-- Payment Account Card -->
          <div class="table-card-container" style="padding: 24px;">
            <h3 style="margin-bottom: 16px;">Rekening Pembayaran & Cadangan Data</h3>
            <form id="settingsBankForm" style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
              <div class="form-group">
                <label for="setBankName">Nama Bank / QRIS</label>
                <input type="text" id="setBankName" class="form-control" value="{{ $setting->bank_name }}">
              </div>
              <div class="form-group">
                <label for="setBankAccount">Nomor Rekening</label>
                <input type="text" id="setBankAccount" class="form-control" value="{{ $setting->bank_account }}">
              </div>
              <div class="form-group">
                <label for="setBankHolder">Atas Nama</label>
                <input type="text" id="setBankHolder" class="form-control" value="{{ $setting->bank_holder }}">
              </div>
              <button type="submit" class="btn-primary" style="align-self: flex-start;">Simpan Rekening</button>
            </form>

            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 20px 0;">

            <h4 style="margin-bottom: 10px;">Manajemen Data Sistem</h4>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">Data tersimpan aman di database SQLite Laravel. Anda dapat mengunduh cadangan JSON kapan saja.</p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
              <button type="button" class="btn-secondary" id="btnExportData">📥 Unduh Cadangan JSON</button>
              <button type="button" class="btn-secondary" id="btnResetData" style="color: #f87171;">🔄 Reset Data Sampel</button>
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
  </script>

@endsection
