@extends('layouts.app')

@section('title', (!empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING') . ' — Kos Jember')

@section('content')

@php
  $isOwnerUser = Auth::user()->isOwner();
  $roleDestinationText = $isOwnerUser ? 'MENUJU HALAMAN OWNER...' : 'MENUJU HALAMAN ADMIN...';
@endphp

<style>
  /* =========================================================================
     WHITE BACKGROUND LOADING WITH SNAKE TRACER LOGO (LOGIN -> ADMIN / OWNER)
     ========================================================================= */
  .f3d-dash-portal-overlay {
    position: fixed;
    inset: 0;
    z-index: 999999999;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    visibility: visible;
    overflow: hidden;
    cursor: default;
    transition: 
      opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1),
      transform 0.65s cubic-bezier(0.16, 1, 0.3, 1),
      filter 0.65s ease,
      visibility 0.65s ease;
  }

  .f3d-dash-loader-wrap {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 24px;
    transition: transform 0.55s ease, opacity 0.55s ease;
  }

  .f3d-dash-svg-box {
    width: 165px;
    height: 165px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
  }

  .f3d-dash-svg-canvas {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .f3d-dash-faint-track {
    stroke: rgba(10, 86, 97, 0.08);
  }

  .f3d-dash-drawn-green {
    stroke: #0a5661;
  }

  .f3d-dash-snake-tracer {
    filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.75));
  }

  .f3d-dash-loading-track {
    width: 190px;
    height: 3.5px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
    margin: 0 auto 12px;
  }

  .f3d-dash-loading-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #0a5661 0%, #10b981 100%);
    border-radius: 999px;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    transition: width 0.08s linear;
  }

  .f3d-dash-loading-label {
    font-family: 'Outfit', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    color: #0a5661;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0;
  }

  .f3d-dash-portal-overlay.portal-exit {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: scale(1.04);
    filter: blur(8px);
  }

  .f3d-dash-portal-overlay.portal-exit .f3d-dash-loader-wrap {
    transform: scale(1.08);
    opacity: 0;
  }

  .btn-survey-calendar:hover,
  #btnModalAddToCalendar:hover {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
  }
</style>

{{-- WHITE BACKGROUND LOADING: MENUJU HALAMAN ADMIN / OWNER --}}
<div id="f3dDashPortalOverlay" class="f3d-dash-portal-overlay" style="display: none;">
  <div class="f3d-dash-loader-wrap" id="f3dDashLoaderWrap">
    <div class="f3d-dash-svg-box">
      <svg class="f3d-dash-svg-canvas" viewBox="0 0 224 232" fill="none">
        <defs>
          <linearGradient id="dashSnakeGreenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#10b981" />
            <stop offset="50%" stop-color="#34d399" />
            <stop offset="100%" stop-color="#0a5661" />
          </linearGradient>
        </defs>

        <!-- Garis Faint Panduan -->
        <path class="f3d-dash-faint-track" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path class="f3d-dash-faint-track" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Garis Hijau Rumah & Ular -->
        <path id="f3dDashGreenHouse" class="f3d-dash-drawn-green" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dDashSnakeHouse" class="f3d-dash-snake-tracer" pathLength="1000" d="M 65 167 L 65 102 L 113 65 L 159 102 L 159 167 L 65 167 Z" stroke="url(#dashSnakeGreenGrad)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Garis Hijau Pintu Dalam & Ular -->
        <path id="f3dDashGreenDoor" class="f3d-dash-drawn-green" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
        <path id="f3dDashSnakeDoor" class="f3d-dash-snake-tracer" pathLength="1000" d="M 96.5 167 L 96.5 118 C 96.5 110 102 106 113 106 C 124 106 129.5 110 129.5 118 L 129.5 167" stroke="url(#dashSnakeGreenGrad)" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>

    <!-- Tampilan Loading Saja -->
    <div class="f3d-dash-loading-track">
      <div class="f3d-dash-loading-bar" id="f3dDashProgressBar"></div>
    </div>
    <div class="f3d-dash-loading-label">{{ $roleDestinationText }}</div>
  </div>
</div>

<script>
  (function() {
    var overlay = document.getElementById('f3dDashPortalOverlay');
    var bar = document.getElementById('f3dDashProgressBar');
    var gHouse = document.getElementById('f3dDashGreenHouse');
    var sHouse = document.getElementById('f3dDashSnakeHouse');
    var gDoor = document.getElementById('f3dDashGreenDoor');
    var sDoor = document.getElementById('f3dDashSnakeDoor');
    var portalTimer = null;
    var animRaf = null;

    function setSnakePath(baseEl, snakeEl, progress, snakeLen) {
      if (!baseEl || !snakeEl) return;
      var total = 1000;
      snakeLen = snakeLen || 160;
      var head = progress * total;
      var tail = Math.max(0, head - snakeLen);

      if (progress <= 0) {
        baseEl.style.strokeDasharray = '0 1000';
        baseEl.style.strokeDashoffset = '0';
        snakeEl.style.strokeDasharray = '0 1000';
        snakeEl.style.strokeDashoffset = '0';
        snakeEl.style.opacity = '0';
        return;
      }

      baseEl.style.strokeDasharray = head.toFixed(2) + ' ' + total;
      baseEl.style.strokeDashoffset = '0';

      if (progress >= 1) {
        snakeEl.style.opacity = '0';
      } else {
        snakeEl.style.opacity = '1';
        var bodyLen = head - tail;
        snakeEl.style.strokeDasharray = '0 ' + tail.toFixed(2) + ' ' + bodyLen.toFixed(2) + ' ' + total;
        snakeEl.style.strokeDashoffset = '0';
      }
    }

    window.dismissDash3dPortal = function() {
      if (portalTimer) clearTimeout(portalTimer);
      if (animRaf) cancelAnimationFrame(animRaf);
      if (!overlay) return;

      setSnakePath(gHouse, sHouse, 1);
      setSnakePath(gDoor, sDoor, 1);
      if (bar) bar.style.width = '100%';

      overlay.classList.add('portal-exit');
      setTimeout(function() {
        overlay.style.display = 'none';
      }, 650);

      try {
        sessionStorage.removeItem('f3d_from_login');
      } catch(e) {}
    };

    function startDashSnakeAnimation() {
      setSnakePath(gHouse, sHouse, 0);
      setSnakePath(gDoor, sDoor, 0);
      if (bar) bar.style.width = '0%';

      var startTime = performance.now();
      var houseDuration = 1150;
      var doorDelay = 380;
      var doorDuration = 820;
      var totalDuration = 1450;

      function step(now) {
        var elapsed = now - startTime;

        var pHouse = Math.min(1, elapsed / houseDuration);
        setSnakePath(gHouse, sHouse, pHouse, 150);

        if (elapsed < doorDelay) {
          setSnakePath(gDoor, sDoor, 0);
        } else {
          var pDoor = Math.min(1, (elapsed - doorDelay) / doorDuration);
          setSnakePath(gDoor, sDoor, pDoor, 130);
        }

        var pBar = Math.min(1, elapsed / totalDuration);
        if (bar) bar.style.width = (pBar * 100).toFixed(1) + '%';

        if (elapsed < totalDuration) {
          animRaf = requestAnimationFrame(step);
        } else {
          setSnakePath(gHouse, sHouse, 1);
          setSnakePath(gDoor, sDoor, 1);
          if (bar) bar.style.width = '100%';

          portalTimer = setTimeout(function() {
            window.dismissDash3dPortal();
          }, 300);
        }
      }

      animRaf = requestAnimationFrame(step);
    }

    window.replayDashSnakeAnimation = function() {
      if (!overlay) return;
      overlay.style.display = 'flex';
      overlay.classList.remove('portal-exit');
      startDashSnakeAnimation();
    };

    document.addEventListener('DOMContentLoaded', function() {
      var fromLogin = false;
      try {
        fromLogin = sessionStorage.getItem('f3d_from_login') === '1';
      } catch(e) {}

      // HANYA jalankan jika datang dari proses login
      if (!fromLogin) {
        if (overlay) overlay.style.display = 'none';
        return;
      }

      if (overlay) overlay.style.display = 'flex';
      startDashSnakeAnimation();
      try {
        sessionStorage.removeItem('f3d_from_login');
      } catch(e) {}
    });
  })();
</script>

  <script>
    window.initialAdminData = {
      settings: @json($setting),
      rooms: @json($rooms),
      tenants: @json($allTenants ?? []),
      finances: @json($allFinances ?? []),
      maintenance: @json($allMaintenance ?? [])
    };

    window.isOwnerUser = {{ Auth::user()->isOwner() ? 'true' : 'false' }};
    window.switchTab = function(tabId) {
      if (tabId === 'tab-finances' && !window.isOwnerUser) {
        tabId = 'tab-dashboard';
      }
      var navTitles = {
        'tab-dashboard': { title: 'Dashboard', sub: 'Monitoring real-time hunian, status kamar, dan operasional' },
        'tab-rooms': { title: 'Daftar Kamar', sub: 'Daftar seluruh unit kamar, fasilitas, dan tarif sewa' },
        'tab-tenants': { title: 'Data Penghuni', sub: 'Kelola kontak anak kos, sewa aktif, dan pengingat WhatsApp' },
        'tab-surveys': { title: 'Survei Lokasi', sub: 'Kelola jadwal kunjungan dan permohonan survei calon anak kos' },
        'tab-bills': { title: 'Tagihan Sewa', sub: 'Monitoring jatuh tempo sewa, kirim WA pengingat tagihan, dan konfirmasi lunas' },
        'tab-transfers': { title: 'Pindah Kamar', sub: 'Daftar permohonan alih kamar dari anak kos dan persetujuan' },
        'tab-maintenance': { title: 'Keluhan & Servis', sub: 'Daftar laporan fasilitas kos dan penanganan teknisi' },
        'tab-finances': { title: 'Laporan Keuangan', sub: 'Analisis kurva pendapatan bulanan s/d 1 tahun penuh, laba bersih, dan buku kas' },
        'tab-admins': { title: 'Akun Admin', sub: 'Pantau siapa saja yang memiliki akses Admin operasional dan kelola akun staf' },
        'tab-settings': { title: 'Pengaturan Kos', sub: 'Pengaturan profil properti, nomor rekening, dan cadangan data' }
      };
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.toggle('active', pane.id === tabId);
      });
      document.querySelectorAll('.app-sidebar .nav-item').forEach(function(item) {
        item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
      });
      document.querySelectorAll('.mobile-bottom-nav .bottom-nav-item').forEach(function(item) {
        var isAct = item.getAttribute('data-tab') === tabId;
        item.classList.toggle('active', isAct);
        if (isAct && typeof item.scrollIntoView === 'function') {
          try { item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }); } catch(e) {}
        }
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

        document.querySelectorAll('.header-active-page-title, [data-page-title]').forEach(function(el) {
          el.textContent = navTitles[tabId].title;
        });
      }
      if (typeof window.updateSettingsSubChip === 'function') window.updateSettingsSubChip(tabId);
      if (tabId === 'tab-finances' && typeof window.drawRevenueCurve === 'function') {
        setTimeout(window.drawRevenueCurve, 80);
      }
      var sb = document.getElementById('appSidebar');
      if (sb) sb.classList.remove('open');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // =========================================================================
    // FITUR NOTIFIKASI SISTEM (ADMIN & OWNER) - EARLY REGISTRATION
    // =========================================================================
    window.toggleNotifDropdown = function(e) {
      if (e) {
        if (typeof e.preventDefault === 'function') e.preventDefault();
        if (typeof e.stopPropagation === 'function') e.stopPropagation();
      }
      var dd = document.getElementById('dropdownNotifications');
      var btn = document.getElementById('btnToggleNotifications');
      if (!dd) return;
      var isOpen = dd.classList.contains('open');
      if (isOpen) {
        dd.classList.remove('open');
        dd.style.display = 'none';
        if (btn) btn.classList.remove('active');
      } else {
        dd.classList.add('open');
        dd.style.display = 'flex';
        if (btn) btn.classList.add('active');
      }
    };

    window.closeNotifDropdown = function() {
      var dd = document.getElementById('dropdownNotifications');
      var btn = document.getElementById('btnToggleNotifications');
      if (dd) {
        dd.classList.remove('open');
        dd.style.display = 'none';
      }
      if (btn) btn.classList.remove('active');
    };

    window.handleNotifClick = function(tabId) {
      if (window.closeNotifDropdown) window.closeNotifDropdown();
      if (window.switchTab) {
        window.switchTab(tabId);
      }
    };

    window.filterNotifList = function(cat, btn) {
      document.querySelectorAll('.notif-filter-btn').forEach(function(b) { b.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      var items = document.querySelectorAll('#notifListBody .notif-item');
      items.forEach(function(item) {
        var itemCat = item.getAttribute('data-cat');
        if (cat === 'all' || itemCat === cat) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    };

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
      var wrap = document.getElementById('headerNotifWrap');
      if (wrap && !wrap.contains(e.target)) {
        if (window.closeNotifDropdown) window.closeNotifDropdown();
      }
    });

    // Close notification dropdown on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        if (window.closeNotifDropdown) window.closeNotifDropdown();
        if (window.closeSurveyDetailModal) window.closeSurveyDetailModal();
      }
    });

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
        modal.style.setProperty('display', 'flex', 'important');
        modal.style.setProperty('visibility', 'visible', 'important');
        modal.style.setProperty('opacity', '1', 'important');
        modal.style.setProperty('pointer-events', 'auto', 'important');
        modal.style.setProperty('z-index', '2000', 'important');
        document.body.style.overflow = 'hidden';
      }
    };

    window.closeModal = function(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('open');
        modal.style.setProperty('display', 'none', 'important');
        modal.style.setProperty('visibility', 'hidden', 'important');
        modal.style.setProperty('opacity', '0', 'important');
        modal.style.setProperty('pointer-events', 'none', 'important');
        document.body.style.overflow = '';
      }
    };

    // =========================================================================
    // GLOBAL FILTER & NAVIGATION SYSTEM (INSTANT ZERO-LAG DOM FILTERING)
    // =========================================================================

    // 1. DASHBOARD FILTERS (DENAH KAMAR, LANTAI 1, 2 & 3, CABANG, STATUS & SEARCH)
    window.currentDashSearch = '';
    window.currentDashStatus = 'all';
    window.currentDashFloor = 'all';
    window.currentDashLocation = 'all';

    function matchCardStatus(cardStatus, filterStatus) {
      if (!filterStatus || filterStatus === 'all') return true;
      var cs = (cardStatus || '').toLowerCase().trim();
      var fs = filterStatus.toLowerCase().trim();
      if (fs === 'occupied' || fs === 'terisi') return (cs === 'occupied' || cs === 'terisi');
      if (fs === 'available' || fs === 'kosong') return (cs === 'available' || cs === 'kosong');
      if (fs === 'due' || fs === 'jatuh tempo' || fs === 'jatuhtempo') return (cs === 'due' || cs === 'jatuh tempo' || cs === 'jatuhtempo');
      if (fs === 'repair' || fs === 'perbaikan') return (cs === 'repair' || cs === 'perbaikan');
      return cs === fs;
    }

    function matchCardLocation(cardLoc, filterLoc) {
      if (!filterLoc || filterLoc === 'all') return true;
      var cl = (cardLoc || 'Lokasi 1').toLowerCase().trim();
      var fl = filterLoc.toLowerCase().trim();
      if (fl.indexOf('2') !== -1) return cl.indexOf('2') !== -1;
      if (fl.indexOf('1') !== -1) return cl.indexOf('2') === -1;
      return cl === fl;
    }

    function getCardFloor(card) {
      var f = card.getAttribute('data-floor');
      if (f && f !== '0' && f !== '') return String(f);
      var rId = card.getAttribute('data-room-id') || '';
      var num = parseInt(rId.replace(/\D/g, ''), 10);
      if (num >= 200 && num < 300) return '2';
      if (num >= 300 && num < 400) return '3';
      return '1';
    }

    function matchCardFloor(card, filterFloor) {
      if (!filterFloor || filterFloor === 'all') return true;
      return getCardFloor(card) === String(filterFloor);
    }

    window.applyDashboardFilters = function() {
      var q = (window.currentDashSearch || '').toLowerCase().trim();
      var cards = document.querySelectorAll('#tab-dashboard .room-card');
      var visibleCount = 0;

      cards.forEach(function(card) {
        var cStatus = card.getAttribute('data-status') || '';
        var cLoc = card.getAttribute('data-location') || 'Lokasi 1';
        var cRoomId = (card.getAttribute('data-room-id') || '').toLowerCase();
        var cText = (card.innerText || card.textContent || '').toLowerCase();

        var matchStatus = matchCardStatus(cStatus, window.currentDashStatus);
        var matchLoc = matchCardLocation(cLoc, window.currentDashLocation);
        var matchFloor = matchCardFloor(card, window.currentDashFloor);
        var matchSearch = !q || cRoomId.indexOf(q) !== -1 || cText.indexOf(q) !== -1;

        var isVisible = matchStatus && matchLoc && matchFloor && matchSearch;
        if (isVisible) {
          card.classList.remove('filter-hidden', 'd-none');
          card.style.removeProperty('display');
          visibleCount++;
        } else {
          card.classList.add('filter-hidden', 'd-none');
          card.style.setProperty('display', 'none', 'important');
        }
      });

      var emptyState = document.getElementById('dashboardRoomsEmptyState');
      if (emptyState) {
        if (visibleCount === 0) {
          emptyState.style.setProperty('display', 'block', 'important');
        } else {
          emptyState.style.setProperty('display', 'none', 'important');
        }
      }

      document.querySelectorAll('#tab-dashboard .floor-section').forEach(function(sec) {
        var secId = sec.id || '';
        var match = secId.match(/dashboardFloor(\d+)Section/);
        if (match) {
          var fNum = match[1];
          if (parseInt(fNum, 10) >= 3 && sec.querySelectorAll('.room-card').length === 0) {
            sec.style.setProperty('display', 'none', 'important');
            return;
          }
          var hasVisible = Array.from(sec.querySelectorAll('.room-card')).some(function(c) {
            return !c.classList.contains('filter-hidden') && c.style.display !== 'none';
          });
          if ((window.currentDashFloor === 'all' || window.currentDashFloor === fNum) && hasVisible) {
            sec.style.removeProperty('display');
          } else {
            sec.style.setProperty('display', 'none', 'important');
          }
        }
      });
    };

    window.resetDashboardFilters = function() {
      window.currentDashSearch = '';
      var sInput = document.getElementById('desktopGlobalSearch');
      if (sInput) sInput.value = '';
      window.filterDashboardStatus('all');
      window.filterDashboardLocation('all');
      window.filterDashboardFloor('all');
    };

    window.filterDashboardStatus = function(status, btn) {
      window.currentDashStatus = status;
      document.querySelectorAll('#dashboardFilters .status-pill-btn, #dashboardFilters .filter-pill').forEach(function(p) {
        p.classList.remove('active');
      });
      if (btn) {
        btn.classList.add('active');
      } else {
        var target = document.querySelector('#dashboardFilters [data-filter="' + status + '"]');
        if (target) target.classList.add('active');
      }
      window.applyDashboardFilters();
    };

    window.filterDashboardFloor = function(floor, btn) {
      window.currentDashFloor = String(floor);
      document.querySelectorAll('#dashboardFloorFilters .filter-pill').forEach(function(p) {
        p.classList.remove('active');
      });
      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#dashboardFloorFilters .filter-pill[data-dfloor="' + floor + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }
      window.applyDashboardFilters();
    };

    window.filterDashboardLocation = function(loc, btn) {
      window.currentDashLocation = loc;
      document.querySelectorAll('#dashboardLocationFilters .filter-pill').forEach(function(p) {
        p.classList.remove('active');
      });
      if (btn) {
        btn.classList.add('active');
      } else {
        var target = document.querySelector('#dashboardLocationFilters [data-dloc="' + loc + '"]');
        if (target) target.classList.add('active');
      }
      var branchLabel = document.getElementById('branchSummaryDropdownLabel');
      if (branchLabel) {
        if (loc === 'all') branchLabel.textContent = 'Semua Cabang';
        else if (loc === 'Lokasi 1') {
          var n1 = document.querySelector('[data-branch-name="lokasi_1"]');
          branchLabel.textContent = n1 ? n1.textContent.trim() : 'Cabang 1';
        } else if (loc === 'Lokasi 2') {
          var n2 = document.querySelector('[data-branch-name="lokasi_2"]');
          branchLabel.textContent = n2 ? n2.textContent.trim() : 'Cabang 2';
        }
      }
      window.applyDashboardFilters();
    };

    window.handleGlobalHeaderSearch = function(query) {
      window.currentDashSearch = (query || '').toLowerCase().trim();
      if (typeof window.applyDashboardFilters === 'function') {
        window.applyDashboardFilters();
      }
      var roomSearch = document.getElementById('roomSearchInput');
      if (roomSearch && roomSearch.value !== query) {
        roomSearch.value = query;
        if (typeof window.applyRoomMasterFilters === 'function') {
          window.applyRoomMasterFilters();
        }
      }
      var tenantSearch = document.getElementById('tenantSearchInput');
      if (tenantSearch && tenantSearch.value !== query) {
        tenantSearch.value = query;
        if (typeof window.filterTenantCards === 'function') {
          window.filterTenantCards();
        }
      }
    };

    window.openRoomDetail = function(roomNumber) {
      if (window.kosApp && typeof window.kosApp.openRoomDetailModal === 'function') {
        window.kosApp.openRoomDetailModal(roomNumber);
      } else if (typeof window.openRoomDetailModal === 'function') {
        window.openRoomDetailModal(roomNumber);
      }
    };

    window._occIdx = 0;
    window.cycleOccupancyTimeframe = function() {
      var options = [
        { label: '12 Bulan Terakhir', line: 'M 50 120 C 90 125, 120 115, 160 100 C 200 85, 230 110, 270 95 C 310 80, 350 70, 390 75 C 430 80, 460 50, 495 40 C 510 36, 525 36, 530 36', area: 'M 50 120 C 90 125, 120 115, 160 100 C 200 85, 230 110, 270 95 C 310 80, 350 70, 390 75 C 430 80, 460 50, 495 40 C 510 36, 525 36, 530 36 L 530 140 L 50 140 Z' },
        { label: '6 Bulan Terakhir', line: 'M 50 90 C 120 85, 200 70, 280 60 C 360 50, 440 38, 530 32', area: 'M 50 90 C 120 85, 200 70, 280 60 C 360 50, 440 38, 530 32 L 530 140 L 50 140 Z' },
        { label: '3 Bulan Terakhir', line: 'M 50 75 C 150 70, 280 48, 400 42 C 460 38, 500 35, 530 30', area: 'M 50 75 C 150 70, 280 48, 400 42 C 460 38, 500 35, 530 30 L 530 140 L 50 140 Z' },
        { label: 'Bulan Ini', line: 'M 50 48 C 150 45, 300 40, 420 36 C 480 32, 510 30, 530 28', area: 'M 50 48 C 150 45, 300 40, 420 36 C 480 32, 510 30, 530 28 L 530 140 L 50 140 Z' }
      ];
      window._occIdx = (window._occIdx + 1) % options.length;
      var sel = options[window._occIdx];
      var lbl = document.getElementById('occupancyTimeframeLabel');
      if (lbl) lbl.textContent = sel.label;
      var lPath = document.getElementById('occupancyChartLine');
      if (lPath) lPath.setAttribute('d', sel.line);
      var aPath = document.getElementById('occupancyChartArea');
      if (aPath) aPath.setAttribute('d', sel.area);
    };

    window.cycleBranchSummaryFilter = function() {
      var locs = ['all', 'Lokasi 1', 'Lokasi 2'];
      var currentLoc = window.currentDashLocation || 'all';
      var nextIdx = (locs.indexOf(currentLoc) + 1) % locs.length;
      window.filterDashboardLocation(locs[nextIdx]);
    };

    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        var sInput = document.getElementById('desktopGlobalSearch');
        if (sInput) {
          sInput.focus();
          sInput.select();
        }
      }
    });

    // --- EDIT JATUH TEMPO SEWA ---
    window.openEditDueDayModal = function(targetId, tenantName, roomNumber, currentDueDay) {
      var sub = document.getElementById('editDueDaySubtitle');
      if (sub) sub.textContent = 'Kamar #' + roomNumber + ' — ' + (tenantName || 'Penghuni');
      var targetInput = document.getElementById('editDueTargetId');
      if (targetInput) targetInput.value = targetId || roomNumber;
      var roomNumInput = document.getElementById('editDueRoomNumber');
      if (roomNumInput) roomNumInput.value = roomNumber;
      var dueInput = document.getElementById('editDueDayInput');
      if (dueInput) dueInput.value = currentDueDay || 1;
      
      if (typeof window.highlightDuePill === 'function') {
        window.highlightDuePill(currentDueDay || 1);
      }

      window.openModal('modalEditDueDay');
    };

    window.setDueDayInputValue = function(val) {
      var dueInput = document.getElementById('editDueDayInput');
      if (dueInput) {
        dueInput.value = val;
        window.highlightDuePill(val);
      }
    };

    window.highlightDuePill = function(val) {
      document.querySelectorAll('#dueDayQuickPills .due-pill-btn').forEach(function(pill) {
        if (pill.textContent.trim() === 'Tgl ' + val) {
          pill.classList.add('active');
        } else {
          pill.classList.remove('active');
        }
      });
    };

    window.submitEditDueDayForm = async function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var targetId = document.getElementById('editDueTargetId')?.value;
      var roomNumber = document.getElementById('editDueRoomNumber')?.value;
      var dueDay = parseInt(document.getElementById('editDueDayInput')?.value, 10);

      if (!dueDay || dueDay < 1 || dueDay > 31) {
        if (window.showModernAlert) {
          window.showModernAlert('Silakan masukkan tanggal jatuh tempo antara 1 sampai 31.', 'warning', 'Periksa Tanggal');
        } else {
          alert('Silakan masukkan tanggal jatuh tempo antara 1 sampai 31.');
        }
        return;
      }

      var btn = document.getElementById('btnSubmitEditDueDay');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        var res = await fetch('/api/tenants/' + targetId + '/due-day', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ due_day: dueDay })
        });

        var data = await res.json();
        if (res.ok && data.success) {
          window.closeModal('modalEditDueDay');
          var dueTextEls = document.querySelectorAll('#dueDayText_' + roomNumber + ', #billDueDayText_' + roomNumber);
          dueTextEls.forEach(function(el) { el.textContent = dueDay; });

          if (window.showModernAlert) {
            window.showModernAlert('Tanggal jatuh tempo sewa Kamar #' + roomNumber + ' berhasil diubah ke tanggal ' + dueDay + ' setiap bulan!', 'success', 'Jatuh Tempo Diperbarui');
          } else {
            alert('Tanggal jatuh tempo berhasil diubah ke tanggal ' + dueDay + '!');
          }

          setTimeout(function() { window.location.reload(); }, 900);
        } else {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal mengubah tanggal jatuh tempo.', 'error', 'Gagal');
          } else {
            alert(data.message || 'Gagal mengubah tanggal jatuh tempo.');
          }
        }
      } catch (err) {
        console.error('Error updating due day:', err);
        alert('Terjadi kesalahan saat menyimpan perubahan ke server.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Simpan Tanggal Jatuh Tempo';
        }
      }
    };

    // --- EDIT DATA PENGHUNI KOS ---
    window.setEditDueDayInputValue = function(val) {
      var inp = document.getElementById('editTenantDueDay');
      if (inp) {
        inp.value = val;
        window.highlightEditDuePill(val);
      }
    };

    window.highlightEditDuePill = function(val) {
      document.querySelectorAll('#editDueDayQuickPills .due-pill-btn').forEach(function(pill) {
        if (pill.textContent.trim() === 'Tgl ' + val) {
          pill.classList.add('active');
        } else {
          pill.classList.remove('active');
        }
      });
    };

    window.openEditTenantModal = async function(tenantId, roomNumber) {
      var modal = document.getElementById('modalEditTenant');
      if (!modal) return;

      var sub = document.getElementById('editTenantSubtitle');
      if (sub) sub.textContent = 'Kamar #' + roomNumber + ' — Memuat data penghuni...';

      document.getElementById('editTenantId').value = tenantId || '';
      document.getElementById('editTenantOriginalRoomId').value = '';
      document.getElementById('editTenantName').value = '';
      document.getElementById('editTenantPhone').value = '';
      document.getElementById('editTenantIdCard').value = '';
      var photoWrapInit = document.getElementById('editTenantIdCardPhotoWrap');
      if (photoWrapInit) photoWrapInit.style.display = 'none';
      document.getElementById('editTenantStartDate').value = '';
      document.getElementById('editTenantDueDay').value = '1';
      document.getElementById('editTenantRentAmount').value = '';
      document.getElementById('editTenantEmergencyContact').value = '';
      document.getElementById('editTenantEmail').value = '';
      document.getElementById('editTenantPassword').value = '';

      var roomSelect = document.getElementById('editTenantRoomSelect');
      if (roomSelect) {
        roomSelect.innerHTML = '';
        var rooms = (window.appData && window.appData.rooms) || (window.initialAdminData && window.initialAdminData.rooms) || [];
        var loc1 = rooms.filter(function(r) { return (r.location || 'Lokasi 1') === 'Lokasi 1'; });
        var loc2 = rooms.filter(function(r) { return r.location === 'Lokasi 2'; });
        var branch1 = (typeof window.getBranchName === 'function' ? window.getBranchName('Lokasi 1') : 'KOS UNEJ');
        var branch2 = (typeof window.getBranchName === 'function' ? window.getBranchName('Lokasi 2') : 'KOS UNMUH');

        if (loc1.length > 0) {
          var grp1 = document.createElement('optgroup');
          grp1.label = branch1;
          loc1.forEach(function(r) {
            var opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = 'Kamar #' + r.number + ' (' + r.type + ')';
            if (String(r.number) === String(roomNumber) || String(r.id) === String(tenantId)) opt.selected = true;
            grp1.appendChild(opt);
          });
          roomSelect.appendChild(grp1);
        }
        if (loc2.length > 0) {
          var grp2 = document.createElement('optgroup');
          grp2.label = branch2;
          loc2.forEach(function(r) {
            var opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = 'Kamar #' + r.number + ' (' + r.type + ')';
            if (String(r.number) === String(roomNumber) || String(r.id) === String(tenantId)) opt.selected = true;
            grp2.appendChild(opt);
          });
          roomSelect.appendChild(grp2);
        }
      }

      var localRoom = null;
      if (window.appData && window.appData.rooms) {
        localRoom = window.appData.rooms.find(function(r) {
          return String(r.number) === String(roomNumber) || String(r.id) === String(tenantId);
        });
      }
      if (localRoom) {
        if (localRoom.tenant) document.getElementById('editTenantName').value = localRoom.tenant;
        if (localRoom.phone) document.getElementById('editTenantPhone').value = localRoom.phone;
        if (localRoom.startDate) document.getElementById('editTenantStartDate').value = localRoom.startDate;
        if (localRoom.dueDay) {
          document.getElementById('editTenantDueDay').value = localRoom.dueDay;
          window.highlightEditDuePill(localRoom.dueDay);
        }
        if (localRoom.price) document.getElementById('editTenantRentAmount').value = localRoom.price;
        if (sub) sub.textContent = 'Kamar #' + roomNumber + ' — ' + (localRoom.tenant || 'Penghuni');
      }

      window.openModal('modalEditTenant');

      try {
        var target = tenantId || roomNumber;
        var res = await fetch('/api/tenants/' + target);
        if (res.ok) {
          var data = await res.json();
          if (data.success && data.tenant) {
            var t = data.tenant;
            document.getElementById('editTenantId').value = t.id || tenantId || '';
            document.getElementById('editTenantOriginalRoomId').value = t.room_id || '';
            document.getElementById('editTenantName').value = t.name || '';
            document.getElementById('editTenantPhone').value = t.phone || '';
            document.getElementById('editTenantIdCard').value = t.id_card || '';
            var photoWrap = document.getElementById('editTenantIdCardPhotoWrap');
            var photoImg = document.getElementById('editTenantIdCardPhotoImg');
            var photoLink = document.getElementById('editTenantIdCardPhotoLink');
            if (t.id_card_photo) {
              if (photoImg) photoImg.src = t.id_card_photo;
              if (photoLink) photoLink.href = t.id_card_photo;
              if (photoWrap) photoWrap.style.display = 'flex';
            } else if (photoWrap) {
              photoWrap.style.display = 'none';
            }
            document.getElementById('editTenantStartDate').value = t.start_date || '';
            document.getElementById('editTenantDueDay').value = t.due_day || 1;
            window.highlightEditDuePill(t.due_day || 1);
            document.getElementById('editTenantRentAmount').value = t.rent_amount || '';
            document.getElementById('editTenantEmergencyContact').value = t.emergency_contact || '';
            if (t.user_email) document.getElementById('editTenantEmail').value = t.user_email;
            if (t.room_id && roomSelect) {
              roomSelect.value = t.room_id;
            }
            if (sub) sub.textContent = 'Kamar #' + (t.room_number || roomNumber) + ' — ' + t.name;
          }
        }
      } catch (err) {
        console.error('Error fetching tenant:', err);
      }
    };

    window.submitEditTenantForm = async function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var tenantId = document.getElementById('editTenantId').value;
      var roomId = document.getElementById('editTenantRoomSelect').value;
      var name = document.getElementById('editTenantName').value.trim();
      var phone = document.getElementById('editTenantPhone').value.trim();
      var idCard = document.getElementById('editTenantIdCard').value.trim();
      var startDate = document.getElementById('editTenantStartDate').value;
      var dueDay = parseInt(document.getElementById('editTenantDueDay').value, 10);
      var rentAmount = parseFloat(document.getElementById('editTenantRentAmount').value);
      var emergencyContact = document.getElementById('editTenantEmergencyContact').value.trim();
      var email = document.getElementById('editTenantEmail').value.trim();
      var password = document.getElementById('editTenantPassword').value.trim();

      if (!name || !phone || !roomId || !startDate || !dueDay || isNaN(rentAmount)) {
        if (window.showModernAlert) {
          window.showModernAlert('Mohon lengkapi seluruh isian wajib bertanda bintang (*)', 'warning', 'Form Belum Lengkap');
        } else {
          alert('Mohon lengkapi seluruh isian wajib.');
        }
        return;
      }

      var btn = document.getElementById('btnSubmitEditTenant');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        var target = tenantId || roomId;
        var res = await fetch('/api/tenants/' + target + '/update', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            name: name,
            phone: phone,
            id_card: idCard,
            room_id: roomId,
            start_date: startDate,
            due_day: dueDay,
            rent_amount: rentAmount,
            emergency_contact: emergencyContact,
            email: email,
            password: password
          })
        });

        var data = await res.json();
        if (res.ok && data.success) {
          window.closeModal('modalEditTenant');
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Data penghuni berhasil diperbarui!', 'success', 'Berhasil Disimpan');
          } else {
            alert('Data penghuni berhasil diperbarui!');
          }
          setTimeout(function() { window.location.reload(); }, 900);
        } else {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal menyimpan perubahan data penghuni.', 'error', 'Gagal');
          } else {
            alert(data.message || 'Gagal menyimpan perubahan.');
          }
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Simpan Perubahan Penghuni';
          }
        }
      } catch (err) {
        console.error('Error updating tenant:', err);
        alert('Terjadi kesalahan saat menyimpan data penghuni.');
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Simpan Perubahan Penghuni';
        }
      }
    };

    // 2. DAFTAR KAMAR MASTER FILTERS (LANTAI, CABANG, STATUS KAMAR KOSONG/TERISI)
    window.currentRoomFloor = 'all';
    window.currentRoomLocation = 'all';
    window.currentRoomStatus = 'all';

    window.applyRoomMasterFilters = function() {
      var inputEl = document.getElementById('roomSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#roomsMasterTableBody tr');
      var cards = document.querySelectorAll('#roomsMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          if (el.querySelector('td[colspan]')) return;
          var s = ((el.getAttribute('data-search') || '') + ' ' + (el.textContent || '')).toLowerCase();
          var fl = String(el.getAttribute('data-floor') || '1');
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var st = el.getAttribute('data-status') || 'available';

          var matchSearch = !q || s.includes(q);
          var matchFloor = (window.currentRoomFloor === 'all' || fl === window.currentRoomFloor);
          var matchLoc = (window.currentRoomLocation === 'all' || loc === window.currentRoomLocation);
          var matchStatus = (window.currentRoomStatus === 'all' || st === window.currentRoomStatus);

          el.style.display = (matchSearch && matchFloor && matchLoc && matchStatus) ? '' : 'none';
        });
      });

      if (typeof window.renderRoomsMasterTable === 'function' && window.appData && window.appData.rooms) {
        window.renderRoomsMasterTable(q, window.currentRoomFloor, window.currentRoomLocation, window.currentRoomStatus);
      }
    };

    window.filterRoomMasterStatus = function(status, btn) {
      window.currentRoomStatus = status;
      document.querySelectorAll('#roomStatusMasterFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', window.currentRoomFloor, window.currentRoomLocation, window.currentRoomStatus);
      }
    };

    window.filterRoomMasterFloor = function(floor, btn) {
      window.currentRoomFloor = String(floor);
      document.querySelectorAll('#roomTabFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', window.currentRoomFloor, window.currentRoomLocation, window.currentRoomStatus);
      }
    };

    window.filterRoomMasterLocation = function(loc, btn) {
      window.currentRoomLocation = loc;
      document.querySelectorAll('#roomLocationFilters .loc-filter-btn').forEach(function(p) {
        p.classList.remove('active');
        p.style.background = '#ffffff';
        p.style.color = p.getAttribute('data-location') === 'Lokasi 2' ? '#0d9488' : '#0a5661';
        p.style.boxShadow = 'none';
      });
      if (btn) {
        btn.classList.add('active');
        btn.style.background = '#0a5661';
        btn.style.color = '#ffffff';
        btn.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
      }
      window.applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', window.currentRoomFloor, window.currentRoomLocation, window.currentRoomStatus);
      }
    };

    // 3. DATA PENGHUNI FILTERS (AKTIF MENGHUNI, JATUH TEMPO, 2 CABANG)
    window.currentTenantLoc = 'all';
    window.currentTenantStatus = 'all';

    window.applyTenantFilters = function() {
      var inputEl = document.getElementById('tenantSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#tenantsTableBody tr');
      var cards = document.querySelectorAll('#tenantsMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          if (el.querySelector('td[colspan]')) return;
          var s = ((el.getAttribute('data-search') || '') + ' ' + (el.textContent || '')).toLowerCase();
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var status = el.getAttribute('data-status') || 'active';

          var matchSearch = !q || s.includes(q);
          var matchLoc = (window.currentTenantLoc === 'all' || loc === window.currentTenantLoc);
          var matchStatus = (window.currentTenantStatus === 'all' || status === window.currentTenantStatus);

          el.style.display = (matchSearch && (q ? true : (matchLoc && matchStatus))) ? '' : 'none';
        });
      });
    };

    window.filterTenantLocation = function(loc, btn) {
      window.currentTenantLoc = loc;
      document.querySelectorAll('#tenantLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyTenantFilters();
      if (typeof window.renderTenantsTable === 'function') {
        var inputEl = document.getElementById('tenantSearchInput');
        window.renderTenantsTable(inputEl ? inputEl.value : '', window.currentTenantStatus, window.currentTenantLoc);
      }
    };

    window.filterTenantStatus = function(status, btn) {
      window.currentTenantStatus = status;
      document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyTenantFilters();
      if (typeof window.renderTenantsTable === 'function') {
        var inputEl = document.getElementById('tenantSearchInput');
        window.renderTenantsTable(inputEl ? inputEl.value : '', window.currentTenantStatus, window.currentTenantLoc);
      }
    };

    window.filterTenantTable = function() {
      window.applyTenantFilters();
    };

    // 4. TAGIHAN SEWA FILTERS (CICILAN AKTIF, JATUH TEMPO, LUNAS, KOSONG, 2 CABANG)
    window.currentBillingStatus = 'all';
    window.currentBillingLocation = 'all';

    window.applyBillingFilters = function() {
      var inputEl = document.getElementById('billingSearchInput');
      var input = inputEl ? inputEl.value.toLowerCase() : '';
      var rows = document.querySelectorAll('#billingTableBody tr');
      var cards = document.querySelectorAll('#billingMobileCardList .mobile-data-card');

      var paidCount = 0;
      var dueCount = 0;
      var dueNominal = 0;
      var partialCount = 0;
      var partialRemNominal = 0;

      rows.forEach(function(r) {
        var s = (r.getAttribute('data-search') || r.textContent || '').toLowerCase();
        var rLoc = r.getAttribute('data-location') || 'Lokasi 1';
        var rStatus = r.getAttribute('data-status') || '';
        var rPrice = parseInt(r.getAttribute('data-price') || 0, 10);
        var rRem = parseInt(r.getAttribute('data-rem') || 0, 10);

        var matchLoc = (window.currentBillingLocation === 'all' || rLoc === window.currentBillingLocation);
        if (matchLoc) {
          if (rStatus === 'paid') paidCount++;
          if (rStatus === 'due') {
            dueCount++;
            dueNominal += rPrice;
          }
          if (rStatus === 'partial') {
            partialCount++;
            partialRemNominal += rRem;
          }
        }

        var matchSearch = !input || s.includes(input);
        var matchStatus = (window.currentBillingStatus === 'all' || rStatus === window.currentBillingStatus);

        r.style.display = (matchLoc && matchSearch && matchStatus) ? '' : 'none';
      });

      cards.forEach(function(c) {
        var s = (c.getAttribute('data-search') || c.textContent || '').toLowerCase();
        var rLoc = c.getAttribute('data-location') || 'Lokasi 1';
        var rStatus = c.getAttribute('data-status') || '';

        var matchLoc = (window.currentBillingLocation === 'all' || rLoc === window.currentBillingLocation);
        var matchSearch = !input || s.includes(input);
        var matchStatus = (window.currentBillingStatus === 'all' || rStatus === window.currentBillingStatus);

        c.style.display = (matchLoc && matchSearch && matchStatus) ? '' : 'none';
      });

      var paidEl = document.getElementById('billKpiPaidVal');
      var dueEl = document.getElementById('billKpiDueVal');
      var dueNomEl = document.getElementById('billKpiDueNom');
      var partialEl = document.getElementById('billKpiPartialVal');
      var rateEl = document.getElementById('billKpiRateVal');

      if (paidEl) paidEl.textContent = paidCount + ' Kamar';
      if (dueEl) dueEl.textContent = dueCount + ' Kamar';
      if (dueNomEl) dueNomEl.textContent = 'Rp ' + dueNominal.toLocaleString('id-ID');
      if (partialEl) partialEl.textContent = partialCount + ' Kamar';
      if (rateEl) {
        var totOcc = paidCount + dueCount + partialCount;
        var rate = totOcc > 0 ? Math.round((paidCount / totOcc) * 1000) / 10 : 100;
        rateEl.textContent = rate + '%';
      }
    };

    window.filterBillingStatus = function(status, btn) {
      window.currentBillingStatus = status;
      document.querySelectorAll('#billingStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyBillingFilters();
    };

    window.filterBillingLocation = function(loc, btn) {
      window.currentBillingLocation = loc;
      document.querySelectorAll('#billingLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyBillingFilters();
    };

    window.filterBillingTable = function() {
      window.applyBillingFilters();
    };

    // 5. PINDAH KAMAR FILTERS (MENUNGGU, DISETUJUI, DITOLAK, INTERNAL & LINTAS CABANG)
    window.currentTransferStatus = 'all';
    window.currentTransferLocation = 'all';

    window.applyTransferFilters = function() {
      var inputEl = document.getElementById('transferSearchInput');
      var input = inputEl ? inputEl.value.toLowerCase() : '';
      var rows = document.querySelectorAll('#transfersTableBody tr[data-status]');
      var cards = document.querySelectorAll('#transfersMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(r) {
          var searchData = (r.getAttribute('data-search') || r.textContent || '').toLowerCase();
          var rStatus = r.getAttribute('data-status') || '';
          var rLoc = r.getAttribute('data-location') || '';
          var rFrom = r.getAttribute('data-from-loc') || '';
          var rTo = r.getAttribute('data-to-loc') || '';

          var matchSearch = !input || searchData.includes(input);
          var matchStatus = (window.currentTransferStatus === 'all' || rStatus === window.currentTransferStatus);
          var matchLoc = true;
          if (window.currentTransferLocation === 'Lintas Cabang') {
            matchLoc = (rLoc === 'Lintas Cabang');
          } else if (window.currentTransferLocation !== 'all') {
            matchLoc = (rLoc === window.currentTransferLocation || rFrom === window.currentTransferLocation || rTo === window.currentTransferLocation);
          }

          r.style.display = (matchSearch && matchStatus && matchLoc) ? '' : 'none';
        });
      });
    };

    window.filterTransferStatus = function(status, btn) {
      window.currentTransferStatus = status;
      document.querySelectorAll('#transferStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyTransferFilters();
    };

    window.filterTransferLocation = function(loc, btn) {
      window.currentTransferLocation = loc;
      document.querySelectorAll('#transferLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyTransferFilters();
    };

    window.filterTransferTable = function() {
      window.applyTransferFilters();
    };

    // --- MODAL KONFIRMASI PINDAH KAMAR (MODERN POPUP) ---
    window.openConfirmRoomTransferModal = function(idOrEl, status, tenantName, fromRoom, toRoom) {
      var id = idOrEl;
      if (idOrEl && (idOrEl.nodeType || (typeof idOrEl === 'object' && idOrEl.getAttribute))) {
        id = idOrEl.getAttribute('data-id');
        status = idOrEl.getAttribute('data-status');
        tenantName = idOrEl.getAttribute('data-tenant');
        fromRoom = idOrEl.getAttribute('data-from');
        toRoom = idOrEl.getAttribute('data-to');
      }

      var idInput = document.getElementById('confirmTransferId');
      if (idInput) idInput.value = id || '';
      var statusInput = document.getElementById('confirmTransferStatus');
      if (statusInput) statusInput.value = status || 'approved';
      var tenantEl = document.getElementById('confirmTransferTenant');
      if (tenantEl) tenantEl.textContent = tenantName || 'Penghuni';
      var fromRoomEl = document.getElementById('confirmTransferFromRoom');
      if (fromRoomEl) fromRoomEl.textContent = '#' + (fromRoom || '-');
      var toRoomEl = document.getElementById('confirmTransferToRoom');
      if (toRoomEl) toRoomEl.textContent = '#' + (toRoom || '-');

      var titleEl = document.getElementById('confirmTransferTitle');
      var subEl = document.getElementById('confirmTransferSubtitle');
      var headerEl = document.getElementById('confirmTransferHeader');
      var bannerEl = document.getElementById('confirmTransferActionBanner');
      var btnSubmit = document.getElementById('btnSubmitConfirmTransfer');
      var noteInput = document.getElementById('confirmTransferNote');

      if (status === 'approved') {
        if (titleEl) titleEl.textContent = 'Setujui Permohonan Pindah Kamar';
        if (subEl) subEl.textContent = 'Penghuni akan dipindahkan ke kamar baru yang dipilih';
        if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #065f46 0%, #059669 100%)';
        if (bannerEl) {
          bannerEl.style.background = '#dcfce7';
          bannerEl.style.color = '#065f46';
          bannerEl.style.border = '1px solid #86efac';
          bannerEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Anda akan menyetujui mutasi kamar ini. Sistem akan mengalihkan status sewa ke unit tujuan.';
        }
        if (btnSubmit) {
          btnSubmit.textContent = 'Ya, Setujui Permohonan';
          btnSubmit.style.background = '#059669';
        }
        if (noteInput) noteInput.value = 'Pengajuan disetujui, silakan koordinasi kunci kamar baru.';
      } else {
        if (titleEl) titleEl.textContent = 'Tolak Permohonan Pindah Kamar';
        if (subEl) subEl.textContent = 'Permohonan mutasi tidak dapat disetujui saat ini';
        if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #991b1b 0%, #dc2626 100%)';
        if (bannerEl) {
          bannerEl.style.background = '#fee2e2';
          bannerEl.style.color = '#991b1b';
          bannerEl.style.border = '1px solid #fca5a5';
          bannerEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Permohonan mutasi kamar akan ditolak. Penghuni tetap menempati kamar asal.';
        }
        if (btnSubmit) {
          btnSubmit.textContent = 'Ya, Tolak Permohonan';
          btnSubmit.style.background = '#dc2626';
        }
        if (noteInput) noteInput.value = 'Mohon maaf, kamar yang diminta belum tersedia saat ini.';
      }

      window.openModal('modalConfirmRoomTransfer');
    };

    window.submitConfirmRoomTransfer = async function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var id = document.getElementById('confirmTransferId')?.value;
      var status = document.getElementById('confirmTransferStatus')?.value;
      var note = document.getElementById('confirmTransferNote')?.value;
      var btn = document.getElementById('btnSubmitConfirmTransfer');

      if (!id) {
        alert('Data permohonan pindah kamar tidak valid.');
        return;
      }

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        var res = await fetch('/api/room-transfers/' + id + '/status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            status: status,
            admin_note: note
          })
        });
        var data = await res.json();
        if (data.success) {
          window.closeModal('modalConfirmRoomTransfer');
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Status pindah kamar berhasil diperbarui!', 'success', 'Status Berhasil Diperbarui', function() {
              window.location.reload();
            });
          } else {
            alert(data.message || 'Status berhasil diperbarui!');
            window.location.reload();
          }
        } else {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal memperbarui status.', 'error', 'Gagal');
          } else {
            alert(data.message || 'Gagal memperbarui status.');
          }
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan saat memproses status pindah kamar.');
      } finally {
        if (btn) {
          btn.disabled = false;
        }
      }
    };

    function handleTransferAction(id, status) {
      window.openConfirmRoomTransferModal(id, status);
    }
    window.handleTransferAction = handleTransferAction;

    document.addEventListener('click', function(e) {
      var transferBtn = e.target.closest('.btn-transfer-action');
      if (transferBtn) {
        e.preventDefault();
        window.openConfirmRoomTransferModal(transferBtn);
      }
    });

    // 6. KELUHAN & SERVIS FILTERS (MENUNGGU, DALAM PROSES, SELESAI, 2 CABANG)
    window.currentMaintLoc = 'all';
    window.currentMaintStatus = 'all';

    window.applyMaintenanceFilters = function() {
      var inputEl = document.getElementById('maintenanceSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#maintenanceTableBody tr');
      var cards = document.querySelectorAll('#maintenanceMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          var s = (el.getAttribute('data-search') || el.textContent || '').toLowerCase();
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var status = el.getAttribute('data-status') || '';

          var matchSearch = !q || s.includes(q);
          var matchLoc = (window.currentMaintLoc === 'all' || loc === window.currentMaintLoc);
          var matchStatus = (window.currentMaintStatus === 'all' || status === window.currentMaintStatus);

          el.style.display = (matchSearch && matchLoc && matchStatus) ? '' : 'none';
        });
      });
    };

    window.filterMaintenanceLocation = function(loc, btn) {
      window.currentMaintLoc = loc;
      document.querySelectorAll('#maintenanceLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyMaintenanceFilters();
      if (typeof window.renderMaintenanceTable === 'function') {
        var inputEl = document.getElementById('maintenanceSearchInput');
        window.renderMaintenanceTable(inputEl ? inputEl.value : '', window.currentMaintStatus, window.currentMaintLoc);
      }
    };

    window.filterMaintenanceStatus = function(status, btn) {
      window.currentMaintStatus = status;
      document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      window.applyMaintenanceFilters();
      if (typeof window.renderMaintenanceTable === 'function') {
        var inputEl = document.getElementById('maintenanceSearchInput');
        window.renderMaintenanceTable(inputEl ? inputEl.value : '', window.currentMaintStatus, window.currentMaintLoc);
      }
    };

    window.filterMaintenanceTable = function() {
      window.applyMaintenanceFilters();
    };

    // 7. PENGATURAN KOS SUB-TABS (LANDING PAGE, TITIK LOKASI, TATA TERTIB, WIFI, FORMULIR)
    window.switchSettingsSubtab = function(subtabId, btn) {
      var subTabBtns = document.querySelectorAll('#settingsSubTabNav .btn-settings-subtab');
      var subTabPanes = document.querySelectorAll('.settings-subtab-pane');

      subTabBtns.forEach(function(b) {
        b.classList.remove('active');
        b.style.background = '';
        b.style.color = '';
      });

      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#settingsSubTabNav .btn-settings-subtab[data-subtab="' + subtabId + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      subTabPanes.forEach(function(pane) {
        if (pane.id === subtabId) {
          pane.classList.add('active');
          pane.style.display = 'block';
        } else {
          pane.classList.remove('active');
          pane.style.display = 'none';
        }
      });

      if (typeof window.updateSettingsSubChip === 'function') {
        window.updateSettingsSubChip('tab-settings');
      }
    };

    // Helper Toast Notification Universal
    if (!window.showToast) {
      window.showToast = function(msg, type) {
        var toast = document.createElement('div');
        toast.className = 'toast-notification ' + (type || 'info');
        toast.textContent = msg;
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:999999;padding:12px 22px;border-radius:14px;font-size:13.5px;font-weight:700;box-shadow:0 12px 32px rgba(0,0,0,0.18);transition:all 0.3s cubic-bezier(0.16,1,0.3,1);transform:translateY(24px);opacity:0;';
        if (type === 'success') {
          toast.style.background = '#033f3c';
          toast.style.color = '#ffffff';
          toast.style.border = '1px solid #10b981';
        } else if (type === 'error') {
          toast.style.background = '#dc2626';
          toast.style.color = '#ffffff';
        } else {
          toast.style.background = '#1e293b';
          toast.style.color = '#ffffff';
        }
        document.body.appendChild(toast);
        setTimeout(function() {
          toast.style.transform = 'translateY(0)';
          toast.style.opacity = '1';
        }, 20);
        setTimeout(function() {
          toast.style.transform = 'translateY(24px)';
          toast.style.opacity = '0';
          setTimeout(function() { toast.remove(); }, 350);
        }, 3600);
      };
    }

    // 7.1 PENGATURAN LANDING PAGE & HALAMAN LOGIN (LIVE PREVIEW, LOGO, & SUBMISSION)
    window.updateLiveLoginPreview = function() {
      var pHead = document.getElementById('lp_promo_headline');
      var pDesc = document.getElementById('lp_promo_desc');
      var bTitle = document.getElementById('lp_brand_title');
      var fSub = document.getElementById('lp_form_subtitle');
      var phEmail = document.getElementById('lp_placeholder_email');
      var phPwd = document.getElementById('lp_placeholder_password');
      var fPwd = document.getElementById('lp_forgot_password_text');
      var bLogin = document.getElementById('lp_btn_login_text');
      var divText = document.getElementById('lp_divider_text');
      var bHome = document.getElementById('lp_back_home_text');
      var logoSize = document.getElementById('lp_brand_logo_size');

      var prevHead = document.getElementById('prevPromoHeadline');
      var prevDesc = document.getElementById('prevPromoDesc');
      var prevTitle = document.getElementById('prevBrandTitle');
      var prevSub = document.getElementById('prevFormSubtitle');
      var prevEmail = document.getElementById('prevPlaceholderEmail');
      var prevPwd = document.getElementById('prevPlaceholderPassword');
      var prevForgot = document.getElementById('prevForgotText');
      var prevBtn = document.getElementById('prevBtnLogin');
      var prevDiv = document.getElementById('prevDividerText');
      var prevHome = document.getElementById('prevBackHomeText');
      var prevLogoWrap = document.getElementById('prevLogoWrap');

      if (prevHead && pHead) prevHead.textContent = pHead.value || "Simplify\nmanagement with\nour dashboard.";
      if (prevDesc && pDesc) prevDesc.textContent = pDesc.value || 'Simplify your e-commerce management with our user-friendly admin dashboard.';
      if (prevTitle && bTitle) prevTitle.textContent = bTitle.value || 'SMART LIVING';
      if (prevSub && fSub) prevSub.textContent = fSub.value || 'Please login to your account';
      if (prevEmail && phEmail) prevEmail.textContent = phEmail.value || 'Email address';
      if (prevPwd && phPwd) prevPwd.textContent = phPwd.value || 'Password';
      if (prevForgot && fPwd) prevForgot.textContent = fPwd.value || 'Forgot password?';
      if (prevBtn && bLogin) prevBtn.textContent = bLogin.value || 'Login';
      if (prevDiv && divText) prevDiv.textContent = divText.value || 'Or Login with';
      if (prevHome && bHome) prevHome.textContent = bHome.value || 'Kembali Ke Beranda';

      if (prevLogoWrap && logoSize) {
        var sz = parseInt(logoSize.value, 10) || 48;
        prevLogoWrap.style.width = sz + 'px';
        prevLogoWrap.style.height = sz + 'px';
      }
    };

    window.previewLoginBrandLogo = function(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var formImg = document.getElementById('lpBrandLogoPreviewImg');
          var formIcon = document.getElementById('lpBrandLogoDefaultIcon');
          var prevImg = document.getElementById('prevLogoImg');
          var prevIcon = document.getElementById('prevLogoIcon');
          var removeInput = document.getElementById('lp_remove_logo');

          if (formImg) {
            formImg.src = e.target.result;
            formImg.style.display = 'block';
          }
          if (formIcon) formIcon.style.display = 'none';
          if (prevImg) {
            prevImg.src = e.target.result;
            prevImg.style.display = 'block';
          }
          if (prevIcon) prevIcon.style.display = 'none';
          if (removeInput) removeInput.value = '0';
        };
        reader.readAsDataURL(input.files[0]);
      }
    };

    window.removeCustomLoginLogo = function() {
      if (!confirm('Hapus logo kustom dan gunakan icon standar SMART LIVING?')) return;
      var fileInput = document.getElementById('lp_brand_logo');
      var removeInput = document.getElementById('lp_remove_logo');
      var formImg = document.getElementById('lpBrandLogoPreviewImg');
      var formIcon = document.getElementById('lpBrandLogoDefaultIcon');
      var prevImg = document.getElementById('prevLogoImg');
      var prevIcon = document.getElementById('prevLogoIcon');

      if (fileInput) fileInput.value = '';
      if (removeInput) removeInput.value = '1';
      if (formImg) {
        formImg.src = '';
        formImg.style.display = 'none';
      }
      if (formIcon) formIcon.style.display = 'block';
      if (prevImg) {
        prevImg.src = '';
        prevImg.style.display = 'none';
      }
      if (prevIcon) prevIcon.style.display = 'block';
    };

    window.resetLoginPageSettings = function() {
      if (!confirm('Kembalikan seluruh teks, logo, dan formulir Login Page ke pengaturan default bawaan sistem?')) return;
      var btn = document.getElementById('btnResetLoginPage');
      if (btn) btn.disabled = true;

      fetch('/api/settings/login-page', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ action: 'reset' })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success && data.login_content) {
          var lc = data.login_content;
          var setVal = function(id, val) { var el = document.getElementById(id); if (el) el.value = val; };
          setVal('lp_promo_headline', lc.promo_headline || "Simplify\nmanagement with\nour dashboard.");
          setVal('lp_promo_desc', lc.promo_desc || 'Simplify your e-commerce management with our user-friendly admin dashboard.');
          setVal('lp_promo_badge', lc.promo_badge || 'Exclusive Management');
          setVal('lp_brand_title', lc.brand_title || 'SMART LIVING');
          setVal('lp_form_subtitle', lc.form_subtitle || 'Please login to your account');
          setVal('lp_placeholder_email', lc.placeholder_email || 'Email address');
          setVal('lp_placeholder_password', lc.placeholder_password || 'Password');
          setVal('lp_forgot_password_text', lc.forgot_password_text || 'Forgot password?');
          setVal('lp_btn_login_text', lc.btn_login_text || 'Login');
          setVal('lp_divider_text', lc.divider_text || 'Or Login with');
          setVal('lp_back_home_text', lc.back_home_text || 'Kembali Ke Beranda');
          setVal('lp_brand_logo_size', lc.brand_logo_size || 48);
          setVal('lp_brand_logo_size_slider', lc.brand_logo_size || 48);

          var formImg = document.getElementById('lpBrandLogoPreviewImg');
          var formIcon = document.getElementById('lpBrandLogoDefaultIcon');
          var prevImg = document.getElementById('prevLogoImg');
          var prevIcon = document.getElementById('prevLogoIcon');
          if (formImg) formImg.style.display = 'none';
          if (formIcon) formIcon.style.display = 'block';
          if (prevImg) prevImg.style.display = 'none';
          if (prevIcon) prevIcon.style.display = 'block';

          window.updateLiveLoginPreview();
          if (typeof window.showToast === 'function') {
            window.showToast(data.message || 'Pengaturan berhasil dikembalikan ke standar!', 'success');
          }
        } else {
          alert(data.message || 'Gagal mereset pengaturan.');
        }
      })
      .catch(function(err) {
        console.error(err);
        alert('Terjadi kesalahan koneksi.');
      })
      .finally(function() {
        if (btn) btn.disabled = false;
      });
    };

    document.addEventListener('DOMContentLoaded', function() {
      // Bind live preview on input in login editor
      var loginEditor = document.getElementById('loginPageEditorForm');
      if (loginEditor) {
        loginEditor.querySelectorAll('input, textarea').forEach(function(input) {
          input.addEventListener('input', window.updateLiveLoginPreview);
        });

        loginEditor.addEventListener('submit', function(e) {
          e.preventDefault();
          var btn = document.getElementById('btnSaveLoginPage');
          var statusEl = document.getElementById('loginPageSaveStatus');
          if (btn) btn.disabled = true;
          if (statusEl) {
            statusEl.textContent = 'Menyimpan perubahan...';
            statusEl.style.color = '#033f3c';
          }

          var formData = new FormData(loginEditor);
          fetch('/api/settings/login-page', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Accept': 'application/json'
            },
            body: formData
          })
          .then(function(res) { return res.json(); })
          .then(function(data) {
            if (data.success) {
              if (typeof window.showToast === 'function') {
                window.showToast(data.message || 'Pengaturan Landing Page berhasil disimpan!', 'success');
              } else {
                alert(data.message || 'Pengaturan Landing Page berhasil disimpan!');
              }
              if (statusEl) {
                statusEl.textContent = 'Berhasil disimpan!';
                statusEl.style.color = '#10b981';
                setTimeout(function() { statusEl.textContent = ''; }, 3500);
              }
            } else {
              alert(data.message || 'Gagal menyimpan pengaturan');
              if (statusEl) statusEl.textContent = '';
            }
          })
          .catch(function(err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat menyimpan pengaturan.');
            if (statusEl) statusEl.textContent = '';
          })
          .finally(function() {
            if (btn) btn.disabled = false;
          });
        });
      }
    });

    // 8. LAPORAN KEUANGAN FILTERS (BUKU KAS UNEJ/UNMUH, PEMASUKAN, PENGELUARAN, LABA BERSIH)
    window.currentFinLoc = 'all';
    window.currentFinType = 'all';

    window.filterFinanceBranch = function(loc, btn) {
      window.currentFinLoc = loc;
      document.querySelectorAll('#financeLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#financeLocationFilters .filter-pill[data-floc="' + loc + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      if (typeof window.renderFinancesTable === 'function') {
        var searchEl = document.getElementById('financeSearchInput');
        window.renderFinancesTable(searchEl ? searchEl.value : '', window.currentFinType, window.currentFinLoc);
      }
    };

    window.filterFinanceType = function(type, btn) {
      window.currentFinType = type;
      document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#financeTypeFilter .filter-pill[data-ftype="' + type + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      if (typeof window.renderFinancesTable === 'function') {
        var searchEl = document.getElementById('financeSearchInput');
        window.renderFinancesTable(searchEl ? searchEl.value : '', window.currentFinType, window.currentFinLoc);
      }
    };
  </script>

  @php
    $allBranchesGlobal = $setting->branches ?? [];
    $branch1Name = $allBranchesGlobal['lokasi_1']['name'] ?? 'Kos Mas Robi 1 - Kampus Utama';
    $branch2Name = $allBranchesGlobal['lokasi_2']['name'] ?? 'Kos Mas Robi 2 - Ringroad Exclusive';
    $b1Active = $allBranchesGlobal['lokasi_1']['is_active'] ?? true;
    $b2Active = $allBranchesGlobal['lokasi_2']['is_active'] ?? true;
    $b1Image = $allBranchesGlobal['lokasi_1']['image'] ?? 'assets/images/kos_exterior.webp';
    $b2Image = $allBranchesGlobal['lokasi_2']['image'] ?? 'assets/images/landing/hero_corridor_teal.webp';
    $existingFloors = collect([1, 2])->merge($rooms->pluck('floor')->map(fn($f) => (int)$f)->filter(fn($f) => $f > 0))->unique()->sort()->values();
    $ownerInitials = collect(preg_split('/\s+/', trim($setting->owner_name ?? 'Mas Robi')))
      ->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'MR';
  @endphp

  <!-- =========================================================================
       SIDEBAR (Desktop Fixed, Mobile Slide-out Drawer)
       ========================================================================= -->
  <aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-header">
      <div class="brand-icon-box modern-brand-box" style="padding: 0; overflow: hidden; background: transparent; border: none; box-shadow: none;">
        <img src="{{ asset('assets/images/logo_kos_jember.png') }}" alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'Kos Jember' }}" width="38" height="38" fetchpriority="high" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
      </div>
      <div class="brand-details">
        <h2 class="brand-title">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</h2>
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

      <div class="nav-item" data-tab="tab-surveys" onclick="window.switchTab('tab-surveys')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
        <span>Survei Lokasi</span>
        @php
          $pendingSurveysCount = ($allSurveys ?? collect())->where('status', 'pending')->count();
        @endphp
        <span class="nav-badge-count" id="badgePendingSurveys" style="{{ $pendingSurveysCount > 0 ? 'background: #f0fdfa; color: #0a5661; border: 1px solid #99f6e4; font-weight:800;' : 'display:none;' }}">{{ $pendingSurveysCount }}</span>
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
      @endif

      <div class="nav-item" data-tab="tab-admins" onclick="window.switchTab('tab-admins')" role="button" tabindex="0">
        <span class="nav-icon-circle"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span>Akun Admin</span>
        <span class="nav-badge-count" id="badgeTotalAdmins" style="background: rgba(14, 116, 130, 0.22); color: #0a5661; border: 1px solid rgba(14, 116, 130, 0.35); font-weight:800;">{{ $totalAdminsCount ?? count($allAdmins ?? []) }}</span>
      </div>

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
        <!-- Desktop SaaS Search Bar -->
        <div class="header-search-bar" onclick="var el = document.getElementById('desktopGlobalSearch'); if(el) el.focus();">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" id="desktopGlobalSearch" placeholder="Cari kamar, tipe, cabang, atau nama penghuni..." autocomplete="off" oninput="if(typeof handleGlobalHeaderSearch === 'function') handleGlobalHeaderSearch(this.value)">
          <span class="kbd-shortcut">Ctrl + K</span>
        </div>

        <div class="modern-title-box" style="display: none;">
          <div class="header-breadcrumb">
            <span class="bc-home">{{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}</span>
            <span class="bc-sep">/</span>
            <span class="bc-curr" id="breadcrumbCurrent">Dashboard</span>
          </div>
          <h1 class="header-active-page-title header-main-title" id="headerActiveTitle" data-page-title>Dashboard</h1>
          <p class="header-sub-title" id="pageSubtitle">Monitoring real-time hunian, status kamar, dan operasional</p>
        </div>
        <span class="header-sub-chip" id="headerActiveSubChip" style="display: none;"></span>
        <span id="pageTitle" style="display: none;">Dashboard</span>
        <div class="mobile-header-brand">
          <div class="brand-icon-box modern-brand-box" style="padding: 0; overflow: hidden; background: transparent; border: none; box-shadow: none;">
            <img src="{{ asset('assets/images/logo_kos_jember.png') }}" alt="{{ !empty($setting->kos_name) ? $setting->kos_name : 'Kos Jember' }}" width="38" height="38" fetchpriority="high" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
          </div>
          <div class="brand-details">
            <h2 class="brand-title">{{ !empty($setting->kos_name) ? $setting->kos_name : 'SMART LIVING' }}</h2>
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

        <!-- {{-- FITUR NOTIFIKASI DI SEBELAH KIRI TOMBOL LOGOUT (ADMIN & OWNER) --}} -->
        @php
          $notifPendingTransfers = ($roomTransfers ?? collect())->where('status', 'pending');
          $notifActiveTickets = ($allMaintenance ?? collect())->whereIn('status', ['pending', 'progress']);
          $notifDueRooms = ($rooms ?? collect())->where('status', 'due');
          $notifSurveys = ($allSurveys ?? collect())->where('status', 'pending');
          $notifTotalCount = $notifPendingTransfers->count() + $notifActiveTickets->count() + $notifDueRooms->count() + $notifSurveys->count();
        @endphp
        <div class="header-notif-wrap" id="headerNotifWrap">
          <button type="button" class="header-action-btn modern-notif-btn" id="btnToggleNotifications" onclick="window.toggleNotifDropdown(event)" title="Notifikasi Sistem" aria-label="Notifikasi Sistem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            @if($notifTotalCount > 0)
              <span class="header-notif-badge" id="headerNotifBadge">{{ $notifTotalCount > 9 ? '9+' : $notifTotalCount }}</span>
            @endif
          </button>

          {{-- DROPDOWN NOTIFIKASI --}}
          <div class="header-notif-dropdown" id="dropdownNotifications">
            <div class="notif-dropdown-header">
              <div class="notif-dropdown-title-row">
                <h4 class="notif-dropdown-title">Notifikasi Sistem</h4>
                <span class="notif-pill-badge" id="notifPillBadge" style="{{ $notifTotalCount > 0 ? '' : 'background: #dcfce7; color: #15803d;' }}">
                  <span id="notifPillText">{{ $notifTotalCount > 0 ? $notifTotalCount . ' Perlu Tindakan' : '0 Baru' }}</span>
                </span>
              </div>
              <button type="button" class="btn-secondary" onclick="window.closeNotifDropdown()" style="padding: 3px 8px; font-size: 0.72rem; border-radius: 6px;" title="Tutup">Tutup</button>
            </div>

            {{-- Filter Kategori Notifikasi --}}
            <div class="notif-filter-bar">
              <button type="button" class="notif-filter-btn active" onclick="window.filterNotifList('all', this)">Semua (<span id="notifCountTotal">{{ $notifTotalCount }}</span>)</button>
              <button type="button" class="notif-filter-btn" onclick="window.filterNotifList('survey', this)">Survei (<span id="notifCountSurvey">{{ $notifSurveys->count() }}</span>)</button>
              <button type="button" class="notif-filter-btn" onclick="window.filterNotifList('transfer', this)">Mutasi (<span id="notifCountTransfer">{{ $notifPendingTransfers->count() }}</span>)</button>
              <button type="button" class="notif-filter-btn" onclick="window.filterNotifList('complaint', this)">Keluhan (<span id="notifCountComplaint">{{ $notifActiveTickets->count() }}</span>)</button>
              <button type="button" class="notif-filter-btn" onclick="window.filterNotifList('billing', this)">Tagihan (<span id="notifCountBilling">{{ $notifDueRooms->count() }}</span>)</button>
            </div>

            <div class="notif-list-body" id="notifListBody">
              @if($notifTotalCount === 0)
                <div id="notifEmptyState" style="text-align: center; padding: 34px 18px; color: #64748b;">
                  <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; margin-bottom: 4px;">Semua Beres & Terkendali</div>
                  <p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">Tidak ada survei lokasi baru, mutasi pending, keluhan, ataupun tagihan jatuh tempo saat ini.</p>
                </div>
              @else
                {{-- 0. Jadwal Survei Lokasi Masuk (Survey Requests) --}}
                @foreach($notifSurveys as $sv)
                  <div class="notif-item" data-cat="survey" data-id="{{ $sv->id }}" onclick="window.openSurveyDetailModal({{ $sv->id }})">
                    <div class="notif-item-icon survey">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="notif-item-content">
                      <div class="notif-item-header">
                        <span class="notif-item-title">Survei: {{ $sv->name }}</span>
                        <span class="notif-item-time">{{ $sv->created_at ? $sv->created_at->diffForHumans(null, true) : 'Baru' }}</span>
                      </div>
                      <p class="notif-item-desc">
                        Ingin survei <strong>{{ $sv->room ?: 'Unit Kamar' }}</strong> ({{ $sv->branch }}) tgl <strong>{{ $sv->survey_date ? $sv->survey_date->format('d M Y') : 'Segera' }}</strong> jam {{ $sv->survey_time ?: '-' }} via {{ $sv->survey_method ?: 'Datang Langsung' }}.
                      </p>
                      @if($sv->phone)
                        <div style="margin-top: 4px; display: flex; align-items: center; gap: 8px;">
                          <span style="font-size: 0.68rem; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 5px; font-weight: 700;">WA: {{ $sv->phone }}</span>
                          <span style="font-size: 0.68rem; color: #7c3aed; font-weight: 600;">Klik untuk detail</span>
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach
                {{-- 1. Pengajuan Pindah Kamar (Pending) --}}
                @foreach($notifPendingTransfers as $tr)
                  <div class="notif-item" data-cat="transfer" data-id="{{ $tr->id }}" onclick="window.handleNotifClick('tab-transfers')">
                    <div class="notif-item-icon transfer">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
                    </div>
                    <div class="notif-item-content">
                      <div class="notif-item-header">
                        <span class="notif-item-title">Pengajuan Pindah Kamar</span>
                        <span class="notif-item-time">{{ $tr->created_at ? $tr->created_at->diffForHumans(null, true) : 'Baru' }}</span>
                      </div>
                      <p class="notif-item-desc">
                        <strong>{{ $tr->user?->name ?? 'Penghuni' }}</strong>: Kamar #{{ $tr->currentRoom?->number ?? '-' }} &rarr; #{{ $tr->targetRoom?->number ?? '-' }} ({{ Str::limit($tr->reason ?? 'Permohonan mutasi', 45) }})
                      </p>
                    </div>
                  </div>
                @endforeach

                {{-- 2. Keluhan & Servis Masuk (Pending & Progress) --}}
                @foreach($notifActiveTickets->take(5) as $tk)
                  <div class="notif-item" data-cat="complaint" data-id="{{ $tk->id }}" onclick="window.handleNotifClick('tab-maintenance')">
                    <div class="notif-item-icon complaint">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div class="notif-item-content">
                      <div class="notif-item-header">
                        <span class="notif-item-title">Keluhan Kamar #{{ $tk->room?->number ?? ($tk->room_number ?? '-') }}</span>
                        <span class="notif-item-time">{{ !empty($tk->report_date) ? \Carbon\Carbon::parse($tk->report_date)->diffForHumans(null, true) : 'Aktif' }}</span>
                      </div>
                      <p class="notif-item-desc">
                        {{ Str::limit($tk->description, 55) }} (Pelapor: {{ $tk->tenant?->name ?? ($tk->reported_by ?? 'Penghuni') }})
                      </p>
                    </div>
                  </div>
                @endforeach

                {{-- 3. Tagihan Sewa Jatuh Tempo --}}
                @foreach($notifDueRooms->take(5) as $dr)
                  <div class="notif-item" data-cat="billing" data-id="{{ $dr->id }}" onclick="window.handleNotifClick('tab-bills')">
                    <div class="notif-item-icon billing">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div class="notif-item-content">
                      <div class="notif-item-header">
                        <span class="notif-item-title">Jatuh Tempo: Kamar #{{ $dr->number }}</span>
                        <span class="notif-item-time" style="color: #ef4444; font-weight: 700;">Tagihan</span>
                      </div>
                      <p class="notif-item-desc">
                        Penghuni <strong>{{ $dr->activeTenant?->name ?? 'Penyewa' }}</strong> memiliki tunggakan sewa (Rp {{ number_format($dr->price ?? 0, 0, ',', '.') }}/bln).
                      </p>
                    </div>
                  </div>
                @endforeach
              @endif
            </div>

            <div class="notif-dropdown-footer">
              <button type="button" class="notif-footer-link" onclick="window.handleNotifClick('tab-dashboard')">Ringkasan Dashboard</button>
              <button type="button" class="notif-footer-link" onclick="window.closeNotifDropdown()" style="color: #64748b;">Tutup</button>
            </div>
          </div>
        </div>

        <!-- Desktop User Monogram Avatar -->
        <button type="button" class="header-desktop-avatar" onclick="window.openModal('modalAkunPengelola')" title="Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}" aria-label="Akun {{ Auth::user()->isOwner() ? 'Owner' : 'Admin' }}">
          {{ $ownerInitials }}
        </button>

        <form action="{{ route('logout') }}" method="POST" id="formLogoutAdmin" style="display:inline; margin:0;">
          @csrf
          <button type="button" class="header-action-btn modern-logout-btn" id="btnTriggerLogoutAdmin" title="Keluar / Logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="logout-btn-label">Keluar</span>
          </button>
        </form>
      </div>
    </header>

    <!-- MOBILE ACTIVE FEATURE TITLE BAR (Dihilangkan pada HP sesuai permintaan) -->
    <div class="mobile-page-titlebar" id="mobilePageTitlebar" style="display: none !important;">
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

        <!-- =====================================================================
             1. HERO WELCOME BANNER (MODERN SAAS 2026-2027)
             ===================================================================== -->
        <div class="dashboard-hero-banner">
          <div class="hero-left-content">
            <h1 class="hero-headline">
              Kelola Kos dengan<br>
              Lebih Mudah &amp; Modern
            </h1>
            <p class="hero-subtitle">
              Pantau kamar, penghuni, dan transaksi secara real-time dalam satu dashboard.
            </p>
            <button type="button" class="hero-detail-btn" onclick="window.switchTab('tab-rooms')">
              <span>Lihat Detail</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </div>

          <!-- Landing Page Hero Photo Background Blend -->
          @php
            $heroPhoto = !empty($setting->landing_content['hero_image'])
                ? asset($setting->landing_content['hero_image'])
                : asset('assets/images/landing/hero_corridor_teal.webp');
          @endphp
          <div class="hero-exterior-wrapper">
            <img src="{{ $heroPhoto }}" class="hero-exterior-img" alt="Foto Kos Modern" loading="lazy" onerror="this.src='{{ asset('assets/images/landing/hero_corridor_teal.webp') }}'">
          </div>

          <!-- Right Floating Frosted Card: Total Penghuni -->
          <div class="hero-resident-card" onclick="window.switchTab('tab-tenants')" style="cursor: pointer;" title="Buka Daftar Penghuni">
            <div class="hero-res-info">
              <div class="hero-res-header">
                <div class="hero-res-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                  </svg>
                </div>
                <span class="hero-res-label">Total Penghuni</span>
              </div>
              <div class="hero-res-val">{{ $occupiedRooms }} Penghuni</div>
              <span class="hero-res-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                <span>+12% dari bulan lalu</span>
              </span>
            </div>
            <div class="hero-mini-barchart" aria-hidden="true">
              <span class="mini-bar" style="height: 18px;"></span>
              <span class="mini-bar" style="height: 28px;"></span>
              <span class="mini-bar" style="height: 34px;"></span>
              <span class="mini-bar" style="height: 42px;"></span>
              <span class="mini-bar" style="height: 48px;"></span>
              <span class="mini-bar" style="height: 54px;"></span>
            </div>
          </div>
        </div>

        <!-- =====================================================================
             2. FINTECH KPI STAT CARDS (ROW OF 4)
             ===================================================================== -->
        <section class="dashboard-stats-grid" aria-label="Ringkasan Statistik Kos">
          <!-- Card 1: Tingkat Okupansi -->
          <div class="stat-card modern-kpi-card" onclick="window.switchTab('tab-rooms')" style="cursor: pointer;" title="Lihat Daftar Kamar">
            <div class="stat-card-header">
              <span class="stat-card-title">Tingkat Okupansi</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 3v18h18"></path>
                  <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                </svg>
              </div>
            </div>
            <div class="stat-card-value" id="statOccupancyRate">{{ $occupancyRate }}%</div>
            <div class="stat-card-meta">
              <span class="stat-trend-pill mint">&uarr; +2.5%</span>
            </div>
            <!-- Teal Smooth Area Wave -->
            <svg viewBox="0 0 140 45" class="stat-sparkline" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
              <defs>
                <linearGradient id="gradTealSpark" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#14b8a6" stop-opacity="0.4"/>
                  <stop offset="100%" stop-color="#14b8a6" stop-opacity="0.0"/>
                </linearGradient>
              </defs>
              <path d="M 0 35 Q 20 40 40 28 T 80 20 T 110 10 T 140 8 L 140 45 L 0 45 Z" fill="url(#gradTealSpark)"/>
              <path d="M 0 35 Q 20 40 40 28 T 80 20 T 110 10 T 140 8" stroke="#14b8a6" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </div>

          <!-- Card 2: Kamar Tersedia -->
          <div class="stat-card modern-kpi-card" onclick="window.switchTab('tab-rooms'); if(typeof window.filterRoomStatus === 'function') { window.filterRoomStatus('available'); } else if(typeof window.filterDashboardStatus === 'function') { window.filterDashboardStatus('available'); }" style="cursor: pointer;" title="Lihat Kamar Kosong Siap Huni">
            <div class="stat-card-header">
              <span class="stat-card-title">Kamar Tersedia</span>
              <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
            </div>
            <div class="stat-card-value" id="statAvailableRooms">{{ $availableRooms }} Kamar</div>
            <div class="stat-card-meta">
              <span class="stat-trend-pill blue">Siap Huni</span>
            </div>
            <!-- Blue Smooth Area Wave -->
            <svg viewBox="0 0 140 45" class="stat-sparkline" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
              <defs>
                <linearGradient id="gradBlueSpark" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
                  <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                </linearGradient>
              </defs>
              <path d="M 0 30 Q 30 18 60 25 T 100 15 T 140 20 L 140 45 L 0 45 Z" fill="url(#gradBlueSpark)"/>
              <path d="M 0 30 Q 30 18 60 25 T 100 15 T 140 20" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </div>

          <!-- Card 3: Tagihan Jatuh Tempo -->
          <div class="stat-card modern-kpi-card" onclick="window.switchTab('tab-bills')" style="cursor: pointer;" title="Lihat Tagihan Jatuh Tempo">
            <div class="stat-card-header">
              <span class="stat-card-title">Tagihan Jatuh Tempo</span>
              <div class="stat-icon purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
            </div>
            <div class="stat-card-value" id="statDueBills">{{ $dueRooms }} Kamar (Rp {{ number_format($dueAmount, 0, ',', '.') }})</div>
            <div class="stat-card-meta">
              <span class="stat-trend-pill purple">- 0%</span>
            </div>
            <!-- Purple Smooth Area Wave -->
            <svg viewBox="0 0 140 45" class="stat-sparkline" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
              <defs>
                <linearGradient id="gradPurpleSpark" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#a855f7" stop-opacity="0.4"/>
                  <stop offset="100%" stop-color="#a855f7" stop-opacity="0.0"/>
                </linearGradient>
              </defs>
              <path d="M 0 38 Q 35 32 70 28 T 105 18 T 140 12 L 140 45 L 0 45 Z" fill="url(#gradPurpleSpark)"/>
              <path d="M 0 38 Q 35 32 70 28 T 105 18 T 140 12" stroke="#a855f7" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </div>

          <!-- Card 4: Total Tagihan / Pemasukan -->
          <div class="stat-card modern-kpi-card" onclick="{{ Auth::user()->isOwner() ? "window.switchTab('tab-finances')" : "window.switchTab('tab-bills')" }}" style="cursor: pointer;" title="{{ Auth::user()->isOwner() ? 'Buka Laporan Keuangan' : 'Buka Tagihan Sewa' }}">
            <div class="stat-card-header">
              <span class="stat-card-title">{{ Auth::user()->isOwner() ? 'Pemasukan Bulan Ini' : 'Total Tagihan' }}</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                  <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                  <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
              </div>
            </div>
            <div class="stat-card-value" id="statMonthlyIncome">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-pill amber">● Terverifikasi</span>
            </div>
            <!-- Amber Smooth Area Wave -->
            <svg viewBox="0 0 140 45" class="stat-sparkline" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
              <defs>
                <linearGradient id="gradAmberSpark" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.4"/>
                  <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0"/>
                </linearGradient>
              </defs>
              <path d="M 0 35 Q 25 38 50 25 T 95 22 T 140 10 L 140 45 L 0 45 Z" fill="url(#gradAmberSpark)"/>
              <path d="M 0 35 Q 25 38 50 25 T 95 22 T 140 10" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </div>
        </section>

        <!-- =====================================================================
             3. MIDDLE SECTION: 2 COLUMNS (DENAH & STATUS KAMAR + AKTIVITAS TERBARU)
             ===================================================================== -->
        <div class="dashboard-main-row">
          <!-- Left Column: Denah & Status Kamar -->
          <div class="card-room-status-overview">
            <div class="room-status-header">
              <div class="title-group">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <h2>Denah &amp; Status Kamar</h2>
              </div>
              <div class="dashboard-status-pills" id="dashboardFilters">
                <button type="button" class="status-pill-btn active" data-filter="all" onclick="filterDashboardStatus('all', this)">Semua Kamar</button>
                <button type="button" class="status-pill-btn" data-filter="occupied" onclick="filterDashboardStatus('occupied', this)">Terisi</button>
                <button type="button" class="status-pill-btn" data-filter="available" onclick="filterDashboardStatus('available', this)">Kosong</button>
                <button type="button" class="status-pill-btn" data-filter="due" onclick="filterDashboardStatus('due', this)">Jatuh Tempo</button>
                <button type="button" class="status-pill-btn" data-filter="repair" onclick="filterDashboardStatus('repair', this)">Perbaikan</button>
              </div>
            </div>

            <!-- Secondary Branch & Floor Filter Bar -->
            <div class="secondary-filter-bar">
              <div class="filter-pills" id="dashboardLocationFilters" style="margin: 0; padding: 0;">
                <button type="button" class="filter-pill active" data-dloc="all" onclick="filterDashboardLocation('all', this)">Semua Cabang ({{ $totalRooms }})</button>
                <button type="button" class="filter-pill" id="filterPillB1" data-dloc="Lokasi 1" onclick="filterDashboardLocation('Lokasi 1', this)" style="{{ $b1Active ? '' : 'display: none !important;' }}"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $roomsLoc1->count() }})</button>
                <button type="button" class="filter-pill" id="filterPillB2" data-dloc="Lokasi 2" onclick="filterDashboardLocation('Lokasi 2', this)" style="{{ $b2Active ? '' : 'display: none !important;' }}"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $roomsLoc2->count() }})</button>
              </div>
              <div class="filter-pills" id="dashboardFloorFilters" style="margin: 0; padding: 0;">
                <button type="button" class="filter-pill active" data-dfloor="all" onclick="filterDashboardFloor('all', this)">Semua Lantai</button>
                @foreach($existingFloors as $fl)
                  <button type="button" class="filter-pill" data-dfloor="{{ $fl }}" onclick="filterDashboardFloor('{{ $fl }}', this)">Lantai {{ $fl }}</button>
                @endforeach
              </div>
            </div>

            <!-- Modern Room Grid (4 columns) -->
            <div class="room-modern-grid" id="dashboardRoomGrid">
              @php
                $sampleRoomImages = [
                  asset('assets/images/rooms/room_1.webp'),
                  asset('assets/images/rooms/room_2.webp'),
                  asset('assets/images/rooms/room_3.webp'),
                ];
              @endphp
              @foreach($rooms as $rCard)
                @php
                  $cleanNum = (int)preg_replace('/\D/', '', $rCard->number);
                  $imgIdx = ($cleanNum > 0 ? ($cleanNum - 1) : $loop->index) % 3;
                  $roomThumb = !empty($rCard->image) && file_exists(public_path($rCard->image))
                      ? asset($rCard->image)
                      : $sampleRoomImages[$imgIdx];
                  $hasAC = (stripos($rCard->type, 'AC') !== false) || (is_array($rCard->facilities) && in_array('AC', $rCard->facilities));
                @endphp
                <div class="room-overview-card room-card"
                     data-room-id="{{ $rCard->number }}"
                     data-status="{{ $rCard->status }}"
                     data-floor="{{ $rCard->floor }}"
                     data-location="{{ $rCard->location ?? 'Lokasi 1' }}"
                     onclick="window.openRoomDetail('{{ $rCard->number }}')"
                     title="Klik untuk lihat detail Kamar #{{ $rCard->number }}">
                  <div class="room-ov-left">
                    <div class="room-ov-header">
                      <span class="room-ov-number">{{ $rCard->number }}</span>
                      @if($rCard->status === 'occupied')
                        <span class="room-ov-badge status-occupied">Terisi</span>
                      @elseif($rCard->status === 'available')
                        <span class="room-ov-badge status-available">Kosong</span>
                      @elseif($rCard->status === 'due')
                        <span class="room-ov-badge status-due">Jatuh Tempo</span>
                      @elseif($rCard->status === 'repair')
                        <span class="room-ov-badge status-repair">Perbaikan</span>
                      @else
                        <span class="room-ov-badge">{{ $rCard->status }}</span>
                      @endif
                    </div>
                    <div class="room-ov-fac">
                      @if($hasAC)
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="2" y="4" width="20" height="12" rx="2"></rect>
                          <path d="M6 16v3"></path>
                          <path d="M18 16v3"></path>
                          <path d="M6 8h12"></path>
                        </svg>
                        <span>AC</span>
                      @else
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M12 12c0-3 2.5-5 5-5s3 3 0 5-5 0-5 0z"></path>
                          <path d="M12 12c3 0 5 2.5 5 5s-3 3-5 0 0-5 0-5z"></path>
                          <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                        <span>Kipas</span>
                      @endif
                    </div>
                    <div class="room-ov-type">{{ $rCard->type }}</div>
                  </div>
                  <div class="room-ov-thumb">
                    <img src="{{ $roomThumb }}" alt="Kamar #{{ $rCard->number }}" loading="lazy">
                    @if($rCard->status === 'repair')
                      <div class="room-ov-repair-overlay" title="Unit Kamar Dalam Perbaikan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach

              <!-- Empty State when no rooms match filters -->
              <div id="dashboardRoomsEmptyState" style="display: none; grid-column: 1 / -1; padding: 32px 18px; text-align: center; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px; color: #64748b; margin: 6px 0;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; background: #e2e8f0; color: #64748b; margin-bottom: 10px;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                </div>
                <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem; margin-bottom: 4px;">Tidak ada kamar yang cocok</div>
                <div style="font-size: 0.82rem; color: #64748b; max-width: 320px; margin: 0 auto 12px; line-height: 1.4;">Tidak ditemukan unit kamar untuk kombinasi status, cabang, atau lantai yang dipilih.</div>
                <button type="button" onclick="window.resetDashboardFilters()" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; font-size: 0.8rem; font-weight: 600; color: #0d9488; background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 9999px; cursor: pointer;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                  <span>Reset Filter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Right Column: Aktivitas Terbaru -->
          <div class="card-recent-activity">
            <div class="activity-header">
              <div class="title-group">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <h2>Aktivitas Terbaru</h2>
              </div>
              <a href="javascript:void(0)" class="see-all-link" onclick="window.switchTab('tab-tenants')">Lihat Semua &rarr;</a>
            </div>

            <div class="activity-timeline-list">
              <!-- Item 1: Penghuni baru mendaftar -->
              <div class="activity-timeline-item" onclick="window.switchTab('tab-tenants')" style="cursor: pointer;" title="Buka Daftar Penghuni">
                <div class="activity-icon mint">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                  </svg>
                </div>
                <div class="activity-content">
                  <div class="activity-title">Penghuni baru mendaftar</div>
                  <div class="activity-meta">2 jam yang lalu &bull; Diterima di Kamar {{ $allTenants->first()?->room?->number ?? '101' }}</div>
                </div>
              </div>

              <!-- Item 2: Tagihan berhasil dibayar -->
              <div class="activity-timeline-item" onclick="window.switchTab('tab-bills')" style="cursor: pointer;" title="Buka Tagihan Sewa">
                <div class="activity-icon blue">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                  </svg>
                </div>
                <div class="activity-content">
                  <div class="activity-title">Tagihan berhasil dibayar</div>
                  <div class="activity-meta">3 jam yang lalu &bull; Pembayaran Kamar {{ $billingList->first()?->number ?? '105' }}</div>
                </div>
              </div>

              <!-- Item 3: Kamar dalam perbaikan -->
              <div class="activity-timeline-item" onclick="window.switchTab('tab-maintenance')" style="cursor: pointer;" title="Buka Servis Kamar">
                <div class="activity-icon amber">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                  </svg>
                </div>
                <div class="activity-content">
                  <div class="activity-title">Kamar dalam perbaikan</div>
                  <div class="activity-meta">5 jam yang lalu &bull; Kamar {{ $allMaintenance->first()?->room?->number ?? '104' }}</div>
                </div>
              </div>

              <!-- Item 4: Penghuni pindah kamar -->
              <div class="activity-timeline-item" onclick="window.switchTab('tab-transfers')" style="cursor: pointer;" title="Buka Pindah Kamar">
                <div class="activity-icon purple">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 3 21 3 21 8"></polyline>
                    <line x1="4" y1="20" x2="21" y2="3"></line>
                    <polyline points="21 16 21 21 16 21"></polyline>
                    <line x1="15" y1="15" x2="21" y2="21"></line>
                  </svg>
                </div>
                <div class="activity-content">
                  <div class="activity-title">Penghuni pindah kamar</div>
                  <div class="activity-meta">1 hari yang lalu &bull; Dari Kamar {{ $roomTransfers->first()?->currentRoom?->number ?? '102' }} ke {{ $roomTransfers->first()?->targetRoom?->number ?? '106' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- =====================================================================
             4. BOTTOM ROW: 3 COLUMNS (GRAFIK OKUPANSI, RINGKASAN CABANG, TAGIHAN MENDATANG)
             ===================================================================== -->
        <div class="dashboard-bottom-row">
          <!-- Column 1: Grafik Okupansi Kamar -->
          <div class="card-occupancy-chart">
            <div class="chart-header">
              <div class="title-group">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <h2>Grafik Okupansi Kamar</h2>
              </div>
              <button type="button" class="header-dropdown-pill" id="occupancyTimeframeBtn" onclick="window.cycleOccupancyTimeframe()" title="Ganti Periode Grafik">
                <span id="occupancyTimeframeLabel">12 Bulan Terakhir</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
            </div>

            <!-- SVG Smooth Area Chart with Grid & Axis -->
            <div class="chart-svg-container">
              <svg viewBox="0 0 540 160" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <defs>
                  <linearGradient id="occupancyAreaGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.32"/>
                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                  </linearGradient>
                </defs>
                <!-- Grid Lines -->
                <line x1="42" y1="20" x2="530" y2="20" stroke="#f1f5f9" stroke-width="1.2"/>
                <line x1="42" y1="52" x2="530" y2="52" stroke="#f1f5f9" stroke-width="1.2"/>
                <line x1="42" y1="84" x2="530" y2="84" stroke="#f1f5f9" stroke-width="1.2"/>
                <line x1="42" y1="116" x2="530" y2="116" stroke="#f1f5f9" stroke-width="1.2"/>
                <line x1="42" y1="140" x2="530" y2="140" stroke="#e2e8f0" stroke-width="1.2"/>

                <!-- Y Axis Labels -->
                <text x="34" y="24" fill="#94a3b8" font-size="10" font-weight="600" text-anchor="end">100%</text>
                <text x="34" y="56" fill="#94a3b8" font-size="10" font-weight="600" text-anchor="end">75%</text>
                <text x="34" y="88" fill="#94a3b8" font-size="10" font-weight="600" text-anchor="end">50%</text>
                <text x="34" y="120" fill="#94a3b8" font-size="10" font-weight="600" text-anchor="end">25%</text>

                <!-- Gradient Area Fill under Bézier Curve -->
                <path id="occupancyChartArea" d="M 50 120 C 90 125, 120 115, 160 100 C 200 85, 230 110, 270 95 C 310 80, 350 70, 390 75 C 430 80, 460 50, 495 40 C 510 36, 525 36, 530 36 L 530 140 L 50 140 Z" fill="url(#occupancyAreaGrad)" style="transition: all 0.4s ease;"/>

                <!-- Stroke Line -->
                <path id="occupancyChartLine" d="M 50 120 C 90 125, 120 115, 160 100 C 200 85, 230 110, 270 95 C 310 80, 350 70, 390 75 C 430 80, 460 50, 495 40 C 510 36, 525 36, 530 36" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="transition: all 0.4s ease;"/>

                <!-- Key Peak Nodes with Tooltip Highlight -->
                <circle cx="50" cy="120" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
                <circle cx="160" cy="100" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
                <circle cx="270" cy="95" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
                <circle cx="390" cy="75" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
                <circle cx="495" cy="40" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
                <circle cx="530" cy="36" r="4.5" fill="#ffffff" stroke="#10b981" stroke-width="2.5"/>
              </svg>
            </div>
          </div>

          <!-- Column 2: Ringkasan Cabang -->
          <div class="card-branch-summary">
            <div class="branch-header">
              <div class="title-group">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                  <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
                  <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
                  <path d="M10 6h4"></path>
                  <path d="M10 10h4"></path>
                  <path d="M10 14h4"></path>
                  <path d="M10 18h4"></path>
                </svg>
                <h2>Ringkasan Cabang</h2>
              </div>
              <button type="button" class="header-dropdown-pill" id="branchSummaryDropdownBtn" onclick="window.cycleBranchSummaryFilter()" title="Filter Kamar Cabang">
                <span id="branchSummaryDropdownLabel">Semua Cabang</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
            </div>

            <div class="branch-summary-list" id="dashboardBranchSummaryList">
              <!-- Cabang 1 -->
              <div class="branch-summary-item" id="dashBranchItem1" onclick="window.filterDashboardLocation('Lokasi 1')" title="Filter denah ke {{ $branch1Name }}" style="cursor: pointer; {{ $b1Active ? '' : 'display: none !important;' }}">
                <img src="{{ asset($b1Image) }}" class="branch-thumb" id="dashThumbBranch1" alt="{{ $branch1Name }}" style="object-fit: cover;">
                <div class="branch-info">
                  <div class="branch-title"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></div>
                  <div class="branch-count">{{ $loc1Total ?? 8 }} Kamar</div>
                  <div class="branch-progress-wrap">
                    <div class="branch-progress-bar" style="width: {{ $loc1OccupancyRate ?? 90 }}%;"></div>
                  </div>
                </div>
                <div class="branch-rate">{{ $loc1OccupancyRate ?? 90 }}%</div>
              </div>

              <!-- Cabang 2 -->
              <div class="branch-summary-item" id="dashBranchItem2" onclick="window.filterDashboardLocation('Lokasi 2')" title="Filter denah ke {{ $branch2Name }}" style="cursor: pointer; {{ $b2Active ? '' : 'display: none !important;' }}">
                <img src="{{ asset($b2Image) }}" class="branch-thumb" id="dashThumbBranch2" alt="{{ $branch2Name }}" style="object-fit: cover;">
                <div class="branch-info">
                  <div class="branch-title"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span></div>
                  <div class="branch-count">{{ $loc2Total ?? 8 }} Kamar</div>
                  <div class="branch-progress-wrap">
                    <div class="branch-progress-bar cyan" style="width: {{ $loc2OccupancyRate ?? 85 }}%;"></div>
                  </div>
                </div>
                <div class="branch-rate">{{ $loc2OccupancyRate ?? 85 }}%</div>
              </div>

              <div id="dashBranchesAllHiddenNotice" style="{{ (!$b1Active && !$b2Active) ? 'display: block;' : 'display: none;' }} text-align: center; padding: 18px 12px; color: var(--text-muted); font-size: 0.85rem; background: var(--bg-surface); border-radius: 12px; border: 1px dashed var(--border-color);">
                Semua cabang saat ini diatur untuk tidak ditampilkan di Pengaturan Kos.
              </div>
            </div>
          </div>

          <!-- Column 3: Tagihan Mendatang -->
          <div class="card-upcoming-bills">
            <div class="bills-header">
              <div class="title-group">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h2>Tagihan Mendatang</h2>
              </div>
              <a href="javascript:void(0)" class="see-all-link" onclick="window.switchTab('tab-bills')">Lihat Semua &rarr;</a>
            </div>

            <div class="upcoming-bills-list">
              @php
                $upcomingDue = $billingList->first();
                $tenantAvatar = file_exists(public_path('assets/images/tenant_avatar.jpg'))
                    ? asset('assets/images/tenant_avatar.jpg')
                    : asset('assets/images/room_sample.jpg');
              @endphp
              <div class="upcoming-bill-row" onclick="window.switchTab('tab-bills')" title="Buka Tagihan Sewa" style="cursor: pointer;">
                <img src="{{ $tenantAvatar }}" class="upcoming-tenant-avatar" alt="Avatar Penghuni">
                <div class="upcoming-tenant-info">
                  <div class="upcoming-tenant-name">{{ $upcomingDue?->activeTenant?->name ?? 'Budi Santoso' }}</div>
                  <div class="upcoming-tenant-room">Kamar {{ $upcomingDue?->number ?? '107' }}</div>
                </div>
                <span class="upcoming-due-badge">Hari Ini</span>
                <div class="upcoming-bill-amount">Rp {{ number_format($upcomingDue?->price ?? 750000, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =====================================================================
           PANE 2: TAB DAFTAR KAMAR
           ===================================================================== -->
      <div class="tab-pane" id="tab-rooms">
        <div class="section-header rooms-action-bar" style="justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
          <button type="button" class="btn-primary-action" id="btnAddNewRoom">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Tambah Kamar Baru</span>
          </button>
          <button type="button" class="btn-secondary" id="btnAddNewFloor" onclick="window.openAddFloorModal()" style="border-radius: 12px; font-weight: 700; height: 42px; display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #0f766e; color: #0f766e; background: #f0fdfa;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M12 3v18"/></svg>
            <span>+ Tambah Lantai</span>
          </button>
          <button type="button" class="btn-secondary" id="btnDeleteFloor" onclick="window.openDeleteFloorModal()" style="border-radius: 12px; font-weight: 700; height: 42px; display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #fca5a5; color: #dc2626; background: #fef2f2; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <span>- Hapus Lantai</span>
          </button>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar" style="display: flex; flex-direction: column; gap: 14px; padding: 18px 20px;">
            <!-- BARIS 1: SEARCH BAR DI UJUNG KIRI, FILTER STATUS (KAMAR KOSONG DLL) DI KANAN -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; width: 100%;">
              {{-- SEARCH BAR DI UJUNG KIRI --}}
              <div class="table-search" style="width: 340px; max-width: 100%; min-width: 250px; margin-right: auto;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="roomSearchInput" placeholder="Cari kamar, tipe, cabang, lantai, harga, fasilitas..." oninput="window.applyRoomMasterFilters()">
              </div>

              {{-- FILTER STATUS KAMAR (TERMASUK KAMAR KOSONG) DI KANAN --}}
              <div class="filter-pills" id="roomStatusMasterFilters" style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                <button type="button" class="filter-pill active" data-rstatus="all" onclick="filterRoomMasterStatus('all', this)">Semua Status ({{ $totalRooms }})</button>
                <button type="button" class="filter-pill" data-rstatus="available" onclick="filterRoomMasterStatus('available', this)" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: #0f766e; border-color: #99f6e4;">
                  <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                  <span>Kamar Kosong ({{ $rooms->where('status', 'available')->count() }})</span>
                </button>
                <button type="button" class="filter-pill" data-rstatus="occupied" onclick="filterRoomMasterStatus('occupied', this)">Terisi ({{ $rooms->where('status', 'occupied')->count() }})</button>
                <button type="button" class="filter-pill" data-rstatus="due" onclick="filterRoomMasterStatus('due', this)">Jatuh Tempo ({{ $rooms->where('status', 'due')->count() }})</button>
                <button type="button" class="filter-pill" data-rstatus="repair" onclick="filterRoomMasterStatus('repair', this)">Perbaikan ({{ $rooms->where('status', 'repair')->count() }})</button>
              </div>
            </div>

            <!-- BARIS 2: FILTER CABANG & FILTER LANTAI DI SEBELAH KANAN -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; flex-wrap: wrap; width: 100%;">
              <!-- 2 TITIK LOKASI TABS FILTER (CABANG) -->
              <div class="location-switcher-bar" id="roomLocationFilters" style="display: flex; gap: 6px; flex-wrap: wrap; background: #f0fdfa; border: 1.5px solid #ccfbf1; padding: 4px 6px; border-radius: 12px;">
                <button type="button" class="loc-filter-btn active" data-location="all" onclick="filterRoomMasterLocation('all', this)" style="display: flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; border: none; font-size: 0.8rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: #0a5661; color: #ffffff; box-shadow: 0 2px 8px rgba(10,86,97,0.25);">
                  Semua Cabang ({{ $totalRooms }})
                </button>
                <button type="button" class="loc-filter-btn" data-location="Lokasi 1" onclick="filterRoomMasterLocation('Lokasi 1', this)" style="display: flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; border: 1px solid rgba(10,86,97,0.15); font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0a5661;">
                  <span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $roomsLoc1->count() }})
                </button>
                <button type="button" class="loc-filter-btn" data-location="Lokasi 2" onclick="filterRoomMasterLocation('Lokasi 2', this)" style="display: flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; border: 1px solid rgba(13,148,136,0.15); font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0d9488;">
                  <span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $roomsLoc2->count() }})
                </button>
              </div>

              <!-- FILTER LANTAI -->
              <div class="filter-pills" id="roomTabFilters" style="display: flex; gap: 6px; flex-wrap: wrap;">
                <button type="button" class="filter-pill active" data-floor="all" onclick="filterRoomMasterFloor('all', this)">Semua Lantai</button>
                @foreach($existingFloors as $fl)
                  <button type="button" class="filter-pill" data-floor="{{ $fl }}" onclick="filterRoomMasterFloor('{{ $fl }}', this)">Lantai {{ $fl }}</button>
                @endforeach
              </div>
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
              @forelse($rooms as $room)
                @php
                  $isLoc2 = ($room->location ?? '') === 'Lokasi 2';
                  $tName = $room->activeTenant ? $room->activeTenant->name : ($room->tenant ?: ($room->booked_by_name ?: ($room->status === 'occupied' || $room->status === 'due' ? ('Penghuni #' . $room->number) : null)));
                  $facs = is_array($room->facilities) ? implode(', ', $room->facilities) : '';
                  $branchLabel = $isLoc2 ? $branch2Name : $branch1Name;
                  $priceStr = 'rp ' . number_format($room->price, 0, ',', '.') . ' ' . $room->price;
                  $statusTxt = match($room->status) {
                    'occupied' => 'terisi lunas',
                    'available' => 'tersedia kosong',
                    'due' => 'jatuh tempo',
                    'repair' => 'servis perbaikan',
                    'booked' => 'booking dipesan',
                    default => $room->status
                  };
                  $fullSearchText = strtolower("kamar #{$room->number} {$room->number} {$branchLabel} {$room->location} {$room->type} {$facs} lantai {$room->floor} lt {$room->floor} {$priceStr} {$statusTxt} {$room->status} " . ($tName ?? ''));
                @endphp
                <tr class="room-master-row" data-number="{{ strtolower($room->number) }}" data-floor="{{ $room->floor }}" data-location="{{ $room->location ?? 'Lokasi 1' }}" data-status="{{ $room->status }}" data-search="{{ $fullSearchText }}">
                  <td style="font-weight:800;font-size:1rem;color:#0a5661;">#{{ $room->number }}</td>
                  <td>
                    @if($isLoc2)
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0f9ff;color:#0284c7;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #bae6fd;"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span></span>
                    @else
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0fdfa;color:#0a5661;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #99f6e4;"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></span>
                    @endif
                  </td>
                  <td>
                    <div style="font-weight:700;color:#1e293b;">{{ $room->type }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">{{ $facs }}</div>
                  </td>
                  <td>Lantai {{ $room->floor }}</td>
                  <td style="font-weight:800;color:#0a5661;">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                  <td>
                    @if($room->status === 'occupied')
                      <span class="status-badge occupied">● Terisi</span>
                    @elseif($room->status === 'available')
                      <span class="status-badge available">● Kosong</span>
                    @elseif($room->status === 'due')
                      <span class="status-badge due">Jatuh Tempo</span>
                    @elseif($room->status === 'repair')
                      <span class="status-badge repair">Servis</span>
                    @else
                      <span class="status-badge">{{ $room->status }}</span>
                    @endif
                  </td>
                  <td>{!! $tName ? ('<strong>' . e($tName) . '</strong>') : '<span style="color:var(--text-muted)">-</span>' !!}</td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <button type="button" class="btn-secondary" style="padding:5px 12px;font-size:0.78rem;font-weight:700;border-radius:8px;" onclick="window.kosApp.editRoom('{{ $room->number }}')">Edit</button>
                      <button type="button" class="btn-secondary" style="padding:5px 12px;font-size:0.78rem;font-weight:700;border-radius:8px;" onclick="window.kosApp.openRoomDetailModal('{{ $room->number }}')">Detail</button>
                      <button type="button" class="btn-sm" style="padding:5px 10px;font-size:0.78rem;font-weight:700;border-radius:8px;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" onclick="window.confirmDeleteRoom('{{ $room->id }}', '{{ $room->number }}')">Hapus</button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ditemukan kamar yang sesuai</td></tr>
              @endforelse
            </tbody>
          </table>

          <!-- Mobile Card View -->
          <div class="mobile-card-list" id="roomsMobileCardList">
            @foreach($rooms as $room)
              @php
                $isLoc2 = ($room->location ?? '') === 'Lokasi 2';
                $tName = $room->activeTenant ? $room->activeTenant->name : ($room->tenant ?: ($room->booked_by_name ?: ($room->status === 'occupied' || $room->status === 'due' ? ('Penghuni #' . $room->number) : null)));
                $facs = is_array($room->facilities) ? implode(', ', $room->facilities) : '';
                $branchLabel = $isLoc2 ? $branch2Name : $branch1Name;
                $priceStr = 'rp ' . number_format($room->price, 0, ',', '.') . ' ' . $room->price;
                $statusTxt = match($room->status) {
                  'occupied' => 'terisi lunas',
                  'available' => 'tersedia kosong',
                  'due' => 'jatuh tempo',
                  'repair' => 'servis perbaikan',
                  'booked' => 'booking dipesan',
                  default => $room->status
                };
                $fullSearchText = strtolower("kamar #{$room->number} {$room->number} {$branchLabel} {$room->location} {$room->type} {$facs} lantai {$room->floor} lt {$room->floor} {$priceStr} {$statusTxt} {$room->status} " . ($tName ?? ''));
              @endphp
              <div class="mobile-data-card" data-floor="{{ $room->floor }}" data-location="{{ $room->location ?? 'Lokasi 1' }}" data-status="{{ $room->status }}" data-search="{{ $fullSearchText }}">
                <div class="mobile-data-header">
                  <div>
                    <span class="mobile-data-title">Kamar #{{ $room->number }} ({{ $room->type }})</span>
                    <div style="margin-top:3px;">
                      @if($isLoc2)
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0f9ff;color:#0284c7;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #bae6fd;"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span></span>
                      @else
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0fdfa;color:#0a5661;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #99f6e4;"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></span>
                      @endif
                    </div>
                  </div>
                  @if($room->status === 'occupied')
                    <span class="status-badge occupied">● Terisi</span>
                  @elseif($room->status === 'available')
                    <span class="status-badge available">● Kosong</span>
                  @elseif($room->status === 'due')
                    <span class="status-badge due">Jatuh Tempo</span>
                  @elseif($room->status === 'repair')
                    <span class="status-badge repair">Servis</span>
                  @else
                    <span class="status-badge">{{ $room->status }}</span>
                  @endif
                </div>
                <div class="mobile-data-row">
                  <span>Lantai:</span>
                  <span>Lantai {{ $room->floor }}</span>
                </div>
                <div class="mobile-data-row">
                  <span>Tarif Sewa:</span>
                  <span style="color:#0a5661;font-weight:700;">Rp {{ number_format($room->price, 0, ',', '.') }} /bln</span>
                </div>
                <div class="mobile-data-row">
                  <span>Penghuni:</span>
                  <span>{{ $tName ?: 'Kosong' }}</span>
                </div>
                <div style="display:flex;gap:8px;margin-top:8px;">
                  <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:6px;border-radius:8px;" onclick="window.kosApp.editRoom('{{ $room->number }}')">Edit Kamar</button>
                  <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:6px;border-radius:8px;" onclick="window.kosApp.openRoomDetailModal('{{ $room->number }}')">Lihat Detail</button>
                  <button type="button" style="padding:6px 12px;font-size:0.8rem;font-weight:700;border-radius:8px;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;cursor:pointer;" onclick="window.confirmDeleteRoom('{{ $room->id }}', '{{ $room->number }}')">Hapus</button>
                </div>
              </div>
            @endforeach
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
          $dbTenants = $allTenants ?? \App\Models\Tenant::with('room')->where('status', 'active')->orderBy('id', 'desc')->get();
          $tenantRoomIds = $dbTenants->pluck('room_id')->filter()->all();
          $legacyOccupiedRooms = $rooms->filter(fn($r) => in_array($r->status, ['occupied', 'due']) && !in_array($r->id, $tenantRoomIds));
          $allActiveTenantsCount = $dbTenants->count() + $legacyOccupiedRooms->count();
          $b1TenantsCount = $dbTenants->filter(fn($t) => ($t->room?->location ?? 'Lokasi 1') === 'Lokasi 1')->count() + $legacyOccupiedRooms->filter(fn($r) => ($r->location ?? 'Lokasi 1') === 'Lokasi 1')->count();
          $b2TenantsCount = $dbTenants->filter(fn($t) => ($t->room?->location ?? '') === 'Lokasi 2')->count() + $legacyOccupiedRooms->filter(fn($r) => ($r->location ?? '') === 'Lokasi 2')->count();
        @endphp

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (DATA PENGHUNI) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="tenantLocationFilters">
            <button type="button" class="filter-pill active" data-tloc="all" onclick="filterTenantLocation('all', this)">Semua Cabang ({{ $allActiveTenantsCount }})</button>
            <button type="button" class="filter-pill" data-tloc="Lokasi 1" onclick="filterTenantLocation('Lokasi 1', this)"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $b1TenantsCount }})</button>
            <button type="button" class="filter-pill" data-tloc="Lokasi 2" onclick="filterTenantLocation('Lokasi 2', this)"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $b2TenantsCount }})</button>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="tenantSearchInput" placeholder="Cari nama, kamar, cabang, atau nomor WhatsApp..." oninput="filterTenantTable()">
            </div>
            <div class="filter-pills" id="tenantStatusFilter">
              <button type="button" class="filter-pill active" data-tstatus="all" onclick="filterTenantStatus('all', this)">Semua</button>
              <button type="button" class="filter-pill" data-tstatus="active" onclick="filterTenantStatus('active', this)">Aktif Menghuni</button>
              <button type="button" class="filter-pill" data-tstatus="due" onclick="filterTenantStatus('due', this)">Jatuh Tempo</button>
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
              @foreach($dbTenants as $tItem)
                @php
                  $tName = $tItem->name;
                  $tPhone = $tItem->phone ?? '';
                  $cleanPhone = preg_replace('/[^0-9]/', '', $tPhone);
                  if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                  $tStart = $tItem->start_date ? \Carbon\Carbon::parse($tItem->start_date)->format('Y-m-d') : '-';
                  $tDue = $tItem->due_day ?? 1;
                  $roomObj = $tItem->room;
                  $rNum = $roomObj ? $roomObj->number : '-';
                  $rFloor = $roomObj ? ($roomObj->floor ?? 1) : 1;
                  $rPrice = $roomObj ? $roomObj->price : ($tItem->rent_amount ?? 0);
                  $rType = $roomObj ? ($roomObj->type ?? '-') : '-';
                  $rLoc = $roomObj ? ($roomObj->location ?? 'Lokasi 1') : 'Lokasi 1';
                  $isDue = $roomObj ? ($roomObj->status === 'due') : false;
                  $isLoc2 = $rLoc === 'Lokasi 2';
                @endphp
                <tr class="tenant-row" data-location="{{ $rLoc }}" data-status="{{ $isDue ? 'due' : 'active' }}" data-search="{{ strtolower($tName . ' ' . $rNum . ' ' . $rLoc . ' ' . $tPhone . ' ' . $rType) }}">
                  <td>
                    <div style="font-weight:700;">{{ $tName }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $rType }}</div>
                  </td>
                  <td>
                    <strong style="color:var(--brand-primary-light);">Kamar #{{ $rNum }}</strong>
                    <div style="font-size:0.72rem;color:var(--text-muted);">Lantai {{ $rFloor }} &bull; Rp {{ number_format($rPrice, 0, ',', '.') }}/bln</div>
                  </td>
                  <td>
                    @if($isLoc2)
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">{{ $branch2Name }}</span>
                      <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Smart Door PIN &amp; Co-Working</div>
                    @else
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">{{ $branch1Name }}</span>
                      <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Kartu RFID &amp; Area Kampus</div>
                    @endif
                  </td>
                  <td>
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" style="color:var(--accent-blue);display:inline-flex;align-items:center;gap:4px;font-weight:600;">
                      {{ $tPhone ?: '-' }}
                    </a>
                  </td>
                  <td>{{ $tStart }}</td>
                  <td>
                    <button type="button" class="btn-edit-due-date" onclick="window.openEditDueDayModal('{{ $tItem->id }}', '{{ addslashes($tName) }}', '{{ $rNum }}', {{ $tDue }})" title="Klik untuk ubah tanggal jatuh tempo sewa">
                      <span>Tgl <strong id="dueDayText_{{ $rNum }}">{{ $tDue }}</strong> / bln</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </button>
                  </td>
                  <td>
                    @if($isDue)
                      <span class="status-badge due">Jatuh Tempo</span>
                    @else
                      <span class="status-badge occupied">Lunas</span>
                    @endif
                  </td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.sendWhatsAppReminder('{{ $rNum }}')">Tagihan</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.recordPaymentForRoom('{{ $rNum }}')">Bayar</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.openEditTenantModal('{{ $tItem->id }}', '{{ $rNum }}')">Edit</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;color:#f87171;" onclick="window.kosApp.checkoutTenant('{{ $rNum }}')">Keluar</button>
                    </div>
                  </td>
                </tr>
              @endforeach
              @foreach($legacyOccupiedRooms as $roomItem)
                @php
                  $tName = $roomItem->tenant ?: ($roomItem->booked_by_name ?: ('Penghuni #' . $roomItem->number));
                  $tPhone = $roomItem->phone ?? '';
                  $cleanPhone = preg_replace('/[^0-9]/', '', $tPhone);
                  if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                  $tStart = '-';
                  $tDue = $roomItem->due_day ?? 1;
                  $isDue = $roomItem->status === 'due';
                  $isLoc2 = ($roomItem->location ?? '') === 'Lokasi 2';
                @endphp
                <tr class="tenant-row" data-location="{{ $roomItem->location ?? 'Lokasi 1' }}" data-status="{{ $isDue ? 'due' : 'active' }}" data-search="{{ strtolower($tName . ' ' . $roomItem->number . ' ' . ($roomItem->location ?? '') . ' ' . $tPhone . ' ' . ($roomItem->type ?? '')) }}">
                  <td>
                    <div style="font-weight:700;">{{ $tName }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $roomItem->type ?? '-' }}</div>
                  </td>
                  <td>
                    <strong style="color:var(--brand-primary-light);">Kamar #{{ $roomItem->number }}</strong>
                    <div style="font-size:0.72rem;color:var(--text-muted);">Lantai {{ $roomItem->floor ?? 1 }} &bull; Rp {{ number_format($roomItem->price, 0, ',', '.') }}/bln</div>
                  </td>
                  <td>
                    @if($isLoc2)
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">{{ $branch2Name }}</span>
                      <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Smart Door PIN &amp; Co-Working</div>
                    @else
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">{{ $branch1Name }}</span>
                      <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Kartu RFID &amp; Area Kampus</div>
                    @endif
                  </td>
                  <td>
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" style="color:var(--accent-blue);display:inline-flex;align-items:center;gap:4px;font-weight:600;">
                      {{ $tPhone ?: '-' }}
                    </a>
                  </td>
                  <td>{{ $tStart }}</td>
                  <td>
                    <button type="button" class="btn-edit-due-date" onclick="window.openEditDueDayModal('{{ $roomItem->id }}', '{{ addslashes($tName) }}', '{{ $roomItem->number }}', {{ $tDue }})" title="Klik untuk ubah tanggal jatuh tempo sewa">
                      <span>Tgl <strong id="dueDayText_{{ $roomItem->number }}">{{ $tDue }}</strong> / bln</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </button>
                  </td>
                  <td>
                    @if($isDue)
                      <span class="status-badge due">Jatuh Tempo</span>
                    @else
                      <span class="status-badge occupied">Lunas</span>
                    @endif
                  </td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.sendWhatsAppReminder('{{ $roomItem->number }}')">Tagihan</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.recordPaymentForRoom('{{ $roomItem->number }}')">Bayar</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.openEditTenantModal('', '{{ $roomItem->number }}')">Edit</button>
                      <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;color:#f87171;" onclick="window.kosApp.checkoutTenant('{{ $roomItem->number }}')">Keluar</button>
                    </div>
                  </td>
                </tr>
              @endforeach
              @if($dbTenants->count() === 0 && $legacyOccupiedRooms->count() === 0)
                <tr><td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada data penghuni</td></tr>
              @endif
            </tbody>
          </table>

          <div class="mobile-card-list" id="tenantsMobileCardList">
            @foreach($dbTenants as $tItem)
              @php
                $tName = $tItem->name;
                $tPhone = $tItem->phone ?? '';
                $cleanPhone = preg_replace('/[^0-9]/', '', $tPhone);
                if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                $tDue = $tItem->due_day ?? 1;
                $roomObj = $tItem->room;
                $rNum = $roomObj ? $roomObj->number : '-';
                $rPrice = $roomObj ? $roomObj->price : ($tItem->rent_amount ?? 0);
                $rType = $roomObj ? ($roomObj->type ?? '-') : '-';
                $rLoc = $roomObj ? ($roomObj->location ?? 'Lokasi 1') : 'Lokasi 1';
                $isDue = $roomObj ? ($roomObj->status === 'due') : false;
                $isLoc2 = $rLoc === 'Lokasi 2';
              @endphp
              <div class="mobile-data-card" data-location="{{ $rLoc }}" data-status="{{ $isDue ? 'due' : 'active' }}" data-search="{{ strtolower($tName . ' ' . $rNum . ' ' . $rLoc . ' ' . $tPhone . ' ' . $rType) }}">
                <div class="mobile-data-header">
                  <span class="mobile-data-title">{{ $tName }}</span>
                  @if($isDue)
                    <span class="status-badge due">Jatuh Tempo</span>
                  @else
                    <span class="status-badge occupied">Lunas</span>
                  @endif
                </div>
                <div class="mobile-data-row">
                  <span>Cabang Kos:</span>
                  <span>{{ $isLoc2 ? $branch2Name : $branch1Name }}</span>
                </div>
                <div class="mobile-data-row">
                  <span>Kamar:</span>
                  <span style="font-weight:700;">Kamar #{{ $rNum }} ({{ $rType }})</span>
                </div>
                <div class="mobile-data-row">
                  <span>WhatsApp:</span>
                  <span><a href="https://wa.me/{{ $cleanPhone }}" target="_blank" style="color:var(--accent-blue);">{{ $tPhone ?: '-' }}</a></span>
                </div>
                <div class="mobile-data-row">
                  <span>Jatuh Tempo:</span>
                  <span>Tanggal {{ $tDue }} setiap bulan</span>
                </div>
                <div class="mobile-data-row">
                  <span>Tarif Sewa:</span>
                  <span style="font-weight:700;color:var(--brand-primary-light);">Rp {{ number_format($rPrice, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;gap:8px;margin-top:10px;">
                  <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.sendWhatsAppReminder('{{ $rNum }}')">Tagihan</button>
                  <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.recordPaymentForRoom('{{ $rNum }}')">Catat Bayar</button>
                  <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.openEditTenantModal('{{ $tItem->id }}', '{{ $rNum }}')">Edit</button>
                  <button type="button" class="btn-secondary" style="font-size:0.8rem;padding:7px;color:#f87171;" onclick="window.kosApp.checkoutTenant('{{ $rNum }}')" title="Penghuni Keluar">Keluar</button>
                </div>
              </div>
            @endforeach
            @foreach($legacyOccupiedRooms as $roomItem)
              @php
                $tName = $roomItem->tenant ?: ($roomItem->booked_by_name ?: ('Penghuni #' . $roomItem->number));
                $tPhone = $roomItem->phone ?? '';
                $cleanPhone = preg_replace('/[^0-9]/', '', $tPhone);
                if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                $tDue = $roomItem->due_day ?? 1;
                $isDue = $roomItem->status === 'due';
                $isLoc2 = ($roomItem->location ?? '') === 'Lokasi 2';
              @endphp
              <div class="mobile-data-card" data-location="{{ $roomItem->location ?? 'Lokasi 1' }}" data-status="{{ $isDue ? 'due' : 'active' }}" data-search="{{ strtolower($tName . ' ' . $roomItem->number . ' ' . ($roomItem->location ?? '') . ' ' . $tPhone . ' ' . ($roomItem->type ?? '')) }}">
                <div class="mobile-data-header">
                  <span class="mobile-data-title">{{ $tName }}</span>
                  @if($isDue)
                    <span class="status-badge due">Jatuh Tempo</span>
                  @else
                    <span class="status-badge occupied">Lunas</span>
                  @endif
                </div>
                <div class="mobile-data-row">
                  <span>Cabang Kos:</span>
                  <span>{{ $isLoc2 ? $branch2Name : $branch1Name }}</span>
                </div>
                <div class="mobile-data-row">
                  <span>Kamar:</span>
                  <span style="font-weight:700;">Kamar #{{ $roomItem->number }} ({{ $roomItem->type ?? '-' }})</span>
                </div>
                <div class="mobile-data-row">
                  <span>WhatsApp:</span>
                  <span><a href="https://wa.me/{{ $cleanPhone }}" target="_blank" style="color:var(--accent-blue);">{{ $tPhone ?: '-' }}</a></span>
                </div>
                <div class="mobile-data-row">
                  <span>Jatuh Tempo:</span>
                  <span>Tanggal {{ $tDue }} setiap bulan</span>
                </div>
                <div class="mobile-data-row">
                  <span>Tarif Sewa:</span>
                  <span style="font-weight:700;color:var(--brand-primary-light);">Rp {{ number_format($roomItem->price, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;gap:8px;margin-top:10px;">
                  <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.sendWhatsAppReminder('{{ $roomItem->number }}')">Tagihan</button>
                  <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.recordPaymentForRoom('{{ $roomItem->number }}')">Catat Bayar</button>
                  <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.openEditTenantModal('', '{{ $roomItem->number }}')">Edit</button>
                  <button type="button" class="btn-secondary" style="font-size:0.8rem;padding:7px;color:#f87171;" onclick="window.kosApp.checkoutTenant('{{ $roomItem->number }}')" title="Penghuni Keluar">Keluar</button>
                </div>
              </div>
            @endforeach
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
          $nowMonthNum = now()->month;
          $nowYearNum = now()->year;
          $nowMonthName = now()->translatedFormat('F Y');

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

          // Kalkulasi pelacak cicilan aktif bulan berjalan
          $activeInstallmentRoomsCount = 0;
          $totalRemainingInstallment = 0;
          foreach ($rooms as $chkR) {
              if ($chkR->status === 'available') continue;
              $chkIncomes = ($allFinances ?? collect())->filter(function($f) use ($chkR, $nowMonthNum, $nowYearNum) {
                  if ($f->type !== 'income' || $f->room_id != $chkR->id) return false;
                  $dt = \Carbon\Carbon::parse($f->date);
                  return $dt->month === $nowMonthNum && $dt->year === $nowYearNum;
              });
              $chkPaid = (float) $chkIncomes->sum('amount');
              $chkPrice = (float) $chkR->price;
              $chkRem = max(0, $chkPrice - $chkPaid);
              if ($chkPaid > 0 && $chkRem > 0) {
                  $activeInstallmentRoomsCount++;
                  $totalRemainingInstallment += $chkRem;
              }
          }
        @endphp

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (TAGIHAN SEWA) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="billingLocationFilters">
            <button type="button" class="filter-pill active" onclick="filterBillingLocation('all', this)">Semua Cabang ({{ $totalRooms }} Unit)</button>
            <button type="button" class="filter-pill" onclick="filterBillingLocation('Lokasi 1', this)"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span> ({{ $b1RoomsList->count() }} Unit)</button>
            <button type="button" class="filter-pill" onclick="filterBillingLocation('Lokasi 2', this)"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span> ({{ $b2RoomsList->count() }} Unit)</button>
          </div>
        </div>

        <!-- KPI Cards Tagihan & Monitoring Cicilan -->
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
              <span class="stat-card-title">Tagihan Jatuh Tempo</span>
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
              <span class="stat-card-title">Cicilan Aktif Bulan Ini</span>
              <div class="stat-icon teal" style="background: rgba(245, 158, 11, 0.12); color: #d97706; border-color: rgba(245, 158, 11, 0.3);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="billKpiPartialVal" style="color:#d97706;">{{ $activeInstallmentRoomsCount }} Kamar</div>
            <div class="stat-card-meta">
              <span style="color:#b45309;font-weight:700;">Sisa: Rp {{ number_format($totalRemainingInstallment, 0, ',', '.') }}</span>
              <span>terpantau dicicil sebagian</span>
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
              <button type="button" class="filter-pill" onclick="filterBillingStatus('partial', this)" style="color:#d97706;font-weight:800;">Cicilan Aktif ({{ $activeInstallmentRoomsCount }})</button>
              <button type="button" class="filter-pill" onclick="filterBillingStatus('due', this)" style="color:#ef4444;">Jatuh Tempo ({{ $dueRooms }})</button>
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
                $rRentPrice = (float) $r->price;

                // Hitung total cicilan masuk pada bulan berjalan untuk unit kamar ini
                $rIncomeThisMonth = ($allFinances ?? collect())->filter(function($f) use ($r, $nowMonthNum, $nowYearNum) {
                    if ($f->type !== 'income' || $f->room_id != $r->id) return false;
                    $dt = \Carbon\Carbon::parse($f->date);
                    return $dt->month === $nowMonthNum && $dt->year === $nowYearNum;
                });
                $paidThisMonth = (float) $rIncomeThisMonth->sum('amount');
                $remainingBill = max(0, $rRentPrice - $paidThisMonth);
                $isPartialInstallment = ($paidThisMonth > 0 && $remainingBill > 0);
                $percentPaid = $rRentPrice > 0 ? min(100, round(($paidThisMonth / $rRentPrice) * 100)) : 0;

                $isDue = $r->status === 'due';
                $isOccupied = $r->status === 'occupied';
                $isAvailable = $r->status === 'available';

                if ($isAvailable) {
                    $statusType = 'available';
                } elseif ($isPartialInstallment) {
                    $statusType = 'partial';
                } elseif ($paidThisMonth >= $rRentPrice || ($isOccupied && !$isDue)) {
                    $statusType = 'paid';
                } else {
                    $statusType = 'due';
                }

                $tenantName = $t ? $t->name : '-';
                $tenantPhone = $t ? $t->phone : '';
                $dueDay = $t ? $t->due_day : 5;
                $rLoc = $r->location ?? (intval($r->number) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                $isLoc2 = $rLoc === 'Lokasi 2';
                $branchLabel = $isLoc2 ? ($branch2Name ?? 'Kos Mas Robi 2') : ($branch1Name ?? 'Kos Mas Robi 1');
                $bankName = $setting->bank_name ?? 'BCA';
                $bankAccount = $setting->bank_account ?? '8830192831';
                $bankHolder = $setting->bank_holder ?? 'Robi Suryanto';

                // Teks pesan WhatsApp otomatis (Deteksi status cicilan aktif vs tagihan baru)
                if ($isPartialInstallment) {
                    $waRawMsg = "Halo Kak {$tenantName}, kami dari Pengelola {$branchLabel} menginfokan update sewa Kamar #{$r->number} periode {$nowMonthName}.\n\nDari total tagihan sewa sebesar Rp " . number_format($rRentPrice, 0, ',', '.') . ", pembayaran cicilan yang sudah tercatat masuk adalah *Rp " . number_format($paidThisMonth, 0, ',', '.') . " ({$percentPaid}%)*.\n\nSisa tagihan yang perlu dilunasi adalah sebesar *Rp " . number_format($remainingBill, 0, ',', '.') . "*.\n\nPembayaran sisa dapat ditransfer ke rekening resmi kami:\n- Bank: {$bankName}\n- No. Rekening: {$bankAccount}\n- Atas Nama: {$bankHolder}\n\nSetelah melakukan transfer, mohon kirimkan bukti transfer melalui WhatsApp ini agar kuitansi pelunasan dapat segera kami terbitkan. Terima kasih banyak Kak!";
                } else {
                    $waRawMsg = "Halo Kak {$tenantName}, kami dari Pengelola {$branchLabel} ingin menginformasikan terkait tagihan sewa Kamar #{$r->number} periode {$nowMonthName} sebesar Rp " . number_format($rRentPrice, 0, ',', '.') . ".\n\nPembayaran dapat ditransfer ke rekening:\n- Bank: {$bankName}\n- No. Rekening: {$bankAccount}\n- Atas Nama: {$bankHolder}\n\nTerima kasih banyak Kak!";
                }
                $waText = rawurlencode($waRawMsg);
              @endphp
              <tr data-status="{{ $statusType }}" data-location="{{ $rLoc }}" data-price="{{ (int)$rRentPrice }}" data-rem="{{ (int)$remainingBill }}" data-paid="{{ (int)$paidThisMonth }}" data-search="{{ strtolower($r->number . ' ' . $rLoc . ' ' . $r->type . ' ' . $tenantName . ' ' . ($isPartialInstallment ? 'cicilan aktif sebagian' : ($statusType === 'paid' ? 'lunas' : ($isDue ? 'jatuh tempo belum lunas' : 'kosong')))) }}">
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
                  Rp {{ number_format($rRentPrice, 0, ',', '.') }}
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
                  @if($isPartialInstallment)
                    <div style="display: flex; flex-direction: column; gap: 3px; min-width: 170px;">
                      <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 800; padding: 3px 9px; border-radius: 999px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.72rem; width: fit-content;">
                        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span>
                        <span>Cicilan Aktif ({{ $percentPaid }}%)</span>
                      </span>
                      <div style="font-size: 0.72rem; color: #475569;">
                        <span>Masuk: <strong style="color: #059669;">Rp {{ number_format($paidThisMonth, 0, ',', '.') }}</strong></span> &bull; 
                        <span>Sisa: <strong style="color: #dc2626;">Rp {{ number_format($remainingBill, 0, ',', '.') }}</strong></span>
                      </div>
                      <div style="width: 100%; height: 5px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-top: 2px;">
                        <div style="width: {{ $percentPaid }}%; height: 100%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 999px;"></div>
                      </div>
                    </div>
                  @elseif($statusType === 'paid')
                    <span class="status-badge occupied" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      Lunas Bulan Ini
                    </span>
                  @elseif($isDue)
                    <span class="status-badge due" style="font-weight: 800; padding: 4px 12px; border-radius: 999px;">
                      Belum Lunas (Jatuh Tempo)
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
                      @endphp
                      <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn-secondary" style="background:#059669;color:#ffffff;border:none;padding:5px 10px;font-size:0.76rem;font-weight:700;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" title="Kirim Pesan WhatsApp Penagihan / Info Sisa Cicilan">
                        {{ $isPartialInstallment ? 'WA Sisa Cicilan' : 'WA Tagihan' }}
                      </a>
                      <button type="button" class="btn-secondary" style="background:{{ $isPartialInstallment ? '#d97706' : '#0a5661' }};color:#ffffff;border:none;padding:5px 11px;font-size:0.76rem;font-weight:800;border-radius:8px;display:inline-flex;align-items:center;gap:4px;" onclick="openManualPaymentReceiptModal('{{ $r->id }}', '{{ $r->number }}', '{{ addslashes($t->name) }}', {{ (int)($isPartialInstallment ? $remainingBill : $rRentPrice) }}, '{{ $rLoc }}', {{ (int)$paidThisMonth }}, {{ (int)$remainingBill }}, {{ (int)$rRentPrice }})" title="Catat pembayaran & terbitkan kuitansi digital resmi">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>{{ $isPartialInstallment ? 'Pelunasan Cicilan' : 'Input & Kirim Kuitansi' }}</span>
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

          <!-- Mobile Card List Tagihan Sewa -->
          <div class="mobile-card-list" id="billingMobileCardList">
            @forelse($rooms as $r)
              @php
                $t = $r->activeTenant;
                $rRentPrice = (float) $r->price;

                $rIncomeThisMonth = ($allFinances ?? collect())->filter(function($f) use ($r, $nowMonthNum, $nowYearNum) {
                    if ($f->type !== 'income' || $f->room_id != $r->id) return false;
                    $dt = \Carbon\Carbon::parse($f->date);
                    return $dt->month === $nowMonthNum && $dt->year === $nowYearNum;
                });
                $paidThisMonth = (float) $rIncomeThisMonth->sum('amount');
                $remainingBill = max(0, $rRentPrice - $paidThisMonth);
                $isPartialInstallment = ($paidThisMonth > 0 && $remainingBill > 0);
                $percentPaid = $rRentPrice > 0 ? min(100, round(($paidThisMonth / $rRentPrice) * 100)) : 0;

                $isDue = $r->status === 'due';
                $isOccupied = $r->status === 'occupied';
                $isAvailable = $r->status === 'available';

                if ($isAvailable) {
                    $statusType = 'available';
                } elseif ($isPartialInstallment) {
                    $statusType = 'partial';
                } elseif ($paidThisMonth >= $rRentPrice || ($isOccupied && !$isDue)) {
                    $statusType = 'paid';
                } else {
                    $statusType = 'due';
                }

                $tenantName = $t ? $t->name : '-';
                $tenantPhone = $t ? $t->phone : '';
                $dueDay = $t ? $t->due_day : 5;
                $rLoc = $r->location ?? (intval($r->number) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                $isLoc2 = $rLoc === 'Lokasi 2';
                $branchLabel = $isLoc2 ? ($branch2Name ?? 'Kos Mas Robi 2') : ($branch1Name ?? 'Kos Mas Robi 1');
                $bankName = $setting->bank_name ?? 'BCA';
                $bankAccount = $setting->bank_account ?? '8830192831';
                $bankHolder = $setting->bank_holder ?? 'Robi Suryanto';

                if ($isPartialInstallment) {
                    $waRawMsg = "Halo Kak {$tenantName}, kami dari Pengelola {$branchLabel} menginfokan update sewa Kamar #{$r->number} periode {$nowMonthName}.\n\nDari total tagihan sewa sebesar Rp " . number_format($rRentPrice, 0, ',', '.') . ", pembayaran cicilan yang sudah tercatat masuk adalah *Rp " . number_format($paidThisMonth, 0, ',', '.') . " ({$percentPaid}%)*.\n\nSisa tagihan yang perlu dilunasi adalah sebesar *Rp " . number_format($remainingBill, 0, ',', '.') . "*.\n\nPembayaran sisa dapat ditransfer ke rekening resmi kami:\n- Bank: {$bankName}\n- No. Rekening: {$bankAccount}\n- Atas Nama: {$bankHolder}\n\nSetelah melakukan transfer, mohon kirimkan bukti transfer melalui WhatsApp ini agar kuitansi pelunasan dapat segera kami terbitkan. Terima kasih banyak Kak!";
                } else {
                    $waRawMsg = "Halo Kak {$tenantName}, kami dari Pengelola {$branchLabel} ingin menginformasikan terkait tagihan sewa Kamar #{$r->number} periode {$nowMonthName} sebesar Rp " . number_format($rRentPrice, 0, ',', '.') . ".\n\nPembayaran dapat ditransfer ke rekening:\n- Bank: {$bankName}\n- No. Rekening: {$bankAccount}\n- Atas Nama: {$bankHolder}\n\nTerima kasih banyak Kak!";
                }
                $waText = rawurlencode($waRawMsg);
                $cleanPhone = preg_replace('/[^0-9]/', '', $tenantPhone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
              @endphp
              <div class="mobile-data-card" data-status="{{ $statusType }}" data-location="{{ $rLoc }}" data-price="{{ (int)$rRentPrice }}" data-rem="{{ (int)$remainingBill }}" data-paid="{{ (int)$paidThisMonth }}" data-search="{{ strtolower($r->number . ' ' . $rLoc . ' ' . $r->type . ' ' . $tenantName . ' ' . ($isPartialInstallment ? 'cicilan aktif sebagian' : ($statusType === 'paid' ? 'lunas' : ($isDue ? 'jatuh tempo belum lunas' : 'kosong')))) }}">
                <div class="mobile-data-header">
                  <div>
                    <span class="mobile-data-title">Kamar #{{ $r->number }}</span>
                    <div style="font-size: 0.74rem; color: var(--text-secondary); margin-top: 2px;">Lt. {{ $r->floor }} &bull; {{ $r->type }}</div>
                  </div>
                  <div>
                    @if($isPartialInstallment)
                      <span class="status-badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 800;">
                        Cicilan ({{ $percentPaid }}%)
                      </span>
                    @elseif($statusType === 'paid')
                      <span class="status-badge occupied" style="font-weight: 800;">Lunas</span>
                    @elseif($isDue)
                      <span class="status-badge due" style="font-weight: 800;">Jatuh Tempo</span>
                    @else
                      <span class="status-badge available">● Kosong</span>
                    @endif
                  </div>
                </div>

                <div class="mobile-data-row">
                  <span>Cabang:</span>
                  <span>
                    @if($isLoc2)
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;background:#f0f9ff;color:#0284c7;font-weight:800;padding:2px 8px;border-radius:6px;border:1px solid #bae6fd;"><span data-branch-name="lokasi_2">{{ $branch2Name }}</span></span>
                    @else
                      <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;background:#f0fdfa;color:#0a5661;font-weight:800;padding:2px 8px;border-radius:6px;border:1px solid #99f6e4;"><span data-branch-name="lokasi_1">{{ $branch1Name }}</span></span>
                    @endif
                  </span>
                </div>

                <div class="mobile-data-row">
                  <span>Penghuni:</span>
                  <span style="font-weight: 700;">{{ $t ? $t->name : 'Unit Kosong' }}</span>
                </div>

                <div class="mobile-data-row">
                  <span>Sewa Bulanan:</span>
                  <span style="font-weight: 800; color: #0a5661;">Rp {{ number_format($rRentPrice, 0, ',', '.') }}</span>
                </div>

                <div class="mobile-data-row">
                  <span>Jatuh Tempo:</span>
                  <span style="{{ $isDue ? 'color:#dc2626;font-weight:700;' : '' }}">
                    {{ $t ? ('Tiap Tgl ' . $dueDay . ' / bln') : '-' }}
                  </span>
                </div>

                @if($isPartialInstallment)
                  <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 8px 10px; margin: 8px 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.72rem; margin-bottom: 4px;">
                      <span>Masuk: <strong style="color: #059669;">Rp {{ number_format($paidThisMonth, 0, ',', '.') }}</strong></span>
                      <span>Sisa: <strong style="color: #dc2626;">Rp {{ number_format($remainingBill, 0, ',', '.') }}</strong></span>
                    </div>
                    <div style="width: 100%; height: 5px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                      <div style="width: {{ $percentPaid }}%; height: 100%; background: linear-gradient(90deg, #f59e0b, #10b981); border-radius: 999px;"></div>
                    </div>
                  </div>
                @endif

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 10px;">
                  @if($t)
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn-secondary" style="background:#059669;color:#ffffff;border:none;padding:8px 6px;font-size:0.76rem;font-weight:700;border-radius:10px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:4px;box-sizing:border-box;">
                      {{ $isPartialInstallment ? 'WA Cicilan' : 'WA Tagihan' }}
                    </a>
                    <button type="button" class="btn-secondary" style="background:{{ $isPartialInstallment ? '#d97706' : '#0a5661' }};color:#ffffff;border:none;padding:8px 6px;font-size:0.76rem;font-weight:800;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;gap:4px;box-sizing:border-box;" onclick="openManualPaymentReceiptModal('{{ $r->id }}', '{{ $r->number }}', '{{ addslashes($t->name) }}', {{ (int)($isPartialInstallment ? $remainingBill : $rRentPrice) }}, '{{ $rLoc }}', {{ (int)$paidThisMonth }}, {{ (int)$remainingBill }}, {{ (int)$rRentPrice }})">
                      <span>{{ $isPartialInstallment ? 'Catat Sisa' : 'Catat Bayar' }}</span>
                    </button>
                  @else
                    <button type="button" class="btn-secondary" style="grid-column: 1 / -1; padding: 8px 10px; font-size: 0.8rem; font-weight: 700; border-radius: 10px;" onclick="document.getElementById('btnAddNewTenant')?.click()">
                      + Tambah Penghuni
                    </button>
                  @endif
                </div>
              </div>
            @empty
              <div style="text-align: center; padding: 24px; color: var(--text-muted);">Tidak ada data unit kamar.</div>
            @endforelse
          </div>
        </div>
      </div>

      @if(Auth::user()->isOwner())
      <!-- =====================================================================
           PANE 4: TAB LAPORAN KEUANGAN & KURVA ARUS KAS (KHUSUS ROLE OWNER)
           ===================================================================== -->
      <div class="tab-pane" id="tab-finances">
        <div class="section-header" style="justify-content: flex-end;">
          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @php
              $currYear = (int)($selectedYear ?? 2026);
              if ($currYear < 2000) $currYear = 2026;
            @endphp
            <input type="hidden" id="selectFinanceYear" value="{{ $currYear }}">

            <!-- Input Ketik Tahun Laporan Keuangan (Sistem Ketik dengan Konfirmasi Simpan Khusus Owner) -->
            <div class="finance-year-type-wrap" id="finYearTypeWrap" title="Ketik tahun laporan keuangan (contoh: {{ $currYear }})">
              <span class="fyp-label">Tahun Laporan:</span>
              <div class="fyp-type-box">
                <input type="number" 
                       id="inputFinanceYear" 
                       class="fyp-number-input" 
                       value="{{ $currYear }}" 
                       min="2000" 
                       max="2099" 
                       placeholder="2026" 
                       onkeydown="if(event.key==='Enter'){ event.preventDefault(); window.confirmSaveFinanceYear(); }"
                       aria-label="Ketik Tahun Laporan Keuangan">
                <button type="button" 
                        class="fyp-btn-save-inline" 
                        id="btnSubmitYearInput" 
                        onclick="window.confirmSaveFinanceYear()" 
                        title="Simpan Tahun Laporan">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                  <span>Simpan</span>
                </button>
              </div>
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
            <div class="finance-actions-row" style="display:flex;gap:10px;flex-wrap:wrap;">
              <button type="button" class="btn-secondary" onclick="openFinancePdfModal()" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#0f2e33 0%,#0a5661 100%);color:#ffffff;border:1px solid #0d9488;font-weight:700;padding:10px 18px;border-radius:12px;box-shadow:0 6px 16px rgba(10,86,97,0.22);cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                <span>File PDF Laporan Keuangan</span>
              </button>
              <button type="button" class="btn-primary-action" id="btnRecordTransaction" onclick="window.openTransactionModal ? window.openTransactionModal() : window.openModal('modalTransaction')" style="cursor:pointer;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Catat Transaksi Baru</span>
              </button>
            </div>
          </div>
        </div>

        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (LAPORAN KEUANGAN) -->
        <div style="margin-bottom: 18px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div class="filter-pills" id="financeLocationFilters">
            <button type="button" class="filter-pill active" data-floc="all" onclick="filterFinanceBranch('all', this)">Gabungan 2 Kos</button>
            <button type="button" class="filter-pill" data-floc="Lokasi 1" onclick="filterFinanceBranch('Lokasi 1', this)">Buku Kas <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></button>
            <button type="button" class="filter-pill" data-floc="Lokasi 2" onclick="filterFinanceBranch('Lokasi 2', this)">Buku Kas <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></button>
          </div>
        </div>

        <!-- 4 KPI Cards Keuangan Kompleks (Dinamis Berdasarkan Filter Tahun & Cabang) -->
        <div class="stats-grid financial-kpi-grid" style="padding: 0; margin-bottom: 24px;">
          <div class="stat-card" onclick="filterFinanceType('income')" style="cursor: pointer;" title="Klik untuk filter transaksi Pemasukan">
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

          <div class="stat-card" onclick="filterFinanceType('expense')" style="cursor: pointer;" title="Klik untuk filter transaksi Biaya Pengeluaran">
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

          <div class="stat-card" onclick="filterFinanceType('all')" style="cursor: pointer;" title="Klik untuk tampilkan Semua Transaksi / Laba Bersih">
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
              <button type="button" class="filter-pill active" data-ftype="all" onclick="filterFinanceType('all', this)">Semua Transaksi</button>
              <button type="button" class="filter-pill" data-ftype="income" onclick="filterFinanceType('income', this)">Pemasukan (Sewa)</button>
              <button type="button" class="filter-pill" data-ftype="expense" onclick="filterFinanceType('expense', this)">Pengeluaran Kos</button>
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
      @endif

      <!-- =====================================================================
           PANE: TAB MANAJEMEN AKUN ADMIN OPERASIONAL (ADMIN & OWNER)
           ===================================================================== -->
      <div class="tab-pane" id="tab-admins">
        <!-- Tabel & Kartu Daftar Seluruh Admin -->
        <div class="content-card modern-table-card" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm);">
          <div class="admins-toolbar-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; background: #fafafa;">
            <div>
              <h3 style="margin: 0 0 2px; font-size: 1.05rem; font-weight: 800; color: #0f172a;">Daftar Akun yang Memiliki Akses Admin</h3>
              <p style="margin: 0; font-size: 0.76rem; color: #64748b;">Hanya pengguna di bawah ini yang dapat masuk ke dashboard operasional kos.</p>
            </div>
            <div class="admins-toolbar-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
              <input type="text" id="searchAdminInput" oninput="filterAdminTable()" placeholder="Cari nama atau email admin..." style="padding: 8px 14px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 0.8rem; width: 220px;">
              <button type="button" class="btn-primary" onclick="openAddAdminModal()" style="background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; border: none; padding: 8px 16px; border-radius: 10px; font-weight: 800; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(10, 86, 97, 0.25); cursor: pointer; transition: all 0.2s;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Tambah Admin Baru</span>
              </button>
            </div>
          </div>

          <!-- TAMPILAN DESKTOP: TABEL DATA ADMIN -->
          <div class="table-responsive">
            <table class="data-table" id="tableAdminsList" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%); border-bottom: 2px solid #0f766e;">
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em;">Staf Admin</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em;">Email Login</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em;">Kontak WhatsApp</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em;">Penugasan Cabang Kos</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em;">Terdaftar</th>
                  <th style="padding: 14px 20px; font-size: 0.74rem; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 0.06em; text-align: right;">Tindakan</th>
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
                  $curBranch = $admin->branch_assignment ?? 'all';
                @endphp
                <tr class="admin-data-row" id="adminRow_{{ $admin->id }}" data-id="{{ $admin->id }}" data-name="{{ strtolower($admin->name) }}" data-email="{{ strtolower($admin->email) }}" data-phone="{{ $admin->phone }}" style="border-bottom: 1px solid #f1f5f9;">
                  <!-- Kolom Staf Admin (Sesuai nama akun yang dimiliki oleh si admin) -->
                  <td style="padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(10, 86, 97, 0.2);">
                        {{ $admInitials }}
                      </div>
                      <div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.94rem;" class="adm-name-text">{{ $admin->name }}</div>
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
                  <!-- Kolom Penugasan Cabang Kos -->
                  <td style="padding: 14px 20px;">
                    <div style="min-width: 200px;">
                      <select class="admin-branch-select" onchange="changeAdminBranch({{ $admin->id }}, this.value)" style="width: 100%; padding: 8px 12px; font-size: 0.82rem; font-weight: 800; border-radius: 10px; border: 1.5px solid #0d9488; background: #f0fdfa; color: #0a5661; cursor: pointer; outline: none; transition: all 0.2s;">
                        <option value="all" {{ $curBranch === 'all' ? 'selected' : '' }}>Kedua Cabang (Kos 1 &amp; Kos 2)</option>
                        <option value="Lokasi 1" {{ $curBranch === 'Lokasi 1' ? 'selected' : '' }}>Admin Kos 1 ({{ $branch1Name }})</option>
                        <option value="Lokasi 2" {{ $curBranch === 'Lokasi 2' ? 'selected' : '' }}>Admin Kos 2 ({{ $branch2Name }})</option>
                      </select>
                    </div>
                  </td>
                  <td style="padding: 14px 20px;">
                    <span style="font-size: 0.8rem; color: #64748b;">{{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}</span>
                  </td>
                  <td style="padding: 14px 20px; text-align: right;">
                    <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: flex-end;">
                      <button type="button" class="btn-secondary" onclick="openEditAdminModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ addslashes($admin->email) }}', '{{ addslashes($admin->phone ?? '') }}', '{{ $admin->branch_assignment ?? 'all' }}')" style="font-size: 0.76rem; padding: 6px 12px; border-radius: 8px; font-weight: 700; cursor: pointer;" title="Edit Data & Ganti Password">
                        Edit
                      </button>
                      <button type="button" class="btn-secondary" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')" style="font-size: 0.76rem; padding: 6px 10px; border-radius: 8px; font-weight: 700; color: #ef4444; border-color: #fecaca; cursor: pointer;" title="Hapus Akses Admin">
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

          <!-- TAMPILAN MOBILE: KARTU DATA STAF ADMIN OPERASIONAL -->
          <div class="mobile-card-list" id="adminsMobileCardList" style="padding: 14px; gap: 12px;">
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
              $curBranch = $admin->branch_assignment ?? 'all';
            @endphp
            <div class="mobile-data-card admin-mobile-card" id="adminMobileCard_{{ $admin->id }}" data-id="{{ $admin->id }}" data-name="{{ strtolower($admin->name) }}" data-email="{{ strtolower($admin->email) }}" data-phone="{{ $admin->phone }}" style="background: #ffffff; border: 1.5px solid rgba(226, 232, 240, 0.95); border-radius: 20px; padding: 16px; box-shadow: 0 8px 24px -4px rgba(10, 86, 97, 0.06); display: flex; flex-direction: column; gap: 10px;">
              <!-- Header Card: Avatar + Nama + Status Badge -->
              <div class="mobile-data-header" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 10px; border-bottom: 1.5px solid #f1f5f9; margin-bottom: 2px;">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1;">
                  <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(10, 86, 97, 0.2);">
                    {{ $admInitials }}
                  </div>
                  <div style="min-width: 0; flex: 1;">
                    <div class="mobile-data-title" style="font-size: 0.98rem; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;">{{ $admin->name }}</div>
                    <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.68rem; font-weight: 800; color: #0f766e; background: #f0fdfa; border: 1px solid rgba(15, 118, 110, 0.25); border-radius: 999px; padding: 2px 8px;">
                      <span style="width: 5px; height: 5px; border-radius: 50%; background: #0d9488;"></span>
                      Staf Admin
                    </span>
                  </div>
                </div>
              </div>

              <!-- Baris Detail: Email Login -->
              <div class="mobile-data-row" style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f8fafc; font-size: 0.82rem;">
                <span style="color: #64748b; font-weight: 600;">Email Login:</span>
                <span style="display: inline-flex; align-items: center; gap: 6px; max-width: 65%;">
                  <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.82rem; font-weight: 700; color: #0f172a;">{{ $admin->email }}</span>
                  <button type="button" onclick="navigator.clipboard.writeText('{{ $admin->email }}'); window.showNotification('success', 'Email tersalin!');" title="Salin Email" style="border: none; background: #f1f5f9; border-radius: 6px; cursor: pointer; color: #475569; padding: 4px 6px; display: inline-flex; align-items: center; flex-shrink: 0;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                  </button>
                </span>
              </div>

              <!-- Baris Detail: Kontak WhatsApp -->
              <div class="mobile-data-row" style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f8fafc; font-size: 0.82rem;">
                <span style="color: #64748b; font-weight: 600;">WhatsApp:</span>
                <span>
                  @if($admin->phone)
                    <a href="https://wa.me/{{ $adminPhoneClean }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; font-weight: 700; color: #0a5661; text-decoration: none; background: #f0fdf4; border: 1px solid rgba(22, 163, 74, 0.25); border-radius: 8px; padding: 3px 8px; font-size: 0.78rem;">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="#16a34a"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
                      <span>{{ $admin->phone }}</span>
                    </a>
                  @else
                    <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">Belum diisi</span>
                  @endif
                </span>
              </div>

              <!-- Baris Detail: Penugasan Cabang Kos -->
              <div style="padding: 6px 0; border-bottom: 1px solid #f8fafc;">
                <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Penugasan Cabang Kos:</div>
                <select class="admin-branch-select" onchange="changeAdminBranch({{ $admin->id }}, this.value)" style="width: 100%; padding: 8px 12px; font-size: 0.82rem; font-weight: 800; border-radius: 10px; border: 1.5px solid #0d9488; background: #f0fdfa; color: #0a5661; cursor: pointer; outline: none; transition: all 0.2s;">
                  <option value="all" {{ $curBranch === 'all' ? 'selected' : '' }}>Kedua Cabang (Kos 1 &amp; Kos 2)</option>
                  <option value="Lokasi 1" {{ $curBranch === 'Lokasi 1' ? 'selected' : '' }}>Admin Kos 1 ({{ $branch1Name }})</option>
                  <option value="Lokasi 2" {{ $curBranch === 'Lokasi 2' ? 'selected' : '' }}>Admin Kos 2 ({{ $branch2Name }})</option>
                </select>
              </div>

              <!-- Baris Detail: Terdaftar -->
              <div class="mobile-data-row" style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: none; font-size: 0.76rem;">
                <span style="color: #64748b; font-weight: 500;">Terdaftar:</span>
                <span style="font-weight: 600; color: #64748b;">{{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}</span>
              </div>

              <!-- Tombol Aksi: Edit & Hapus -->
              <div style="display: flex; gap: 8px; margin-top: 4px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn-secondary" onclick="openEditAdminModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ addslashes($admin->email) }}', '{{ addslashes($admin->phone ?? '') }}', '{{ $admin->branch_assignment ?? 'all' }}')" style="flex: 1; font-size: 0.8rem; padding: 9px; border-radius: 10px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  <span>Edit Data</span>
                </button>
                <button type="button" class="btn-secondary" onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')" style="font-size: 0.8rem; padding: 9px 14px; border-radius: 10px; font-weight: 700; color: #ef4444; border-color: #fecaca; background: #fff5f5; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  <span>Hapus</span>
                </button>
              </div>
            </div>
            @empty
            <div id="emptyAdminMobileCard" style="text-align: center; padding: 30px 16px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px; color: #64748b;">
              <div style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Belum Ada Staf Admin Tambahan</div>
              <div style="font-size: 0.8rem; color: #64748b;">Klik tombol 'Tambah Admin Baru' di atas untuk memberikan akses kepada staf operasional kos.</div>
            </div>
            @endforelse
          </div>
        </div>
      </div>

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
            <button type="button" class="filter-pill active" data-mloc="all" onclick="filterMaintenanceLocation('all', this)">Semua Cabang</button>
            <button type="button" class="filter-pill" data-mloc="Lokasi 1" onclick="filterMaintenanceLocation('Lokasi 1', this)">Servis <span data-branch-name="lokasi_1">{{ $branch1Name }}</span></button>
            <button type="button" class="filter-pill" data-mloc="Lokasi 2" onclick="filterMaintenanceLocation('Lokasi 2', this)">Servis <span data-branch-name="lokasi_2">{{ $branch2Name }}</span></button>
          </div>
        </div>

        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="maintenanceSearchInput" placeholder="Cari tiket, keluhan, cabang, atau nomor kamar..." oninput="filterMaintenanceTable()">
            </div>
            <div class="filter-pills" id="repairStatusFilter">
              <button type="button" class="filter-pill active" data-rstatus="all" onclick="filterMaintenanceStatus('all', this)">Semua</button>
              <button type="button" class="filter-pill" data-rstatus="pending" onclick="filterMaintenanceStatus('pending', this)">Menunggu</button>
              <button type="button" class="filter-pill" data-rstatus="progress" onclick="filterMaintenanceStatus('progress', this)">Dalam Proses</button>
              <button type="button" class="filter-pill" data-rstatus="done" onclick="filterMaintenanceStatus('done', this)">Selesai</button>
            </div>
          </div>

          <div class="table-responsive">
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
                @forelse($allMaintenance ?? [] as $mItem)
                  @php
                    $mLoc = $mItem->room ? ($mItem->room->location ?? 'Lokasi 1') : 'Lokasi 1';
                    $isLoc2 = $mLoc === 'Lokasi 2';
                    $rNum = $mItem->room ? $mItem->room->number : '-';
                    $tRep = $mItem->reporter_name ?: 'Penghuni';
                    $tDesc = $mItem->issue_description ?: '-';
                    $tDate = $mItem->report_date ? \Carbon\Carbon::parse($mItem->report_date)->format('d/m/Y') : '-';
                  @endphp
                  <tr class="maintenance-row" data-status="{{ $mItem->status }}" data-location="{{ $mLoc }}" data-search="{{ strtolower(($mItem->ticket_code ?? '') . ' ' . $rNum . ' ' . $tRep . ' ' . $tDesc . ' ' . $mLoc) }}">
                    <td style="font-family: monospace; font-size: 0.85rem; font-weight: 700;">#{{ $mItem->ticket_code ?? ('T-' . $mItem->id) }}</td>
                    <td>
                      <strong>Kamar #{{ $rNum }}</strong>
                      <div style="font-size: 0.75rem; color: var(--text-secondary);">Pelapor: {{ $tRep }}</div>
                    </td>
                    <td>
                      @if($isLoc2)
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">
                          <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                        </span>
                        <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">PIC: Mas Dani (Smart System)</div>
                      @else
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">
                          <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                        </span>
                        <div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">PIC: Pak Budi (Utilitas)</div>
                      @endif
                    </td>
                    <td style="max-width: 300px; line-height: 1.45;">{{ $tDesc }}</td>
                    <td>{{ $tDate }}</td>
                    <td>
                      @if(in_array(strtolower($mItem->priority ?? ''), ['urgent', 'high', 'tinggi', 'mendesak']))
                        <span class="feature-pill" style="color: #ef4444; font-weight: 700; background: #fee2e2;">{{ $mItem->priority }}</span>
                      @else
                        <span class="feature-pill" style="font-weight: 700;">{{ $mItem->priority ?: 'Normal' }}</span>
                      @endif
                    </td>
                    <td class="maint-status-col" id="maintStatusCell_{{ $mItem->id }}">
                      @if($mItem->status === 'done')
                        <span class="status-badge occupied">Selesai</span>
                      @elseif($mItem->status === 'progress')
                        <span class="status-badge due">Dalam Proses</span>
                      @else
                        <span class="status-badge repair">Menunggu</span>
                      @endif
                    </td>
                    <td>
                      <select class="form-control" style="padding: 4px 8px; font-size: 0.78rem;" onchange="window.handleMaintenanceStatusChange ? window.handleMaintenanceStatusChange(this, '{{ $mItem->id }}') : (window.kosApp ? window.kosApp.updateRepairStatus('{{ $mItem->id }}', this.value) : null)">
                        <option value="done" {{ $mItem->status === 'done' ? 'selected' : '' }}>Selesai</option>
                        <option value="progress" {{ $mItem->status === 'progress' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="pending" {{ $mItem->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                      </select>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" style="text-align: center; padding: 24px; color: var(--text-muted);">Tidak ada tiket keluhan saat ini</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mobile-card-list" id="maintenanceMobileCardList">
            @foreach($allMaintenance ?? [] as $mItem)
              @php
                $mLoc = $mItem->room ? ($mItem->room->location ?? 'Lokasi 1') : 'Lokasi 1';
                $isLoc2 = $mLoc === 'Lokasi 2';
                $rNum = $mItem->room ? $mItem->room->number : '-';
                $tRep = $mItem->reporter_name ?: 'Penghuni';
                $tDesc = $mItem->issue_description ?: '-';
              @endphp
              <div class="mobile-data-card" id="maintMobileCard_{{ $mItem->id }}" data-status="{{ $mItem->status }}" data-location="{{ $mLoc }}" data-search="{{ strtolower(($mItem->ticket_code ?? '') . ' ' . $rNum . ' ' . $tRep . ' ' . $tDesc . ' ' . $mLoc) }}">
                <div class="mobile-data-header">
                  <span class="mobile-data-title">Kamar #{{ $rNum }} ({{ $isLoc2 ? $branch2Name : $branch1Name }})</span>
                  <span class="maint-card-badge" id="maintMobileBadge_{{ $mItem->id }}">
                  @if($mItem->status === 'done')
                    <span class="status-badge occupied">Selesai</span>
                  @elseif($mItem->status === 'progress')
                    <span class="status-badge due">Dalam Proses</span>
                  @else
                    <span class="status-badge repair">Menunggu</span>
                  @endif
                  </span>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.45;">{{ $tDesc }}</div>
                <div class="mobile-data-row" style="margin-top: 6px;">
                  <span>Pelapor:</span>
                  <span>{{ $tRep }}</span>
                </div>
                <div class="mobile-data-row">
                  <span>Cabang &amp; Teknisi:</span>
                  <span>{{ $isLoc2 ? ($branch2Name . ' • Mas Dani') : ($branch1Name . ' • Pak Budi') }}</span>
                </div>
                <div style="margin-top: 8px;">
                  <select class="form-control" style="padding: 6px; font-size: 0.82rem;" onchange="window.handleMaintenanceStatusChange ? window.handleMaintenanceStatusChange(this, '{{ $mItem->id }}') : (window.kosApp ? window.kosApp.updateRepairStatus('{{ $mItem->id }}', this.value) : null)">
                    <option value="done" {{ $mItem->status === 'done' ? 'selected' : '' }}>Status: Selesai</option>
                    <option value="progress" {{ $mItem->status === 'progress' ? 'selected' : '' }}>Status: Dalam Proses</option>
                    <option value="pending" {{ $mItem->status === 'pending' ? 'selected' : '' }}>Status: Menunggu</option>
                  </select>
                </div>
              </div>
            @endforeach
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
                  $trTenantName = $tr->user->name ?? $tr->tenant->name ?? 'Anak Kos';
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
                        <button type="button" class="btn-sm btn-transfer-action btn-transfer-approve" data-id="{{ $tr->id }}" data-status="approved" data-tenant="{{ htmlspecialchars($trTenantName, ENT_QUOTES) }}" data-from="{{ $fromNum }}" data-to="{{ $toNum }}" style="background: #10b981; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem;" onclick="window.openConfirmRoomTransferModal(this)">
                          Izinkan
                        </button>
                        <button type="button" class="btn-sm btn-transfer-action btn-transfer-reject" data-id="{{ $tr->id }}" data-status="rejected" data-tenant="{{ htmlspecialchars($trTenantName, ENT_QUOTES) }}" data-from="{{ $fromNum }}" data-to="{{ $toNum }}" style="background: #ef4444; color: #fff; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem;" onclick="window.openConfirmRoomTransferModal(this)">
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

          <!-- Mobile Card List Pindah Kamar -->
          <div class="mobile-card-list" id="transfersMobileCardList">
            @forelse($roomTransfers ?? [] as $tr)
              @php
                $trTenantName = $tr->user->name ?? $tr->tenant->name ?? 'Anak Kos';
                $fromNum = $tr->currentRoom->number ?? '101';
                $toNum = $tr->targetRoom->number ?? '102';
                $fromLoc = $tr->currentRoom->location ?? (intval($fromNum) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                $toLoc = $tr->targetRoom->location ?? (intval($toNum) >= 200 ? 'Lokasi 2' : 'Lokasi 1');
                $isCrossBranch = $fromLoc !== $toLoc;
                $transferLocCategory = $isCrossBranch ? 'Lintas Cabang' : $fromLoc;
                $priceDiff = (int)($tr->targetRoom->price ?? 0) - (int)($tr->currentRoom->price ?? 0);
              @endphp
              <div class="mobile-data-card" data-status="{{ $tr->status }}" data-location="{{ $transferLocCategory }}" data-from-loc="{{ $fromLoc }}" data-to-loc="{{ $toLoc }}" data-search="{{ strtolower(($tr->user->name ?? $tr->tenant->name ?? '') . ' ' . $fromNum . ' ' . $toNum . ' ' . $fromLoc . ' ' . $toLoc . ' ' . $transferLocCategory . ' ' . $tr->reason) }}">
                <div class="mobile-data-header">
                  <div>
                    <span class="mobile-data-title">{{ $trTenantName }}</span>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                      Diajukan: {{ $tr->created_at->format('d/m/Y H:i') }} WIB
                    </div>
                  </div>
                  <div>
                    @if($tr->status === 'pending')
                      <span class="status-pill status-due">Menunggu</span>
                    @elseif($tr->status === 'approved')
                      <span class="status-pill status-occupied">Disetujui</span>
                    @else
                      <span class="status-pill status-repair">Ditolak</span>
                    @endif
                  </div>
                </div>

                <div class="mobile-data-row">
                  <span>Perpindahan:</span>
                  <span style="display: flex; align-items: center; gap: 6px;">
                    <span class="badge" style="background: #f1f5f9; color: #0f172a; font-weight: 700; padding: 2px 7px; border-radius: 6px;">#{{ $fromNum }}</span>
                    <span style="color: #0a5661; font-weight: 800;">&rarr;</span>
                    <span class="badge" style="background: #f0fdfa; color: #0a5661; font-weight: 800; padding: 2px 7px; border-radius: 6px; border: 1px solid #99f6e4;">#{{ $toNum }}</span>
                  </span>
                </div>

                <div class="mobile-data-row">
                  <span>Tipe Mutasi:</span>
                  <span>
                    @if($isCrossBranch)
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd;">
                        Lintas Cabang
                      </span>
                    @elseif($fromLoc === 'Lokasi 2')
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;">
                        Internal <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                      </span>
                    @else
                      <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;">
                        Internal <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                      </span>
                    @endif
                  </span>
                </div>

                <div class="mobile-data-row">
                  <span>Penyesuaian Tarif:</span>
                  <span>
                    @if($priceDiff > 0)
                      <span style="color: #059669; font-weight: 700;">+Rp {{ number_format($priceDiff, 0, ',', '.') }}/bln</span>
                    @elseif($priceDiff < 0)
                      <span style="color: #d97706; font-weight: 700;">-Rp {{ number_format(abs($priceDiff), 0, ',', '.') }}/bln</span>
                    @else
                      <span style="color: #64748b; font-weight: 600;">Tarif Setara</span>
                    @endif
                  </span>
                </div>

                @if($tr->reason)
                  <div style="background: #f8fafc; border-radius: 10px; padding: 8px 10px; margin: 8px 0; font-size: 0.8rem; color: #334155; line-height: 1.4;">
                    <strong>Alasan:</strong> {{ $tr->reason }}
                    @if($tr->preferred_date)
                      <div style="font-size: 0.72rem; color: #0a5661; margin-top: 4px;">Rencana: {{ \Carbon\Carbon::parse($tr->preferred_date)->format('d M Y') }}</div>
                    @endif
                  </div>
                @endif

                @if($tr->status === 'pending')
                  <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 10px;">
                    <button type="button" class="btn-sm btn-transfer-action btn-transfer-approve" data-id="{{ $tr->id }}" data-status="approved" data-tenant="{{ htmlspecialchars($trTenantName, ENT_QUOTES) }}" data-from="{{ $fromNum }}" data-to="{{ $toNum }}" style="background: #10b981; color: #fff; border: none; padding: 8px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.78rem; text-align: center;" onclick="window.openConfirmRoomTransferModal(this)">
                      Izinkan Pindah
                    </button>
                    <button type="button" class="btn-sm btn-transfer-action btn-transfer-reject" data-id="{{ $tr->id }}" data-status="rejected" data-tenant="{{ htmlspecialchars($trTenantName, ENT_QUOTES) }}" data-from="{{ $fromNum }}" data-to="{{ $toNum }}" style="background: #ef4444; color: #fff; border: none; padding: 8px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 0.78rem; text-align: center;" onclick="window.openConfirmRoomTransferModal(this)">
                      Tolak
                    </button>
                  </div>
                @endif
              </div>
            @empty
              <div style="text-align: center; padding: 24px; color: var(--text-muted);">
                Belum ada pengajuan pindah kamar dari penghuni kos.
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- =========================================================================
           TAB: SURVEI LOKASI (MANAJEMEN PERMOHONAN SURVEI DARI CALON ANAK KOS)
           ========================================================================= -->
      <div class="tab-pane" id="tab-surveys">
        <!-- SWITCHER FILTER 2 TITIK LOKASI KOS (SURVEI LOKASI) -->
        <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
          <div class="filter-pills" id="surveyLocationFilters">
            <button type="button" class="filter-pill active" onclick="window.filterSurveyBranch('all', this)">Semua Lokasi</button>
            <button type="button" class="filter-pill" onclick="window.filterSurveyBranch('Lokasi 1', this)">Cabang 1 (<span data-branch-name="lokasi_1">{{ $branch1Name }}</span>)</button>
            <button type="button" class="filter-pill" onclick="window.filterSurveyBranch('Lokasi 2', this)">Cabang 2 (<span data-branch-name="lokasi_2">{{ $branch2Name }}</span>)</button>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" class="btn-secondary" onclick="window.switchTab('tab-settings'); if (typeof window.switchSettingsSubtab === 'function') window.switchSettingsSubtab('subtab-survey');" style="font-size: 0.78rem; padding: 7px 14px; display: inline-flex; align-items: center; gap: 6px; border-radius: 10px;" title="Atur Template Formulir Pesan Survei">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              <span>Format Template WA</span>
            </button>
          </div>
        </div>

        @php
          $surveysCollection = $allSurveys ?? collect();
          $totalSurveysCount = $surveysCollection->count();
          $pendingSurveysCount = $surveysCollection->where('status', 'pending')->count();
          $confirmedSurveysCount = $surveysCollection->where('status', 'confirmed')->count();
          $completedSurveysCount = $surveysCollection->where('status', 'completed')->count();
        @endphp

        <!-- STATS KPI CARDS -->
        <section class="stats-grid" style="padding: 0; margin-bottom: 24px;">
          <div class="stat-card" style="cursor: pointer;" onclick="window.filterSurveyStatus('all', document.querySelector('#surveyStatusFilter [data-status-filter=all]'))" title="Tampilkan Semua Permohonan Survei">
            <div class="stat-card-header">
              <span class="stat-card-title">Total Permohonan</span>
              <div class="stat-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="kpiSurveyTotal">{{ $totalSurveysCount }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">Semua Jadwal</span>
              <span>Calon Anak Kos</span>
            </div>
          </div>

          <div class="stat-card" style="cursor: pointer;" onclick="window.filterSurveyStatus('pending', document.querySelector('#surveyStatusFilter [data-status-filter=pending]'))" title="Tampilkan Permohonan Menunggu">
            <div class="stat-card-header">
              <span class="stat-card-title">Menunggu Konfirmasi</span>
              <div class="stat-icon amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="kpiSurveyPending" style="color: #f59e0b;">{{ $pendingSurveysCount }}</div>
            <div class="stat-card-meta">
              <span class="stat-badge-info" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">Perlu Respons</span>
              <span>Konfirmasi via WA</span>
            </div>
          </div>

          <div class="stat-card" style="cursor: pointer;" onclick="window.filterSurveyStatus('confirmed', document.querySelector('#surveyStatusFilter [data-status-filter=confirmed]'))" title="Tampilkan Jadwal Dikonfirmasi">
            <div class="stat-card-header">
              <span class="stat-card-title">Dikonfirmasi</span>
              <div class="stat-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="kpiSurveyConfirmed" style="color: #2563eb;">{{ $confirmedSurveysCount }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">Jadwal Disetujui</span>
              <span>Siap Menyambut</span>
            </div>
          </div>

          <div class="stat-card" style="cursor: pointer;" onclick="window.filterSurveyStatus('completed', document.querySelector('#surveyStatusFilter [data-status-filter=completed]'))" title="Tampilkan Kunjungan Selesai">
            <div class="stat-card-header">
              <span class="stat-card-title">Kunjungan Selesai</span>
              <div class="stat-icon emerald">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
            </div>
            <div class="stat-card-value" id="kpiSurveyCompleted" style="color: #10b981;">{{ $completedSurveysCount }}</div>
            <div class="stat-card-meta">
              <span class="stat-trend-up">Survei Terlaksana</span>
              <span>Potensi Penghuni</span>
            </div>
          </div>
        </section>

        <!-- TABLE CARD CONTAINER -->
        <div class="table-card-container">
          <div class="table-toolbar">
            <div class="table-search">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="surveySearchInput" placeholder="Cari nama calon penghuni, WhatsApp, kamar, atau tanggal..." onkeyup="window.filterSurveyTable()">
            </div>
            <div class="filter-pills" id="surveyStatusFilter">
              <button type="button" class="filter-pill active" data-status-filter="all" onclick="window.filterSurveyStatus('all', this)">Semua (<span id="surveyPillAllCount">{{ $totalSurveysCount }}</span>)</button>
              <button type="button" class="filter-pill" data-status-filter="pending" onclick="window.filterSurveyStatus('pending', this)">Menunggu (<span id="surveyPillPendingCount">{{ $pendingSurveysCount }}</span>)</button>
              <button type="button" class="filter-pill" data-status-filter="confirmed" onclick="window.filterSurveyStatus('confirmed', this)">Dikonfirmasi</button>
              <button type="button" class="filter-pill" data-status-filter="completed" onclick="window.filterSurveyStatus('completed', this)">Selesai</button>
              <button type="button" class="filter-pill" data-status-filter="cancelled" onclick="window.filterSurveyStatus('cancelled', this)">Dibatalkan</button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="custom-table" id="surveysTable">
              <thead>
                <tr>
                  <th>Tgl Pengajuan</th>
                  <th>Calon Anak Kos</th>
                  <th>Cabang & Unit Kamar</th>
                  <th>Jadwal Kunjungan</th>
                  <th>Catatan / Pesan</th>
                  <th>Status Survei</th>
                  <th>Aksi Pengelola</th>
                </tr>
              </thead>
              <tbody id="surveysTableBody">
                @forelse($surveysCollection as $sv)
                  @php
                    $rawBranch = strtolower($sv->branch ?? '');
                    $isBranch2 = str_contains($rawBranch, '2') || str_contains($rawBranch, 'ringroad') || ($branch2Name && str_contains(strtolower($branch2Name), $rawBranch));
                    $svBranch = $isBranch2 ? 'Lokasi 2' : 'Lokasi 1';
                    $svPhone = $sv->phone ?? '-';
                    $svCleanPhone = preg_replace('/[^0-9]/', '', $svPhone);
                    if (\Illuminate\Support\Str::startsWith($svCleanPhone, '0')) {
                      $svCleanPhone = '62' . substr($svCleanPhone, 1);
                    }
                    $svDateFormatted = $sv->survey_date ? \Carbon\Carbon::parse($sv->survey_date)->format('d/m/Y') : 'Segera';
                    $svTimeFormatted = $sv->survey_time ?? '14:00 WIB';
                    $searchBlob = strtolower(($sv->name ?? '') . ' ' . ($sv->phone ?? '') . ' ' . ($sv->job ?? '') . ' ' . ($sv->branch ?? '') . ' ' . ($sv->room ?? '') . ' ' . ($sv->survey_method ?? '') . ' ' . ($sv->message ?? '') . ' ' . ($sv->status ?? '') . ' ' . $svDateFormatted . ' ' . $svBranch);
                  @endphp
                  <tr id="surveyRow_{{ $sv->id }}" data-id="{{ $sv->id }}" data-status="{{ $sv->status }}" data-branch="{{ $svBranch }}" data-search="{{ $searchBlob }}">
                    <td>
                      <div style="font-weight: 700; color: #0f172a;">{{ $sv->created_at ? $sv->created_at->format('d/m/Y') : '-' }}</div>
                      <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $sv->created_at ? $sv->created_at->format('H:i') . ' WIB' : '' }} ({{ $sv->created_at ? $sv->created_at->diffForHumans(null, true) : 'Baru' }})</div>
                    </td>
                    <td>
                      <div style="font-weight: 800; color: var(--brand-primary); font-size: 0.9rem;">{{ $sv->name }}</div>
                      <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                        <span style="font-size: 0.72rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 7px; border-radius: 6px;">{{ $sv->job ?? 'Calon Penghuni' }}</span>
                      </div>
                      @if($svCleanPhone)
                        <div style="margin-top: 4px;">
                          <a href="https://wa.me/{{ $svCleanPhone }}" target="_blank" style="font-size: 0.76rem; color: #0284c7; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
                            <span>{{ $svPhone }}</span>
                          </a>
                        </div>
                      @endif
                    </td>
                    <td>
                      <div>
                        @if($svBranch === 'Lokasi 2')
                          <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 999px; background: #ccfbf1; color: #0a5661; border: 1px solid #5eead4;">
                            <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                          </span>
                        @else
                          <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 999px; background: #dcfce7; color: #059669; border: 1px solid #86efac;">
                            <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                          </span>
                        @endif
                      </div>
                      <div style="font-weight: 700; color: #0f172a; font-size: 0.82rem; margin-top: 4px;">
                        {{ $sv->room ?? 'Semua Tipe Kamar' }}
                      </div>
                    </td>
                    <td>
                      <div style="font-weight: 800; color: #059669; font-size: 0.84rem;">{{ $svDateFormatted }}</div>
                      <div style="font-size: 0.74rem; color: #475569; font-weight: 600;">Pukul {{ $svTimeFormatted }}</div>
                      <div style="margin-top: 4px;">
                        @if(strtolower($sv->survey_method ?? '') === 'video call')
                          <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #f3e8ff; color: #7e22ce;">
                            Video Call
                          </span>
                        @else
                          <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #e0f2fe; color: #0369a1;">
                            Datang Langsung
                          </span>
                        @endif
                      </div>
                    </td>
                    <td style="max-width: 220px;">
                      <div style="font-size: 0.8rem; line-height: 1.4; color: #334155; max-height: 60px; overflow-y: auto;">
                        {{ $sv->message ?: 'Tidak ada pesan khusus.' }}
                      </div>
                    </td>
                    <td>
                      <div style="margin-bottom: 6px;">
                        @if($sv->status === 'pending')
                          <span class="status-pill survey-status-badge status-due">Menunggu</span>
                        @elseif($sv->status === 'confirmed')
                          <span class="status-pill survey-status-badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Dikonfirmasi</span>
                        @elseif($sv->status === 'completed')
                          <span class="status-pill survey-status-badge status-occupied">Selesai</span>
                        @else
                          <span class="status-pill survey-status-badge status-repair">Dibatalkan</span>
                        @endif
                      </div>
                      <div>
                        <select class="custom-select" style="font-size: 0.74rem; padding: 3px 8px; border-radius: 6px; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff; cursor: pointer;" onchange="window.updateSurveyStatusAjax({{ $sv->id }}, this.value)">
                          <option value="pending" {{ $sv->status === 'pending' ? 'selected' : '' }}>Ubah: Menunggu</option>
                          <option value="confirmed" {{ $sv->status === 'confirmed' ? 'selected' : '' }}>Ubah: Dikonfirmasi</option>
                          <option value="completed" {{ $sv->status === 'completed' ? 'selected' : '' }}>Ubah: Selesai</option>
                          <option value="cancelled" {{ $sv->status === 'cancelled' ? 'selected' : '' }}>Ubah: Dibatalkan</option>
                        </select>
                      </div>
                    </td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <button type="button" class="btn-sm" style="background: #059669; color: #ffffff; border: none; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px;" onclick="window.openSurveyWhatsAppChat('{{ $svPhone }}', '{{ htmlspecialchars($sv->name, ENT_QUOTES) }}', '{{ htmlspecialchars($svBranch, ENT_QUOTES) }}', '{{ htmlspecialchars($sv->room ?? '', ENT_QUOTES) }}', '{{ $svDateFormatted }}', '{{ $svTimeFormatted }}')" title="Chat Konfirmasi WhatsApp">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
                          <span>WA</span>
                        </button>
                        <button type="button" class="btn-sm btn-survey-calendar" style="background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;" onclick="window.addToCalendar({{ $sv->id }})" title="Tambahkan Jadwal ke Kalender">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M12 14v4"/><path d="M10 16h4"/></svg>
                          <span>Kalender</span>
                        </button>
                        <button type="button" class="btn-sm" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px;" onclick="window.openSurveyDetailModal({{ $sv->id }})" title="Lihat Detail Pengajuan">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                          <span>Detail</span>
                        </button>
                        <button type="button" class="btn-sm" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 6px 9px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.74rem;" onclick="window.deleteSurveyAjax({{ $sv->id }}, '{{ htmlspecialchars($sv->name, ENT_QUOTES) }}')" title="Hapus Permohonan Survei">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                @endforelse
                <tr id="surveyEmptyStateRow" style="{{ $totalSurveysCount === 0 ? '' : 'display:none;' }}">
                  <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: #f0fdfa; color: #0a5661; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; border: 1px solid #ccfbf1;">
                      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a; margin-bottom: 4px;">Tidak Ada Data Permohonan Survei</div>
                    <div style="font-size: 0.8rem; color: #64748b; max-width: 440px; margin: 0 auto;">
                      Permohonan jadwal survei kamar yang diajukan calon anak kos melalui formulir survei di website akan langsung tercatat dan tersimpan otomatis di sini secara real-time.
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Card List Survei Lokasi -->
          <div class="mobile-card-list" id="surveysMobileCardList">
            @forelse($surveysCollection as $sv)
              @php
                $rawBranch = strtolower($sv->branch ?? '');
                $isBranch2 = str_contains($rawBranch, '2') || str_contains($rawBranch, 'ringroad') || ($branch2Name && str_contains(strtolower($branch2Name), $rawBranch));
                $svBranch = $isBranch2 ? 'Lokasi 2' : 'Lokasi 1';
                $svPhone = $sv->phone ?? '-';
                $svCleanPhone = preg_replace('/[^0-9]/', '', $svPhone);
                if (\Illuminate\Support\Str::startsWith($svCleanPhone, '0')) {
                  $svCleanPhone = '62' . substr($svCleanPhone, 1);
                }
                $svDateFormatted = $sv->survey_date ? \Carbon\Carbon::parse($sv->survey_date)->format('d/m/Y') : 'Segera';
                $svTimeFormatted = $sv->survey_time ?? '14:00 WIB';
                $searchBlob = strtolower(($sv->name ?? '') . ' ' . ($sv->phone ?? '') . ' ' . ($sv->job ?? '') . ' ' . ($sv->branch ?? '') . ' ' . ($sv->room ?? '') . ' ' . ($sv->survey_method ?? '') . ' ' . ($sv->message ?? '') . ' ' . ($sv->status ?? '') . ' ' . $svDateFormatted . ' ' . $svBranch);
              @endphp
              <div class="mobile-data-card" id="surveyMobileCard_{{ $sv->id }}" data-id="{{ $sv->id }}" data-status="{{ $sv->status }}" data-branch="{{ $svBranch }}" data-search="{{ $searchBlob }}">
                <div class="mobile-data-header">
                  <div>
                    <span class="mobile-data-title">{{ $sv->name }}</span>
                    <div style="font-size: 0.72rem; color: #475569; margin-top: 2px;">
                      {{ $sv->job ?? 'Calon Penghuni' }} &bull; {{ $sv->created_at ? $sv->created_at->format('d/m/Y') : '-' }}
                    </div>
                  </div>
                  <div>
                    @if($sv->status === 'pending')
                      <span class="status-pill status-due">Menunggu</span>
                    @elseif($sv->status === 'confirmed')
                      <span class="status-pill" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Dikonfirmasi</span>
                    @elseif($sv->status === 'completed')
                      <span class="status-pill status-occupied">Selesai</span>
                    @else
                      <span class="status-pill status-repair">Dibatalkan</span>
                    @endif
                  </div>
                </div>

                <div class="mobile-data-row">
                  <span>Cabang & Unit:</span>
                  <span style="font-weight: 700;">
                    {{ $svBranch === 'Lokasi 2' ? $branch2Name : $branch1Name }} &bull; {{ $sv->room ?? 'Semua Tipe' }}
                  </span>
                </div>

                <div class="mobile-data-row">
                  <span>Jadwal Survei:</span>
                  <span style="font-weight: 800; color: #059669;">{{ $svDateFormatted }}, {{ $svTimeFormatted }}</span>
                </div>

                <div class="mobile-data-row">
                  <span>Metode:</span>
                  <span>
                    @if(strtolower($sv->survey_method ?? '') === 'video call')
                      <span style="font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #f3e8ff; color: #7e22ce;">Video Call</span>
                    @else
                      <span style="font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #e0f2fe; color: #0369a1;">Datang Langsung</span>
                    @endif
                  </span>
                </div>

                @if($sv->message)
                  <div style="background: #f8fafc; border-radius: 10px; padding: 8px 10px; margin: 8px 0; font-size: 0.78rem; color: #334155; line-height: 1.4;">
                    <strong>Pesan:</strong> {{ $sv->message }}
                  </div>
                @endif

                <div style="margin-top: 8px;">
                  <select class="custom-select" style="width: 100%; font-size: 0.78rem; padding: 6px 10px; border-radius: 8px; font-weight: 600; border: 1.5px solid #cbd5e1; background: #ffffff;" onchange="window.updateSurveyStatusAjax({{ $sv->id }}, this.value)">
                    <option value="pending" {{ $sv->status === 'pending' ? 'selected' : '' }}>Status: Menunggu</option>
                    <option value="confirmed" {{ $sv->status === 'confirmed' ? 'selected' : '' }}>Status: Dikonfirmasi</option>
                    <option value="completed" {{ $sv->status === 'completed' ? 'selected' : '' }}>Status: Selesai</option>
                    <option value="cancelled" {{ $sv->status === 'cancelled' ? 'selected' : '' }}>Status: Dibatalkan</option>
                  </select>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; margin-top: 8px;">
                  <button type="button" class="btn-sm" style="background: #059669; color: #ffffff; border: none; padding: 8px 4px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" onclick="window.openSurveyWhatsAppChat('{{ $svPhone }}', '{{ htmlspecialchars($sv->name, ENT_QUOTES) }}', '{{ htmlspecialchars($svBranch, ENT_QUOTES) }}', '{{ htmlspecialchars($sv->room ?? '', ENT_QUOTES) }}', '{{ $svDateFormatted }}', '{{ $svTimeFormatted }}')" title="Chat Konfirmasi WhatsApp">
                    <span>Chat WA</span>
                  </button>
                  <button type="button" class="btn-sm" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 4px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" onclick="window.openSurveyDetailModal({{ $sv->id }})" title="Lihat Detail Pengajuan">
                    <span>Detail</span>
                  </button>
                  <button type="button" class="btn-sm" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 8px 4px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" onclick="window.deleteSurveyAjax({{ $sv->id }}, '{{ htmlspecialchars($sv->name, ENT_QUOTES) }}')" title="Hapus Permohonan Survei">
                    <span>Hapus</span>
                  </button>
                </div>
              </div>
            @empty
              <div style="text-align: center; padding: 24px; color: var(--text-muted);">Tidak Ada Data Permohonan Survei</div>
            @endforelse
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
              </div>
            </div>
          </div>
        </div>

        <!-- ====== PREMIUM SUB-TAB NAV ====== -->
        <div style="margin-bottom: 26px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 6px;">
          <div class="settings-segmented-nav" id="settingsSubTabNav" style="display: flex; gap: 4px; background: transparent; padding: 0; border-radius: 0;">
            <button type="button" class="settings-nav-btn btn-settings-subtab active" data-subtab="subtab-landing" onclick="switchSettingsSubtab('subtab-landing', this)" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Pengaturan Landing Page</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-location" onclick="switchSettingsSubtab('subtab-location', this)" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Titik Lokasi</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-profile" onclick="switchSettingsSubtab('subtab-profile', this)" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Tata Tertib</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-wifi" onclick="switchSettingsSubtab('subtab-wifi', this)" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Jaringan WiFi</span>
            </button>
            <button type="button" class="settings-nav-btn btn-settings-subtab" data-subtab="subtab-survey" onclick="switchSettingsSubtab('subtab-survey', this)" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.84rem; transition: all 0.2s;">
              <span>Formulir Calon Penghuni</span>
            </button>
          </div>
        </div>

        @php
          $lc = $setting->landing_content ?? [];
          $loginContent = $setting ? $setting->getLoginPageContent() : [];
          $bGallery = $lc['bento_gallery'] ?? [
              'assets/images/hero_scandinavian_room.webp',
              'assets/images/rooms/room_2.webp',
              'assets/images/rooms/room_3.webp',
              'assets/images/kos_exterior.webp'
          ];
        @endphp

        <!-- =========================================================================
             SUBTAB 1: PENGATURAN LANDING PAGE & HALAMAN LOGIN
             ========================================================================= -->
        <div class="settings-subtab-pane active" id="subtab-landing">

          <!-- =====================================================================
               CARD UTAMA: PENGATURAN KONTEN HALAMAN LOGIN & BRANDING SMART LIVING
               ===================================================================== -->
          <div class="settings-modern-card" style="margin-bottom: 24px; border: 1.5px solid rgba(3, 63, 60, 0.2); box-shadow: 0 12px 36px -10px rgba(3, 63, 60, 0.1);">
            <div class="settings-card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 18px; margin-bottom: 22px;">
              <div class="settings-card-title-group">
                <div class="settings-card-icon-badge" style="background: rgba(3, 63, 60, 0.12); color: #033f3c;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                  </svg>
                </div>
                <div>
                  <h3 class="settings-card-title" style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">Pengaturan Konten Halaman Login & Branding</h3>
                  <div class="settings-card-subtitle">Ubah seluruh teks promosi, nama brand, logo, serta formulir login secara dinamis dan real-time</div>
                </div>
              </div>
              <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <span style="font-size: 0.74rem; background: rgba(3, 63, 60, 0.08); color: #033f3c; padding: 5px 12px; border-radius: 9999px; font-weight: 700; border: 1px solid rgba(3, 63, 60, 0.15);">Tersinkronisasi Database</span>
                <a href="{{ route('login') }}" target="_blank" class="btn-secondary" style="font-size: 0.76rem; padding: 5px 12px; border-radius: 9999px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                  <span>Buka Login ↗</span>
                </a>
              </div>
            </div>

            <form id="loginPageEditorForm" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 24px;">
              @csrf

              <!-- SECTION A: PENGATURAN PROMOTIONAL SECTION (BAGIAN KIRI) -->
              <div style="background: var(--bg-hover, rgba(0,0,0,0.02)); border: 1px solid var(--border-color); border-radius: 16px; padding: 18px 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                  <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: #033f3c; color: #fff; font-weight: 800; font-size: 0.8rem;">A</span>
                  <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Pengaturan Promotional Section (Panel Kiri Hijau)</h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px;">
                  <div class="form-group">
                    <label for="lp_promo_headline" style="font-weight: 700; font-size: 0.85rem;">Headline Utama Promosi (Gunakan Enter untuk Baris Baru)</label>
                    <textarea id="lp_promo_headline" name="promo_headline" class="form-control" rows="3" required placeholder="Simplify&#10;management with&#10;our dashboard." style="font-weight: 700; font-size: 0.95rem;">{{ $loginContent['promo_headline'] ?? "Simplify\nmanagement with\nour dashboard." }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 4px; display: block;">Tulisan judul tebal berwarna putih dengan lengkung hijau di panel kiri.</span>
                  </div>

                  <div class="form-group">
                    <label for="lp_promo_desc" style="font-weight: 700; font-size: 0.85rem;">Supporting Text (Deskripsi Pendukung)</label>
                    <textarea id="lp_promo_desc" name="promo_desc" class="form-control" rows="3" required placeholder="Simplify your e-commerce management with our user-friendly admin dashboard.">{{ $loginContent['promo_desc'] ?? 'Simplify your e-commerce management with our user-friendly admin dashboard.' }}</textarea>
                    <span style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 4px; display: block;">Deskripsi penjelasan di bawah headline utama panel kiri.</span>
                  </div>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                  <label for="lp_promo_badge" style="font-weight: 700; font-size: 0.85rem;">Teks Tambahan / Badge Kategori (Opsional)</label>
                  <input type="text" id="lp_promo_badge" name="promo_badge" class="form-control" value="{{ $loginContent['promo_badge'] ?? 'Exclusive Management' }}" placeholder="Exclusive Management">
                </div>
              </div>

              <!-- SECTION B: PENGATURAN BRANDING & LOGO -->
              <div style="background: var(--bg-hover, rgba(0,0,0,0.02)); border: 1px solid var(--border-color); border-radius: 16px; padding: 18px 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                  <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: #033f3c; color: #fff; font-weight: 800; font-size: 0.8rem;">B</span>
                  <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Pengaturan Branding & Logo SMART LIVING</h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; align-items: start;">
                  <div class="form-group">
                    <label for="lp_brand_title" style="font-weight: 700; font-size: 0.85rem;">Nama Brand / Judul Brand</label>
                    <input type="text" id="lp_brand_title" name="brand_title" class="form-control" value="{{ $loginContent['brand_title'] ?? 'SMART LIVING' }}" required placeholder="SMART LIVING" style="font-weight: 800; letter-spacing: 0.5px;">
                    <span style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 4px; display: block;">Ditampilkan dengan huruf kapital di atas formulir login.</span>
                  </div>

                  <div class="form-group">
                    <label for="lp_brand_logo_size" style="font-weight: 700; font-size: 0.85rem;">Ukuran Tampilan Logo (px)</label>
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <input type="range" id="lp_brand_logo_size_slider" min="32" max="72" step="2" value="{{ $loginContent['brand_logo_size'] ?? 48 }}" style="flex: 1;" oninput="document.getElementById('lp_brand_logo_size').value = this.value; updateLiveLoginPreview();">
                      <input type="number" id="lp_brand_logo_size" name="brand_logo_size" class="form-control" value="{{ $loginContent['brand_logo_size'] ?? 48 }}" min="32" max="72" style="width: 75px; text-align: center; font-weight: 700;" oninput="document.getElementById('lp_brand_logo_size_slider').value = this.value; updateLiveLoginPreview();">
                    </div>
                  </div>

                  <!-- Upload Logo Box -->
                  <div class="form-group">
                    <label style="font-weight: 700; font-size: 0.85rem;">Upload Logo SMART LIVING</label>
                    <div style="display: flex; align-items: center; gap: 14px;">
                      <div id="lpBrandLogoPreviewWrap" style="width: 52px; height: 52px; border-radius: 14px; background: #033f3c; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; border: 1px solid var(--border-color);">
                        @if (!empty($loginContent['brand_logo_url']))
                          <img id="lpBrandLogoPreviewImg" src="{{ asset($loginContent['brand_logo_url']) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                          <svg id="lpBrandLogoDefaultIcon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
                            <path d="M9 21V12h6v9"/>
                          </svg>
                        @else
                          <svg id="lpBrandLogoDefaultIcon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
                            <path d="M9 21V12h6v9"/>
                          </svg>
                          <img id="lpBrandLogoPreviewImg" src="" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; display: none;">
                        @endif
                      </div>
                      <div style="flex: 1;">
                        <input type="file" id="lp_brand_logo" name="brand_logo" accept="image/*" class="form-control" style="font-size: 0.78rem; padding: 6px 10px;" onchange="previewLoginBrandLogo(this)">
                        <input type="hidden" id="lp_remove_logo" name="remove_logo" value="0">
                        @if (!empty($loginContent['brand_logo_url']))
                          <button type="button" onclick="removeCustomLoginLogo()" style="margin-top: 6px; background: none; border: none; font-size: 0.74rem; color: #dc2626; cursor: pointer; text-decoration: underline; padding: 0;">
                            Hapus logo kustom (Gunakan icon rumah hijau standar)
                          </button>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- SECTION C: PENGATURAN FORMULIR HALAMAN LOGIN -->
              <div style="background: var(--bg-hover, rgba(0,0,0,0.02)); border: 1px solid var(--border-color); border-radius: 16px; padding: 18px 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                  <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: #033f3c; color: #fff; font-weight: 800; font-size: 0.8rem;">C</span>
                  <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Pengaturan Teks Formulir Login (Panel Kanan)</h4>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                  <div class="form-group">
                    <label for="lp_form_subtitle" style="font-weight: 700; font-size: 0.85rem;">Subtitle / Petunjuk Login</label>
                    <input type="text" id="lp_form_subtitle" name="form_subtitle" class="form-control" value="{{ $loginContent['form_subtitle'] ?? 'Please login to your account' }}" required placeholder="Please login to your account">
                  </div>

                  <div class="form-group">
                    <label for="lp_placeholder_email" style="font-weight: 700; font-size: 0.85rem;">Placeholder Kolom Email</label>
                    <input type="text" id="lp_placeholder_email" name="placeholder_email" class="form-control" value="{{ $loginContent['placeholder_email'] ?? 'Email address' }}" required placeholder="Email address">
                  </div>

                  <div class="form-group">
                    <label for="lp_placeholder_password" style="font-weight: 700; font-size: 0.85rem;">Placeholder Kolom Password</label>
                    <input type="text" id="lp_placeholder_password" name="placeholder_password" class="form-control" value="{{ $loginContent['placeholder_password'] ?? 'Password' }}" required placeholder="Password">
                  </div>

                  <div class="form-group">
                    <label for="lp_forgot_password_text" style="font-weight: 700; font-size: 0.85rem;">Tulisan Link Forgot Password</label>
                    <input type="text" id="lp_forgot_password_text" name="forgot_password_text" class="form-control" value="{{ $loginContent['forgot_password_text'] ?? 'Forgot password?' }}" required placeholder="Forgot password?">
                  </div>

                  <div class="form-group">
                    <label for="lp_btn_login_text" style="font-weight: 700; font-size: 0.85rem;">Tulisan Tombol Utama Login</label>
                    <input type="text" id="lp_btn_login_text" name="btn_login_text" class="form-control" value="{{ $loginContent['btn_login_text'] ?? 'Login' }}" required placeholder="Login">
                  </div>

                  <div class="form-group">
                    <label for="lp_divider_text" style="font-weight: 700; font-size: 0.85rem;">Tulisan Pemisah (Or Login with)</label>
                    <input type="text" id="lp_divider_text" name="divider_text" class="form-control" value="{{ $loginContent['divider_text'] ?? 'Or Login with' }}" required placeholder="Or Login with">
                  </div>

                  <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="lp_back_home_text" style="font-weight: 700; font-size: 0.85rem;">Tulisan Link Kembali ke Beranda</label>
                    <input type="text" id="lp_back_home_text" name="back_home_text" class="form-control" value="{{ $loginContent['back_home_text'] ?? 'Kembali Ke Beranda' }}" required placeholder="Kembali Ke Beranda">
                  </div>
                </div>
              </div>

              <!-- SECTION D: PRATINJAU INTERAKTIF (LIVE PREVIEW) REAL-TIME -->
              <div style="background: #f8fafc; border: 1.5px dashed var(--border-color); border-radius: 20px; padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981; animation: pulse 1.8s infinite;"></span>
                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Pratinjau Interaktif Real-time (Live Preview)</h4>
                  </div>
                  <span style="font-size: 0.74rem; color: var(--text-secondary); background: #ffffff; padding: 4px 10px; border-radius: 9999px; border: 1px solid var(--border-color);">Tampilan akan otomatis berubah saat Anda mengetik di formulir atas</span>
                </div>

                <!-- PREVIEW CONTAINER CARD (SCALEABLE MOCKUP) -->
                <div style="max-width: 820px; margin: 0 auto; background: #ffffff; border-radius: 0; padding: 10px; box-shadow: none; border: none; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                  
                  <!-- Left Promo Mockup -->
                  <div style="background-color: #033f3c; background-image: url('{{ asset('assets/images/landing/login_left_card_clean.webp') }}?v={{ file_exists(public_path('assets/images/landing/login_left_card_clean.webp')) ? filemtime(public_path('assets/images/landing/login_left_card_clean.webp')) : time() }}'); background-repeat: no-repeat; background-size: cover; background-position: center bottom; border-radius: 20px; padding: 28px 24px 180px; position: relative; overflow: hidden; min-height: 380px;">
                    <div style="position: relative; z-index: 2;">
                      <div id="prevPromoHeadline" style="font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 800; color: #ffffff; line-height: 1.22; margin-bottom: 4px; white-space: pre-line;">
                        {{ $loginContent['promo_headline'] ?? "Simplify\nmanagement with\nour dashboard." }}
                      </div>
                      <svg width="80" height="10" viewBox="0 0 118 12" fill="none" style="display: block; margin: 4px 0 10px;">
                        <path d="M3 4C35 12 83 12 115 4" stroke="#22c55e" stroke-width="3" stroke-linecap="round"/>
                      </svg>
                      <div id="prevPromoDesc" style="color: rgba(255, 255, 255, 0.85); font-size: 11px; line-height: 1.5; max-width: 240px;">
                        {{ $loginContent['promo_desc'] ?? 'Simplify your e-commerce management with our user-friendly admin dashboard.' }}
                      </div>
                    </div>
                  </div>

                  <!-- Right Form Mockup -->
                  <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px 12px;">
                    <div style="width: 100%; max-width: 270px; display: flex; flex-direction: column; align-items: center;">
                      
                      <!-- Logo preview -->
                      <div id="prevLogoWrap" style="width: {{ $loginContent['brand_logo_size'] ?? 48 }}px; height: {{ $loginContent['brand_logo_size'] ?? 48 }}px; border-radius: 12px; background: #033f3c; display: flex; align-items: center; justify-content: center; color: #ffffff; margin-bottom: 8px;">
                        @if (!empty($loginContent['brand_logo_url']))
                          <img id="prevLogoImg" src="{{ asset($loginContent['brand_logo_url']) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                          <svg id="prevLogoIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" style="display: none;">
                            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
                            <path d="M9 21V12h6v9"/>
                          </svg>
                        @else
                          <svg id="prevLogoIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2">
                            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
                            <path d="M9 21V12h6v9"/>
                          </svg>
                          <img id="prevLogoImg" src="" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; display: none;">
                        @endif
                      </div>

                      <div id="prevBrandTitle" style="font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 800; color: #111827; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 2px;">
                        {{ $loginContent['brand_title'] ?? 'SMART LIVING' }}
                      </div>
                      <div id="prevFormSubtitle" style="font-size: 10px; color: #6b7280; margin-bottom: 16px;">
                        {{ $loginContent['form_subtitle'] ?? 'Please login to your account' }}
                      </div>

                      <!-- Mock Email -->
                      <div style="width: 100%; height: 36px; border-radius: 10px; border: 1.5px solid #e5e7eb; padding: 0 10px; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; background: #fff;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <span id="prevPlaceholderEmail" style="font-size: 10.5px; color: #9ca3af;">{{ $loginContent['placeholder_email'] ?? 'Email address' }}</span>
                      </div>

                      <!-- Mock Password -->
                      <div style="width: 100%; height: 36px; border-radius: 10px; border: 1.5px solid #e5e7eb; padding: 0 10px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; background: #fff;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                          <span id="prevPlaceholderPassword" style="font-size: 10.5px; color: #9ca3af;">{{ $loginContent['placeholder_password'] ?? 'Password' }}</span>
                        </div>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                      </div>

                      <!-- Mock Forgot -->
                      <div style="width: 100%; text-align: right; margin-bottom: 12px;">
                        <span id="prevForgotText" style="font-size: 9.5px; font-weight: 600; color: #033f3c;">{{ $loginContent['forgot_password_text'] ?? 'Forgot password?' }}</span>
                      </div>

                      <!-- Mock Submit -->
                      <div style="width: 100%; height: 36px; border-radius: 10px; background: #033f3c; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <span id="prevBtnLogin">{{ $loginContent['btn_login_text'] ?? 'Login' }}</span>
                      </div>

                      <!-- Mock Divider -->
                      <div style="width: 100%; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                        <div style="flex: 1; height: 1px; background: #e5e7eb;"></div>
                        <span id="prevDividerText" style="font-size: 9px; color: #9ca3af;">{{ $loginContent['divider_text'] ?? 'Or Login with' }}</span>
                        <div style="flex: 1; height: 1px; background: #e5e7eb;"></div>
                      </div>

                      <!-- Mock Google -->
                      <div style="width: 100%; height: 36px; border-radius: 10px; border: 1.5px solid #e5e7eb; background: #fff; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 11px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                        <span>Google</span>
                      </div>

                      <!-- Mock Back Home -->
                      <span id="prevBackHomeText" style="font-size: 10px; font-weight: 600; color: #033f3c;">
                        {{ $loginContent['back_home_text'] ?? 'Kembali Ke Beranda' }}
                      </span>

                    </div>
                  </div>

                </div>
              </div>

              <!-- ACTION BAR BUTTONS -->
              <div style="display: flex; gap: 14px; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid var(--border-color); flex-wrap: wrap;">
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                  <button type="submit" id="btnSaveLoginPage" class="btn-primary" style="padding: 12px 28px; font-size: 0.92rem; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; border-radius: 12px; background: #033f3c; border-color: #033f3c; box-shadow: 0 4px 14px rgba(3, 63, 60, 0.25);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span>Simpan Perubahan Landing Page</span>
                  </button>

                  <button type="button" id="btnResetLoginPage" onclick="resetLoginPageSettings()" class="btn-secondary" style="padding: 12px 20px; font-size: 0.88rem; font-weight: 700; border-radius: 12px; display: inline-flex; align-items: center; gap: 6px;" title="Kembalikan semua teks ke bawaan standar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    <span>Reset ke Pengaturan Default</span>
                  </button>
                </div>

                <div id="loginPageSaveStatus" style="font-size: 0.85rem; font-weight: 700; color: #10b981;"></div>
              </div>

            </form>
          </div>

          <!-- =====================================================================
               CARD KEDUA: KELOLA TAMPILAN BERANDA UTAMA (HERO, BENTO, FOTO & FOOTER)
               ===================================================================== -->
          <form id="landingPageEditorForm" style="display: flex; flex-direction: column; gap: 24px;" enctype="multipart/form-data">

            <!-- 1. HERO SECTION CARD -->
            <div class="settings-modern-card">
              <div class="settings-card-header">
                <div class="settings-card-title-group">
                  <div class="settings-card-icon-badge coral"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                  <div>
                    <h3 class="settings-card-title">1. Hero Banner Utama (Bagian Teratas)</h3>
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
          <form id="settingsProfileForm" style="display: flex; flex-direction: column; gap: 24px;" enctype="multipart/form-data">

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
                  <p style="color: rgba(255,255,255,0.7); font-size: 0.78rem; margin: 3px 0 0;">Setiap lokasi memiliki fasilitas, tipe kamar, alamat, foto profil &amp; kontrol visibilitas sendiri</p>
                </div>
              </div>
              <div style="display: flex; gap: 8px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.18);">
                <button type="button" class="branch-pill-tab active" id="btnTabBranch1" onclick="switchBranchSettings('lokasi_1')" style="display: flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; border: none; font-size: 0.84rem; font-weight: 800; cursor: pointer; transition: all 0.2s; background: #ffffff; color: #0a5661; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                  <span data-branch-name="lokasi_1">{{ $branch1Name }}</span>
                  <span id="badgeStatusTabB1" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 6px; font-weight: 700; background: {{ ($b1['is_active'] ?? true) ? '#10b981' : '#ef4444' }}; color: #fff;">{{ ($b1['is_active'] ?? true) ? 'Tampil' : 'Disembunyikan' }}</span>
                </button>
                <button type="button" class="branch-pill-tab" id="btnTabBranch2" onclick="switchBranchSettings('lokasi_2')" style="display: flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; border: none; font-size: 0.84rem; font-weight: 700; cursor: pointer; transition: all 0.2s; background: transparent; color: rgba(255,255,255,0.8);">
                  <span data-branch-name="lokasi_2">{{ $branch2Name }}</span>
                  <span id="badgeStatusTabB2" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 6px; font-weight: 700; background: {{ ($b2['is_active'] ?? true) ? '#10b981' : '#ef4444' }}; color: #fff;">{{ ($b2['is_active'] ?? true) ? 'Tampil' : 'Disembunyikan' }}</span>
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
                      <div class="settings-card-subtitle">Foto profil cabang, status visibilitas, alamat, peta Maps, dan fasilitas</div>
                    </div>
                  </div>
                </div>

                <!-- 1. KONTROL VISIBILITAS CABANG 1 (TAMPILKAN / SEMBUNYIKAN) -->
                <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 14px; padding: 16px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(10,86,97,0.1); color: #0a5661; display: flex; align-items: center; justify-content: center;">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div>
                      <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                        <span>Status Tampilan Cabang 1</span>
                        <span id="badgeB1Active" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 6px; font-weight: 700; background: {{ ($b1['is_active'] ?? true) ? '#dcfce7' : '#fee2e2' }}; color: {{ ($b1['is_active'] ?? true) ? '#15803d' : '#b91c1c' }};">
                          {{ ($b1['is_active'] ?? true) ? 'Aktif / Ditampilkan' : 'Disembunyikan' }}
                        </span>
                      </div>
                      <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 2px;">
                        Atur apakah Cabang 1 ditampilkan pada Ringkasan Cabang Dashboard dan Landing Page publik.
                      </div>
                    </div>
                  </div>
                  <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer; margin: 0; background: #ffffff; padding: 6px 14px; border-radius: 10px; border: 1.5px solid var(--border-color);">
                    <input type="checkbox" id="b1_is_active" name="b1_is_active" value="1" {{ ($b1['is_active'] ?? true) ? 'checked' : '' }} onchange="window.updateBranchActiveState('lokasi_1', this.checked)" style="width: 18px; height: 18px; accent-color: #0a5661; cursor: pointer;">
                    <span style="margin-left: 8px; font-size: 0.82rem; font-weight: 700; color: var(--text-dark);">Tampilkan Cabang 1</span>
                  </label>
                </div>

                <!-- 2. GANTI FOTO PROFIL CABANG 1 -->
                <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 14px; padding: 18px; margin-bottom: 20px;">
                  <div style="margin-bottom: 12px;">
                    <label style="font-weight: 800; font-size: 0.88rem; color: var(--text-dark); display: flex; align-items: center; gap: 8px; margin: 0;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0a5661" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                      Foto Profil / Thumbnail Cabang 1
                    </label>
                    <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 2px;">
                      Foto profil gedung yang tampil pada kartu Ringkasan Cabang di Dashboard dan Landing Page publik.
                    </div>
                  </div>
                  <div style="display: flex; gap: 18px; align-items: center; flex-wrap: wrap;">
                    <div style="position: relative; width: 140px; height: 95px; border-radius: 12px; overflow: hidden; border: 2px solid #0a5661; box-shadow: 0 4px 12px rgba(10,86,97,0.12); flex-shrink: 0; background: #0f172a;">
                      <img id="previewBranch1Image" src="{{ asset($b1['image'] ?? 'assets/images/kos_exterior.webp') }}" alt="Foto Profil Cabang 1" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 8px;">
                      <div>
                        <label style="font-size: 0.78rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 4px;">Pilih File Foto Baru untuk Cabang 1</label>
                        <input type="file" id="branch1_image_file" name="branch1_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="border-radius: 10px; font-size: 0.82rem;" onchange="window.previewBranchFile(this, 'previewBranch1Image')">
                      </div>
                      <input type="hidden" id="b1_image_url" value="{{ $b1['image'] ?? 'assets/images/kos_exterior.webp' }}">
                      <div style="font-size: 0.72rem; color: var(--text-muted);">
                        Format: JPG, PNG, WEBP. Maks 5MB. Rasio lanskap disarankan.
                      </div>
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
                      <div class="settings-card-subtitle">Foto profil cabang, status visibilitas, alamat, peta Maps, dan fasilitas</div>
                    </div>
                  </div>
                </div>

                <!-- 1. KONTROL VISIBILITAS CABANG 2 (TAMPILKAN / SEMBUNYIKAN) -->
                <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 14px; padding: 16px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(13,148,136,0.12); color: #0d9488; display: flex; align-items: center; justify-content: center;">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div>
                      <div style="font-weight: 800; font-size: 0.9rem; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                        <span>Status Tampilan Cabang 2</span>
                        <span id="badgeB2Active" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 6px; font-weight: 700; background: {{ ($b2['is_active'] ?? true) ? '#dcfce7' : '#fee2e2' }}; color: {{ ($b2['is_active'] ?? true) ? '#15803d' : '#b91c1c' }};">
                          {{ ($b2['is_active'] ?? true) ? 'Aktif / Ditampilkan' : 'Disembunyikan' }}
                        </span>
                      </div>
                      <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 2px;">
                        Atur apakah Cabang 2 ditampilkan pada Ringkasan Cabang Dashboard dan Landing Page publik.
                      </div>
                    </div>
                  </div>
                  <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer; margin: 0; background: #ffffff; padding: 6px 14px; border-radius: 10px; border: 1.5px solid var(--border-color);">
                    <input type="checkbox" id="b2_is_active" name="b2_is_active" value="1" {{ ($b2['is_active'] ?? true) ? 'checked' : '' }} onchange="window.updateBranchActiveState('lokasi_2', this.checked)" style="width: 18px; height: 18px; accent-color: #0d9488; cursor: pointer;">
                    <span style="margin-left: 8px; font-size: 0.82rem; font-weight: 700; color: var(--text-dark);">Tampilkan Cabang 2</span>
                  </label>
                </div>

                <!-- 2. GANTI FOTO PROFIL CABANG 2 -->
                <div style="background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: 14px; padding: 18px; margin-bottom: 20px;">
                  <div style="margin-bottom: 12px;">
                    <label style="font-weight: 800; font-size: 0.88rem; color: var(--text-dark); display: flex; align-items: center; gap: 8px; margin: 0;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                      Foto Profil / Thumbnail Cabang 2
                    </label>
                    <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 2px;">
                      Foto profil gedung yang tampil pada kartu Ringkasan Cabang di Dashboard dan Landing Page publik.
                    </div>
                  </div>
                  <div style="display: flex; gap: 18px; align-items: center; flex-wrap: wrap;">
                    <div style="position: relative; width: 140px; height: 95px; border-radius: 12px; overflow: hidden; border: 2px solid #0d9488; box-shadow: 0 4px 12px rgba(13,148,136,0.12); flex-shrink: 0; background: #0f172a;">
                      <img id="previewBranch2Image" src="{{ asset($b2['image'] ?? 'assets/images/landing/hero_corridor_teal.webp') }}" alt="Foto Profil Cabang 2" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 8px;">
                      <div>
                        <label style="font-size: 0.78rem; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 4px;">Pilih File Foto Baru untuk Cabang 2</label>
                        <input type="file" id="branch2_image_file" name="branch2_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="border-radius: 10px; font-size: 0.82rem;" onchange="window.previewBranchFile(this, 'previewBranch2Image')">
                      </div>
                      <input type="hidden" id="b2_image_url" value="{{ $b2['image'] ?? 'assets/images/landing/hero_corridor_teal.webp' }}">
                      <div style="font-size: 0.72rem; color: var(--text-muted);">
                        Format: JPG, PNG, WEBP. Maks 5MB. Rasio lanskap disarankan.
                      </div>
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
                @if(Auth::user()->isStaffOrOwner())
                  <button type="submit" class="btn-primary" style="padding: 12px 28px; font-weight: 800; border-radius: 12px; display: flex; align-items: center; gap: 8px;">
                    Simpan Seluruh Pengaturan 2 Lokasi &amp; Peta
                  </button>
                @else
                  <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 12px 18px; color: #dc2626; font-size: 0.8rem; font-weight: 700;">
                    Akses Dibatasi: Hanya Admin atau Owner yang berhak mengubah pengaturan 2 lokasi kos dan peta.
                  </div>
                @endif
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
                      <button type="button" class="btn-secondary" onclick="deleteWfItem(this)" style="padding: 4px 10px; border-radius: 8px; color: #ef4444; font-size: 0.75rem; font-weight: 700;" title="Hapus WiFi">
                        Hapus
                      </button>
                    </div>
                  </div>

                  @if(!empty($wf['password']))
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 12px; font-size: 0.78rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--text-muted); font-size: 0.72rem;">Sandi (Password):</span>
                        <span class="wf-pass-masked" style="font-family: monospace; font-weight: 700; color: var(--text-primary); letter-spacing: 2px;">••••••••</span>
                        <code style="display: none; font-family: monospace; font-weight: 700; color: #0a5661; background: #ccfbf1; padding: 2px 6px; border-radius: 4px;" class="wf-pass-text wf-pass-plain">{{ $wf['password'] }}</code>
                        <button type="button" onclick="toggleAdminWfPass(this)" class="btn-secondary" style="padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; cursor: pointer;">
                          <span class="admin-wf-toggle-label">Tampilkan</span>
                        </button>
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
    <div class="bottom-nav-item active" data-tab="tab-dashboard" onclick="window.switchTab('tab-dashboard')" title="Dashboard" aria-label="Dashboard">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
      <span>Dashboard</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-rooms" onclick="window.switchTab('tab-rooms')" title="Kamar" aria-label="Kamar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
      <span>Kamar</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-tenants" onclick="window.switchTab('tab-tenants')" title="Penghuni" aria-label="Penghuni">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <span>Penghuni</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-surveys" onclick="window.switchTab('tab-surveys')" title="Survei" aria-label="Survei">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <span>Survei</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-bills" onclick="window.switchTab('tab-bills')" title="Tagihan" aria-label="Tagihan">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      <span>Tagihan</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-transfers" onclick="window.switchTab('tab-transfers')" title="Pindah" aria-label="Pindah">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
      <span>Pindah</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-maintenance" onclick="window.switchTab('tab-maintenance')" title="Servis" aria-label="Servis">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      <span>Servis</span>
    </div>
    @if(Auth::user()->isOwner())
    <div class="bottom-nav-item" data-tab="tab-finances" onclick="window.switchTab('tab-finances')" title="Keuangan" aria-label="Keuangan">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      <span>Keuangan</span>
    </div>
    @endif
    <div class="bottom-nav-item" data-tab="tab-admins" onclick="window.switchTab('tab-admins')" title="Admin" aria-label="Admin">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span>Admin</span>
    </div>
    <div class="bottom-nav-item" data-tab="tab-settings" onclick="window.switchTab('tab-settings')" title="Pengaturan" aria-label="Pengaturan">
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
            <input type="text" id="setKosName" class="form-control" value="{{ $setting->kos_name }}" {{ !Auth::user()->isOwner() ? 'disabled readonly style=background:#f8fafc;cursor:not-allowed;color:#64748b;' : '' }}>
          </div>
          <div class="form-group">
            <label for="setOwnerName" style="font-weight: 700;">Nama Pemilik / Pengelola Utama</label>
            <input type="text" id="setOwnerName" class="form-control" value="{{ $setting->owner_name }}" {{ !Auth::user()->isOwner() ? 'disabled readonly style=background:#f8fafc;cursor:not-allowed;color:#64748b;' : '' }}>
          </div>
          <div class="form-group">
            <label for="setOwnerPhone" style="font-weight: 700;">No. WhatsApp Pengelola (Format 62...)</label>
            <input type="text" id="setOwnerPhone" class="form-control" value="{{ $setting->owner_phone }}" {{ !Auth::user()->isOwner() ? 'disabled readonly style=background:#f8fafc;cursor:not-allowed;color:#64748b;' : '' }}>
          </div>
          @if(Auth::user()->isOwner())
            <button type="submit" class="btn-primary" style="align-self: flex-start; margin-top: 6px; padding: 10px 22px; border-radius: 12px; font-weight: 800;">Simpan Akun Pengelola</button>
          @else
            <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 12px 16px; color: #dc2626; font-size: 0.78rem; font-weight: 700; line-height: 1.5;">
              Akses Terbatas: Hanya Role Owner yang memiliki hak untuk mengubah profil properti &amp; akun pengelola. Akun staf Admin tidak dapat mengubah profil.
            </div>
          @endif
        </form>

        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 22px 0;">

        <div>
          <h4 style="margin-bottom: 6px; font-size: 0.95rem; font-weight: 800; color: var(--text-primary);">Manajemen Data & Cadangan</h4>
          <p style="font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 14px;">Data tersimpan aman di database Laravel. Anda dapat mengekspor cadangan JSON sewaktu-waktu.</p>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn-secondary" id="btnExportData" style="border-radius: 10px; font-weight: 700;">Unduh Cadangan JSON</button>
            <button type="button" class="btn-secondary" id="btnResetData" style="color: #f87171; border-radius: 10px; font-weight: 700;">Reset Data Sampel</button>
          </div>
          <button type="button" class="btn-primary" onclick="window.closeModal('modalAkunPengelola'); window.switchTab('tab-admins');" style="margin-top: 12px; width: 100%; border-radius: 10px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Buka Menu Akun Admin</span>
          </button>
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
            <label for="adminBranchInput" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Penugasan Cabang Kos *</label>
            <select id="adminBranchInput" class="form-control" style="border-radius: 12px; font-weight: 700; color: #0f172a;">
              <option value="all">Kedua Cabang Kos (Kos 1 &amp; Kos 2)</option>
              <option value="Lokasi 1">Admin Kos Pertama ({{ $branch1Name }})</option>
              <option value="Lokasi 2">Admin Kos Kedua ({{ $branch2Name }})</option>
            </select>
            <span style="font-size: 0.72rem; color: #64748b; margin-top: 4px; display: block;">Tentukan apakah staf ini mengelola Kos Pertama, Kos Kedua, atau Kedua Cabang Kos.</span>
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
              Informasi email dan kata sandi ini dapat langsung Anda berikan secara manual kepada penghuni/anak kos untuk login ke portal.
            </div>
          </div>

          <!-- SECTION PENEMPATAN KAMAR & SEWA -->
          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="tenantSelectRoom" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Pilih Kamar & Lokasi *</label>
              <select id="tenantSelectRoom" class="form-control" required style="border-radius: 12px; font-weight: 600;">
                @php
                  $availRoomsLoc1 = $rooms->filter(fn($r) => ($r->location ?? 'Lokasi 1') === 'Lokasi 1' && $r->status === 'available');
                  $availRoomsLoc2 = $rooms->filter(fn($r) => ($r->location ?? '') === 'Lokasi 2' && $r->status === 'available');
                @endphp
                @if($availRoomsLoc1->count() > 0)
                  <optgroup label="{{ $branch1Name }}">
                    @foreach($availRoomsLoc1 as $ar)
                      <option value="{{ $ar->id }}" data-number="{{ $ar->number }}" data-price="{{ $ar->price }}">Kamar #{{ $ar->number }} ({{ $ar->type }}) - Tersedia (Rp {{ number_format($ar->price, 0, ',', '.') }}/bln)</option>
                    @endforeach
                  </optgroup>
                @endif
                @if($availRoomsLoc2->count() > 0)
                  <optgroup label="{{ $branch2Name }}">
                    @foreach($availRoomsLoc2 as $ar)
                      <option value="{{ $ar->id }}" data-number="{{ $ar->number }}" data-price="{{ $ar->price }}">Kamar #{{ $ar->number }} ({{ $ar->type }}) - Tersedia (Rp {{ number_format($ar->price, 0, ',', '.') }}/bln)</option>
                    @endforeach
                  </optgroup>
                @endif
                @if($availRoomsLoc1->count() === 0 && $availRoomsLoc2->count() === 0)
                  <option value="" disabled selected>Tidak ada kamar kosong yang tersedia saat ini</option>
                @endif
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalAddTenant" onclick="window.closeModal('modalAddTenant')">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 800;">Simpan & Buat Akun Penghuni</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: KREDENSIAL AKUN PENGHUNI BARU (UNTUK DIBERIKAN MANUAL OLEH ADMIN)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalTenantCreatedCredentials" style="display: none;" onclick="if(event.target === this) window.closeModal('modalTenantCreatedCredentials');">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.18); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800;">Akun Penghuni Baru Berhasil Dibuat</h3>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.74rem; margin: 2px 0 0;">Berikan email & kata sandi ini kepada pelanggan secara manual</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalTenantCreatedCredentials" onclick="window.closeModal('modalTenantCreatedCredentials')">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" style="padding: 22px; gap: 14px;">
        <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px 16px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Nama Penghuni:</span>
            <strong id="mCredTenantName" style="color: #0f172a; font-size: 0.85rem;">-</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Penempatan Kamar:</span>
            <strong id="mCredTenantRoom" style="color: #0a5661; font-size: 0.85rem;">-</strong>
          </div>
        </div>

        <div style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border: 1.5px solid #99f6e4; border-radius: 16px; padding: 16px;">
          <div style="margin-bottom: 12px;">
            <label style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: #0f766e; font-weight: 800; display: block; margin-bottom: 4px;">Email Login Portal</label>
            <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 10px 14px; border-radius: 10px; border: 1px solid #99f6e4;">
              <span id="mCredTenantEmail" style="font-family: monospace; font-size: 0.95rem; font-weight: 700; color: #04272c;">-</span>
            </div>
          </div>
          <div>
            <label style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: #0f766e; font-weight: 800; display: block; margin-bottom: 4px;">Kata Sandi (Password)</label>
            <div style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 10px 14px; border-radius: 10px; border: 1px solid #99f6e4;">
              <span id="mCredTenantPassword" style="font-family: monospace; font-size: 0.95rem; font-weight: 700; color: #04272c;">-</span>
            </div>
          </div>
        </div>

        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 10px 14px; font-size: 0.76rem; color: #92400e; line-height: 1.45;">
          Saat penghuni pertama kali login ke portal menggunakan akun di atas, sistem akan menampilkan pop-up wajib untuk melengkapi data diri (No. WhatsApp, wajib upload foto KTP / SIM / KTM / KTA, dan Kontak Darurat) sebelum fitur portal lainnya dapat diakses.
        </div>
      </div>
      <div class="modal-footer" style="padding: 16px 22px; justify-content: space-between; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <button type="button" class="btn-secondary" onclick="window.closeModal('modalTenantCreatedCredentials')">Tutup</button>
        <button type="button" id="btnCopyCreatedCreds" onclick="window.copyCreatedTenantCreds(this)" class="btn-primary" style="padding: 10px 20px; border-radius: 12px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          <span>Salin Email & Password</span>
        </button>
      </div>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: EDIT DATA PENGHUNI KOS
       ========================================================================= -->
  <div class="modal-backdrop" id="modalEditTenant" style="display: none;" onclick="if(event.target === this) window.closeModal('modalEditTenant');">
    <div class="modal-dialog" style="max-width: 600px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.15rem; font-weight: 800; letter-spacing: -0.2px;">Edit Data Penghuni Kos</h3>
            <p id="editTenantSubtitle" style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Perbarui data profil, kamar, tanggal jatuh tempo, dan tarif sewa</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalEditTenant" onclick="window.closeModal('modalEditTenant')">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formEditTenant" onsubmit="window.submitEditTenantForm(event)">
        <input type="hidden" id="editTenantId" value="">
        <input type="hidden" id="editTenantOriginalRoomId" value="">

        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          <div class="form-group">
            <label for="editTenantName" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Nama Lengkap Penghuni *</label>
            <input type="text" id="editTenantName" class="form-control" placeholder="Nama lengkap penghuni..." required style="border-radius: 12px;">
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantPhone" style="font-weight: 700; font-size: 0.82rem; color: #334155;">No. WhatsApp *</label>
              <input type="tel" id="editTenantPhone" class="form-control" placeholder="0812xxxxxxxx" required style="border-radius: 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantIdCard" style="font-weight: 700; font-size: 0.82rem; color: #334155;">No. KTP / SIM / KTM / KTA</label>
              <input type="text" id="editTenantIdCard" class="form-control" placeholder="33xxxxxxxxxxxxxx" style="border-radius: 12px;">
              <div id="editTenantIdCardPhotoWrap" style="display: none; margin-top: 8px; padding: 6px 10px; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 10px; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <img id="editTenantIdCardPhotoImg" src="" alt="Foto Identitas" style="width: 44px; height: 30px; object-fit: cover; border-radius: 4px; border: 1px solid #86efac;">
                  <span style="font-size: 0.72rem; font-weight: 700; color: #166534;">Foto Kartu Terlampir</span>
                </div>
                <a id="editTenantIdCardPhotoLink" href="#" target="_blank" style="font-size: 0.72rem; font-weight: 700; color: #15803d; text-decoration: underline;">
                  Lihat Foto
                </a>
              </div>
            </div>
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantRoomSelect" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Pilih Kamar & Lokasi *</label>
              <select id="editTenantRoomSelect" class="form-control" required style="border-radius: 12px; font-weight: 600;">
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantStartDate" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tanggal Masuk *</label>
              <input type="date" id="editTenantStartDate" class="form-control" required style="border-radius: 12px;">
            </div>
          </div>

          <div class="form-row" style="gap: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantDueDay" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tgl Jatuh Tempo Bulanan *</label>
              <input type="number" id="editTenantDueDay" class="form-control" min="1" max="31" value="1" required style="border-radius: 12px;">
              <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px;" id="editDueDayQuickPills">
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(1)">Tgl 1</button>
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(5)">Tgl 5</button>
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(10)">Tgl 10</button>
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(15)">Tgl 15</button>
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(20)">Tgl 20</button>
                <button type="button" class="due-pill-btn" style="padding: 2px 8px; font-size: 0.72rem;" onclick="window.setEditDueDayInputValue(25)">Tgl 25</button>
              </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label for="editTenantRentAmount" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Tarif Sewa (Rp / Bulan) *</label>
              <input type="number" id="editTenantRentAmount" class="form-control" placeholder="1500000" required style="border-radius: 12px;">
            </div>
          </div>

          <div class="form-group">
            <label for="editTenantEmergencyContact" style="font-weight: 700; font-size: 0.82rem; color: #334155;">Kontak Darurat (Ortu / Wali / Saudara)</label>
            <input type="text" id="editTenantEmergencyContact" class="form-control" placeholder="Contoh: Bpk. Joko (0813xxxxxxxx) - Orang Tua" style="border-radius: 12px;">
          </div>

          <!-- SECTION AKUN PORTAL PENGHUNI (OPSIONAL) -->
          <div style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border: 1.5px solid #99f6e4; border-radius: 16px; padding: 14px; margin-top: 4px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 24px; height: 24px; background: #0a5661; color: #fff; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <span style="font-weight: 800; font-size: 0.84rem; color: #04272c;">Akun Login Portal Anak Kos</span>
              </div>
              <span style="font-size: 0.68rem; color: #0f766e; background: #ccfbf1; padding: 2px 7px; border-radius: 6px; font-weight: 700;">Opsional</span>
            </div>

            <div class="form-row" style="gap: 12px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label for="editTenantEmail" style="font-weight: 700; font-size: 0.78rem; color: #0f766e;">Email Akun (Gmail)</label>
                <input type="email" id="editTenantEmail" class="form-control" placeholder="dimas@gmail.com" style="border-radius: 10px; border-color: #99f6e4; background: #fff; font-size: 0.85rem;">
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label for="editTenantPassword" style="font-weight: 700; font-size: 0.78rem; color: #0f766e;">Ganti Sandi Baru</label>
                <input type="text" id="editTenantPassword" class="form-control" placeholder="Kosongkan jika tetap..." style="border-radius: 10px; border-color: #99f6e4; background: #fff; font-family: monospace; font-size: 0.85rem;">
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalEditTenant" onclick="window.closeModal('modalEditTenant')">Batal</button>
          <button type="submit" id="btnSubmitEditTenant" class="btn-primary" style="padding: 10px 22px; border-radius: 12px; font-weight: 800; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">Simpan Perubahan Penghuni</button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: EDIT TANGGAL JATUH TEMPO SEWA
       ========================================================================= -->
  <div class="modal-backdrop" id="modalEditDueDay" style="display: none;" onclick="if(event.target === this) window.closeModal('modalEditDueDay');">
    <div class="modal-dialog" style="max-width: 480px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #032328 0%, #063d45 45%, #0a5661 100%);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div>
            <h3 style="color: #fff; margin: 0; font-size: 1.12rem; font-weight: 800; letter-spacing: -0.2px;">Ubah Tanggal Jatuh Tempo</h3>
            <p id="editDueDaySubtitle" style="color: rgba(255,255,255,0.75); font-size: 0.74rem; margin: 2px 0 0;">Kamar #101 — Penghuni</p>
          </div>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalEditDueDay" onclick="window.closeModal('modalEditDueDay')">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formEditDueDay" onsubmit="window.submitEditDueDayForm(event)">
        <input type="hidden" id="editDueTargetId" value="">
        <input type="hidden" id="editDueRoomNumber" value="">
        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          
          <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 14px; padding: 14px 16px;">
            <div style="font-size: 0.74rem; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.05em;">PILIHAN CEPAT TANGGAL</div>
            <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;" id="dueDayQuickPills">
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(1)">Tgl 1</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(5)">Tgl 5</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(10)">Tgl 10</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(15)">Tgl 15</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(20)">Tgl 20</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(25)">Tgl 25</button>
              <button type="button" class="due-pill-btn" onclick="window.setDueDayInputValue(28)">Tgl 28</button>
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="editDueDayInput" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Pilih Tanggal Jatuh Tempo (1 - 31) *</label>
            <div style="display: flex; align-items: center; gap: 10px;">
              <button type="button" class="btn-secondary" style="width: 44px; height: 44px; font-size: 1.2rem; font-weight: 800; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 0;" onclick="window.stepDueDayInput(-1)">-</button>
              <input type="number" id="editDueDayInput" class="form-control" min="1" max="31" value="1" required style="border-radius: 12px; text-align: center; font-size: 1.15rem; font-weight: 800; color: #0a5661; height: 44px;">
              <button type="button" class="btn-secondary" style="width: 44px; height: 44px; font-size: 1.2rem; font-weight: 800; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 0;" onclick="window.stepDueDayInput(1)">+</button>
            </div>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 6px;">Setiap tanggal ini tiba tiap bulannya, status sewa kamar otomatis berubah menjadi <strong>Jatuh Tempo</strong> jika belum dilunasi.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" data-close-modal="modalEditDueDay" onclick="window.closeModal('modalEditDueDay')">Batal</button>
          <button type="submit" class="btn-primary" id="btnSubmitEditDueDay" style="padding: 10px 22px; border-radius: 12px; font-weight: 800; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
            Simpan Tanggal Jatuh Tempo
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: KONFIRMASI PINDAH KAMAR (MODERN POPUP GANTI NATIVE PROMPT)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalConfirmRoomTransfer" style="display: none; position: fixed; inset: 0; background: rgba(3, 35, 40, 0.72); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 2000; align-items: center; justify-content: center; padding: 16px;" onclick="if(event.target === this) window.closeModal('modalConfirmRoomTransfer');">
    <div class="modal-dialog" style="max-width: 520px;">
      <div class="modal-header" id="confirmTransferHeader" style="background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
        <div>
          <h3 id="confirmTransferTitle" style="color: #ffffff; font-weight: 800; font-size: 1.15rem; margin: 0;">Konfirmasi Pindah Kamar</h3>
          <p id="confirmTransferSubtitle" style="color: rgba(255,255,255,0.85); font-size: 0.76rem; margin: 3px 0 0 0;">Persetujuan permohonan mutasi kamar penghuni kos</p>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalConfirmRoomTransfer" onclick="window.closeModal('modalConfirmRoomTransfer')" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formConfirmRoomTransfer" onsubmit="window.submitConfirmRoomTransfer(event)">
        <input type="hidden" id="confirmTransferId" value="">
        <input type="hidden" id="confirmTransferStatus" value="approved">

        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          <!-- Detail Pemohon & Rute Kamar -->
          <div style="background: #f0fdfa; border: 1.5px solid #ccfbf1; border-radius: 14px; padding: 14px 16px;">
            <div style="font-size: 0.7rem; font-weight: 800; color: #0f766e; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">DETAIL MUTASI KAMAR</div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <span style="font-size: 0.82rem; color: #475569;">Penghuni:</span>
              <strong id="confirmTransferTenant" style="font-size: 0.92rem; color: #0a5661;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 0.82rem; color: #475569;">Rute Pindah:</span>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge" id="confirmTransferFromRoom" style="background: #ffffff; color: #334155; font-weight: 800; padding: 4px 10px; border-radius: 8px; border: 1px solid #cbd5e1;">#101</span>
                <span style="color: #0d9488; font-weight: 800; font-size: 1.1rem;">→</span>
                <span class="badge" id="confirmTransferToRoom" style="background: #0a5661; color: #ffffff; font-weight: 800; padding: 4px 10px; border-radius: 8px;">#201</span>
              </div>
            </div>
          </div>

          <!-- Tindakan Status Indicator Banner -->
          <div id="confirmTransferActionBanner" style="padding: 12px 16px; border-radius: 12px; font-size: 0.82rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
          </div>

          <!-- Catatan Pengelola -->
          <div class="form-group" style="margin-bottom: 0;">
            <label for="confirmTransferNote" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Catatan / Pesan Pengelola untuk Penghuni (Opsional)</label>
            <textarea id="confirmTransferNote" class="form-control" rows="3" style="border-radius: 12px; font-size: 0.85rem; line-height: 1.45; resize: vertical; border-color: #cbd5e1;" placeholder="Tuliskan catatan atau instruksi lanjutan untuk penghuni..."></textarea>
            <div style="font-size: 0.72rem; color: #64748b; margin-top: 5px;">Catatan ini akan tampil pada portal akun penghuni dan riwayat permohonan.</div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 16px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalConfirmRoomTransfer" onclick="window.closeModal('modalConfirmRoomTransfer')" style="border-radius: 10px; font-weight: 700; padding: 9px 18px;">Batal</button>
          <button type="submit" id="btnSubmitConfirmTransfer" class="btn-primary" style="padding: 9px 22px; border-radius: 10px; font-weight: 800;">
            Konfirmasi
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: TAMBAH LANTAI BANGUNAN KOS
       ========================================================================= -->
  <div class="modal-backdrop" id="modalAddFloor" style="display: none;" onclick="if(event.target === this) window.closeModal('modalAddFloor');">
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%);">
        <div>
          <h3 style="color: #ffffff; font-weight: 800; font-size: 1.15rem; margin: 0;">Tambah Lantai Bangunan Kos</h3>
          <p style="color: rgba(255,255,255,0.85); font-size: 0.76rem; margin: 3px 0 0 0;">Konfigurasi tingkat lantai baru untuk unit kamar kos</p>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalAddFloor" onclick="window.closeModal('modalAddFloor')" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formAddFloor" onsubmit="window.submitAddFloor(event)">
        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          <div class="form-group" style="margin-bottom: 0;">
            <label for="addFloorLocation" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Pilih Cabang Kos *</label>
            <select id="addFloorLocation" class="form-control" style="border-radius: 10px;" required>
              <option value="all">Semua Cabang (KOS UNEJ &amp; KOS UNMUH)</option>
              <option value="Lokasi 1">KOS UNEJ (Lokasi 1)</option>
              <option value="Lokasi 2">KOS UNMUH (Lokasi 2)</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="addFloorNumber" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Tingkat Lantai *</label>
            <select id="addFloorNumber" class="form-control" style="border-radius: 10px;" required>
              <option value="3" selected>Lantai 3 (Lantai Atas / Rooftop)</option>
              <option value="4">Lantai 4</option>
              <option value="5">Lantai 5</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="addFloorLabel" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Nama / Label Lantai</label>
            <input type="text" id="addFloorLabel" class="form-control" value="Lantai 3" placeholder="Contoh: Lantai 3 (Rooftop)" style="border-radius: 10px;">
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="addFloorDesc" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Deskripsi / Fasilitas Lantai (Opsional)</label>
            <input type="text" id="addFloorDesc" class="form-control" placeholder="Contoh: Area Rooftop, Pantry bersama, Balkon santai" style="border-radius: 10px;">
          </div>

          <div style="background: #f0fdfa; border: 1.5px solid #ccfbf1; border-radius: 12px; padding: 12px 14px; font-size: 0.78rem; color: #0f766e; line-height: 1.5;">
            Lantai 3 telah aktif pada denah kamar dashboard, tabel daftar master kamar, dan formulir penambahan unit kamar.
          </div>
        </div>
        <div class="modal-footer" style="padding: 16px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalAddFloor" onclick="window.closeModal('modalAddFloor')" style="border-radius: 10px; font-weight: 700; padding: 9px 18px;">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 9px 22px; border-radius: 10px; font-weight: 800;">
            Simpan &amp; Aktifkan Lantai
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- =========================================================================
       MODAL: HAPUS LANTAI BANGUNAN KOS
       ========================================================================= -->
  <div class="modal-backdrop" id="modalDeleteFloor" style="display: none;" onclick="if(event.target === this) window.closeModal('modalDeleteFloor');">
    <div class="modal-dialog" style="max-width: 500px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
        <div>
          <h3 style="color: #ffffff; font-weight: 800; font-size: 1.15rem; margin: 0;">Hapus Lantai Bangunan Kos</h3>
          <p style="color: rgba(255,255,255,0.85); font-size: 0.76rem; margin: 3px 0 0 0;">Pilih lantai yang ingin dihapus dari sistem kos</p>
        </div>
        <button type="button" class="modal-close-btn" data-close-modal="modalDeleteFloor" onclick="window.closeModal('modalDeleteFloor')" aria-label="Tutup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="formDeleteFloor" onsubmit="window.submitDeleteFloor(event)">
        <div class="modal-body" style="padding: 22px; display: flex; flex-direction: column; gap: 16px;">
          <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 14px 16px; display: flex; gap: 12px; align-items: flex-start;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div style="font-size: 0.8rem; color: #991b1b; line-height: 1.45;">
              <strong>Perhatian Penting:</strong> Menghapus lantai akan menghapus seluruh data unit kamar pada lantai tersebut. Pastikan tidak ada penghuni aktif yang masih menghuni kamar pada lantai yang dipilih.
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="deleteFloorSelect" style="font-weight: 700; font-size: 0.82rem; color: #334155; margin-bottom: 6px;">Pilih Tingkat Lantai yang Akan Dihapus *</label>
            <select id="deleteFloorSelect" class="form-control" style="border-radius: 10px; border-color: #fca5a5;" required onchange="window.updateDeleteFloorInfo()">
              @foreach($existingFloors as $fl)
                <option value="{{ $fl }}">Lantai {{ $fl }}</option>
              @endforeach
            </select>
          </div>

          <div id="deleteFloorInfoBox" style="font-size: 0.82rem; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px;">
            Memuat data kamar pada lantai terpilih...
          </div>
        </div>
        <div class="modal-footer" style="padding: 16px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalDeleteFloor" onclick="window.closeModal('modalDeleteFloor')" style="border-radius: 10px; font-weight: 700; padding: 9px 18px;">Batal</button>
          <button type="submit" class="btn-primary" style="padding: 9px 22px; border-radius: 10px; font-weight: 800; background: #dc2626; border-color: #dc2626;">
            Lanjutkan Hapus Lantai
          </button>
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
                  @foreach($existingFloors as $fl)
                    <option value="{{ $fl }}">Lantai {{ $fl }}</option>
                  @endforeach
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
                @php
                  $t = $r->activeTenant;
                  $rPriceNum = (int)$r->price;
                  $rIncomesM = ($allFinances ?? collect())->filter(function($f) use ($r, $nowMonthNum, $nowYearNum) {
                      if ($f->type !== 'income' || $f->room_id != $r->id) return false;
                      $dt = \Carbon\Carbon::parse($f->date);
                      return $dt->month === $nowMonthNum && $dt->year === $nowYearNum;
                  });
                  $rPaidM = (int)$rIncomesM->sum('amount');
                  $rRemM = max(0, $rPriceNum - $rPaidM);
                  $rIsPartial = ($rPaidM > 0 && $rRemM > 0);
                @endphp
                <option value="{{ $r->id }}" data-price="{{ $rPriceNum }}" data-paid="{{ $rPaidM }}" data-rem="{{ $rRemM }}" data-full-price="{{ $rPriceNum }}" data-tenant="{{ $t ? $t->name : 'Unit Kosong' }}" data-room="{{ $r->number }}">
                  Kamar #{{ $r->number }} ({{ $r->type }}) — {{ $t ? $t->name : '(Unit Kosong)' }} [Tarif Rp {{ number_format($r->price, 0, ',', '.') }}{{ $rIsPartial ? ' • Dicicil Rp ' . number_format($rPaidM, 0, ',', '.') . ' (Sisa Rp ' . number_format($rRemM, 0, ',', '.') . ')' : '' }}]
                </option>
              @endforeach
            </select>
            <div id="payRecInstallmentInfo" style="display: none; margin-top: 8px;"></div>
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

  @if(Auth::user()->isOwner())
  <!-- =========================================================================
       MODAL: KONFIRMASI SIMPAN TAHUN LAPORAN KEUANGAN (KHUSUS ROLE OWNER)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalConfirmSaveFinanceYear" onclick="if(event.target === this) window.closeConfirmSaveFinanceYear()" style="display: none; position: fixed; inset: 0; background: rgba(3, 35, 40, 0.78); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 20px;">
    <div class="modal-dialog" style="max-width: 440px; width: 100%; margin: auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 28px 65px -12px rgba(3, 35, 40, 0.45); border: 1.5px solid rgba(13, 148, 136, 0.28); text-align: center; padding: 32px 26px;">
      <div style="width: 68px; height: 68px; border-radius: 22px; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; background: rgba(13, 148, 136, 0.12); color: #0d9488; border: 1.5px solid rgba(13, 148, 136, 0.32);">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      <h3 style="margin: 0 0 8px; font-size: 1.3rem; font-weight: 800; color: #0f172a;">Yakin disimpan?</h3>
      <p style="margin: 0 0 24px; font-size: 0.9rem; color: #475569; line-height: 1.55;">
        Tahun laporan keuangan akan diganti ke <strong>Tahun <span id="targetSaveYearText">2026</span></strong> dan seluruh data kas akan diperbarui.
      </p>
      <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
        <button type="button" class="btn-secondary" onclick="window.closeConfirmSaveFinanceYear()" style="min-width: 110px; padding: 11px 22px; border-radius: 12px; font-weight: 700; cursor: pointer; border: 1px solid #cbd5e1; background: #f1f5f9; color: #334155;">
          Batal
        </button>
        <button type="button" class="btn-primary" id="btnConfirmSaveYearModal" onclick="window.applyConfirmedFinanceYear()" style="min-width: 140px; padding: 11px 26px; border-radius: 12px; font-weight: 800; cursor: pointer; border: none; background: linear-gradient(135deg, #0d9488 0%, #0a5661 100%); color: #ffffff; box-shadow: 0 4px 14px rgba(10, 86, 97, 0.35);">
          Iya, Simpan
        </button>
      </div>
    </div>
  </div>
  @endif

  <!-- =========================================================================
       MODAL: LAPORAN KEUANGAN PDF MODERN & EKSEKUTIF (PREVIEW & CETAK/SIMPAN PDF)
       ========================================================================= -->
  <div class="modal-backdrop" id="modalFinancePdfPreview" onclick="if(event.target === this) closeFinancePdfModal()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div class="modal-dialog" style="max-width: 960px; width: 100%; max-height: 94vh; margin: auto; display: flex; flex-direction: column; border-radius: 24px; overflow: hidden; background: #ffffff; border: 1.5px solid rgba(13, 148, 136, 0.25); box-shadow: 0 25px 60px rgba(10, 86, 97, 0.22), 0 0 1px rgba(0,0,0,0.08);">
      <!-- Toolbar Header PDF (Clean Modern White Theme) -->
      <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; background: #ffffff; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 12px; background: #f0fdfa; border: 1.5px solid #99f6e4; display: flex; align-items: center; justify-content: center; color: #0a5661;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a;" id="pdfModalTitleText">Dokumen PDF Laporan Keuangan</h3>
              <span style="background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0; font-size: 0.68rem; font-weight: 800; padding: 2px 9px; border-radius: 999px; letter-spacing: 0.04em;">A4 EXECUTIVE PDF</span>
            </div>
            <p style="margin: 2px 0 0 0; font-size: 0.76rem; color: #64748b;" id="pdfModalSubtitleText">Pratinjau resmi dokumen laporan keuangan siap unduh &amp; cetak</p>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <button type="button" onclick="downloadFinancePdfDocument()" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; border: none; padding: 10px 18px; border-radius: 12px; font-size: 0.82rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(10, 86, 97, 0.28);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Download / Cetak PDF</span>
          </button>
          <button type="button" class="modal-close-btn" data-close-modal="modalFinancePdfPreview" onclick="closeFinancePdfModal()" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; width: 36px; height: 36px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </div>

      <!-- Canvas Area A4 Document Sheet (Soft Clean Light Gray Backdrop) -->
      <div style="padding: 24px; overflow-y: auto; background: #f1f5f9; flex: 1; display: flex; justify-content: center;">
        <div id="financePdfA4Sheet" style="width: 100%; max-width: 820px; background: #ffffff; color: #0f172a; border-radius: 14px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; overflow: hidden; font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;">
          <!-- Injected dynamically by openFinancePdfModal() -->
        </div>
      </div>

      <!-- Footer PDF Modal (Clean Modern White Theme) -->
      <div style="padding: 14px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 0.76rem; color: #64748b;">Format Dokumen: Standar A4 Portrait &bull; Otomatis menyesuaikan Periode Bulan, Tahun &amp; Cabang Kos yang dipilih</span>
        <div style="display: flex; gap: 10px;">
          <button type="button" class="btn-secondary" data-close-modal="modalFinancePdfPreview" onclick="closeFinancePdfModal()" style="background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; cursor: pointer; padding: 9px 18px; border-radius: 10px; font-weight: 700;">Tutup Pratinjau</button>
          <button type="button" onclick="downloadFinancePdfDocument()" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #0a5661 0%, #0d9488 100%); color: #ffffff; border: 1px solid #0d9488; padding: 9px 20px; border-radius: 11px; font-size: 0.82rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(10, 86, 97, 0.25);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            <span>Simpan sebagai PDF</span>
          </button>
        </div>
      </div>
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

  <!-- =========================================================================
       MODAL: DETAIL JADWAL SURVEI LOKASI MASUK DARI CALON PENGHUNI (AJAX)
       ========================================================================= -->
  <div class="kmr-logout-overlay" id="modalSurveyDetail" style="display: none; z-index: 99999;">
    <div class="kmr-logout-card" style="max-width: 540px; text-align: left; padding: 24px; border-radius: 20px;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div>
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a;">Jadwal Survei Lokasi Masuk</h3>
            <span style="font-size: 0.72rem; color: #64748b;" id="sdTimeAgo">Baru saja</span>
          </div>
        </div>
        <button type="button" onclick="window.closeSurveyDetailModal()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer; line-height: 1;" title="Tutup">&times;</button>
      </div>

      <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.85rem;" id="surveyDetailBody">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Calon Penghuni</span>
              <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem;" id="sdName">-</div>
            </div>
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Status / Profesi</span>
              <div style="font-weight: 700; color: #334155;" id="sdJob">-</div>
            </div>
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">No. WhatsApp</span>
              <div style="font-weight: 800; color: #0284c7;" id="sdPhone">-</div>
            </div>
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Pilihan Cabang</span>
              <div style="font-weight: 700; color: #0f766e;" id="sdBranch">-</div>
            </div>
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Kamar yang Diminati</span>
              <div style="font-weight: 700; color: #0f172a;" id="sdRoom">-</div>
            </div>
            <div>
              <span style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Metode Kunjungan</span>
              <div style="font-weight: 700; color: #7c3aed;" id="sdMethod">-</div>
            </div>
          </div>
          <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">Rencana Waktu Survei:</span>
            <span style="font-weight: 800; color: #059669; font-size: 0.88rem;" id="sdDateTime">-</span>
          </div>
        </div>

        <div>
          <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">Format Pesan WhatsApp Masuk:</span>
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; font-size: 0.78rem; color: #334155; white-space: pre-line; max-height: 120px; overflow-y: auto; font-family: inherit; margin-top: 4px;" id="sdMessage">
            -
          </div>
        </div>
      </div>

      <div style="margin-top: 18px; display: flex; gap: 8px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
        <button type="button" class="btn-secondary" onclick="window.closeSurveyDetailModal()" style="padding: 8px 14px; font-size: 0.8rem; border-radius: 8px;">Tutup</button>
        <button type="button" class="btn-secondary" id="btnMarkSurveyDone" onclick="window.markSurveyCompleted()" style="padding: 8px 14px; font-size: 0.8rem; border-radius: 8px; background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; font-weight: 700;">Tandai Selesai</button>
        <button type="button" id="btnModalAddToCalendar" onclick="if(window.activeSurveyDetailId) window.addToCalendar(window.activeSurveyDetailId)" style="padding: 8px 14px; font-size: 0.8rem; border-radius: 8px; background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M12 14v4"/><path d="M10 16h4"/></svg>
          <span>Tambahkan ke Kalender</span>
        </button>
        <a id="btnChatSurveyWa" href="#" target="_blank" class="btn-primary" style="padding: 8px 16px; font-size: 0.8rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #059669; border-color: #059669;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.27-2.42 5.82a8.19 8.19 0 0 1-5.82 2.42c-1.42 0-2.82-.37-4.06-1.1l-.29-.17-3.02.79.81-2.94-.19-.3a8.21 8.21 0 0 1-1.26-4.52c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.72 4.31 3.81.6.26 1.07.42 1.44.54.61.19 1.16.17 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
          <span>Chat Calon Anak Kos</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Modern Alert & Confirm modal is globally defined in layouts/app.blade.php -->

  <script>
    // Konfirmasi Logout Admin Modern
    var formLogoutAdmin = document.getElementById('formLogoutAdmin');
    var modalAdminLogout = document.getElementById('modalAdminLogoutConfirm');
    if (modalAdminLogout) {
      document.getElementById('btnTriggerLogoutAdmin')?.addEventListener('click', function(e) {
        e.preventDefault();
        modalAdminLogout.classList.add('open');
        modalAdminLogout.style.display = 'flex';
        modalAdminLogout.style.pointerEvents = 'auto';
      });
      document.getElementById('btnCancelAdminLogout')?.addEventListener('click', function() {
        modalAdminLogout.classList.remove('open');
        modalAdminLogout.style.display = 'none';
        modalAdminLogout.style.pointerEvents = 'none';
      });
      document.getElementById('btnConfirmAdminLogout')?.addEventListener('click', function() {
        formLogoutAdmin?.submit();
      });
      modalAdminLogout.addEventListener('click', function(e) {
        if (e.target === modalAdminLogout) {
          modalAdminLogout.classList.remove('open');
          modalAdminLogout.style.display = 'none';
          modalAdminLogout.style.pointerEvents = 'none';
        }
      });
    }

    // =========================================================================
    // FITUR SURVEI LOKASI & LIVE AJAX NOTIFICATIONS POLLING
    // =========================================================================
    window.allSurveysData = @json($allSurveys ?? []);
    window.allSurveysMap = {};
    (window.allSurveysData || []).forEach(function(s) {
      window.allSurveysMap[s.id] = s;
    });
    window.activeSurveyDetailId = null;

    // ─── Survey Management Filter & Handlers ───
    window.activeSurveyBranchFilter = 'all';
    window.activeSurveyStatusFilter = 'all';

    window.filterSurveyBranch = function(branch, btn) {
      window.activeSurveyBranchFilter = branch;
      if (btn && btn.parentElement) {
        btn.parentElement.querySelectorAll('.filter-pill').forEach(function(p) { p.classList.remove('active'); });
        btn.classList.add('active');
      }
      window.applySurveyFilters();
    };

    window.filterSurveyStatus = function(status, btn) {
      window.activeSurveyStatusFilter = status;
      if (btn && btn.parentElement) {
        btn.parentElement.querySelectorAll('.filter-pill').forEach(function(p) { p.classList.remove('active'); });
        btn.classList.add('active');
      }
      window.applySurveyFilters();
    };

    window.filterSurveyTable = function() {
      window.applySurveyFilters();
    };

    window.applySurveyFilters = function() {
      var query = (document.getElementById('surveySearchInput')?.value || '').toLowerCase().trim();
      var branch = window.activeSurveyBranchFilter || 'all';
      var status = window.activeSurveyStatusFilter || 'all';
      var rows = document.querySelectorAll('#surveysTableBody tr[id^="surveyRow_"]');
      var cards = document.querySelectorAll('#surveysMobileCardList .mobile-data-card');
      var visibleCount = 0;

      [rows, cards].forEach(function(list) {
        list.forEach(function(r) {
          var rBranch = r.getAttribute('data-branch') || '';
          var rStatus = r.getAttribute('data-status') || '';
          var rSearch = r.getAttribute('data-search') || '';

          var matchBranch = (branch === 'all') || (rBranch === branch);
          var matchStatus = (status === 'all') || (rStatus === status);
          var matchQuery = !query || rSearch.includes(query);

          if (matchBranch && matchStatus && matchQuery) {
            r.style.display = '';
            if (r.tagName === 'TR') visibleCount++;
          } else {
            r.style.display = 'none';
          }
        });
      });

      var emptyRow = document.getElementById('surveyEmptyStateRow');
      if (emptyRow) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
      }
    };

    window.applySurveyStatusLocally = function(surveyId, newStatus) {
      if (window.allSurveysMap && window.allSurveysMap[surveyId]) {
        window.allSurveysMap[surveyId].status = newStatus;
      }
      var row = document.getElementById('surveyRow_' + surveyId);
      if (row) {
        row.setAttribute('data-status', newStatus);
        var pill = row.querySelector('.survey-status-badge');
        if (pill) {
          if (newStatus === 'pending') {
            pill.className = 'status-pill survey-status-badge status-due';
            pill.textContent = 'Menunggu';
            pill.style.background = '';
            pill.style.color = '';
            pill.style.border = '';
          } else if (newStatus === 'confirmed') {
            pill.className = 'status-pill survey-status-badge';
            pill.textContent = 'Dikonfirmasi';
            pill.style.background = '#eff6ff';
            pill.style.color = '#1d4ed8';
            pill.style.border = '1px solid #bfdbfe';
          } else if (newStatus === 'completed') {
            pill.className = 'status-pill survey-status-badge status-occupied';
            pill.textContent = 'Selesai';
            pill.style.background = '';
            pill.style.color = '';
            pill.style.border = '';
          } else {
            pill.className = 'status-pill survey-status-badge status-repair';
            pill.textContent = 'Dibatalkan';
            pill.style.background = '';
            pill.style.color = '';
            pill.style.border = '';
          }
        }
        var select = row.querySelector('.custom-select');
        if (select) select.value = newStatus;
      }

      var card = document.getElementById('surveyMobileCard_' + surveyId);
      if (card) {
        card.setAttribute('data-status', newStatus);
        var cardPill = card.querySelector('.status-pill');
        if (cardPill) {
          if (newStatus === 'pending') {
            cardPill.className = 'status-pill status-due';
            cardPill.textContent = 'Menunggu';
            cardPill.style.background = '';
            cardPill.style.color = '';
            cardPill.style.border = '';
          } else if (newStatus === 'confirmed') {
            cardPill.className = 'status-pill';
            cardPill.textContent = 'Dikonfirmasi';
            cardPill.style.background = '#eff6ff';
            cardPill.style.color = '#1d4ed8';
            cardPill.style.border = '1px solid #bfdbfe';
          } else if (newStatus === 'completed') {
            cardPill.className = 'status-pill status-occupied';
            cardPill.textContent = 'Selesai';
            cardPill.style.background = '';
            cardPill.style.color = '';
            cardPill.style.border = '';
          } else {
            cardPill.className = 'status-pill status-repair';
            cardPill.textContent = 'Dibatalkan';
            cardPill.style.background = '';
            cardPill.style.color = '';
            cardPill.style.border = '';
          }
        }
        var cardSelect = card.querySelector('.custom-select');
        if (cardSelect) cardSelect.value = newStatus;
      }
      window.recalcSurveyKpis();
    };

    window.recalcSurveyKpis = function() {
      var rows = document.querySelectorAll('#surveysTableBody tr[id^="surveyRow_"]');
      var total = rows.length;
      var pending = 0;
      var confirmed = 0;
      var completed = 0;
      var cancelled = 0;

      rows.forEach(function(r) {
        var st = r.getAttribute('data-status');
        if (st === 'pending') pending++;
        else if (st === 'confirmed') confirmed++;
        else if (st === 'completed') completed++;
        else if (st === 'cancelled') cancelled++;
      });

      var elTotal = document.getElementById('kpiSurveyTotal');
      var elPending = document.getElementById('kpiSurveyPending');
      var elConfirmed = document.getElementById('kpiSurveyConfirmed');
      var elCompleted = document.getElementById('kpiSurveyCompleted');
      if (elTotal) elTotal.textContent = total;
      if (elPending) elPending.textContent = pending;
      if (elConfirmed) elConfirmed.textContent = confirmed;
      if (elCompleted) elCompleted.textContent = completed;

      var pillAll = document.getElementById('surveyPillAllCount');
      if (pillAll) pillAll.textContent = total;
      var pillPending = document.getElementById('surveyPillPendingCount');
      if (pillPending) pillPending.textContent = pending;

      var badgeNav = document.getElementById('badgePendingSurveys');
      if (badgeNav) {
        badgeNav.textContent = pending;
        badgeNav.style.display = pending > 0 ? '' : 'none';
      }
    };

    window.updateSurveyStatusAjax = function(surveyId, newStatus) {
      fetch('/api/surveys/' + surveyId + '/status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          window.applySurveyStatusLocally(surveyId, newStatus);
          if (typeof window.fetchLiveNotifications === 'function') window.fetchLiveNotifications();
        } else {
          alert(data.message || 'Gagal mengubah status survei.');
        }
      })
      .catch(function(err) {
        alert('Terjadi kesalahan koneksi saat memperbarui status.');
      });
    };

    window.deleteSurveyAjax = function(surveyId, candidateName) {
      if (!confirm('Apakah Anda yakin ingin menghapus permohonan survei dari ' + candidateName + '? Data yang dihapus tidak dapat dipulihkan.')) {
        return;
      }
      fetch('/api/surveys/' + surveyId + '/delete', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          var row = document.getElementById('surveyRow_' + surveyId);
          if (row) row.remove();
          if (window.allSurveysMap && window.allSurveysMap[surveyId]) {
            delete window.allSurveysMap[surveyId];
          }
          window.recalcSurveyKpis();
          window.applySurveyFilters();
          if (typeof window.fetchLiveNotifications === 'function') window.fetchLiveNotifications();
        } else {
          alert(data.message || 'Gagal menghapus data survei.');
        }
      })
      .catch(function(err) {
        alert('Terjadi kesalahan saat menghapus permohonan survei.');
      });
    };

    window.openSurveyWhatsAppChat = function(phone, name, branch, room, dateStr, timeStr) {
      if (!phone || phone === '-') {
        alert('Nomor WhatsApp calon anak kos tidak tersedia.');
        return;
      }
      var cleanPhone = phone.replace(/[^0-9]/g, '');
      if (cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.slice(1);
      var kosBrand = @json($setting->kos_name ?? 'Kos Mas Robi');
      var text = "Halo Kak " + name + ", kami dari Pengelola " + kosBrand + ".\n\n" +
                 "Terkait permohonan survei kamar " + (room || 'pilihan') + " di " + (branch || 'Kos kami') +
                 (dateStr ? (" pada tanggal " + dateStr) : "") +
                 (timeStr ? (" pukul " + timeStr) : "") +
                 ", kami konfirmasikan bahwa jadwal tersebut siap dan kami nantikan kunjungannya.\n\n" +
                 "Apakah ada hal lain yang ingin Kakak tanyakan sebelum datang?";
      window.open('https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(text), '_blank');
    };

    function openSurveyDetailModal(surveyId) {
      if (window.closeNotifDropdown) window.closeNotifDropdown();
      var survey = (window.allSurveysMap && window.allSurveysMap[surveyId]) ? window.allSurveysMap[surveyId] : (window.allSurveysData || []).find(function(s) { return s.id == surveyId; });
      if (!survey) return;

      window.activeSurveyDetailId = survey.id;
      document.getElementById('sdName').textContent = survey.name || '-';
      document.getElementById('sdJob').textContent = survey.job || 'Calon Penghuni';
      document.getElementById('sdPhone').textContent = survey.phone || '-';
      document.getElementById('sdBranch').textContent = survey.branch || 'Lokasi 1';
      document.getElementById('sdRoom').textContent = survey.room || 'Semua Tipe Kamar';
      document.getElementById('sdMethod').textContent = survey.survey_method || 'Datang Langsung';
      
      var dateStr = survey.survey_date ? (typeof survey.survey_date === 'string' ? survey.survey_date.split('T')[0] : survey.survey_date) : 'Segera';
      document.getElementById('sdDateTime').textContent = dateStr + ' (Pukul ' + (survey.survey_time || '14:00') + ')';
      document.getElementById('sdMessage').textContent = survey.message || 'Tidak ada catatan tambahan.';

      // Set chat WhatsApp link
      var btnWa = document.getElementById('btnChatSurveyWa');
      if (btnWa) {
        if (survey.phone && survey.phone !== '-') {
          var cleanPhone = survey.phone.replace(/[^0-9]/g, '');
          if (cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.slice(1);
          var kosBrand = @json($setting->kos_name ?? 'Kos Mas Robi');
          var replyMsg = "Halo Kak " + (survey.name || '') + ", kami dari Pengelola " + kosBrand + ". Terkait permohonan survei kamar " + (survey.room || '') + " pada " + dateStr + " pukul " + (survey.survey_time || '') + ", jadwal tersebut tersedia dan kami nantikan kunjungannya.";
          btnWa.href = "https://wa.me/" + cleanPhone + "?text=" + encodeURIComponent(replyMsg);
          btnWa.style.display = 'inline-flex';
        } else {
          btnWa.style.display = 'none';
        }
      }

      var modal = document.getElementById('modalSurveyDetail');
      if (modal) {
        modal.classList.add('open');
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
      }
    }
    window.openSurveyDetailModal = openSurveyDetailModal;

    function closeSurveyDetailModal() {
      var modal = document.getElementById('modalSurveyDetail');
      if (modal) {
        modal.classList.remove('open');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
      }
    }
    window.closeSurveyDetailModal = closeSurveyDetailModal;

    function markSurveyCompleted() {
      if (!window.activeSurveyDetailId) return;
      var surveyId = window.activeSurveyDetailId;
      var btn = document.getElementById('btnMarkSurveyDone');
      if (btn) btn.textContent = 'Memproses...';

      fetch('/api/surveys/' + surveyId + '/status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'completed' })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (btn) btn.textContent = 'Tandai Selesai';
        closeSurveyDetailModal();
        if (typeof window.applySurveyStatusLocally === 'function') {
          window.applySurveyStatusLocally(surveyId, 'completed');
        }
        fetchLiveNotifications();
      })
      .catch(function(err) {
        if (btn) btn.textContent = 'Tandai Selesai';
        console.warn('Gagal update status survei:', err);
      });
    }
    window.markSurveyCompleted = markSurveyCompleted;

    // ─── Tambahkan Jadwal Survei ke Kalender ───
    window.addToCalendar = function(surveyId) {
      if (!surveyId) return;
      var survey = (window.allSurveysMap && window.allSurveysMap[surveyId]) ? window.allSurveysMap[surveyId] : (window.allSurveysData || []).find(function(s) { return s.id == surveyId; });

      var clickedBtn = null;
      if (typeof event !== 'undefined' && event && event.currentTarget && event.currentTarget.tagName === 'BUTTON') {
        clickedBtn = event.currentTarget;
      } else {
        clickedBtn = document.getElementById('btnModalAddToCalendar');
      }
      var origBtnHtml = clickedBtn ? clickedBtn.innerHTML : '';
      if (clickedBtn) {
        clickedBtn.disabled = true;
        clickedBtn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> <span>Menyiapkan...</span>';
      }

      fetch('/api/surveys/' + surveyId + '/calendar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (clickedBtn) {
          clickedBtn.disabled = false;
          clickedBtn.innerHTML = origBtnHtml;
        }
        if (data && data.calendar_url) {
          window.open(data.calendar_url, '_blank');
        } else if (survey) {
          openGoogleCalendarDirectFallback(survey);
        }
      })
      .catch(function(err) {
        if (clickedBtn) {
          clickedBtn.disabled = false;
          clickedBtn.innerHTML = origBtnHtml;
        }
        console.warn('Gagal panggil API kalender, membuka fallback:', err);
        if (survey) openGoogleCalendarDirectFallback(survey);
      });
    };

    function openGoogleCalendarDirectFallback(survey) {
      if (!survey) return;
      var kosBrand = @json($setting->kos_name ?? 'Kos Mas Robi');
      var title = "Survei Kamar " + (survey.room || 'Unit') + " - " + (survey.name || 'Calon Penghuni') + " (" + kosBrand + ")";
      var dateStr = survey.survey_date ? (typeof survey.survey_date === 'string' ? survey.survey_date.split('T')[0] : survey.survey_date) : '';
      var timeStr = survey.survey_time || '14:00';
      
      var startDt = dateStr ? new Date(dateStr + 'T' + timeStr + ':00') : new Date();
      if (isNaN(startDt.getTime())) startDt = new Date();
      var endDt = new Date(startDt.getTime() + 60 * 60 * 1000);
      
      var toUtcIso = function(d) {
        return d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
      };
      
      var dates = toUtcIso(startDt) + '/' + toUtcIso(endDt);
      var details = "Jadwal Survei Lokasi Kamar Kos\n" +
                    "Nama: " + (survey.name || '-') + "\n" +
                    "No. WA: " + (survey.phone || '-') + "\n" +
                    "Cabang: " + (survey.branch || '-') + "\n" +
                    "Unit Kamar: " + (survey.room || '-') + "\n" +
                    "Metode: " + (survey.survey_method || 'Datang Langsung') + "\n" +
                    "Catatan: " + (survey.message || '-');
      var location = (survey.branch || 'Kos Mas Robi');
      var calUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" + encodeURIComponent(title) + "&dates=" + dates + "&details=" + encodeURIComponent(details) + "&location=" + encodeURIComponent(location);
      window.open(calUrl, '_blank');
    }

    // ─── Live AJAX Notifications Poller ───
    function fetchLiveNotifications() {
      fetch('/api/notifications/live', {
        headers: { 'Accept': 'application/json' }
      })
      .then(function(res) {
        if (!res.ok) {
          return fetch('/notifications/live', { headers: { 'Accept': 'application/json' } }).then(function(r) { return r.json(); });
        }
        return res.json();
      })
      .then(function(data) {
        if (!data || !data.success) return;

        // Update counts
        var total = data.totalCount || 0;
        var badge = document.getElementById('headerNotifBadge');
        var btnToggle = document.getElementById('btnToggleNotifications');
        if (total > 0) {
          if (!badge && btnToggle) {
            badge = document.createElement('span');
            badge.id = 'headerNotifBadge';
            badge.className = 'header-notif-badge';
            btnToggle.appendChild(badge);
          }
          if (badge) {
            badge.textContent = total > 9 ? '9+' : total;
            badge.style.display = 'flex';
          }
        } else if (badge) {
          badge.style.display = 'none';
        }

        var pillText = document.getElementById('notifPillText');
        var pillBadge = document.getElementById('notifPillBadge');
        if (pillText) {
          pillText.textContent = total > 0 ? (total + ' Perlu Tindakan') : '0 Baru';
        }
        if (pillBadge) {
          if (total > 0) {
            pillBadge.style.background = '';
            pillBadge.style.color = '';
          } else {
            pillBadge.style.background = '#dcfce7';
            pillBadge.style.color = '#15803d';
          }
        }

        if (document.getElementById('notifCountTotal')) document.getElementById('notifCountTotal').textContent = total;
        if (document.getElementById('notifCountSurvey')) document.getElementById('notifCountSurvey').textContent = data.counts?.surveys || 0;
        if (document.getElementById('notifCountTransfer')) document.getElementById('notifCountTransfer').textContent = data.counts?.transfers || 0;
        if (document.getElementById('notifCountComplaint')) document.getElementById('notifCountComplaint').textContent = data.counts?.complaints || 0;
        if (document.getElementById('notifCountBilling')) document.getElementById('notifCountBilling').textContent = data.counts?.billings || 0;

        // Update sidebar badges dynamically
        var badgePendingSurveys = document.getElementById('badgePendingSurveys');
        if (badgePendingSurveys) {
          var sPendingCount = data.counts?.surveys || 0;
          badgePendingSurveys.textContent = sPendingCount;
          badgePendingSurveys.style.display = sPendingCount > 0 ? '' : 'none';
        }

        var badgePendingRepairs = document.getElementById('badgePendingRepairs');
        if (badgePendingRepairs) {
          var rCount = data.counts?.complaints || 0;
          badgePendingRepairs.textContent = rCount;
          badgePendingRepairs.style.display = rCount > 0 ? '' : 'none';
        }

        var badgePendingTransfers = document.getElementById('badgePendingTransfers');
        if (badgePendingTransfers) {
          var tCount = data.counts?.transfers || 0;
          badgePendingTransfers.textContent = tCount;
          badgePendingTransfers.style.display = tCount > 0 ? '' : 'none';
        }

        var badgeDueBills = document.getElementById('badgeDueBills');
        if (badgeDueBills) {
          var bCount = data.counts?.billings || 0;
          badgeDueBills.textContent = bCount;
          badgeDueBills.style.display = bCount > 0 ? '' : 'none';
        }

        // Merge any new survey data returned into window.allSurveysMap
        if (Array.isArray(data.surveys)) {
          data.surveys.forEach(function(s) {
            if (window.allSurveysMap) window.allSurveysMap[s.id] = s;
          });
          window.allSurveysData = data.surveys;
        }

        // Render live notifications into list body
        renderLiveNotifList(data);
      })
      .catch(function(err) {
        console.warn('Gagal memuat notifikasi sistem:', err);
      });
    }
    window.fetchLiveNotifications = fetchLiveNotifications;

    function renderLiveNotifList(data) {
      var body = document.getElementById('notifListBody');
      if (!body) return;

      if (data.totalCount === 0) {
        body.innerHTML = '<div style="text-align: center; padding: 34px 18px; color: #64748b;">' +
          '<div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">' +
            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>' +
          '</div>' +
          '<div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; margin-bottom: 4px;">Semua Beres & Terkendali</div>' +
          '<p style="font-size: 0.78rem; color: #94a3b8; margin: 0;">Tidak ada survei lokasi baru, mutasi pending, keluhan, ataupun tagihan jatuh tempo saat ini.</p>' +
        '</div>';
        return;
      }

      var html = '';

      // 0. Survei Lokasi
      if (Array.isArray(data.surveys)) {
        data.surveys.forEach(function(sv) {
          html += '<div class="notif-item" data-cat="survey" data-id="' + sv.id + '" onclick="window.openSurveyDetailModal(' + sv.id + ')">' +
            '<div class="notif-item-icon survey">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' +
            '</div>' +
            '<div class="notif-item-content">' +
              '<div class="notif-item-header">' +
                '<span class="notif-item-title">Survei: ' + escapeHtml(sv.name) + '</span>' +
                '<span class="notif-item-time">' + (sv.time_ago || 'Baru') + '</span>' +
              '</div>' +
              '<p class="notif-item-desc">' +
                'Ingin survei <strong>' + escapeHtml(sv.room || 'Unit Kamar') + '</strong> (' + escapeHtml(sv.branch || '') + ') tgl <strong>' + escapeHtml(sv.survey_date || 'Segera') + '</strong> jam ' + escapeHtml(sv.survey_time || '-') + ' via ' + escapeHtml(sv.survey_method || 'Datang Langsung') + '.' +
              '</p>' +
              (sv.phone && sv.phone !== '-' ? ('<div style="margin-top: 4px; display: flex; align-items: center; gap: 8px;"><span style="font-size: 0.68rem; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 5px; font-weight: 700;">WA: ' + escapeHtml(sv.phone) + '</span><span style="font-size: 0.68rem; color: #7c3aed; font-weight: 600;">Klik untuk detail</span></div>') : '') +
            '</div>' +
          '</div>';
        });
      }

      // 1. Mutasi Kamar
      if (Array.isArray(data.transfers)) {
        data.transfers.forEach(function(tr) {
          html += '<div class="notif-item" data-cat="transfer" data-id="' + tr.id + '" onclick="window.handleNotifClick(\'tab-transfers\')">' +
            '<div class="notif-item-icon transfer">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>' +
            '</div>' +
            '<div class="notif-item-content">' +
              '<div class="notif-item-header">' +
                '<span class="notif-item-title">Pengajuan Pindah Kamar</span>' +
                '<span class="notif-item-time">' + (tr.time_ago || 'Baru') + '</span>' +
              '</div>' +
              '<p class="notif-item-desc"><strong>' + escapeHtml(tr.userName) + '</strong>: Kamar #' + escapeHtml(tr.currentRoom) + ' &rarr; #' + escapeHtml(tr.targetRoom) + ' (' + escapeHtml(tr.reason) + ')</p>' +
            '</div>' +
          '</div>';
        });
      }

      // 2. Keluhan & Servis
      if (Array.isArray(data.complaints)) {
        data.complaints.forEach(function(tk) {
          html += '<div class="notif-item" data-cat="complaint" data-id="' + tk.id + '" onclick="window.handleNotifClick(\'tab-maintenance\')">' +
            '<div class="notif-item-icon complaint">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>' +
            '</div>' +
            '<div class="notif-item-content">' +
              '<div class="notif-item-header">' +
                '<span class="notif-item-title">Keluhan Kamar #' + escapeHtml(tk.roomNumber) + '</span>' +
                '<span class="notif-item-time">' + (tk.time_ago || 'Aktif') + '</span>' +
              '</div>' +
              '<p class="notif-item-desc">' + escapeHtml(tk.description) + ' (Pelapor: ' + escapeHtml(tk.reportedBy) + ')</p>' +
            '</div>' +
          '</div>';
        });
      }

      // 3. Tagihan Jatuh Tempo
      if (Array.isArray(data.billings)) {
        data.billings.forEach(function(dr) {
          html += '<div class="notif-item" data-cat="billing" data-id="' + dr.id + '" onclick="window.handleNotifClick(\'tab-bills\')">' +
            '<div class="notif-item-icon billing">' +
              '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>' +
            '</div>' +
            '<div class="notif-item-content">' +
              '<div class="notif-item-header">' +
                '<span class="notif-item-title">Jatuh Tempo: Kamar #' + escapeHtml(dr.number) + '</span>' +
                '<span class="notif-item-time" style="color: #ef4444; font-weight: 700;">Tagihan</span>' +
              '</div>' +
              '<p class="notif-item-desc">Penghuni <strong>' + escapeHtml(dr.tenantName) + '</strong> memiliki tunggakan sewa (Rp ' + escapeHtml(dr.price) + '/bln).</p>' +
            '</div>' +
          '</div>';
        });
      }

      body.innerHTML = html;

      // Re-apply current active filter if any
      var activeFilterBtn = document.querySelector('.notif-filter-bar .notif-filter-btn.active');
      if (activeFilterBtn) {
        var cat = 'all';
        if (activeFilterBtn.textContent.indexOf('Survei') !== -1) cat = 'survey';
        else if (activeFilterBtn.textContent.indexOf('Mutasi') !== -1) cat = 'transfer';
        else if (activeFilterBtn.textContent.indexOf('Keluhan') !== -1) cat = 'complaint';
        else if (activeFilterBtn.textContent.indexOf('Tagihan') !== -1) cat = 'billing';
        if (cat !== 'all') {
          body.querySelectorAll('.notif-item').forEach(function(it) {
            it.style.display = (it.getAttribute('data-cat') === cat) ? 'flex' : 'none';
          });
        }
      }
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Start polling every 12 seconds
    setInterval(fetchLiveNotifications, 12000);

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

    // --- UBAH STATUS KELUHAN REACTIVE UPDATE ---
    window.handleMaintenanceStatusChange = async function(selectEl, ticketId) {
      var newStatus = selectEl.value;

      // Update adjacent status badge in desktop table row
      var row = selectEl.closest('tr');
      if (row) {
        var cell = row.querySelector('.maint-status-col') || (selectEl.closest('td') ? selectEl.closest('td').previousElementSibling : null);
        if (cell) {
          if (newStatus === 'done') {
            cell.innerHTML = '<span class="status-badge occupied">Selesai</span>';
          } else if (newStatus === 'progress') {
            cell.innerHTML = '<span class="status-badge due">Dalam Proses</span>';
          } else {
            cell.innerHTML = '<span class="status-badge repair">Menunggu</span>';
          }
        }
        row.setAttribute('data-status', newStatus);
      }

      // Update mobile card badge
      var mobileCard = selectEl.closest('.mobile-data-card') || document.getElementById('maintMobileCard_' + ticketId);
      if (mobileCard) {
        var cardBadgeWrap = mobileCard.querySelector('.maint-card-badge') || mobileCard.querySelector('.mobile-data-header .status-badge');
        if (cardBadgeWrap) {
          if (cardBadgeWrap.classList.contains('maint-card-badge')) {
            if (newStatus === 'done') {
              cardBadgeWrap.innerHTML = '<span class="status-badge occupied">Selesai</span>';
            } else if (newStatus === 'progress') {
              cardBadgeWrap.innerHTML = '<span class="status-badge due">Dalam Proses</span>';
            } else {
              cardBadgeWrap.innerHTML = '<span class="status-badge repair">Menunggu</span>';
            }
          } else {
            cardBadgeWrap.className = 'status-badge ' + (newStatus === 'done' ? 'occupied' : (newStatus === 'progress' ? 'due' : 'repair'));
            cardBadgeWrap.textContent = newStatus === 'done' ? 'Selesai' : (newStatus === 'progress' ? 'Dalam Proses' : 'Menunggu');
          }
        }
        mobileCard.setAttribute('data-status', newStatus);
      }

      // Sync any other select element for the same ticketId
      document.querySelectorAll('select').forEach(function(s) {
        if (s !== selectEl && s.getAttribute('onchange') && s.getAttribute('onchange').indexOf(ticketId) !== -1) {
          s.value = newStatus;
        }
      });

      // Optimistic update on notification dropdown item if marked done
      if (newStatus === 'done') {
        var notifItem = document.querySelector('#notifListBody .notif-item[data-cat="complaint"][data-id="' + ticketId + '"]');
        if (notifItem) notifItem.remove();
      }

      // API call to persist
      try {
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        var res = await fetch('/api/maintenance/' + ticketId + '/status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ status: newStatus })
        });
        var data = await res.json();
        var label = newStatus === 'done' ? 'Selesai' : (newStatus === 'progress' ? 'Dalam Proses' : 'Menunggu');
        if (window.showToast) {
          window.showToast('Status tiket keluhan berhasil diubah ke ' + label + '.', 'success');
        }
        if (typeof window.fetchLiveNotifications === 'function') {
          window.fetchLiveNotifications();
        }
      } catch (err) {
        console.warn('Gagal menyimpan status tiket ke server:', err);
      }
    };

    // --- MODAL KONFIRMASI PINDAH KAMAR (MODERN POPUP) ---
    window.openConfirmRoomTransferModal = function(idOrEl, status, tenantName, fromRoom, toRoom) {
      var id = idOrEl;
      if (idOrEl && (idOrEl.nodeType || (typeof idOrEl === 'object' && idOrEl.getAttribute))) {
        id = idOrEl.getAttribute('data-id');
        status = idOrEl.getAttribute('data-status');
        tenantName = idOrEl.getAttribute('data-tenant');
        fromRoom = idOrEl.getAttribute('data-from');
        toRoom = idOrEl.getAttribute('data-to');
      }

      var idInput = document.getElementById('confirmTransferId');
      if (idInput) idInput.value = id || '';
      var statusInput = document.getElementById('confirmTransferStatus');
      if (statusInput) statusInput.value = status || 'approved';
      var tenantEl = document.getElementById('confirmTransferTenant');
      if (tenantEl) tenantEl.textContent = tenantName || 'Penghuni';
      var fromRoomEl = document.getElementById('confirmTransferFromRoom');
      if (fromRoomEl) fromRoomEl.textContent = '#' + (fromRoom || '-');
      var toRoomEl = document.getElementById('confirmTransferToRoom');
      if (toRoomEl) toRoomEl.textContent = '#' + (toRoom || '-');

      var titleEl = document.getElementById('confirmTransferTitle');
      var subEl = document.getElementById('confirmTransferSubtitle');
      var headerEl = document.getElementById('confirmTransferHeader');
      var bannerEl = document.getElementById('confirmTransferActionBanner');
      var btnSubmit = document.getElementById('btnSubmitConfirmTransfer');
      var noteInput = document.getElementById('confirmTransferNote');

      if (status === 'approved') {
        if (titleEl) titleEl.textContent = 'Setujui Permohonan Pindah Kamar';
        if (subEl) subEl.textContent = 'Penghuni akan dipindahkan ke kamar baru yang dipilih';
        if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #065f46 0%, #059669 100%)';
        if (bannerEl) {
          bannerEl.style.background = '#dcfce7';
          bannerEl.style.color = '#065f46';
          bannerEl.style.border = '1px solid #86efac';
          bannerEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Anda akan menyetujui mutasi kamar ini. Sistem akan mengalihkan status sewa ke unit tujuan.';
        }
        if (btnSubmit) {
          btnSubmit.textContent = 'Ya, Setujui Permohonan';
          btnSubmit.style.background = '#059669';
        }
        if (noteInput) noteInput.value = 'Pengajuan disetujui, silakan koordinasi kunci kamar baru.';
      } else {
        if (titleEl) titleEl.textContent = 'Tolak Permohonan Pindah Kamar';
        if (subEl) subEl.textContent = 'Permohonan mutasi tidak dapat disetujui saat ini';
        if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #991b1b 0%, #dc2626 100%)';
        if (bannerEl) {
          bannerEl.style.background = '#fee2e2';
          bannerEl.style.color = '#991b1b';
          bannerEl.style.border = '1px solid #fca5a5';
          bannerEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Permohonan mutasi kamar akan ditolak. Penghuni tetap menempati kamar asal.';
        }
        if (btnSubmit) {
          btnSubmit.textContent = 'Ya, Tolak Permohonan';
          btnSubmit.style.background = '#dc2626';
        }
        if (noteInput) noteInput.value = 'Mohon maaf, kamar yang diminta belum tersedia saat ini.';
      }

      window.openModal('modalConfirmRoomTransfer');
    };

    window.submitConfirmRoomTransfer = async function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var id = document.getElementById('confirmTransferId')?.value;
      var status = document.getElementById('confirmTransferStatus')?.value;
      var note = document.getElementById('confirmTransferNote')?.value;
      var btn = document.getElementById('btnSubmitConfirmTransfer');

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
      }

      try {
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        var res = await fetch('/api/room-transfers/' + id + '/status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            status: status,
            admin_note: note
          })
        });
        var data = await res.json();
        if (data.success) {
          window.closeModal('modalConfirmRoomTransfer');
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Status pindah kamar berhasil diperbarui!', 'success', 'Status Berhasil Diperbarui', function() {
              window.location.reload();
            });
          } else {
            alert(data.message || 'Status berhasil diperbarui!');
            window.location.reload();
          }
        } else {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal memperbarui status.', 'error', 'Gagal');
          } else {
            alert(data.message || 'Gagal memperbarui status.');
          }
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan saat memproses status pindah kamar.');
      } finally {
        if (btn) {
          btn.disabled = false;
        }
      }
    };

    function handleTransferAction(id, status) {
      window.openConfirmRoomTransferModal(id, status);
    }
    window.handleTransferAction = handleTransferAction;

    // --- AUTOMATIC FLOOR FILTERS & MANAGEMENT DYNAMIC GENERATION ---
    window.addNewFloorDynamically = function(floorVal) {
      if (!floorVal) return;
      var fNum = String(floorVal).trim();

      // 1. Tambah Pill Filter di Tab Daftar Kamar
      var roomTabFilters = document.getElementById('roomTabFilters');
      if (roomTabFilters && !roomTabFilters.querySelector('.filter-pill[data-floor="' + fNum + '"]')) {
        var pill = document.createElement('button');
        pill.type = 'button';
        pill.className = 'filter-pill';
        pill.setAttribute('data-floor', fNum);
        pill.textContent = 'Lantai ' + fNum;
        pill.onclick = function() { window.filterRoomMasterFloor(fNum, this); };
        roomTabFilters.appendChild(pill);
      }

      // 2. Tambah Pill Filter di Dashboard Denah Kamar
      var dashFloorFilters = document.getElementById('dashboardFloorFilters');
      if (dashFloorFilters && !dashFloorFilters.querySelector('.filter-pill[data-dfloor="' + fNum + '"]')) {
        var dPill = document.createElement('button');
        dPill.type = 'button';
        dPill.className = 'filter-pill';
        dPill.setAttribute('data-dfloor', fNum);
        dPill.textContent = 'Lantai ' + fNum;
        dPill.onclick = function() { window.filterDashboardFloor(fNum, this); };
        dashFloorFilters.appendChild(dPill);
      }

      // 3. Tambah Pilihan di Dropdown Form Kamar (roomFloorInput)
      var roomFloorSelect = document.getElementById('roomFloorInput');
      if (roomFloorSelect && !roomFloorSelect.querySelector('option[value="' + fNum + '"]')) {
        var opt = document.createElement('option');
        opt.value = fNum;
        opt.textContent = 'Lantai ' + fNum;
        roomFloorSelect.appendChild(opt);
      }

      // 4. Tambah Pilihan di Dropdown Modal Hapus Lantai (deleteFloorSelect)
      var delFloorSelect = document.getElementById('deleteFloorSelect');
      if (delFloorSelect && !delFloorSelect.querySelector('option[value="' + fNum + '"]')) {
        var dOpt = document.createElement('option');
        dOpt.value = fNum;
        dOpt.textContent = 'Lantai ' + fNum;
        delFloorSelect.appendChild(dOpt);
      }

      // 5. Tambah Section Denah Baru di Tab Dashboard jika belum ada
      var dashSecId = 'dashboardFloor' + fNum + 'Section';
      if (!document.getElementById(dashSecId)) {
        var allSecs = document.querySelectorAll('#tab-dashboard .floor-section');
        var lastSec = allSecs.length > 0 ? allSecs[allSecs.length - 1] : null;
        var newSec = document.createElement('div');
        newSec.className = 'floor-section';
        newSec.id = dashSecId;
        newSec.innerHTML = '<div class="floor-badge" onclick="window.filterDashboardFloor(\'' + fNum + '\')" style="cursor: pointer; user-select: none;" title="Klik untuk fokus Lantai ' + fNum + '">Lantai ' + fNum + '</div>' +
          '<div class="rooms-grid" id="floor' + fNum + 'Grid">' +
          '  <div style="grid-column: 1 / -1; padding: 22px; text-align: center; color: var(--text-muted); background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 14px; font-size: 0.84rem;">' +
          '    Belum ada unit kamar di Lantai ' + fNum + '. Silakan klik <strong>+ Tambah Kamar Baru</strong> untuk menempatkan kamar di lantai ini.' +
          '  </div>' +
          '</div>';
        if (lastSec && lastSec.parentNode) {
          lastSec.parentNode.insertBefore(newSec, lastSec.nextSibling);
        }
      }

      // 6. Switch filter aktif ke lantai yang baru ditambahkan
      if (typeof window.filterDashboardFloor === 'function') {
        window.filterDashboardFloor(fNum);
      }
      if (typeof window.filterRoomMasterFloor === 'function') {
        window.filterRoomMasterFloor(fNum);
      }
    };

    // --- MODAL TAMBAH LANTAI BANGUNAN KOS ---
    window.openAddFloorModal = function() {
      window.openModal('modalAddFloor');
    };

    window.openAddRoomWithFloor = function(floorNum) {
      var floorSelect = document.getElementById('roomFloorInput');
      if (floorSelect) floorSelect.value = String(floorNum);
      if (window.openRoomFormModal) {
        window.openRoomFormModal();
      } else {
        window.openModal('modalAddRoom');
      }
    };

    window.submitAddFloor = function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var floorVal = document.getElementById('addFloorNumber')?.value || '3';
      var floorLabel = document.getElementById('addFloorLabel')?.value || ('Lantai ' + floorVal);
      window.closeModal('modalAddFloor');

      // Generate dynamic filters & elements
      window.addNewFloorDynamically(floorVal);

      if (window.showModernAlert) {
        window.showModernAlert(floorLabel + ' berhasil diaktifkan pada sistem kos! Filter lantai otomatis dibuat dan Anda dapat segera menambahkan unit kamar pada lantai ini.', 'success', 'Lantai Berhasil Diaktifkan', function() {
          window.openAddRoomWithFloor(floorVal);
        });
      } else {
        alert(floorLabel + ' berhasil diaktifkan!');
        window.openAddRoomWithFloor(floorVal);
      }
    };

    // --- FITUR HAPUS LANTAI DENGAN POPUP PILIH & KONFIRMASI ULANG ---
    window.openDeleteFloorModal = function() {
      var delSel = document.getElementById('deleteFloorSelect');
      if (delSel) {
        var currentFloors = new Set();
        document.querySelectorAll('#roomTabFilters .filter-pill[data-floor]').forEach(function(p) {
          var fl = p.getAttribute('data-floor');
          if (fl && fl !== 'all') currentFloors.add(parseInt(fl));
        });
        document.querySelectorAll('#roomsMasterTableBody .room-master-row[data-floor]').forEach(function(r) {
          var fl = r.getAttribute('data-floor');
          if (fl) currentFloors.add(parseInt(fl));
        });
        var sortedFloors = Array.from(currentFloors).sort(function(a,b) { return a - b; });
        if (sortedFloors.length > 0) {
          delSel.innerHTML = '';
          sortedFloors.forEach(function(fl) {
            var opt = document.createElement('option');
            opt.value = String(fl);
            opt.textContent = 'Lantai ' + fl;
            delSel.appendChild(opt);
          });
        }
      }
      window.updateDeleteFloorInfo();
      window.openModal('modalDeleteFloor');
    };

    window.updateDeleteFloorInfo = function() {
      var sel = document.getElementById('deleteFloorSelect');
      var floorVal = sel ? sel.value : '1';
      var rows = document.querySelectorAll('#roomsMasterTableBody .room-master-row[data-floor="' + floorVal + '"]');
      var roomCount = rows.length;
      var occupiedCount = 0;
      rows.forEach(function(r) {
        var text = (r.textContent || '').toLowerCase();
        if (text.includes('terisi') || text.includes('jatuh tempo')) {
          occupiedCount++;
        }
      });

      var infoBox = document.getElementById('deleteFloorInfoBox');
      if (infoBox) {
        var html = '<strong>Informasi Lantai ' + floorVal + ':</strong> ';
        html += 'Terdapat <strong>' + roomCount + ' unit kamar</strong> di lantai ini.';
        if (occupiedCount > 0) {
          html += '<div style="margin-top:6px;color:#dc2626;font-weight:700;">Perhatian: Ada ' + occupiedCount + ' kamar yang masih berpenghuni. Anda harus mengosongkan/checkout penghuni terlebih dahulu sebelum menghapus lantai.</div>';
        } else {
          html += '<div style="margin-top:6px;color:#059669;font-weight:600;">Lantai siap dihapus jika Anda telah yakin.</div>';
        }
        infoBox.innerHTML = html;
      }
    };

    window.submitDeleteFloor = async function(e) {
      if (e && e.preventDefault) e.preventDefault();
      var sel = document.getElementById('deleteFloorSelect');
      var floorVal = sel ? sel.value : '';
      if (!floorVal) return;

      var rows = document.querySelectorAll('#roomsMasterTableBody .room-master-row[data-floor="' + floorVal + '"]');
      var roomCount = rows.length;

      // Tutup modal pilihan lantai
      window.closeModal('modalDeleteFloor');

      // Popup konfirmasi ulang kedua (Double Confirmation)
      var confirmed = await window.showModernConfirm(
        'PERINGATAN KONFIRMASI ULANG:\n\nApakah Anda benar-benar yakin ingin menghapus Lantai ' + floorVal + ' beserta seluruh ' + roomCount + ' unit kamar di dalamnya?\n\nTindakan ini permanen dan tidak dapat dibatalkan.',
        {
          title: 'Konfirmasi Ulang Hapus Lantai ' + floorVal,
          okText: 'Ya, Hapus Lantai Sekarang',
          cancelText: 'Batalkan',
          danger: true
        }
      );
      if (!confirmed) return;

      try {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var res = await fetch('/floors/' + encodeURIComponent(floorVal) + '/delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });
        var data = await res.json();
        if (!res.ok || !data.success) {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal menghapus lantai.', 'error', 'Hapus Lantai Ditolak');
          } else {
            alert(data.message || 'Gagal menghapus lantai.');
          }
          return;
        }

        // Hapus elemen kamar lantai tersebut dari DOM
        document.querySelectorAll('#roomsMasterTableBody .room-master-row[data-floor="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        document.querySelectorAll('#roomsMobileCardList .mobile-data-card[data-floor="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        document.querySelectorAll('#tab-dashboard .room-card[data-floor="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        var floorSection = document.getElementById('dashboardFloor' + floorVal + 'Section');
        if (floorSection) floorSection.remove();

        // Hapus pill filter lantai tersebut
        document.querySelectorAll('#roomTabFilters .filter-pill[data-floor="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        document.querySelectorAll('#dashboardFloorFilters .filter-pill[data-dfloor="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        document.querySelectorAll('#roomFloorInput option[value="' + floorVal + '"]').forEach(function(el) { el.remove(); });
        document.querySelectorAll('#deleteFloorSelect option[value="' + floorVal + '"]').forEach(function(el) { el.remove(); });

        // Reset filter ke Semua Lantai
        if (window.filterRoomMasterFloor) window.filterRoomMasterFloor('all');
        if (window.filterDashboardFloor) window.filterDashboardFloor('all');

        // Update appData jika aktif
        if (window.appData && Array.isArray(window.appData.rooms)) {
          window.appData.rooms = window.appData.rooms.filter(function(r) { return String(r.floor) !== String(floorVal); });
        }

        if (window.showModernAlert) {
          window.showModernAlert(data.message || ('Lantai ' + floorVal + ' berhasil dihapus dari sistem kos.'), 'success', 'Lantai Berhasil Dihapus');
        } else {
          alert(data.message || ('Lantai ' + floorVal + ' berhasil dihapus.'));
        }
      } catch (err) {
        console.error(err);
        if (window.showModernAlert) {
          window.showModernAlert('Terjadi kesalahan jaringan saat menghapus lantai.', 'error', 'Gangguan Koneksi');
        }
      }
    };

    // --- FITUR HAPUS SATU UNIT KAMAR DENGAN KONFIRMASI ---
    window.confirmDeleteRoom = async function(id, roomNumber) {
      if (!roomNumber) return;

      // Cek apakah kamar masih terisi
      var row = document.querySelector('#roomsMasterTableBody .room-master-row[data-number="' + String(roomNumber).toLowerCase() + '"]');
      var rowText = (row ? row.textContent : '').toLowerCase();
      var isOccupied = rowText.includes('terisi') || rowText.includes('jatuh tempo');

      if (isOccupied) {
        if (window.showModernAlert) {
          window.showModernAlert('Kamar #' + roomNumber + ' saat ini masih berpenghuni aktif. Silakan lakukan proses checkout penghuni terlebih dahulu sebelum menghapus unit kamar ini.', 'warning', 'Kamar Masih Dihuni');
        } else {
          alert('Kamar #' + roomNumber + ' saat ini masih berpenghuni aktif.');
        }
        return;
      }

      var confirmed = await window.showModernConfirm(
        'Apakah Anda yakin ingin menghapus Kamar #' + roomNumber + ' dari daftar kamar kos?\n\nUnit kamar ini akan dihapus permanen dari sistem.',
        {
          title: 'Hapus Unit Kamar #' + roomNumber,
          okText: 'Ya, Hapus Kamar',
          cancelText: 'Batalkan',
          danger: true
        }
      );
      if (!confirmed) return;

      try {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var targetId = id || roomNumber;
        var res = await fetch('/rooms/' + encodeURIComponent(targetId) + '/delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });
        var data = await res.json();
        if (!res.ok || !data.success) {
          if (window.showModernAlert) {
            window.showModernAlert(data.message || 'Gagal menghapus unit kamar.', 'error', 'Gagal Menghapus');
          } else {
            alert(data.message || 'Gagal menghapus unit kamar.');
          }
          return;
        }

        // Hapus elemen dari DOM
        if (row) row.remove();
        document.querySelectorAll('#roomsMobileCardList .mobile-data-card').forEach(function(card) {
          var s = card.getAttribute('data-search') || '';
          if (s.includes('kamar #' + String(roomNumber).toLowerCase()) || s.startsWith(String(roomNumber).toLowerCase() + ' ')) {
            card.remove();
          }
        });
        var dashCard = document.querySelector('#tab-dashboard .room-card[data-room-id="' + roomNumber + '"]');
        if (dashCard) dashCard.remove();

        // Update appData jika aktif
        if (window.appData && Array.isArray(window.appData.rooms)) {
          window.appData.rooms = window.appData.rooms.filter(function(r) {
            return String(r.number) !== String(roomNumber) && String(r.id) !== String(targetId);
          });
        }

        if (window.showModernAlert) {
          window.showModernAlert(data.message || ('Kamar #' + roomNumber + ' berhasil dihapus.'), 'success', 'Kamar Berhasil Dihapus');
        } else {
          alert(data.message || ('Kamar #' + roomNumber + ' berhasil dihapus.'));
        }
      } catch (err) {
        console.error(err);
        if (window.showModernAlert) {
          window.showModernAlert('Terjadi kesalahan jaringan saat menghapus unit kamar.', 'error', 'Gangguan Koneksi');
        }
      }
    };

    // Filter Table Pindah Kamar (2 Cabang & Lintas Cabang)
    var currentTransferStatus = 'all';
    var currentTransferLocation = 'all';

    function applyTransferFilters() {
      var inputEl = document.getElementById('transferSearchInput');
      var input = inputEl ? inputEl.value.toLowerCase() : '';
      var rows = document.querySelectorAll('#transfersTableBody tr[data-status]');
      var cards = document.querySelectorAll('#transfersMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(r) {
          var searchData = (r.getAttribute('data-search') || r.textContent || '').toLowerCase();
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
    window.filterTransferStatus = filterTransferStatus;
    window.filterTransferTable = filterTransferTable;

    // --- DASHBOARD FILTERS (DENAH KAMAR, LANTAI 1 & 2, CABANG & STATUS) ---
    function applyDashboardFilters() {
      if (typeof window.applyDashboardFilters === 'function') {
        window.applyDashboardFilters();
      }
    }

    function filterDashboardStatus(status, btn) {
      if (typeof window.filterDashboardStatus === 'function') {
        window.filterDashboardStatus(status, btn);
      }
    }

    function filterDashboardFloor(floor, btn) {
      if (typeof window.filterDashboardFloor === 'function') {
        window.filterDashboardFloor(floor, btn);
      }
    }

    function filterDashboardLocation(loc, btn) {
      if (typeof window.filterDashboardLocation === 'function') {
        window.filterDashboardLocation(loc, btn);
      }
    }

    // --- DAFTAR KAMAR MASTER FILTERS (LANTAI, CABANG, STATUS KAMAR KOSONG/TERISI) ---
    var currentRoomFloor = 'all';
    var currentRoomLocation = 'all';
    var currentRoomStatus = 'all';

    function applyRoomMasterFilters() {
      var inputEl = document.getElementById('roomSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#roomsMasterTableBody .room-master-row, #roomsMasterTableBody tr');
      var cards = document.querySelectorAll('#roomsMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          if (el.querySelector('td[colspan]')) return;
          var s = ((el.getAttribute('data-search') || '') + ' ' + (el.textContent || '')).toLowerCase();
          var fl = String(el.getAttribute('data-floor') || '1');
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var st = el.getAttribute('data-status') || 'available';

          var matchSearch = !q || s.includes(q);
          var matchFloor = (currentRoomFloor === 'all' || fl === currentRoomFloor);
          var matchLoc = (currentRoomLocation === 'all' || loc === currentRoomLocation);
          var matchStatus = (currentRoomStatus === 'all' || st === currentRoomStatus);

          el.style.display = (matchSearch && matchFloor && matchLoc && matchStatus) ? '' : 'none';
        });
      });
    }

    function filterRoomMasterStatus(status, btn) {
      currentRoomStatus = status;
      window.currentRoomStatus = status;
      document.querySelectorAll('#roomStatusMasterFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', currentRoomFloor, currentRoomLocation, currentRoomStatus);
      }
    }
    window.filterRoomMasterStatus = filterRoomMasterStatus;

    function filterRoomMasterFloor(floor, btn) {
      currentRoomFloor = String(floor);
      window.currentRoomFloor = String(floor);
      document.querySelectorAll('#roomTabFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', currentRoomFloor, currentRoomLocation, currentRoomStatus);
      }
    }
    window.filterRoomMasterFloor = filterRoomMasterFloor;

    function filterRoomMasterLocation(loc, btn) {
      currentRoomLocation = loc;
      window.currentRoomLocation = loc;
      document.querySelectorAll('#roomLocationFilters .loc-filter-btn').forEach(function(p) {
        p.classList.remove('active');
        p.style.background = '#ffffff';
        p.style.color = p.getAttribute('data-location') === 'Lokasi 2' ? '#0d9488' : '#0a5661';
        p.style.boxShadow = 'none';
      });
      if (btn) {
        btn.classList.add('active');
        btn.style.background = '#0a5661';
        btn.style.color = '#ffffff';
        btn.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
      }
      applyRoomMasterFilters();
      if (typeof window.renderRoomsMasterTable === 'function') {
        var inputEl = document.getElementById('roomSearchInput');
        window.renderRoomsMasterTable(inputEl ? inputEl.value : '', currentRoomFloor, currentRoomLocation, currentRoomStatus);
      }
    }
    window.filterRoomMasterLocation = filterRoomMasterLocation;

    // --- DATA PENGHUNI FILTERS (AKTIF MENGHUNI, JATUH TEMPO, 2 CABANG) ---
    var currentTenantLoc = 'all';
    var currentTenantStatus = 'all';

    function applyTenantFilters() {
      var inputEl = document.getElementById('tenantSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#tenantsTableBody .tenant-row, #tenantsTableBody tr');
      var cards = document.querySelectorAll('#tenantsMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          if (el.querySelector('td[colspan]')) return;
          var s = ((el.getAttribute('data-search') || '') + ' ' + (el.textContent || '')).toLowerCase();
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var status = el.getAttribute('data-status') || 'active';

          var matchSearch = !q || s.includes(q);
          var matchLoc = (currentTenantLoc === 'all' || loc === currentTenantLoc);
          var matchStatus = (currentTenantStatus === 'all' || status === currentTenantStatus);

          el.style.display = (matchSearch && (q ? true : (matchLoc && matchStatus))) ? '' : 'none';
        });
      });
    }

    function filterTenantLocation(loc, btn) {
      currentTenantLoc = loc;
      document.querySelectorAll('#tenantLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyTenantFilters();
    }
    window.filterTenantLocation = filterTenantLocation;

    function filterTenantStatus(status, btn) {
      currentTenantStatus = status;
      document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyTenantFilters();
    }
    window.filterTenantStatus = filterTenantStatus;

    function filterTenantTable() {
      applyTenantFilters();
    }
    window.filterTenantTable = filterTenantTable;

    // --- KELUHAN & SERVIS FILTERS (MENUNGGU, PROSES, SELESAI, 2 CABANG) ---
    var currentMaintLoc = 'all';
    var currentMaintStatus = 'all';

    function applyMaintenanceFilters() {
      var inputEl = document.getElementById('maintenanceSearchInput');
      var q = inputEl ? inputEl.value.toLowerCase().trim() : '';

      var rows = document.querySelectorAll('#maintenanceTableBody .maintenance-row');
      var cards = document.querySelectorAll('#maintenanceMobileCardList .mobile-data-card');

      [rows, cards].forEach(function(list) {
        list.forEach(function(el) {
          var s = el.getAttribute('data-search') || '';
          var loc = el.getAttribute('data-location') || 'Lokasi 1';
          var status = el.getAttribute('data-status') || '';

          var matchSearch = !q || s.includes(q);
          var matchLoc = (currentMaintLoc === 'all' || loc === currentMaintLoc);
          var matchStatus = (currentMaintStatus === 'all' || status === currentMaintStatus);

          el.style.display = (matchSearch && matchLoc && matchStatus) ? '' : 'none';
        });
      });
    }

    function filterMaintenanceLocation(loc, btn) {
      currentMaintLoc = loc;
      document.querySelectorAll('#maintenanceLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyMaintenanceFilters();
    }
    window.filterMaintenanceLocation = filterMaintenanceLocation;

    function filterMaintenanceStatus(status, btn) {
      currentMaintStatus = status;
      document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) btn.classList.add('active');
      applyMaintenanceFilters();
    }
    window.filterMaintenanceStatus = filterMaintenanceStatus;

    function filterMaintenanceTable() {
      applyMaintenanceFilters();
    }
    window.filterMaintenanceTable = filterMaintenanceTable;

    // --- PUSAT PENGATURAN SUB-TABS NAVIGATION ---
    function switchSettingsSubtab(subtabId, btn) {
      var subTabBtns = document.querySelectorAll('#settingsSubTabNav .btn-settings-subtab');
      var subTabPanes = document.querySelectorAll('.settings-subtab-pane');

      subTabBtns.forEach(function(b) {
        b.classList.remove('active');
        b.style.background = '';
        b.style.color = '';
      });

      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#settingsSubTabNav .btn-settings-subtab[data-subtab="' + subtabId + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      subTabPanes.forEach(function(pane) {
        if (pane.id === subtabId) {
          pane.classList.add('active');
          pane.style.display = 'block';
        } else {
          pane.classList.remove('active');
          pane.style.display = 'none';
        }
      });

      if (typeof window.updateSettingsSubChip === 'function') {
        window.updateSettingsSubChip('tab-settings');
      }
    }
    window.switchSettingsSubtab = switchSettingsSubtab;

    // --- LAPORAN KEUANGAN & BUKU KAS FILTERS ---
    var currentFinLoc = 'all';
    var currentFinType = 'all';

    function filterFinanceBranch(loc, btn) {
      currentFinLoc = loc;
      document.querySelectorAll('#financeLocationFilters .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#financeLocationFilters .filter-pill[data-floc="' + loc + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      if (typeof window.renderFinancesTable === 'function') {
        var searchEl = document.getElementById('financeSearchInput');
        window.renderFinancesTable(searchEl ? searchEl.value : '', currentFinType, currentFinLoc);
      }
    }
    window.filterFinanceBranch = filterFinanceBranch;

    function filterFinanceType(type, btn) {
      currentFinType = type;
      document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(function(p) { p.classList.remove('active'); });
      if (btn) {
        btn.classList.add('active');
      } else {
        var matchBtn = document.querySelector('#financeTypeFilter .filter-pill[data-ftype="' + type + '"]');
        if (matchBtn) matchBtn.classList.add('active');
      }

      if (typeof window.renderFinancesTable === 'function') {
        var searchEl = document.getElementById('financeSearchInput');
        window.renderFinancesTable(searchEl ? searchEl.value : '', currentFinType, currentFinLoc);
      }
    }
    window.filterFinanceType = filterFinanceType;


    // =========================================================================
    // 7.2 INTEGRASI GMAIL SMTP & NOTIFIKASI SISTEM
    // =========================================================================
    window.toggleGmailPasswordVisibility = function() {
      var input = document.getElementById('gmail_input_password');
      var icon = document.getElementById('iconToggleGmailPwd');
      if (!input) return;
      var isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      if (icon) {
        if (isPwd) {
          icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
          icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
      }
    };

    window.saveEmailSettingsAjax = function(e) {
      if (e) e.preventDefault();
      var btn = document.getElementById('btnSaveEmailSettings');
      var alertBox = document.getElementById('gmailSettingsAlert');
      var username = document.getElementById('gmail_input_username') ? document.getElementById('gmail_input_username').value.trim() : '';
      var password = document.getElementById('gmail_input_password') ? document.getElementById('gmail_input_password').value : '';
      var fromName = document.getElementById('gmail_input_from_name') ? document.getElementById('gmail_input_from_name').value.trim() : '';
      var isEnabled = document.getElementById('gmail_input_is_enabled') && document.getElementById('gmail_input_is_enabled').checked ? 1 : 0;

      if (!username) {
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.background = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.textContent = 'Alamat email Gmail pengirim wajib diisi.';
        }
        return;
      }

      var prevBtnHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>Menyimpan...</span>';
      }

      fetch('{{ route("api.settings.email") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          mail_username: username,
          mail_password: password || null,
          mail_from_name: fromName,
          is_enabled: isEnabled
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = prevBtnHtml;
        }
        if (alertBox) {
          alertBox.style.display = 'block';
          if (data.success) {
            alertBox.style.background = '#f0fdfa';
            alertBox.style.color = '#0f766e';
            alertBox.style.border = '1px solid #99f6e4';
            alertBox.textContent = data.message || 'Pengaturan Gmail berhasil disimpan.';

            var badgeText = document.getElementById('gmailStatusTextBadge');
            if (badgeText) {
              badgeText.textContent = 'Terhubung & Aktif';
              badgeText.style.color = '#059669';
            }
          } else {
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#dc2626';
            alertBox.style.border = '1px solid #fecaca';
            alertBox.textContent = data.message || 'Gagal menyimpan pengaturan Gmail.';
          }
        }
      })
      .catch(function(err) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = prevBtnHtml;
        }
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.background = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.textContent = 'Terjadi kesalahan jaringan saat menyimpan konfigurasi.';
        }
      });
    };

    window.sendTestEmailAjax = function() {
      var btn = document.getElementById('btnRunEmailTest');
      var alertBox = document.getElementById('gmailTestAlert');
      var targetEmail = document.getElementById('gmail_test_target_email') ? document.getElementById('gmail_test_target_email').value.trim() : '';
      var username = document.getElementById('gmail_input_username') ? document.getElementById('gmail_input_username').value.trim() : '';
      var password = document.getElementById('gmail_input_password') ? document.getElementById('gmail_input_password').value : '';

      if (!targetEmail) {
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.background = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.textContent = 'Masukkan alamat email tujuan uji coba terlebih dahulu.';
        }
        return;
      }

      var prevBtnHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>Mengirim email uji coba...</span>';
      }
      if (alertBox) {
        alertBox.style.display = 'block';
        alertBox.style.background = '#f0fdf4';
        alertBox.style.color = '#15803d';
        alertBox.style.border = '1px solid #bbf7d0';
        alertBox.textContent = 'Sedang menghubungi server Gmail SMTP (smtp.gmail.com:587) dan mengirim email verifikasi...';
      }

      fetch('{{ route("api.settings.email_test") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          target_email: targetEmail,
          mail_username: username || null,
          mail_password: password || null
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = prevBtnHtml;
        }
        if (alertBox) {
          alertBox.style.display = 'block';
          if (data.success) {
            alertBox.style.background = '#f0fdfa';
            alertBox.style.color = '#0f766e';
            alertBox.style.border = '1px solid #99f6e4';
            alertBox.innerHTML = '<strong>Sukses!</strong> ' + (data.message || 'Email uji coba berhasil dikirim.');
          } else {
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#dc2626';
            alertBox.style.border = '1px solid #fecaca';
            var errorHtml = '<strong>Gagal:</strong> ' + (data.message || 'Koneksi ke server Gmail gagal.');
            if (data.hint) {
              errorHtml += '<div style="margin-top: 6px; font-size: 0.76rem; color: #7f1d1d; line-height: 1.45;">' + data.hint + '</div>';
            }
            alertBox.innerHTML = errorHtml;
          }
        }
      })
      .catch(function(err) {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = prevBtnHtml;
        }
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.background = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.textContent = 'Terjadi kesalahan jaringan atau waktu koneksi habis.';
        }
      });
    };

    // =========================================================================
    // KURVA PERTUMBUHAN PENDAPATAN & ARUS KAS (CANVAS SPLINE CURVE INTERACTIVE)
    // =========================================================================
    var chartMonths = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    window.chartIncomeFull = {!! json_encode($chartIncome ?? array_fill(0, 12, 0)) !!};
    window.chartExpenseFull = {!! json_encode($chartExpense ?? array_fill(0, 12, 0)) !!};
    window.chartNetFull = {!! json_encode($chartNet ?? array_fill(0, 12, 0)) !!};

    var currentCurvePeriod = "1y";

    function handleYearWheelScroll(e) {
      // Dinonaktifkan untuk mencegah lonjakan tahun otomatis dan lag saat scrolling halaman
    }
    window.handleYearWheelScroll = handleYearWheelScroll;

    function scrollFinanceYear(delta) {
      var select = document.getElementById('selectFinanceYear');
      if (!select) return;
      var currentYear = parseInt(select.value, 10) || 2026;
      var targetYear = currentYear + delta;
      if (targetYear < 2024) targetYear = 2024;
      if (targetYear > 2045) targetYear = 2045;

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
      var btnNext = document.getElementById('btnNextYear');
      if (select) {
        var currentYear = parseInt(select.value, 10) || 2026;
        if (btnPrev) btnPrev.disabled = (currentYear <= 2024);
        if (btnNext) btnNext.disabled = (currentYear >= 2045);
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

    var finYearDebounceTimer = null;
    function changeFinanceYear(year) {
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

      // Sinkronkan tabel transaksi, 4 KPI, dan kurva secara instan dari data lokal (ZERO LAG)
      if (window.kosApp && typeof window.kosApp.renderFinancesTable === 'function') {
        var searchVal = document.getElementById('financeSearchInput')?.value || '';
        var activeTypeBtn = document.querySelector('#financeTypeFilter .filter-pill.active');
        var activeType = activeTypeBtn ? (activeTypeBtn.getAttribute('data-ftype') || 'all') : 'all';
        var activeLocBtn = document.querySelector('#financeLocationFilters .filter-pill.active');
        var activeLoc = activeLocBtn ? (activeLocBtn.getAttribute('data-floc') || 'all') : 'all';
        window.kosApp.renderFinancesTable(searchVal, activeType, activeLoc);
      }

      clearTimeout(finYearDebounceTimer);
      finYearDebounceTimer = setTimeout(async function() {
        try {
          var activeLocEl = document.querySelector('#financeLocationFilters .filter-pill.active');
          var locParam = activeLocEl ? (activeLocEl.getAttribute('data-floc') || 'all') : 'all';
          var res = await fetch('/api/finances/stats?year=' + encodeURIComponent(yr) + '&location=' + encodeURIComponent(locParam));
          var data = await res.json();
          if (data && data.success) {
            var mSelect = document.getElementById('selectFinanceMonth');
            var isMonthActive = mSelect && mSelect.value !== 'all';
            if (!isMonthActive) {
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
            }

            window.chartIncomeFull = Array.isArray(data.chartIncome) ? data.chartIncome : new Array(12).fill(0);
            window.chartExpenseFull = Array.isArray(data.chartExpense) ? data.chartExpense : new Array(12).fill(0);
            window.chartNetFull = Array.isArray(data.chartNet) ? data.chartNet : new Array(12).fill(0);
            drawRevenueCurve();
          }
        } catch (err) {
          console.error('Error changing finance year:', err);
        }
      }, 150);
    }
    window.changeFinanceYear = changeFinanceYear;

    window.openTransactionModal = function() {
      if (window.kosApp && typeof window.kosApp.openTransactionModal === 'function') {
        window.kosApp.openTransactionModal();
      } else {
        window.openModal('modalTransaction');
      }
    };

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
      var cards = document.querySelectorAll("#billingMobileCardList .mobile-data-card");
      var paidCount = 0;
      var dueCount = 0;
      var dueNominal = 0;
      var partialCount = 0;
      var partialRemNominal = 0;

      rows.forEach(function(r) {
        var s = r.getAttribute("data-search") || "";
        var rStatus = r.getAttribute("data-status") || "";
        var rLoc = r.getAttribute("data-location") || "Lokasi 1";
        var rPrice = parseInt(r.getAttribute("data-price") || "0", 10);
        var rRem = parseInt(r.getAttribute("data-rem") || "0", 10);

        var matchLoc = (currentBillingLocation === "all" || rLoc === currentBillingLocation);
        if (matchLoc) {
          if (rStatus === "paid") paidCount++;
          if (rStatus === "due") {
            dueCount++;
            dueNominal += rPrice;
          }
          if (rStatus === "partial") {
            partialCount++;
            partialRemNominal += rRem;
          }
        }

        var matchSearch = !input || s.includes(input);
        var matchStatus = (currentBillingStatus === "all" || rStatus === currentBillingStatus);

        r.style.display = (matchLoc && matchSearch && matchStatus) ? "" : "none";
      });

      cards.forEach(function(c) {
        var s = (c.getAttribute("data-search") || c.textContent || "").toLowerCase();
        var rLoc = c.getAttribute("data-location") || "Lokasi 1";
        var rStatus = c.getAttribute("data-status") || "";

        var matchLoc = (currentBillingLocation === "all" || rLoc === currentBillingLocation);
        var matchSearch = !input || s.includes(input);
        var matchStatus = (currentBillingStatus === "all" || rStatus === currentBillingStatus);

        c.style.display = (matchLoc && matchSearch && matchStatus) ? "" : "none";
      });

      var paidEl = document.getElementById("billKpiPaidVal");
      var dueEl = document.getElementById("billKpiDueVal");
      var dueNomEl = document.getElementById("billKpiDueNom");
      var partialEl = document.getElementById("billKpiPartialVal");
      var rateEl = document.getElementById("billKpiRateVal");

      if (paidEl) paidEl.textContent = paidCount + " Kamar";
      if (dueEl) dueEl.textContent = dueCount + " Kamar";
      if (dueNomEl) dueNomEl.textContent = "Rp " + dueNominal.toLocaleString("id-ID");
      if (partialEl) partialEl.textContent = partialCount + " Kamar";
      if (rateEl) {
        var totOcc = paidCount + dueCount + partialCount;
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
    window.filterBillingStatus = filterBillingStatus;
    window.filterBillingTable = filterBillingTable;

    window.openManualPaymentReceiptModal = function(roomId, roomNumber, tenantName, price, location, paidSoFar, remainingBill, fullPrice) {
      var select = document.getElementById('payRecRoomId');
      if (roomId && select) select.value = roomId;

      var amt = document.getElementById('payRecAmount');
      var desc = document.getElementById('payRecDesc');
      var infoWrap = document.getElementById('payRecInstallmentInfo');
      var nowMonth = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date());

      paidSoFar = parseInt(paidSoFar || 0, 10);
      remainingBill = parseInt(remainingBill || 0, 10);
      fullPrice = parseInt(fullPrice || price || 0, 10);
      var isPartial = (paidSoFar > 0 && remainingBill > 0);

      if (amt) {
        amt.value = isPartial ? remainingBill : (price || fullPrice || 0);
      }

      if (infoWrap) {
        if (isPartial) {
          infoWrap.style.display = 'block';
          infoWrap.innerHTML =
            '<div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 12px 14px; font-size: 0.8rem; color: #92400e;">' +
              '<div style="font-weight: 800; font-size: 0.84rem; color: #b45309; margin-bottom: 4px;">Informasi Cicilan Aktif Unit Kamar #' + (roomNumber || '') + '</div>' +
              '<div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-top: 4px; font-size: 0.78rem;">' +
                '<span>Tarif: <strong>Rp ' + fullPrice.toLocaleString('id-ID') + '</strong></span>' +
                '<span>Telah Dicicil: <strong style="color: #059669;">Rp ' + paidSoFar.toLocaleString('id-ID') + '</strong></span>' +
                '<span>Sisa Pelunasan: <strong style="color: #dc2626;">Rp ' + remainingBill.toLocaleString('id-ID') + '</strong></span>' +
              '</div>' +
              '<div style="margin-top: 8px;">' +
                '<button type="button" onclick="document.getElementById(\'payRecAmount\').value = ' + remainingBill + ';" style="font-size: 0.74rem; font-weight: 800; background: #d97706; color: #ffffff; border: none; border-radius: 8px; padding: 5px 12px; cursor: pointer;">' +
                  'Isi Nominal Sisa Tagihan (Rp ' + remainingBill.toLocaleString('id-ID') + ')' +
                '</button>' +
              '</div>' +
            '</div>';
        } else {
          infoWrap.style.display = 'none';
          infoWrap.innerHTML = '';
        }
      }

      if (desc) {
        if (isPartial) {
          desc.value = 'Pelunasan Sewa Kamar #' + (roomNumber || '') + (tenantName && tenantName !== '-' ? ' (' + tenantName + ')' : '') + ' Periode ' + nowMonth;
        } else {
          desc.value = 'Pembayaran Sewa Kamar #' + (roomNumber || '') + (tenantName && tenantName !== '-' ? ' (' + tenantName + ')' : '') + ' Periode ' + nowMonth;
        }
      }

      window.openModal('modalManualPaymentReceipt');
    };

    window.onManualPayRoomChanged = function(sel) {
      var opt = sel.options[sel.selectedIndex];
      if (!opt || !opt.value) return;
      var price = parseInt(opt.getAttribute('data-price') || '0', 10);
      var tenant = opt.getAttribute('data-tenant');
      var room = opt.getAttribute('data-room');
      var paidSoFar = parseInt(opt.getAttribute('data-paid') || '0', 10);
      var remainingBill = parseInt(opt.getAttribute('data-rem') || '0', 10);
      var fullPrice = parseInt(opt.getAttribute('data-full-price') || String(price), 10);

      var isPartial = (paidSoFar > 0 && remainingBill > 0);
      var amt = document.getElementById('payRecAmount');
      if (amt) {
        amt.value = isPartial ? remainingBill : price;
      }

      var infoWrap = document.getElementById('payRecInstallmentInfo');
      if (infoWrap) {
        if (isPartial) {
          infoWrap.style.display = 'block';
          infoWrap.innerHTML =
            '<div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 12px 14px; font-size: 0.8rem; color: #92400e;">' +
              '<div style="font-weight: 800; font-size: 0.84rem; color: #b45309; margin-bottom: 4px;">Informasi Cicilan Aktif Unit Kamar #' + (room || '') + '</div>' +
              '<div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 6px; margin-top: 4px; font-size: 0.78rem;">' +
                '<span>Tarif: <strong>Rp ' + fullPrice.toLocaleString('id-ID') + '</strong></span>' +
                '<span>Telah Dicicil: <strong style="color: #059669;">Rp ' + paidSoFar.toLocaleString('id-ID') + '</strong></span>' +
                '<span>Sisa Pelunasan: <strong style="color: #dc2626;">Rp ' + remainingBill.toLocaleString('id-ID') + '</strong></span>' +
              '</div>' +
              '<div style="margin-top: 8px;">' +
                '<button type="button" onclick="document.getElementById(\'payRecAmount\').value = ' + remainingBill + ';" style="font-size: 0.74rem; font-weight: 800; background: #d97706; color: #ffffff; border: none; border-radius: 8px; padding: 5px 12px; cursor: pointer;">' +
                  'Isi Nominal Sisa Tagihan (Rp ' + remainingBill.toLocaleString('id-ID') + ')' +
                '</button>' +
              '</div>' +
            '</div>';
        } else {
          infoWrap.style.display = 'none';
          infoWrap.innerHTML = '';
        }
      }

      var desc = document.getElementById('payRecDesc');
      var nowMonth = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date());
      if (desc) {
        if (isPartial) {
          desc.value = 'Pelunasan Sewa Kamar #' + (room || '') + (tenant && tenant !== '(Unit Kosong)' ? ' (' + tenant + ')' : '') + ' Periode ' + nowMonth;
        } else {
          desc.value = 'Pembayaran Sewa Kamar #' + (room || '') + (tenant && tenant !== '(Unit Kosong)' ? ' (' + tenant + ')' : '') + ' Periode ' + nowMonth;
        }
      }
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
          window.closeModal('modalManualPaymentReceipt');
          window.showModernAlert(data.message || 'Pembayaran berhasil dicatat & kuitansi resmi langsung dikirim ke pelanggan!', 'success', 'Pembayaran Berhasil', function() {
            window.location.reload();
          });
        } else {
          window.showModernAlert(data.message || 'Gagal memproses pembayaran.', 'error', 'Gagal Memproses');
        }
      })
      .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = prevTxt;
        window.showModernAlert('Terjadi kesalahan saat memproses pembayaran.', 'error', 'Gangguan Sistem');
      });
    };

    async function quickMarkPaid(roomNumber, tenantName, price) {
      var ok = await window.showModernConfirm("Konfirmasi bahwa tagihan Kamar #" + roomNumber + " (" + tenantName + ") sebesar Rp " + price.toLocaleString("id-ID") + " telah lunas diterima?", {
        title: "Konfirmasi Pelunasan Sewa",
        okText: "Ya, Tandai Lunas",
        danger: false
      });
      if (!ok) return;

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
        window.showModernAlert("Pembayaran berhasil dikonfirmasi dan dicatat ke Buku Kas!", "success", "Pembayaran Berhasil Dicatat", function() {
          window.location.reload();
        });
      })
      .catch(function(err) {
        window.showModernAlert("Gagal mencatat pembayaran.", "error", "Gagal Mencatat");
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
            '<td colspan="6" style="padding:28px 16px;text-align:center;background:#f8fafc;border-bottom:1px solid #e2e8f0;">' +
              '<div style="font-weight:800;color:#0f172a;font-size:0.86rem;margin-bottom:4px;">DATA NIHIL (Rp 0) — Belum Ada Transaksi Tercatat pada ' + trxPeriodText + '</div>' +
              '<div style="font-size:0.76rem;color:#64748b;margin-bottom:12px;">Data transaksi keuangan pada periode ini belum memiliki riwayat pembayaran sewa maupun beban operasional.</div>' +
              '<button type="button" onclick="setPdfToAllMonths()" style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#0a5661 0%,#0d9488 100%);color:#ffffff;border:none;padding:8px 18px;border-radius:10px;font-size:0.78rem;font-weight:800;cursor:pointer;box-shadow:0 4px 12px rgba(10,86,97,0.22);">' +
                '<span>Tampilkan Semua Bulan (1 Tahun Penuh)</span>' +
              '</button>' +
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
        '<div class="pdf-executive-sheet" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">' +
          /* Executive Header (Clean White Customer Theme) */
          '<div style="background:#ffffff;color:#0f172a;padding:28px 32px;display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap;border-bottom:3px solid #0d9488;">' +
            '<div style="max-width:440px;">' +
              '<div style="display:inline-flex;align-items:center;gap:8px;background:#f0fdfa;border:1.5px solid #99f6e4;padding:4px 12px;border-radius:999px;font-size:0.68rem;font-weight:800;letter-spacing:0.06em;color:#0a5661;margin-bottom:10px;">' + (payload.isMonthFiltered ? 'LAPORAN KEUANGAN RESMI & BUKU KAS BULANAN' : 'LAPORAN KEUANGAN RESMI & BUKU KAS TAHUNAN') + '</div>' +
              '<h1 style="margin:0;font-size:1.55rem;font-weight:900;letter-spacing:-0.02em;color:#0a5661;">' + payload.kosName + '</h1>' +
              '<p style="margin:5px 0 0 0;font-size:0.78rem;color:#64748b;line-height:1.45;">' + payload.kosAddress + '</p>' +
            '</div>' +
            '<div style="background:#f8fafc;border:1.5px solid #cbd5e1;border-radius:14px;padding:12px 18px;text-align:right;min-width:210px;">' +
              '<div style="font-size:0.66rem;text-transform:uppercase;letter-spacing:0.08em;color:#0d9488;font-weight:800;">PERIODE ANGGARAN</div>' +
              '<div style="font-size:1.35rem;font-weight:900;color:#0f172a;margin:2px 0;">' + payload.periodTitle + '</div>' +
              '<div style="font-size:0.72rem;color:#475569;font-weight:600;">No. Dokumen: ' + docCode + '</div>' +
              '<div style="font-size:0.7rem;color:#64748b;margin-top:2px;">Dicetak: ' + printedDate + '</div>' +
            '</div>' +
          '</div>' +

          /* Body Content */
          '<div style="padding:26px 32px;display:flex;flex-direction:column;gap:22px;background:#ffffff;">' +
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
                    '<tr style="background:#f8fafc;color:#0f172a;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;border-bottom:2px solid #0d9488;">' +
                      '<th style="padding:10px 10px;text-align:left;font-weight:800;">Bulan</th>' +
                      '<th style="padding:10px 10px;text-align:left;font-weight:800;">Indikator Visual</th>' +
                      '<th style="padding:10px 10px;text-align:right;font-weight:800;">Pemasukan</th>' +
                      '<th style="padding:10px 10px;text-align:right;font-weight:800;">Pengeluaran</th>' +
                      '<th style="padding:10px 10px;text-align:right;font-weight:800;">Laba Bersih</th>' +
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

    window.setPdfToAllMonths = function() {
      var mSelect = document.getElementById("selectFinanceMonth");
      if (mSelect) {
        mSelect.value = "all";
        if (typeof changeFinanceMonth === "function") changeFinanceMonth("all");
      }
      openFinancePdfModal();
    };

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
        '<' + 'style>' +
          '*, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }' +
          'body { margin: 0; padding: 20px; background: #ffffff; font-family: "Outfit", -apple-system, BlinkMacSystemFont, sans-serif; color: #0f172a; }' +
          '.pdf-executive-sheet { max-width: 840px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; }' +
          '@page { size: A4 portrait; margin: 8mm; }' +
          '@media print { body { padding: 0; background: #ffffff; } .pdf-executive-sheet { max-width: 100%; border-radius: 0; box-shadow: none; } }' +
        '<' + '/style></head><body>' +
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
              '<' + 'style>*, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } body { margin: 0; padding: 20px; background: #f1f5f9; font-family: "Outfit", sans-serif; } .pdf-executive-sheet { max-width: 840px; margin: 0 auto; background: #fff; } @page { size: A4 portrait; margin: 8mm; } @media print { body { padding: 0; } }<' + '/style></head><body>' +
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
        window.showModernAlert('Mohon isi Nama Metode Tampilan terlebih dahulu!', 'error', 'Perhatian');
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

    function toggleAdminWfPass(btn) {
      if (!btn) return;
      var parent = btn.parentElement;
      if (!parent) return;
      var masked = parent.querySelector('.wf-pass-masked');
      var plain = parent.querySelector('.wf-pass-plain');
      var label = btn.querySelector('.admin-wf-toggle-label') || btn;
      if (!masked || !plain) return;
      if (plain.style.display === 'none' || !plain.style.display) {
        masked.style.display = 'none';
        plain.style.display = 'inline-block';
        label.textContent = 'Sembunyikan';
      } else {
        masked.style.display = 'inline-block';
        plain.style.display = 'none';
        label.textContent = 'Tampilkan';
      }
    }
    window.toggleAdminWfPass = toggleAdminWfPass;

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
    window.toggleAddWifiForm = toggleAddWifiForm;

    function switchBranchSettings(branchKey) {
      var tabB1 = document.getElementById('btnTabBranch1');
      var tabB2 = document.getElementById('btnTabBranch2');
      var panelB1 = document.getElementById('panelBranch1');
      var panelB2 = document.getElementById('panelBranch2');

      if (branchKey === 'lokasi_2') {
        if (panelB1) panelB1.style.display = 'none';
        if (panelB2) panelB2.style.display = 'flex';
        if (tabB1) {
          tabB1.classList.remove('active');
          tabB1.style.background = 'transparent';
          tabB1.style.color = 'rgba(255,255,255,0.8)';
          tabB1.style.boxShadow = 'none';
        }
        if (tabB2) {
          tabB2.classList.add('active');
          tabB2.style.background = '#ffffff';
          tabB2.style.color = '#0d9488';
          tabB2.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        }
      } else {
        if (panelB2) panelB2.style.display = 'none';
        if (panelB1) panelB1.style.display = 'flex';
        if (tabB2) {
          tabB2.classList.remove('active');
          tabB2.style.background = 'transparent';
          tabB2.style.color = 'rgba(255,255,255,0.8)';
          tabB2.style.boxShadow = 'none';
        }
        if (tabB1) {
          tabB1.classList.add('active');
          tabB1.style.background = '#ffffff';
          tabB1.style.color = '#0a5661';
          tabB1.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        }
      }
    }
    window.switchBranchSettings = switchBranchSettings;

    function updateBranchActiveState(branchKey, isChecked) {
      if (branchKey === 'lokasi_1') {
        var badge = document.getElementById('badgeB1Active');
        var tabBadge = document.getElementById('badgeStatusTabB1');
        if (badge) {
          badge.textContent = isChecked ? 'Aktif / Ditampilkan' : 'Disembunyikan';
          badge.style.background = isChecked ? '#dcfce7' : '#fee2e2';
          badge.style.color = isChecked ? '#15803d' : '#b91c1c';
        }
        if (tabBadge) {
          tabBadge.textContent = isChecked ? 'Tampil' : 'Disembunyikan';
          tabBadge.style.background = isChecked ? '#10b981' : '#ef4444';
        }
      } else if (branchKey === 'lokasi_2') {
        var badge = document.getElementById('badgeB2Active');
        var tabBadge = document.getElementById('badgeStatusTabB2');
        if (badge) {
          badge.textContent = isChecked ? 'Aktif / Ditampilkan' : 'Disembunyikan';
          badge.style.background = isChecked ? '#dcfce7' : '#fee2e2';
          badge.style.color = isChecked ? '#15803d' : '#b91c1c';
        }
        if (tabBadge) {
          tabBadge.textContent = isChecked ? 'Tampil' : 'Disembunyikan';
          tabBadge.style.background = isChecked ? '#10b981' : '#ef4444';
        }
      }
    }
    window.updateBranchActiveState = updateBranchActiveState;

    function previewBranchFile(input, targetImgId) {
      if (input && input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = document.getElementById(targetImgId);
          if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    window.previewBranchFile = previewBranchFile;

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
        window.showModernAlert('Mohon isi Nama WiFi (SSID) terlebih dahulu!', 'error', 'Form Belum Lengkap');
        document.getElementById('newWfName')?.focus();
        return;
      }
      if (!modem) {
        window.showModernAlert('Mohon isi Modem / Lokasi Modem terlebih dahulu!', 'error', 'Form Belum Lengkap');
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
        window.showModernAlert('Minimal harus terdapat 1 poin peraturan kos.', 'error', 'Peraturan Kos');
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

    window.resetSurveyTemplateToDefault = async function() {
      var confirmed = await window.showModernConfirm('Kembalikan format formulir ke format standar awal?', {
        title: 'Reset Template Formulir',
        okText: 'Ya, Kembalikan',
        danger: true
      });
      if (confirmed) {
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
        window.showModernAlert('Format formulir tidak boleh kosong', 'error', 'Perhatian');
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
      if (document.getElementById('adminBranchInput')) document.getElementById('adminBranchInput').value = 'all';
      document.getElementById('modalManageAdminTitle').textContent = 'Tambah Akun Admin Baru';
      document.getElementById('adminPasswordInput').required = true;
      document.getElementById('adminPasswordHelp').style.display = 'none';
      document.getElementById('btnSubmitManageAdmin').textContent = 'Simpan Akun Admin';
      window.openModal('modalManageAdmin');
    };

    window.openEditAdminModal = function(idOrData, name, email, phone, branch) {
      var adminData;
      if (typeof idOrData === 'object' && idOrData !== null) {
        adminData = idOrData;
      } else {
        adminData = {
          id: idOrData,
          name: name,
          email: email,
          phone: phone,
          branch_assignment: branch
        };
      }
      if (!adminData || !adminData.id) return;
      document.getElementById('manageAdminId').value = adminData.id;
      document.getElementById('adminNameInput').value = adminData.name || '';
      document.getElementById('adminEmailInput').value = adminData.email || '';
      document.getElementById('adminPhoneInput').value = adminData.phone || '';
      if (document.getElementById('adminBranchInput')) document.getElementById('adminBranchInput').value = adminData.branch_assignment || 'all';
      document.getElementById('adminPasswordInput').value = '';
      document.getElementById('adminPasswordInput').required = false;
      document.getElementById('adminPasswordHelp').style.display = 'block';
      document.getElementById('modalManageAdminTitle').textContent = 'Edit Akun Staf Admin';
      document.getElementById('btnSubmitManageAdmin').textContent = 'Perbarui Akun Admin';
      window.openModal('modalManageAdmin');
    };

    window.changeAdminBranch = function(adminId, newBranch) {
      if (!adminId || !newBranch) return;
      var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

      fetch('/api/admins/' + adminId + '/branch', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ branch_assignment: newBranch })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          // Sinkronkan pilihan dropdown antara tabel desktop dan kartu mobile
          var tableRow = document.getElementById('adminRow_' + adminId);
          if (tableRow) {
            var tableSel = tableRow.querySelector('.admin-branch-select');
            if (tableSel && tableSel.value !== newBranch) tableSel.value = newBranch;
          }
          var mobileCard = document.getElementById('adminMobileCard_' + adminId);
          if (mobileCard) {
            var cardSel = mobileCard.querySelector('.admin-branch-select');
            if (cardSel && cardSel.value !== newBranch) cardSel.value = newBranch;
          }
          window.showModernAlert(
            data.message || 'Penugasan cabang staf Admin berhasil diperbarui.',
            'success',
            'Penugasan Cabang Berhasil Diubah'
          );
        } else {
          window.showModernAlert(data.message || 'Gagal mengubah penugasan cabang admin.', 'error', 'Gagal Memperbarui');
        }
      })
      .catch(function(err) {
        window.showModernAlert('Terjadi kesalahan jaringan saat menyimpan penugasan cabang.', 'error', 'Gangguan Jaringan');
      });
    };

    window.toggleInputPasswordVisibility = function(inputId, iconId) {
      var input = document.getElementById(inputId);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
    };

    window.filterAdminTable = function() {
      var query = (document.getElementById('searchAdminInput')?.value || '').toLowerCase().trim();
      var rows = document.querySelectorAll('#tbodyAdminRows .admin-data-row');
      var cards = document.querySelectorAll('#adminsMobileCardList .admin-mobile-card');

      var visibleRowCount = 0;
      rows.forEach(function(row) {
        var name = (row.getAttribute('data-name') || '').toLowerCase();
        var email = (row.getAttribute('data-email') || '').toLowerCase();
        var phone = (row.getAttribute('data-phone') || '').toLowerCase();
        if (!query || name.includes(query) || email.includes(query) || phone.includes(query)) {
          row.style.display = '';
          visibleRowCount++;
        } else {
          row.style.display = 'none';
        }
      });

      var visibleCardCount = 0;
      cards.forEach(function(card) {
        var name = (card.getAttribute('data-name') || '').toLowerCase();
        var email = (card.getAttribute('data-email') || '').toLowerCase();
        var phone = (card.getAttribute('data-phone') || '').toLowerCase();
        if (!query || name.includes(query) || email.includes(query) || phone.includes(query)) {
          card.style.display = '';
          visibleCardCount++;
        } else {
          card.style.display = 'none';
        }
      });

      var emptyRow = document.getElementById('emptyAdminRow');
      if (emptyRow) {
        emptyRow.style.display = visibleRowCount === 0 ? '' : 'none';
      }
      var emptyCard = document.getElementById('emptyAdminMobileCard');
      if (emptyCard) {
        emptyCard.style.display = visibleCardCount === 0 ? '' : 'none';
      }
    };

    window.submitAdminForm = function(e) {
      if (e) e.preventDefault();
      var btn = document.getElementById('btnSubmitManageAdmin');
      var adminId = document.getElementById('manageAdminId').value;
      var name = document.getElementById('adminNameInput').value.trim();
      var email = document.getElementById('adminEmailInput').value.trim();
      var phone = document.getElementById('adminPhoneInput').value.trim();
      var branchAssignment = document.getElementById('adminBranchInput') ? document.getElementById('adminBranchInput').value : 'all';
      var password = document.getElementById('adminPasswordInput').value;

      if (!name || !email) {
        window.showModernAlert('Nama lengkap dan email admin wajib diisi.', 'error', 'Data Belum Lengkap');
        return;
      }

      if (!adminId && (!password || password.length < 6)) {
        window.showModernAlert('Kata sandi untuk admin baru minimal 6 karakter.', 'error', 'Keamanan Password');
        return;
      }

      if (password && password.length < 6) {
        window.showModernAlert('Kata sandi minimal 6 karakter.', 'error', 'Keamanan Password');
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
          branch_assignment: branchAssignment,
          password: password || null
        })
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        btn.disabled = false;
        btn.textContent = prevBtnText;
        if (data.success) {
          try {
            sessionStorage.setItem('active_dashboard_tab', 'tab-admins');
          } catch(err) {}
          window.closeModal('modalManageAdmin');
          window.showModernAlert(
            data.message || 'Akun staf Admin berhasil diperbarui.',
            'success',
            'Akun Berhasil Disimpan',
            function() {
              window.location.reload();
            }
          );
        } else {
          window.showModernAlert(data.message || 'Gagal menyimpan akun admin', 'error', 'Penyimpanan Gagal');
        }
      })
      .catch(function(err) {
        btn.disabled = false;
        btn.textContent = prevBtnText;
        window.showModernAlert('Terjadi kesalahan jaringan saat menyimpan data admin.', 'error', 'Gangguan Koneksi');
      });
    };

    window.confirmDeleteAdmin = async function(adminId, adminName) {
      var confirmed = await window.showModernConfirm(
        'Apakah Anda yakin ingin menghapus hak akses dan akun staf Admin "' + adminName + '"? Staf tidak akan dapat login lagi ke sistem.',
        {
          title: 'Hapus Akses Admin',
          okText: 'Ya, Hapus Akses',
          danger: true
        }
      );
      if (!confirmed) return;

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
          window.showModernAlert(data.message || 'Akun admin berhasil dihapus.', 'success', 'Akun Berhasil Dihapus');
          var row = document.getElementById('adminRow_' + adminId);
          if (row) row.remove();
          var card = document.getElementById('adminMobileCard_' + adminId);
          if (card) card.remove();
          var badge = document.getElementById('badgeTotalAdmins');
          if (badge) {
            var curr = parseInt(badge.textContent) || 0;
            if (curr > 0) badge.textContent = curr - 1;
          }
        } else {
          window.showModernAlert(data.message || 'Gagal menghapus akun admin.', 'error', 'Gagal Menghapus');
        }
      })
      .catch(function(err) {
        window.showModernAlert('Terjadi kesalahan saat menghapus data admin.', 'error', 'Gangguan Sistem');
      });
    };

    // Pulihkan tab aktif jika ada permintaan navigasi setelah submit
    try {
      var savedTab = sessionStorage.getItem('active_dashboard_tab');
      if (savedTab) {
        sessionStorage.removeItem('active_dashboard_tab');
        setTimeout(function() {
          if (typeof window.switchTab === 'function') window.switchTab(savedTab);
        }, 100);
      }
    } catch(e) {}

  </script>

@endsection
