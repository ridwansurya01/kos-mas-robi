/**
 * ============================================================================
 * KOS MAS ROBI - SMART MANAGEMENT SYSTEM (LARAVEL POWERED)
 * Responsive Engine & Reactive Laravel API Integration
 * Automatically adapts to any device (Mobile, Tablet, Desktop)
 * ============================================================================
 */

(function () {
  'use strict';

  // --- CSRF TOKEN HELPER ---
  const getCsrfToken = () => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  };

  const THEME_KEY = 'KOS_MAS_ROBI_THEME';

  // --- APP STATE ---
  let appData = {
    settings: {
      kos_name: 'Kos Mas Robi - Kampus Terpadu',
      owner_name: 'Mas Robi',
      owner_phone: '6281234567890',
      kos_address: 'Jl. Kaliurang KM 5, Sleman, DI Yogyakarta',
      bank_name: 'Bank Central Asia (BCA)',
      bank_account: '8830192831',
      bank_holder: 'Robi Suryanto'
    },
    rooms: [],
    finances: [],
    maintenance: []
  };

  // --- AUTOMATIC DEVICE DETECTION (Fluid & Automatic) ---
  function initAutoDeviceDetector() {
    function detectAndApplyDevice() {
      const width = window.innerWidth;
      const ua = navigator.userAgent.toLowerCase();
      let device = 'desktop';

      if (width <= 768 || /iphone|ipod|android.*mobile|windows phone|blackberry/i.test(ua)) {
        device = 'mobile';
      } else if ((width > 768 && width <= 1024) || /ipad|tablet|android(?!.*mobile)/i.test(ua)) {
        device = 'tablet';
      }

      document.documentElement.setAttribute('data-device', device);
    }

    window.addEventListener('resize', detectAndApplyDevice);
    window.addEventListener('orientationchange', detectAndApplyDevice);
    detectAndApplyDevice();
  }

  // --- API FETCH & SYNC ---
  async function fetchServerData() {
    try {
      const response = await fetch('/api/data');
      if (response.ok) {
        const json = await response.json();
        appData.settings = json.settings || appData.settings;
        appData.rooms = (json.rooms || []).map(r => ({
          id: String(r.id),
          number: r.number,
          floor: r.floor,
          type: r.type,
          location: r.location || 'Lokasi 1',
          price: Number(r.price),
          status: r.status,
          facilities: r.facilities || [],
          image: r.image || '',
          images: r.images || [],
          images_list: r.images_list || [],
          custom_interior_status: r.custom_interior_status || 'none',
          custom_interior_budget: Number(r.custom_interior_budget || 0),
          custom_interior_request: r.custom_interior_request || '',
          custom_interior_admin_notes: r.custom_interior_admin_notes || '',
          booked_by_name: r.booked_by_name || '',
          tenant: r.active_tenant ? r.active_tenant.name : null,
          phone: r.active_tenant ? r.active_tenant.phone : '',
          startDate: r.active_tenant ? r.active_tenant.start_date : '',
          dueDay: r.active_tenant ? r.active_tenant.due_day : 1
        }));
        appData.finances = (json.finances || []).map(f => ({
          id: f.transaction_code,
          dbId: f.id,
          date: f.date,
          paymentTime: f.formatted_payment_full || (f.date + ', ' + (f.formatted_payment_time || '12:00:00 WIB')),
          timezone: f.timezone || 'WIB',
          type: f.type,
          category: f.category,
          amount: Number(f.amount),
          desc: f.description,
          method: f.payment_method,
          roomId: f.room_id ? String(f.room_id) : null,
          location: f.room && f.room.location ? f.room.location : null,
          proofImage: f.proof_image ? ('/' + f.proof_image.replace(/^\//, '')) : null
        }));
        appData.maintenance = (json.maintenance || []).map(m => ({
          id: m.ticket_code,
          dbId: m.id,
          roomId: m.room ? m.room.number : String(m.room_id),
          location: m.room && m.room.location ? m.room.location : null,
          reporter: m.reporter_name,
          issue: m.issue_description,
          date: m.report_date,
          priority: m.priority,
          status: m.status
        }));
        renderAllViews();
      }
    } catch (e) {
      console.warn('API sync unavailable, using cached state:', e);
    }
  }

  // --- UTILITY FORMATTERS ---
  const formatRupiah = (num) => {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
  };

  const getStatusBadge = (status) => {
    switch (status) {
      case 'occupied':
        return '<span class="status-badge occupied">● Terisi</span>';
      case 'available':
        return '<span class="status-badge available">● Kosong</span>';
      case 'due':
        return '<span class="status-badge due">Jatuh Tempo</span>';
      case 'repair':
        return '<span class="status-badge repair">Servis</span>';
      default:
        return `<span class="status-badge">${status}</span>`;
    }
  };

  const showToast = (message, type = 'success') => {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <div style="font-weight: 600;">${type === 'success' ? '' : type === 'warning' ? '' : 'ℹ'}</div>
      <div>${message}</div>
    `;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3200);
  };

  // --- THEME CONTROLLER (PERMANENT SINGLE LUXURY TEAL THEME MATCHING LANDING PAGE) ---
  function initTheme() {
    document.documentElement.removeAttribute('data-theme');
    try {
      localStorage.removeItem(THEME_KEY);
      localStorage.removeItem('kosTheme');
    } catch(e) {}
  }

  // --- NAVIGATION CONTROLLER ---
  const navTitles = {
    'tab-dashboard': { title: 'Dashboard Ikhtisar', sub: 'Monitoring real-time hunian, status kamar, dan operasional' },
    'tab-rooms': { title: 'Manajemen Kamar', sub: 'Daftar seluruh unit kamar, fasilitas, dan tarif sewa' },
    'tab-tenants': { title: 'Data Penghuni Kos', sub: 'Kelola kontak anak kos, sewa aktif, dan pengingat WhatsApp' },
    'tab-bills': { title: 'Tagihan Sewa & Invoice', sub: 'Monitoring jatuh tempo sewa, kirim WA pengingat tagihan, dan konfirmasi lunas' },
    'tab-transfers': { title: 'Pengajuan Pindah Kamar', sub: 'Daftar permohonan alih kamar dari anak kos dan persetujuan' },
    'tab-maintenance': { title: 'Keluhan & Perbaikan', sub: 'Daftar laporan fasilitas kos dan penanganan teknisi' },
    'tab-finances': { title: 'Laporan Keuangan & Arus Kas', sub: 'Analisis kurva pendapatan bulanan s/d 1 tahun penuh, laba bersih, dan buku kas' },
    'tab-settings': { title: 'Pengaturan Kos', sub: 'Pengaturan tampilan landing page, titik lokasi, profil properti, dan jaringan WiFi' }
  };

  function switchTab(tabId) {
    const panes = document.querySelectorAll('.tab-pane');
    const sidebarItems = document.querySelectorAll('.app-sidebar .nav-item');
    const bottomNavItems = document.querySelectorAll('.mobile-bottom-nav .bottom-nav-item');
    const pageTitle = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');
    const breadcrumbCurrent = document.getElementById('breadcrumbCurrent');

    panes.forEach(pane => {
      pane.classList.toggle('active', pane.id === tabId);
    });

    sidebarItems.forEach(item => {
      item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
    });

    bottomNavItems.forEach(item => {
      const isAct = item.getAttribute('data-tab') === tabId;
      item.classList.toggle('active', isAct);
      if (isAct && typeof item.scrollIntoView === 'function') {
        try { item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }); } catch (e) {}
      }
    });

    if (navTitles[tabId]) {
      if (pageTitle) pageTitle.textContent = navTitles[tabId].title;
      if (pageSubtitle) pageSubtitle.textContent = navTitles[tabId].sub;
      if (breadcrumbCurrent) breadcrumbCurrent.textContent = navTitles[tabId].title;
    }

    if (tabId === 'tab-finances' && typeof window.drawRevenueCurve === 'function') {
      setTimeout(window.drawRevenueCurve, 80);
    }

    document.getElementById('appSidebar')?.classList.remove('open');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function initNavigation() {
    window.switchTab = switchTab;

    document.addEventListener('click', (e) => {
      const tabTarget = e.target.closest('[data-tab]');
      if (tabTarget) {
        const tab = tabTarget.getAttribute('data-tab');
        if (tab) switchTab(tab);
      }
    });

    const mobileMenuToggle = document.getElementById('btnToggleMobileSidebar');
    mobileMenuToggle?.addEventListener('click', () => {
      document.getElementById('appSidebar')?.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
      const sidebar = document.getElementById('appSidebar');
      if (sidebar && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !mobileMenuToggle?.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      }
    });
  }

  // --- DASHBOARD & ROOM GRIDS ---
  function renderRoomGrid(filter = 'all') {
    const f1Grid = document.getElementById('floor1Grid');
    const f2Grid = document.getElementById('floor2Grid');
    if (!f1Grid || !f2Grid) return;

    f1Grid.innerHTML = '';
    f2Grid.innerHTML = '';

    const filteredRooms = appData.rooms.filter(room => {
      if (filter === 'all') return true;
      return room.status === filter;
    });

    filteredRooms.forEach(room => {
      const card = document.createElement('div');
      card.className = 'room-card';
      card.setAttribute('data-room-id', room.number);

      const featuresHtml = (room.facilities || []).slice(0, 3).map(f => `<span class="feature-pill">${f}</span>`).join('');

      const isLoc2 = room.location === 'Lokasi 2';
      const locPill = isLoc2
        ? `<span style="font-size:0.68rem;font-weight:800;color:#0284c7;background:#e0f2fe;padding:2px 7px;border-radius:6px;border:1px solid #bae6fd;display:inline-flex;align-items:center;gap:3px;">${getBranchName('Lokasi 2')}</span>`
        : `<span style="font-size:0.68rem;font-weight:800;color:#0a5661;background:#f0fdfa;padding:2px 7px;border-radius:6px;border:1px solid #ccfbf1;display:inline-flex;align-items:center;gap:3px;">${getBranchName('Lokasi 1')}</span>`;

      if (isLoc2) {
        card.style.borderTop = '3px solid #0284c7';
      } else {
        card.style.borderTop = '3px solid #0a5661';
      }

      card.innerHTML = `
        <div class="room-card-top">
          <div class="room-number-wrap">
            <span class="room-number">#${room.number}</span>
            ${locPill}
            <span class="room-type-tag">${room.type}</span>
          </div>
          ${getStatusBadge(room.status)}
        </div>
        <div class="room-details">
          <div class="room-tenant-name">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            ${room.tenant ? room.tenant : '<em style="color:var(--text-muted);font-weight:400">Belum berpenghuni</em>'}
          </div>
          <div class="room-due-info">
            ${room.status === 'occupied' || room.status === 'due' 
              ? `Jatuh tempo: Tanggal <strong>${room.dueDay}</strong> setiap bulan`
              : (room.status === 'repair' ? 'Sedang tahap perawatan fasilitas' : 'Kamar bersih & siap langsung huni')}
          </div>
          <div class="room-features">${featuresHtml}</div>
        </div>
        <div class="room-card-bottom">
          <div class="room-price">${formatRupiah(room.price)} <span>/bln</span></div>
          <div class="room-actions">
            ${room.phone ? `
              <button type="button" class="btn-icon-sm whatsapp" title="Kirim Pesan WhatsApp" data-wa-room="${room.number}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
              </button>` : ''}
            <button type="button" class="btn-icon-sm" title="Lihat Detail Kamar" data-detail-room="${room.number}">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      `;

      card.addEventListener('click', (e) => {
        if (e.target.closest('.whatsapp')) {
          sendWhatsAppReminder(room.number);
          return;
        }
        openRoomDetailModal(room.number);
      });

      if (room.floor === 1) {
        f1Grid.appendChild(card);
      } else {
        f2Grid.appendChild(card);
      }
    });

    if (f1Grid.children.length === 0) {
      f1Grid.innerHTML = '<div style="color:var(--text-muted);font-size:0.9rem;padding:12px;">Tidak ada kamar dengan status ini di Lantai 1</div>';
    }
    if (f2Grid.children.length === 0) {
      f2Grid.innerHTML = '<div style="color:var(--text-muted);font-size:0.9rem;padding:12px;">Tidak ada kamar dengan status ini di Lantai 2</div>';
    }
  }

  function initDashboardFilters() {
    const filterButtons = document.querySelectorAll('#dashboardFilters .filter-pill');
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderRoomGrid(btn.getAttribute('data-filter'));
      });
    });
  }

  function updateDashboardStats() {
    const totalRooms = appData.rooms.length;
    const occupiedRooms = appData.rooms.filter(r => r.status === 'occupied').length;
    const dueRooms = appData.rooms.filter(r => r.status === 'due').length;
    const availableRooms = appData.rooms.filter(r => r.status === 'available').length;
    const totalOccupied = occupiedRooms + dueRooms;

    const rate = totalRooms > 0 ? ((totalOccupied / totalRooms) * 100).toFixed(1) : 0;

    const badgeRoom = document.getElementById('badgeRoomCount');
    const badgeTenant = document.getElementById('badgeTenantCount');
    const badgeDue = document.getElementById('badgeDueBills');
    const badgePendingRepairs = document.getElementById('badgePendingRepairs');

    if (badgeRoom) badgeRoom.textContent = totalRooms;
    if (badgeTenant) badgeTenant.textContent = totalOccupied;
    if (badgeDue) badgeDue.textContent = dueRooms;

    const pendingRepairsCount = appData.maintenance.filter(m => m.status !== 'done').length;
    if (badgePendingRepairs) badgePendingRepairs.textContent = pendingRepairsCount;

    const elOccRate = document.getElementById('statOccupancyRate');
    const elAvail = document.getElementById('statAvailableRooms');
    const elMonthly = document.getElementById('statMonthlyIncome');
    const elDue = document.getElementById('statDueBills');

    if (elOccRate) elOccRate.textContent = `${rate}%`;
    if (elAvail) elAvail.textContent = `${availableRooms} Kamar`;

    const totalIncome = appData.finances
      .filter(f => f.type === 'income')
      .reduce((sum, f) => sum + Number(f.amount || 0), 0);

    const totalDueAmount = appData.rooms
      .filter(r => r.status === 'due')
      .reduce((sum, r) => sum + Number(r.price || 0), 0);

    if (elMonthly) elMonthly.textContent = formatRupiah(totalIncome);
    if (elDue) elDue.textContent = `${dueRooms} Kamar (${formatRupiah(totalDueAmount)})`;
  }

  // --- TAB: MASTER KAMAR ---
  function renderRoomsMasterTable(search = '', floorFilter = 'all', locationFilter = 'all') {
    const tbody = document.getElementById('roomsMasterTableBody');
    const mobileList = document.getElementById('roomsMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const filtered = appData.rooms.filter(r => {
      const matchSearch = r.number.toLowerCase().includes(search.toLowerCase()) ||
                          r.type.toLowerCase().includes(search.toLowerCase()) ||
                          (r.location && r.location.toLowerCase().includes(search.toLowerCase())) ||
                          (r.tenant && r.tenant.toLowerCase().includes(search.toLowerCase()));
      const matchFloor = floorFilter === 'all' || String(r.floor) === String(floorFilter);
      const matchLocation = locationFilter === 'all' || (r.location || 'Lokasi 1') === locationFilter;
      return matchSearch && matchFloor && matchLocation;
    });

    filtered.forEach(room => {
      const isLoc2 = room.location === 'Lokasi 2';
      const locBadge = isLoc2
        ? `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0f9ff;color:#0284c7;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #bae6fd;">${getBranchName('Lokasi 2')}</span>`
        : `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;background:#f0fdfa;color:#0a5661;font-weight:800;padding:4px 10px;border-radius:8px;border:1.5px solid #99f6e4;">${getBranchName('Lokasi 1')}</span>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-weight:800;font-size:1rem;color:#0a5661;">#${room.number}</td>
        <td>${locBadge}</td>
        <td>
          <div style="font-weight:700;color:#1e293b;">${room.type}</div>
          <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">${(room.facilities || []).join(', ')}</div>
        </td>
        <td>Lantai ${room.floor}</td>
        <td style="font-weight:800;color:#0a5661;">${formatRupiah(room.price)}</td>
        <td>${getStatusBadge(room.status)}</td>
        <td>${room.tenant ? `<strong>${room.tenant}</strong>` : '<span style="color:var(--text-muted)">-</span>'}</td>
        <td>
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn-secondary" style="padding:5px 12px;font-size:0.78rem;font-weight:700;border-radius:8px;" onclick="window.kosApp.editRoom('${room.number}')">Edit</button>
            <button type="button" class="btn-secondary" style="padding:5px 12px;font-size:0.78rem;font-weight:700;border-radius:8px;" onclick="window.kosApp.openRoomDetailModal('${room.number}')">Detail</button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);

      const mCard = document.createElement('div');
      mCard.className = 'mobile-data-card';
      mCard.innerHTML = `
        <div class="mobile-data-header">
          <div>
            <span class="mobile-data-title">Kamar #${room.number} (${room.type})</span>
            <div style="margin-top:3px;">${locBadge}</div>
          </div>
          ${getStatusBadge(room.status)}
        </div>
        <div class="mobile-data-row">
          <span>Lantai:</span>
          <span>Lantai ${room.floor}</span>
        </div>
        <div class="mobile-data-row">
          <span>Tarif Sewa:</span>
          <span style="color:#0a5661;font-weight:700;">${formatRupiah(room.price)} /bln</span>
        </div>
        <div class="mobile-data-row">
          <span>Penghuni:</span>
          <span>${room.tenant || 'Kosong'}</span>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px;">
          <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:6px;border-radius:8px;" onclick="window.kosApp.editRoom('${room.number}')">Edit Kamar</button>
          <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:6px;border-radius:8px;" onclick="window.kosApp.openRoomDetailModal('${room.number}')">Lihat Detail</button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ditemukan kamar yang sesuai</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ditemukan kamar yang sesuai</div>';
    }
  }

  // --- HELPER: RESOLVE CABANG KOS (LOKASI 1 VS LOKASI 2) ---
  let activeTenantLocationFilter = 'all';
  let activeFinanceLocationFilter = 'all';
  let activeMaintLocationFilter = 'all';

  function getBranchName(loc) {
    const isLoc2 = (loc === 'Lokasi 2' || loc === 'lokasi_2');
    const branches = appData.settings?.branches || {};
    if (isLoc2) {
      const inputVal = document.getElementById('b2_name')?.value?.trim();
      return branches.lokasi_2?.name || inputVal || 'Kos Mas Robi 2 - Ringroad Exclusive';
    }
    const inputVal = document.getElementById('b1_name')?.value?.trim();
    return branches.lokasi_1?.name || inputVal || 'Kos Mas Robi 1 - Kampus Utama';
  }
  window.getBranchName = getBranchName;

  function syncBranchNamesInDOM() {
    const b1Name = getBranchName('Lokasi 1');
    const b2Name = getBranchName('Lokasi 2');

    document.querySelectorAll('[data-branch-name="lokasi_1"]').forEach(el => {
      el.textContent = b1Name;
    });
    document.querySelectorAll('[data-branch-name="lokasi_2"]').forEach(el => {
      el.textContent = b2Name;
    });
    document.querySelectorAll('[data-branch-option="lokasi_1"]').forEach(opt => {
      opt.textContent = '' + b1Name;
    });
    document.querySelectorAll('[data-branch-option="lokasi_2"]').forEach(opt => {
      opt.textContent = '' + b2Name;
    });
  }
  window.syncBranchNamesInDOM = syncBranchNamesInDOM;

  function resolveRoomLocation(roomObjOrNum) {
    if (!roomObjOrNum) return 'Lokasi 1';
    if (typeof roomObjOrNum === 'object' && roomObjOrNum.location) {
      return roomObjOrNum.location;
    }
    const numStr = String(typeof roomObjOrNum === 'object' ? roomObjOrNum.number : roomObjOrNum);
    const found = appData.rooms.find(r => r.number === numStr || String(r.id) === numStr);
    if (found && found.location) return found.location;
    const numVal = parseInt(numStr.replace(/[^0-9]/g, ''), 10);
    return numVal >= 200 ? 'Lokasi 2' : 'Lokasi 1';
  }

  function resolveFinanceLocation(trx) {
    if (trx.location) return trx.location;
    if (trx.roomId) {
      const found = appData.rooms.find(r => String(r.id) === String(trx.roomId) || r.number === String(trx.roomId));
      if (found && found.location) return found.location;
    }
    const text = ((trx.id || '') + ' ' + (trx.desc || '')).toLowerCase();
    if (text.includes('lokasi 2') || text.includes('ringroad') || text.includes('cabang 2') || text.includes('kmr2') || /kamar\s*#?20\d/i.test(text) || /#20\d/.test(text)) {
      return 'Lokasi 2';
    }
    return 'Lokasi 1';
  }

  function resolveMaintenanceLocation(item) {
    if (item.location) return item.location;
    return resolveRoomLocation(item.roomId);
  }

  // --- TAB: DATA PENGHUNI (2 KOS) ---
  function renderTenantsTable(search = '', statusFilter = 'all', locationFilter = activeTenantLocationFilter) {
    activeTenantLocationFilter = locationFilter;
    const tbody = document.getElementById('tenantsTableBody');
    const mobileList = document.getElementById('tenantsMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const tenants = appData.rooms.filter(r => r.tenant && r.tenant.trim() !== '');

    const loc1Count = tenants.filter(t => resolveRoomLocation(t) === 'Lokasi 1').length;
    const loc2Count = tenants.filter(t => resolveRoomLocation(t) === 'Lokasi 2').length;
    const elLoc1 = document.getElementById('tenantCountLoc1');
    const elLoc2 = document.getElementById('tenantCountLoc2');
    if (elLoc1) elLoc1.textContent = `${loc1Count} Orang`;
    if (elLoc2) elLoc2.textContent = `${loc2Count} Orang`;

    const filtered = tenants.filter(t => {
      const tLoc = resolveRoomLocation(t);
      const matchLoc = (locationFilter === 'all' || tLoc === locationFilter);
      const q = search.toLowerCase();
      const matchSearch = !q ||
                          t.tenant.toLowerCase().includes(q) ||
                          (t.phone || '').includes(q) ||
                          String(t.number).toLowerCase().includes(q) ||
                          tLoc.toLowerCase().includes(q) ||
                          (t.type || '').toLowerCase().includes(q);
      let matchStatus = true;
      if (statusFilter === 'active') matchStatus = t.status === 'occupied';
      if (statusFilter === 'due') matchStatus = t.status === 'due';
      return matchLoc && matchSearch && matchStatus;
    });

    filtered.forEach(t => {
      const isDue = t.status === 'due';
      const statusLabel = isDue 
        ? '<span class="status-badge due">Jatuh Tempo</span>' 
        : '<span class="status-badge occupied">Lunas</span>';

      const tLoc = resolveRoomLocation(t);
      const isLoc2 = tLoc === 'Lokasi 2';
      const branchBadge = isLoc2
        ? `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">${getBranchName('Lokasi 2')}</span><div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Smart Door PIN & Co-Working</div>`
        : `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">${getBranchName('Lokasi 1')}</span><div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">Kartu RFID & Area Kampus</div>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="font-weight:700;">${t.tenant}</div>
          <div style="font-size:0.75rem;color:var(--text-muted);">${t.type}</div>
        </td>
        <td>
          <strong style="color:var(--brand-primary-light);">Kamar #${t.number}</strong>
          <div style="font-size:0.72rem;color:var(--text-muted);">Lantai ${t.floor || 1} • ${formatRupiah(t.price)}/bln</div>
        </td>
        <td>${branchBadge}</td>
        <td>
          <a href="https://wa.me/${t.phone}" target="_blank" style="color:var(--accent-blue);display:inline-flex;align-items:center;gap:4px;font-weight:600;">
            ${t.phone || '-'}
          </a>
        </td>
        <td>${t.startDate || '-'}</td>
        <td>Tgl <strong>${t.dueDay}</strong> / bln</td>
        <td>${statusLabel}</td>
        <td>
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.sendWhatsAppReminder('${t.number}')">WA Tagihan</button>
            <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.recordPaymentForRoom('${t.number}')">Bayar</button>
            <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;color:#f87171;" onclick="window.kosApp.checkoutTenant('${t.number}')">Keluar</button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);

      const mCard = document.createElement('div');
      mCard.className = 'mobile-data-card';
      mCard.innerHTML = `
        <div class="mobile-data-header">
          <span class="mobile-data-title">${t.tenant}</span>
          ${statusLabel}
        </div>
        <div class="mobile-data-row">
          <span>Cabang Kos:</span>
          <span>${isLoc2 ? '' + getBranchName('Lokasi 2') : '' + getBranchName('Lokasi 1')}</span>
        </div>
        <div class="mobile-data-row">
          <span>Kamar:</span>
          <span style="font-weight:700;">Kamar #${t.number} (${t.type})</span>
        </div>
        <div class="mobile-data-row">
          <span>WhatsApp:</span>
          <span><a href="https://wa.me/${t.phone}" target="_blank" style="color:var(--accent-blue);">${t.phone}</a></span>
        </div>
        <div class="mobile-data-row">
          <span>Jatuh Tempo:</span>
          <span>Tanggal ${t.dueDay} setiap bulan</span>
        </div>
        <div class="mobile-data-row">
          <span>Tarif Sewa:</span>
          <span style="font-weight:700;color:var(--brand-primary-light);">${formatRupiah(t.price)}</span>
        </div>
        <div style="display:flex;gap:8px;margin-top:10px;">
          <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.sendWhatsAppReminder('${t.number}')">Kirim WA</button>
          <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.recordPaymentForRoom('${t.number}')">Catat Bayar</button>
          <button type="button" class="btn-secondary" style="font-size:0.8rem;padding:7px;color:#f87171;" onclick="window.kosApp.checkoutTenant('${t.number}')" title="Penghuni Keluar"></button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada data penghuni pada filter cabang ini</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada data penghuni pada filter cabang ini</div>';
    }
  }

  function parseFinanceYearMonth(dateStr) {
    if (!dateStr) return { year: 2026, month: 1 };
    const m = String(dateStr).match(/^(\d{4})-(\d{2})/);
    if (m) return { year: parseInt(m[1], 10), month: parseInt(m[2], 10) };
    const d = new Date(dateStr);
    if (!isNaN(d.getTime())) return { year: d.getFullYear(), month: d.getMonth() + 1 };
    return { year: 2026, month: 1 };
  }

  // --- TAB: KEUANGAN (2 KOS & FILTER TAHUN OTOMATIS) ---
  function renderFinancesTable(search = '', filterType = 'all', locationFilter = activeFinanceLocationFilter) {
    activeFinanceLocationFilter = locationFilter;
    const tbody = document.getElementById('financesTableBody');
    const mobileList = document.getElementById('financesMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const selectYearEl = document.getElementById('selectFinanceYear');
    const selectedYear = parseInt(selectYearEl?.value || 2026, 10) || 2026;
    const selectMonthEl = document.getElementById('selectFinanceMonth');
    const selectedMonth = selectMonthEl ? selectMonthEl.value : 'all';
    const isMonthFiltered = (selectedMonth !== 'all');
    const monthNum = isMonthFiltered ? parseInt(selectedMonth, 10) : null;
    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const shortMonthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const periodLabel = isMonthFiltered ? `${monthNames[monthNum]} ${selectedYear}` : `Tahun ${selectedYear}`;
    const periodBadge = isMonthFiltered ? `${shortMonthNames[monthNum]} ${selectedYear}` : `${selectedYear}`;

    // Filter transaksi tahun yang dipilih
    const yearFinances = appData.finances.filter(f => parseFinanceYearMonth(f.date).year === selectedYear);

    // Target transaksi untuk KPI & Neraca (apakah 1 tahun penuh atau 1 bulan tertentu)
    const targetFinances = isMonthFiltered
      ? yearFinances.filter(f => parseFinanceYearMonth(f.date).month === monthNum)
      : yearFinances;

    // Hitung Neraca Kas Kos 1 vs Kos 2 pada periode terpilih secara real-time
    let b1Inc = 0, b1Exp = 0, b2Inc = 0, b2Exp = 0;
    targetFinances.forEach(f => {
      const fLoc = resolveFinanceLocation(f);
      const amt = Number(f.amount || 0);
      if (fLoc === 'Lokasi 2') {
        if (f.type === 'income') b2Inc += amt;
        else b2Exp += amt;
      } else {
        if (f.type === 'income') b1Inc += amt;
        else b1Exp += amt;
      }
    });

    const elB1Inc = document.getElementById('finBranch1Inc');
    const elB1Exp = document.getElementById('finBranch1Exp');
    const elB1Net = document.getElementById('finBranch1Net');
    const elB2Inc = document.getElementById('finBranch2Inc');
    const elB2Exp = document.getElementById('finBranch2Exp');
    const elB2Net = document.getElementById('finBranch2Net');

    if (elB1Inc) elB1Inc.textContent = formatRupiah(b1Inc);
    if (elB1Exp) elB1Exp.textContent = formatRupiah(b1Exp);
    if (elB1Net) elB1Net.textContent = formatRupiah(b1Inc - b1Exp);
    if (elB2Inc) elB2Inc.textContent = formatRupiah(b2Inc);
    if (elB2Exp) elB2Exp.textContent = formatRupiah(b2Exp);
    if (elB2Net) elB2Net.textContent = formatRupiah(b2Inc - b2Exp);

    // Hitung data bulanan (Jan-Des) untuk tahun & cabang terpilih (untuk kurva grafik)
    const scopedYearFinances = yearFinances.filter(f => {
      const fLoc = resolveFinanceLocation(f);
      return locationFilter === 'all' || fLoc === locationFilter;
    });

    const mInc = new Array(12).fill(0);
    const mExp = new Array(12).fill(0);
    const mNet = new Array(12).fill(0);

    scopedYearFinances.forEach(f => {
      const ym = parseFinanceYearMonth(f.date);
      const idx = Math.max(0, Math.min(11, (ym.month || 1) - 1));
      const amt = Number(f.amount || 0);
      if (f.type === 'income') mInc[idx] += amt;
      else mExp[idx] += amt;
    });
    for (let i = 0; i < 12; i++) {
      mNet[i] = mInc[i] - mExp[i];
    }

    // Sinkronkan kurva pendapatan dengan data riil tahun terpilih
    window.chartIncomeFull = mInc;
    window.chartExpenseFull = mExp;
    window.chartNetFull = mNet;
    if (typeof window.drawRevenueCurve === 'function') {
      window.drawRevenueCurve();
    }

    // Update badge periode di seluruh KPI & judul kurva
    document.querySelectorAll('.fin-selected-year-badge').forEach(el => {
      el.textContent = periodBadge;
    });
    const curveYearEl = document.getElementById('curveYearLabel');
    if (curveYearEl) {
      curveYearEl.textContent = isMonthFiltered ? `(${monthNames[monthNum]} ${selectedYear})` : `(Tahun ${selectedYear})`;
    }

    // Filter transaksi untuk KPI sesuai filter cabang
    const scopedTargetFinances = targetFinances.filter(f => {
      const fLoc = resolveFinanceLocation(f);
      return locationFilter === 'all' || fLoc === locationFilter;
    });

    const hasScopedData = scopedTargetFinances.length > 0;

    // Update 4 KPI Cards sesuai periode & filter cabang
    const kpiIncEl = document.getElementById('finKpiIncomeVal');
    const kpiExpEl = document.getElementById('finKpiExpenseVal');
    const kpiNetEl = document.getElementById('finKpiNetVal');
    const kpiAvgEl = document.getElementById('finKpiAvgVal');
    const kpiBadgeEl = document.getElementById('finKpiBranchBadge');
    const kpiUnitEl = document.getElementById('finKpiUnitCountLabel');
    const finInsightEl = document.getElementById('finInsightText');
    const finStatusCashEl = document.getElementById('finStatusCashLabel');

    if (locationFilter === 'Lokasi 1' || locationFilter === 'Lokasi 2') {
      const isB2 = locationFilter === 'Lokasi 2';
      const branchInc = hasScopedData ? (isB2 ? b2Inc : b1Inc) : 0;
      const branchExp = hasScopedData ? (isB2 ? b2Exp : b1Exp) : 0;
      const branchNet = branchInc - branchExp;
      if (kpiIncEl) kpiIncEl.textContent = formatRupiah(branchInc);
      if (kpiExpEl) kpiExpEl.textContent = formatRupiah(branchExp);
      if (kpiNetEl) kpiNetEl.textContent = formatRupiah(branchNet);
      if (kpiAvgEl) {
        kpiAvgEl.textContent = isMonthFiltered
          ? formatRupiah(branchNet)
          : formatRupiah(hasScopedData ? Math.round(branchNet / 12) : 0);
      }
      if (kpiBadgeEl) kpiBadgeEl.textContent = isB2 ? 'Khusus ' + getBranchName('Lokasi 2') : 'Khusus ' + getBranchName('Lokasi 1');
      if (kpiUnitEl) kpiUnitEl.textContent = isB2 ? '8 Unit Kamar (#201–#208)' : '8 Unit Kamar (#101–#108)';
      if (finInsightEl) {
        if (!hasScopedData) {
          finInsightEl.innerHTML = `<strong>Status Data ${periodLabel}:</strong> Belum ada transaksi keuangan yang dicatat oleh Admin untuk ${getBranchName(locationFilter)} (Data Rp 0).`;
        } else {
          finInsightEl.innerHTML = `<strong>Insight Kas ${getBranchName(locationFilter)} (${periodLabel}):</strong> Total ${scopedTargetFinances.length} transaksi tercatat dengan surplus bersih ${formatRupiah(branchNet)}.`;
        }
      }
      if (finStatusCashEl) {
        finStatusCashEl.textContent = !hasScopedData ? 'Status Kas: Data Kosong (Rp 0)' : (branchNet >= 0 ? 'Status Kas: Surplus Finansial' : 'Status Kas: Defisit Operasional');
      }
    } else {
      const totInc = hasScopedData ? (b1Inc + b2Inc) : 0;
      const totExp = hasScopedData ? (b1Exp + b2Exp) : 0;
      const totNet = totInc - totExp;
      if (kpiIncEl) kpiIncEl.textContent = formatRupiah(totInc);
      if (kpiExpEl) kpiExpEl.textContent = formatRupiah(totExp);
      if (kpiNetEl) kpiNetEl.textContent = formatRupiah(totNet);
      if (kpiAvgEl) {
        kpiAvgEl.textContent = isMonthFiltered
          ? formatRupiah(totNet)
          : formatRupiah(hasScopedData ? Math.round(totNet / 12) : 0);
      }
      if (kpiBadgeEl) kpiBadgeEl.textContent = 'Gabungan 2 Cabang';
      if (kpiUnitEl) kpiUnitEl.textContent = `16 Unit Kamar (${getBranchName('Lokasi 1')} & ${getBranchName('Lokasi 2')})`;
      if (finInsightEl) {
        if (!hasScopedData) {
          finInsightEl.innerHTML = `<strong>Status Data ${periodLabel}:</strong> Belum ada transaksi keuangan yang dicatat oleh Admin pada periode ini (Seluruh indikator berada di titik Rp 0).`;
        } else {
          finInsightEl.innerHTML = `<strong>Insight Keuangan 2 Cabang (${periodLabel}):</strong> Tercatat ${scopedTargetFinances.length} transaksi resmi dengan pemasukan ${formatRupiah(totInc)} dan laba bersih ${formatRupiah(totNet)}.`;
        }
      }
      if (finStatusCashEl) {
        finStatusCashEl.textContent = !hasScopedData ? 'Status Kas: Data Kosong (Rp 0)' : (totNet >= 0 ? 'Status Kas: Surplus Finansial' : 'Status Kas: Defisit Operasional');
      }
    }

    const q = search.toLowerCase();
    const filtered = targetFinances.filter(f => {
      const fLoc = resolveFinanceLocation(f);
      const matchLoc = (locationFilter === 'all' || fLoc === locationFilter);
      const matchSearch = !q ||
                          (f.desc || '').toLowerCase().includes(q) ||
                          (f.id || '').toLowerCase().includes(q) ||
                          (f.category || '').toLowerCase().includes(q) ||
                          fLoc.toLowerCase().includes(q);
      const matchType = filterType === 'all' || f.type === filterType;
      return matchLoc && matchSearch && matchType;
    });

    filtered.forEach(trx => {
      const isIncome = trx.type === 'income';
      const colorClass = isIncome ? '#10b981' : '#ef4444';
      const prefix = isIncome ? '+ ' : '- ';
      const fLoc = resolveFinanceLocation(trx);
      const isLoc2 = fLoc === 'Lokasi 2';
      const branchPill = isLoc2
        ? `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">${getBranchName('Lokasi 2')}</span>`
        : `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">${getBranchName('Lokasi 1')}</span>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-family:monospace;font-size:0.85rem;font-weight:700;">${trx.id}</td>
        <td>${trx.date}</td>
        <td>${branchPill}</td>
        <td style="font-weight:600;">${trx.desc}</td>
        <td><span class="feature-pill">${trx.category}</span></td>
        <td style="font-weight:800;color:${colorClass};">${prefix}${formatRupiah(trx.amount)}</td>
        <td>${trx.method}</td>
        <td>
          ${trx.proofImage ? `
            <a href="${trx.proofImage}" target="_blank" class="btn-secondary" style="padding:3px 8px;font-size:0.75rem;color:#2563eb;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;margin-right:4px;">
              Foto Bukti
            </a>
          ` : ''}
          <button type="button" class="btn-secondary" style="padding:3px 8px;font-size:0.75rem;" onclick="window.kosApp.showReceiptModal('${trx.id}')">
            Kuitansi
          </button>
        </td>
      `;
      tbody.appendChild(tr);

      const mCard = document.createElement('div');
      mCard.className = 'mobile-data-card';
      mCard.innerHTML = `
        <div class="mobile-data-header">
          <span class="mobile-data-title" style="font-size:0.88rem;">${trx.desc}</span>
          <span style="font-weight:700;color:${colorClass};font-size:0.95rem;">${prefix}${formatRupiah(trx.amount)}</span>
        </div>
        <div class="mobile-data-row">
          <span>Lokasi Cabang:</span>
          <span>${isLoc2 ? '' + getBranchName('Lokasi 2') : '' + getBranchName('Lokasi 1')}</span>
        </div>
        <div class="mobile-data-row">
          <span>Kategori & Tanggal:</span>
          <span>${trx.category} • ${trx.date}</span>
        </div>
        <div class="mobile-data-row">
          <span>Metode:</span>
          <span>${trx.method}</span>
        </div>
        <div style="margin-top:6px;display:flex;justify-content:flex-end;">
          <button type="button" class="btn-secondary" style="padding:4px 10px;font-size:0.78rem;" onclick="window.kosApp.showReceiptModal('${trx.id}')">
            Lihat Kuitansi
          </button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      const emptyMsg = targetFinances.length === 0
        ? `Belum ada data transaksi yang diisi oleh Admin pada ${periodLabel} (Data = Rp 0).`
        : `Tidak ada data transaksi yang sesuai dengan filter pada ${periodLabel}.`;
      tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:28px 20px;color:var(--text-muted);font-weight:600;">${emptyMsg}</td></tr>`;
      mobileList.innerHTML = `<div style="text-align:center;color:var(--text-muted);padding:24px 16px;font-weight:600;">${emptyMsg}</div>`;
    }
  }

  function getFinanceReportPayload() {
    const selectYearEl = document.getElementById('selectFinanceYear');
    const selectedYear = parseInt(selectYearEl?.value || 2026, 10) || 2026;
    const selectMonthEl = document.getElementById('selectFinanceMonth');
    const selectedMonth = selectMonthEl ? selectMonthEl.value : 'all';
    const isMonthFiltered = (selectedMonth !== 'all');
    const monthNum = isMonthFiltered ? parseInt(selectedMonth, 10) : null;
    const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const shortMonthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthName = isMonthFiltered ? monthNames[monthNum] : 'Semua Bulan (1 Tahun)';
    const periodTitle = isMonthFiltered ? `${monthName.toUpperCase()} ${selectedYear}` : `TAHUN ${selectedYear}`;

    const locFilter = activeFinanceLocationFilter || 'all';
    const yearFinances = appData.finances.filter(f => parseFinanceYearMonth(f.date).year === selectedYear);
    const targetFinances = isMonthFiltered
      ? yearFinances.filter(f => parseFinanceYearMonth(f.date).month === monthNum)
      : yearFinances;

    let b1Inc = 0, b1Exp = 0, b2Inc = 0, b2Exp = 0;
    targetFinances.forEach(f => {
      const fLoc = resolveFinanceLocation(f);
      const amt = Number(f.amount || 0);
      if (fLoc === 'Lokasi 2') {
        if (f.type === 'income') b2Inc += amt;
        else b2Exp += amt;
      } else {
        if (f.type === 'income') b1Inc += amt;
        else b1Exp += amt;
      }
    });

    const scoped = targetFinances.filter(f => {
      const fLoc = resolveFinanceLocation(f);
      return locFilter === 'all' || fLoc === locFilter;
    });

    const mInc = new Array(12).fill(0);
    const mExp = new Array(12).fill(0);
    const mNet = new Array(12).fill(0);

    const yearScoped = yearFinances.filter(f => {
      const fLoc = resolveFinanceLocation(f);
      return locFilter === 'all' || fLoc === locFilter;
    });

    yearScoped.forEach(f => {
      const ym = parseFinanceYearMonth(f.date);
      const idx = Math.max(0, Math.min(11, (ym.month || 1) - 1));
      const amt = Number(f.amount || 0);
      if (f.type === 'income') {
        mInc[idx] += amt;
      } else {
        mExp[idx] += amt;
      }
    });
    for (let i = 0; i < 12; i++) {
      mNet[i] = mInc[i] - mExp[i];
    }

    let totalInc = 0, totalExp = 0;
    scoped.forEach(f => {
      const amt = Number(f.amount || 0);
      if (f.type === 'income') totalInc += amt;
      else totalExp += amt;
    });
    const totalNet = totalInc - totalExp;
    const avgMonthly = isMonthFiltered ? totalNet : (yearScoped.length > 0 ? Math.round(totalNet / 12) : 0);

    return {
      year: selectedYear,
      selectedMonth,
      isMonthFiltered,
      monthNum,
      monthName,
      periodTitle,
      locationFilter: locFilter,
      branchLabel: locFilter === 'Lokasi 1'
        ? getBranchName('Lokasi 1')
        : (locFilter === 'Lokasi 2' ? getBranchName('Lokasi 2') : `Gabungan 2 Cabang (${getBranchName('Lokasi 1')} & ${getBranchName('Lokasi 2')})`),
      branch1Name: getBranchName('Lokasi 1'),
      branch2Name: getBranchName('Lokasi 2'),
      kosName: appData.settings?.kos_name || 'Kos Mas Robi',
      ownerName: appData.settings?.owner_name || 'Mas Robi',
      kosAddress: appData.settings?.kos_address || 'Sleman, DI Yogyakarta',
      b1Inc,
      b1Exp,
      b1Net: b1Inc - b1Exp,
      b2Inc,
      b2Exp,
      b2Net: b2Inc - b2Exp,
      totalInc,
      totalExp,
      totalNet,
      avgMonthly,
      mInc,
      mExp,
      mNet,
      transactions: scoped.map(f => Object.assign({}, f, {
        resolvedLocation: resolveFinanceLocation(f),
        branchName: getBranchName(resolveFinanceLocation(f))
      }))
    };
  }

  // --- TAB: KELUHAN & SERVIS (2 KOS) ---
  function renderMaintenanceTable(search = '', filterStatus = 'all', locationFilter = activeMaintLocationFilter) {
    activeMaintLocationFilter = locationFilter;
    const tbody = document.getElementById('maintenanceTableBody');
    const mobileList = document.getElementById('maintenanceMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const q = search.toLowerCase();
    const filtered = appData.maintenance.filter(m => {
      const mLoc = resolveMaintenanceLocation(m);
      const matchLoc = (locationFilter === 'all' || mLoc === locationFilter);
      const matchSearch = !q ||
                          (m.issue || '').toLowerCase().includes(q) ||
                          String(m.roomId).toLowerCase().includes(q) ||
                          (m.reporter || '').toLowerCase().includes(q) ||
                          mLoc.toLowerCase().includes(q);
      const matchStatus = filterStatus === 'all' || m.status === filterStatus;
      return matchLoc && matchSearch && matchStatus;
    });

    filtered.forEach(item => {
      let statusBadge = '<span class="status-badge repair">Menunggu</span>';
      if (item.status === 'progress') statusBadge = '<span class="status-badge due">Dikerjakan</span>';
      if (item.status === 'done') statusBadge = '<span class="status-badge occupied">Selesai</span>';

      const mLoc = resolveMaintenanceLocation(item);
      const isLoc2 = mLoc === 'Lokasi 2';
      const branchTechBadge = isLoc2
        ? `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#ccfbf1;color:#0a5661;border:1px solid #5eead4;">${getBranchName('Lokasi 2')}</span><div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">PIC: Mas Dani (Smart System)</div>`
        : `<span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:800;padding:3px 9px;border-radius:999px;background:#dcfce7;color:#059669;border:1px solid #86efac;">${getBranchName('Lokasi 1')}</span><div style="font-size:0.7rem;color:var(--text-secondary);margin-top:3px;">PIC: Pak Budi (Utilitas)</div>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-family:monospace;font-size:0.85rem;font-weight:700;">#${item.id}</td>
        <td>
          <strong>Kamar #${item.roomId}</strong>
          <div style="font-size:0.75rem;color:var(--text-secondary);">Pelapor: ${item.reporter}</div>
        </td>
        <td>${branchTechBadge}</td>
        <td style="max-width:300px;line-height:1.45;">${item.issue}</td>
        <td>${item.date}</td>
        <td><span class="feature-pill" style="color:${item.priority === 'Mendesak' || item.priority === 'Tinggi' ? '#ef4444' : 'inherit'};font-weight:700;">${item.priority}</span></td>
        <td>${statusBadge}</td>
        <td>
          <select class="form-control" style="padding:4px 8px;font-size:0.78rem;" onchange="window.kosApp.updateRepairStatus('${item.id}', this.value)">
            <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>Menunggu</option>
            <option value="progress" ${item.status === 'progress' ? 'selected' : ''}>Dikerjakan</option>
            <option value="done" ${item.status === 'done' ? 'selected' : ''}>Selesai</option>
          </select>
        </td>
      `;
      tbody.appendChild(tr);

      const mCard = document.createElement('div');
      mCard.className = 'mobile-data-card';
      mCard.innerHTML = `
        <div class="mobile-data-header">
          <span class="mobile-data-title">Kamar #${item.roomId} (${isLoc2 ? getBranchName('Lokasi 2') : getBranchName('Lokasi 1')})</span>
          ${statusBadge}
        </div>
        <div style="font-size:0.85rem;color:var(--text-secondary);">${item.issue}</div>
        <div class="mobile-data-row" style="margin-top:6px;">
          <span>Cabang & Teknisi:</span>
          <span>${isLoc2 ? '' + getBranchName('Lokasi 2') + ' • Mas Dani' : '' + getBranchName('Lokasi 1') + ' • Pak Budi'}</span>
        </div>
        <div class="mobile-data-row">
          <span>Pelapor / Tgl:</span>
          <span>${item.reporter} • ${item.date}</span>
        </div>
        <div style="margin-top:8px;">
          <select class="form-control" style="padding:6px;font-size:0.82rem;" onchange="window.kosApp.updateRepairStatus('${item.id}', this.value)">
            <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>Status: Menunggu</option>
            <option value="progress" ${item.status === 'progress' ? 'selected' : ''}>Status: Sedang Dikerjakan</option>
            <option value="done" ${item.status === 'done' ? 'selected' : ''}>Status: Selesai</option>
          </select>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada tiket keluhan pada filter cabang ini</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada tiket keluhan pada filter cabang ini</div>';
    }
  }

  // --- WHATSAPP REMINDER GENERATOR ---
  function sendWhatsAppReminder(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room || !room.tenant) {
      showToast('Kamar ini belum memiliki penghuni!', 'warning');
      return;
    }

    if (!room.phone) {
      showToast('Nomor WhatsApp belum tercatat!', 'warning');
      return;
    }

    let cleanPhone = room.phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
      cleanPhone = '62' + cleanPhone.slice(1);
    }

    const set = appData.settings;
    const msg = 
`Halo Kak *${room.tenant}* ,

Semoga harinya menyenangkan!
Kami dari pengelola *${set.kos_name || 'Kos Mas Robi'}* menginfokan seputar tagihan sewa kamar:

*Unit:* Kamar #${room.number} (${room.type})
*Nominal Sewa:* ${formatRupiah(room.price)}
*Jatuh Tempo:* Tanggal ${room.dueDay} bulan ini

Pembayaran dapat ditransfer ke:
*${set.bank_name || 'BCA'}*
No. Rek: *${set.bank_account || '-'}*
A.N: *${set.bank_holder || '-'}*

Mohon konfirmasi dengan mengirimkan bukti transfer jika sudah melakukan pembayaran ya Kak. Terima kasih banyak atas kerjasamanya! 

- Salam Hangat,
*${set.owner_name || 'Mas Robi'}* (Pengelola)`;

    const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
    window.open(waUrl, '_blank');
    showToast(`Membuka WhatsApp untuk Kak ${room.tenant}...`, 'info');
  }

  // --- MODAL DIALOGS CONTROLLERS ---
  function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('open');
      modal.style.display = 'flex';
      modal.style.pointerEvents = 'auto';
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('open');
      modal.style.display = 'none';
      modal.style.pointerEvents = 'none';
      document.body.style.overflow = '';
    }
  }

  window.openModal = openModal;
  window.closeModal = closeModal;

  function initModalDismissHandlers() {
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-close-modal');
        closeModal(id);
      });
    });

    document.querySelectorAll('.modal-backdrop, .kmr-logout-overlay').forEach(backdrop => {
      backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
          closeModal(backdrop.id);
        }
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.open, .kmr-logout-overlay.open').forEach(m => closeModal(m.id));
      }
    });
  }

  // --- ROOM DETAIL MODAL ---
  function openRoomDetailModal(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;

    document.getElementById('mRoomDetailTitle').textContent = `Detail Kamar #${room.number} (${room.type})`;
    const locBadge = document.getElementById('mRoomDetailLocBadge');
    if (locBadge) {
      if (room.location === 'Lokasi 2') {
        locBadge.innerHTML = '' + getBranchName('Lokasi 2');
        locBadge.style.color = '#7dd3fc';
      } else {
        locBadge.innerHTML = '' + getBranchName('Lokasi 1');
        locBadge.style.color = '#a7f3d0';
      }
    }

    const body = document.getElementById('mRoomDetailBody');
    const footer = document.getElementById('mRoomDetailFooter');

    const facilitiesHtml = (room.facilities || []).map(f => `<span class="feature-pill" style="padding:4px 10px;font-size:0.8rem;background:#f0fdfa;color:#0a5661;border:1px solid #ccfbf1;border-radius:6px;font-weight:600;">${f}</span>`).join(' ');

    const roomImgSrc = room.image ? (room.image.startsWith('http') || room.image.startsWith('/') ? room.image : '/' + room.image) : '/assets/images/room_sample.jpg';

    body.innerHTML = `
      <div style="margin-bottom:14px;border-radius:16px;overflow:hidden;max-height:200px;border:1px solid var(--border-color);position:relative;box-shadow:0 4px 14px rgba(0,0,0,0.06);">
        <img src="${roomImgSrc}" alt="Foto Kamar #${room.number}" style="width:100%;height:200px;object-fit:cover;display:block;">
        <span style="position:absolute;bottom:10px;left:10px;background:rgba(3,35,40,0.85);backdrop-filter:blur(4px);color:#ffffff;font-size:0.75rem;font-weight:700;padding:4px 12px;border-radius:999px;">Foto Kamar #${room.number}</span>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:12px;">
        <div>
          <div style="font-size:0.8rem;color:var(--text-muted);font-weight:600;">Status Saat Ini</div>
          <div style="margin-top:4px;">${getStatusBadge(room.status)}</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:0.8rem;color:var(--text-muted);font-weight:600;">Harga Sewa / Bulan</div>
          <div style="font-size:1.25rem;font-weight:800;color:#0a5661;">${formatRupiah(room.price)}</div>
        </div>
      </div>

      <div style="background:#f8fafc;padding:14px;border-radius:14px;border:1px solid #e2e8f0;">
        <h4 style="font-size:0.88rem;margin-bottom:8px;color:#334155;font-weight:700;">Informasi Penghuni:</h4>
        ${room.tenant ? `
          <div style="font-size:1.05rem;font-weight:800;color:#0f172a;">${room.tenant}</div>
          <div style="font-size:0.85rem;color:var(--text-secondary);margin-top:4px;">
            WhatsApp: <a href="https://wa.me/${room.phone}" target="_blank" style="color:#0a5661;font-weight:700;text-decoration:none;">${room.phone}</a>
          </div>
          <div style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">
            Mulai Masuk: ${room.startDate || '-'} • Jatuh Tempo Tiap Tgl <strong>${room.dueDay}</strong>
          </div>
        ` : `
          <p style="color:var(--text-muted);font-size:0.88rem;margin:0;">Kamar ini saat ini belum ditempati penghuni.</p>
          <button type="button" class="btn-primary" style="margin-top:10px;font-size:0.82rem;padding:7px 16px;border-radius:10px;font-weight:700;" onclick="window.kosApp.assignNewTenantToRoom('${room.number}')">
            + Isi Penghuni Baru di Kamar Ini
          </button>
        `}
      </div>

      <div>
        <h4 style="font-size:0.88rem;margin-bottom:8px;color:#334155;font-weight:700;">Fasilitas Lengkap Kamar:</h4>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">${facilitiesHtml}</div>
      </div>

      <div class="form-group" style="margin-top:6px;">
        <label for="changeRoomStatusSelect" style="font-weight:700;font-size:0.82rem;color:#334155;">Ubah Cepat Status Kamar:</label>
        <select id="changeRoomStatusSelect" class="form-control" style="border-radius:10px;font-weight:700;" onchange="window.kosApp.changeRoomStatusDirect('${room.number}', this.value)">
          <option value="available" ${room.status === 'available' ? 'selected' : ''}>Kosong (Tersedia)</option>
          <option value="occupied" ${room.status === 'occupied' ? 'selected' : ''}>Terisi (Lunas)</option>
          <option value="due" ${room.status === 'due' ? 'selected' : ''}>Terisi (Jatuh Tempo)</option>
          <option value="repair" ${room.status === 'repair' ? 'selected' : ''}>Dalam Perbaikan / Servis</option>
        </select>
      </div>

      <!-- Custom Interior & Dynamic Budget Admin Section -->
      <div style="background: #f0fdfa; border: 1.5px solid #ccfbf1; border-radius: 14px; padding: 16px; margin-top: 10px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
          <h4 style="font-size:0.88rem;font-weight:800;color:#0a5661;margin:0;display:flex;align-items:center;gap:6px;">
            Custom Interior & Anggaran
          </h4>
          <span style="font-size:0.72rem;padding:3px 10px;border-radius:999px;font-weight:800;background:#ccfbf1;color:#0f766e;">
            ${(room.custom_interior_status || 'NONE').toUpperCase()}
          </span>
        </div>

        ${room.custom_interior_request ? `
          <div style="font-size:0.82rem;color:var(--text-secondary);background:#ffffff;padding:10px 12px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:10px;">
            <strong>Permintaan Pelanggan (${room.booked_by_name || 'Penghuni'}):</strong><br>${room.custom_interior_request}
          </div>
        ` : `<p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:8px;">Belum ada catatan custom interior khusus dari penyewa.</p>`}

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:8px;">
          <div>
            <label style="font-size:0.72rem;font-weight:700;color:#0f766e;display:block;margin-bottom:4px;">Atur Anggaran / Budget (Rp):</label>
            <input type="number" id="detailRoomCustomBudget" class="form-control" value="${room.custom_interior_budget || 0}" step="10000" style="padding:7px 10px;font-size:0.84rem;border-radius:8px;">
          </div>
          <div>
            <label style="font-size:0.72rem;font-weight:700;color:#0f766e;display:block;margin-bottom:4px;">Status Custom Interior:</label>
            <select id="detailRoomCustomStatus" class="form-control" style="padding:7px 10px;font-size:0.84rem;border-radius:8px;font-weight:600;">
              <option value="none" ${(!room.custom_interior_status || room.custom_interior_status === 'none') ? 'selected' : ''}>Tidak Ada (None)</option>
              <option value="pending" ${room.custom_interior_status === 'pending' ? 'selected' : ''}>Menunggu Review</option>
              <option value="approved" ${room.custom_interior_status === 'approved' ? 'selected' : ''}>Disetujui (Approved)</option>
              <option value="in_progress" ${room.custom_interior_status === 'in_progress' ? 'selected' : ''}>Dikerjakan</option>
              <option value="completed" ${room.custom_interior_status === 'completed' ? 'selected' : ''}>Selesai</option>
              <option value="rejected" ${room.custom_interior_status === 'rejected' ? 'selected' : ''}>Ditolak</option>
            </select>
          </div>
        </div>

        <div>
          <label style="font-size:0.72rem;font-weight:700;color:#0f766e;display:block;margin-bottom:4px;">Catatan Mas Robi untuk Pelanggan:</label>
          <input type="text" id="detailRoomCustomNotes" class="form-control" value="${room.custom_interior_admin_notes || ''}" placeholder="Catatan respons terkait bahan/waktu pengerjaan..." style="padding:7px 10px;font-size:0.84rem;margin-bottom:8px;border-radius:8px;">
        </div>

        <button type="button" class="btn-primary" style="font-size:0.8rem;padding:9px 14px;width:100%;margin-top:4px;border-radius:10px;font-weight:800;" onclick="window.kosApp.saveCustomInteriorDirect('${room.number}')">
          Simpan Anggaran & Status Custom Interior
        </button>
      </div>
    `;

    footer.innerHTML = `
      <button type="button" class="btn-secondary" data-close-modal="modalRoomDetail" style="border-radius:10px;padding:8px 18px;">Tutup</button>
      ${room.tenant ? `
        <button type="button" class="btn-primary" style="border-radius:10px;padding:8px 18px;font-weight:800;" onclick="window.kosApp.sendWhatsAppReminder('${room.number}')">
          Kirim WA Tagihan
        </button>
      ` : ''}
    `;

    openModal('modalRoomDetail');
  }

  async function changeRoomStatusDirect(roomNumber, newStatus) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;
    room.status = newStatus;

    try {
      await fetch(`/api/rooms/${roomNumber}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ status: newStatus })
      });
    } catch (e) {
      console.warn('Status updated locally');
    }

    renderAllViews();
    showToast(`Status Kamar #${roomNumber} diperbarui jadi ${newStatus}!`, 'success');
  }

  async function saveCustomInteriorDirect(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;

    const budget = Number(document.getElementById('detailRoomCustomBudget')?.value || 0);
    const status = document.getElementById('detailRoomCustomStatus')?.value || 'none';
    const notes = document.getElementById('detailRoomCustomNotes')?.value?.trim() || '';

    try {
      const res = await fetch(`/api/rooms/${roomNumber}/custom-interior`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          custom_interior_budget: budget,
          custom_interior_status: status,
          custom_interior_admin_notes: notes
        })
      });

      if (res.ok) {
        const json = await res.json();
        room.custom_interior_budget = budget;
        room.custom_interior_status = status;
        room.custom_interior_admin_notes = notes;
        renderAllViews();
        closeModal('modalRoomDetail');
        showToast(`Anggaran Custom Interior Kamar #${roomNumber} berhasil disimpan: Rp ${budget.toLocaleString('id-ID')} (${status})`, 'success');
        return;
      }
    } catch (e) {
      console.warn('API error, saving locally', e);
    }

    room.custom_interior_budget = budget;
    room.custom_interior_status = status;
    room.custom_interior_admin_notes = notes;
    renderAllViews();
    closeModal('modalRoomDetail');
    showToast(`Data Custom Interior Kamar #${roomNumber} disimpan secara lokal!`, 'success');
  }

  // --- ADD / EDIT ROOM MODAL ---
  function fillDefaultRoomImages() {
    const defaultImgs = [
      'assets/images/rooms/room_1.jpg',
      'assets/images/rooms/room_2.jpg',
      'assets/images/rooms/room_3.jpg',
      ''
    ];
    for (let i = 1; i <= 4; i++) {
      const input = document.getElementById(`roomImageInput${i}`);
      const preview = document.getElementById(`roomImgPreview${i}`);
      if (input) input.value = defaultImgs[i - 1];
      if (preview && defaultImgs[i - 1]) {
        preview.src = '/' + defaultImgs[i - 1];
        if (i === 4) preview.style.filter = 'brightness(0.4)';
      }
    }
  }

  function syncRoomTypesForLocation(loc, selectedType = null) {
    const typeSelect = document.getElementById('roomTypeInput');
    const facInput = document.getElementById('roomFacilitiesInput');
    if (!typeSelect) return;

    if (loc === 'Lokasi 2') {
      typeSelect.innerHTML = `
        <option value="Executive Suite (Balkon)">Executive Suite (Balkon)</option>
        <option value="Superior AC Plus">Superior AC Plus</option>
        <option value="Studio Mezzanine">Studio Mezzanine</option>
      `;
      if (!selectedType && facInput) {
        facInput.value = 'Smart Digital Door Lock, Balkon Pribadi, Smart Android TV, AC Inverter, Meja Kerja Ergonomis, KM Dalam, WiFi 100 Mbps';
      }
    } else {
      typeSelect.innerHTML = `
        <option value="VIP AC (KM Dalam)">VIP AC (KM Dalam)</option>
        <option value="Deluxe AC">Deluxe AC</option>
        <option value="Standard Fan (KM Luar)">Standard Fan (KM Luar)</option>
      `;
      if (!selectedType && facInput) {
        facInput.value = 'AC Dingin & Hemat Listrik, Kasur Springbed, Lemari 2 Pintu, Meja Belajar, KM Dalam, Water Heater, WiFi Fiber';
      }
    }

    if (selectedType) {
      if (![...typeSelect.options].some(o => o.value === selectedType)) {
        const opt = document.createElement('option');
        opt.value = selectedType;
        opt.textContent = selectedType;
        typeSelect.appendChild(opt);
      }
      typeSelect.value = selectedType;
    }
  }

  function editRoom(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;

    document.getElementById('mRoomFormTitle').textContent = `Edit Kamar #${room.number}`;
    document.getElementById('roomEditId').value = room.id;
    document.getElementById('roomNumberInput').value = room.number;
    document.getElementById('roomNumberInput').disabled = true;
    document.getElementById('roomFloorInput').value = room.floor;

    const locInput = document.getElementById('roomLocationInput');
    const roomLoc = room.location || 'Lokasi 1';
    if (locInput) {
      locInput.value = roomLoc;
      syncRoomTypesForLocation(roomLoc, room.type);
    } else {
      document.getElementById('roomTypeInput').value = room.type;
    }

    document.getElementById('roomPriceInput').value = room.price;
    document.getElementById('roomFacilitiesInput').value = (room.facilities || []).join(', ');
    document.getElementById('roomStatusInput').value = room.status;

    // Populate Custom Interior & Budget fields
    const customStatus = document.getElementById('roomCustomInteriorStatusInput');
    const customBudget = document.getElementById('roomCustomInteriorBudgetInput');
    const customReq = document.getElementById('roomCustomInteriorRequestInput');
    const customNotes = document.getElementById('roomCustomInteriorNotesInput');
    if (customStatus) customStatus.value = room.custom_interior_status || 'none';
    if (customBudget) customBudget.value = room.custom_interior_budget || 0;
    if (customReq) customReq.value = room.custom_interior_request || '';
    if (customNotes) customNotes.value = room.custom_interior_admin_notes || '';

    // Populate up to 4 images
    const imgs = (room.images && room.images.length > 0) ? room.images : (room.images_list || [
      room.image || ''
    ]);

    for (let i = 1; i <= 4; i++) {
      const val = imgs[i - 1] || '';
      if (typeof window.setRoomPhotoSlot === 'function') {
        window.setRoomPhotoSlot(i, val);
      } else {
        const input = document.getElementById(`roomImageInput${i}`);
        if (input) input.value = val;
      }
    }

    openModal('modalAddRoom');
  }

  function initRoomForm() {
    const form = document.getElementById('formAddRoom');
    const btnNew = document.getElementById('btnAddNewRoom');
    const locInput = document.getElementById('roomLocationInput');

    locInput?.addEventListener('change', (e) => {
      syncRoomTypesForLocation(e.target.value);
    });

    btnNew?.addEventListener('click', () => {
      document.getElementById('mRoomFormTitle').textContent = 'Tambah Master Kamar Baru';
      document.getElementById('roomEditId').value = '';
      document.getElementById('roomNumberInput').value = '';
      document.getElementById('roomNumberInput').disabled = false;
      document.getElementById('roomFloorInput').value = '1';
      document.getElementById('roomPriceInput').value = '1500000';
      
      if (locInput) {
        locInput.value = 'Lokasi 1';
        syncRoomTypesForLocation('Lokasi 1');
      } else {
        document.getElementById('roomFacilitiesInput').value = 'AC, Springbed, Lemari, Meja Belajar, KM Dalam';
      }

      document.getElementById('roomStatusInput').value = 'available';
      const customStatus = document.getElementById('roomCustomInteriorStatusInput');
      const customBudget = document.getElementById('roomCustomInteriorBudgetInput');
      const customReq = document.getElementById('roomCustomInteriorRequestInput');
      const customNotes = document.getElementById('roomCustomInteriorNotesInput');
      if (customStatus) customStatus.value = 'none';
      if (customBudget) customBudget.value = 0;
      if (customReq) customReq.value = '';
      if (customNotes) customNotes.value = '';
      
      // Kosongkan seluruh slot foto sampai admin mengupload foto baru
      for (let i = 1; i <= 4; i++) {
        if (typeof window.setRoomPhotoSlot === 'function') {
          window.setRoomPhotoSlot(i, '');
        } else {
          const input = document.getElementById(`roomImageInput${i}`);
          if (input) input.value = '';
        }
      }
      openModal('modalAddRoom');
    });

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const editId = document.getElementById('roomEditId').value;
      const number = document.getElementById('roomNumberInput').value.trim();
      const floor = Number(document.getElementById('roomFloorInput').value);
      const location = document.getElementById('roomLocationInput')?.value || 'Lokasi 1';
      const type = document.getElementById('roomTypeInput').value;
      const price = Number(document.getElementById('roomPriceInput').value);
      const facilities = document.getElementById('roomFacilitiesInput').value.split(',').map(s => s.trim()).filter(Boolean);
      const status = document.getElementById('roomStatusInput').value;
      
      const img1 = document.getElementById('roomImageInput1')?.value.trim() || '';
      const img2 = document.getElementById('roomImageInput2')?.value.trim() || '';
      const img3 = document.getElementById('roomImageInput3')?.value.trim() || '';
      const img4 = document.getElementById('roomImageInput4')?.value.trim() || '';
      const images = [img1, img2, img3, img4].filter(Boolean);
      const mainImage = images[0] || (editId ? '' : 'assets/images/rooms/room_1.jpg');

      const custom_interior_status = document.getElementById('roomCustomInteriorStatusInput')?.value || 'none';
      const custom_interior_budget = Number(document.getElementById('roomCustomInteriorBudgetInput')?.value || 0);
      const custom_interior_request = document.getElementById('roomCustomInteriorRequestInput')?.value.trim() || null;
      const custom_interior_admin_notes = document.getElementById('roomCustomInteriorNotesInput')?.value.trim() || null;

      try {
        const res = await fetch('/api/rooms', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            id: editId || null,
            number,
            floor,
            type,
            location,
            price,
            facilities,
            status,
            image: mainImage || img1 || (images.length > 0 ? images[0] : null),
            images,
            custom_interior_status,
            custom_interior_budget,
            custom_interior_request,
            custom_interior_admin_notes
          })
        });

        if (res.ok) {
          showToast(editId ? `Kamar #${number} berhasil diperbarui!` : `Kamar baru #${number} berhasil ditambahkan!`, 'success');
          await fetchServerData();
          closeModal('modalAddRoom');
          return;
        }
      } catch (err) {
        console.error(err);
      }

      // Local fallback
      if (editId) {
        const room = appData.rooms.find(r => r.id === editId);
        if (room) {
          room.floor = floor;
          room.type = type;
          room.price = price;
          room.facilities = facilities;
          room.status = status;
        }
      } else {
        appData.rooms.push({
          id: String(Date.now()),
          number,
          floor,
          type,
          price,
          status,
          facilities,
          tenant: null,
          phone: '',
          startDate: '',
          dueDay: 1
        });
      }

      renderAllViews();
      closeModal('modalAddRoom');
    });
  }

  // --- ADD TENANT MODAL ---
  function openAddTenantModal(targetRoomNumber = null) {
    const select = document.getElementById('tenantSelectRoom');
    select.innerHTML = '';

    const loc1Rooms = appData.rooms.filter(r => (r.location || 'Lokasi 1') === 'Lokasi 1');
    const loc2Rooms = appData.rooms.filter(r => r.location === 'Lokasi 2');

    if (loc1Rooms.length > 0) {
      const grp1 = document.createElement('optgroup');
      grp1.label = '' + getBranchName('Lokasi 1');
      loc1Rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.setAttribute('data-number', r.number);
        opt.textContent = `Kamar #${r.number} (${r.type}) - ${r.status === 'available' ? 'Tersedia' : 'Terisi: ' + (r.tenant || '')}`;
        if (targetRoomNumber && r.number === targetRoomNumber) opt.selected = true;
        grp1.appendChild(opt);
      });
      select.appendChild(grp1);
    }

    if (loc2Rooms.length > 0) {
      const grp2 = document.createElement('optgroup');
      grp2.label = '' + getBranchName('Lokasi 2');
      loc2Rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.setAttribute('data-number', r.number);
        opt.textContent = `Kamar #${r.number} (${r.type}) - ${r.status === 'available' ? 'Tersedia' : 'Terisi: ' + (r.tenant || '')}`;
        if (targetRoomNumber && r.number === targetRoomNumber) opt.selected = true;
        grp2.appendChild(opt);
      });
      select.appendChild(grp2);
    }

    function updateRentTariff() {
      const selectedId = select.value;
      const found = appData.rooms.find(r => r.id === selectedId);
      if (found) {
        document.getElementById('tenantRentAmount').value = found.price;
      }
    }
    select.onchange = updateRentTariff;
    updateRentTariff();

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tenantStartDate').value = today;

    const pwdField = document.getElementById('tenantPassword');
    if (pwdField && !pwdField.value) {
      pwdField.value = 'kost' + Math.floor(1000 + Math.random() * 9000);
    }

    openModal('modalAddTenant');
  }

  function initTenantForm() {
    const form = document.getElementById('formAddTenant');
    const btnQuick = document.getElementById('btnQuickAdd');
    const btnNew = document.getElementById('btnAddNewTenant');

    btnQuick?.addEventListener('click', () => openAddTenantModal());
    btnNew?.addEventListener('click', () => openAddTenantModal());

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('tenantFullName').value.trim();
      const phone = document.getElementById('tenantPhone').value.trim();
      const email = document.getElementById('tenantEmail')?.value.trim() || '';
      const password = document.getElementById('tenantPassword')?.value.trim() || '';
      const idCard = document.getElementById('tenantIdNumber').value.trim();
      const roomId = document.getElementById('tenantSelectRoom').value;
      const startDate = document.getElementById('tenantStartDate').value;
      const dueDay = Number(document.getElementById('tenantDueDay').value);
      const rentAmount = Number(document.getElementById('tenantRentAmount').value);
      const emergencyContact = document.getElementById('tenantEmergencyContact').value.trim();

      try {
        const res = await fetch('/api/tenants', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            name,
            email,
            password,
            phone,
            id_card: idCard,
            room_id: roomId,
            start_date: startDate,
            due_day: dueDay,
            rent_amount: rentAmount,
            emergency_contact: emergencyContact
          })
        });

        const resData = await res.json();

        if (res.ok && resData.success) {
          showToast(`Penghuni ${name} dan akun login berhasil didaftarkan!`, 'success');
          await fetchServerData();
          closeModal('modalAddTenant');
          form.reset();

          if (resData.user && resData.user.wa_creds_url) {
            const sendWa = confirm(`Akun penghuni ${name} berhasil dibuat!\n\nEmail Login: ${resData.user.email}\nPassword: ${resData.user.plain_password}\n\nKirim informasi akun ini ke WhatsApp penghuni sekarang?`);
            if (sendWa) {
              window.open(resData.user.wa_creds_url, '_blank');
            }
          }
          return;
        } else {
          alert(resData.message || 'Gagal mendaftarkan penghuni. Pastikan email dan data sudah sesuai.');
        }
      } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan koneksi saat mendaftarkan penghuni.');
      }

      // Local fallback
      const room = appData.rooms.find(r => r.id === roomId);
      if (room) {
        room.tenant = name;
        room.phone = phone;
        room.startDate = startDate;
        room.dueDay = dueDay;
        room.price = rentAmount;
        room.status = 'occupied';
      }

      renderAllViews();
      closeModal('modalAddTenant');
      form.reset();
      showToast(`Penghuni ${name} berhasil didaftarkan!`, 'success');
    });
  }

  function assignNewTenantToRoom(roomNumber) {
    closeModal('modalRoomDetail');
    openAddTenantModal(roomNumber);
  }

  async function checkoutTenant(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;

    if (confirm(`Apakah Anda yakin ingin memproses checkout untuk ${room.tenant} dari Kamar #${room.number}? Kamar akan berstatus Kosong.`)) {
      try {
        await fetch(`/api/tenants/${room.id}/checkout`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          }
        });
        await fetchServerData();
      } catch (e) {
        room.tenant = null;
        room.phone = '';
        room.startDate = '';
        room.status = 'available';
        renderAllViews();
      }
      showToast(`Kamar #${roomNumber} sekarang telah dikosongkan.`, 'info');
    }
  }

  // --- TRANSACTION MODAL ---
  function openTransactionModal(defaultRoomNumber = null) {
    const select = document.getElementById('transRoomId');
    select.innerHTML = `
      <option value="">-- Tanpa Kamar Spesifik (Operasional ${getBranchName('Lokasi 1')}) --</option>
      <option value="loc2_general">-- Tanpa Kamar Spesifik (Operasional ${getBranchName('Lokasi 2')}) --</option>
    `;

    const loc1Rooms = appData.rooms.filter(r => resolveRoomLocation(r) === 'Lokasi 1');
    const loc2Rooms = appData.rooms.filter(r => resolveRoomLocation(r) === 'Lokasi 2');

    if (loc1Rooms.length > 0) {
      const grp1 = document.createElement('optgroup');
      grp1.label = `${getBranchName('Lokasi 1')} (Kamar #101–#108)`;
      loc1Rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : '(Kosong)'}`;
        if (defaultRoomNumber && r.number === defaultRoomNumber) opt.selected = true;
        grp1.appendChild(opt);
      });
      select.appendChild(grp1);
    }

    if (loc2Rooms.length > 0) {
      const grp2 = document.createElement('optgroup');
      grp2.label = `${getBranchName('Lokasi 2')} (Kamar #201–#208)`;
      loc2Rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : '(Kosong)'}`;
        if (defaultRoomNumber && r.number === defaultRoomNumber) opt.selected = true;
        grp2.appendChild(opt);
      });
      select.appendChild(grp2);
    }

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('transDate').value = today;

    if (defaultRoomNumber) {
      const r = appData.rooms.find(room => room.number === defaultRoomNumber);
      if (r) {
        document.getElementById('transAmount').value = r.price;
        document.getElementById('transDescription').value = `Pembayaran Sewa Kamar #${r.number} (${r.tenant || ''}) - ${resolveRoomLocation(r)}`;
      }
    }

    openModal('modalTransaction');
  }

  function recordPaymentForRoom(roomNumber) {
    openTransactionModal(roomNumber);
  }

  function initTransactionForm() {
    const form = document.getElementById('formTransaction');
    const btnRecord = document.getElementById('btnRecordTransaction');

    btnRecord?.addEventListener('click', () => openTransactionModal());

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const type = document.getElementById('transType').value;
      const date = document.getElementById('transDate').value;
      const rawRoomVal = document.getElementById('transRoomId').value || null;
      const isLoc2General = rawRoomVal === 'loc2_general';
      const roomId = isLoc2General ? null : rawRoomVal;
      let description = document.getElementById('transDescription').value.trim();
      if (isLoc2General && !/lokasi 2|ringroad|cabang 2/i.test(description)) {
        description += ' (Lokasi 2 - Ringroad Exclusive)';
      }
      const amount = Number(document.getElementById('transAmount').value);
      const paymentMethod = document.getElementById('transMethod').value;

      try {
        const res = await fetch('/api/finances', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            type,
            date,
            room_id: roomId,
            description,
            amount,
            payment_method: paymentMethod
          })
        });

        if (res.ok) {
          const data = await res.json();
          const txYear = parseFinanceYearMonth(date).year;
          const selectYearEl = document.getElementById('selectFinanceYear');
          if (selectYearEl && txYear) {
            if (![...selectYearEl.options].some(o => parseInt(o.value, 10) === txYear)) {
              const opt = document.createElement('option');
              opt.value = txYear;
              opt.textContent = 'Tahun ' + txYear;
              selectYearEl.appendChild(opt);
            }
            selectYearEl.value = txYear;
          }
          showToast(`Transaksi ${formatRupiah(amount)} berhasil disimpan di Laravel!`, 'success');
          await fetchServerData();
          closeModal('modalTransaction');
          form.reset();

          if (type === 'income' && data.finance) {
            setTimeout(() => showReceiptModal(data.finance.transaction_code), 400);
          }
          return;
        }
      } catch (err) {
        console.error(err);
      }

      // Local fallback
      const newId = 'TRX-' + Math.floor(100 + Math.random() * 900);
      appData.finances.unshift({
        id: newId,
        date,
        type,
        category: type === 'income' ? 'Sewa Kamar' : 'Operasional Kos',
        amount,
        desc: description,
        method: paymentMethod,
        roomId
      });

      renderAllViews();
      closeModal('modalTransaction');
      form.reset();
      showToast(`Transaksi ${formatRupiah(amount)} tersimpan!`, 'success');
    });
  }

  // --- RECEIPT CONTROLLER ---
  function showReceiptModal(trxCode) {
    const trx = appData.finances.find(f => f.id === trxCode);
    if (!trx) return;

    let tenantName = 'Umum / Tamu';
    let roomLabel = 'Umum';

    if (trx.roomId) {
      const room = appData.rooms.find(r => r.id === trx.roomId || r.number === trx.roomId);
      if (room) {
        tenantName = room.tenant || 'Penghuni Kamar #' + room.number;
        roomLabel = `Kamar #${room.number} (Lantai ${room.floor})`;
      }
    }

    document.getElementById('recNumber').textContent = `NO: ${trx.id}`;
    document.getElementById('recDate').textContent = trx.paymentTime || (trx.date + ', ' + (trx.time || '12:00:00') + ' ' + (trx.timezone || 'WIB'));
    document.getElementById('recTenant').textContent = tenantName;
    document.getElementById('recRoom').textContent = roomLabel;
    document.getElementById('recDesc').textContent = trx.desc;
    document.getElementById('recMethod').textContent = trx.method;
    document.getElementById('recAmount').textContent = formatRupiah(trx.amount);

    const proofRow = document.getElementById('recProofRow');
    const proofImg = document.getElementById('recProofImg');
    if (proofRow && proofImg) {
      if (trx.proofImage) {
        proofRow.style.display = 'block';
        proofImg.src = trx.proofImage;
      } else {
        proofRow.style.display = 'none';
      }
    }

    openModal('modalReceipt');
  }

  function initReceiptPrint() {
    const btnPrint = document.getElementById('btnPrintReceipt');
    btnPrint?.addEventListener('click', () => {
      window.print();
    });
  }

  // --- MAINTENANCE CONTROLLER ---
  function initMaintenanceForm() {
    const form = document.getElementById('formRepairTicket');
    const btnNew = document.getElementById('btnNewRepairTicket');

    btnNew?.addEventListener('click', () => {
      const select = document.getElementById('repairRoomSelect');
      select.innerHTML = '';

      const loc1Rooms = appData.rooms.filter(r => resolveRoomLocation(r) === 'Lokasi 1');
      const loc2Rooms = appData.rooms.filter(r => resolveRoomLocation(r) === 'Lokasi 2');

      if (loc1Rooms.length > 0) {
        const grp1 = document.createElement('optgroup');
        grp1.label = `${getBranchName('Lokasi 1')} (PIC: Pak Budi)`;
        loc1Rooms.forEach(r => {
          const opt = document.createElement('option');
          opt.value = r.id;
          opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : '(Kosong)'}`;
          grp1.appendChild(opt);
        });
        select.appendChild(grp1);
      }

      if (loc2Rooms.length > 0) {
        const grp2 = document.createElement('optgroup');
        grp2.label = `${getBranchName('Lokasi 2')} (PIC: Mas Dani)`;
        loc2Rooms.forEach(r => {
          const opt = document.createElement('option');
          opt.value = r.id;
          opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : '(Kosong)'}`;
          grp2.appendChild(opt);
        });
        select.appendChild(grp2);
      }

      openModal('modalRepairTicket');
    });

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const roomId = document.getElementById('repairRoomSelect').value;
      const priority = document.getElementById('repairPriority').value;
      const issue = document.getElementById('repairDescription').value.trim();

      try {
        const res = await fetch('/api/maintenance', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            room_id: roomId,
            priority,
            issue_description: issue
          })
        });

        if (res.ok) {
          showToast('Tiket keluhan berhasil dibuat di server Laravel!', 'success');
          await fetchServerData();
          closeModal('modalRepairTicket');
          form.reset();
          return;
        }
      } catch (err) {
        console.error(err);
      }

      // Local fallback
      const room = appData.rooms.find(r => r.id === roomId);
      appData.maintenance.unshift({
        id: 'TK-' + Math.floor(100 + Math.random() * 900),
        roomId: room ? room.number : roomId,
        reporter: room?.tenant || 'Pengelola',
        issue,
        date: new Date().toISOString().split('T')[0],
        priority,
        status: 'pending'
      });

      renderAllViews();
      closeModal('modalRepairTicket');
      form.reset();
      showToast('Tiket keluhan tersimpan!', 'success');
    });
  }

  async function updateRepairStatus(ticketCode, newStatus) {
    const ticket = appData.maintenance.find(m => m.id === ticketCode);
    if (!ticket) return;
    ticket.status = newStatus;

    try {
      await fetch(`/api/maintenance/${ticketCode}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ status: newStatus })
      });
    } catch (e) {
      console.warn('Status updated locally');
    }

    renderAllViews();
    showToast(`Status Tiket #${ticketCode} diubah menjadi ${newStatus}.`, 'info');
  }

  // --- SETTINGS, BACKUP & RESTORE ---
  function initSettings() {
    const profileForm = document.getElementById('settingsProfileForm');
    const generalProfileForm = document.getElementById('settingsGeneralProfileForm');
    const landingForm = document.getElementById('landingPageEditorForm');
    const bankForm = document.getElementById('settingsBankForm');
    const btnExport = document.getElementById('btnExportData');
    const btnReset = document.getElementById('btnResetData');

    // Sub-tab navigation in Settings
    const subTabBtns = document.querySelectorAll('#settingsSubTabNav .btn-settings-subtab');
    const subTabPanes = document.querySelectorAll('.settings-subtab-pane');
    subTabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        subTabBtns.forEach(b => {
          b.classList.remove('active');
          b.style.background = '';
          b.style.color = '';
        });
        btn.classList.add('active');
        btn.style.background = '';
        btn.style.color = '';

        const target = btn.getAttribute('data-subtab');
        subTabPanes.forEach(pane => {
          if (pane.id === target) {
            pane.style.display = 'block';
          } else {
            pane.style.display = 'none';
          }
        });
      });
    });

    // Hero image file preview
    const heroFileInput = document.getElementById('lp_hero_image_file');
    const heroPreview = document.getElementById('previewHeroImage');
    heroFileInput?.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file && heroPreview) {
        const reader = new FileReader();
        reader.onload = (re) => { heroPreview.src = re.target.result; };
        reader.readAsDataURL(file);
      }
    });

    // Bento Gallery file previews (4 photos)
    const galInputs = document.querySelectorAll('.gal-file-input');
    galInputs.forEach(input => {
      input.addEventListener('change', (e) => {
        const targetId = input.getAttribute('data-preview-target');
        const imgEl = document.getElementById(targetId);
        const file = e.target.files[0];
        if (file && imgEl) {
          const reader = new FileReader();
          reader.onload = (re) => { imgEl.src = re.target.result; };
          reader.readAsDataURL(file);
        }
      });
    });

    // Save Landing Page Form
    landingForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btnSaveLandingPage');
      if (btn) btn.disabled = true;

      const formData = new FormData(landingForm);
      try {
        const res = await fetch('/api/settings/landing', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: formData
        });
        const data = await res.json();
        if (data.success) {
          showToast('Seluruh tampilan & foto Landing Page berhasil disimpan!', 'success');
        } else {
          showToast(data.message || 'Gagal menyimpan tampilan', 'warning');
        }
      } catch (err) {
        showToast('Gagal terhubung ke server', 'warning');
      } finally {
        if (btn) btn.disabled = false;
      }
    });

    // Switcher 2 Titik Lokasi Settings
    window.switchBranchSettings = function(branchKey) {
      const p1 = document.getElementById('panelBranch1');
      const p2 = document.getElementById('panelBranch2');
      const t1 = document.getElementById('btnTabBranch1');
      const t2 = document.getElementById('btnTabBranch2');

      if (branchKey === 'lokasi_2') {
        if (p1) p1.style.display = 'none';
        if (p2) p2.style.display = 'flex';
        if (t1) {
          t1.style.background = 'transparent';
          t1.style.color = 'rgba(255,255,255,0.8)';
          t1.style.boxShadow = 'none';
        }
        if (t2) {
          t2.style.background = '#ffffff';
          t2.style.color = '#0d9488';
          t2.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        }
      } else {
        if (p1) p1.style.display = 'flex';
        if (p2) p2.style.display = 'none';
        if (t1) {
          t1.style.background = '#ffffff';
          t1.style.color = '#0a5661';
          t1.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        }
        if (t2) {
          t2.style.background = 'transparent';
          t2.style.color = 'rgba(255,255,255,0.8)';
          t2.style.boxShadow = 'none';
        }
      }
    };

    function resolveLiveEmbed(rawUrl, fallbackAddr) {
      let embedVal = (rawUrl || '').trim();
      if (!embedVal) {
        return 'https://maps.google.com/maps?q=' + encodeURIComponent(fallbackAddr || 'Yogyakarta') + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
      }
      if (embedVal.includes('<iframe')) {
        const match = embedVal.match(/src=["']([^"']+)["']/);
        if (match && match[1]) embedVal = match[1];
      }
      const placeMatch = embedVal.match(/place\/([^\/@?#]+)/);
      if (placeMatch && placeMatch[1]) {
        const placeName = decodeURIComponent(placeMatch[1].replace(/\+/g, ' '));
        if (placeName && placeName.toLowerCase() !== 'dropped pin') {
          return 'https://maps.google.com/maps?q=' + encodeURIComponent(placeName) + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
        }
      }
      const pinMatch = embedVal.match(/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/);
      if (pinMatch) {
        return 'https://maps.google.com/maps?q=' + pinMatch[1] + ',' + pinMatch[2] + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
      }
      if (embedVal.includes('q=')) {
        try {
          const urlObj = new URL(embedVal.startsWith('http') ? embedVal : 'https://' + embedVal);
          const qParam = urlObj.searchParams.get('q');
          if (qParam) {
            return 'https://maps.google.com/maps?q=' + encodeURIComponent(qParam) + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
          }
        } catch (e) {}
      }
      if (embedVal.includes('maps.app.goo.gl') || embedVal.includes('goo.gl/maps')) {
        return 'https://maps.google.com/maps?q=' + encodeURIComponent(fallbackAddr || 'Yogyakarta') + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
      }
      if (!embedVal.includes('google.com/maps/embed') && !embedVal.includes('output=embed')) {
        return 'https://maps.google.com/maps?q=' + encodeURIComponent(embedVal) + '&t=&z=16&ie=UTF8&iwloc=&output=embed';
      }
      return embedVal;
    }

    // Live update maps on input for Branch 1 and Branch 2
    document.getElementById('b1_gmaps_url')?.addEventListener('input', (e) => {
      const iframe1 = document.getElementById('iframePreviewB1');
      const addr1 = document.getElementById('b1_address')?.value.trim();
      if (iframe1) iframe1.src = resolveLiveEmbed(e.target.value, addr1);
    });

    document.getElementById('b2_gmaps_url')?.addEventListener('input', (e) => {
      const iframe2 = document.getElementById('iframePreviewB2');
      const addr2 = document.getElementById('b2_address')?.value.trim();
      if (iframe2) iframe2.src = resolveLiveEmbed(e.target.value, addr2);
    });

    // Live sync branch names across all Admin UI as Admin types in #b1_name / #b2_name
    document.getElementById('b1_name')?.addEventListener('input', (e) => {
      if (!appData.settings.branches) appData.settings.branches = {};
      if (!appData.settings.branches.lokasi_1) appData.settings.branches.lokasi_1 = {};
      appData.settings.branches.lokasi_1.name = e.target.value.trim() || 'Kos Mas Robi 1 - Kampus Utama';
      syncBranchNamesInDOM();
      renderAllViews();
    });

    document.getElementById('b2_name')?.addEventListener('input', (e) => {
      if (!appData.settings.branches) appData.settings.branches = {};
      if (!appData.settings.branches.lokasi_2) appData.settings.branches.lokasi_2 = {};
      appData.settings.branches.lokasi_2.name = e.target.value.trim() || 'Kos Mas Robi 2 - Ringroad Exclusive';
      syncBranchNamesInDOM();
      renderAllViews();
    });

    profileForm?.addEventListener('submit', async (e) => {
      e.preventDefault();

      const b1_name = document.getElementById('b1_name')?.value.trim() || 'Kos Mas Robi 1 - Kampus Utama';
      const b1_tagline = document.getElementById('b1_tagline')?.value.trim() || '';
      const b1_address = document.getElementById('b1_address')?.value.trim() || '';
      const b1_gmaps_url = document.getElementById('b1_gmaps_url')?.value.trim() || '';
      const b1_gmaps_embed = document.getElementById('b1_gmaps_embed')?.value.trim() || '';
      const b1_room_types = (document.getElementById('b1_room_types')?.value || '').split(',').map(s => s.trim()).filter(Boolean);
      const b1_facilities = (document.getElementById('b1_facilities')?.value || '').split(',').map(s => s.trim()).filter(Boolean);

      const b2_name = document.getElementById('b2_name')?.value.trim() || 'Kos Mas Robi 2 - Ringroad Exclusive';
      const b2_tagline = document.getElementById('b2_tagline')?.value.trim() || '';
      const b2_address = document.getElementById('b2_address')?.value.trim() || '';
      const b2_gmaps_url = document.getElementById('b2_gmaps_url')?.value.trim() || '';
      const b2_gmaps_embed = document.getElementById('b2_gmaps_embed')?.value.trim() || '';
      const b2_room_types = (document.getElementById('b2_room_types')?.value || '').split(',').map(s => s.trim()).filter(Boolean);
      const b2_facilities = (document.getElementById('b2_facilities')?.value || '').split(',').map(s => s.trim()).filter(Boolean);

      const branchesPayload = {
        lokasi_1: {
          id: 'lokasi_1',
          code: 'Lokasi 1',
          name: b1_name,
          tagline: b1_tagline,
          address: b1_address,
          gmaps_url: b1_gmaps_url,
          gmaps_embed: b1_gmaps_embed,
          room_types: b1_room_types,
          facilities: b1_facilities,
          badge: b1_name,
          color: '#0a5661'
        },
        lokasi_2: {
          id: 'lokasi_2',
          code: 'Lokasi 2',
          name: b2_name,
          tagline: b2_tagline,
          address: b2_address,
          gmaps_url: b2_gmaps_url,
          gmaps_embed: b2_gmaps_embed,
          room_types: b2_room_types,
          facilities: b2_facilities,
          badge: b2_name,
          color: '#0d9488'
        }
      };

      // Collect 4 location highlights
      const highlightItems = [];
      const editContainers = document.querySelectorAll('#locationHighlightsEditorList .highlight-edit-item');
      editContainers.forEach(item => {
        const icon = item.querySelector('.hl-icon')?.value.trim() || '';
        const title = item.querySelector('.hl-title')?.value.trim() || '';
        const desc = item.querySelector('.hl-desc')?.value.trim() || '';
        highlightItems.push({ icon, title, desc });
      });

      try {
        const res = await fetch('/api/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            branches: branchesPayload,
            kos_address: b1_address,
            gmaps_url: b1_gmaps_url,
            location_highlights: highlightItems
          })
        });
        const data = await res.json();
        showToast('Pengaturan 2 Titik Lokasi & Nama Cabang berhasil disinkronkan!', 'success');
        
        if (data.setting && data.setting.branches) {
          appData.settings = Object.assign({}, appData.settings, data.setting);
          const if1 = document.getElementById('iframePreviewB1');
          const if2 = document.getElementById('iframePreviewB2');
          if (if1 && data.setting.branches.lokasi_1) {
            if1.src = resolveLiveEmbed(data.setting.branches.lokasi_1.gmaps_embed || data.setting.branches.lokasi_1.gmaps_url, b1_address);
          }
          if (if2 && data.setting.branches.lokasi_2) {
            if2.src = resolveLiveEmbed(data.setting.branches.lokasi_2.gmaps_embed || data.setting.branches.lokasi_2.gmaps_url, b2_address);
          }
        } else {
          appData.settings.branches = branchesPayload;
        }
        syncBranchNamesInDOM();
        renderAllViews();
      } catch (err) {
        showToast('Gagal menyimpan pengaturan lokasi ke server', 'warning');
      }
    });

    generalProfileForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const kosName = document.getElementById('setKosName').value.trim();
      const ownerName = document.getElementById('setOwnerName').value.trim();
      const ownerPhone = document.getElementById('setOwnerPhone').value.trim();

      try {
        await fetch('/api/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            kos_name: kosName,
            owner_name: ownerName,
            owner_phone: ownerPhone
          })
        });
        showToast('Informasi akun pengelola & properti berhasil disimpan!', 'success');
        if (document.getElementById('bannerPropertyName')) document.getElementById('bannerPropertyName').textContent = kosName;
        if (document.getElementById('profileOwnerName')) document.getElementById('profileOwnerName').textContent = ownerName;
        if (document.getElementById('akunModalOwnerName')) document.getElementById('akunModalOwnerName').textContent = ownerName || 'Mas Robi';
        const initials = (ownerName || 'Mas Robi').split(/\s+/).filter(Boolean).slice(0, 2).map(w => w.charAt(0).toUpperCase()).join('') || 'MR';
        document.querySelectorAll('.host-avatar, .mobile-account-avatar, #akunModalAvatar').forEach(el => { el.textContent = initials; });
        document.querySelectorAll('.brand-title').forEach(el => { el.textContent = kosName || 'Kos Mas Robi'; });
        if (typeof window.closeModal === 'function') window.closeModal('modalAkunPengelola');
      } catch (err) {
        showToast('Gagal menyimpan profil', 'warning');
      }
    });

    bankForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const bankName = document.getElementById('setBankName').value.trim();
      const bankAccount = document.getElementById('setBankAccount').value.trim();
      const bankHolder = document.getElementById('setBankHolder').value.trim();

      try {
        await fetch('/api/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            bank_name: bankName,
            bank_account: bankAccount,
            bank_holder: bankHolder
          })
        });
        showToast('Informasi rekening pembayaran tersimpan di database!', 'success');
      } catch (err) {
        showToast('Gagal menyimpan rekening', 'warning');
      }
    });

    btnExport?.addEventListener('click', () => {
      window.location.href = '/api/export';
      showToast('Mengunduh data cadangan JSON dari database Laravel...', 'info');
    });

    btnReset?.addEventListener('click', async () => {
      if (confirm('Apakah Anda yakin ingin mereset seluruh database ke sampel awal Laravel Seeder?')) {
        try {
          const res = await fetch('/api/reset', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': getCsrfToken()
            }
          });
          if (res.ok) {
            showToast('Database berhasil direset ke data sampel!', 'success');
            await fetchServerData();
          }
        } catch (e) {
          showToast('Gagal mereset database', 'warning');
        }
      }
    });
  }

  // --- SEARCH & FILTER LISTENERS ---
  function initSearchListeners() {
    const globalSearch = document.getElementById('globalSearchInput');
    globalSearch?.addEventListener('input', (e) => {
      const val = e.target.value.toLowerCase();
      renderRoomGrid('all');
      renderRoomsMasterTable(val);
      renderTenantsTable(val);
    });

    const roomSearch = document.getElementById('roomSearchInput');
    let currentFloor = 'all';
    let currentLocation = 'all';

    roomSearch?.addEventListener('input', (e) => {
      renderRoomsMasterTable(e.target.value, currentFloor, currentLocation);
    });

    document.querySelectorAll('#roomTabFilters .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#roomTabFilters .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFloor = btn.getAttribute('data-floor');
        renderRoomsMasterTable(roomSearch?.value || '', currentFloor, currentLocation);
      });
    });

    document.querySelectorAll('#roomLocationFilters .loc-filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#roomLocationFilters .loc-filter-btn').forEach(b => {
          b.classList.remove('active');
          b.style.background = '#ffffff';
          b.style.color = b.getAttribute('data-location') === 'Lokasi 2' ? '#0d9488' : '#0a5661';
          b.style.boxShadow = 'none';
          b.style.border = '1px solid rgba(10,86,97,0.15)';
        });
        btn.classList.add('active');
        currentLocation = btn.getAttribute('data-location');
        btn.style.background = currentLocation === 'Lokasi 2' ? '#0d9488' : '#0a5661';
        btn.style.color = '#ffffff';
        btn.style.boxShadow = '0 2px 8px rgba(10,86,97,0.25)';
        btn.style.border = 'none';
        renderRoomsMasterTable(roomSearch?.value || '', currentFloor, currentLocation);
      });
    });

    const tenantSearch = document.getElementById('tenantSearchInput');
    let currentTStatus = 'all';
    let currentTLoc = 'all';
    tenantSearch?.addEventListener('input', (e) => {
      renderTenantsTable(e.target.value, currentTStatus, currentTLoc);
    });

    document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentTStatus = btn.getAttribute('data-tstatus');
        renderTenantsTable(tenantSearch?.value || '', currentTStatus, currentTLoc);
      });
    });

    document.querySelectorAll('#tenantLocationFilters .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#tenantLocationFilters .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentTLoc = btn.getAttribute('data-tloc') || 'all';
        renderTenantsTable(tenantSearch?.value || '', currentTStatus, currentTLoc);
      });
    });

    const financeSearch = document.getElementById('financeSearchInput');
    let currentFType = 'all';
    let currentFLoc = 'all';
    financeSearch?.addEventListener('input', (e) => {
      renderFinancesTable(e.target.value, currentFType, currentFLoc);
    });

    document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFType = btn.getAttribute('data-ftype');
        renderFinancesTable(financeSearch?.value || '', currentFType, currentFLoc);
      });
    });

    document.querySelectorAll('#financeLocationFilters .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#financeLocationFilters .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFLoc = btn.getAttribute('data-floc') || 'all';
        renderFinancesTable(financeSearch?.value || '', currentFType, currentFLoc);
      });
    });

    const maintSearch = document.getElementById('maintenanceSearchInput');
    let currentMStatus = 'all';
    let currentMLoc = 'all';
    maintSearch?.addEventListener('input', (e) => {
      renderMaintenanceTable(e.target.value, currentMStatus, currentMLoc);
    });

    document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentMStatus = btn.getAttribute('data-rstatus');
        renderMaintenanceTable(maintSearch?.value || '', currentMStatus, currentMLoc);
      });
    });

    document.querySelectorAll('#maintenanceLocationFilters .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#maintenanceLocationFilters .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentMLoc = btn.getAttribute('data-mloc') || 'all';
        renderMaintenanceTable(maintSearch?.value || '', currentMStatus, currentMLoc);
      });
    });
  }

  // --- RENDER ALL VIEWS ---
  function renderAllViews() {
    syncBranchNamesInDOM();
    renderRoomGrid();
    renderRoomsMasterTable();
    renderTenantsTable('', 'all', activeTenantLocationFilter);
    renderFinancesTable('', 'all', activeFinanceLocationFilter);
    renderMaintenanceTable('', 'all', activeMaintLocationFilter);
    updateDashboardStats();
  }

  // --- INITIAL DATA SYNC (ZERO-LATENCY INSTANT RENDER) ---
  function loadInitialAdminData() {
    if (window.initialAdminData) {
      const data = window.initialAdminData;
      if (data.settings) appData.settings = Object.assign({}, appData.settings, data.settings);
      if (Array.isArray(data.rooms) && data.rooms.length > 0) {
        appData.rooms = data.rooms.map(r => ({
          id: String(r.id),
          number: r.number,
          floor: Number(r.floor),
          type: r.type,
          location: r.location || 'Lokasi 1',
          price: Number(r.price),
          status: r.status,
          facilities: r.facilities || [],
          image: r.image || '',
          images: r.images || [],
          images_list: r.images_list || [],
          custom_interior_status: r.custom_interior_status || 'none',
          custom_interior_budget: Number(r.custom_interior_budget || 0),
          custom_interior_request: r.custom_interior_request || '',
          custom_interior_admin_notes: r.custom_interior_admin_notes || '',
          booked_by_name: r.booked_by_name || '',
          tenant: r.active_tenant ? r.active_tenant.name : (r.tenant || null),
          phone: r.active_tenant ? r.active_tenant.phone : (r.phone || ''),
          startDate: r.active_tenant ? r.active_tenant.start_date : '',
          dueDay: r.active_tenant ? r.active_tenant.due_day : (r.due_day || 1)
        }));
      }
      if (Array.isArray(data.finances) && data.finances.length > 0) {
        appData.finances = data.finances.map(f => ({
          id: f.transaction_code || String(f.id || ''),
          dbId: f.id,
          date: f.date || '',
          paymentTime: f.formatted_payment_full || (f.date + ', ' + (f.formatted_payment_time || '12:00:00 WIB')),
          timezone: f.timezone || 'WIB',
          type: f.type || 'income',
          category: f.category || 'Sewa Kamar',
          amount: Number(f.amount || 0),
          desc: f.description || '',
          method: f.payment_method || 'Transfer',
          roomId: f.room_id ? String(f.room_id) : null,
          location: f.room && f.room.location ? f.room.location : null,
          proofImage: f.proof_image ? ('/' + f.proof_image.replace(/^\//, '')) : null
        }));
      }
      if (Array.isArray(data.maintenance) && data.maintenance.length > 0) {
        appData.maintenance = data.maintenance.map(m => ({
          id: m.ticket_code || String(m.id || ''),
          dbId: m.id,
          roomId: m.room ? m.room.number : String(m.room_id || '-'),
          location: m.room && m.room.location ? m.room.location : null,
          reporter: m.reporter_name || 'Penghuni',
          issue: m.issue_description || m.issue || '',
          status: m.status || 'pending',
          priority: m.priority || 'Sedang',
          date: m.report_date || ''
        }));
      }
      renderAllViews();
    }
  }

  // --- INITIALIZE APPLICATION ---
  let isInitialized = false;
  async function init() {
    if (isInitialized) return;
    isInitialized = true;
    const safeRun = (fnName, fn) => {
      try {
        fn();
      } catch (err) {
        console.error(`Error in ${fnName}:`, err);
      }
    };

    safeRun('initAutoDeviceDetector', initAutoDeviceDetector);
    safeRun('initTheme', initTheme);
    safeRun('initNavigation', initNavigation);
    safeRun('initDashboardFilters', initDashboardFilters);
    safeRun('initModalDismissHandlers', initModalDismissHandlers);
    safeRun('initRoomForm', initRoomForm);
    safeRun('initTenantForm', initTenantForm);
    safeRun('initTransactionForm', initTransactionForm);
    safeRun('initReceiptPrint', initReceiptPrint);
    safeRun('initMaintenanceForm', initMaintenanceForm);
    safeRun('initSettings', initSettings);
    safeRun('initSearchListeners', initSearchListeners);
    safeRun('loadInitialAdminData', loadInitialAdminData);

    try {
      await fetchServerData();
    } catch (e) {
      console.warn('fetchServerData error:', e);
    }
    console.log('Kos Mas Robi Laravel System initialized.');
  }

  window.kosApp = {
    openRoomDetailModal,
    openModal,
    closeModal,
    switchTab,
    changeRoomStatusDirect,
    saveCustomInteriorDirect,
    editRoom,
    fillDefaultRoomImages,
    sendWhatsAppReminder,
    assignNewTenantToRoom,
    checkoutTenant,
    recordPaymentForRoom,
    showReceiptModal,
    updateRepairStatus,
    renderAllViews,
    renderFinancesTable,
    getFinanceReportPayload,
    getAppData: () => appData
  };

  window.openRoomDetailModal = openRoomDetailModal;
  window.openModal = openModal;
  window.closeModal = closeModal;
  window.switchTab = switchTab;
  window.editRoom = editRoom;
  window.sendWhatsAppReminder = sendWhatsAppReminder;
  window.assignNewTenantToRoom = assignNewTenantToRoom;
  window.checkoutTenant = checkoutTenant;
  window.recordPaymentForRoom = recordPaymentForRoom;
  window.showReceiptModal = showReceiptModal;
  window.updateRepairStatus = updateRepairStatus;

  if (document.getElementById('appSidebar') || document.readyState !== 'loading') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }

})();
