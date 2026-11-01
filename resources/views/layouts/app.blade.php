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
</head>
<body>

  <div class="app-container">
    @yield('content')
  </div>

  <!-- TOAST NOTIFICATIONS WRAPPER -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- JAVASCRIPT APPLICATION LOGIC -->
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
