<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Portal Anak Kos - SMART LIVING. Kelola tagihan, lapor keluhan, dan akses fasilitas kos.">
  <meta name="theme-color" content="#0a5661">
  <title>@yield('title', 'Portal Anak Kos - SMART LIVING')</title>

  <!-- Favicon Sesuai Logo Landing Page -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
  <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Portal-exclusive stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ file_exists(public_path('css/portal.css')) ? filemtime(public_path('css/portal.css')) : time() }}">
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
