<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Perpustakaan SMKN 1 Sumedang' }}</title>
    @vite('resources/css/app.css', 'resources/js/app.js')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-white justify-center text-center pt-10">

    <div>
        {{ $slot }}
    </div>
</body>

</html>
