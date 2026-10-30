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
          price: Number(r.price),
          status: r.status,
          facilities: r.facilities || [],
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
          proofImage: f.proof_image ? ('/' + f.proof_image.replace(/^\//, '')) : null
        }));
        appData.maintenance = (json.maintenance || []).map(m => ({
          id: m.ticket_code,
          dbId: m.id,
          roomId: m.room ? m.room.number : String(m.room_id),
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
        return '<span class="status-badge due">⚠️ Jatuh Tempo</span>';
      case 'repair':
        return '<span class="status-badge repair">🔧 Servis</span>';
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
      <div style="font-weight: 600;">${type === 'success' ? '✓' : type === 'warning' ? '⚠️' : 'ℹ️'}</div>
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

  // --- THEME CONTROLLER ---
  function initTheme() {
    const root = document.documentElement;
    const btn = document.getElementById('btnToggleTheme');
    const icon = document.getElementById('themeIconText');
    const savedTheme = localStorage.getItem(THEME_KEY) || 'light';

    function applyTheme(theme) {
      root.setAttribute('data-theme', theme);
      localStorage.setItem(THEME_KEY, theme);
      localStorage.setItem('kosTheme', theme);
      if (icon) {
        icon.textContent = theme === 'dark' ? '🌙' : '☀️';
      }
    }

    applyTheme(savedTheme);

    btn?.addEventListener('click', () => {
      const current = root.getAttribute('data-theme');
      applyTheme(current === 'dark' ? 'light' : 'dark');
    });
  }

  // --- NAVIGATION CONTROLLER ---
  function initNavigation() {
    const sidebarItems = document.querySelectorAll('.app-sidebar .nav-item');
    const bottomNavItems = document.querySelectorAll('.mobile-bottom-nav .bottom-nav-item');
    const panes = document.querySelectorAll('.tab-pane');
    const pageTitle = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');

    const titles = {
      'tab-dashboard': { title: 'Dashboard Ikhtisar', sub: 'Monitoring real-time hunian dan operasional Kos Mas Robi' },
      'tab-rooms': { title: 'Manajemen Kamar', sub: 'Daftar seluruh unit kamar, fasilitas, dan tarif sewa' },
      'tab-tenants': { title: 'Data Penghuni Kos', sub: 'Kelola kontak anak kos, sewa aktif, dan pengingat WhatsApp' },
      'tab-finances': { title: 'Keuangan & Tagihan', sub: 'Pencatatan uang masuk, pengeluaran, dan kuitansi digital' },
      'tab-maintenance': { title: 'Keluhan & Perbaikan', sub: 'Daftar laporan fasilitas kos dan penanganan teknisi' },
      'tab-settings': { title: 'Pengaturan Kos', sub: 'Pengaturan profil properti, nomor rekening, dan cadangan data' }
    };

    function switchTab(tabId) {
      panes.forEach(pane => {
        pane.classList.toggle('active', pane.id === tabId);
      });

      sidebarItems.forEach(item => {
        item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
      });

      bottomNavItems.forEach(item => {
        item.classList.toggle('active', item.getAttribute('data-tab') === tabId);
      });

      if (titles[tabId]) {
        pageTitle.textContent = titles[tabId].title;
        pageSubtitle.textContent = titles[tabId].sub;
      }

      document.getElementById('appSidebar')?.classList.remove('open');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    sidebarItems.forEach(item => {
      item.addEventListener('click', () => {
        const tab = item.getAttribute('data-tab');
        if (tab) switchTab(tab);
      });
    });

    bottomNavItems.forEach(item => {
      item.addEventListener('click', () => {
        const tab = item.getAttribute('data-tab');
        if (tab) switchTab(tab);
      });
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

      card.innerHTML = `
        <div class="room-card-top">
          <div class="room-number-wrap">
            <span class="room-number">#${room.number}</span>
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
  function renderRoomsMasterTable(search = '', floorFilter = 'all') {
    const tbody = document.getElementById('roomsMasterTableBody');
    const mobileList = document.getElementById('roomsMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const filtered = appData.rooms.filter(r => {
      const matchSearch = r.number.toLowerCase().includes(search.toLowerCase()) ||
                          r.type.toLowerCase().includes(search.toLowerCase()) ||
                          (r.tenant && r.tenant.toLowerCase().includes(search.toLowerCase()));
      const matchFloor = floorFilter === 'all' || String(r.floor) === String(floorFilter);
      return matchSearch && matchFloor;
    });

    filtered.forEach(room => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-weight:700;font-size:1rem;">#${room.number}</td>
        <td>
          <div style="font-weight:600;">${room.type}</div>
          <div style="font-size:0.75rem;color:var(--text-muted);">${(room.facilities || []).join(', ')}</div>
        </td>
        <td>Lantai ${room.floor}</td>
        <td style="font-weight:600;color:var(--brand-primary-light);">${formatRupiah(room.price)}</td>
        <td>${getStatusBadge(room.status)}</td>
        <td>${room.tenant ? `<strong>${room.tenant}</strong>` : '<span style="color:var(--text-muted)">-</span>'}</td>
        <td>
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn-secondary" style="padding:4px 10px;font-size:0.78rem;" onclick="window.kosApp.editRoom('${room.number}')">Edit</button>
            <button type="button" class="btn-secondary" style="padding:4px 10px;font-size:0.78rem;" onclick="window.kosApp.openRoomDetailModal('${room.number}')">Detail</button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);

      const mCard = document.createElement('div');
      mCard.className = 'mobile-data-card';
      mCard.innerHTML = `
        <div class="mobile-data-header">
          <span class="mobile-data-title">Kamar #${room.number} (${room.type})</span>
          ${getStatusBadge(room.status)}
        </div>
        <div class="mobile-data-row">
          <span>Lantai:</span>
          <span>Lantai ${room.floor}</span>
        </div>
        <div class="mobile-data-row">
          <span>Tarif Sewa:</span>
          <span style="color:var(--brand-primary-light);font-weight:700;">${formatRupiah(room.price)} /bln</span>
        </div>
        <div class="mobile-data-row">
          <span>Penghuni:</span>
          <span>${room.tenant || 'Kosong'}</span>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px;">
          <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:6px;" onclick="window.kosApp.editRoom('${room.number}')">Edit Kamar</button>
          <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:6px;" onclick="window.kosApp.openRoomDetailModal('${room.number}')">Lihat Detail</button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ditemukan kamar yang sesuai</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ditemukan kamar yang sesuai</div>';
    }
  }

  // --- TAB: DATA PENGHUNI ---
  function renderTenantsTable(search = '', statusFilter = 'all') {
    const tbody = document.getElementById('tenantsTableBody');
    const mobileList = document.getElementById('tenantsMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const tenants = appData.rooms.filter(r => r.tenant && r.tenant.trim() !== '');

    const filtered = tenants.filter(t => {
      const matchSearch = t.tenant.toLowerCase().includes(search.toLowerCase()) ||
                          t.phone.includes(search) ||
                          t.number.includes(search);
      let matchStatus = true;
      if (statusFilter === 'active') matchStatus = t.status === 'occupied';
      if (statusFilter === 'due') matchStatus = t.status === 'due';
      return matchSearch && matchStatus;
    });

    filtered.forEach(t => {
      const isDue = t.status === 'due';
      const statusLabel = isDue 
        ? '<span class="status-badge due">⚠️ Jatuh Tempo</span>' 
        : '<span class="status-badge occupied">🟢 Lunas</span>';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="font-weight:700;">${t.tenant}</div>
          <div style="font-size:0.75rem;color:var(--text-muted);">${t.type}</div>
        </td>
        <td><strong style="color:var(--brand-primary-light);">Kamar #${t.number}</strong></td>
        <td>
          <a href="https://wa.me/${t.phone}" target="_blank" style="color:var(--accent-blue);display:inline-flex;align-items:center;gap:4px;">
            ${t.phone || '-'}
          </a>
        </td>
        <td>${t.startDate || '-'}</td>
        <td>Tgl <strong>${t.dueDay}</strong> / bln</td>
        <td>${statusLabel}</td>
        <td>
          <div style="display:flex;gap:6px;">
            <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.sendWhatsAppReminder('${t.number}')">📲 WA Tagihan</button>
            <button type="button" class="btn-secondary" style="padding:4px 8px;font-size:0.78rem;" onclick="window.kosApp.recordPaymentForRoom('${t.number}')">💳 Bayar</button>
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
          <button type="button" class="btn-primary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.sendWhatsAppReminder('${t.number}')">📲 Kirim WA</button>
          <button type="button" class="btn-secondary" style="flex:1;font-size:0.8rem;padding:7px;" onclick="window.kosApp.recordPaymentForRoom('${t.number}')">💳 Catat Bayar</button>
          <button type="button" class="btn-secondary" style="font-size:0.8rem;padding:7px;color:#f87171;" onclick="window.kosApp.checkoutTenant('${t.number}')" title="Penghuni Keluar">✕</button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada data penghuni</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada data penghuni</div>';
    }
  }

  // --- TAB: KEUANGAN ---
  function renderFinancesTable(search = '', filterType = 'all') {
    const tbody = document.getElementById('financesTableBody');
    const mobileList = document.getElementById('financesMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const filtered = appData.finances.filter(f => {
      const matchSearch = f.desc.toLowerCase().includes(search.toLowerCase()) ||
                          f.id.toLowerCase().includes(search.toLowerCase()) ||
                          f.category.toLowerCase().includes(search.toLowerCase());
      const matchType = filterType === 'all' || f.type === filterType;
      return matchSearch && matchType;
    });

    filtered.forEach(trx => {
      const isIncome = trx.type === 'income';
      const colorClass = isIncome ? '#34d399' : '#f87171';
      const prefix = isIncome ? '+ ' : '- ';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-family:monospace;font-size:0.85rem;">${trx.id}</td>
        <td>${trx.date}</td>
        <td style="font-weight:600;">${trx.desc}</td>
        <td><span class="feature-pill">${trx.category}</span></td>
        <td style="font-weight:700;color:${colorClass};">${prefix}${formatRupiah(trx.amount)}</td>
        <td>${trx.method}</td>
        <td>
          ${trx.proofImage ? `
            <a href="${trx.proofImage}" target="_blank" class="btn-secondary" style="padding:3px 8px;font-size:0.75rem;color:#2563eb;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;margin-right:4px;">
              📎 Foto Bukti
            </a>
          ` : ''}
          <button type="button" class="btn-secondary" style="padding:3px 8px;font-size:0.75rem;" onclick="window.kosApp.showReceiptModal('${trx.id}')">
            🧾 Kuitansi
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
          <span>Kategori & Tanggal:</span>
          <span>${trx.category} • ${trx.date}</span>
        </div>
        <div class="mobile-data-row">
          <span>Metode:</span>
          <span>${trx.method}</span>
        </div>
        <div style="margin-top:6px;display:flex;justify-content:flex-end;">
          <button type="button" class="btn-secondary" style="padding:4px 10px;font-size:0.78rem;" onclick="window.kosApp.showReceiptModal('${trx.id}')">
            🧾 Lihat Kuitansi
          </button>
        </div>
      `;
      mobileList.appendChild(mCard);
    });

    if (filtered.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada data transaksi</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada data transaksi</div>';
    }
  }

  // --- TAB: KELUHAN & SERVIS ---
  function renderMaintenanceTable(search = '', filterStatus = 'all') {
    const tbody = document.getElementById('maintenanceTableBody');
    const mobileList = document.getElementById('maintenanceMobileCardList');
    if (!tbody || !mobileList) return;

    tbody.innerHTML = '';
    mobileList.innerHTML = '';

    const filtered = appData.maintenance.filter(m => {
      const matchSearch = m.issue.toLowerCase().includes(search.toLowerCase()) ||
                          String(m.roomId).includes(search) ||
                          m.reporter.toLowerCase().includes(search.toLowerCase());
      const matchStatus = filterStatus === 'all' || m.status === filterStatus;
      return matchSearch && matchStatus;
    });

    filtered.forEach(item => {
      let statusBadge = '<span class="status-badge repair">Pending</span>';
      if (item.status === 'progress') statusBadge = '<span class="status-badge due">Dikerjakan</span>';
      if (item.status === 'done') statusBadge = '<span class="status-badge occupied">Selesai</span>';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="font-family:monospace;font-size:0.85rem;">#${item.id}</td>
        <td><strong>Kamar #${item.roomId}</strong> (${item.reporter})</td>
        <td style="max-width:320px;">${item.issue}</td>
        <td>${item.date}</td>
        <td><span class="feature-pill" style="color:${item.priority === 'Mendesak' ? '#f87171' : 'inherit'};">${item.priority}</span></td>
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
          <span class="mobile-data-title">Kamar #${item.roomId} - ${item.priority}</span>
          ${statusBadge}
        </div>
        <div style="font-size:0.85rem;color:var(--text-secondary);">${item.issue}</div>
        <div class="mobile-data-row" style="margin-top:6px;">
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
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ada tiket keluhan</td></tr>';
      mobileList.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:20px;">Tidak ada tiket keluhan</div>';
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
`Halo Kak *${room.tenant}* 👋,

Semoga harinya menyenangkan!
Kami dari pengelola *${set.kos_name || 'Kos Mas Robi'}* menginfokan seputar tagihan sewa kamar:

📌 *Unit:* Kamar #${room.number} (${room.type})
💰 *Nominal Sewa:* ${formatRupiah(room.price)}
📅 *Jatuh Tempo:* Tanggal ${room.dueDay} bulan ini

Pembayaran dapat ditransfer ke:
🏦 *${set.bank_name || 'BCA'}*
💳 No. Rek: *${set.bank_account || '-'}*
👤 A.N: *${set.bank_holder || '-'}*

Mohon konfirmasi dengan mengirimkan bukti transfer jika sudah melakukan pembayaran ya Kak. Terima kasih banyak atas kerjasamanya! 🙏✨

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
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  function initModalDismissHandlers() {
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-close-modal');
        closeModal(id);
      });
    });

    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
          closeModal(backdrop.id);
        }
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m.id));
      }
    });
  }

  // --- ROOM DETAIL MODAL ---
  function openRoomDetailModal(roomNumber) {
    const room = appData.rooms.find(r => r.number === roomNumber);
    if (!room) return;

    document.getElementById('mRoomDetailTitle').textContent = `Detail Kamar #${room.number} (${room.type})`;
    const body = document.getElementById('mRoomDetailBody');
    const footer = document.getElementById('mRoomDetailFooter');

    const facilitiesHtml = (room.facilities || []).map(f => `<span class="feature-pill" style="padding:4px 10px;font-size:0.8rem;">✓ ${f}</span>`).join(' ');

    const roomImgSrc = room.image ? (room.image.startsWith('http') || room.image.startsWith('/') ? room.image : '/' + room.image) : '/assets/images/room_sample.jpg';

    body.innerHTML = `
      <div style="margin-bottom:14px;border-radius:12px;overflow:hidden;max-height:190px;border:1px solid var(--border-color);position:relative;">
        <img src="${roomImgSrc}" alt="Foto Kamar #${room.number}" style="width:100%;height:190px;object-fit:cover;display:block;">
        <span style="position:absolute;bottom:10px;left:10px;background:rgba(15,23,42,0.8);color:#ffffff;font-size:0.75rem;font-weight:700;padding:4px 10px;border-radius:999px;">📸 Foto Kamar #${room.number}</span>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-color);padding-bottom:12px;">
        <div>
          <div style="font-size:0.8rem;color:var(--text-muted);">Status Saat Ini</div>
          <div style="margin-top:4px;">${getStatusBadge(room.status)}</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:0.8rem;color:var(--text-muted);">Harga Sewa / Bulan</div>
          <div style="font-size:1.25rem;font-weight:700;color:var(--brand-primary-light);">${formatRupiah(room.price)}</div>
        </div>
      </div>

      <div style="background:var(--bg-surface);padding:14px;border-radius:var(--border-radius-md);">
        <h4 style="font-size:0.9rem;margin-bottom:8px;color:var(--text-secondary);">Informasi Penghuni:</h4>
        ${room.tenant ? `
          <div style="font-size:1.05rem;font-weight:700;">${room.tenant}</div>
          <div style="font-size:0.85rem;color:var(--text-secondary);margin-top:4px;">
            WhatsApp: <a href="https://wa.me/${room.phone}" target="_blank" style="color:var(--accent-blue);font-weight:600;">${room.phone}</a>
          </div>
          <div style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">
            Mulai Masuk: ${room.startDate || '-'} • Jatuh Tempo Tiap Tgl <strong>${room.dueDay}</strong>
          </div>
        ` : `
          <p style="color:var(--text-muted);font-size:0.88rem;">Kamar ini saat ini belum ditempati penghuni.</p>
          <button type="button" class="btn-primary" style="margin-top:10px;font-size:0.82rem;padding:6px 14px;" onclick="window.kosApp.assignNewTenantToRoom('${room.number}')">
            + Isi Penghuni Baru di Kamar Ini
          </button>
        `}
      </div>

      <div>
        <h4 style="font-size:0.88rem;margin-bottom:8px;color:var(--text-secondary);">Fasilitas Lengkap Kamar:</h4>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">${facilitiesHtml}</div>
      </div>

      <div class="form-group" style="margin-top:6px;">
        <label for="changeRoomStatusSelect">Ubah Cepat Status Kamar:</label>
        <select id="changeRoomStatusSelect" class="form-control" onchange="window.kosApp.changeRoomStatusDirect('${room.number}', this.value)">
          <option value="available" ${room.status === 'available' ? 'selected' : ''}>🔵 Kosong (Tersedia)</option>
          <option value="occupied" ${room.status === 'occupied' ? 'selected' : ''}>🟢 Terisi (Lunas)</option>
          <option value="due" ${room.status === 'due' ? 'selected' : ''}>🟡 Terisi (Jatuh Tempo)</option>
          <option value="repair" ${room.status === 'repair' ? 'selected' : ''}>🔴 Dalam Perbaikan / Servis</option>
        </select>
      </div>

      <!-- Custom Interior & Dynamic Budget Admin Section -->
      <div style="background: rgba(37,99,235,0.04); border: 1px solid rgba(37,99,235,0.2); border-radius: var(--border-radius-md); padding: 14px; margin-top: 14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
          <h4 style="font-size:0.88rem;font-weight:700;color:#1e40af;margin:0;display:flex;align-items:center;gap:6px;">
            <span>🎨</span> Custom Interior & Anggaran
          </h4>
          <span style="font-size:0.72rem;padding:2px 8px;border-radius:999px;font-weight:700;background:#dbeafe;color:#1e40af;">
            ${(room.custom_interior_status || 'NONE').toUpperCase()}
          </span>
        </div>

        ${room.custom_interior_request ? `
          <div style="font-size:0.82rem;color:var(--text-secondary);background:#ffffff;padding:8px 10px;border-radius:8px;border:1px solid var(--border-color);margin-bottom:10px;">
            <strong>Permintaan Pelanggan (${room.booked_by_name || 'Penghuni'}):</strong><br>${room.custom_interior_request}
          </div>
        ` : `<p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:8px;">Belum ada catatan custom interior khusus dari penyewa.</p>`}

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:8px;">
          <div>
            <label style="font-size:0.72rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:4px;">Atur Anggaran / Budget (Rp):</label>
            <input type="number" id="detailRoomCustomBudget" class="form-control" value="${room.custom_interior_budget || 0}" step="10000" style="padding:6px 10px;font-size:0.84rem;">
          </div>
          <div>
            <label style="font-size:0.72rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:4px;">Status Custom Interior:</label>
            <select id="detailRoomCustomStatus" class="form-control" style="padding:6px 10px;font-size:0.84rem;">
              <option value="none" ${(!room.custom_interior_status || room.custom_interior_status === 'none') ? 'selected' : ''}>Tidak Ada (None)</option>
              <option value="pending" ${room.custom_interior_status === 'pending' ? 'selected' : ''}>⏳ Menunggu Review</option>
              <option value="approved" ${room.custom_interior_status === 'approved' ? 'selected' : ''}>✅ Disetujui (Approved)</option>
              <option value="in_progress" ${room.custom_interior_status === 'in_progress' ? 'selected' : ''}>🛠️ Dikerjakan</option>
              <option value="completed" ${room.custom_interior_status === 'completed' ? 'selected' : ''}>🎉 Selesai</option>
              <option value="rejected" ${room.custom_interior_status === 'rejected' ? 'selected' : ''}>❌ Ditolak</option>
            </select>
          </div>
        </div>

        <div>
          <label style="font-size:0.72rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:4px;">Catatan Mas Robi untuk Pelanggan:</label>
          <input type="text" id="detailRoomCustomNotes" class="form-control" value="${room.custom_interior_admin_notes || ''}" placeholder="Catatan respons terkait bahan/waktu pengerjaan..." style="padding:6px 10px;font-size:0.84rem;margin-bottom:8px;">
        </div>

        <button type="button" class="btn-primary" style="font-size:0.78rem;padding:7px 12px;width:100%;margin-top:2px;" onclick="window.kosApp.saveCustomInteriorDirect('${room.number}')">
          💾 Simpan Anggaran & Status Custom Interior
        </button>
      </div>
    `;

    footer.innerHTML = `
      <button type="button" class="btn-secondary" data-close-modal="modalRoomDetail">Tutup</button>
      ${room.tenant ? `
        <button type="button" class="btn-primary" onclick="window.kosApp.sendWhatsAppReminder('${room.number}')">
          📲 Kirim WA Tagihan
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
      'assets/images/rooms/room_3.jpg'
    ];
    for (let i = 1; i <= 3; i++) {
      const input = document.getElementById(`roomImageInput${i}`);
      const preview = document.getElementById(`roomImgPreview${i}`);
      if (input) input.value = defaultImgs[i - 1];
      if (preview) preview.src = '/' + defaultImgs[i - 1];
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
    document.getElementById('roomTypeInput').value = room.type;
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

    // Populate 3 images
    const imgs = (room.images && room.images.length > 0) ? room.images : (room.images_list || [
      room.image || 'assets/images/rooms/room_1.jpg',
      'assets/images/rooms/room_2.jpg',
      'assets/images/rooms/room_3.jpg'
    ]);

    for (let i = 1; i <= 3; i++) {
      const input = document.getElementById(`roomImageInput${i}`);
      const preview = document.getElementById(`roomImgPreview${i}`);
      const val = imgs[i - 1] || imgs[0] || 'assets/images/rooms/room_1.jpg';
      if (input) input.value = val;
      if (preview) preview.src = val.startsWith('http') || val.startsWith('/') ? val : '/' + val;
    }

    openModal('modalAddRoom');
  }

  function initRoomForm() {
    const form = document.getElementById('formAddRoom');
    const btnNew = document.getElementById('btnAddNewRoom');

    btnNew?.addEventListener('click', () => {
      document.getElementById('mRoomFormTitle').textContent = 'Tambah Master Kamar';
      document.getElementById('roomEditId').value = '';
      document.getElementById('roomNumberInput').value = '';
      document.getElementById('roomNumberInput').disabled = false;
      document.getElementById('roomFloorInput').value = '1';
      document.getElementById('roomPriceInput').value = '1500000';
      document.getElementById('roomFacilitiesInput').value = 'AC, Springbed, Lemari, Meja Belajar, KM Dalam';
      document.getElementById('roomStatusInput').value = 'available';
      const customStatus = document.getElementById('roomCustomInteriorStatusInput');
      const customBudget = document.getElementById('roomCustomInteriorBudgetInput');
      const customReq = document.getElementById('roomCustomInteriorRequestInput');
      const customNotes = document.getElementById('roomCustomInteriorNotesInput');
      if (customStatus) customStatus.value = 'none';
      if (customBudget) customBudget.value = 0;
      if (customReq) customReq.value = '';
      if (customNotes) customNotes.value = '';
      fillDefaultRoomImages();
      openModal('modalAddRoom');
    });

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const editId = document.getElementById('roomEditId').value;
      const number = document.getElementById('roomNumberInput').value.trim();
      const floor = Number(document.getElementById('roomFloorInput').value);
      const type = document.getElementById('roomTypeInput').value;
      const price = Number(document.getElementById('roomPriceInput').value);
      const facilities = document.getElementById('roomFacilitiesInput').value.split(',').map(s => s.trim()).filter(Boolean);
      const status = document.getElementById('roomStatusInput').value;
      
      const img1 = document.getElementById('roomImageInput1')?.value.trim() || 'assets/images/rooms/room_1.jpg';
      const img2 = document.getElementById('roomImageInput2')?.value.trim() || 'assets/images/rooms/room_2.jpg';
      const img3 = document.getElementById('roomImageInput3')?.value.trim() || 'assets/images/rooms/room_3.jpg';
      const images = [img1, img2, img3];

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
            price,
            facilities,
            status,
            image: img1,
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

    appData.rooms.forEach(r => {
      const opt = document.createElement('option');
      opt.value = r.id; // DB ID
      opt.setAttribute('data-number', r.number);
      opt.textContent = `Kamar #${r.number} (${r.type}) - ${r.status === 'available' ? 'Tersedia' : 'Terisi: ' + (r.tenant || '')}`;
      if (targetRoomNumber && r.number === targetRoomNumber) {
        opt.selected = true;
      }
      select.appendChild(opt);
    });

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
            phone,
            id_card: idCard,
            room_id: roomId,
            start_date: startDate,
            due_day: dueDay,
            rent_amount: rentAmount,
            emergency_contact: emergencyContact
          })
        });

        if (res.ok) {
          showToast(`Penghuni ${name} berhasil didaftarkan di sistem Laravel!`, 'success');
          await fetchServerData();
          closeModal('modalAddTenant');
          form.reset();
          return;
        }
      } catch (err) {
        console.error(err);
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
    select.innerHTML = '<option value="">-- Tanpa Kamar Spesifik (Umum) --</option>';

    appData.rooms.forEach(r => {
      const opt = document.createElement('option');
      opt.value = r.id; // DB ID
      opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : ''}`;
      if (defaultRoomNumber && r.number === defaultRoomNumber) {
        opt.selected = true;
      }
      select.appendChild(opt);
    });

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('transDate').value = today;

    if (defaultRoomNumber) {
      const r = appData.rooms.find(room => room.number === defaultRoomNumber);
      if (r) {
        document.getElementById('transAmount').value = r.price;
        document.getElementById('transDescription').value = `Pembayaran Sewa Kamar #${r.number} (${r.tenant || ''}) Bulan Ini`;
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
      const roomId = document.getElementById('transRoomId').value || null;
      const description = document.getElementById('transDescription').value.trim();
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
      appData.rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id; // DB ID
        opt.textContent = `Kamar #${r.number} ${r.tenant ? '(' + r.tenant + ')' : ''}`;
        select.appendChild(opt);
      });
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
    const bankForm = document.getElementById('settingsBankForm');
    const btnExport = document.getElementById('btnExportData');
    const btnReset = document.getElementById('btnResetData');

    profileForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const kosName = document.getElementById('setKosName').value.trim();
      const ownerName = document.getElementById('setOwnerName').value.trim();
      const ownerPhone = document.getElementById('setOwnerPhone').value.trim();
      const kosAddress = document.getElementById('setKosAddress').value.trim();

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
            owner_phone: ownerPhone,
            kos_address: kosAddress
          })
        });
        showToast('Profil properti kos berhasil disimpan di Laravel!', 'success');
        document.getElementById('bannerPropertyName').textContent = kosName;
        document.getElementById('bannerPropertyAddress').textContent = '📍 ' + kosAddress;
        document.getElementById('profileOwnerName').textContent = ownerName;
      } catch (err) {
        showToast('Gagal menyimpan profil ke server', 'warning');
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
    roomSearch?.addEventListener('input', (e) => {
      renderRoomsMasterTable(e.target.value, currentFloor);
    });

    document.querySelectorAll('#roomTabFilters .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#roomTabFilters .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFloor = btn.getAttribute('data-floor');
        renderRoomsMasterTable(roomSearch?.value || '', currentFloor);
      });
    });

    const tenantSearch = document.getElementById('tenantSearchInput');
    let currentTStatus = 'all';
    tenantSearch?.addEventListener('input', (e) => {
      renderTenantsTable(e.target.value, currentTStatus);
    });

    document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#tenantStatusFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentTStatus = btn.getAttribute('data-tstatus');
        renderTenantsTable(tenantSearch?.value || '', currentTStatus);
      });
    });

    const financeSearch = document.getElementById('financeSearchInput');
    let currentFType = 'all';
    financeSearch?.addEventListener('input', (e) => {
      renderFinancesTable(e.target.value, currentFType);
    });

    document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#financeTypeFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFType = btn.getAttribute('data-ftype');
        renderFinancesTable(financeSearch?.value || '', currentFType);
      });
    });

    const maintSearch = document.getElementById('maintenanceSearchInput');
    let currentMStatus = 'all';
    maintSearch?.addEventListener('input', (e) => {
      renderMaintenanceTable(e.target.value, currentMStatus);
    });

    document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#repairStatusFilter .filter-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentMStatus = btn.getAttribute('data-rstatus');
        renderMaintenanceTable(maintSearch?.value || '', currentMStatus);
      });
    });
  }

  // --- RENDER ALL VIEWS ---
  function renderAllViews() {
    renderRoomGrid();
    renderRoomsMasterTable();
    renderTenantsTable();
    renderFinancesTable();
    renderMaintenanceTable();
    updateDashboardStats();
  }

  // --- INITIALIZE APPLICATION ---
  async function init() {
    initAutoDeviceDetector();
    initTheme();
    initNavigation();
    initDashboardFilters();
    initModalDismissHandlers();
    initRoomForm();
    initTenantForm();
    initTransactionForm();
    initReceiptPrint();
    initMaintenanceForm();
    initSettings();
    initSearchListeners();

    await fetchServerData();
    console.log('Kos Mas Robi Laravel System initialized.');
  }

  window.kosApp = {
    openRoomDetailModal,
    changeRoomStatusDirect,
    saveCustomInteriorDirect,
    editRoom,
    fillDefaultRoomImages,
    sendWhatsAppReminder,
    assignNewTenantToRoom,
    checkoutTenant,
    recordPaymentForRoom,
    showReceiptModal,
    updateRepairStatus
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
