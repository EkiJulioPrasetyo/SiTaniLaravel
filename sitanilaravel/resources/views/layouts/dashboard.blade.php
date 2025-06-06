{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'SiTani' }}</title>

  {{-- TailwindCSS --}}
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      background-image: url('{{ asset("Foto/Petani.png") }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
  </style>

  {{-- Jika Anda ingin menambahkan <script> atau <style> khusus di <head> --}}
  @stack('head')
</head>

<body class="bg-lime-900 min-h-screen font-sans flex flex-col">

  {{-- Konten utama: Semua view child akan disini --}}
  <main class="flex-grow flex items-center justify-center px-4">
    @yield('content')
  </main>

  {{-- ===== KUNCI: PERLU MEMANGGIL @yield('scripts') ===== --}}
  @yield('scripts')

</body>
</html>
