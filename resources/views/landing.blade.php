<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <title>{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember</title>

  <!-- SEO Meta Tags for Google Search Ranking: info kost jember & kos jember -->
  <meta name="description" content="Info Kost Jember terbaik &amp; eksklusif di {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} Kos Jember. Hunian kost modern, bersih, aman 24 jam, full furnished, AC, WiFi 100 Mbps, kamar mandi dalam, lokasi strategis dekat kampus UNEJ, UNMUH, dan Polije Jember.">
  <meta name="keywords" content="info kost jember, kos jember, kost jember, sewa kost jember, info kos jember, kost unej jember, kos unej, kost unmuh jember, kost dekat polije jember, kos mahasiswa jember, kost putri jember, kost putra jember, kost bebas jember, kos eksklusif jember, kost ac jember, smart living jember, kos mas robi jember">
  <meta name="author" content="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <meta name="theme-color" content="#0a5661">
  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Geo Target Meta Tags for Local Google Search Dominance in Jember -->
  <meta name="geo.region" content="ID-JI">
  <meta name="geo.placename" content="Jember, Jawa Timur, Indonesia">
  <meta name="geo.position" content="-8.1724;113.7007">
  <meta name="ICBM" content="-8.1724, 113.7007">

  <!-- Open Graph Meta Tags (Google, WhatsApp, Facebook, LinkedIn preview) -->
  <meta property="og:locale" content="id_ID">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember | Info Kost Jember Terbaik &amp; Eksklusif">
  <meta property="og:description" content="Info Kost Jember terbaik &amp; eksklusif di {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} Kos Jember. Fasilitas lengkap AC, WiFi 100M, KM Dalam, dekat kampus UNEJ &amp; UNMUH Jember.">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:site_name" content="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember">
  <meta property="og:image" content="{{ asset('assets/images/hero_scandinavian_room.jpg') }}">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Info Kost Jember - {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} Kos Jember">

  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember | Info Kost Jember">
  <meta name="twitter:description" content="Info Kost Jember terbaik &amp; eksklusif di {{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} Kos Jember. Hunian kost modern, AC, WiFi 100M, KM Dalam, dekat kampus UNEJ &amp; UNMUH Jember.">
  <meta name="twitter:image" content="{{ asset('assets/images/hero_scandinavian_room.jpg') }}">

  <!-- Favicon Sesuai Logo Landing Page (Teal Gradient Badge dengan Ikon Rumah & Pintu Putih) -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
  <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">

  <!-- Google Schema.org JSON-LD Structured Data (LocalBusiness, LodgingBusiness, FAQPage, BreadcrumbList) for Top Ranking on Google -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "LodgingBusiness",
        "@id": "{{ url('/') }}#lodging",
        "name": "{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember",
        "alternateName": [
          "Info Kost Jember",
          "Kos Jember SMART LIVING",
          "Kost Jember",
          "Kos Mas Robi Jember",
          "Kost Dekat Kampus UNEJ & UNMUH Jember"
        ],
        "description": "Info Kost Jember terbaik, eksklusif, dan modern di SMART LIVING Kos Jember. Fasilitas kamar full furnished, AC, kamar mandi dalam, WiFi fiber 100 Mbps, keamanan CCTV 24 jam, lokasi strategis dekat Universitas Jember (UNEJ) dan Universitas Muhammadiyah Jember (UNMUH).",
        "url": "{{ url('/') }}",
        "image": "{{ asset('assets/images/hero_scandinavian_room.jpg') }}",
        "telephone": "+{{ preg_replace('/[^0-9]/', '', $setting->owner_phone ?? '6281234567890') }}",
        "priceRange": "Rp 950.000 - Rp 1.700.000",
        "currenciesAccepted": "IDR",
        "paymentAccepted": "Cash, Bank Transfer, QRIS",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Jl. Kalimantan & Jl. Mastrip, Area Kampus UNEJ & UNMUH, Sumbersari",
          "addressLocality": "Jember",
          "addressRegion": "Jawa Timur",
          "postalCode": "68121",
          "addressCountry": "ID"
        },
        "geo": {
          "@type": "GeoCoordinates",
          "latitude": -8.1724,
          "longitude": 113.7007
        },
        "openingHoursSpecification": [
          {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            "opens": "00:00",
            "closes": "23:59"
          }
        ],
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "4.9",
          "reviewCount": "148",
          "bestRating": "5",
          "worstRating": "1"
        },
        "amenityFeature": [
          {
            "@type": "LocationFeatureSpecification",
            "name": "WiFi High Speed Fiber 100 Mbps",
            "value": true
          },
          {
            "@type": "LocationFeatureSpecification",
            "name": "Kamar Mandi Dalam & Water Heater",
            "value": true
          },
          {
            "@type": "LocationFeatureSpecification",
            "name": "Pendingin Udara Air Conditioner (AC)",
            "value": true
          },
          {
            "@type": "LocationFeatureSpecification",
            "name": "Springbed Nyaman & Meja Belajar",
            "value": true
          },
          {
            "@type": "LocationFeatureSpecification",
            "name": "Keamanan CCTV 24 Jam & Akses Mandiri",
            "value": true
          },
          {
            "@type": "LocationFeatureSpecification",
            "name": "Parkir Motor & Mobil Luas Aman",
            "value": true
          }
        ]
      },
      {
        "@type": "FAQPage",
        "@id": "{{ url('/') }}#faq",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "Bagaimana cara mendapatkan info kost Jember dan survei kamar di SMART LIVING?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Anda dapat melihat ketersediaan kamar secara live di website SMART LIVING Kos Jember, kemudian klik tombol 'Jadwal Survey' atau 'Chat WhatsApp' untuk langsung terhubung dengan pengelola kos dan menentukan jadwal kunjungan lokasi."
            }
          },
          {
            "@type": "Question",
            "name": "Berapa tarif harga sewa kamar kos di Kos Jember SMART LIVING?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Harga sewa kamar Kos Jember di SMART LIVING mulai dari Rp 950.000 hingga Rp 1.700.000 per bulan dengan fasilitas all-in (WiFi 100 Mbps gratis, springbed, lemari pakaian, meja belajar, AC, dan kamar mandi dalam)."
            }
          },
          {
            "@type": "Question",
            "name": "Apakah lokasi Kos Jember SMART LIVING dekat dengan kampus UNEJ dan UNMUH?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Sangat dekat. Unit Kos Jember SMART LIVING Cabang 1 dan Cabang 2 hanya berjarak 3-5 menit dari gerbang Universitas Jember (UNEJ), Universitas Muhammadiyah Jember (UNMUH), dan Politeknik Negeri Jember (Polije)."
            }
          },
          {
            "@type": "Question",
            "name": "Fasilitas apa saja yang didapat penghuni Kos Jember SMART LIVING?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Fasilitas lengkap meliputi kamar full furnished (springbed, meja, lemari), AC dingin, kamar mandi dalam bersih, water heater, WiFi fiber 100 Mbps, dapur bersama, kulkas umum, CCTV 24 jam, dan parkir luas."
            }
          }
        ]
      },
      {
        "@type": "BreadcrumbList",
        "@id": "{{ url('/') }}#breadcrumb",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Beranda Kos Jember",
            "item": "{{ url('/') }}"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Info Kost Jember",
            "item": "{{ url('/') }}#kamar"
          }
        ]
      }
    ]
  }
  </script>

  <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Luxury Real Estate Stylesheet -->
  @php
    $landingCssFile = file_exists(public_path('css/landing.min.css')) ? 'css/landing.min.css' : 'css/landing.css';
  @endphp
  <link rel="stylesheet" href="{{ asset($landingCssFile) }}?v={{ filemtime(public_path($landingCssFile)) }}">

  <!-- Critical Anti-FOUC Intro Loader: Mencegah kedip / flash landing page saat pertama kali dibuka -->
  <style>
    .flutter-3d-intro-overlay {
      position: fixed;
      inset: 0;
      z-index: 99999999;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 1;
      visibility: visible;
    }
    html.f3d-intro-skip #flutter3dIntroOverlay {
      display: none !important;
    }
    html.f3d-intro-active body {
      overflow: hidden !important;
    }
    html.f3d-intro-active .lr-navbar {
      transform: translateY(-40px);
      opacity: 0 !important;
      pointer-events: none !important;
    }
    html.f3d-intro-active .lr-hero-photo-bg img {
      transform: scale(1.12) translateY(-10px);
      filter: blur(10px) contrast(90%);
    }
    html.f3d-intro-active .lr-hero-title {
      transform: perspective(900px) translateY(36px) translateZ(-40px) rotateX(10deg);
      opacity: 0 !important;
    }
    html.f3d-intro-active .lr-hero-desc {
      transform: translateY(28px);
      opacity: 0 !important;
    }
    html.f3d-intro-active .lr-hero-cta-group {
      transform: translateY(24px) scale(0.96);
      opacity: 0 !important;
    }
  </style>
  <script>
    (function() {
      try {
        var mode = localStorage.getItem('kos_3d_flutter_mode');
        var shown = sessionStorage.getItem('f3d_intro_shown');
        if (mode === 'disabled' || shown === '1') {
          document.documentElement.classList.add('f3d-intro-skip');
        } else {
          document.documentElement.classList.add('f3d-intro-active');
        }
      } catch(e) {}
    })();
  </script>
</head>
<body>

  <!-- =========================================================================
       FLUTTER INTRO ENTRY: LOGO RESMI DENGAN ANIMASI ULAR MENGGAMBAR GARIS PUTIH
       ========================================================================= -->
  <div id="flutter3dIntroOverlay" class="flutter-3d-intro-overlay">
    <div class="f3d-intro-container" id="f3dIntroContainer">
      <div class="f3d-ambient-aura" id="f3dAmbientAura"></div>

      <!-- LOGO RESMI BADGE (SAMA PERSIS DENGAN GAMBAR USER) -->
      <div class="f3d-logo-badge-wrap">
        <div class="f3d-official-logo-badge" id="f3dOfficialLogoBadge">

          <svg class="f3d-svg-canvas" viewBox="0 0 226 230" fill="none">
            <defs>
              <linearGradient id="landingSnakeGreenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#10b981" />
                <stop offset="50%" stop-color="#2dd4bf" />
                <stop offset="100%" stop-color="#34d399" />
              </linearGradient>
            </defs>

            <!-- 1. Garis Putih Rumah (Awalnya Tidak Ada / 0, Muncul Mengikuti Ular Berjalan) -->
            <path id="f3dPathHouseWhite" pathLength="1000" class="f3d-drawn-white" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="#ffffff" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 2. Animasi Ular Hijau Berjalan di Garis Rumah -->
            <path id="f3dPathHouseSnake" class="f3d-snake-tracer" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="url(#landingSnakeGreenGrad)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 3. Garis Putih Pintu (Awalnya Tidak Ada / 0, Muncul Mengikuti Ular Berjalan) -->
            <path id="f3dPathDoorWhite" pathLength="1000" class="f3d-drawn-white" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="#ffffff" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />

            <!-- 4. Animasi Ular Hijau Berjalan di Garis Pintu -->
            <path id="f3dPathDoorSnake" class="f3d-snake-tracer" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="url(#landingSnakeGreenGrad)" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </div>

    </div>
  </div>

  <!-- Inisialisasi awal sinkron SVG: Menjamin garis putih murni 0 saat logo pertama kali muncul -->
  <script>
    (function() {
      try {
        var wH = document.getElementById('f3dPathHouseWhite');
        var sH = document.getElementById('f3dPathHouseSnake');
        var wD = document.getElementById('f3dPathDoorWhite');
        var sD = document.getElementById('f3dPathDoorSnake');
        if (wH) { wH.style.strokeDasharray = '0 1000'; wH.style.strokeDashoffset = '0'; }
        if (sH) { sH.style.strokeDasharray = '0 1000'; sH.style.strokeDashoffset = '0'; sH.style.opacity = '0'; }
        if (wD) { wD.style.strokeDasharray = '0 1000'; wD.style.strokeDashoffset = '0'; }
        if (sD) { sD.style.strokeDasharray = '0 1000'; sD.style.strokeDashoffset = '0'; }
      } catch(e) {}
    })();
  </script>

  @php
    $landing = $setting->landing_content ?? [];
    $heroImg = $landing['hero_image'] ?? 'assets/images/kos_exterior.webp';
    $rawOwnerPhone = $setting->owner_phone ?? '6281234567890';
    $cleanOwnerPhone = preg_replace('/[^0-9]/', '', $rawOwnerPhone);
    if (str_starts_with($cleanOwnerPhone, '0')) {
        $cleanOwnerPhone = '62' . substr($cleanOwnerPhone, 1);
    }
    $rawSurveyTemplate = $setting->survey_form_template;
    $bentoGallery = $landing['bento_gallery'] ?? [
        'assets/images/hero_scandinavian_room.webp',
        'assets/images/rooms/room_2.webp',
        'assets/images/rooms/room_3.webp',
        'assets/images/kos_exterior.webp'
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
          <img src="{{ asset('assets/images/logo_kos_jember.png') }}" alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'Kos Jember' }}" class="lr-brand-img" width="42" height="42" fetchpriority="high">
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
          <a href="{{ route('login') }}" class="lr-btn-pill-teal" onclick="window.f3dNavigateToLogin(event, this.href)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            <span class="lr-nav-btn-text-full">Login</span>
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
           fetchpriority="high"
           decoding="async"
           onerror="this.src='{{ asset('assets/images/kos_exterior.webp') }}'">
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
  <div class="lr-search-wrapper" data-spatial-reveal="search-dock">
    <div class="lr-container">
      <div class="lr-search-card">

        <form action="{{ route('home') }}#kamar" method="GET" class="lr-search-left-inputs">
          {{-- Row 1: Tipe Kamar & Lantai --}}
          <div class="lr-search-row" data-spatial-reveal="card-item" data-stagger="1">
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
          <div class="lr-search-row" data-spatial-reveal="card-item" data-stagger="2">
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
        <div class="lr-search-right-cta" data-spatial-reveal="card-item" data-stagger="3">
          <div class="lr-metric-box">
            <div class="lr-metric-val">{{ $availableCount }}<span style="font-size: 1.1rem; color: var(--text-muted); font-weight: 600;">/{{ $totalRooms }}</span></div>
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
       4. FEATURED LISTINGS (KATALOG UNIT KAMAR KOS)
       ========================================================================= --}}
  <section class="lr-featured-section" id="kamar">
    <div class="lr-container">

      {{-- 2 TITIK LOKASI SWITCHER BANNER (Client-side, No Reload) --}}
      @php
        $b1NameLanding = $setting->branches['lokasi_1']['name'] ?? 'Kos 1 (Kampus Utama)';
        $b2NameLanding = $setting->branches['lokasi_2']['name'] ?? 'Kos 2 (Ringroad Exclusive)';
      @endphp
      @php
        $b1ActiveLanding = $setting->branches['lokasi_1']['is_active'] ?? true;
        $b2ActiveLanding = $setting->branches['lokasi_2']['is_active'] ?? true;
      @endphp
      <div style="margin: 0 0 28px; display: flex; gap: 10px; flex-wrap: wrap; background: #f0fdfa; border: 1.5px solid #ccfbf1; padding: 8px; border-radius: 16px;" id="landingBranchFilters" data-spatial-reveal="section-header">
        @if($b1ActiveLanding && $b2ActiveLanding)
          <button type="button" class="lr-filter-pill-branch active" data-location="all" onclick="filterLandingRoomsLocation('all', this)">
            Semua Cabang
          </button>
          <button type="button" class="lr-filter-pill-branch" data-location="Lokasi 1" onclick="filterLandingRoomsLocation('Lokasi 1', this)">
            {{ $b1NameLanding }}
          </button>
          <button type="button" class="lr-filter-pill-branch" data-location="Lokasi 2" onclick="filterLandingRoomsLocation('Lokasi 2', this)">
            {{ $b2NameLanding }}
          </button>
        @elseif($b1ActiveLanding)
          <button type="button" class="lr-filter-pill-branch active" data-location="Lokasi 1" onclick="filterLandingRoomsLocation('Lokasi 1', this)">
            {{ $b1NameLanding }}
          </button>
        @elseif($b2ActiveLanding)
          <button type="button" class="lr-filter-pill-branch active" data-location="Lokasi 2" onclick="filterLandingRoomsLocation('Lokasi 2', this)">
            {{ $b2NameLanding }}
          </button>
        @endif
      </div>

      {{-- 4.1 LOGIKA SLIDER (1 SLIDE = 6 KAMAR: 3 KAMAR LT. 1 + 3 KAMAR LT. 2) --}}
      @php
        $floor1Rooms = $allRooms->filter(fn($r) => (int)$r->floor === 1)->values();
        $floor2Rooms = $allRooms->filter(fn($r) => (int)$r->floor === 2)->values();

        $slides = [];
        $maxSlides = max(
          (int) ceil($floor1Rooms->count() / 3),
          (int) ceil($floor2Rooms->count() / 3)
        );

        if ($maxSlides > 0) {
          for ($i = 0; $i < $maxSlides; $i++) {
            $sFloor1 = $floor1Rooms->slice($i * 3, 3)->values();
            $sFloor2 = $floor2Rooms->slice($i * 3, 3)->values();
            $slides[] = [
              'floor1' => $sFloor1,
              'floor2' => $sFloor2,
            ];
          }
        } else {
          $chunks = $allRooms->chunk(6);
          foreach ($chunks as $chunk) {
            $slides[] = [
              'floor1' => $chunk->filter(fn($r) => (int)$r->floor === 1)->values(),
              'floor2' => $chunk->filter(fn($r) => (int)$r->floor !== 1)->values(),
            ];
          }
        }
      @endphp

      {{-- SLIDER VIEWPORT & TRACK --}}
      <div class="lr-room-slider-wrapper">
        <div class="lr-room-slider-viewport" id="lrRoomSliderViewport">
          <div class="lr-room-slider-track" id="lrRoomSliderTrack">
            @forelse($slides as $slideIdx => $slide)
              <div class="lr-room-slide {{ $slideIdx === 0 ? 'active' : '' }}" data-slide-index="{{ $slideIdx }}">
                
                {{-- Row 1: 3 Kamar Lantai 1 --}}
                @if($slide['floor1']->isNotEmpty())
                  <div class="lr-slide-floor-row">
                    <div class="lr-slide-floor-title">
                      <span class="lr-floor-tag floor-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Lantai 1
                      </span>
                    </div>
                    <div class="lr-featured-grid">
                      @foreach($slide['floor1'] as $roomIdx => $room)
                        @include('landing._room_card', ['room' => $room, 'roomIdx' => $roomIdx])
                      @endforeach
                    </div>
                  </div>
                @endif

                {{-- Row 2: 3 Kamar Lantai 2 --}}
                @if($slide['floor2']->isNotEmpty())
                  <div class="lr-slide-floor-row" style="{{ $slide['floor1']->isNotEmpty() ? 'margin-top: 26px;' : '' }}">
                    <div class="lr-slide-floor-title">
                      <span class="lr-floor-tag floor-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Lantai 2
                      </span>
                    </div>
                    <div class="lr-featured-grid">
                      @foreach($slide['floor2'] as $roomIdx => $room)
                        @include('landing._room_card', ['room' => $room, 'roomIdx' => $roomIdx])
                      @endforeach
                    </div>
                  </div>
                @endif

              </div>
            @empty
              <div style="width: 100%; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 24px; border: 1.5px dashed #cbd5e1; max-width: 580px; margin: 20px auto;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: #f0fdfa; display: inline-flex; align-items: center; justify-content: center; color: #0a5661; margin-bottom: 14px;">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Daftar Kamar Belum Tersedia</h4>
                <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 20px; line-height: 1.5;">Unit kamar sedang dalam proses pembaruan oleh pengelola. Hubungi kami melalui WhatsApp untuk informasi sewa dan ketersediaan unit terbaru.</p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->owner_phone ?? '6281234567890') }}?text=Halo%20Pengelola%20Kos,%20saya%20ingin%20menanyakan%20ketersediaan%20kamar" target="_blank" class="lr-btn-pill-teal" style="background: #0a5661; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 999px; font-weight: 700; font-size: 0.88rem;">
                  Hubungi Pengelola Kos
                </a>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Bottom Navigation Bar: Dots Indicator di Antara Tombol Kiri dan Kanan --}}
      <div class="lr-slider-bottom-bar" id="lrSliderBottomBar" data-spatial-reveal="section-header">
        <div class="lr-slider-ctrl-group" id="lrSliderBottomCtrl" style="{{ count($slides) <= 1 ? 'display: none;' : '' }}">
          <button type="button" class="lr-slider-btn prev" id="lrBtnRoomPrev" aria-label="Slide Sebelumnya" title="Slide Sebelumnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><polyline points="15 18 9 12 15 6"/></svg>
          </button>

          {{-- Dots Indicator berada tepat di antara tombol panah kiri dan tombol panah kanan --}}
          <div class="lr-dots-indicator" id="lrRoomSliderDots" style="margin: 0; padding: 0 6px;">
            @foreach($slides as $slideIdx => $slide)
              <button type="button" class="lr-dot-btn {{ $slideIdx === 0 ? 'active' : '' }}"
                      data-target-slide="{{ $slideIdx }}"
                      aria-label="Beralih ke Slide {{ $slideIdx + 1 }}"
                      title="Slide {{ $slideIdx + 1 }}">
                <span class="lr-dot"></span>
              </button>
            @endforeach
          </div>

          <button type="button" class="lr-slider-btn next" id="lrBtnRoomNext" aria-label="Slide Selanjutnya" title="Slide Selanjutnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
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
        <div class="lr-bento-header-left">
          <div class="lr-bento-tag-badge" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(235, 110, 75, 0.1); color: var(--coral-primary); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 6px 14px; border-radius: 999px; margin-bottom: 14px; border: 1px solid rgba(235, 110, 75, 0.22);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <span>{{ $landing['bento_tag'] ?? 'Kenyamanan Maksimal' }}</span>
          </div>
          <h2 class="lr-two-tone-title" style="margin: 0 0 14px; font-size: clamp(1.85rem, 3.2vw, 2.5rem); font-weight: 900; line-height: 1.25; letter-spacing: -0.02em;">
            <span class="dark-teal">{{ $landing['bento_title'] ?? 'Untuk Istirahat & Produktivitas' }}</span>
          </h2>
          <p class="lr-bento-header-desc" style="margin: 0; font-size: 0.95rem; line-height: 1.68; color: var(--text-muted); max-width: 540px;">
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
        <div class="lr-gallery-hero-card" data-spatial-reveal="photo-depth" data-stagger="1">
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
          <div class="lr-gallery-photo-card" data-spatial-reveal="photo-depth" data-stagger="2">
            <img src="{{ asset($bentoGallery[0] ?? 'assets/images/hero_scandinavian_room.webp') }}"
                 alt="Foto Galeri 1"
                 loading="lazy"
                 decoding="async"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.webp') }}'">
          </div>
          <div class="lr-gallery-photo-card" data-spatial-reveal="photo-depth" data-stagger="3">
            <img src="{{ asset($bentoGallery[1] ?? 'assets/images/rooms/room_2.webp') }}"
                 alt="Foto Galeri 2"
                 loading="lazy"
                 decoding="async"
                 onerror="this.src='{{ asset('assets/images/room_sample.webp') }}'">
          </div>
        </div>

        {{-- Card 3: 2 Stacked Photo Cards --}}
        <div class="lr-gallery-column">
          <div class="lr-gallery-photo-card" data-spatial-reveal="photo-depth" data-stagger="4">
            <img src="{{ asset($bentoGallery[2] ?? 'assets/images/rooms/room_3.webp') }}"
                 alt="Foto Galeri 3"
                 loading="lazy"
                 decoding="async"
                 onerror="this.src='{{ asset('assets/images/rooms/room_1.webp') }}'">
          </div>
          <div class="lr-gallery-photo-card" data-spatial-reveal="photo-depth" data-stagger="5">
            <img src="{{ asset($bentoGallery[3] ?? 'assets/images/kos_exterior.webp') }}"
                 alt="Foto Galeri 4"
                 loading="lazy"
                 decoding="async"
                 onerror="this.src='{{ asset('assets/images/hero_scandinavian_room.webp') }}'">
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
        $defaultB1 = [
          'name' => 'Kos Mas Robi 1 - Kampus Utama',
          'tagline' => 'Hunian Tenang, Nyaman & 3 Menit ke Kampus Utama',
          'address' => $setting->kos_address ?? 'Jl. Pandega Marta No. 88, Caturtunggal, Depok, Sleman, D.I. Yogyakarta',
          'gmaps_url' => $setting->gmaps_url ?: 'https://maps.google.com/?q=Jl.+Pandega+Marta+No.88+Yogyakarta',
        ];
        $defaultB2 = [
          'name' => 'Kos Mas Robi 2 - Ringroad Exclusive',
          'tagline' => 'Smart Living, Co-Working Space & Fasilitas Modern',
          'address' => 'Jl. Kaliurang KM 12, Sardonoharjo, Ngaglik, Sleman, D.I. Yogyakarta',
          'gmaps_url' => 'https://maps.google.com/?q=Jl.+Kaliurang+KM+12+Yogyakarta',
        ];
        $b1 = array_merge($defaultB1, array_filter($allBranches['lokasi_1'] ?? [], fn($v) => !is_null($v) && $v !== ''));
        $b2 = array_merge($defaultB2, array_filter($allBranches['lokasi_2'] ?? [], fn($v) => !is_null($v) && $v !== ''));
        $embedB1 = $setting->getBranchEmbedUrl('lokasi_1');
        $embedB2 = $setting->getBranchEmbedUrl('lokasi_2');
      @endphp

      <div data-spatial-reveal="section-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
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
        @if($b1ActiveLanding && $b2ActiveLanding)
          <div style="display: flex; gap: 8px; background: #ffffff; padding: 6px; border-radius: 16px; border: 1.5px solid var(--border-subtle); box-shadow: 0 4px 14px rgba(0,0,0,0.04);">
            <button type="button" id="btnLandingLoc1" onclick="switchLandingMap(1)" style="padding: 10px 20px; border-radius: 12px; border: none; font-size: 0.84rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: var(--teal-primary); color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);">
              {{ $b1NameLanding }}
            </button>
            <button type="button" id="btnLandingLoc2" onclick="switchLandingMap(2)" style="padding: 10px 20px; border-radius: 12px; border: none; font-size: 0.84rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--text-dark);">
              {{ $b2NameLanding }}
            </button>
          </div>
        @elseif($b1ActiveLanding)
          <div style="font-size: 0.85rem; font-weight: 700; color: var(--teal-primary); background: #f0fdfa; padding: 8px 16px; border-radius: 12px; border: 1.5px solid #99f6e4;">
            {{ $b1NameLanding }}
          </div>
        @elseif($b2ActiveLanding)
          <div style="font-size: 0.85rem; font-weight: 700; color: #0d9488; background: #f0fdfa; padding: 8px 16px; border-radius: 12px; border: 1.5px solid #99f6e4;">
            {{ $b2NameLanding }}
          </div>
        @endif
      </div>

      <div class="lr-location-grid">

        {{-- Left: Informasi Detail & 4 Editable POI Highlights --}}
        <div class="lr-loc-card-left" data-spatial-reveal="location-left">
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
              @foreach($setting->location_highlights as $poiIdx => $poi)
                <div class="lr-poi-item" data-spatial-reveal="poi-cascade" data-stagger="{{ ($poiIdx % 4) + 1 }}">
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
        <div class="lr-map-card-right" data-spatial-reveal="location-right">
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

        window.currentLandingBranch = '{{ $b1ActiveLanding ? 'Lokasi 1' : ($b2ActiveLanding ? 'Lokasi 2' : 'Lokasi 1') }}';
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
            if (titleEl) titleEl.textContent = branchData2.title;
            if (addrEl) addrEl.textContent = branchData2.address;
            if (linkEl) linkEl.href = branchData2.gmapsUrl;
            if (frameEl) frameEl.src = branchData2.embedUrl;
            if (labelEl) labelEl.textContent = branchData2.label;

            if (btn2) {
              btn2.style.background = '#0d9488';
              btn2.style.color = '#ffffff';
              btn2.style.boxShadow = '0 2px 8px rgba(13,148,136,0.25)';
            }
            if (btn1) {
              btn1.style.background = 'transparent';
              btn1.style.color = 'var(--text-dark)';
              btn1.style.boxShadow = 'none';
            }
          } else {
            window.currentLandingBranch = 'Lokasi 1';
            if (titleEl) titleEl.textContent = branchData1.title;
            if (addrEl) addrEl.textContent = branchData1.address;
            if (linkEl) linkEl.href = branchData1.gmapsUrl;
            if (frameEl) frameEl.src = branchData1.embedUrl;
            if (labelEl) labelEl.textContent = branchData1.label;

            if (btn1) {
              btn1.style.background = 'var(--teal-primary)';
              btn1.style.color = '#ffffff';
              btn1.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
            }
            if (btn2) {
              btn2.style.background = 'transparent';
              btn2.style.color = 'var(--text-dark)';
              btn2.style.boxShadow = 'none';
            }
          }
        }

        @if(!$b1ActiveLanding && $b2ActiveLanding)
          document.addEventListener('DOMContentLoaded', function() {
            switchLandingMap(2);
          });
        @endif
      </script>

    </div>
  </section>

  {{-- =========================================================================
       7. FOUNDER QUOTE & COMMITMENT (EDITABLE DARI ADMIN)
       ========================================================================= --}}
  <section class="lr-quote-section" id="tentang">
    <div class="lr-container">
      <div class="lr-quote-box" data-spatial-reveal="quote-unfold">
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
        <div class="lr-footer-brand" data-spatial-reveal="footer-col" data-stagger="1">
          <div style="display: flex; align-items: center; gap: 11px; margin-bottom: 12px;">
            <img src="{{ asset('assets/images/logo_kos_jember.png') }}" alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}" style="width: 38px; height: 38px; object-fit: contain; border-radius: 11px;">
            <h4 style="margin: 0;">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }} — Kos Jember</h4>
          </div>
          <p style="font-size: 0.88rem; line-height: 1.65; max-width: 440px; color: #94a3b8; margin-bottom: 14px;">
            {{ $landing['footer_desc'] ?? 'Pusat Info Kost Jember terbaik & eksklusif dengan fasilitas lengkap, kamar ber-AC, kamar mandi dalam, WiFi fiber cepat 100 Mbps, dan keamanan 24 jam di kawasan strategis dekat kampus UNEJ & UNMUH Jember.' }}
          </p>
          <div style="font-size: 0.84rem; color: #cbd5e1;">
            {{ $setting->kos_address ?? 'Kawasan Kampus UNEJ & UNMUH, Sumbersari, Jember, Jawa Timur' }}
          </div>
        </div>

        <div class="lr-footer-col" data-spatial-reveal="footer-col" data-stagger="2">
          <h5>Navigasi Halaman</h5>
          <ul class="lr-footer-list">
            <li><a href="#beranda">Beranda Utama</a></li>
            <li><a href="#kamar">Pilihan Unit Kamar</a></li>
            <li><a href="#fasilitas">Fasilitas Kost</a></li>
            <li><a href="#lokasi">Peta & Google Maps</a></li>
            <li><a href="#tentang">Tentang Pengelola</a></li>
          </ul>
        </div>

        <div class="lr-footer-col" data-spatial-reveal="footer-col" data-stagger="3">
          <h5>Hubungi & Akses</h5>
          <ul class="lr-footer-list">
            <li>
              <a href="javascript:void(0)" onclick="openSurveyModal()" title="Jadwalkan Survey Kamar">
                WhatsApp: {{ $setting->owner_phone ?? '6281234567890' }}
              </a>
            </li>
            <li><a href="{{ route('login') }}" onclick="window.f3dNavigateToLogin(event, this.href)">Portal Login Anak Kos</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Login Pengelola (Admin)</a></li>
            <li><a href="#beranda">Kembali ke Atas ↑</a></li>
          </ul>
        </div>
      </div>

      <div class="lr-footer-bottom" data-spatial-reveal="footer-col" data-stagger="4" style="justify-content: center; text-align: center;">
        <div>
          © {{ date('Y') }} {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Seluruh hak cipta dilindungi undang-undang.
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
            <label for="sf_name">Nama Lengkap Calon Penghuni *</label>
            <input type="text" id="sf_name" class="lr-survey-input" placeholder="Tulis Nama Lengkap Anda" required oninput="renderSurveyPreview()">
          </div>
          <div class="lr-survey-form-group">
            <label for="sf_phone">Nomor WhatsApp Aktif *</label>
            <input type="tel" id="sf_phone" class="lr-survey-input" placeholder="Contoh: 08123456789" required oninput="renderSurveyPreview()">
          </div>
        </div>

        <div class="lr-survey-grid-2" style="margin-top: 10px;">
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
          <div class="lr-survey-form-group">
            <label for="sf_branch">Pilihan Lokasi Kost</label>
            <select id="sf_branch" class="lr-survey-input" onchange="renderSurveyPreview()">
              <option value="{{ $b1['name'] ?? 'Lokasi 1' }}">{{ $b1['name'] ?? 'Lokasi 1 (Kampus Utama)' }}</option>
              <option value="{{ $b2['name'] ?? 'Lokasi 2' }}">{{ $b2['name'] ?? 'Lokasi 2 (Ringroad Exclusive)' }}</option>
            </select>
          </div>
        </div>

        <!-- 2. PILIHAN KAMAR -->
        <div class="lr-survey-form-group" style="margin-top: 10px;">
          <label for="sf_room">Pilihan Kamar yang Diminati</label>
          <input type="text" id="sf_room" class="lr-survey-input" placeholder="Contoh: Kamar 104 - Lantai 2" oninput="renderSurveyPreview()">
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

  <!-- =========================================================================
       MODAL: LIGHTBOX PREVIEW FOTO KAMAR & MANUAL SLIDE (LANDING PAGE)
       ========================================================================= -->
  <div class="lr-lightbox-backdrop" id="modalRoomLightbox" style="display: none;">
    <div class="lr-lightbox-card" id="lrLightboxCard">
      <!-- Close button -->
      <button type="button" class="lr-lightbox-close" id="lrLightboxClose" onclick="window.closeRoomImageLightbox()" aria-label="Tutup">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>

      <!-- Main Image Display with Next/Prev Buttons -->
      <div class="lr-lightbox-image-wrap" id="lrLightboxImageWrap">
        <img src="" id="lrLightboxImg" alt="Foto Kamar" class="lr-lightbox-img">
        
        <!-- Prev Button -->
        <button type="button" class="lr-lightbox-arrow prev" id="lrLightboxPrev" aria-label="Foto Sebelumnya">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

        <!-- Next Button -->
        <button type="button" class="lr-lightbox-arrow next" id="lrLightboxNext" aria-label="Foto Berikutnya">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <!-- Badges on top of image -->
        <div class="lr-lightbox-badges">
          <span class="lr-lightbox-badge-branch" id="lrLightboxBranch">KOS UNEJ</span>
          <span class="lr-lightbox-badge-floor" id="lrLightboxFloor">Lantai 1</span>
        </div>

        <!-- Counter Badge bottom-right -->
        <div class="lr-lightbox-counter" id="lrLightboxCounter">Foto 1 dari 3</div>
      </div>

      <!-- Bottom Meta & Dots -->
      <div class="lr-lightbox-footer">
        <div class="lr-lightbox-meta">
          <div>
            <h3 class="lr-lightbox-title" id="lrLightboxTitle">Kamar #101 - VIP AC</h3>
            <div class="lr-lightbox-price" id="lrLightboxPrice">Rp 1.650.000 <span>/ bulan (all-in)</span></div>
          </div>
          <button type="button" class="lr-btn-book-round" id="lrLightboxBookBtn" style="padding: 10px 22px; font-weight: 800; border-radius: 999px;">
            <span>Booking Sekarang</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>

        <div class="lr-lightbox-dots" id="lrLightboxDots"></div>
      </div>
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
      var phoneEl = document.getElementById('sf_phone');
      var jobEl = document.getElementById('sf_job');
      var branchEl = document.getElementById('sf_branch');
      var roomEl = document.getElementById('sf_room');
      var dateEl = document.getElementById('sf_date');
      var timeEl = document.getElementById('sf_time');
      var methodEl = document.getElementById('sf_method');

      return {
        name: nameEl ? nameEl.value.trim() : '',
        phone: phoneEl ? phoneEl.value.trim() : '',
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
      if (!data.name) {
        alert('Mohon tuliskan nama lengkap Anda terlebih dahulu.');
        return;
      }
      var msg = buildSurveyMessageText(data);
      if (data.phone) {
        msg += "\n• No. WhatsApp : " + data.phone;
      }
      var waUrl = 'https://wa.me/' + window.kosWaPhone + '?text=' + encodeURIComponent(msg);

      // Kirim data survey via AJAX ke server agar tercatat dan masuk notifikasi admin
      try {
        fetch('/api/survey/submit', {
          method: 'POST',
          keepalive: true,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            name: data.name,
            phone: data.phone,
            job: data.job,
            branch: data.branch,
            room: data.room,
            date: data.date,
            time: data.time,
            method: data.method,
            message: msg
          })
        }).catch(function(err) {
          console.warn('Gagal mencatat notifikasi survei via AJAX:', err);
        });
      } catch (err) {
        console.warn('Survey dispatch error:', err);
      }

      // Buka aplikasi WhatsApp
      window.open(waUrl, '_blank');

      // Tampilkan feedback sukses di tombol modal & tutup
      var btnSubmit = document.querySelector('#landingSurveyForm button[type="submit"]');
      if (btnSubmit) {
        var origHtml = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<span>Terkirim ke Admin & Menuju WhatsApp...</span>';
        btnSubmit.disabled = true;
        setTimeout(function() {
          btnSubmit.innerHTML = origHtml;
          btnSubmit.disabled = false;
          closeSurveyModal();
        }, 1500);
      }
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

  {{-- Slider & Client-side Filtering Script (Auto Slide, Zero-Reload Filter, Bottom Controls) --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var track = document.getElementById('lrRoomSliderTrack');
      var viewport = document.getElementById('lrRoomSliderViewport');
      var dotsContainer = document.getElementById('lrRoomSliderDots');
      var bottomCtrl = document.getElementById('lrSliderBottomCtrl');
      var currentSlideNumEl = document.getElementById('lrCurrentSlideNum');
      var totalSlidesNumEl = document.getElementById('lrTotalSlidesNum');
      var btnPrev = document.getElementById('lrBtnRoomPrev');
      var btnNext = document.getElementById('lrBtnRoomNext');

      var currentIndex = 0;
      var totalSlides = document.querySelectorAll('.lr-room-slide').length;
      var autoSlideInterval = null;
      var currentLandingLocation = 'all';
      var currentLandingType = 'all';

      // 1. COLLECT ALL ROOM CARD ELEMENTS AS PERSISTENT TEMPLATES
      var allCardsData = [];
      document.querySelectorAll('.lr-card-featured').forEach(function(card) {
        allCardsData.push({
          node: card.cloneNode(true),
          location: card.getAttribute('data-location') || 'Lokasi 1',
          type: card.getAttribute('data-type') || '',
          floor: card.getAttribute('data-floor') || '1',
          number: card.getAttribute('data-number') || ''
        });
      });

      // 2. SLIDE UPDATE FUNCTION
      function updateSlide(index) {
        var slides = document.querySelectorAll('.lr-room-slide');
        totalSlides = slides.length;
        if (totalSlides === 0) return;

        if (index < 0) index = 0;
        if (index >= totalSlides) index = totalSlides - 1;
        currentIndex = index;

        if (track) {
          track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        }

        slides.forEach(function(s, idx) {
          s.classList.toggle('active', idx === currentIndex);
        });

        if (currentSlideNumEl) currentSlideNumEl.textContent = (currentIndex + 1);
        if (totalSlidesNumEl) totalSlidesNumEl.textContent = totalSlides;

        var dotBtns = document.querySelectorAll('#lrRoomSliderDots .lr-dot-btn');
        dotBtns.forEach(function(dot, idx) {
          dot.classList.toggle('active', idx === currentIndex);
        });

        var isFirst = (currentIndex === 0);
        var isLast = (currentIndex === totalSlides - 1);
        if (btnPrev) btnPrev.disabled = isFirst;
        if (btnNext) btnNext.disabled = isLast;

        // Picu animasi masuk spasial untuk kartu kamar pada slide aktif
        var activeSlide = slides[currentIndex];
        if (activeSlide) {
          activeSlide.querySelectorAll('[data-spatial-reveal]').forEach(function(el) {
            if (!el.classList.contains('lr-revealed')) {
              el.classList.add('lr-revealed');
              setTimeout(function() {
                el.classList.add('lr-revealed-ready');
              }, 1100);
            }
          });
        }
      }

      // 3. AUTO SLIDE LOGIC (Otomatis Slide)
      function startAutoSlide() {
        stopAutoSlide();
        if (totalSlides > 1) {
          autoSlideInterval = setInterval(function() {
            var nextIndex = (currentIndex + 1) % totalSlides;
            updateSlide(nextIndex);
          }, 4500);
        }
      }

      function stopAutoSlide() {
        if (autoSlideInterval) {
          clearInterval(autoSlideInterval);
          autoSlideInterval = null;
        }
      }

      // Auto-slide continues seamlessly; pauses only on interactive bottom control hover
      var bottomBar = document.getElementById('lrSliderBottomBar');
      if (bottomBar) {
        bottomBar.addEventListener('mouseenter', stopAutoSlide);
        bottomBar.addEventListener('mouseleave', function() {
          if (!isLightboxOpen) startAutoSlide();
        });
      }

      // Button handlers
      function goPrev() {
        if (currentIndex > 0) {
          updateSlide(currentIndex - 1);
        }
      }
      function goNext() {
        if (currentIndex < totalSlides - 1) {
          updateSlide(currentIndex + 1);
        }
      }

      if (btnPrev) btnPrev.addEventListener('click', function(e) { e.preventDefault(); goPrev(); });
      if (btnNext) btnNext.addEventListener('click', function(e) { e.preventDefault(); goNext(); });

      // Attach click to dots
      function bindDots() {
        document.querySelectorAll('#lrRoomSliderDots .lr-dot-btn').forEach(function(btn) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            var target = parseInt(this.getAttribute('data-target-slide'), 10);
            if (!isNaN(target)) updateSlide(target);
          });
        });
      }
      bindDots();

      // Touch swipe support for mobile
      var touchStartX = 0;
      var touchEndX = 0;
      if (viewport) {
        viewport.addEventListener('touchstart', function(e) {
          touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        viewport.addEventListener('touchend', function(e) {
          touchEndX = e.changedTouches[0].screenX;
          var diff = touchStartX - touchEndX;
          if (Math.abs(diff) > 40) {
            if (diff > 0) {
              goNext();
            } else {
              goPrev();
            }
          }
        }, { passive: true });
      }

      // 4. CLIENT-SIDE ZERO-RELOAD FILTERING (Semua Cabang, KOS UNEJ, KOS UNMUH, & Tipe Kamar)
      function rebuildLandingSlider() {
        if (!track || allCardsData.length === 0) return;

        // Filter cards matching location and type
        var filtered = allCardsData.filter(function(item) {
          var matchLoc = (currentLandingLocation === 'all' || item.location === currentLandingLocation);
          var matchType = (currentLandingType === 'all' || item.type.toLowerCase().includes(currentLandingType.toLowerCase()));
          return matchLoc && matchType;
        });

        // Split into floor 1 and floor 2
        var f1 = filtered.filter(function(i) { return String(i.floor) === '1'; });
        var f2 = filtered.filter(function(i) { return String(i.floor) === '2'; });
        var f3 = filtered.filter(function(i) { return String(i.floor) === '3'; });

        var maxSlides = Math.max(
          Math.ceil(f1.length / 3),
          Math.ceil(f2.length / 3),
          Math.ceil(f3.length / 3),
          1
        );

        track.innerHTML = '';
        if (filtered.length === 0) {
          track.innerHTML = `
            <div style="width: 100%; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 20px; border: 1.5px dashed var(--border-subtle); margin: 10px;">
              <h4 style="font-size: 1.15rem; color: var(--text-dark); margin-bottom: 6px;">Unit Kamar Tidak Ditemukan</h4>
              <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 16px;">Silakan pilih cabang atau tipe kamar lain untuk melihat unit yang tersedia.</p>
              <button type="button" class="lr-btn-pill-teal" onclick="window.resetLandingFilters()" style="background: var(--teal-primary); display: inline-flex; border: none; cursor: pointer; padding: 10px 22px; color: #fff; border-radius: 999px; font-weight: 700;">Tampilkan Semua Kamar</button>
            </div>
          `;
          totalSlides = 1;
          if (bottomCtrl) bottomCtrl.style.display = 'none';
          if (dotsContainer) dotsContainer.innerHTML = '';
          stopAutoSlide();
          return;
        }

        // Build slide elements
        for (var sIdx = 0; sIdx < maxSlides; sIdx++) {
          var slideEl = document.createElement('div');
          slideEl.className = 'lr-room-slide' + (sIdx === 0 ? ' active' : '');
          slideEl.setAttribute('data-slide-index', sIdx);

          var sF1 = f1.slice(sIdx * 3, (sIdx + 1) * 3);
          var sF2 = f2.slice(sIdx * 3, (sIdx + 1) * 3);
          var sF3 = f3.slice(sIdx * 3, (sIdx + 1) * 3);

          if (sF1.length > 0) {
            var f1Row = document.createElement('div');
            f1Row.className = 'lr-slide-floor-row';
            f1Row.innerHTML = `
              <div class="lr-slide-floor-title">
                <span class="lr-floor-tag floor-1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  Lantai 1
                </span>
              </div>
            `;
            var f1Grid = document.createElement('div');
            f1Grid.className = 'lr-featured-grid';
            sF1.forEach(function(item) { f1Grid.appendChild(item.node.cloneNode(true)); });
            f1Row.appendChild(f1Grid);
            slideEl.appendChild(f1Row);
          }

          if (sF2.length > 0) {
            var f2Row = document.createElement('div');
            f2Row.className = 'lr-slide-floor-row';
            if (sF1.length > 0) f2Row.style.marginTop = '26px';
            f2Row.innerHTML = `
              <div class="lr-slide-floor-title">
                <span class="lr-floor-tag floor-2">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 6"/></svg>
                  Lantai 2
                </span>
              </div>
            `;
            var f2Grid = document.createElement('div');
            f2Grid.className = 'lr-featured-grid';
            sF2.forEach(function(item) { f2Grid.appendChild(item.node.cloneNode(true)); });
            f2Row.appendChild(f2Grid);
            slideEl.appendChild(f2Row);
          }

          if (sF3.length > 0) {
            var f3Row = document.createElement('div');
            f3Row.className = 'lr-slide-floor-row';
            if (sF1.length > 0 || sF2.length > 0) f3Row.style.marginTop = '26px';
            f3Row.innerHTML = `
              <div class="lr-slide-floor-title">
                <span class="lr-floor-tag" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                  Lantai 3
                </span>
              </div>
            `;
            var f3Grid = document.createElement('div');
            f3Grid.className = 'lr-featured-grid';
            sF3.forEach(function(item) { f3Grid.appendChild(item.node.cloneNode(true)); });
            f3Row.appendChild(f3Grid);
            slideEl.appendChild(f3Row);
          }

          track.appendChild(slideEl);
        }

        totalSlides = maxSlides;

        // Rebuild dots
        if (dotsContainer) {
          dotsContainer.innerHTML = '';
          for (var dIdx = 0; dIdx < totalSlides; dIdx++) {
            var dotBtn = document.createElement('button');
            dotBtn.type = 'button';
            dotBtn.className = 'lr-dot-btn' + (dIdx === 0 ? ' active' : '');
            dotBtn.setAttribute('data-target-slide', dIdx);
            dotBtn.setAttribute('aria-label', 'Beralih ke Slide ' + (dIdx + 1));
            dotBtn.setAttribute('title', 'Slide ' + (dIdx + 1));
            dotBtn.innerHTML = '<span class="lr-dot"></span>';
            dotsContainer.appendChild(dotBtn);
          }
          bindDots();
        }

        // Bottom control visibility
        if (bottomCtrl) {
          bottomCtrl.style.display = totalSlides > 1 ? 'inline-flex' : 'none';
        }
        if (totalSlidesNumEl) totalSlidesNumEl.textContent = totalSlides;
        if (currentSlideNumEl) currentSlideNumEl.textContent = '1';

        // Reset slide position & start auto slide
        updateSlide(0);
        if (window.triggerSpatialReveals) {
          window.triggerSpatialReveals();
        }
        startAutoSlide();
      }

      // Filter Handlers
      window.filterLandingRoomsLocation = function(loc, btn) {
        currentLandingLocation = loc;
        document.querySelectorAll('#landingBranchFilters .lr-filter-pill-branch').forEach(function(b) {
          b.classList.remove('active');
          b.style.background = '#ffffff';
          b.style.boxShadow = 'none';
          if (b.getAttribute('data-location') === 'Lokasi 2') {
            b.style.color = '#0284c7';
            b.style.border = '1px solid rgba(2,132,199,0.15)';
          } else {
            b.style.color = '#0a5661';
            b.style.border = '1px solid rgba(10,86,97,0.15)';
          }
        });

        if (btn) {
          btn.classList.add('active');
          if (loc === 'Lokasi 2') {
            btn.style.background = '#0284c7';
            btn.style.color = '#ffffff';
            btn.style.boxShadow = '0 2px 8px rgba(2,132,199,0.25)';
            btn.style.border = 'none';
          } else {
            btn.style.background = 'var(--teal-primary)';
            btn.style.color = '#ffffff';
            btn.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
            btn.style.border = 'none';
          }
        }
        rebuildLandingSlider();
      };

      window.filterLandingRoomsType = function(type, btn) {
        currentLandingType = type;
        document.querySelectorAll('#landingTypeFilters .lr-filter-pill-type').forEach(function(b) {
          b.classList.remove('active');
          b.style.background = '#ffffff';
          b.style.color = 'var(--text-dark)';
          b.style.border = '1px solid var(--border-subtle)';
          b.style.boxShadow = 'none';
        });

        if (btn) {
          btn.classList.add('active');
          btn.style.background = 'var(--teal-primary)';
          btn.style.color = '#ffffff';
          btn.style.border = 'none';
          btn.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
        }
        rebuildLandingSlider();
      };

      window.resetLandingFilters = function() {
        var allLocBtn = document.querySelector('#landingBranchFilters .lr-filter-pill-branch[data-location="all"]');
        var allTypeBtn = document.querySelector('#landingTypeFilters .lr-filter-pill-type[data-type="all"]');
        if (allLocBtn) window.filterLandingRoomsLocation('all', allLocBtn);
        if (allTypeBtn) window.filterLandingRoomsType('all', allTypeBtn);
      };

      // =========================================================================
      // LIGHTBOX POPUP PREVIEW FOTO KAMAR & MANUAL SLIDER (LANDING PAGE)
      // =========================================================================
      var currentLightboxImages = [];
      var currentLightboxIndex = 0;
      var isLightboxOpen = false;
      var lightboxLeaveTimer = null;

      window.openRoomImageLightbox = function(el, evt) {
        if (evt) {
          evt.preventDefault();
          evt.stopPropagation();
        }
        if (!el) return;

        // Hentikan auto-slide slider utama saat popup terbuka
        stopAutoSlide();

        var roomNumber = el.getAttribute('data-room-number') || '';
        var roomType = el.getAttribute('data-room-type') || '';
        var roomFloor = el.getAttribute('data-room-floor') || '1';
        var roomBranch = el.getAttribute('data-room-branch') || '';
        var roomPrice = el.getAttribute('data-room-price') || '';
        var imagesRaw = el.getAttribute('data-images') || '[]';
        var images = [];
        try {
          images = JSON.parse(imagesRaw);
        } catch (err) {
          var sImg = el.querySelector('img')?.src;
          images = sImg ? [sImg] : ['/assets/images/room_sample.jpg'];
        }
        if (!Array.isArray(images) || images.length === 0) {
          var sImg2 = el.querySelector('img')?.src;
          images = sImg2 ? [sImg2] : ['/assets/images/room_sample.jpg'];
        }

        var activeIdx = parseInt(el.getAttribute('data-active-index') || '0', 10);
        currentLightboxImages = images;
        currentLightboxIndex = (activeIdx >= 0 && activeIdx < images.length) ? activeIdx : 0;
        currentLightboxActiveTrigger = el;
        isLightboxOpen = true;

        var titleEl = document.getElementById('lrLightboxTitle');
        if (titleEl) titleEl.textContent = 'Kamar #' + roomNumber + ' - ' + roomType;
        var branchEl = document.getElementById('lrLightboxBranch');
        if (branchEl) branchEl.textContent = roomBranch;
        var floorEl = document.getElementById('lrLightboxFloor');
        if (floorEl) floorEl.textContent = 'Lantai ' + roomFloor;
        var priceEl = document.getElementById('lrLightboxPrice');
        if (priceEl) priceEl.innerHTML = roomPrice + ' <span>/ bulan (all-in)</span>';

        var bookBtn = document.getElementById('lrLightboxBookBtn');
        if (bookBtn) {
          bookBtn.onclick = function() {
            window.closeRoomImageLightbox();
            if (typeof window.openSurveyModal === 'function') {
              window.openSurveyModal({
                room: 'Kamar #' + roomNumber + ' - ' + roomType + ' (Lantai ' + roomFloor + ')',
                branch: roomBranch
              });
            }
          };
        }

        renderLightboxSlide(currentLightboxIndex);

        var modal = document.getElementById('modalRoomLightbox');
        if (modal) {
          modal.style.display = 'flex';
          modal.offsetHeight; // force reflow
          modal.classList.add('active');
        }
      };

      function renderLightboxSlide(idx) {
        if (!currentLightboxImages || currentLightboxImages.length === 0) return;
        if (idx < 0) idx = currentLightboxImages.length - 1;
        if (idx >= currentLightboxImages.length) idx = 0;
        currentLightboxIndex = idx;

        var imgEl = document.getElementById('lrLightboxImg');
        if (imgEl) {
          imgEl.style.opacity = '0.5';
          imgEl.src = currentLightboxImages[currentLightboxIndex];
          imgEl.onload = function() { imgEl.style.opacity = '1'; };
        }

        var counterEl = document.getElementById('lrLightboxCounter');
        if (counterEl) {
          counterEl.textContent = 'Foto ' + (currentLightboxIndex + 1) + ' dari ' + currentLightboxImages.length;
        }

        var dotsWrap = document.getElementById('lrLightboxDots');
        if (dotsWrap) {
          dotsWrap.innerHTML = '';
          currentLightboxImages.forEach(function(src, dIdx) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'lr-lightbox-dot' + (dIdx === currentLightboxIndex ? ' active' : '');
            dot.setAttribute('aria-label', 'Foto ' + (dIdx + 1));
            dot.onclick = function(e) {
              e.stopPropagation();
              renderLightboxSlide(dIdx);
            };
            dotsWrap.appendChild(dot);
          });
        }
      }

      window.closeRoomImageLightbox = function() {
        if (!isLightboxOpen) return;
        isLightboxOpen = false;
        if (lightboxLeaveTimer) {
          clearTimeout(lightboxLeaveTimer);
          lightboxLeaveTimer = null;
        }

        var modal = document.getElementById('modalRoomLightbox');
        if (modal) {
          modal.classList.remove('active');
          setTimeout(function() {
            modal.style.display = 'none';
          }, 200);
        }

        if (currentLightboxActiveTrigger && typeof window.setCardThumbnailIndex === 'function') {
          window.setCardThumbnailIndex(currentLightboxActiveTrigger, currentLightboxIndex);
        }

        // KEMBALI AUTO SLIDE SESUAI AWAL!
        startAutoSlide();
      };

      // KELUAR DARI AREA GAMBAR (MOUSELEAVE) -> CLOSE POPUP & KEMBALI AUTO SLIDE
      var lbCard = document.getElementById('lrLightboxCard');
      if (lbCard) {
        lbCard.addEventListener('mouseleave', function() {
          lightboxLeaveTimer = setTimeout(function() {
            window.closeRoomImageLightbox();
          }, 200);
        });

        lbCard.addEventListener('mouseenter', function() {
          if (lightboxLeaveTimer) {
            clearTimeout(lightboxLeaveTimer);
            lightboxLeaveTimer = null;
          }
        });
      }

      // KLIK DI LUAR AREA GAMBAR (BACKDROP CLICK) -> CLOSE POPUP & KEMBALI AUTO SLIDE
      var lbBackdrop = document.getElementById('modalRoomLightbox');
      if (lbBackdrop) {
        lbBackdrop.addEventListener('click', function(e) {
          if (e.target === lbBackdrop) {
            window.closeRoomImageLightbox();
          }
        });
      }

      // TOMBOL MANUAL SLIDE (PREVIOUS & NEXT)
      document.getElementById('lrLightboxPrev')?.addEventListener('click', function(e) {
        e.stopPropagation();
        renderLightboxSlide(currentLightboxIndex - 1);
      });
      document.getElementById('lrLightboxNext')?.addEventListener('click', function(e) {
        e.stopPropagation();
        renderLightboxSlide(currentLightboxIndex + 1);
      });

      // NAVIGASI KEYBOARD MANUAL SLIDE & ESC CLOSE
      document.addEventListener('keydown', function(e) {
        if (!isLightboxOpen) return;
        if (e.key === 'ArrowLeft') {
          renderLightboxSlide(currentLightboxIndex - 1);
        } else if (e.key === 'ArrowRight') {
          renderLightboxSlide(currentLightboxIndex + 1);
        } else if (e.key === 'Escape') {
          window.closeRoomImageLightbox();
        }
      });

      // TOUCH SWIPE PADA LAYAR SENTUH MOBILE
      var lbTouchStartX = 0;
      var lbTouchEndX = 0;
      var lbImgWrap = document.getElementById('lrLightboxImageWrap');
      if (lbImgWrap) {
        lbImgWrap.addEventListener('touchstart', function(e) {
          lbTouchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        lbImgWrap.addEventListener('touchend', function(e) {
          lbTouchEndX = e.changedTouches[0].screenX;
          var diff = lbTouchStartX - lbTouchEndX;
          if (Math.abs(diff) > 35) {
            if (diff > 0) {
              renderLightboxSlide(currentLightboxIndex + 1);
            } else {
              renderLightboxSlide(currentLightboxIndex - 1);
            }
          }
        }, { passive: true });
      }

      // =========================================================================
      // MINI PHOTO SLIDER PADA KARTU KAMAR (SAAT DI KECILKAN / PADA CARD)
      // =========================================================================
      window.slideCardThumbnail = function(target, dir, e) {
        if (e) {
          e.stopPropagation();
          e.preventDefault();
        }
        var wrap = (target && target.closest) ? target.closest('.lr-card-thumb-wrap') : target;
        if (!wrap) return;
        var slides = wrap.querySelectorAll('.lr-card-thumb-slide');
        if (slides.length <= 1) return;
        var cur = parseInt(wrap.getAttribute('data-active-index') || '0', 10);
        var next = (cur + dir + slides.length) % slides.length;
        window.setCardThumbnailIndex(wrap, next);
      };

      window.jumpCardThumbnail = function(target, idx, e) {
        if (e) {
          e.stopPropagation();
          e.preventDefault();
        }
        var wrap = (target && target.closest) ? target.closest('.lr-card-thumb-wrap') : target;
        if (!wrap) return;
        window.setCardThumbnailIndex(wrap, idx);
      };

      window.setCardThumbnailIndex = function(wrap, idx) {
        if (!wrap) return;
        var slides = wrap.querySelectorAll('.lr-card-thumb-slide');
        var dots = wrap.querySelectorAll('.lr-thumb-dot');
        var counter = wrap.querySelector('.lr-thumb-counter');
        if (slides.length === 0) return;
        if (idx < 0) idx = slides.length - 1;
        if (idx >= slides.length) idx = 0;

        wrap.setAttribute('data-active-index', idx);
        slides.forEach(function(s, i) {
          s.classList.toggle('active', i === idx);
        });
        dots.forEach(function(d, i) {
          d.classList.toggle('active', i === idx);
        });
        if (counter) {
          counter.textContent = (idx + 1) + '/' + slides.length;
        }
      };

      // TOUCH SWIPE PADA SETIAP FOTO KARTU (MOBILE / LAYAR KECIL / SAAT DIKECILKAN)
      var cardTouchStartX = 0;
      var cardTouchStartY = 0;
      var currentTouchingCard = null;

      document.addEventListener('touchstart', function(e) {
        var wrap = e.target.closest('.lr-card-thumb-wrap');
        if (wrap) {
          currentTouchingCard = wrap;
          cardTouchStartX = e.changedTouches[0].screenX;
          cardTouchStartY = e.changedTouches[0].screenY;
        } else {
          currentTouchingCard = null;
        }
      }, { passive: true });

      document.addEventListener('touchend', function(e) {
        if (!currentTouchingCard) return;
        var wrap = currentTouchingCard;
        currentTouchingCard = null;
        var endX = e.changedTouches[0].screenX;
        var endY = e.changedTouches[0].screenY;
        var diffX = cardTouchStartX - endX;
        var diffY = Math.abs(cardTouchStartY - endY);

        // Jika sapuan horizontal lebih dominan (>30px)
        if (Math.abs(diffX) > 30 && Math.abs(diffX) > diffY) {
          e.stopPropagation();
          if (diffX > 0) {
            window.slideCardThumbnail(wrap, 1, e);
          } else {
            window.slideCardThumbnail(wrap, -1, e);
          }
        }
      }, { passive: true });

      // EVENT DELEGATION GLOBAL UNTUK KLIK THUMBNAIL (MENCAKUP SEMUA KARTU AWAL MAUPUN HASIL FILTER)
      document.addEventListener('click', function(e) {
        var navBtn = e.target.closest('.lr-thumb-nav-btn');
        var dotBtn = e.target.closest('.lr-thumb-dot') || e.target.closest('.lr-thumb-dots');
        if (navBtn || dotBtn) {
          return; // Abaikan navigasi foto kecil agar tidak membuka popup secara tidak sengaja
        }
        var thumb = e.target.closest('.lr-card-thumb-wrap');
        if (thumb) {
          e.preventDefault();
          e.stopPropagation();
          window.openRoomImageLightbox(thumb, e);
        }
      });

      // Initial start
      updateSlide(0);
      startAutoSlide();
    });
  </script>

  <script>
    (function() {
      var is3dActive = true;
      var storedPref = localStorage.getItem('kos_3d_flutter_mode');
      if (storedPref === 'disabled') {
        is3dActive = false;
      }

      function apply3DState() {
        if (is3dActive) {
          document.body.classList.add('flutter-3d-enabled');
        } else {
          document.body.classList.remove('flutter-3d-enabled');
          document.querySelectorAll('.lr-card-featured, .lr-search-card, .lr-loc-card-left, .lr-gallery-hero-card').forEach(function(c) {
            c.style.transform = '';
            c.classList.remove('is-tilting');
          });
        }
      }

      window.toggle3DFlutterMode = function() {
        is3dActive = !is3dActive;
        localStorage.setItem('kos_3d_flutter_mode', is3dActive ? 'enabled' : 'disabled');
        apply3DState();
        if (is3dActive) {
          initAll3DTiltElements();
        }
      };

      // Controller 3D Intro Splash Overlay & Ultra Smooth Page Transition
      var introTimer = null;
      var introAnimRaf = null;

      function setSnakePathProgress(elWhite, elSnake, p, snakeLen) {
        if (!elWhite || !elSnake) return;
        var total = 1000;
        var head = p * total;
        var len = snakeLen || 140;
        var tail = Math.max(0, head - len);

        if (p <= 0) {
          elWhite.style.strokeDasharray = '0 1000';
          elWhite.style.strokeDashoffset = '0';
          elSnake.style.strokeDasharray = '0 1000';
          elSnake.style.strokeDashoffset = '0';
          elSnake.style.opacity = '0';
          return;
        }

        // 1. Garis putih muncul dari awal sampai kepala ular
        elWhite.style.strokeDasharray = head + ' ' + total;
        elWhite.style.strokeDashoffset = '0';

        // 2. Badan ular berjalan di posisi terdepan
        if (p >= 1) {
          elSnake.style.opacity = '0';
        } else {
          elSnake.style.opacity = '1';
          var body = head - tail;
          elSnake.style.strokeDasharray = '0 ' + tail + ' ' + body + ' ' + total;
          elSnake.style.strokeDashoffset = '0';
        }
      }

      window.dismiss3dIntro = function(isManualSkip) {
        if (introTimer) clearTimeout(introTimer);
        if (introAnimRaf) cancelAnimationFrame(introAnimRaf);

        var ov = document.getElementById('flutter3dIntroOverlay');
        if (!ov) return;

        ov.classList.add('smooth-exit');
        document.documentElement.classList.remove('f3d-intro-active');
        document.documentElement.classList.add('f3d-intro-skip');
        document.body.classList.remove('intro-active');
        document.body.classList.add('intro-complete');

        try { sessionStorage.setItem('f3d_intro_shown', '1'); } catch(e) {}

        // Mulai animasi masuk spasial untuk elemen di viewport saat intro memudar
        setTimeout(function() {
          if (window.triggerSpatialReveals) {
            window.triggerSpatialReveals();
          }
        }, 180);

        var delay = isManualSkip ? 400 : 950;
        setTimeout(function() {
          ov.style.display = 'none';
        }, delay);
      };

      function startSnakeIntroSequence() {
        if (introTimer) clearTimeout(introTimer);
        if (introAnimRaf) cancelAnimationFrame(introAnimRaf);

        var pWhiteHouse = document.getElementById('f3dPathHouseWhite');
        var pSnakeHouse = document.getElementById('f3dPathHouseSnake');
        var pWhiteDoor = document.getElementById('f3dPathDoorWhite');
        var pSnakeDoor = document.getElementById('f3dPathDoorSnake');

        // Reset total ke kondisi AWAL: HANYA LOGO TEAL POLOS TANPA GARIS PUTIH SAMA SEKALI
        setSnakePathProgress(pWhiteHouse, pSnakeHouse, 0);
        setSnakePathProgress(pWhiteDoor, pSnakeDoor, 0);

        var tStart = performance.now();
        var blankDelay = 600;     // Logo polos ditampilkan 600ms (agak lebih cepat dan responsif)
        var houseDuration = 2250; // Ular menggambar kontur rumah (2.25s, proporsional dan mengalir luwes)
        var doorDelay = 1550;     // Ular mulai menggambar pintu dalam (delay 1.55s)
        var doorDuration = 1500;  // Durasi ular menggambar lengkungan pintu (1.5s)

        function loop(now) {
          var elapsed = now - tStart;

          // Fase 1: Hanya logo teal yang muncul, garis putih masih 0
          if (elapsed < blankDelay) {
            setSnakePathProgress(pWhiteHouse, pSnakeHouse, 0);
            setSnakePathProgress(pWhiteDoor, pSnakeDoor, 0);
            introAnimRaf = requestAnimationFrame(loop);
            return;
          }

          var tAnim = elapsed - blankDelay;

          // Ular Rumah
          var pHouse = Math.min(1, tAnim / houseDuration);
          setSnakePathProgress(pWhiteHouse, pSnakeHouse, pHouse, 160);

          // Ular Pintu Dalam
          if (tAnim < (doorDelay - blankDelay)) {
            setSnakePathProgress(pWhiteDoor, pSnakeDoor, 0);
          } else {
            var pDoor = Math.min(1, (tAnim - (doorDelay - blankDelay)) / doorDuration);
            setSnakePathProgress(pWhiteDoor, pSnakeDoor, pDoor, 140);
          }

          // Cek kelanjutan animasi
          var isHouseDone = (pHouse >= 1);
          var isDoorDone = ((tAnim - (doorDelay - blankDelay)) >= doorDuration);

          if (!isHouseDone || !isDoorDone) {
            introAnimRaf = requestAnimationFrame(loop);
          } else {
            // Kedua garis putih telah digambar sempurna, ular memudar
            setSnakePathProgress(pWhiteHouse, pSnakeHouse, 1);
            setSnakePathProgress(pWhiteDoor, pSnakeDoor, 1);

            // Picu kilau penyelesaian (Completion Burst & Ambient Aura)
            var badgeEl = document.getElementById('f3dOfficialLogoBadge');
            var containerEl = document.getElementById('f3dIntroContainer');
            if (badgeEl) badgeEl.classList.add('logo-complete-burst');
            if (containerEl) containerEl.classList.add('is-complete');

            // Jeda 550ms untuk menampilkan logo utuh sebelum transisi portal sinematik ke landing page
            introTimer = setTimeout(function() {
              window.dismiss3dIntro(false);
            }, 550);
          }
        }

        introAnimRaf = requestAnimationFrame(loop);
      }

      window.replay3dIntro = function() {
        var ov = document.getElementById('flutter3dIntroOverlay');
        if (!ov) return;

        var badgeEl = document.getElementById('f3dOfficialLogoBadge');
        var containerEl = document.getElementById('f3dIntroContainer');
        if (badgeEl) badgeEl.classList.remove('logo-complete-burst');
        if (containerEl) containerEl.classList.remove('is-complete');

        document.documentElement.classList.remove('f3d-intro-skip');
        document.documentElement.classList.add('f3d-intro-active');
        document.body.classList.add('intro-active');
        document.body.classList.remove('intro-complete');
        ov.classList.remove('smooth-exit');
        ov.style.display = 'flex';

        startSnakeIntroSequence();
      };

      function init3DIntro() {
        var ov = document.getElementById('flutter3dIntroOverlay');
        if (!ov) return;
        var hasShown = false;
        try {
          hasShown = sessionStorage.getItem('f3d_intro_shown') === '1';
        } catch(e) {}

        if (!hasShown && is3dActive) {
          document.documentElement.classList.remove('f3d-intro-skip');
          document.documentElement.classList.add('f3d-intro-active');
          document.body.classList.add('intro-active');
          document.body.classList.remove('intro-complete');
          ov.classList.remove('smooth-exit');
          ov.style.display = 'flex';
          startSnakeIntroSequence();
        } else {
          document.documentElement.classList.remove('f3d-intro-active');
          document.documentElement.classList.add('f3d-intro-skip');
          document.body.classList.add('intro-complete');
          ov.style.display = 'none';
          ov.classList.add('smooth-exit');
        }

        // Interaksi Gerak Mouse Halus pada Badge Logo saat Intro
        var badge = document.getElementById('f3dOfficialLogoBadge');
        if (badge && ov) {
          ov.addEventListener('mousemove', function(e) {
            if (!is3dActive || ov.classList.contains('smooth-exit')) return;
            var xRatio = (e.clientX / window.innerWidth) - 0.5;
            var yRatio = (e.clientY / window.innerHeight) - 0.5;
            badge.style.transform = 'translateY(' + (yRatio * -10).toFixed(2) + 'px) rotateY(' + (xRatio * 18).toFixed(2) + 'deg) rotateX(' + (-yRatio * 18).toFixed(2) + 'deg)';
          });

          ov.addEventListener('mouseleave', function() {
            badge.style.transform = '';
          });
        }
      }

      // Universal 3D Tilt Engine
      function attach3DTilt(element, maxAngle, maxScale, translateZ, hasGlare) {
        if (!element || element._has3dInit) return;
        element._has3dInit = true;

        // Glare kilat dinonaktifkan sesuai permintaan pengguna

        var rafId = null;

        function handleMove(clientX, clientY) {
          if (!is3dActive) return;
          var rect = element.getBoundingClientRect();
          var x = clientX - rect.left;
          var y = clientY - rect.top;

          var xPct = Math.max(0, Math.min(1, x / rect.width));
          var yPct = Math.max(0, Math.min(1, y / rect.height));

          var rotateX = ((0.5 - yPct) * maxAngle).toFixed(2);
          var rotateY = ((xPct - 0.5) * maxAngle).toFixed(2);

          element.style.setProperty('--glare-x', (xPct * 100).toFixed(1) + '%');
          element.style.setProperty('--glare-y', (yPct * 100).toFixed(1) + '%');

          if (rafId) cancelAnimationFrame(rafId);
          rafId = requestAnimationFrame(function() {
            element.style.transform = 'perspective(1100px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale3d(' + maxScale + ', ' + maxScale + ', ' + maxScale + ') translateZ(' + translateZ + 'px)';
          });
        }

        element.addEventListener('mouseenter', function(e) {
          if (!is3dActive) return;
          element.classList.add('is-tilting');
          handleMove(e.clientX, e.clientY);
        });

        element.addEventListener('mousemove', function(e) {
          handleMove(e.clientX, e.clientY);
        });

        element.addEventListener('mouseleave', function() {
          if (!is3dActive) return;
          element.classList.remove('is-tilting');
          if (rafId) cancelAnimationFrame(rafId);
          element.style.transform = 'perspective(1100px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1) translateZ(0px)';
        });

        element.addEventListener('touchstart', function(e) {
          if (!is3dActive) return;
          element.classList.add('is-tilting');
          if (e.touches && e.touches[0]) {
            handleMove(e.touches[0].clientX, e.touches[0].clientY);
          }
        }, { passive: true });

        element.addEventListener('touchmove', function(e) {
          if (!is3dActive) return;
          if (e.touches && e.touches[0]) {
            handleMove(e.touches[0].clientX, e.touches[0].clientY);
          }
        }, { passive: true });

        element.addEventListener('touchend', function() {
          if (!is3dActive) return;
          element.classList.remove('is-tilting');
          element.style.transform = 'perspective(1100px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1) translateZ(0px)';
        });
      }

      function initAll3DTiltElements() {
        // 1. Room Cards
        document.querySelectorAll('.lr-card-featured').forEach(function(card) {
          attach3DTilt(card, 16, 1.025, 12, true);
        });

        // 2. Kotak Pencarian Kamar / Search Box
        document.querySelectorAll('.lr-search-card').forEach(function(box) {
          attach3DTilt(box, 6, 1.01, 15, false);
        });

        // 3. Bento Hero Card
        document.querySelectorAll('.lr-gallery-hero-card').forEach(function(card) {
          attach3DTilt(card, 8, 1.015, 14, false);
        });

        // 4. Kartu 2 Titik Cabang Lokasi
        document.querySelectorAll('.lr-loc-card-left').forEach(function(card) {
          attach3DTilt(card, 7, 1.01, 12, false);
        });
      }

      // =========================================================================
      // KINETIC PRISM SPATIAL REVEAL ENGINE
      // Menjalankan animasi masuk spasial berdimensi 3D dan kilau prisma kristal
      // saat pengunjung scroll masuk ke setiap fitur di landing page.
      // =========================================================================
      var spatialObserver = null;

      function initKineticSpatialReveal() {
        var elements = document.querySelectorAll('[data-spatial-reveal]');
        if (!elements.length) return;

        var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
          elements.forEach(function(el) {
            el.classList.add('lr-revealed');
          });
          return;
        }

        elements.forEach(function(el) {
          if (!el.classList.contains('lr-revealed')) {
            el.classList.add('lr-reveal-init');
          }
        });

        if (!spatialObserver) {
          spatialObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting) {
                var el = entry.target;
                el.classList.add('lr-revealed');
                spatialObserver.unobserve(el);

                setTimeout(function() {
                  el.classList.add('lr-revealed-ready');
                }, 1100);
              }
            });
          }, {
            root: null,
            rootMargin: '0px 0px -40px 0px',
            threshold: 0.08
          });
        }

        elements.forEach(function(el) {
          if (!el.classList.contains('lr-revealed')) {
            spatialObserver.observe(el);
          }
        });
      }

      window.triggerSpatialReveals = function() {
        initKineticSpatialReveal();
      };

      document.addEventListener('DOMContentLoaded', function() {
        apply3DState();
        init3DIntro();
        initAll3DTiltElements();

        var hasShown = false;
        try { hasShown = sessionStorage.getItem('f3d_intro_shown') === '1'; } catch(e) {}
        if (hasShown || !is3dActive) {
          initKineticSpatialReveal();
        }
      });

      // 3D Navigation Departure Transition to Login
      window.f3dNavigateToLogin = function(e, url) {
        if (e && e.preventDefault) e.preventDefault();
        try {
          sessionStorage.setItem('f3d_from_landing', '1');
          sessionStorage.removeItem('f3d_login_shown');
        } catch(err) {}
        document.body.style.transition = 'opacity 0.28s ease, transform 0.28s cubic-bezier(0.16, 1, 0.3, 1)';
        document.body.style.opacity = '0';
        document.body.style.transform = 'scale(0.98)';
        setTimeout(function() {
          window.location.href = url;
        }, 220);
      };

      // Sinkronisasi bila slide room berganti
      setInterval(function() {
        if (is3dActive) initAll3DTiltElements();
      }, 1500);
    })();
  </script>

</body>
</html>
