@extends('layouts.portal')

@section('title', 'Portal Anak Kos — ' . ($setting->kos_name ?? 'Kos Mas Robi'))

@section('content')

  @php
    $hasRealAvatar = !empty($user->avatar) && !str_contains($user->avatar, 'tenant_avatar.jpg');
    $userInitial = strtoupper(substr($user->name ?? 'P', 0, 1));
    $isB2 = ($tenantBranchKey ?? 'lokasi_1') === 'lokasi_2';
    $b1NamePortal = $branches['lokasi_1']['name'] ?? ($setting->branches['lokasi_1']['name'] ?? 'Kos Mas Robi 1 - Kampus Utama');
    $b2NamePortal = $branches['lokasi_2']['name'] ?? ($setting->branches['lokasi_2']['name'] ?? 'Kos Mas Robi 2 - Ringroad Exclusive');
    $branchName = $isB2 ? $b2NamePortal : $b1NamePortal;
    $branchAddr = $isB2 ? ($branches['lokasi_2']['address'] ?? 'Jl. Kaliurang KM 12, Sardonoharjo, Sleman') : ($branches['lokasi_1']['address'] ?? 'Jl. Pandega Marta, Pogung, Sinduadi (3 Mnt UGM)');
    $branchMaps = $isB2 ? ($branches['lokasi_2']['gmaps_url'] ?? 'https://maps.google.com/?q=Kos+Mas+Robi+Ringroad') : ($branches['lokasi_1']['gmaps_url'] ?? 'https://maps.google.com/?q=Kos+Mas+Robi+Kampus');
  @endphp

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
               alt="{{ $setting->kos_name ?? 'Kos Mas Robi' }}"
               onerror="this.src='{{ asset('assets/images/kos_exterior.jpg') }}'"
               class="tp-hero-landing-photo">
        </div>

        {{-- Left Content: Dynamic Typography from Admin Landing Page Settings --}}
        <div class="tp-hero-landing-content">
          <div class="tp-hero-landing-tag">
            <span>{{ $landing['bento_card_tag'] ?? 'Kos Eksklusif & Nyaman' }}</span>
          </div>
          <h1 class="tp-hero-landing-title">{!! nl2br(e($heroTitle)) !!}</h1>
          <p class="tp-hero-landing-desc">{{ $heroDesc }}</p>

          <div class="tp-hero-landing-actions">
            <button type="button" class="tp-btn-coral" onclick="document.getElementById('catalogRoomGrid')?.scrollIntoView({ behavior: 'smooth' })">
              <span>{{ $heroBtnText }}</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <a href="#sectionBranchSelector" class="tp-btn-ghost-white">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span>Pilih Titik Lokasi</span>
            </a>
          </div>
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
        @foreach($allRooms as $r)
          @php
            $isMyRoom = ($room && $room->id === $r->id);
            $rThumb = !empty($r->images_list[0]) ? asset($r->images_list[0]) : asset('assets/images/rooms/room_1.jpg');
            $statusClass = $isMyRoom ? 'my-room' : ($r->status === 'available' ? 'available' : 'occupied');
            $statusText = $isMyRoom ? 'Kamar Anda' : ($r->status === 'available' ? 'Tersedia' : 'Sudah Disewa');
            $hasCustomInterior = $r->custom_interior_status && $r->custom_interior_status !== 'none';
          @endphp
          <div class="tp-room-card room-item"
               data-status="{{ $r->status }}"
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
                @else
                  <button type="button" class="tp-room-card-action-btn" onclick="event.stopPropagation(); handleRoomCardClick({{ $r->id }})">
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
              <div class="tp-welcome-avatar tp-welcome-avatar-initial" id="welcomeAvatarInitial" style="display:none;">{{ $userInitial }}</div>
            @else
              <img src=""
                   alt="{{ $user->name }}" class="tp-welcome-avatar" id="welcomeAvatarImg" style="display:none;">
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

      {{-- 2. SHOWCASE SECTION (Desktop: Left Gallery Slider, Right Room & Billing Card) --}}
      @php
        $roomImages = $room->images_list ?? [
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
            <span id="heroSlideBadgeText">Foto 1/{{ count($roomImages) }}: Kamar #{{ $room->number ?? '102' }}</span>
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
                <span class="tp-badge-category">Hunian Aktif Anda</span>
                <h2 class="tp-details-title">Kamar #{{ $room->number ?? '102' }} • {{ $room->type ?? 'Deluxe AC' }}</h2>
              </div>
              <span class="tp-status-pill {{ ($room->status ?? '') === 'due' ? 'due' : 'paid' }}">
                @if(($room->status ?? '') === 'due')
                  Menunggu Bayar
                @else
                  ● Sewa Aktif
                @endif
              </span>
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
              <div class="tp-price-main">
                <span class="tp-price-lbl">Tagihan Sewa Bulanan</span>
                <div class="tp-price-val">
                  Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}
                  <span class="tp-price-cycle">/bulan</span>
                </div>
              </div>
              <div class="tp-due-badge">
                <span class="tp-due-lbl">Jatuh Tempo Sewa</span>
                <span class="tp-due-date">{{ $dueDate->translatedFormat('d F Y') }}</span>
              </div>
            </div>

            {{-- Description --}}
            <div class="tp-desc-block">
              <h4 class="tp-desc-heading">Spesifikasi & Kenyamanan Unit</h4>
              <p class="tp-desc-body">
                Unit kamar tipe {{ $room->type ?? 'Deluxe AC' }} di Lantai {{ $room->floor ?? '1' }} ({{ $branchName }}). Dilengkapi pendingin ruangan (AC), kamar mandi dalam pribadi, meja kerja ergonomis, lemari pakaian, serta koneksi internet WiFi cepat.
              </p>
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
            <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin%20Kos%20Mas%20Robi,%20saya%20penghuni%20Kamar%20%23{{ $room->number ?? '102' }}%20ingin%20konfirmasi%20sewa"
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
            <span class="tp-badge blue">Kamar #{{ $room->number ?? '102' }}</span>
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
                <div class="tp-roominfo-val">Kamar #{{ $room->number ?? '102' }} (Lt. {{ $room->floor ?? '1' }})</div>
                <div class="tp-roominfo-key">Unit & Lantai Kamar</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val">{{ $dueDate->format('d M Y') }}</div>
                <div class="tp-roominfo-key">Jatuh Tempo Berikutnya</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val">Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}</div>
                <div class="tp-roominfo-key">Tarif Sewa / Bulan</div>
              </div>
            </div>
            <div class="tp-roominfo-item">
              <div class="tp-roominfo-ico" style="background: #ecfdf5; color: #059669;">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <div>
                <div class="tp-roominfo-val green">{{ ($room->status ?? '') === 'due' ? 'Menunggu Bayar' : 'Lunas & Aktif' }}</div>
                <div class="tp-roominfo-key">Status Pembayaran</div>
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

          <button type="button" class="tp-quick-tile" id="btnOpenComplaint">
            <div class="tp-quick-ico purple">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
              </svg>
            </div>
            <div>
              <div class="tp-quick-ttl">Lapor Keluhan</div>
              <div class="tp-quick-desc">Laporkan kendala AC, air, lampu, atau perbaikan fasilitas kamar</div>
            </div>
            <div class="tp-quick-footer"><span class="tp-quick-cta-lbl">Buat Laporan</span><span class="tp-quick-arrow">→</span></div>
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

          <button type="button" class="tp-quick-tile" id="btnOpenRoomTransfer">
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
              <div class="tp-quick-ttl">Pindah Kamar</div>
              <div class="tp-quick-desc">Ajukan perpindahan unit kamar atau pindah lintas cabang kos</div>
            </div>
            <div class="tp-quick-footer"><span class="tp-quick-cta-lbl">Ajukan Pindah</span><span class="tp-quick-arrow">→</span></div>
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

      <!-- Custom Interior Request Form -->
      <form id="formCustomInterior" method="POST" action="">
        @csrf
        <div class="tp-custom-interior-card">
          <div class="tp-custom-interior-title">
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
        <button type="submit" class="btn-primary">Kirim Laporan ke Mas Robi</button>
      </div>
    </form>
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
          <p class="modal-subtitle">Bukti resmi transaksi sewa Kamar #{{ $room->number ?? '102' }} yang telah divalidasi sistem</p>
        </div>
      </div>
      <button type="button" class="modal-close" data-close-modal="modalReceipts" aria-label="Tutup Modal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="modal-body tp-receipts-modal-body">
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
            Pengelola kos belum menerbitkan atau mengirimkan kuitansi pembayaran untuk kamar Anda. Kuitansi resmi dan riwayat pembayaran akan otomatis muncul di sini setelah divalidasi dan dikirimkan oleh Admin Kos Mas Robi.
          </p>
        </div>
      @endif
    </div>

    <div class="modal-ftr tp-receipts-modal-ftr">
      <button type="button" class="btn-secondary" data-close-modal="modalReceipts">Tutup</button>
      <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Admin%20Kos%20Mas%20Robi,%20saya%20ingin%20konfirmasi%20kuitansi%20pembayaran%20sewa%20kamar%20%23{{ $room->number ?? '102' }}"
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
          <div class="tp-slip-brand">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
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
          <p>Kuitansi elektronik ini merupakan bukti pembayaran resmi yang sah dari Sistem Manajemen {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Disimpan secara digital dan dapat dicetak sewaktu-waktu.</p>
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
      @php
        $myBranchLoc = ($room->location ?? 'Lokasi 1') === 'Lokasi 2' ? 'Lokasi 2' : 'Lokasi 1';
        $activeWifis = $setting->active_wifi_list ?? [];
        if (empty($activeWifis)) {
            $activeWifis = [
                ['name' => 'KOS_MAS_ROBI_1_5G', 'location' => 'Lokasi 1', 'modem' => 'Modem Fiber Utama (Lantai 1)', 'password' => 'kostnyaman2026', 'speed' => '100 Mbps Dual-Band'],
                ['name' => 'ROBI_EXCLUSIVE_2_ULTRA5G', 'location' => 'Lokasi 2', 'modem' => 'Router Mesh WiFi 6 (Ringroad)', 'password' => 'ringroadvip2026', 'speed' => '300 Mbps Mesh WiFi 6'],
            ];
        }
      @endphp

      <div class="tp-wifi-status-card" style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; border: 1.5px solid #bbf7d0; padding: 10px 14px; border-radius: 12px; flex-wrap: wrap; gap: 8px;">
        <span class="tp-wifi-status-text" style="color: #166534; font-weight: 700; font-size: 0.8rem;">● Status Jaringan: Aktif & Stabil ({{ $myBranchLoc === 'Lokasi 2' ? $b2NamePortal : $b1NamePortal }})</span>
        <span class="tp-badge green" style="background: #dcfce7; color: #15803d; font-weight: 800; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">{{ $myBranchLoc === 'Lokasi 2' ? 'Dedicated 300 Mbps' : 'Fiber 100 Mbps' }}</span>
      </div>

      {{-- Switcher Lokasi WiFi di Portal Pelanggan --}}
      <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <button type="button" class="tp-wf-filter-btn" onclick="filterPortalWifi('all', this)" style="flex: 1; padding: 7px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; border: 1.5px solid #0a5661; background: #0a5661; color: #fff; cursor: pointer;">Semua Cabang</button>
        <button type="button" class="tp-wf-filter-btn" onclick="filterPortalWifi('Lokasi 1', this)" style="flex: 1; padding: 7px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; border: 1.5px solid #cbd5e1; background: #f8fafc; color: #334155; cursor: pointer;">{{ $b1NamePortal }}</button>
        <button type="button" class="tp-wf-filter-btn" onclick="filterPortalWifi('Lokasi 2', this)" style="flex: 1; padding: 7px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; border: 1.5px solid #cbd5e1; background: #f8fafc; color: #334155; cursor: pointer;">{{ $b2NamePortal }}</button>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px;" id="portalWifiCardList">
        @foreach($activeWifis as $idx => $wf)
          @php
            $wfLoc = $wf['location'] ?? 'Lokasi 1';
            $isWfLoc2 = $wfLoc === 'Lokasi 2';
            $wfSpeed = $wf['speed'] ?? ($isWfLoc2 ? '300 Mbps Mesh WiFi 6' : '100 Mbps Dual-Band');
          @endphp
          <div class="portal-wf-card" data-location="{{ $wfLoc }}" style="background: #ffffff; border: 1.5px solid {{ $wfLoc === $myBranchLoc ? '#0d9488' : '#e2e8f0' }}; border-radius: 14px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: all 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $isWfLoc2 ? '#ccfbf1' : '#dcfce7' }}; border: 1px solid {{ $isWfLoc2 ? '#5eead4' : '#86efac' }}; color: {{ $isWfLoc2 ? '#0a5661' : '#059669' }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                </div>
                <div>
                  <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span style="font-size: 0.66rem; font-weight: 800; padding: 2px 7px; border-radius: 999px; background: {{ $isWfLoc2 ? '#ccfbf1' : '#dcfce7' }}; color: {{ $isWfLoc2 ? '#0a5661' : '#059669' }};">
                      {{ $isWfLoc2 ? '' . $b2NamePortal : '' . $b1NamePortal }}
                    </span>
                    <span style="font-size: 0.66rem; font-weight: 700; color: #64748b;">• {{ $wfSpeed }}</span>
                  </div>
                  <div style="font-size: 0.96rem; font-weight: 800; color: #0f172a; letter-spacing: -0.2px; margin-top: 2px;">{{ $wf['name'] ?? 'KOS_MAS_ROBI' }}</div>
                </div>
              </div>
              <button type="button" class="tp-btn-copy" onclick="copyText('{{ $wf['name'] ?? '' }}', this)" title="Salin Nama WiFi" style="background: #f8fafc; border: 1.5px solid #cbd5e1; padding: 5px 10px; border-radius: 8px; font-size: 0.74rem; font-weight: 700; color: #334155; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
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
              {{-- Info Password (Opsional jika ada) --}}
              <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px dashed #e2e8f0; font-size: 0.78rem;">
                <div style="color: #64748b;">
                  <span>Sandi: </span>
                  <code style="font-family: monospace; font-size: 0.84rem; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $wf['password'] }}</code>
                </div>
                <button type="button" onclick="copyText('{{ $wf['password'] }}', this)" style="background: transparent; border: none; color: #0a5661; font-weight: 700; font-size: 0.74rem; cursor: pointer; padding: 2px 6px; border-radius: 4px;">
                  Salin Sandi
                </button>
              </div>
            @endif
          </div>
        @endforeach
      </div>

      <script>
        function filterPortalWifi(loc, btn) {
          document.querySelectorAll('.tp-wf-filter-btn').forEach(function(b) {
            b.style.background = '#f8fafc';
            b.style.color = '#334155';
            b.style.borderColor = '#cbd5e1';
          });
          if (btn) {
            btn.style.background = '#0a5661';
            btn.style.color = '#ffffff';
            btn.style.borderColor = '#0a5661';
          }
          document.querySelectorAll('#portalWifiCardList .portal-wf-card').forEach(function(c) {
            var cLoc = c.getAttribute('data-location') || 'Lokasi 1';
            c.style.display = (loc === 'all' || cLoc === loc) ? 'block' : 'none';
          });
        }
      </script>

      <div style="font-size: 0.76rem; color: #64748b; line-height: 1.5; background: #f8fafc; padding: 10px 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <strong>Tips Koneksi:</strong> Hubungkan perangkat Anda ke WiFi modem terdekat di lantai atau area Anda untuk mendapatkan sinyal terbaik dan terkuat.
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
                  [{{ ($r->location ?? 'Lokasi 1') === 'Lokasi 2' ? 'Cabang 2 - Ringroad' : 'Cabang 1 - Kampus' }}] Kamar #{{ $r->number }} (Lt {{ $r->floor }}) — {{ $r->type }} — Rp {{ number_format($r->price, 0, ',', '.') }}/bln {{ $r->status === 'available' ? '【Tersedia/Kosong】' : '【' . ucfirst($r->status) . '】' }}
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
        <button type="submit" class="btn-primary" style="background: #0d9488; border-color: #0d9488;">
          Kirim Pengajuan Pindah
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
                   value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 081234567890">
          </div>

          {{-- Info Hunian Saat Ini (Read-only badge info) --}}
          <div class="tp-profile-room-box tp-grid-col-2">
            <div class="tp-profile-room-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div class="tp-profile-room-details">
              <strong>Kamar Saat Ini: #{{ $room->number ?? '102' }} ({{ $room->type ?? 'Deluxe AC' }}) • {{ ($tenantBranchKey ?? 'lokasi_1') === 'lokasi_2' ? 'Cabang 2 (Ringroad)' : 'Cabang 1 (Kampus)' }}</strong>
              <p>{{ $tenantBranch['name'] ?? 'Kos Mas Robi' }} • Lantai {{ $room->floor ?? '1' }} • Status Sewa: Aktif Menghuni • Biaya: Rp {{ number_format($room->price ?? 1500000, 0, ',', '.') }}/bulan</p>
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
                    <input type="password" id="inputNewPassword" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required style="font-size: 16px;">
                  </div>
                  <div class="form-group" style="margin-bottom: 0;">
                    <label for="inputNewPasswordConfirm" class="form-label" style="font-size: 0.78rem; font-weight: 700;">
                      <span>Ulangi Password Baru *</span>
                    </label>
                    <input type="password" id="inputNewPasswordConfirm" class="form-control" placeholder="Ketik ulang password" minlength="8" required style="font-size: 16px;">
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
      <div class="modal-ftr">
        <button type="button" class="btn-secondary" data-close-modal="modalProfile">Batal</button>
        <button type="submit" class="btn-primary">Simpan Perubahan Profil</button>
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
   Kos Mas Robi — Portal JS (AJAX + Animations + Auto-slide)
   ============================================================ */

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
              if (el) el.style.display = 'none';
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

  var statusTag = document.getElementById('mCustomRoomStatusTag');
  if (statusTag) {
    if (room.status === 'available') {
      statusTag.innerHTML = '<span class="tp-pill paid" style="font-size:0.75rem;padding:4px 12px;">Tersedia (Bisa Booking)</span>';
    } else {
      statusTag.innerHTML = '<span class="tp-pill" style="font-size:0.75rem;padding:4px 12px;background:#64748b;color:#ffffff;">Sudah Disewa</span>';
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
      pending: 'Menunggu Konfirmasi Mas Robi',
      approved: 'Disetujui Mas Robi',
      in_progress: 'Sedang Dikerjakan',
      completed: 'Selesai Dipasang',
      rejected: 'Tidak Disetujui'
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
        'bank' => 'QRIS Resmi Kos Mas Robi',
        'number' => 'NMID: ID102026192831',
        'holder' => 'Kos Mas Robi / Robi Suryanto',
        'isCash' => false,
        'isQris' => true,
        'instruction' => '<strong>Scan QRIS All Payment:</strong> Buka aplikasi m-Banking atau e-Wallet (GoPay, OVO, DANA, ShopeePay). Scan kode QRIS yang tersedia atau gunakan NMID di atas.'
      ],
      'Tunai (Cash)' => [
        'badge' => 'Pembayaran Tunai (Cash)',
        'bank' => 'Tunai Langsung ke Mas Robi',
        'number' => 'Bayar Cash / Uang Tunai',
        'holder' => 'Mas Robi (Kamar Pengelola Lt. 1)',
        'isCash' => true,
        'isQris' => false,
        'instruction' => '<strong>Pembayaran Tunai (Cash):</strong> Silakan tentukan tanggal penyerahan uang tunai pada kolom di bawah. Lalu serahkan uang tunai sewa kamar langsung kepada Mas Robi di kantor/kamar pengelola lantai 1.'
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
      var previewInit = document.getElementById('avatarPreviewInitial');
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      if (previewInit) previewInit.style.display = 'none';
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
</script>

@endsection
