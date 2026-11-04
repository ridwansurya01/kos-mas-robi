<!DOCTYPE html>
<html lang="id" data-device="{{ $deviceType ?? 'auto' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Sistem Manajemen Kos Mas Robi - Aplikasi web responsif berbasis Laravel untuk kelola kamar, anak kos, tagihan, dan kuitansi otomatis.">
  <meta name="theme-color" content="#0a5661">
  <title>@yield('title', 'Kos Mas Robi - Smart Kost Management')</title>
  
  <!-- Favicon & PWA -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230a5661'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/></svg>">
  
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time() }}">

  <script>
    // Early global alert override to intercept any browser alert before other scripts run
    window._earlyPendingAlerts = [];
    window.alert = function(msg) {
      if (typeof window.showModernAlert === 'function') {
        window.showModernAlert(msg);
      } else {
        window._earlyPendingAlerts.push(msg);
      }
    };
  </script>
</head>
<body>

  <div class="app-container">
    @yield('content')
  </div>

  <!-- MODAL: MODERN ALERT POPUP (REPLACES NATIVE BROWSER ALERT) -->
  <style>
    @keyframes modernAlertPopIn {
      0% { opacity: 0; transform: scale(0.88) translateY(16px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    #modalModernAlert.open .modal-dialog,
    #modalModernConfirm.open .modal-dialog {
      animation: modernAlertPopIn 0.26s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
  </style>
  <div class="modal-backdrop" id="modalModernAlert" onclick="if(event.target === this) closeModernAlert()" style="display: none; position: fixed; inset: 0; background: rgba(3, 35, 40, 0.82); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 20px;">
    <div class="modal-dialog" style="max-width: 440px; width: 100%; margin: auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 28px 65px -12px rgba(3, 35, 40, 0.45); border: 1.5px solid rgba(13, 148, 136, 0.28); text-align: center; padding: 34px 28px;">
      <div id="modernAlertIconWrap" style="width: 72px; height: 72px; border-radius: 22px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1.5px solid rgba(16, 185, 129, 0.32); box-shadow: 0 8px 24px rgba(16, 185, 129, 0.22); transition: all 0.2s;">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <h3 id="modernAlertTitle" style="margin: 0 0 10px; font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">Pemberitahuan Sistem</h3>
      <p id="modernAlertMessage" style="margin: 0 0 26px; font-size: 0.92rem; color: #475569; line-height: 1.6; white-space: pre-line; word-break: break-word;">Pesan informasi sistem.</p>
      <div style="display: flex; justify-content: center;">
        <button type="button" id="btnModernAlertOk" onclick="closeModernAlert()" style="min-width: 150px; padding: 12px 30px; background: linear-gradient(135deg, #0d9488 0%, #0a5661 100%); color: #ffffff; border: none; border-radius: 14px; font-weight: 800; font-size: 0.92rem; cursor: pointer; box-shadow: 0 6px 18px rgba(10, 86, 97, 0.32); transition: all 0.2s;">
          Mengerti
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL: MODERN CONFIRM POPUP (REPLACES NATIVE BROWSER CONFIRM) -->
  <div class="modal-backdrop" id="modalModernConfirm" onclick="if(event.target === this) resolveModernConfirm(false)" style="display: none; position: fixed; inset: 0; background: rgba(3, 35, 40, 0.82); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 999999; align-items: center; justify-content: center; padding: 20px;">
    <div class="modal-dialog" style="max-width: 460px; width: 100%; margin: auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 28px 65px -12px rgba(3, 35, 40, 0.45); border: 1.5px solid rgba(225, 29, 72, 0.28); text-align: center; padding: 34px 28px;">
      <div id="modernConfirmIconWrap" style="width: 72px; height: 72px; border-radius: 22px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; background: rgba(225, 29, 72, 0.12); color: #e11d48; border: 1.5px solid rgba(225, 29, 72, 0.32); box-shadow: 0 8px 24px rgba(225, 29, 72, 0.22);">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <h3 id="modernConfirmTitle" style="margin: 0 0 10px; font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em;">Konfirmasi Tindakan</h3>
      <p id="modernConfirmMessage" style="margin: 0 0 26px; font-size: 0.92rem; color: #475569; line-height: 1.6; white-space: pre-line; word-break: break-word;">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
      <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
        <button type="button" id="btnModernConfirmCancel" onclick="resolveModernConfirm(false)" style="min-width: 120px; padding: 12px 24px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 14px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;">
          Batal
        </button>
        <button type="button" id="btnModernConfirmOk" onclick="resolveModernConfirm(true)" style="min-width: 140px; padding: 12px 26px; background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); color: #ffffff; border: none; border-radius: 14px; font-weight: 800; font-size: 0.9rem; cursor: pointer; box-shadow: 0 6px 18px rgba(225, 29, 72, 0.35); transition: all 0.2s;">
          Ya, Lanjutkan
        </button>
      </div>
    </div>
  </div>

  <script>
    var _modernAlertCallback = null;
    window.showModernAlert = function(message, type, title, onOk) {
      var modal = document.getElementById('modalModernAlert');
      if (!modal) {
        if (typeof onOk === 'function') onOk();
        return;
      }
      var msgText = String(message || '');
      var msgLower = msgText.toLowerCase();

      if (!type) {
        if (msgLower.includes('berhasil') || msgLower.includes('sukses') || msgLower.includes('disimpan') || msgLower.includes('dihapus') || msgLower.includes('lunas') || msgLower.includes('diperbarui')) {
          type = 'success';
        } else if (msgLower.includes('gagal') || msgLower.includes('kesalahan') || msgLower.includes('error') || msgLower.includes('wajib') || msgLower.includes('tidak boleh') || msgLower.includes('minimal')) {
          type = 'error';
        } else {
          type = 'info';
        }
      }

      if (!title) {
        if (type === 'success') title = 'Berhasil';
        else if (type === 'error') title = 'Perhatian';
        else title = 'Informasi Sistem';
      }

      var iconWrap = document.getElementById('modernAlertIconWrap');
      if (iconWrap) {
        if (type === 'success') {
          iconWrap.style.background = 'rgba(16, 185, 129, 0.12)';
          iconWrap.style.color = '#10b981';
          iconWrap.style.borderColor = 'rgba(16, 185, 129, 0.32)';
          iconWrap.style.boxShadow = '0 8px 24px rgba(16, 185, 129, 0.22)';
          iconWrap.innerHTML = '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
        } else if (type === 'error') {
          iconWrap.style.background = 'rgba(244, 63, 94, 0.12)';
          iconWrap.style.color = '#f43f5e';
          iconWrap.style.borderColor = 'rgba(244, 63, 94, 0.32)';
          iconWrap.style.boxShadow = '0 8px 24px rgba(244, 63, 94, 0.22)';
          iconWrap.innerHTML = '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
        } else {
          iconWrap.style.background = 'rgba(13, 148, 136, 0.12)';
          iconWrap.style.color = '#0d9488';
          iconWrap.style.borderColor = 'rgba(13, 148, 136, 0.32)';
          iconWrap.style.boxShadow = '0 8px 24px rgba(13, 148, 136, 0.22)';
          iconWrap.innerHTML = '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
        }
      }

      var titleEl = document.getElementById('modernAlertTitle');
      if (titleEl) titleEl.textContent = title;

      var msgEl = document.getElementById('modernAlertMessage');
      if (msgEl) msgEl.textContent = msgText;

      _modernAlertCallback = (typeof onOk === 'function') ? onOk : null;

      modal.classList.add('open');
      modal.style.display = 'flex';
      modal.style.pointerEvents = 'auto';
    };

    window.closeModernAlert = function() {
      var modal = document.getElementById('modalModernAlert');
      if (modal) {
        modal.classList.remove('open');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
      }
      if (typeof _modernAlertCallback === 'function') {
        var cb = _modernAlertCallback;
        _modernAlertCallback = null;
        try { cb(); } catch(e) { console.error('Alert callback error:', e); }
      }
    };

    var _modernConfirmResolver = null;
    window.showModernConfirm = function(message, options) {
      options = options || {};
      var modal = document.getElementById('modalModernConfirm');
      if (!modal) return Promise.resolve(true);

      var title = options.title || 'Konfirmasi Tindakan';
      var okText = options.okText || 'Ya, Lanjutkan';
      var cancelText = options.cancelText || 'Batal';
      var isDanger = options.danger !== false;

      var titleEl = document.getElementById('modernConfirmTitle');
      if (titleEl) titleEl.textContent = title;

      var msgEl = document.getElementById('modernConfirmMessage');
      if (msgEl) msgEl.textContent = String(message || '');

      var btnOk = document.getElementById('btnModernConfirmOk');
      if (btnOk) {
        btnOk.textContent = okText;
        if (isDanger) {
          btnOk.style.background = 'linear-gradient(135deg, #e11d48 0%, #be123c 100%)';
          btnOk.style.boxShadow = '0 6px 18px rgba(225, 29, 72, 0.35)';
        } else {
          btnOk.style.background = 'linear-gradient(135deg, #0d9488 0%, #0a5661 100%)';
          btnOk.style.boxShadow = '0 6px 18px rgba(10, 86, 97, 0.32)';
        }
      }

      var btnCancel = document.getElementById('btnModernConfirmCancel');
      if (btnCancel) btnCancel.textContent = cancelText;

      modal.classList.add('open');
      modal.style.display = 'flex';
      modal.style.pointerEvents = 'auto';

      return new Promise(function(resolve) {
        _modernConfirmResolver = resolve;
      });
    };

    window.resolveModernConfirm = function(val) {
      var modal = document.getElementById('modalModernConfirm');
      if (modal) {
        modal.classList.remove('open');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
      }
      if (typeof _modernConfirmResolver === 'function') {
        var res = _modernConfirmResolver;
        _modernConfirmResolver = null;
        res(val);
      }
    };

    // Global override for alert and notifications
    window.alert = function(msg) {
      window.showModernAlert(msg);
    };
    window.showNotification = function(type, msg) {
      window.showModernAlert(msg, type);
    };

    // Flush any early pending alerts
    if (window._earlyPendingAlerts && window._earlyPendingAlerts.length > 0) {
      var pending = window._earlyPendingAlerts.shift();
      setTimeout(function() { window.showModernAlert(pending); }, 300);
    }
  </script>

  <!-- TOAST NOTIFICATIONS WRAPPER -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- JAVASCRIPT APPLICATION LOGIC -->
  <script src="{{ asset('js/app.js') }}?v={{ file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time() }}"></script>
</body>
</html>
