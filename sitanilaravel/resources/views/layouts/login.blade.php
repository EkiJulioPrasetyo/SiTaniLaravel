<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SiTani' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('{{ asset('Foto/Petani.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-lime-900 min-h-screen flex items-center justify-center font-sans">
    @yield('content')
</body>
</html>
