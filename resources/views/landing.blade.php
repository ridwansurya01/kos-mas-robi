<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="description" content="Kos Mas Robi — Hunian kost eksklusif modern, nyaman, full furnished, WiFi kencang, dan aman 24 jam.">
  <meta name="theme-color" content="#0f172a">
  <title>{{ $setting->kos_name ?? 'Kos Mas Robi' }} — Kost Eksklusif & Nyaman</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/></svg>">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

  {{-- ========================================================= NAVBAR ========================================================= --}}
  <nav class="lp-navbar">
    <div class="lp-container lp-nav-inner">
      <a href="{{ route('home') }}" class="lp-brand">
        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3); flex-shrink: 0;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
            <path d="M9 21V12h6v9"/>
          </svg>
        </div>
        <div>
          <div class="lp-brand-name">{{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
          <div class="lp-brand-sub">Exclusive Living</div>
        </div>
      </a>

      <ul class="lp-nav-links">
        <li><a href="#beranda" class="active">Beranda</a></li>
        <li><a href="#kamar">Pilihan Kamar</a></li>
        <li><a href="#fasilitas">Fasilitas</a></li>
        <li><a href="#tentang">Tentang Kami</a></li>
        <li><a href="#kontak">Kontak</a></li>
      </ul>

      <div class="lp-nav-actions">
        @auth
          @if(Auth::user()->isOwner())
            <a href="{{ route('admin.dashboard') }}" class="lp-btn-outline">
              ⚙️ Dashboard Admin
            </a>
          @else
            <a href="{{ route('tenant.portal') }}" class="lp-btn-outline">
              🚪 Portal Anak Kos
            </a>
          @endif
        @else
          <a href="{{ route('login') }}" class="lp-btn-outline">
            Login Penghuni
          </a>
        @endauth
        <a href="#kamar" class="lp-btn-dark">
          Cari Kamar
        </a>
      </div>
    </div>
  </nav>

  {{-- ========================================================= HERO SECTION ========================================================= --}}
  <header class="lp-hero-section" id="beranda">
    <div class="lp-container lp-hero-grid">
      
      {{-- Hero Left Content --}}
      <div class="lp-hero-content">
        <div class="lp-hero-badge">
          <span>✨</span>
          <span>Hunian Nyaman, Tenang & Strategis</span>
        </div>

        <h1 class="lp-hero-title">
          Temukan Kamar Kost Yang Nyaman Untukmu
        </h1>

        <p class="lp-hero-desc">
          Mencari kost yang bersih, aman 24 jam dengan fasilitas lengkap dan WiFi kencang?
          {{ $setting->kos_name ?? 'Kos Mas Robi' }} siap menjadi rumah kedua yang nyaman untuk kuliah dan beristirahat.
        </p>

        <div class="lp-hero-cta">
          <a href="#kamar" class="lp-btn-dark" style="padding: 14px 32px; font-size: 0.95rem;">
            Lihat Kamar Kos
          </a>
          <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Mas%20Robi,%20saya%20tertarik%20tanya%20ketersediaan%20kamar%20kost"
             target="_blank" class="lp-btn-outline" style="padding: 14px 28px;">
            <span>💬 Chat Mas Robi</span>
          </a>
        </div>

        {{-- Stats Row (Matching Reference) --}}
        <div class="lp-stats-row">
          <div class="lp-stat-item">
            <div class="lp-stat-num">{{ $totalRooms ?? 16 }}<span>+</span></div>
            <div class="lp-stat-label">Total Kamar Kost</div>
          </div>
          <div class="lp-stat-item">
            <div class="lp-stat-num">{{ $availableCount ?? 5 }}<span>+</span></div>
            <div class="lp-stat-label">Kamar Siap Huni</div>
          </div>
          <div class="lp-stat-item">
            <div class="lp-stat-num">100<span>%</span></div>
            <div class="lp-stat-label">Fasilitas Lengkap</div>
          </div>
        </div>
      </div>

      {{-- Hero Right Image (Modern Architecture Angle) --}}
      <div class="lp-hero-media">
        <div class="lp-hero-img-wrap">
          <img src="{{ asset('assets/images/kos_exterior.jpg') }}"
               alt="Gedung Kos Mas Robi"
               class="lp-hero-img">
        </div>
        <div class="lp-floating-badge">
          <div class="lp-floating-badge-icon">📍</div>
          <div class="lp-floating-badge-text">
            <strong>Lokasi Strategis</strong>
            <span>{{ $setting->address ?? 'Dekat Kampus & Perkantoran' }}</span>
          </div>
        </div>
      </div>

    </div>
  </header>

  {{-- ========================================================= FLOATING SEARCH BAR ========================================================= --}}
  <div class="lp-search-section">
    <div class="lp-container">
      <div class="lp-search-card">
        <div class="lp-search-title">Cari Ketersediaan Kamar Kost</div>
        <form action="{{ route('home') }}#kamar" method="GET" class="lp-search-form">
          {{-- Lantai --}}
          <div class="lp-search-field">
            <select name="floor" aria-label="Pilih Lantai">
              <option value="all" {{ request('floor') == 'all' ? 'selected' : '' }}>🏢 Semua Lantai</option>
              <option value="1" {{ request('floor') == '1' ? 'selected' : '' }}>Lantai 1</option>
              <option value="2" {{ request('floor') == '2' ? 'selected' : '' }}>Lantai 2</option>
            </select>
          </div>

          {{-- Tipe Kamar --}}
          <div class="lp-search-field">
            <select name="type" aria-label="Pilih Tipe Kamar">
              <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>❄️ Semua Tipe Kamar</option>
              <option value="VIP" {{ request('type') == 'VIP' ? 'selected' : '' }}>VIP AC (KM Dalam)</option>
              <option value="Deluxe" {{ request('type') == 'Deluxe' ? 'selected' : '' }}>Deluxe AC</option>
              <option value="Standard" {{ request('type') == 'Standard' ? 'selected' : '' }}>Standard Fan</option>
            </select>
          </div>

          {{-- Budget --}}
          <div class="lp-search-field">
            <select name="budget" aria-label="Pilih Range Biaya">
              <option value="all" {{ request('budget') == 'all' ? 'selected' : '' }}>💰 Semua Budget</option>
              <option value="under15" {{ request('budget') == 'under15' ? 'selected' : '' }}>&lt; Rp 1.500.000 / bln</option>
              <option value="over15" {{ request('budget') == 'over15' ? 'selected' : '' }}>&gt;= Rp 1.500.000 / bln</option>
            </select>
          </div>

          <button type="submit" class="lp-btn-search">
            Cari Kamar Sekarang
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ========================================================= POPULAR ROOMS SECTION ========================================================= --}}
  <section class="lp-popular-section" id="kamar">
    <div class="lp-container">
      <div class="lp-section-header">
        <div>
          <span class="lp-section-sub">— PILIHAN FAVORIT</span>
          <h2 class="lp-section-title">Koleksi Kamar Kos Mas Robi</h2>
        </div>
        <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Mas%20Robi,%20saya%20mau%20lihat%20semua%20kamar%20yang%20tersedia"
           target="_blank" class="lp-explore-link">
          Lihat Semua Kamar →
        </a>
      </div>

      {{-- 3 Column Cards Grid (Matching Reference) --}}
      <div class="lp-rooms-grid">
        @forelse($popularRooms as $room)
          @php
            $imgs = $room->images_list ?? [$room->image ?? 'assets/images/rooms/room_1.jpg'];
            $mainImg = $imgs[0] ?? 'assets/images/rooms/room_1.jpg';
          @endphp
          <div class="lp-room-card">
            <div class="lp-room-media">
              <img src="{{ asset($mainImg) }}"
                   alt="Foto Kamar #{{ $room->number }}"
                   class="lp-room-img"
                   onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">
              
              <span class="lp-room-status-badge {{ $room->status === 'available' ? 'available' : 'occupied' }}">
                {{ $room->status === 'available' ? '● Tersedia' : 'Terisi' }}
              </span>

              <span class="lp-room-floor-badge">
                Lantai {{ $room->floor }}
              </span>
            </div>

            <div class="lp-room-body">
              <div class="lp-room-location">
                <span>📍</span>
                <span>Kamar #{{ $room->number }}</span>
              </div>
              <div class="lp-room-type-tag">
                {{ $room->type }}
              </div>

              {{-- Specs Icons --}}
              <div class="lp-room-specs">
                <div class="lp-room-spec-item" title="Kamar Tidur">
                  <span>🛏️ Kasur</span>
                </div>
                <div class="lp-room-spec-item" title="Pendingin Ruangan">
                  <span>❄️ AC</span>
                </div>
                <div class="lp-room-spec-item" title="Kamar Mandi Dalam">
                  <span>🚿 KM Dalam</span>
                </div>
                <div class="lp-room-spec-item" title="WiFi Cepat">
                  <span>⚡ 100M</span>
                </div>
              </div>

              <div class="lp-room-footer">
                <div class="lp-room-price">
                  Rp {{ number_format($room->price, 0, ',', '.') }}
                  <span>/ bln</span>
                </div>
                <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}?text=Halo%20Mas%20Robi,%20saya%20tertarik%20booking%20Kamar%20%23{{ $room->number }}%20({{ urlencode($room->type) }})"
                   target="_blank" class="lp-btn-book">
                  Pesan Kamar
                </a>
              </div>
            </div>
          </div>
        @empty
          <div style="grid-column: span 3; text-align: center; padding: 40px; color: var(--lp-text-muted);">
            Tidak ditemukan kamar yang cocok dengan kriteria pencarian.
          </div>
        @endforelse
      </div>

    </div>
  </section>

  {{-- ========================================================= FOUNDER QUOTE & TRUST (Matching Reference) ========================================================= --}}
  <section class="lp-quote-section" id="tentang">
    <div class="lp-container">
      <div class="lp-quote-grid">
        
        <div class="lp-quote-founder">
          <div class="lp-founder-name">{{ $setting->bank_holder ?? 'Mas Robi Suryanto' }}</div>
          <div class="lp-founder-title">Pemilik & Pengelola {{ $setting->kos_name ?? 'Kos Mas Robi' }}</div>
        </div>

        <div class="lp-quote-content">
          <span class="lp-quote-mark">“</span>
          <p class="lp-quote-text">
            Kenyamanan, kebersihan, dan keamanan penghuni adalah komitmen utama kami.
            Kami memastikan setiap anak kos merasakan suasana tinggal yang hangat, fasilitas modern tanpa repot,
            dan dukungan pengelola yang selalu siap membantu kapan pun dibutuhkan.
          </p>
        </div>

      </div>

      {{-- Logos / Facilities Row (Matching Bottom Badges in Reference) --}}
      <div class="lp-logos-row" id="fasilitas">
        <div class="lp-logo-item">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
          <span>WiFi 100 Mbps</span>
        </div>
        <div class="lp-logo-item">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Smart Access 24 Jam</span>
        </div>
        <div class="lp-logo-item">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>CCTV Keamanan</span>
        </div>
        <div class="lp-logo-item">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
          <span>Dapur & Kulkas Bersama</span>
        </div>
      </div>

    </div>
  </section>

  {{-- ========================================================= FOOTER ========================================================= --}}
  <footer class="lp-footer" id="kontak">
    <div class="lp-container lp-footer-inner">
      <div>
        © {{ date('Y') }} {{ $setting->kos_name ?? 'Kos Mas Robi' }}. Seluruh hak cipta dilindungi.
      </div>
      <div class="lp-footer-links">
        <a href="https://wa.me/{{ $setting->owner_phone ?? '6281234567890' }}" target="_blank">WhatsApp: {{ $setting->owner_phone ?? '6281234567890' }}</a>
        <span>•</span>
        <a href="{{ route('login') }}">Login Akun</a>
        <span>•</span>
        <a href="#beranda">Kembali ke Atas ↑</a>
      </div>
    </div>
  </footer>

</body>
</html>

