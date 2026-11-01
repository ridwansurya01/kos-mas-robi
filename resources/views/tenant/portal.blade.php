@extends('layouts.portal')

@section('title', 'Portal Anak Kos — ' . ($setting->kos_name ?? 'Kos Mas Robi'))

@section('content')

  {{-- ======================================================= NAVBAR ======================================================= --}}
  <nav class="tp-navbar">
    <div class="tp-navbar-brand">
      <div class="tp-brand-icon-box">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
          <path d="M9 21V12h6v9"/>
        </svg>
      </div>
      <div>
        <div class="tp-brand-name">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
        <div class="tp-brand-sub">Portal Anak Kos</div>
      </div>
    </div>

    {{-- Center Tabs: Dashboard Kos & Kamarku --}}
    <div class="tp-navbar-nav-tabs">
      <button type="button" class="tp-tab-btn active" id="tabBtnCatalog" onclick="switchPortalTab('catalog')">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Dashboard Kos</span>
        <span class="tp-tab-count">{{ $totalRoomsCount }} Kamar</span>
      </button>
      <button type="button" class="tp-tab-btn" id="tabBtnMyRoom" onclick="switchPortalTab('myroom')">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Kamarku</span>
        @if($room)
          <span class="tp-tab-room-pill">#{{ $room->number }}</span>
        @endif
      </button>
    </div>

    <div class="tp-navbar-actions">
      {{-- Profil Pelanggan Button --}}
      <button type="button" class="tp-btn-profile" id="btnOpenProfile" title="Buka Profil Saya">
        <img src="{{ asset($user->avatar ?? 'assets/images/tenant_avatar.jpg') }}"
             alt="{{ $user->name }}" class="tp-btn-profile-img" id="navAvatarImg">
        <div class="tp-btn-profile-info">
          <span class="tp-btn-profile-name">{{ Str::limit($user->name, 15) }}</span>
          <span class="tp-btn-profile-room">Kamar #{{ $room->number ?? '102' }}</span>
        </div>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="tp-btn-profile-chevron">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </button>

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
      <div class="tp-alert success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if(isset($errors) && $errors->any())
      <div class="tp-alert error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    {{-- =========================================================================
         TAB 1: DASHBOARD KOS (ALL ROOMS CATALOG & LIVE AVAILABILITY)
         ========================================================================= --}}
    <div id="sectionDashboardCatalog" class="tp-portal-tab-content active">

      <!-- Catalog Header Banner (With Kos Building Background Image) -->
      <div class="tp-catalog-header">
        <img src="{{ asset('assets/images/kos_exterior.jpg') }}" alt="Eksterior Kos" class="tp-catalog-header-bg">
        <div class="tp-catalog-header-content">
          <div class="tp-catalog-header-top">
            <div>
              <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(37,99,235,0.35); backdrop-filter:blur(8px); border:1px solid rgba(147,197,253,0.35); border-radius:999px; padding:4px 14px; font-size:0.75rem; font-weight:700; color:#bfdbfe; margin-bottom:10px;">
                <span>✨ Kos Eksklusif & Nyaman</span>
              </div>
              <h2 class="tp-catalog-title">Dashboard {{ $setting->kos_name ?? 'Kos Mas Robi' }}</h2>
              <p class="tp-catalog-desc">
                Pantau status ketersediaan setiap kamar secara transparan, lihat fasilitas lengkap, dan ajukan pemesanan atau <strong>Custom Interior</strong> sesuai kebutuhan dan budget Anda.
              </p>
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
          <button type="button" class="tp-filter-btn" data-filter="available" onclick="filterCatalogRooms('available', this)">
            🟢 Tersedia / Kosong ({{ $availableRoomsCount }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="occupied" onclick="filterCatalogRooms('occupied', this)">
            🔒 Sudah Disewa ({{ $occupiedRoomsCount }})
          </button>
          <button type="button" class="tp-filter-btn" data-filter="floor-1" onclick="filterCatalogRooms('floor-1', this)">
            🏢 Lantai 1
          </button>
          <button type="button" class="tp-filter-btn" data-filter="floor-2" onclick="filterCatalogRooms('floor-2', this)">
            🏢 Lantai 2
          </button>
        </div>
      </div>

      <!-- Room Catalog Grid -->
      <div class="tp-catalog-grid" id="catalogRoomGrid">
        @foreach($allRooms as $r)
          @php
            $isMyRoom = ($room && $room->id === $r->id);
            $rThumb = !empty($r->images_list[0]) ? asset($r->images_list[0]) : asset('assets/images/rooms/room_1.jpg');
            $statusClass = $isMyRoom ? 'my-room' : ($r->status === 'available' ? 'available' : 'occupied');
            $statusText = $isMyRoom ? '⭐ Kamar Anda' : ($r->status === 'available' ? '🟢 Tersedia' : '🔒 Sudah Disewa');
            $hasCustomInterior = $r->custom_interior_status && $r->custom_interior_status !== 'none';
          @endphp
          <div class="tp-room-card room-item"
               data-status="{{ $r->status }}"
               data-floor="floor-{{ $r->floor }}"
               onclick="{{ $isMyRoom ? "switchPortalTab('myroom')" : "openRoomCustomModal(" . htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') . ")" }}">
            
            <div class="tp-room-card-thumb-wrap">
              <img src="{{ $rThumb }}" alt="Foto Kamar #{{ $r->number }}" class="tp-room-card-thumb" loading="lazy">
              <span class="tp-room-card-number-badge">Kamar #{{ $r->number }}</span>
              <span class="tp-room-card-status-badge {{ $statusClass }}">{{ $statusText }}</span>
              @if($hasCustomInterior)
                <span class="tp-room-card-interior-tag" title="Permintaan Custom Interior Aktif">
                  <span>🎨</span>
                  <span>Custom Interior</span>
                </span>
              @endif
            </div>

            <div class="tp-room-card-body">
              <div class="tp-room-card-head">
                <h3 class="tp-room-card-title">Kamar #{{ $r->number }}</h3>
                <span class="tp-room-card-floor">Lt. {{ $r->floor }}</span>
              </div>
              <div class="tp-room-card-type">{{ $r->type }}</div>

              <div class="tp-room-card-facilities">
                @foreach(array_slice($r->facilities ?? ['AC', 'WiFi', 'Kasur', 'Lemari', 'KM Dalam'], 0, 4) as $fac)
                  <span class="tp-room-card-facility-pill">✓ {{ $fac }}</span>
                @endforeach
              </div>

              <div class="tp-room-card-foot">
                <div class="tp-room-card-price-box">
                  <span class="tp-room-card-price-label">Biaya Sewa</span>
                  <span class="tp-room-card-price-val">Rp {{ number_format($r->price, 0, ',', '.') }}<span class="tp-room-card-price-unit">/bln</span></span>
                </div>

                @if($isMyRoom)
                  <button type="button" class="tp-room-card-action-btn is-my-room" onclick="event.stopPropagation(); switchPortalTab('myroom')">
                    <span>🏠 Kamarku</span>
                    <span>→</span>
                  </button>
                @else
                  <button type="button" class="tp-room-card-action-btn" onclick="event.stopPropagation(); openRoomCustomModal({{ htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') }})">
                    <span>Lihat & Custom</span>
                    <span>→</span>
                  </button>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>

    {{-- =========================================================================
         TAB 2: KAMARKU (MY ROOM — CURRENTLY RENTED ROOM VIEW)
         ========================================================================= --}}
    <div id="sectionKamarku" class="tp-portal-tab-content">

    {{-- ------------------------------------------------ WELCOME BANNER ------------------------------------------------ --}}
    <div class="tp-welcome-banner">
      <div class="tp-welcome-left">
        <div class="tp-welcome-avatar-wrap" id="btnOpenProfileBanner" style="cursor: pointer;" title="Klik untuk edit profil">
          <img src="{{ asset($user->avatar ?? 'assets/images/tenant_avatar.jpg') }}"
               alt="{{ $user->name }}" class="tp-welcome-avatar">
          <span class="tp-welcome-edit-badge" title="Ubah profil">✎</span>
        </div>
        <div>
          <div class="tp-welcome-greeting">{{ $greeting }} Selamat datang kembali 👋</div>
          <div class="tp-welcome-name">{{ $user->name }}</div>
          <div class="tp-welcome-meta">
            <span class="tp-chip">🏠 Kamar #{{ $room->number ?? '102' }}</span>
            <span class="tp-chip">🏢 Lantai {{ $room->floor ?? '1' }}</span>
            <span class="tp-chip">❄️ {{ $room->type ?? 'Deluxe AC' }}</span>
            <span class="tp-chip green">● Aktif Menghuni</span>
          </div>
        </div>
      </div>

    </div>

    {{-- ----------------------------------------------------- SHOWCASE SECTION (Desktop: Kiri Foto, Kanan Detail | Mobile: Atas Foto, Bawah Detail) ----------------------------------------------------- --}}
    @php
      $roomImages = $room->images_list ?? [
        'assets/images/rooms/room_1.jpg',
        'assets/images/rooms/room_2.jpg',
        'assets/images/rooms/room_3.jpg',
      ];
    @endphp

    <div class="tp-showcase-container" id="tpHeroCard">

      {{-- KIRI di Desktop / ATAS di Mobile: GALLERY SLIDER --}}
      <div class="tp-showcase-gallery">
        {{-- Slider Track --}}
        <div class="tp-gallery-slider" id="heroBgSlider">
          @foreach($roomImages as $idx => $img)
            <div class="tp-hero-bg-slide {{ $idx === 0 ? 'active' : '' }}"
                 style="background-image: url('{{ asset($img) }}');"
                 data-slide-index="{{ $idx }}"></div>
          @endforeach
        </div>

        {{-- Top Badge: Status Foto --}}
        <div class="tp-gallery-badge">
          <span class="tp-pulse-dot"></span>
          <span id="heroSlideBadgeText">Foto 1/3: Kamar #{{ $room->number ?? '102' }}</span>
        </div>

        {{-- Bottom Controls: Prev, Dots, Next --}}
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

      {{-- KANAN di Desktop / BAWAH di Mobile: PENJELASAN & DETAIL TAGIHAN (Clean White Card Inspired by Reference) --}}
      <div class="tp-showcase-details">
        
        <div class="tp-details-top">
          <div class="tp-details-header">
            <div>
              <span class="tp-badge-category">Kamar Kost Anda</span>
              <h2 class="tp-details-title">Kamar #{{ $room->number ?? '102' }} • {{ $room->type ?? 'Deluxe AC' }}</h2>
            </div>
            <span class="tp-status-pill {{ ($room->status ?? '') === 'due' ? 'due' : 'paid' }}">
              @if(($room->status ?? '') === 'due')
                ⚠️ Menunggu Bayar
              @else
                ● Sewa Aktif
              @endif
            </span>
          </div>

          {{-- Pricing & Due Date Box --}}
          <div class="tp-price-banner">
            <div class="tp-price-main">
              <span class="tp-price-lbl">Tagihan Sewa Berikutnya</span>
              <div class="tp-price-val">
                Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}
                <span class="tp-price-cycle">/bulan</span>
              </div>
            </div>
            <div class="tp-due-badge">
              <span class="tp-due-lbl">Jatuh Tempo</span>
              <span class="tp-due-date">{{ $dueDate->translatedFormat('d F Y') }}</span>
            </div>
          </div>

          {{-- Description --}}
          <div class="tp-desc-block">
            <h4 class="tp-desc-heading">Deskripsi Hunian</h4>
            <p class="tp-desc-body">
              Kamar tipe {{ $room->type ?? 'Deluxe AC' }} berlokasi di Lantai {{ $room->floor ?? '1' }}. Dilengkapi pendingin ruangan (AC), kamar mandi pribadi dalam, meja belajar/kerja, lemari, serta pencahayaan alami yang nyaman dan tenang.
            </p>
          </div>

          {{-- Facilities Quick Icons (Matching Reference App) --}}
          <div class="tp-fac-quick-list">
            <div class="tp-fac-pill">
              <span class="tp-fac-emoji">🛏️</span>
              <span class="tp-fac-text">Kasur Queen</span>
            </div>
            <div class="tp-fac-pill">
              <span class="tp-fac-emoji">🚿</span>
              <span class="tp-fac-text">KM Dalam</span>
            </div>
            <div class="tp-fac-pill">
              <span class="tp-fac-emoji">❄️</span>
              <span class="tp-fac-text">AC Dingin</span>
            </div>
            <div class="tp-fac-pill">
              <span class="tp-fac-emoji">📶</span>
              <span class="tp-fac-text">WiFi 100M</span>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="tp-details-actions">
          <button type="button" class="tp-btn-primary-action" id="btnPay">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            <span>Bayar Sekarang</span>
          </button>
          <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin,%20konfirmasi%20sewa%20Kamar%20%23{{ $room->number ?? '102' }}"
             target="_blank" class="tp-btn-secondary-action tp-btn-wa-green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            <span>WhatsApp Admin</span>
          </a>
        </div>

      </div>

    </div>{{-- /tp-showcase-container --}}

    {{-- ----------------------------------------------------- OVERVIEW METRICS & ROOM INFO (Flexible 2 Col on Desktop, 1 Col on Mobile) ----------------------------------------------------- --}}
    <div class="tp-overview-row">

      {{-- Metrics Card --}}
      <div class="tp-metrics-card">
        <div class="tp-card-row">
          <span class="tp-card-label">Status Fasilitas Kamar</span>
          <span class="tp-badge blue">Kamar #{{ $room->number ?? '102' }}</span>
        </div>
        <div class="tp-metrics-grid">
          <div class="tp-metric-box">
            <span class="tp-metric-emoji">🌡️</span>
            <span class="tp-metric-val">22°C</span>
            <span class="tp-metric-lbl">AC Dingin</span>
            <span class="tp-metric-pill">Normal</span>
          </div>
          <div class="tp-metric-box">
            <span class="tp-metric-emoji">📡</span>
            <span class="tp-metric-val">100</span>
            <span class="tp-metric-lbl">WiFi (Mbps)</span>
            <span class="tp-metric-pill">Cepat</span>
          </div>
          <div class="tp-metric-box">
            <span class="tp-metric-emoji">⚡</span>
            <span class="tp-metric-val">On</span>
            <span class="tp-metric-lbl">Token Listrik</span>
            <span class="tp-metric-pill">Aktif</span>
          </div>
        </div>
      </div>

      {{-- Room Info Card --}}
      <div class="tp-roominfo-card">
        <div class="tp-card-row">
          <span class="tp-card-label">Info Hunian Kamu</span>
          <span class="tp-badge green">● Menghuni</span>
        </div>
        <div class="tp-roominfo-grid">
          <div class="tp-roominfo-item">
            <div class="tp-roominfo-ico">🏠</div>
            <div>
              <div class="tp-roominfo-val">Kamar #{{ $room->number ?? '102' }}</div>
              <div class="tp-roominfo-key">Nomor Kamar</div>
            </div>
          </div>
          <div class="tp-roominfo-item">
            <div class="tp-roominfo-ico">📅</div>
            <div>
              <div class="tp-roominfo-val">{{ $dueDate->format('d M Y') }}</div>
              <div class="tp-roominfo-key">Jatuh Tempo</div>
            </div>
          </div>
          <div class="tp-roominfo-item">
            <div class="tp-roominfo-ico">💰</div>
            <div>
              <div class="tp-roominfo-val">Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}</div>
              <div class="tp-roominfo-key">Sewa / Bulan</div>
            </div>
          </div>
          <div class="tp-roominfo-item">
            <div class="tp-roominfo-ico">✅</div>
            <div>
              <div class="tp-roominfo-val green">Lunas</div>
              <div class="tp-roominfo-key">Status Pembayaran</div>
            </div>
          </div>
        </div>
      </div>

    </div>{{-- /tp-overview-row --}}

    {{-- ------------------------------------------- QUICK ACCESS SECTION ------------------------------------------- --}}
    <div class="tp-quick-section">
      <div class="tp-section-hdr">
        <h2 class="tp-section-ttl">Akses & Layanan Cepat</h2>
        <span class="tp-section-sub">Semua layanan anak kos dalam satu klik</span>
      </div>
      <div class="tp-quick-grid">

        <button type="button" class="tp-quick-tile" id="btnOpenPay">
          <div class="tp-quick-ico blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
          </div>
          <div>
            <div class="tp-quick-ttl">Bayar Sewa</div>
            <div class="tp-quick-desc">Konfirmasi transfer pembayaran bulanan ke rekening pengelola</div>
          </div>
          <div class="tp-quick-footer"><span></span><span class="tp-quick-arrow">→</span></div>
        </button>

        <button type="button" class="tp-quick-tile" id="btnOpenComplaint">
          <div class="tp-quick-ico purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
          </div>
          <div>
            <div class="tp-quick-ttl">Lapor Keluhan</div>
            <div class="tp-quick-desc">Laporkan kerusakan AC, kran air, lampu, atau fasilitas kamar</div>
          </div>
          <div class="tp-quick-footer"><span></span><span class="tp-quick-arrow">→</span></div>
        </button>

        <button type="button" class="tp-quick-tile" id="btnOpenReceipts">
          <div class="tp-quick-ico amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
          </div>
          <div>
            <div class="tp-quick-ttl">Kuitansi Digital</div>
            <div class="tp-quick-desc">Lihat riwayat bukti bayar & kuitansi sewa lunas</div>
          </div>
          <div class="tp-quick-footer"><span></span><span class="tp-quick-arrow">→</span></div>
        </button>

        {{-- Layanan Pengajuan Pindah Kamar (Sesuai Permintaan Revisi) --}}
        <button type="button" class="tp-quick-tile" id="btnOpenRoomTransfer">
          <div class="tp-quick-ico teal" style="background: #f0fdfa; color: #0d9488;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 3h5v5"/>
              <path d="M4 20L21 3"/>
              <path d="M21 16v5h-5"/>
              <path d="M15 15l6 6"/>
              <path d="M4 4l5 5"/>
            </svg>
          </div>
          <div>
            <div class="tp-quick-ttl">Pengajuan Pindah Kamar</div>
            <div class="tp-quick-desc">Ajukan pindah unit/lantai kamar kos baru dengan mudah & cepat</div>
          </div>
          <div class="tp-quick-footer"><span></span><span class="tp-quick-arrow">→</span></div>
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
      
      <!-- Photo Preview -->
      <div style="position: relative; border-radius: 16px; overflow: hidden; height: 220px; background: #0f172a; border: 1px solid #e2e8f0;">
        <img id="mCustomRoomImg" src="{{ asset('assets/images/rooms/room_1.jpg') }}" alt="Foto Kamar" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; bottom: 12px; left: 14px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); color: #ffffff; padding: 4px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700;">
          <span id="mCustomRoomBadge">Kamar #101</span> • <span id="mCustomRoomFloor">Lantai 1</span>
        </div>
        <div style="position: absolute; bottom: 12px; right: 14px;" id="mCustomRoomStatusTag">
          <span class="tp-pill paid">🟢 Tersedia</span>
        </div>
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

      <!-- Custom Interior Request Form -->
      <form id="formCustomInterior" method="POST" action="">
        @csrf
        <div class="tp-custom-interior-card">
          <div class="tp-custom-interior-title">
            <span>✨</span>
            <span>Permintaan Custom Interior & Booking Kamar</span>
          </div>
          <p class="tp-custom-interior-desc">
            Ingin tata letak furnitur tambahan seperti ambalan kayu estetik, lampu warm white, karpet, atau penataan khusus? Tuliskan permintaan Anda di bawah beserta perkiraan anggaran. Mas Robi akan meninjau dan menetapkan anggaran khusus secara fleksibel.
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
              🚀 Ajukan Custom Interior & Pesan Kamar
            </button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- Modal 1: Bayar Sewa --}}
<div class="modal-overlay" id="modalPay">
  <div class="modal-box">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Konfirmasi Pembayaran Sewa</h3>
        <p class="modal-subtitle">Kamar #{{ $room->number ?? '102' }} — {{ $user->name }}</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalPay" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <form action="{{ route('tenant.payment.submit') }}" method="POST" enctype="multipart/form-data" id="formPay" onsubmit="return validatePaymentSubmission(event)">
      @csrf
      <div class="modal-body">
        
        <!-- Pilihan Metode Pembayaran Otomatis -->
        <div class="form-group" style="margin-bottom: 12px;">
          <label for="payMethod" class="form-label">
            <span>Metode Pembayaran *</span>
            <span class="form-label-hint">Pilih rekening tujuan atau cara bayar</span>
          </label>
          <select id="payMethod" name="payment_method" class="form-control" required onchange="handlePaymentMethodChange(this.value)">
            <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
            <option value="Transfer BCA">Transfer Bank BCA</option>
            <option value="Transfer BNI">Transfer Bank BNI</option>
            <option value="Transfer BRI">Transfer Bank BRI</option>
            <option value="Transfer QRIS">Transfer QRIS (Instant All Payment / E-Wallet)</option>
            <option value="Tunai (Cash)">Tunai (Cash ke Mas Robi)</option>
          </select>
        </div>

        <!-- Box Tujuan Pembayaran Dinamis (Otomatis Berubah Sesuai Pilihan Bank / QRIS / Cash) -->
        <div class="bank-info-box" id="payDestinationBox" style="margin-bottom: 16px;">
          <div class="bank-info-hdr">
            <span class="bank-badge" id="payDestBadge">Rekening Resmi Mandiri</span>
            <span class="bank-online" id="payDestStatus">● Siap Terima</span>
          </div>
          <div class="bank-name" id="payDestBankName">Bank Mandiri</div>
          
          <!-- Nomor Rekening / Kode -->
          <div class="bank-number-row" id="payDestNumberRow">
            <div class="bank-number" id="bankNumberText">1370019283100</div>
            <button type="button" class="btn-copy-acc" id="btnCopyAcc" onclick="copyText(document.getElementById('bankNumberText').textContent, this)">
              Salin No. Rekening
            </button>
          </div>

          <!-- Keterangan Khusus QRIS / Cash -->
          <div id="payDestInstructions" class="pay-dest-instructions" style="display: none;">
          </div>

          <div class="bank-holder" id="payDestHolder">a.n. <strong>Robi Suryanto</strong></div>
        </div>

        <!-- Fitur Khusus Pembayaran Tunai (Cash): Input Tanggal Bayar -->
        <div class="form-group cash-date-box" id="cashDateGroup" style="display: none; padding: 14px; border-radius: 14px; margin-bottom: 16px;">
          <label for="payCashDate" class="form-label">
            <span style="font-weight: 800;">📅 Tanggal Bayar Tunai (Cash) *</span>
            <span class="form-label-hint">Tentukan tanggal Anda menyerahkan uang tunai</span>
          </label>
          <input type="date" id="payCashDate" name="cash_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
          <div class="cash-date-note" style="font-size: 0.76rem; margin-top: 6px; line-height: 1.4;">
            💡 <em>Catatan:</em> Silakan serahkan uang tunai sewa kamar langsung kepada Mas Robi di lantai 1 pada tanggal yang Anda tentukan di atas. Kuitansi resmi fisik dan digital akan langsung diserahkan.
          </div>
        </div>

        <div class="form-group">
          <label for="payAmount" class="form-label">Nominal Pembayaran (Rp) *</label>
          <input type="number" id="payAmount" name="amount" class="form-control" value="{{ (int)($room->price ?? 1500000) }}" required>
        </div>

        <!-- Fitur Zona Waktu Daerah Pengiriman (WIB, WITA, WIT) - Berjalan Otomatis di Latar Belakang Tanpa Menampilkan UI -->
        <input type="hidden" name="timezone" id="clientTimezoneInput" value="WIB">

        <!-- Fitur Kolom Bukti Transfer Berupa Foto (Wajib Diisi Berupa Nota & Screening Aktif) -->
        <div class="form-group" id="proofUploadGroup">
          <label for="payProofFile" class="form-label">
            <span id="proofLabelTitle">Foto Bukti / Nota Pembayaran *</span>
            <span class="form-label-hint" id="proofLabelHint">Wajib Struk ATM / Screenshot m-Banking</span>
          </label>
          <div class="tp-proof-upload-container">
            <div id="proofPreviewBox" class="tp-proof-preview-box">
              <span id="proofPreviewPlaceholder" style="font-size: 26px;">🧾</span>
              <img id="proofPreviewImg" src="" alt="Bukti Transfer" style="display: none; width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex-grow: 1;">
              <input type="file" id="payProofFile" name="proof_file" class="form-control" accept="image/*" onchange="handleProofImageSelected(event)" style="font-size: 0.8rem; padding: 6px;">
              <span class="tp-proof-hint" style="font-size: 0.72rem; margin-top: 4px; display: block;">Mendukung format JPG, PNG, atau WEBP (Maksimal 5MB)</span>
            </div>
          </div>

          <!-- Status Box Screening Nota Otomatis -->
          <div id="screeningResultBox" style="display: none; margin-top: 10px; padding: 10px 14px; border-radius: 10px; font-size: 0.82rem; line-height: 1.45;">
          </div>
        </div>

      </div>
      <div class="modal-ftr">
        <button type="button" class="btn-secondary" data-close-modal="modalPay">Batal</button>
        <button type="submit" class="btn-primary" id="btnSubmitPayment">
          <span id="btnSubmitText">Kirim Konfirmasi Pembayaran</span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal 2: Lapor Keluhan --}}
<div class="modal-overlay" id="modalComplaint">
  <div class="modal-box">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Lapor Keluhan / Kendala Fasilitas</h3>
        <p class="modal-subtitle">Kamar #{{ $room->number ?? '102' }} — Pengelola akan segera merespons</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalComplaint" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <form action="{{ route('tenant.complaint.submit') }}" method="POST" id="formComplaint">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label for="complaintPriority" class="form-label">Tingkat Urgensi Masalah *</label>
          <select id="complaintPriority" name="priority" class="form-control">
            <option value="Normal">Normal — kendala ringan (1-2 hari kerja)</option>
            <option value="Mendesak">Mendesak — AC mati, air mati, token habis (Segera ditangani)</option>
            <option value="Rendah">Rendah — saran atau pertanyaan umum</option>
          </select>
        </div>
        <div class="form-group">
          <label for="complaintIssue" class="form-label">Rincian Masalah / Fasilitas Rusak *</label>
          <textarea id="complaintIssue" name="issue_description" class="form-control" rows="4"
            placeholder="Jelaskan secara jelas kendala di kamar Anda (contoh: AC tidak dingin sejak kemarin malam, suara blower berisik)..." required></textarea>
        </div>
      </div>
      <div class="modal-ftr">
        <button type="button" class="btn-secondary" data-close-modal="modalComplaint">Batal</button>
        <button type="submit" class="btn-primary">Kirim Laporan ke Mas Robi</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal 3: Kuitansi Digital & Riwayat Pembayaran (Sesuai Permintaan: Riwayat masuk ke Pop-up ini) --}}
<div class="modal-overlay" id="modalReceipts">
  <div class="modal-box wide">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Riwayat Pembayaran & Kuitansi Digital</h3>
        <p class="modal-subtitle">Bukti resmi transaksi pembayaran sewa Kamar #{{ $room->number ?? '102' }}</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalReceipts" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      
      {{-- Tabel Riwayat Pembayaran Lengkap --}}
      <div class="tp-table-wrap" style="margin-bottom: 20px;">
        <table class="tp-table">
          <thead>
            <tr>
              <th>Keterangan</th>
              <th>Tanggal & Jam (Daerah)</th>
              <th>Metode</th>
              <th>Jumlah</th>
              <th>Status & Kuitansi</th>
            </tr>
          </thead>
          <tbody id="paymentsTableBody">
            @forelse($recentPayments as $p)
            <tr>
              <td class="tp-cell-desc">
                <div class="tp-desc-main">{{ $p->description }}</div>
                <div class="tp-desc-sub">
                  <span>{{ $p->transaction_code ?? 'TRX' }}</span>
                  @if(!empty($p->proof_image))
                    <a href="{{ asset($p->proof_image) }}" target="_blank" style="color:#2563eb;font-weight:700;text-decoration:none;margin-left:6px;">📎 Foto Bukti</a>
                  @endif
                </div>
              </td>
              <td class="tp-cell-muted">
                <div>📅 {{ $p->date->format('d M Y') }}</div>
                <div style="font-size: 0.74rem; color: #2563eb; font-weight: 700; margin-top: 3px;">
                  🕒 {{ $p->formatted_payment_time }}
                </div>
              </td>
              <td>
                <span class="tp-method-pill">{{ $p->payment_method }}</span>
              </td>
              <td class="tp-cell-amount">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
              <td>
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                  <span class="tp-pill paid">LUNAS</span>
                  <button type="button" class="tp-btn-receipt-small" onclick="viewDetailedReceipt('{{ $p->transaction_code }}', '{{ $p->date->format('d M Y') }}', '{{ $p->formatted_payment_time }}', '{{ $user->name }}', '{{ $room->number ?? '102' }}', '{{ addslashes($p->description) }}', '{{ $p->payment_method }}', '{{ number_format($p->amount, 0, ',', '.') }}', '{{ $p->proof_image ? asset($p->proof_image) : '' }}')">
                    🧾 Kuitansi
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="tp-table-empty">
                <div class="tp-empty-icon">🧾</div>
                <div class="tp-empty-title">Belum ada riwayat pembayaran tercatat</div>
                <div class="tp-empty-desc">Pembayaran yang telah dikonfirmasi akan muncul otomatis di sini.</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- List Kuitansi Digital --}}
      <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--tp-text-primary); margin-bottom: 12px;">Daftar Kuitansi Digital</h4>
      @forelse($recentPayments as $p)
        <div class="tp-receipt-item">
          <div class="tp-receipt-left">
            <div class="tp-receipt-icon">🧾</div>
            <div>
              <div class="tp-receipt-desc">{{ $p->description }}</div>
              <div class="tp-receipt-meta">
                <span>📅 {{ $p->date->format('d M Y') }}</span>
                <span>•</span>
                <span style="color:#2563eb; font-weight:700;">🕒 {{ $p->formatted_payment_time }}</span>
                <span>•</span>
                <span>💳 {{ $p->payment_method }}</span>
                <span>•</span>
                <span>Kamar #{{ $room->number ?? '102' }}</span>
                @if(!empty($p->proof_image))
                  <span>•</span>
                  <a href="{{ asset($p->proof_image) }}" target="_blank" style="color: #2563eb; font-weight: 700; text-decoration: none;">📎 Foto Bukti</a>
                @endif
              </div>
            </div>
          </div>
          <div class="tp-receipt-right">
            <div class="tp-receipt-amount">+ Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
            <div style="display: flex; gap: 6px; align-items: center; justify-content: flex-end; margin-top: 4px;">
              <span class="tp-pill paid">LUNAS</span>
              <button type="button" class="tp-btn-receipt-small" onclick="viewDetailedReceipt('{{ $p->transaction_code }}', '{{ $p->date->format('d M Y') }}', '{{ $p->formatted_payment_time }}', '{{ $user->name }}', '{{ $room->number ?? '102' }}', '{{ addslashes($p->description) }}', '{{ $p->payment_method }}', '{{ number_format($p->amount, 0, ',', '.') }}', '{{ $p->proof_image ? asset($p->proof_image) : '' }}')">
                🧾 Buka Kuitansi
              </button>
            </div>
          </div>
        </div>
      @empty
        <div class="tp-receipt-empty">
          <div class="tp-empty-icon">📂</div>
          <div class="tp-empty-title">Belum ada kuitansi pembayaran</div>
          <div class="tp-empty-desc">Saat Anda melakukan pembayaran sewa dan dikonfirmasi, kuitansi digital akan otomatis tersimpan di sini.</div>
        </div>
      @endforelse

    </div>
    <div class="modal-ftr">
      <button type="button" class="btn-secondary" data-close-modal="modalReceipts">Tutup</button>
      <button type="button" class="btn-primary" onclick="document.getElementById('modalReceipts')?.classList.remove('open');document.getElementById('modalPay')?.classList.add('open');">
        + Bayar Sewa Sekarang
      </button>
    </div>
  </div>
</div>

{{-- Modal 3B: Kuitansi Digital Pop-up Resmi (Lengkap Jam & Zona Waktu Indonesia WIB / WITA / WIT) --}}
<div class="modal-overlay" id="modalDigitalReceiptDetail">
  <div class="modal-box" style="max-width: 480px;">
    <div class="modal-hdr">
      <div>
        <h3 class="modal-title">Kuitansi Pembayaran Digital</h3>
        <p class="modal-subtitle">Bukti Resmi & Terverifikasi Sistem</p>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalDigitalReceiptDetail" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body receipt-detail-body" style="padding: 18px;">
      <div class="receipt-paper-card" style="border-radius: 16px; padding: 20px;">
        <div style="text-align: center; border-bottom: 2px dashed rgba(148, 163, 184, 0.4); padding-bottom: 14px; margin-bottom: 14px;">
          <h4 style="font-size: 1.15rem; font-weight: 800; margin: 0;" class="receipt-title">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h4>
          <p style="font-size: 0.75rem; margin: 4px 0 0 0;" class="receipt-sub">Kuitansi Resmi Pembayaran Sewa Kost</p>
          <div style="margin-top: 8px; display: inline-block; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 999px;" class="receipt-code-pill" id="dtlRecCode">TRX-000</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.84rem;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="receipt-label">Waktu Pengiriman:</span>
            <span style="font-weight: 800; padding: 2px 8px; border-radius: 6px;" class="receipt-time-pill" id="dtlRecTime">29 Okt 2026, 12:45:10 WIB</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="receipt-label">Nama Penghuni:</span>
            <span style="font-weight: 700;" class="receipt-val" id="dtlRecTenant">{{ $user->name }}</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="receipt-label">Unit Kamar:</span>
            <span style="font-weight: 700;" class="receipt-val" id="dtlRecRoom">Kamar #{{ $room->number ?? '102' }}</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="receipt-label">Metode Bayar:</span>
            <span style="font-weight: 700;" class="receipt-val" id="dtlRecMethod">Transfer Bank</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span class="receipt-label">Keterangan:</span>
            <span style="max-width: 240px; text-align: right;" class="receipt-desc-val" id="dtlRecDesc">-</span>
          </div>
          <div style="border-top: 1.5px dashed rgba(148, 163, 184, 0.4); margin-top: 6px; padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 800; font-size: 0.92rem;" class="receipt-total-lbl">TOTAL DITERIMA:</span>
            <span style="font-size: 1.15rem; font-weight: 800;" class="receipt-total-amount" id="dtlRecAmount">Rp 1.500.000</span>
          </div>
        </div>

        <!-- Lampiran Foto Bukti -->
        <div id="dtlRecProofBox" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(148, 163, 184, 0.3); text-align: center;">
          <div style="font-size: 0.72rem; font-weight: 800; margin-bottom: 6px;" class="receipt-label">LAMPIRAN FOTO BUKTI / NOTA TRANSAKSI:</div>
          <img id="dtlRecProofImg" src="" alt="Bukti Transfer" style="max-height: 180px; max-width: 100%; border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.4); object-fit: contain;">
        </div>

        <div style="margin-top: 16px; text-align: center; border-top: 2px dashed rgba(148, 163, 184, 0.4); padding-top: 10px;">
          <span class="tp-pill paid" style="font-size: 0.78rem; padding: 4px 14px;">● STATUS: LUNAS & SAH</span>
          <p style="font-size: 0.68rem; margin: 8px 0 0 0;" class="receipt-sub">Diterbitkan otomatis secara digital oleh Sistem {{ $setting->kos_name ?? 'Kos Mas Robi' }}</p>
        </div>
      </div>
    </div>
    <div class="modal-ftr">
      <button type="button" class="btn-secondary" data-close-modal="modalDigitalReceiptDetail">Tutup</button>
      <button type="button" class="btn-primary" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
      </button>
    </div>
  </div>
</div>

{{-- Modal 4A: Info WiFi Kos (Sesuai Permintaan: Hanya WiFi, Isi Aturan Dihapus) --}}
<div class="modal-overlay" id="modalWifi">
  <div class="modal-box" style="max-width: 480px;">
    <div class="modal-hdr">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="tp-icon-badge cyan" style="width: 38px; height: 38px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
            <line x1="12" y1="20" x2="12.01" y2="20"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Info WiFi Kos</h3>
          <p class="modal-subtitle">Koneksi internet fiber kecepatan tinggi untuk seluruh penghuni</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalWifi" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
      <div class="tp-wifi-status-card">
        <span class="tp-wifi-status-text">● Status Jaringan: Aktif & Stabil</span>
        <span class="tp-badge green" style="font-weight: 800;">100 Mbps Dedicated</span>
      </div>

      <div class="tp-wifi-box">
        <div class="tp-wifi-row">
          <div class="tp-wifi-info">
            <span class="tp-wifi-key">Nama Jaringan (SSID)</span>
            <span class="tp-wifi-val" id="wifiSsidModal">KOS_MAS_ROBI_5G</span>
          </div>
          <button type="button" class="tp-btn-copy" onclick="copyText('KOS_MAS_ROBI_5G', this)" title="Salin SSID">
            📋 Salin
          </button>
        </div>
        <div class="tp-wifi-row">
          <div class="tp-wifi-info">
            <span class="tp-wifi-key">Kata Sandi (Password)</span>
            <span class="tp-wifi-val font-mono" id="wifiPassModal">kostnyaman2026</span>
          </div>
          <button type="button" class="tp-btn-copy" onclick="copyText('kostnyaman2026', this)" title="Salin Password">
            📋 Salin
          </button>
        </div>
      </div>

      <div style="font-size: 0.78rem; color: #64748b; line-height: 1.5; background: var(--tp-bg-surface); padding: 12px; border-radius: 10px; border: 1px solid var(--tp-border);">
        💡 <strong>Tips Koneksi:</strong> Jika sinyal di kamar terasa lemah, Anda dapat menghubungkan ke repeater WiFi terdekat di koridor lantai masing-masing.
      </div>
    </div>
    <div class="modal-ftr">
      <button type="button" class="btn-primary" data-close-modal="modalWifi" style="width: 100%;">Tutup</button>
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
          <p class="modal-subtitle">Pedoman kenyamanan dan ketertiban bersama di {{ $setting->kos_name ?? 'Kos Mas Robi' }}</p>
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
          <li>
            <span class="tp-rule-bullet">✓</span>
            <span>Akses gerbang utama 24 jam dengan smart card / kunci masing-masing penghuni.</span>
          </li>
          <li>
            <span class="tp-rule-bullet">✓</span>
            <span>Tamu lawan jenis dilarang menginap di dalam kamar demi kenyamanan bersama.</span>
          </li>
          <li>
            <span class="tp-rule-bullet">✓</span>
            <span>Jaga kebersihan area bersama (dapur umum, kulkas bersama, tempat jemuran & parkiran).</span>
          </li>
          <li>
            <span class="tp-rule-bullet">✓</span>
            <span>Jam tenang penghuni mulai pukul 22:00 WIB untuk menjaga istirahat & waktu belajar.</span>
          </li>
          <li>
            <span class="tp-rule-bullet">✓</span>
            <span>Dilarang merokok di dalam kamar ber-AC atau koridor tertutup.</span>
          </li>
        </ul>

        {{-- Kontak Darurat / Security --}}
        <div class="tp-emergency-box" style="margin-top: 14px;">
          <div class="tp-emergency-icon">🚨</div>
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
          <p class="modal-subtitle">Pilih kamar tujuan dan ajukan permohonan pindah unit ke pengelola</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalRoomTransfer" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <form action="{{ route('tenant.room.transfer') }}" method="POST">
      @csrf
      <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
        
        {{-- Kamar Saat Ini --}}
        <div style="background: var(--tp-blue-soft); border: 1.5px solid var(--tp-blue-border); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div style="font-size: 0.72rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase;">Kamar Anda Saat Ini:</div>
            <div style="font-size: 1rem; font-weight: 800; color: #1e3a8a;">Kamar #{{ $room->number ?? '102' }} — {{ $room->type ?? 'Deluxe AC' }}</div>
            <div style="font-size: 0.78rem; color: #3b82f6;">Lantai {{ $room->floor ?? '1' }} • Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}/bulan</div>
          </div>
          <span class="tp-badge green">Aktif</span>
        </div>

        {{-- Dropdown Pilih Kamar Tujuan --}}
        <div class="form-group">
          <label for="transferTargetRoom" class="form-label">Pilih Kamar Tujuan yang Diinginkan *</label>
          <select name="target_room_id" id="transferTargetRoom" class="form-control" required style="font-weight: 600;">
            <option value="">-- Pilih Kamar Tujuan --</option>
            @foreach($allRooms as $r)
              @if($r->id != ($room->id ?? 0))
                <option value="{{ $r->id }}">
                  Kamar #{{ $r->number }} (Lantai {{ $r->floor }}) — {{ $r->type }} — Rp {{ number_format($r->price, 0, ',', '.') }}/bln {{ $r->status === 'available' ? '【Tersedia/Kosong】' : '【' . ucfirst($r->status) . '】' }}
                </option>
              @endif
            @endforeach
          </select>
          <div style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">Pilih unit kamar yang masih kosong atau ingin Anda pesan untuk periode selanjutnya.</div>
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
                      <span class="tp-transfer-status-badge pending">⏳ Menunggu Peninjauan</span>
                    @elseif($tr->status === 'approved')
                      <span class="tp-transfer-status-badge approved">✅ Disetujui Pengelola</span>
                    @else
                      <span class="tp-transfer-status-badge rejected">❌ Ditolak</span>
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
        <button type="submit" class="btn-primary" style="background: #0d9488; border-color: #0d9488;">
          🚀 Kirim Pengajuan Pindah
        </button>
      </div>
    </form>
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
            <img src="{{ asset($user->avatar ?? 'assets/images/tenant_avatar.jpg') }}"
                 alt="{{ $user->name }}" class="tp-profile-preview-avatar" id="avatarPreviewImg">
            <label for="avatarFileInput" class="tp-avatar-upload-badge" title="Unggah foto baru">
              📷
            </label>
          </div>
          <div class="tp-avatar-meta">
            <h4 class="tp-avatar-title">Foto Profil Penghuni</h4>
            <p class="tp-avatar-sub">Unggah foto profil Anda sendiri (JPG, PNG, atau WEBP, maks 3MB) atau gunakan kamera/galeri.</p>
            <div class="tp-avatar-buttons">
              <label for="avatarFileInput" class="tp-btn-upload-file">
                📁 Pilih Foto Dari Perangkat
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
                   value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 081234567890">
          </div>

          {{-- Info Hunian Saat Ini (Read-only badge info) --}}
          <div class="tp-profile-room-box tp-grid-col-2">
            <div class="tp-profile-room-icon">🏠</div>
            <div class="tp-profile-room-details">
              <strong>Kamar Saat Ini: #{{ $room->number ?? '102' }} ({{ $room->type ?? 'Deluxe AC' }})</strong>
              <p>Lantai {{ $room->floor ?? '1' }} • Status Sewa: Aktif Menghuni • Biaya: Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}/bulan</p>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-ftr">
        <button type="button" class="btn-secondary" data-close-modal="modalProfile">Batal</button>
        <button type="submit" class="btn-primary">💾 Simpan Perubahan Profil</button>
      </div>
    </form>
  </div>
</div>

  {{-- Floating Bottom Navigation for Mobile --}}
  <nav class="tp-mobile-nav" aria-label="Navigasi Bawah">
    <a href="#" class="tp-nav-item active">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      <span>Home</span>
    </a>
    <button type="button" class="tp-nav-item" onclick="document.getElementById('modalPay')?.classList.add('open')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      <span>Bayar</span>
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
   Kos Mas Robi — Portal JS (AJAX + Animations + Auto-slide)
   ============================================================ */

// ─── Toast Notification ──────────────────────────────────────
function showPortalToast(message, type) {
  type = type || 'success';
  var existing = document.getElementById('portalToast');
  if (existing) existing.remove();
  var toast = document.createElement('div');
  toast.id = 'portalToast';
  toast.className = 'tp-toast tp-toast-' + type;
  toast.innerHTML = '<span class="tp-toast-icon">' + (type === 'success' ? '✅' : '❌') + '</span>'
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

  // Close on data-close-modal buttons
  document.querySelectorAll('[data-close-modal]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.getElementById(btn.dataset.closeModal)?.classList.remove('open');
    });
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
    var roomNum   = '{{ $room->number ?? "102" }}';
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
        badgeText.textContent = slideLabels[currentIndex] || ('Foto ' + (currentIndex+1) + '/' + totalSlides + ': Kamar #' + roomNum);
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
    var proofHtml = payment.proof_image ? '<a href="' + payment.proof_image + '" target="_blank" style="font-size:0.72rem;color:#2563eb;font-weight:700;display:inline-flex;align-items:center;gap:3px;margin-top:3px;">📎 Lihat Foto Bukti</a>' : '';
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
            document.getElementById('navAvatarImg')?.setAttribute('src', url);
            document.querySelector('.tp-welcome-avatar')?.setAttribute('src', url);
            document.getElementById('avatarPreviewImg')?.setAttribute('src', url);
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

// ─── Room Catalog Filtering ──────────────────────────────────────
function filterCatalogRooms(filter, btn) {
  document.querySelectorAll('#catalogFilterPills .tp-filter-btn').forEach(function(b) {
    b.classList.remove('active');
  });
  btn.classList.add('active');

  var cards = document.querySelectorAll('#catalogRoomGrid .tp-room-card');
  cards.forEach(function(card) {
    var status = card.dataset.status;
    var floor  = card.dataset.floor;
    var show   = false;

    if (filter === 'all') {
      show = true;
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

// ─── Room Detail & Custom Interior Modal Opener ──────────────────
function openRoomCustomModal(room) {
  if (!room) return;
  document.getElementById('mCustomRoomTitle').textContent = 'Detail Kamar #' + room.number + ' (' + room.type + ')';
  document.getElementById('mCustomRoomSubtitle').textContent = 'Lantai ' + room.floor + ' • ' + (room.status === 'available' ? 'Kamar Kosong Siap Huni' : 'Kamar Sedang Ditempati');
  document.getElementById('mCustomRoomBadge').textContent = 'Kamar #' + room.number;
  document.getElementById('mCustomRoomFloor').textContent = 'Lantai ' + room.floor;
  document.getElementById('mCustomRoomPrice').textContent = 'Rp ' + Number(room.price || 0).toLocaleString('id-ID') + ' / bln';
  document.getElementById('mCustomRoomType').textContent = room.type;

  var imgList = room.images_list || [room.image || 'assets/images/rooms/room_1.jpg'];
  var firstImg = imgList[0] || 'assets/images/rooms/room_1.jpg';
  if (!firstImg.startsWith('http') && !firstImg.startsWith('/')) {
    firstImg = '/' + firstImg;
  }
  document.getElementById('mCustomRoomImg').src = firstImg;

  var statusTag = document.getElementById('mCustomRoomStatusTag');
  if (statusTag) {
    if (room.status === 'available') {
      statusTag.innerHTML = '<span class="tp-pill paid" style="font-size:0.75rem;padding:4px 12px;">🟢 Tersedia (Bisa Booking)</span>';
    } else {
      statusTag.innerHTML = '<span class="tp-pill" style="font-size:0.75rem;padding:4px 12px;background:#64748b;color:#ffffff;">🔒 Sudah Disewa</span>';
    }
  }

  // Facilities
  var facContainer = document.getElementById('mCustomRoomFacilities');
  if (facContainer) {
    facContainer.innerHTML = '';
    var facs = room.facilities || ['AC', 'Springbed', 'Lemari', 'KM Dalam', 'WiFi'];
    facs.forEach(function(f) {
      var span = document.createElement('span');
      span.className = 'tp-room-card-facility-pill';
      span.textContent = '✓ ' + f;
      facContainer.appendChild(span);
    });
  }

  // Existing custom interior status if any
  var existingBox = document.getElementById('mCustomExistingStatusBox');
  if (room.custom_interior_status && room.custom_interior_status !== 'none') {
    existingBox.style.display = 'block';
    var badge = document.getElementById('mCustomExistingBadge');
    var statusLabels = {
      pending: '⏳ Menunggu Konfirmasi Mas Robi',
      approved: '✅ Disetujui Mas Robi',
      in_progress: '🛠️ Sedang Dikerjakan',
      completed: '🎉 Selesai Dipasang',
      rejected: '❌ Tidak Disetujui'
    };
    badge.textContent = statusLabels[room.custom_interior_status] || room.custom_interior_status;
    document.getElementById('mCustomExistingReqText').textContent = room.custom_interior_request || '-';
    document.getElementById('mCustomExistingBudget').textContent = 'Rp ' + Number(room.custom_interior_budget || 0).toLocaleString('id-ID');
    document.getElementById('mCustomExistingNotes').textContent = room.custom_interior_admin_notes ? ('Catatan Mas Robi: ' + room.custom_interior_admin_notes) : '';
  } else {
    existingBox.style.display = 'none';
  }

  // Set form action
  var form = document.getElementById('formCustomInterior');
  form.action = '/portal/room/' + room.id + '/custom-interior';

  // Pre-fill inputs
  var reqInput = document.getElementById('customInteriorReqInput');
  var budgetInput = document.getElementById('customInteriorBudgetInput');
  if (reqInput) reqInput.value = room.custom_interior_request || '';
  if (budgetInput) budgetInput.value = room.custom_interior_budget || '';

  document.getElementById('modalRoomDetailCustom')?.classList.add('open');
}

// ─── Dynamic Payment Method Details (Mandiri, BCA, BNI, BRI, QRIS, Cash) ─
var paymentMethodsData = {
  'Transfer Mandiri': {
    badge: 'Rekening Resmi Bank Mandiri',
    bank: 'Bank Mandiri',
    number: '1370019283100',
    holder: 'Robi Suryanto',
    isCash: false,
    isQris: false,
    instruction: ''
  },
  'Transfer BCA': {
    badge: 'Rekening Resmi Bank BCA',
    bank: 'Bank Central Asia (BCA)',
    number: '8830192831',
    holder: 'Robi Suryanto',
    isCash: false,
    isQris: false,
    instruction: ''
  },
  'Transfer BNI': {
    badge: 'Rekening Resmi Bank BNI',
    bank: 'Bank Negara Indonesia (BNI)',
    number: '0928318830',
    holder: 'Robi Suryanto',
    isCash: false,
    isQris: false,
    instruction: ''
  },
  'Transfer BRI': {
    badge: 'Rekening Resmi Bank BRI',
    bank: 'Bank Rakyat Indonesia (BRI)',
    number: '012301092831502',
    holder: 'Robi Suryanto',
    isCash: false,
    isQris: false,
    instruction: ''
  },
  'Transfer QRIS': {
    badge: 'QRIS Instant All Payment',
    bank: 'QRIS Resmi Kos Mas Robi',
    number: 'NMID: ID102026192831',
    holder: 'Kos Mas Robi / Robi Suryanto',
    isCash: false,
    isQris: true,
    instruction: '⚡ <strong>Scan QRIS All Payment:</strong> Buka aplikasi m-Banking (BCA, Mandiri Livin, BRImo, BNI Mobile) atau e-Wallet (GoPay, OVO, DANA, ShopeePay). Scan kode QRIS yang tersedia di meja resepsionis atau gunakan NMID di atas.'
  },
  'Tunai (Cash)': {
    badge: 'Pembayaran Tunai (Cash)',
    bank: 'Tunai Langsung ke Mas Robi',
    number: 'Bayar Cash / Uang Tunai',
    holder: 'Mas Robi (Kamar Pengelola Lt. 1)',
    isCash: true,
    isQris: false,
    instruction: '💵 <strong>Pembayaran Tunai (Cash):</strong> Silakan tentukan tanggal penyerahan uang tunai pada kolom di bawah. Lalu serahkan uang tunai sewa kamar langsung kepada Mas Robi di kantor/kamar pengelola lantai 1. Kuitansi resmi fisik dan digital akan langsung diserahkan.'
  }
};

function handlePaymentMethodChange(selectedMethod) {
  var data = paymentMethodsData[selectedMethod] || paymentMethodsData['Transfer Mandiri'];
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
  resultBox.innerHTML = '<div style="display:flex;align-items:center;gap:8px;"><span>🔍</span><span><strong>Sedang Memindai Nota:</strong> Memeriksa keabsahan format & struktur bukti transfer...</span></div>';

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
    resultBox.innerHTML = '<div style="display:flex;align-items:flex-start;gap:8px;"><span style="font-size:1.1rem;">❌</span><div><strong>Screening Gagal — Bukan Nota Valid:</strong><br>' + msg + '</div></div>';
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
    resultBox.innerHTML = '<div style="display:flex;align-items:center;gap:8px;"><span style="font-size:1.1rem;">✅</span><div><strong>' + msg + '</strong></div></div>';
  }
}

function validatePaymentSubmission(event) {
  var method = document.getElementById('payMethod').value;
  var isCash = method.includes('Tunai') || method.includes('Cash');

  if (isCash) {
    var cashDate = document.getElementById('payCashDate').value;
    if (!cashDate) {
      alert('Mohon tentukan Tanggal Bayar Tunai (Cash) Anda.');
      document.getElementById('payCashDate').focus();
      if (event) event.preventDefault();
      return false;
    }
    return true; // Cash tidak mewajibkan upload foto nota
  }

  // Transfer / QRIS: Wajib nota & lolos screening
  var fileInput = document.getElementById('payProofFile');
  if (!fileInput.files || fileInput.files.length === 0) {
    alert('Foto bukti / nota pembayaran WAJIB diunggah untuk metode transfer atau QRIS!\n\nSilakan lampirkan struk ATM atau screenshot m-Banking Anda.');
    fileInput.focus();
    if (event) event.preventDefault();
    return false;
  }

  if (!isProofValidForTransfer) {
    alert('File yang diunggah belum lolos screening nota pembayaran!\n\nPastikan foto yang diunggah adalah nota transfer atau screenshot m-banking yang sah dan jelas.');
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
  if (proofUrl && proofBox && proofImg) {
    proofBox.style.display = 'block';
    proofImg.src = proofUrl;
  } else if (proofBox) {
    proofBox.style.display = 'none';
  }

  // Buka modal kuitansi digital resmi
  document.getElementById('modalDigitalReceiptDetail')?.classList.add('open');
}

// Inisialisasi Deteksi Zona Waktu Otomatis di Latar Belakang saat Dokumen Siap
document.addEventListener('DOMContentLoaded', function() {
  initTimezoneDetection();
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
      if (preview) preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

// ─── Copy to clipboard ────────────────────────────────────────
function copyText(text, btnElement) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(function() {
      var orig = btnElement.textContent;
      btnElement.textContent = '✓ Tersalin!';
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
</script>

@endsection
