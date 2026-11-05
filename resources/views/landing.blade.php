<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="description" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang 100 Mbps, dan aman 24 jam di Sleman, Yogyakarta.">
  <meta name="keywords" content="kos eksklusif, sewa kost jogja, kos mas robi, kos sleman yogyakarta, sewa kamar kos modern, kos mahasiswa ugm, kos full furnished, kost ac kamar mandi dalam">
  <meta name="author" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }}">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="theme-color" content="#0a5661">
  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Open Graph Meta Tags (Facebook, WhatsApp, LinkedIn preview) -->
  <meta property="og:locale" content="id_ID">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian Kost Eksklusif & Modern Sleman Yogyakarta">
  <meta property="og:description" content="Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang 100 Mbps, dan aman 24 jam di Sleman, Yogyakarta.">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:site_name" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }}">
  <meta property="og:image" content="{{ asset('assets/images/kos_exterior.jpg') }}">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }}">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian Kost Eksklusif & Modern">
  <meta name="twitter:description" content="Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang 100 Mbps, dan aman 24 jam di Sleman, Yogyakarta.">
  <meta name="twitter:image" content="{{ asset('assets/images/kos_exterior.jpg') }}">

  <!-- Google Schema.org JSON-LD Structured Data for Top Search Engine Ranking -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LodgingBusiness",
    "name": "{{ $setting->kos_name ?? 'Kos Mas Robi' }}",
    "description": "Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang 100 Mbps, dan aman 24 jam di Sleman, Yogyakarta.",
    "url": "{{ url('/') }}",
    "image": "{{ asset('assets/images/kos_exterior.jpg') }}",
    "telephone": "+{{ preg_replace('/[^0-9]/', '', $setting->owner_phone ?? '6281234567890') }}",
    "priceRange": "Rp 900.000 - Rp 2.500.000",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "{{ $setting->kos_address ?? 'Sleman, DI Yogyakarta' }}",
      "addressLocality": "Sleman",
      "addressRegion": "DI Yogyakarta",
      "postalCode": "55281",
      "addressCountry": "ID"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": -7.765,
      "longitude": 110.375
    },
    "amenityFeature": [
      {
        "@type": "LocationFeatureSpecification",
        "name": "WiFi High Speed 100 Mbps",
        "value": true
      },
      {
        "@type": "LocationFeatureSpecification",
        "name": "Kamar Mandi Dalam & Water Heater",
        "value": true
      },
      {
        "@type": "LocationFeatureSpecification",
        "name": "Air Conditioner (AC)",
        "value": true
      },
      {
        "@type": "LocationFeatureSpecification",
        "name": "Security CCTV 24 Jam & Smart Key",
        "value": true
      }
    ]
  }
  </script>

  <title>{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Hunian Kost Eksklusif & Modern</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230a5661'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/></svg>">

  <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Luxury Real Estate Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ file_exists(public_path('css/landing.css')) ? filemtime(public_path('css/landing.css')) : time() }}">
</head>
<body>

  @php
    $landing = $setting->landing_content ?? [];
    $heroImg = $landing['hero_image'] ?? 'assets/images/kos_exterior.jpg';
    $rawOwnerPhone = $setting->owner_phone ?? '6281234567890';
    $cleanOwnerPhone = preg_replace('/[^0-9]/', '', $rawOwnerPhone);
    if (str_starts_with($cleanOwnerPhone, '0')) {
        $cleanOwnerPhone = '62' . substr($cleanOwnerPhone, 1);
    }
    $rawSurveyTemplate = $setting->survey_form_template;
    $bentoGallery = $landing['bento_gallery'] ?? [
        'assets/images/hero_scandinavian_room.jpg',
        'assets/images/rooms/room_2.jpg',
        'assets/images/rooms/room_3.jpg',
        'assets/images/kos_exterior.jpg'
    ];
    $bentoFeatures = $landing['bento_features'] ?? [
        [
            'icon' => '',
            'title' => 'Internet Fiber 100 Mbps Tanpa Batas',
            'desc' => 'Koneksi lancar untuk skripsi, kuliah online, zoom meeting, dan hiburan streaming.'
        ],
        [
            'icon' => '',
            'title' => 'Smart Access Mandiri & CCTV 24 Jam',
            'desc' => 'Bebas jam malam dengan kunci mandiri dan pengawasan kamera CCTV di seluruh lorong.'
        ]
    ];
  @endphp

  {{-- =========================================================================
       1. TOP NAVBAR (PUTIH PREMIUM MODERN)
       ========================================================================= --}}
  <nav class="lr-navbar">
    <div class="lr-container lr-nav-inner">
      <a href="{{ route('home') }}" class="lr-brand">
        <div class="lr-brand-badge-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <path d="M9 21V12h6v9"/>
          </svg>
        </div>
        <div class="lr-brand-title">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</div>
      </a>

      <ul class="lr-nav-menu">
        <li><a href="#beranda" class="lr-nav-link active">Beranda</a></li>
        <li><a href="#kamar" class="lr-nav-link">Pilihan Kamar</a></li>
        <li><a href="#fasilitas" class="lr-nav-link">Fasilitas</a></li>
        <li><a href="#lokasi" class="lr-nav-link">Lokasi & Peta</a></li>
        <li><a href="#tentang" class="lr-nav-link">Tentang Pengelola</a></li>
      </ul>

      <div class="lr-nav-actions">
        <button type="button" class="lr-btn-ghost-white lr-nav-survey-btn" onclick="openSurveyModal()" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.12);" title="Isi Formulir Survey">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span class="lr-nav-survey-text">Jadwal Survey</span>
        </button>
        @auth
          @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="lr-btn-pill-teal">
              <span class="lr-nav-btn-text-full">Dashboard Admin</span>
              <span class="lr-nav-btn-text-short">Admin</span>
            </a>
          @else
            <a href="{{ route('tenant.portal') }}" class="lr-btn-pill-teal">
              <span class="lr-nav-btn-text-full">Portal Anak Kos</span>
              <span class="lr-nav-btn-text-short">Portal</span>
            </a>
          @endif
        @else
          <a href="{{ route('login') }}" class="lr-btn-pill-teal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            <span class="lr-nav-btn-text-full">Login Penghuni</span>
            <span class="lr-nav-btn-text-short">Login</span>
          </a>
        @endauth
      </div>
    </div>
  </nav>

  {{-- =========================================================================
       2. HERO SECTION — SEAMLESS FADED PHOTO MASK (ZERO-LINE TRANSITION)
       ========================================================================= --}}
  <header class="lr-hero-section" id="beranda">

    {{-- Photo positioned on the right, alpha masked smoothly into background --}}
    <div class="lr-hero-photo-bg">
      <img src="{{ asset($heroImg) }}"
           alt="Bangunan {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}"
           onerror="this.src='{{ asset('assets/images/kos_exterior.jpg') }}'">
    </div>

    <div class="lr-container lr-hero-grid">
      <div class="lr-hero-left">
        <h1 class="lr-hero-title">
          {!! nl2br(e($landing['hero_title'] ?? "Kos Impian\nOrang Jember")) !!}
        </h1>

        <p class="lr-hero-desc">
          {{ $landing['hero_desc'] ?? 'Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis.' }}
        </p>

        <div class="lr-hero-cta-group">
          <a href="{{ $landing['hero_btn_link'] ?? '#kamar' }}" class="lr-btn-coral">
            <span>{{ $landing['hero_btn_text'] ?? 'Jelajahi Kamar' }}</span>
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
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22.01"/><line x1="15" y1="22" x2="15" y2="22.01"/><line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/></svg>
              <select name="floor" aria-label="Pilih Lantai">
                <option value="all" {{ request('floor') == 'all' ? 'selected' : '' }}>Semua Lantai</option>
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
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status Unit</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Siap Huni</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Sedang Terisi</option>
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
            Pilihan unit kamar kos eksklusif di <strong>2 Titik Lokasi Strategis</strong> dengan kenyamanan maksimal dan fasilitas lengkap.
          </p>
        </div>

        {{-- Quick Filter Pills --}}
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <a href="{{ route('home') }}#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ !request('type') || request('type') == 'all' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Semua Tipe</a>
          <a href="{{ route('home') }}?type=VIP#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'VIP' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">VIP AC</a>
          <a href="{{ route('home') }}?type=Deluxe#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'Deluxe' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Deluxe AC</a>
          <a href="{{ route('home') }}?type=Standard#kamar" style="padding: 8px 18px; border-radius: var(--radius-full); font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; {{ request('type') == 'Standard' ? 'background: var(--teal-primary); color: #ffffff;' : 'background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-subtle);' }}">Standard Fan</a>
        </div>
      </div>

      {{-- 2 TITIK LOKASI SWITCHER BANNER --}}
      @php
        $b1NameLanding = $setting->branches['lokasi_1']['name'] ?? 'Kos 1 (Kampus Utama)';
        $b2NameLanding = $setting->branches['lokasi_2']['name'] ?? 'Kos 2 (Ringroad Exclusive)';
      @endphp
      <div style="margin: 20px 0 28px; display: flex; gap: 10px; flex-wrap: wrap; background: #f0fdfa; border: 1.5px solid #ccfbf1; padding: 8px; border-radius: 16px;">
        <a href="{{ route('home') }}#kamar" style="padding: 9px 20px; border-radius: 12px; font-size: 0.84rem; font-weight: 800; text-decoration: none; transition: all 0.2s; {{ !request('location') || request('location') == 'all' ? 'background: var(--teal-primary); color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);' : 'background: #ffffff; color: var(--teal-primary); border: 1px solid rgba(10,86,97,0.15);' }}">
          Semua Cabang
        </a>
        <a href="{{ route('home') }}?location=Lokasi+1#kamar" style="padding: 9px 20px; border-radius: 12px; font-size: 0.84rem; font-weight: 800; text-decoration: none; transition: all 0.2s; {{ request('location') == 'Lokasi 1' ? 'background: #0a5661; color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);' : 'background: #ffffff; color: #0a5661; border: 1px solid rgba(10,86,97,0.15);' }}">
          {{ $b1NameLanding }}
        </a>
        <a href="{{ route('home') }}?location=Lokasi+2#kamar" style="padding: 9px 20px; border-radius: 12px; font-size: 0.84rem; font-weight: 800; text-decoration: none; transition: all 0.2s; {{ request('location') == 'Lokasi 2' ? 'background: #0284c7; color: #ffffff; box-shadow: 0 2px 8px rgba(2,132,199,0.25);' : 'background: #ffffff; color: #0284c7; border: 1px solid rgba(2,132,199,0.15);' }}">
          {{ $b2NameLanding }}
        </a>
      </div>

      <div class="lr-featured-grid">
        @forelse($allRooms as $room)
          @php
            $imgs = $room->images_list ?? [$room->image ?? 'assets/images/rooms/room_1.jpg'];
            $mainImg = $imgs[0] ?? 'assets/images/rooms/room_1.jpg';
            $isAvail = $room->status === 'available';
            $isLoc2 = ($room->location ?? '') === 'Lokasi 2';
          @endphp
          <div class="lr-card-featured" style="border-top: 4px solid {{ $isLoc2 ? '#0284c7' : '#0a5661' }};">
            <div class="lr-card-thumb-wrap" style="position: relative;">
              <img src="{{ asset($mainImg) }}"
                   alt="Kamar #{{ $room->number }}"
                   class="lr-card-thumb"
                   onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">

              <!-- Lokasi Badge -->
              @if($isLoc2)
                <span style="position: absolute; top: 12px; left: 12px; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(6px); color: #7dd3fc; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; z-index: 2; border: 1.5px solid #0284c7; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                  {{ $b2NameLanding }}
                </span>
              @else
                <span style="position: absolute; top: 12px; left: 12px; background: rgba(3, 35, 40, 0.9); backdrop-filter: blur(6px); color: #a7f3d0; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; z-index: 2; border: 1.5px solid #10b981; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                  {{ $b1NameLanding }}
                </span>
              @endif

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
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">Springbed</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">AC Dingin</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">KM Dalam</span>
                <span style="font-size: 0.72rem; background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); color: var(--text-muted); font-weight: 600;">WiFi 100M</span>
              </div>

              <div class="lr-card-footer">
                <div class="lr-card-mini-meta">
                  <div class="lr-mini-avatar"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/><path d="M9 21V12h6v9"/></svg></div>
                  <div>
                    <div class="lr-price-tag">Rp {{ number_format($room->price, 0, ',', '.') }}</div>
                    <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">per bulan (all-in)</span>
                  </div>
                </div>

                <button type="button" class="lr-btn-book-round"
                        onclick="openSurveyModal({ room: 'Kamar #{{ $room->number }} - {{ $room->type }} (Lantai {{ $room->floor }})', branch: '{{ $room->location ?? 'Lokasi 1' }}' })"
                        title="Isi Formulir Survey & Booking">
                  <span>Booking</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
              </div>
            </div>
          </div>
        @empty
          <div style="grid-column: span 3; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 20px; border: 1px dashed var(--border-subtle);">
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
       5. BENTO ABOUT & FASILITAS COLLAGE (EDITABLE DARI ADMIN)
       ========================================================================= --}}
  <section class="lr-bento-section" id="fasilitas">
    <div class="lr-container">

      {{-- Bento Header: Two-tone Title on Left, Stacked Circular Pills on Right --}}
      <div class="lr-bento-header-grid">
        <div>
          <h2 class="lr-two-tone-title">
            <span class="highlight-coral">{{ $landing['bento_tag'] ?? 'Kenyamanan Maksimal' }}</span><br>
            <span class="dark-teal">{{ $landing['bento_title'] ?? 'Untuk Istirahat & Produktivitas' }}</span>
          </h2>
          <p class="lr-bento-header-desc">
            {{ $landing['bento_desc'] ?? 'Setiap jengkal hunian kami rancang dengan seksama untuk memastikan penghuni mendapatkan istirahat berkualitas dan fokus maksimal dalam menuntut ilmu maupun berkarier.' }}
          </p>
        </div>

        <div class="lr-feature-pill-stack">
          @foreach($bentoFeatures as $idx => $bf)
            <div class="lr-feat-pill-item">
              <div class="lr-feat-circle-icon {{ $idx % 2 == 0 ? 'coral' : 'teal' }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div class="lr-feat-pill-text">
                <strong>{{ $bf['title'] ?? '' }}</strong>
                <span>{{ $bf['desc'] ?? '' }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Bento Photo Collage Grid (4 Foto Terkelola Admin) --}}
      <div class="lr-gallery-grid">

        {{-- Card 1: Fast Racket / Highlight Feature Card --}}
        <div class="lr-gallery-hero-card">
          <div>
            <span style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--coral-light); font-weight: 800; display: block; margin-bottom: 8px;">
              {{ $landing['bento_card_tag'] ?? 'Fasilitas Unggulan' }}
            </span>
            <h3>{{ $landing['bento_card_title'] ?? 'Living & Study In Harmony' }}</h3>
            <p>
              {{ $landing['bento_card_desc'] ?? 'Tersedia dapur bersama komplit dengan kompor dan kulkas, area parkir motor dan mobil luas berpagar aman, serta pembersihan area publik rutin setiap hari.' }}
            </p>
          </div>
          <div style="margin-top: 30px;">
            <a href="#kamar" class="lr-btn-coral" style="padding: 0 22px; height: 42px; font-size: 0.85rem;">
              <span>{{ $landing['bento_card_btn'] ?? 'Lihat Unit Kamar' }}</span>
            </a>
          </div>
        </div>

        {{-- Card 2: 2 Stacked Photo Cards --}}
        <div class="lr-gallery-column">
          <div class="lr-gallery-photo-card">
            <img src="{{ asset($bentoGallery[0] ?? 'assets/images/hero_scandinavian_room.jpg') }}"
                 alt="Foto Galeri 1"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.jpg') }}'">
          </div>
          <div class="lr-gallery-photo-card">
            <img src="{{ asset($bentoGallery[1] ?? 'assets/images/rooms/room_2.jpg') }}"
                 alt="Foto Galeri 2"
                 onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">
          </div>
        </div>

        {{-- Card 3: 2 Stacked Photo Cards --}}
        <div class="lr-gallery-column">
          <div class="lr-gallery-photo-card">
            <img src="{{ asset($bentoGallery[2] ?? 'assets/images/rooms/room_3.jpg') }}"
                 alt="Foto Galeri 3"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.jpg') }}'">
          </div>
          <div class="lr-gallery-photo-card">
            <img src="{{ asset($bentoGallery[3] ?? 'assets/images/kos_exterior.jpg') }}"
                 alt="Foto Galeri 4"
                 onerror="this.src='{{ asset('assets/images/hero_scandinavian_room.jpg') }}'">
          </div>
        </div>

      </div>

    </div>
  </section>

  {{-- =========================================================================
       6. GOOGLE MAPS & 2 TITIK LOKASI CABANG KOS (100% INTERAKTIF & MODERN)
       ========================================================================= --}}
  <section class="lr-location-section" id="lokasi">
    <div class="lr-container">

      @php
        $allBranches = $setting->branches ?? [];
        $b1 = $allBranches['lokasi_1'] ?? [
          'name' => 'Kos Mas Robi 1 - Kampus Utama',
          'tagline' => 'Hunian Tenang, Nyaman & 3 Menit ke Kampus Utama',
          'address' => $setting->kos_address ?? 'Jl. Pandega Marta No. 88, Caturtunggal, Depok, Sleman, D.I. Yogyakarta',
          'gmaps_url' => $setting->gmaps_url ?: 'https://maps.google.com/?q=Jl.+Pandega+Marta+No.88+Yogyakarta',
        ];
        $b2 = $allBranches['lokasi_2'] ?? [
          'name' => 'Kos Mas Robi 2 - Ringroad Exclusive',
          'tagline' => 'Smart Living, Co-Working Space & Fasilitas Modern',
          'address' => 'Jl. Kaliurang KM 12, Sardonoharjo, Ngaglik, Sleman, D.I. Yogyakarta',
          'gmaps_url' => 'https://maps.google.com/?q=Jl.+Kaliurang+KM+12+Yogyakarta',
        ];
        $embedB1 = $setting->getBranchEmbedUrl('lokasi_1');
        $embedB2 = $setting->getBranchEmbedUrl('lokasi_2');
      @endphp

      <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
        <div>
          <span style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--teal-primary); background: #ffffff; padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid var(--border-teal-soft); display: inline-block; margin-bottom: 12px;">
            {{ $landing['location_tag'] ?? '2 TITIK LOKASI & PETA STRATEGIS' }}
          </span>
          <h2 style="font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 900; color: var(--text-dark); letter-spacing: -0.02em; margin: 0;">
            {{ $landing['location_title'] ?? '2 Titik Lokasi Strategis di Sleman' }}
          </h2>
          <p style="font-size: 0.95rem; color: var(--text-muted); max-width: 640px; margin-top: 6px;">
            Tersedia dua pilihan lokasi kost istimewa: Dekat kampus utama & kawasan Ringroad eksklusif.
          </p>
        </div>

        {{-- Switcher Tab Peta Lokasi 1 & 2 --}}
        <div style="display: flex; gap: 8px; background: #ffffff; padding: 6px; border-radius: 16px; border: 1.5px solid var(--border-subtle); box-shadow: 0 4px 14px rgba(0,0,0,0.04);">
          <button type="button" id="btnLandingLoc1" onclick="switchLandingMap(1)" style="padding: 10px 20px; border-radius: 12px; border: none; font-size: 0.84rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: var(--teal-primary); color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);">
            {{ $b1NameLanding }}
          </button>
          <button type="button" id="btnLandingLoc2" onclick="switchLandingMap(2)" style="padding: 10px 20px; border-radius: 12px; border: none; font-size: 0.84rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--text-dark);">
            {{ $b2NameLanding }}
          </button>
        </div>
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
                <h3 class="lr-loc-title" id="landingLocTitle">{{ $b1['name'] }}</h3>
                <p class="lr-loc-address" id="landingLocAddress">{{ $b1['address'] }}</p>
              </div>
            </div>

            {{-- 4 POI Location Highlights --}}
            <div class="lr-poi-list">
              @foreach($setting->location_highlights as $poi)
                <div class="lr-poi-item">
                  <span style="display:inline-flex;align-items:center;justify-content:center;color:var(--teal-primary);flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                  <div>
                    <strong>{{ $poi['title'] ?? '' }}</strong>
                    <p>{{ $poi['desc'] ?? '' }}</p>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="lr-loc-actions">
            <a href="{{ $b1['gmaps_url'] }}" id="landingGmapsLink" target="_blank" rel="noopener noreferrer" class="lr-btn-gmaps">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              <span>Buka Google Maps ↗</span>
            </a>
            <button type="button" id="landingWaSurveyLink" class="lr-btn-survey" onclick="openSurveyModal({ branch: window.currentLandingBranch || 'Lokasi 1' })" style="border:none; cursor:pointer;">
              <span>Janji Survey Lokasi</span>
            </button>
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
              <span id="landingMapLabel">maps.google.com/kos-mas-robi-cabang-1</span>
            </div>
          </div>
          <div class="lr-map-frame">
            <iframe id="landingMapIframe" src="{{ $embedB1 }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps Lokasi {{ $setting->kos_name ?? 'Kos Mas Robi' }}"></iframe>
          </div>
        </div>

      </div>

      <script>
        var branchData1 = {
          title: @json($b1['name']),
          address: @json($b1['address']),
          gmapsUrl: @json($b1['gmaps_url']),
          embedUrl: @json($embedB1),
          label: 'maps.google.com/kos-mas-robi-cabang-1'
        };
        var branchData2 = {
          title: @json($b2['name']),
          address: @json($b2['address']),
          gmapsUrl: @json($b2['gmaps_url']),
          embedUrl: @json($embedB2),
          label: 'maps.google.com/kos-mas-robi-cabang-2'
        };

        window.currentLandingBranch = 'Lokasi 1';
        function switchLandingMap(branchNum) {
          var btn1 = document.getElementById('btnLandingLoc1');
          var btn2 = document.getElementById('btnLandingLoc2');
          var titleEl = document.getElementById('landingLocTitle');
          var addrEl = document.getElementById('landingLocAddress');
          var linkEl = document.getElementById('landingGmapsLink');
          var frameEl = document.getElementById('landingMapIframe');
          var labelEl = document.getElementById('landingMapLabel');

          if (branchNum === 2) {
            window.currentLandingBranch = 'Lokasi 2';
            titleEl.textContent = branchData2.title;
            addrEl.textContent = branchData2.address;
            linkEl.href = branchData2.gmapsUrl;
            frameEl.src = branchData2.embedUrl;
            labelEl.textContent = branchData2.label;

            btn2.style.background = '#0d9488';
            btn2.style.color = '#ffffff';
            btn2.style.boxShadow = '0 2px 8px rgba(13,148,136,0.25)';
            btn1.style.background = 'transparent';
            btn1.style.color = 'var(--text-dark)';
            btn1.style.boxShadow = 'none';
          } else {
            window.currentLandingBranch = 'Lokasi 1';
            titleEl.textContent = branchData1.title;
            addrEl.textContent = branchData1.address;
            linkEl.href = branchData1.gmapsUrl;
            frameEl.src = branchData1.embedUrl;
            labelEl.textContent = branchData1.label;

            btn1.style.background = 'var(--teal-primary)';
            btn1.style.color = '#ffffff';
            btn1.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
            btn2.style.background = 'transparent';
            btn2.style.color = 'var(--text-dark)';
            btn2.style.boxShadow = 'none';
          }
        }
      </script>

    </div>
  </section>

    </div>
  </section>

  {{-- =========================================================================
       7. FOUNDER QUOTE & COMMITMENT (EDITABLE DARI ADMIN)
       ========================================================================= --}}
  <section class="lr-quote-section" id="tentang">
    <div class="lr-container">
      <div class="lr-quote-box">
        <div>
          <div class="lr-quote-avatar">{{ $landing['founder_avatar'] ?? 'MR' }}</div>
          <div class="lr-quote-name">{{ $landing['founder_name'] ?? ($setting->owner_name ?? 'Mas Robi') }}</div>
          <div class="lr-quote-role">{{ $landing['founder_role'] ?? 'Owner & Pengelola Properti' }}</div>
        </div>

        <div class="lr-quote-content">
          <p>
            “{{ $landing['founder_quote'] ?? 'Kenyamanan, kebersihan, dan keamanan penghuni adalah komitmen utama kami. Kami memastikan setiap anak kos merasakan suasana tinggal yang hangat layaknya di rumah sendiri, dengan fasilitas modern tanpa repot dan dukungan pengelola yang selalu siap membantu kapan pun dibutuhkan.' }}”
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
            {{ $landing['footer_desc'] ?? 'Hunian kost eksklusif dengan fasilitas lengkap, kamar ber-AC, kamar mandi dalam, WiFi fiber cepat 100 Mbps, dan keamanan 24 jam di kawasan strategis.' }}
          </p>
          <div style="font-size: 0.84rem; color: #cbd5e1;">
            {{ $setting->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta' }}
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
              <a href="javascript:void(0)" onclick="openSurveyModal()" title="Jadwalkan Survey Kamar">
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

  <!-- =========================================================================
       MODAL FORMULIR SURVEY & HUBUNGI ADMIN (CALON PENGHUNI)
       ========================================================================= -->
  <div id="surveyModal" class="lr-survey-modal-backdrop" style="display: none;" onclick="if(event.target === this) closeSurveyModal()">
    <div class="lr-survey-modal-card">
      <div class="lr-survey-modal-header">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div class="lr-survey-icon-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div>
            <h3 class="lr-survey-modal-title">Jadwal Survey & Booking Kamar</h3>
            <p class="lr-survey-modal-sub">Isi data rencana kunjungan di bawah untuk langsung terhubung ke WhatsApp Pengelola.</p>
          </div>
        </div>
        <button type="button" class="lr-survey-close-btn" onclick="closeSurveyModal()" title="Tutup Modal">&times;</button>
      </div>

      <form id="landingSurveyForm" onsubmit="submitSurveyToWhatsApp(event)" class="lr-survey-form-body">
        <!-- 1. DATA CALON PENGHUNI -->
        <div class="lr-survey-grid-2">
          <div class="lr-survey-form-group">
            <label for="sf_name">Nama Lengkap Calon Penghuni</label>
            <input type="text" id="sf_name" class="lr-survey-input" placeholder="Tulis Nama Lengkap Anda" required oninput="renderSurveyPreview()">
          </div>
          <div class="lr-survey-form-group">
            <label for="sf_job">Pekerjaan / Status</label>
            <select id="sf_job" class="lr-survey-input" onchange="renderSurveyPreview()">
              <option value="Mahasiswa">Mahasiswa</option>
              <option value="Karyawan">Karyawan</option>
              <option value="PNS / BUMN">PNS / BUMN</option>
              <option value="Wiraswasta / Profesional">Wiraswasta / Profesional</option>
              <option value="Umum">Umum / Lainnya</option>
            </select>
          </div>
        </div>

        <!-- 2. PILIHAN LOKASI & KAMAR -->
        <div class="lr-survey-grid-2">
          <div class="lr-survey-form-group">
            <label for="sf_branch">Pilihan Lokasi Kost</label>
            <select id="sf_branch" class="lr-survey-input" onchange="renderSurveyPreview()">
              <option value="{{ $b1['name'] ?? 'Lokasi 1' }}">{{ $b1['name'] ?? 'Lokasi 1 (Kampus Utama)' }}</option>
              <option value="{{ $b2['name'] ?? 'Lokasi 2' }}">{{ $b2['name'] ?? 'Lokasi 2 (Ringroad Exclusive)' }}</option>
            </select>
          </div>
          <div class="lr-survey-form-group">
            <label for="sf_room">Pilihan Kamar</label>
            <input type="text" id="sf_room" class="lr-survey-input" placeholder="Contoh: Kamar 104 - Lantai 2" oninput="renderSurveyPreview()">
          </div>
        </div>

        <!-- 3. RENCANA SURVEY KAMAR -->
        <div class="lr-survey-grid-3">
          <div class="lr-survey-form-group">
            <label for="sf_date">Tanggal Rencana Survey</label>
            <input type="date" id="sf_date" class="lr-survey-input" required onchange="renderSurveyPreview()">
          </div>
          <div class="lr-survey-form-group">
            <label for="sf_time">Jam Survey</label>
            <input type="time" id="sf_time" value="14:00" class="lr-survey-input" required onchange="renderSurveyPreview()">
          </div>
          <div class="lr-survey-form-group">
            <label for="sf_method">Metode Survey</label>
            <select id="sf_method" class="lr-survey-input" onchange="renderSurveyPreview()">
              <option value="Datang Langsung">Datang Langsung</option>
              <option value="Video Call">Video Call</option>
            </select>
          </div>
        </div>

        <!-- 4. LIVE PREVIEW KOTAK PESAN WHATSAPP -->
        <div class="lr-survey-preview-box">
          <div class="lr-survey-preview-header">
            <span class="lr-survey-preview-label">Pratinjau Pesan yang Dikirim ke WhatsApp:</span>
            <span style="font-size: 0.72rem; color: #059669; font-weight: 700;">Format Otomatis</span>
          </div>
          <div id="surveyPreviewBox" class="lr-survey-preview-content"></div>
        </div>

        <!-- 5. TOMBOL AKSI -->
        <div class="lr-survey-actions">
          <button type="submit" class="lr-survey-btn-submit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/>
            </svg>
            <span>Kirim Formulir ke WhatsApp Admin</span>
          </button>
          <div class="lr-survey-actions-sub">
            <button type="button" class="lr-survey-btn-copy" onclick="copySurveyPreviewText()">
              <span id="copySurveyStatus">Salin Teks Formulir</span>
            </button>
            <button type="button" class="lr-survey-btn-raw" onclick="openDirectWhatsAppBlank()">
              <span>Buka WhatsApp Langsung (Format Kosong)</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>


  <script>
    // Survey Form Modal & WhatsApp Generator Logic
    window.kosWaPhone = @json($cleanOwnerPhone);
    window.activeSurveyTemplate = @json($rawSurveyTemplate);

    function formatIndoDate(dateStr) {
      if (!dateStr) return '';
      var parts = dateStr.split('-');
      if (parts.length !== 3) return dateStr;
      var d = new Date(parts[0], parts[1] - 1, parts[2]);
      var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function formatIndoTime(timeStr) {
      if (!timeStr) return '';
      return timeStr.replace(':', '.') + ' WIB';
    }

    function getSurveyFormData() {
      var nameEl = document.getElementById('sf_name');
      var jobEl = document.getElementById('sf_job');
      var branchEl = document.getElementById('sf_branch');
      var roomEl = document.getElementById('sf_room');
      var dateEl = document.getElementById('sf_date');
      var timeEl = document.getElementById('sf_time');
      var methodEl = document.getElementById('sf_method');

      return {
        name: nameEl ? nameEl.value.trim() : '',
        job: jobEl ? jobEl.value : 'Mahasiswa',
        branch: branchEl ? branchEl.value : 'Lokasi 1',
        room: roomEl ? roomEl.value.trim() : '',
        date: dateEl ? dateEl.value : '',
        time: timeEl ? timeEl.value : '14:00',
        method: methodEl ? methodEl.value : 'Datang Langsung'
      };
    }

    function buildSurveyMessageText(data) {
      var tpl = window.activeSurveyTemplate || '';
      var nameVal = data.name || '[Tulis Nama Anda]';
      var jobVal = data.job || '[Mahasiswa / Karyawan / dll]';
      var branchVal = data.branch || '[Pilih Lokasi 1 / Lokasi 2]';
      var roomVal = data.room || '[Tulis Nomor / Tipe Kamar, contoh: Kamar 104 - Lantai 2]';
      var dateVal = data.date ? formatIndoDate(data.date) : '[Tulis Tanggal, contoh: Kamis, 5 November 2026]';
      var timeVal = data.time ? formatIndoTime(data.time) : '[Tulis Jam, contoh : 14.00 WIB]';
      var methodVal = data.method || '[Datang Langsung / Video Call]';

      // Replace literal template bracket placeholders
      tpl = tpl.replace(/\[Tulis Nama Anda\]/gi, nameVal);
      tpl = tpl.replace(/\[Mahasiswa \/ Karyawan \/ dll\]/gi, jobVal);
      tpl = tpl.replace(/\[Pilih Lokasi 1 \/ Lokasi 2\]/gi, branchVal);
      tpl = tpl.replace(/\[Tulis Nomor \/ Tipe Kamar, contoh: Kamar 104 - Lantai 2\]/gi, roomVal);
      tpl = tpl.replace(/\[Tulis Tanggal, contoh: Kamis, 5 November 2026\]/gi, dateVal);
      tpl = tpl.replace(/\[Tulis Jam, contoh : 14.00 WIB\]/gi, timeVal);
      tpl = tpl.replace(/\[Datang Langsung \/ Video Call\]/gi, methodVal);

      // Support flexible generic tags if template was edited in admin settings
      tpl = tpl.replace(/\[nama\]/gi, nameVal);
      tpl = tpl.replace(/\[pekerjaan\]/gi, jobVal);
      tpl = tpl.replace(/\[status\]/gi, jobVal);
      tpl = tpl.replace(/\[lokasi\]/gi, branchVal);
      tpl = tpl.replace(/\[cabang\]/gi, branchVal);
      tpl = tpl.replace(/\[kamar\]/gi, roomVal);
      tpl = tpl.replace(/\[tanggal\]/gi, dateVal);
      tpl = tpl.replace(/\[jam\]/gi, timeVal);
      tpl = tpl.replace(/\[waktu\]/gi, timeVal);
      tpl = tpl.replace(/\[metode\]/gi, methodVal);

      return tpl;
    }

    function renderSurveyPreview() {
      var previewEl = document.getElementById('surveyPreviewBox');
      if (!previewEl) return;
      var data = getSurveyFormData();
      previewEl.textContent = buildSurveyMessageText(data);
    }

    window.openSurveyModal = function(opts) {
      opts = opts || {};
      var modal = document.getElementById('surveyModal');
      if (!modal) return;

      if (opts.room) {
        var roomEl = document.getElementById('sf_room');
        if (roomEl) roomEl.value = opts.room;
      }
      if (opts.branch) {
        var branchEl = document.getElementById('sf_branch');
        if (branchEl) {
          for (var i = 0; i < branchEl.options.length; i++) {
            if (branchEl.options[i].value.indexOf(opts.branch) !== -1 || opts.branch.indexOf(branchEl.options[i].value) !== -1) {
              branchEl.selectedIndex = i;
              break;
            }
          }
        }
      }

      // Set default date to tomorrow if empty
      var dateEl = document.getElementById('sf_date');
      if (dateEl && !dateEl.value) {
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        var yyyy = tomorrow.getFullYear();
        var mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
        var dd = String(tomorrow.getDate()).padStart(2, '0');
        dateEl.value = yyyy + '-' + mm + '-' + dd;
        dateEl.min = new Date().toISOString().split('T')[0];
      }

      renderSurveyPreview();
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    };

    window.closeSurveyModal = function() {
      var modal = document.getElementById('surveyModal');
      if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
      }
    };

    window.submitSurveyToWhatsApp = function(e) {
      if (e) e.preventDefault();
      var data = getSurveyFormData();
      var msg = buildSurveyMessageText(data);
      var waUrl = 'https://wa.me/' + window.kosWaPhone + '?text=' + encodeURIComponent(msg);
      window.open(waUrl, '_blank');
    };

    window.copySurveyPreviewText = function() {
      var data = getSurveyFormData();
      var msg = buildSurveyMessageText(data);
      navigator.clipboard.writeText(msg).then(function() {
        var statusEl = document.getElementById('copySurveyStatus');
        if (statusEl) {
          statusEl.textContent = 'Format Tersalin!';
          setTimeout(function() { statusEl.textContent = 'Salin Teks Formulir'; }, 2000);
        }
      });
    };

    window.openDirectWhatsAppBlank = function() {
      var tpl = window.activeSurveyTemplate || '';
      var waUrl = 'https://wa.me/' + window.kosWaPhone + '?text=' + encodeURIComponent(tpl);
      window.open(waUrl, '_blank');
    };
  </script>

  <script>
    (function() {
      var navLinks = Array.prototype.slice.call(document.querySelectorAll('.lr-nav-menu .lr-nav-link'));
      var sectionIds = ['beranda', 'kamar', 'fasilitas', 'lokasi', 'tentang'];
      var isClickScrolling = false;
      var scrollTimer = null;

      function setActiveNavByHash(hash) {
        if (!hash) hash = '#beranda';
        navLinks.forEach(function(link) {
          var isMatch = link.getAttribute('href') === hash;
          link.classList.toggle('active', isMatch);
        });
      }

      // Click handler on navbar links & internal section links
      document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function() {
          var targetHash = this.getAttribute('href');
          if (targetHash && sectionIds.indexOf(targetHash.replace('#', '')) !== -1) {
            isClickScrolling = true;
            setActiveNavByHash(targetHash);
            if (scrollTimer) clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
              isClickScrolling = false;
            }, 750);
          }
        });
      });

      // Scroll-spy to automatically move the active box as user scrolls through sections
      function updateNavOnScroll() {
        if (isClickScrolling) return;
        var scrollPos = window.scrollY + 160;
        var currentId = 'beranda';

        for (var i = 0; i < sectionIds.length; i++) {
          var el = document.getElementById(sectionIds[i]);
          if (el && el.offsetTop <= scrollPos) {
            currentId = sectionIds[i];
          }
        }

        // If scrolled to the very bottom of page, highlight the last section
        if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 40)) {
          currentId = sectionIds[sectionIds.length - 1];
        }

        setActiveNavByHash('#' + currentId);
      }

      window.addEventListener('scroll', updateNavOnScroll, { passive: true });
      window.addEventListener('hashchange', function() {
        if (window.location.hash) {
          setActiveNavByHash(window.location.hash);
        }
      });

      // Initial state on page load
      if (window.location.hash && sectionIds.indexOf(window.location.hash.replace('#', '')) !== -1) {
        setActiveNavByHash(window.location.hash);
      } else if (window.location.search && (window.location.search.indexOf('type=') !== -1 || window.location.search.indexOf('location=') !== -1 || window.location.search.indexOf('floor=') !== -1 || window.location.search.indexOf('status=') !== -1)) {
        setActiveNavByHash('#kamar');
      } else {
        updateNavOnScroll();
      }
    })();
  </script>

</body>
</html>
