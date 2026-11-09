@php
  $imgs = $room->images_list ?? [$room->image ?? 'assets/images/rooms/room_1.jpg'];
  $assetImgs = array_map(fn($img) => asset($img), $imgs);
  $mainImg = $imgs[0] ?? 'assets/images/rooms/room_1.jpg';
  $isAvail = $room->status === 'available';
  $isLoc2 = ($room->location ?? '') === 'Lokasi 2';
  $b1NameLanding = $setting->branches['lokasi_1']['name'] ?? 'Kos 1 (Kampus Utama)';
  $b2NameLanding = $setting->branches['lokasi_2']['name'] ?? 'Kos 2 (Ringroad Exclusive)';
  $branchName = $isLoc2 ? $b2NameLanding : $b1NameLanding;
  $waveDirection = (($roomIdx ?? 0) % 3 === 0) ? 'room-card-left' : ((($roomIdx ?? 0) % 3 === 1) ? 'room-card-center' : 'room-card-right');
  $staggerVal = (($roomIdx ?? 0) % 3) + 1;
@endphp
<div class="lr-card-featured lr-prism-shimmer" data-spatial-reveal="{{ $waveDirection }}" data-stagger="{{ $staggerVal }}" data-location="{{ $room->location ?? 'Lokasi 1' }}" data-type="{{ $room->type }}" data-floor="{{ $room->floor }}" data-number="{{ $room->number }}" data-price="{{ $room->price }}" style="border-top: 4px solid {{ $isLoc2 ? '#0284c7' : '#0a5661' }};">
  {{-- Efek Kilau Pencahayaan 3D Glare --}}
  <div class="lr-card-3d-glare"></div>
  <div class="lr-card-thumb-wrap"
       style="position: relative; cursor: pointer; overflow: hidden;"
       onclick="window.openRoomImageLightbox(this, event)"
       data-room-number="{{ $room->number }}"
       data-room-type="{{ $room->type }}"
       data-room-floor="{{ $room->floor }}"
       data-room-branch="{{ $branchName }}"
       data-room-price="Rp {{ number_format($room->price, 0, ',', '.') }}"
       data-images='@json($assetImgs)'
       data-active-index="0"
       title="Klik untuk melihat foto kamar">

    {{-- KONTENER FOTO SLIDE (SAAT DI KECILKAN / PADA KARTU) --}}
    <div class="lr-card-thumb-slides">
      @foreach($assetImgs as $imgIdx => $imgUrl)
        <img src="{{ $imgUrl }}"
             alt="Kamar #{{ $room->number }} - Foto {{ $imgIdx + 1 }}"
             class="lr-card-thumb lr-card-thumb-slide {{ $imgIdx === 0 ? 'active' : '' }}"
             data-img-index="{{ $imgIdx }}"
             loading="lazy"
             onerror="this.src='{{ asset('assets/images/room_sample.jpg') }}'">
      @endforeach
    </div>

    @if(count($assetImgs) > 1)
      {{-- Mini Tombol Geser / Slide Foto Kiri & Kanan --}}
      <button type="button" class="lr-thumb-nav-btn prev" onclick="window.slideCardThumbnail(this, -1, event)" aria-label="Foto Sebelumnya" title="Foto Sebelumnya">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button type="button" class="lr-thumb-nav-btn next" onclick="window.slideCardThumbnail(this, 1, event)" aria-label="Foto Selanjutnya" title="Foto Selanjutnya">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      {{-- Mini Indikator Titik (Dots) Slide Foto --}}
      <div class="lr-thumb-dots" onclick="event.stopPropagation()">
        @foreach($assetImgs as $imgIdx => $imgUrl)
          <button type="button" class="lr-thumb-dot {{ $imgIdx === 0 ? 'active' : '' }}"
                  data-idx="{{ $imgIdx }}"
                  onclick="window.jumpCardThumbnail(this, {{ $imgIdx }}, event)"
                  aria-label="Foto {{ $imgIdx + 1 }}">
          </button>
        @endforeach
      </div>

      {{-- Mini Counter Foto --}}
      <span class="lr-thumb-counter">1/{{ count($assetImgs) }}</span>
    @endif

    <div class="lr-thumb-hover-hint">
      <span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
        Lihat Foto
      </span>
    </div>

    <!-- Lokasi Badge -->
    @if($isLoc2)
      <span style="position: absolute; top: 12px; left: 12px; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(6px); color: #7dd3fc; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; z-index: 4; border: 1.5px solid #0284c7; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        {{ $b2NameLanding }}
      </span>
    @else
      <span style="position: absolute; top: 12px; left: 12px; background: rgba(3, 35, 40, 0.9); backdrop-filter: blur(6px); color: #a7f3d0; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; z-index: 4; border: 1.5px solid #10b981; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        {{ $b1NameLanding }}
      </span>
    @endif

    <span class="lr-pill-floor" style="z-index: 4;">
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

      @if($room->status === 'available' && empty($room->booked_by_user_id))
        <button type="button" class="lr-btn-book-round"
                onclick="openSurveyModal({ room: 'Kamar #{{ $room->number }} - {{ $room->type }} (Lantai {{ $room->floor }})', branch: '{{ $room->location ?? 'Lokasi 1' }}' })"
                title="Isi Formulir Survey & Booking">
          <span>Booking</span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      @else
        <button type="button" class="lr-btn-book-round is-occupied" disabled
                title="Kamar ini sudah terisi / disewa oleh penghuni lain"
                style="background: #94a3b8; border-color: #94a3b8; cursor: not-allowed; opacity: 0.85; box-shadow: none;">
          <span>Penuh</span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </button>
      @endif
    </div>
  </div>
</div>

