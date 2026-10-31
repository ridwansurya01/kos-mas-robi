<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="description" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang 100 Mbps, dan aman 24 jam di Sleman, Yogyakarta.">
  <meta name="theme-color" content="#0a5661">
  <title>{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian Kost Eksklusif & Modern Sleman</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230a5661'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/></svg>">

  <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Luxury Real Estate Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

  {{-- =========================================================================
       1. TOP NAVBAR (MATCHING REFERENCE DESIGN)
       ========================================================================= --}}
  <nav class="lr-navbar">
    <div class="lr-container lr-nav-inner">
      <a href="{{ route('home') }}" class="lr-brand">
        <div class="lr-brand-badge-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
            <path d="M9 21V12h6v9"/>
          </svg>
        </div>
        <div class="lr-brand-title">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
      </a>

      <ul class="lr-nav-menu">
        <li><a href="#beranda" class="lr-nav-link active">Beranda</a></li>
        <li><a href="#kamar" class="lr-nav-link">Pilihan Kamar</a></li>
        <li><a href="#fasilitas" class="lr-nav-link">Fasilitas</a></li>
        <li><a href="#lokasi" class="lr-nav-link">Lokasi & Peta</a></li>
        <li><a href="#tentang" class="lr-nav-link">Tentang Pengelola</a></li>
      </ul>

      <div class="lr-nav-actions">
        @auth
          @if(Auth::user()->isOwner())
            <a href="{{ route('admin.dashboard') }}" class="lr-btn-ghost-white">⚙️ Panel Admin</a>
          @else
            <a href="{{ route('tenant.portal') }}" class="lr-btn-ghost-white">🚪 Portal Penghuni</a>
          @endif
        @else
          <a href="{{ route('login') }}" class="lr-btn-ghost-white">Masuk Akun</a>
        @endauth
        <a href="#kamar" class="lr-btn-pill-teal">
          <span>+ Pesan Kamar</span>
        </a>
      </div>
    </div>
  </nav>

  {{-- =========================================================================
       2. HERO SECTION — FULL TEAL BG, PHOTO FADING IN FROM RIGHT
       (Persis referensi transport-logistic: foto menyatu dengan gradient)
       ========================================================================= --}}
  <header class="lr-hero-section" id="beranda">

    {{-- Photo positioned on the right, behind content --}}
    <div class="lr-hero-photo-bg">
      <img src="{{ asset('assets/images/kos_exterior.jpg') }}"
           alt="Bangunan {{ $setting->kos_name ?? 'Kos Mas Robi' }}"
           onerror="this.src='{{ asset('assets/images/hero_scandinavian_room.jpg') }}'">
      {{-- Gradient overlay that fades photo into teal on its left edge --}}
      <div class="lr-hero-photo-fade"></div>
    </div>

    <div class="lr-container lr-hero-grid">
      <div class="lr-hero-left">
        <h1 class="lr-hero-title">
          Find Your<br>Dream Home
        </h1>

        <p class="lr-hero-desc">
          Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis di Sleman, DI Yogyakarta.
        </p>

        <div class="lr-hero-cta-group">
          <a href="#kamar" class="lr-btn-coral">
            <span>Jelajahi Kamar</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
          <a href="#lokasi" class="lr-btn-link-white">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Buka Peta Lokasi</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  {{-- =========================================================================
       3. FLOATING DOUBLE-DECKER SEARCH CARD (OVERLAPPING HERO & LISTINGS)
       ========================================================================= --}}
  <div class="lr-search-wrapper">
    <div class="lr-container">
      <div class="lr-search-card">

        <form action="{{ route('home') }}#kamar" method="GET" class="lr-search-left-inputs">
          {{-- Row 1: Tipe Kamar & Lantai --}}
          <div class="lr-search-row">
            <div class="lr-search-input-pill">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              <input type="text" name="type" placeholder="Tipe Kamar (VIP, Deluxe, Standard)..." value="{{ request('type') != 'all' ? request('type') : '' }}">
            </div>

            <div class="lr-search-input-pill">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22.01"/><line x1="15" y1="22" x2="15" y2="22.01"/><line x1="9" y1="18" x2="9" y2="18.01"/><line x1="15" y1="18" x2="15" y2="18.01"/><line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/></svg>
              <select name="floor" aria-label="Pilih Lantai">
                <option value="all" {{ request('floor') == 'all' ? 'selected' : '' }}>🏢 Semua Lantai</option>
                <option value="1" {{ request('floor') == '1' ? 'selected' : '' }}>Lantai 1</option>
                <option value="2" {{ request('floor') == '2' ? 'selected' : '' }}>Lantai 2</option>
              </select>
            </div>
          </div>

          {{-- Row 2: Fasilitas & Ketersediaan --}}
          <div class="lr-search-row">
            <div class="lr-search-input-pill">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" name="q" placeholder="Fasilitas (AC, Springbed, WiFi)..." value="{{ request('q') ?? '' }}">
            </div>

            <div class="lr-search-input-pill">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <select name="status" aria-label="Pilih Status">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>✨ Semua Status Unit</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>🟢 Siap Huni</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>🔴 Sedang Terisi</option>
              </select>
            </div>
          </div>
        </form>

        {{-- Right Side Metric & Big Submit Pill --}}
        <div class="lr-search-right-cta">
          <div class="lr-metric-box">
            <div class="lr-metric-val">{{ $availableCount ?? 4 }}<span style="font-size: 1.1rem; color: var(--text-muted); font-weight: 600;">/{{ $totalRooms ?? 16 }}</span></div>
            <div class="lr-metric-lbl">Unit Siap Huni</div>
          </div>

          <button type="button" onclick="document.querySelector('.lr-search-left-inputs').submit();" class="lr-btn-search-submit">
            <span>Cari Kamar</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>

      </div>
    </div>
  </div>

  {{-- =========================================================================
       4. FEATURED LISTINGS + TYPE (3 KARTU PERSIS TEMPLATE PADA BACKGROUND MINT)
       ========================================================================= --}}
  <section class="lr-featured-section" id="kamar">
    <div class="lr-container">

      <div class="lr-featured-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
        <div>
          <h2 class="lr-featured-title">
            Featured.listings <span>+ Type</span>
          </h2>
          <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 6px;">
            Pilihan unit kamar kos eksklusif dengan kenyamanan maksimal dan fasilitas lengkap.
          </p>
        </div>

        {{-- Quick Filter Pills --}}
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <a href="{{ route('home') }}#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ !request('type') || request('type') == 'all' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Semua Unit</a>
          <a href="{{ route('home') }}?type=VIP#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'VIP' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">VIP AC</a>
          <a href="{{ route('home') }}?type=Deluxe#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'Deluxe' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Deluxe AC</a>
          <a href="{{ route('home') }}?type=Standard#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'Standard' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Standard Fan</a>
        </div>
      </div>

      <div class="lr-featured-grid">
        @forelse($allRooms as $room)
          @php
            $imgs = $room->images_list ?? [$room->image ?? 'assets/images/rooms/room_1.jpg'];
            $mainImg = $imgs[0] ?? 'assets/images/rooms/room_1.jpg';
            $isAvail = $room->status === 'available';
          @endphp
          <div class="lr-card-featured">
            <div class="lr-card-thumb-wrap">
              <img src="{{ asset($mainImg) }}"
                   alt="Kamar #{{ $room->number }}"
                   class="lr-card-thumb"
                   onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">

              <span class="lr-pill-avail" style="{{ $isAvail ? 'background: #10b981;' : 'background: #64748b;' }}">
                {{ $isAvail ? 'Siap Huni' : 'Terisi' }}
              </span>

              <span class="lr-pill-floor">
                Lantai {{ $room->floor }}
              </span>
            </div>

            <div class="lr-card-body">
              <h3 class="lr-card-room-title">Kamar #{{ $room->number }} - {{ $room->type }}</h3>
              <p class="lr-card-room-desc">
                Dilengkapi springbed empuk, pendingin udara AC, meja belajar, lemari pakaian, dan kamar mandi pribadi bersih.
              </p>

              <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">🛏️ Springbed</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">❄️ AC Dingin</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">🚿 KM Dalam</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">⚡ WiFi 100M</span>
              </div>

              <div class="lr-card-footer">
                <div class="lr-card-mini-meta">
                  <div class="lr-mini-avatar">🏡</div>
                  <div>
                    <div class="lr-price-tag">Rp {{ number_format($room->price, 0, ',', '.') }}</div>
                    <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">per bulan (all-in)</span>
                  </div>
                </div>

                <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Mas%20Robi,%20saya%20tertarik%20booking%20Kamar%20%23{{ $room->number }}%20({{ urlencode($room->type) }})"
                   target="_blank" class="lr-btn-book-round">
                  <span>Booking</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
              </div>
            </div>
          </div>
        @empty
          <div style="grid-column: span 3; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 20px; border: 1px dashed var(--border-subtle);">
            <div style="font-size: 2.2rem; margin-bottom: 10px;">🔍</div>
            <h4 style="font-size: 1.15rem; color: var(--text-dark); margin-bottom: 6px;">Unit Kamar Tidak Ditemukan</h4>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 16px;">Silakan pilih filter lain atau hubungi pengelola untuk cek ketersediaan langsung.</p>
            <a href="{{ route('home') }}#kamar" class="lr-btn-pill-teal" style="background: var(--teal-primary); display: inline-flex;">Tampilkan Semua Kamar</a>
          </div>
        @endforelse
      </div>

      {{-- Dots Indicator (Matching Reference) --}}
      <div class="lr-dots-indicator">
        <span class="lr-dot active"></span>
        <span class="lr-dot"></span>
        <span class="lr-dot"></span>
        <span class="lr-dot"></span>
        <span class="lr-dot"></span>
      </div>

    </div>
  </section>

  {{-- =========================================================================
       5. BENTO ABOUT & FASILITAS COLLAGE (PERSIS BAGIAN BAWAH PADA TEMPLATE)
       ========================================================================= --}}
  <section class="lr-bento-section" id="fasilitas">
    <div class="lr-container">

      {{-- Bento Header: Two-tone Title on Left, Stacked Circular Pills on Right --}}
      <div class="lr-bento-header-grid">
        <div>
          <h2 class="lr-two-tone-title">
            <span class="highlight-coral">Kenyamanan Maksimal</span><br>
            <span class="dark-teal">Untuk Istirahat & Produktivitas</span>
          </h2>
          <p class="lr-bento-header-desc">
            Setiap jengkal hunian kami rancang dengan seksama untuk memastikan penghuni mendapatkan istirahat berkualitas dan fokus maksimal dalam menuntut ilmu maupun berkarier.
          </p>
        </div>

        <div class="lr-feature-pill-stack">
          <div class="lr-feat-pill-item">
            <div class="lr-feat-circle-icon coral">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <div class="lr-feat-pill-text">
              <strong>Internet Fiber 100 Mbps Tanpa Batas</strong>
              <span>Koneksi lancar untuk skripsi, kuliah online, zoom meeting, dan hiburan streaming.</span>
            </div>
          </div>

          <div class="lr-feat-pill-item">
            <div class="lr-feat-circle-icon teal">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div class="lr-feat-pill-text">
              <strong>Smart Access Mandiri & CCTV 24 Jam</strong>
              <span>Bebas jam malam dengan kunci mandiri dan pengawasan kamera CCTV di seluruh lorong.</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Bento Photo Collage Grid (Persis Layout Kotak-Kotak Bawah Template) --}}
      <div class="lr-gallery-grid">

        {{-- Card 1: Fast Racket / Highlight Feature Card --}}
        <div class="lr-gallery-hero-card">
          <div>
            <span style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--coral-light); font-weight: 800; display: block; margin-bottom: 8px;">Fasilitas Unggulan</span>
            <h3>Living & Study In Harmony</h3>
            <p>
              Tersedia dapur bersama komplit dengan kompor dan kulkas, area parkir motor dan mobil luas berpagar aman, serta pembersihan area publik rutin setiap hari.
            </p>
          </div>
          <div style="margin-top: 30px;">
            <a href="#kamar" class="lr-btn-coral" style="padding: 0 22px; height: 42px; font-size: 0.85rem;">
              <span>Lihat Unit Kamar</span>
            </a>
          </div>
        </div>

        {{-- Card 2: 2 Stacked Photo Cards --}}
        <div class="lr-gallery-column">
          <div class="lr-gallery-photo-card">
            <img src="{{ asset('assets/images/hero_scandinavian_room.jpg') }}"
                 alt="Interior Kamar Scandinavian"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.jpg') }}'">
          </div>
          <div class="lr-gallery-photo-card">
            <img src="{{ asset('assets/images/rooms/room_2.jpg') }}"
                 alt="Kamar Kost Nyaman"
                 onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">
          </div>
        </div>

        {{-- Card 3: 2 Stacked Photo Cards --}}
        <div class="lr-gallery-column">
          <div class="lr-gallery-photo-card">
            <img src="{{ asset('assets/images/rooms/room_3.jpg') }}"
                 alt="Fasilitas Kamar Mandi & Kasur"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.jpg') }}'">
          </div>
          <div class="lr-gallery-photo-card">
            <img src="{{ asset('assets/images/kos_exterior.jpg') }}"
                 alt="Tampak Luar Kos Mas Robi Malam Hari"
                 onerror="this.src='{{ asset('assets/images/hero_scandinavian_room.jpg') }}'">
          </div>
        </div>

      </div>

    </div>
  </section>

  {{-- =========================================================================
       6. GOOGLE MAPS & STRATEGIC LOCATION HIGHLIGHTS (DENGAN TEMA TEAL & CORAL)
       ========================================================================= --}}
  <section class="lr-location-section" id="lokasi">
    <div class="lr-container">

      <div style="margin-bottom: 40px;">
        <span style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--teal-primary); background: #ffffff; padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid var(--border-teal-soft); display: inline-block; margin-bottom: 12px;">📍 Peta & Akses Strategis</span>
        <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: var(--text-dark); letter-spacing: -0.02em;">
          Lokasi Sangat Strategis di Sleman
        </h2>
        <p style="font-size: 0.95rem; color: var(--text-muted); max-width: 640px; margin-top: 6px;">
          Terletak di kawasan Pandega Marta - Kaliurang KM 5 Sleman. Tenang untuk belajar, aman 24 jam, dan sangat dekat dengan kampus utama dan fasilitas umum.
        </p>
      </div>

      <div class="lr-location-grid">

        {{-- Left: Informasi Detail & 4 Editable POI Highlights --}}
        <div class="lr-loc-card-left">
          <div>
            <div class="lr-loc-header">
              <div class="lr-loc-pin-box">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <h3 class="lr-loc-title">{{ $setting->kos_name ?? 'Kos Mas Robi - Kampus Terpadu' }}</h3>
                <p class="lr-loc-address">{{ $setting->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta' }}</p>
              </div>
            </div>

            {{-- 4 POI Location Highlights (Dapat diedit via Admin) --}}
            <div class="lr-poi-list">
              @foreach($setting->location_highlights as $poi)
                <div class="lr-poi-item">
                  <span style="font-size: 1.35rem; line-height: 1; flex-shrink: 0;">{{ $poi['icon'] ?? '📍' }}</span>
                  <div>
                    <strong>{{ $poi['title'] ?? '' }}</strong>
                    <p>{{ $poi['desc'] ?? '' }}</p>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="lr-loc-actions">
            @php
              $mapsUrl = $setting->gmaps_url ?: 'https://maps.google.com/?q=' . urlencode($setting->kos_address ?? 'Kos Mas Robi Sleman Yogyakarta');
            @endphp
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="lr-btn-gmaps">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              <span>Buka Google Maps ↗</span>
            </a>
            <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Mas%20Robi,%20saya%20mau%20tanya%20patokan%20lokasi%20atau%20jadwal%20survey%20ke%20Kos%20Mas%20Robi"
               target="_blank" class="lr-btn-survey">
              <span>💬 Janji Survey Lokasi</span>
            </a>
          </div>
        </div>

        {{-- Right: Interactive Google Maps Iframe Frame --}}
        <div class="lr-map-card-right">
          <div class="lr-map-chrome-bar">
            <div class="lr-map-dots">
              <span class="lr-map-dot red"></span>
              <span class="lr-map-dot yellow"></span>
              <span class="lr-map-dot green"></span>
            </div>
            <div class="lr-map-badge">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              <span>maps.google.com/kos-mas-robi</span>
            </div>
          </div>
          <div class="lr-map-frame">
            @php
              $finalEmbed = $setting->gmaps_embed;
              if (str_contains($finalEmbed ?? '', '<iframe')) {
                  preg_match('/src="([^"]+)"/', $finalEmbed, $m);
                  $finalEmbed = $m[1] ?? '';
              }
              if (empty($finalEmbed)) {
                  $finalEmbed = 'https://maps.google.com/maps?q=' . urlencode($setting->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta') . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
              }
            @endphp
            <iframe src="{{ $finalEmbed }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps Lokasi Kos Mas Robi"></iframe>
          </div>
        </div>

      </div>

    </div>
  </section>

  {{-- =========================================================================
       7. FOUNDER QUOTE & COMMITMENT
       ========================================================================= --}}
  <section class="lr-quote-section" id="tentang">
    <div class="lr-container">
      <div class="lr-quote-box">
        <div>
          <div class="lr-quote-avatar">MR</div>
          <div class="lr-quote-name">{{ $setting->owner_name ?? 'Mas Robi' }}</div>
          <div class="lr-quote-role">Owner & Pengelola Properti</div>
        </div>

        <div class="lr-quote-content">
          <p>
            “Kenyamanan, kebersihan, dan keamanan penghuni adalah komitmen utama kami. Kami memastikan setiap anak kos merasakan suasana tinggal yang hangat layaknya di rumah sendiri, dengan fasilitas modern tanpa repot dan dukungan pengelola yang selalu siap membantu kapan pun dibutuhkan.”
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- =========================================================================
       8. MODERN FOOTER
       ========================================================================= --}}
  <footer class="lr-footer" id="kontak">
    <div class="lr-container">
      <div class="lr-footer-grid">
        <div class="lr-footer-brand">
          <h4>{{ $setting->kos_name ?? 'Kos Mas Robi' }}</h4>
          <p style="font-size: 0.88rem; line-height: 1.65; max-width: 440px; color: #94a3b8; margin-bottom: 18px;">
            Hunian kost eksklusif dengan fasilitas lengkap, kamar ber-AC, kamar mandi dalam, WiFi fiber cepat 100 Mbps, dan keamanan 24 jam di Sleman, DI Yogyakarta.
          </p>
          <div style="font-size: 0.84rem; color: #cbd5e1;">
            📍 {{ $setting->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta' }}
          </div>
        </div>

        <div class="lr-footer-col">
          <h5>Navigasi Halaman</h5>
          <ul class="lr-footer-list">
            <li><a href="#beranda">Beranda Utama</a></li>
            <li><a href="#kamar">Pilihan Unit Kamar</a></li>
            <li><a href="#fasilitas">Fasilitas Kost</a></li>
            <li><a href="#lokasi">Peta & Google Maps</a></li>
            <li><a href="#tentang">Tentang Pengelola</a></li>
          </ul>
        </div>

        <div class="lr-footer-col">
          <h5>Hubungi & Akses</h5>
          <ul class="lr-footer-list">
            <li>
              <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}" target="_blank">
                WhatsApp: {{ $setting->owner_phone ?? '6281234567890' }}
              </a>
            </li>
            <li><a href="{{ route('login') }}">Portal Login Anak Kos</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Login Pengelola (Admin)</a></li>
            <li><a href="#beranda">Kembali ke Atas ↑</a></li>
          </ul>
        </div>
      </div>

      <div class="lr-footer-bottom">
        <div>
          © {{ date('Y') }} {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Seluruh hak cipta dilindungi undang-undang.
        </div>
        <div style="color: #64748b;">
          Smart Kost Management System
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
