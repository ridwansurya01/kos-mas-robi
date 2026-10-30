<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Portal Anak Kos - Kos Mas Robi. Kelola tagihan, lapor keluhan, dan akses fasilitas kos.">
  <meta name="theme-color" content="#2563eb">
  <title>@yield('title', 'Portal Anak Kos - Kos Mas Robi')</title>

  <!-- Favicon -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/></svg>">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Early Theme Init (Prevents White Flash) -->
  <script>
    (function() {
      var saved = localStorage.getItem('KOS_MAS_ROBI_THEME') || localStorage.getItem('kosTheme') || 'light';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>

  <!-- Portal-exclusive stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ file_exists(public_path('css/portal.css')) ? filemtime(public_path('css/portal.css')) : time() }}">
  <link rel="stylesheet" href="{{ asset('css/darkmode-portal.css') }}?v={{ file_exists(public_path('css/darkmode-portal.css')) ? filemtime(public_path('css/darkmode-portal.css')) : time() }}">
</head>
<body>
  @yield('content')

  <!-- Toast -->
  <div id="toastContainer" aria-live="polite" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;"></div>

  <!-- Portal JS (no admin scripts) -->
  <script>
    // Modal system
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', function() {
          const id = this.getAttribute('data-close-modal');
          document.getElementById(id)?.classList.remove('open');
        });
      });
      document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
          if (e.target === this) this.classList.remove('open');
        });
      });
    });
  </script>
</body>
</html>
